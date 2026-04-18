<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KhachHang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\URL;

class KhachHangController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = KhachHang::with(['thuCungs:id,khach_hang_id,ten_thu_cung']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate(20);

        $data = collect($paginator->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'full_name' => $item->full_name,
                'so_dien_thoai' => $item->phone,
                'email' => $item->email,
                'thu_cung' => $item->thuCungs->map(function ($pet) {
                    return [
                        'id' => $pet->id,
                        'ten_thu_cung' => $pet->ten_thu_cung,
                    ];
                })->toArray(),
                'updated_at' => $item->updated_at,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
            'meta' => [
                'total' => $paginator->total(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ]
        ]);
    }

    public function dangKi(Request $request): JsonResponse
    {
        $input = $request->all();
        if (isset($input['phone'])) {
            $input['phone'] = preg_replace('/\D+/', '', (string) $input['phone']);
        }

        $rules = [
            'full_name' => 'required|string|max:191',
            'email' => 'required|email|unique:khach_hangs,email',
            'password' => 'required|string|min:8',
            'phone' => ['nullable', 'regex:/^[0-9]{10}$/', 'unique:khach_hangs,phone'],
            'address' => 'nullable|string|max:255',
            'anh_dai_dien' => 'nullable|string|max:255',
        ];

        $messages = [
            'phone.regex' => Lang::get('messages.phone_invalid'),
            'phone.unique' => Lang::get('messages.phone_taken'),
            'email.required' => Lang::get('messages.email_required'),
            'email.unique' => Lang::get('messages.email_taken'),
        ];

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => Lang::get('messages.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $payload = [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'anh_dai_dien' => $data['anh_dai_dien'] ?? null,
            'rank' => 'Silver',
            'trang_thai' => 'active',
        ];

        try {
            $customer = KhachHang::create($payload);

            // Tạo URL xác thực có chữ ký
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $customer->id, 'hash' => sha1($customer->email)]
            );

            // Gửi Email
            Mail::to($customer->email)->send(new VerifyEmail($verificationUrl));
        } catch (\Exception $e) {
            $safePayload = $payload;
            if (isset($safePayload['password'])) { // Đã đổi từ mat_khau sang password
                unset($safePayload['password']);
            }
            Log::error('KhachHang::create failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $safePayload,
            ]);

            return response()->json([
                'status' => false,
                'message' => Lang::get('messages.server_error'),
            ], 500);
        }

        if (method_exists($customer, 'makeHidden')) {
            $customer->makeHidden(['password']);
        } else {
            unset($customer->password);
        }

        return response()->json([
            'status' => true,
            'message' => 'Đăng ký thành công. Vui lòng kiểm tra email để kích hoạt tài khoản.',
            'data' => $customer,
        ], 201);
    }




    private function handleAvatarUpload(Request $request)
    {
        if (! $request->hasFile('anh_dai_dien')) {
            return null;
        }

        $file = $request->file('anh_dai_dien');
        if (! $file->isValid()) return null;

        try {
            $path = $file->store('khachhang/avatars', 'public');
            return url(Storage::url($path));
        } catch (\Exception $e) {
            Log::error('Avatar store failed (helper)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    private function assignProfileFields(KhachHang $model, array $data, Request $request): void
    {
        $model->full_name = $data['full_name'];
        $model->email = $data['email'] ?? $model->email;
        $model->phone = $data['phone'];
        $model->address = $data['address'] ?? $model->address;

        if ($request->hasFile('anh_dai_dien')) {
            $url = $this->handleAvatarUpload($request);
            if ($url) $model->anh_dai_dien = $url;
        } else {
            $model->anh_dai_dien = $data['anh_dai_dien'] ?? $model->anh_dai_dien;
        }
    }


    public function dangNhap(Request $request): JsonResponse
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => Lang::get('messages.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $validator->validated();

        $customer = KhachHang::where('email', $credentials['email'])->first();
        if (! $customer || ! \Illuminate\Support\Facades\Hash::check($credentials['password'], $customer->password)) {
            return response()->json([
                'status' => false,
                'message' => Lang::get('messages.login_failed'),
            ], 401);
        }

        // Kiểm tra xem email đã được xác thực chưa
        if (is_null($customer->email_verified_at)) {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản chưa được kích hoạt. Vui lòng kiểm tra email.',
            ], 403);
        }

        try {
            $token = $customer->createToken('api-token')->plainTextToken;
        } catch (\Exception $e) {
            Log::error('Token creation failed: ' . $e->getMessage());
            $token = null;
        }

        if (method_exists($customer, 'makeHidden')) {
            $customer->makeHidden(['password']);
        }

        return response()->json([
            'status' => true,
            'message' => Lang::get('messages.login_success'),
            'data' => $customer,
            'token' => $token,
        ], 200);
    }

    public function capNhat(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['status' => false, 'message' => Lang::get('messages.unauthorized')], 401);
        }

        $input = $request->all();
        if (isset($input['phone'])) {
            $input['phone'] = preg_replace('/\D+/', '', (string) $input['phone']);
        }

        $rules = [
            'full_name' => 'required|string|max:191',
            'email' => 'required|email|unique:khach_hangs,email,' . $user->id,
            'phone' => ['nullable', 'regex:/^[0-9]{10}$/', 'unique:khach_hangs,phone,' . $user->id],
            'address' => 'nullable|string|max:255',
            'anh_dai_dien' => 'nullable',
        ];

        $messages = [
            'phone.regex' => Lang::get('messages.phone_invalid'),
            'phone.unique' => Lang::get('messages.phone_taken'),
        ];

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => Lang::get('messages.validation_failed'), 'errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('anh_dai_dien')) {
            $fileValidator = Validator::make($request->all(), [
                'anh_dai_dien' => 'file|image|mimes:jpg,jpeg,png,gif|max:5120',
            ]);
            if ($fileValidator->fails()) {
                return response()->json(['status' => false, 'message' => Lang::get('messages.validation_failed'), 'errors' => $fileValidator->errors()], 422);
            }
        }

        $data = $validator->validated();

        $this->assignProfileFields($user, $data, $request);

        try {
            $user->save();
        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id ?? null,
            ]);
            return response()->json(['status' => false, 'message' => Lang::get('messages.server_error')], 500);
        }

        $user->refresh();

        return response()->json(['status' => true, 'message' => Lang::get('messages.update_success'), 'user' => $user], 200);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $target = KhachHang::find($id);
        if (! $target) {
            return response()->json(['status' => false, 'message' => Lang::get('messages.not_found')], 404);
        }

        $input = $request->all();
        if (isset($input['phone'])) {
            $input['phone'] = preg_replace('/\D+/', '', (string) $input['phone']);
        }

        $rules = [
            'full_name' => 'required|string|max:191',
            'email' => 'required|email|unique:khach_hangs,email,' . $target->id,
            'phone' => ['nullable', 'regex:/^[0-9]{10}$/', 'unique:khach_hangs,phone,' . $target->id],
            'address' => 'nullable|string|max:255',
            'anh_dai_dien' => 'nullable',
        ];

        $validator = Validator::make($input, $rules, [
            'phone.regex' => Lang::get('messages.phone_invalid'),
            'phone.unique' => Lang::get('messages.phone_taken'),
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => Lang::get('messages.validation_failed'), 'errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('anh_dai_dien')) {
            $fileValidator = Validator::make($request->all(), [
                'anh_dai_dien' => 'file|image|mimes:jpg,jpeg,png,gif|max:5120',
            ]);
            if ($fileValidator->fails()) {
                return response()->json(['status' => false, 'message' => Lang::get('messages.validation_failed'), 'errors' => $fileValidator->errors()], 422);
            }
        }

        $data = $validator->validated();

        $this->assignProfileFields($target, $data, $request);

        try {
            $target->save();
        } catch (\Exception $e) {
            Log::error('Profile update by id failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'target_id' => $target->id ?? null,
            ]);
            return response()->json(['status' => false, 'message' => Lang::get('messages.server_error')], 500);
        }

        return response()->json(['status' => true, 'message' => Lang::get('messages.update_success'), 'user' => $target], 200);
    }

    // --- Các phương thức đăng nhập mạng xã hội ---

    public function redirectToGoogle()
    {
        /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
        $driver = Socialite::driver('google');
        return $driver->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        return $this->handleSocialCallback('google');
    }

    public function redirectToFacebook()
    {
        /** @var \Laravel\Socialite\Two\FacebookProvider $driver */
        $driver = Socialite::driver('facebook');
        return $driver->stateless()->redirect();
    }

    public function handleFacebookCallback()
    {
        return $this->handleSocialCallback('facebook');
    }

    private function handleSocialCallback($provider)
    {
        try {
            /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
            $driver = Socialite::driver($provider);
            $socialUser = $driver->stateless()->user();
        } catch (\Exception $e) {
            Log::error($provider . ' login failed: ' . $e->getMessage());
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect($frontendUrl . '/khach-hang/dang-nhap?error=social_login_failed');
        }

        $socialId = $socialUser->getId();
        $email = $socialUser->getEmail();
        $name = $socialUser->getName();
        $avatar = $socialUser->getAvatar();

        // 1. Kiểm tra xem tài khoản mạng xã hội này đã tồn tại chưa
        $account = SocialAccount::where('provider', $provider)
            ->where('provider_user_id', $socialId)
            ->first();

        if ($account) {
            $customer = $account->khachHang;
            $token = $customer->createToken('api-token')->plainTextToken;

            // Chuyển hướng về Frontend kèm token
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            $userData = base64_encode(json_encode($customer));
            return redirect($frontendUrl . '/auth/callback?token=' . $token . '&user=' . $userData);
        }

        // 2. Nếu tài khoản mạng xã hội chưa tồn tại, kiểm tra xem email đã tồn tại trong KhachHang chưa
        // Sử dụng DB transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();
        try {
            $customer = null;
            if ($email) {
                $customer = KhachHang::where('email', $email)->first();
            }

            if (! $customer) {
                // 3. Tạo khách hàng mới nếu chưa tồn tại
                $customer = KhachHang::create([
                    'full_name' => $name,
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => null, // Không có mật khẩu cho đăng nhập mạng xã hội
                    'anh_dai_dien' => $avatar,
                    'rank' => 'Silver',
                    'trang_thai' => 'active',
                ]);
            }

            // Liên kết tài khoản mạng xã hội
            SocialAccount::create([
                'khach_hang_id' => $customer->id,
                'provider' => $provider,
                'provider_user_id' => $socialId,
            ]);

            DB::commit();

            $token = $customer->createToken('api-token')->plainTextToken;

            // Chuyển hướng về Frontend kèm token
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            $userData = base64_encode(json_encode($customer));
            return redirect($frontendUrl . '/auth/callback?token=' . $token . '&user=' . $userData);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Social login transaction failed: ' . $e->getMessage());
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect($frontendUrl . '/khach-hang/dang-nhap?error=server_error');
        }
    }

    public function guiLaiXacNhan(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => Lang::get('messages.validation_failed'),
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->input('email');
        $customer = KhachHang::where('email', $email)->first();

        if (! $customer) {
            return response()->json([
                'status' => true,
                'message' => 'Email xác nhận đã được gửi lại thành công.'
            ], 200);
        }

        if ($customer->email_verified_at != null) {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản đã được xác minh. Vui lòng đăng nhập.'
            ], 400);
        }

        try {
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $customer->id, 'hash' => sha1($customer->email)]
            );

            Mail::to($customer->email)->send(new VerifyEmail($verificationUrl));
            
            return response()->json([
                'status' => true,
                'message' => 'Email xác nhận đã được gửi lại thành công.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Resend verification email failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => Lang::get('messages.server_error')
            ], 500);
        }
    }

    public function verifyEmail(Request $request, $id)
    {
        if (! $request->hasValidSignature()) {
            return response()->json(['status' => false, 'message' => 'Liên kết xác thực không hợp lệ hoặc đã hết hạn.'], 403);
        }

        $customer = KhachHang::find($id);

        if (! $customer) {
            return response()->json(['status' => false, 'message' => 'Tài khoản không tồn tại.'], 404);
        }

        if (! hash_equals((string) $request->route('hash'), sha1($customer->email))) {
            return response()->json(['status' => false, 'message' => 'Liên kết xác thực không hợp lệ.'], 403);
        }

        if (! $customer->hasVerifiedEmail()) {
            $customer->markEmailAsVerified();
        }

        // Tạo token để tự động đăng nhập
        $token = $customer->createToken('api-token')->plainTextToken;

        // Chuyển hướng về Frontend kèm token
        $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173'));
        return redirect($frontendUrl . '/auth/verified?token=' . $token);
    }
}
