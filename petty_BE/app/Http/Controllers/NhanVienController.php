<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use App\Models\Admin;
use App\Models\PhanQuyen;
use App\Helpers\UserImageHelper;
use App\Http\Requests\NhanVienRequest;
use App\Notifications\NhanVienCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class NhanVienController extends Controller
{
    /**
     * Cập nhật hồ sơ của nhân viên hiện tại (self-service).
     * Cho phép: full_name, phone, address, chuc_danh, nam_kinh_nghiem, chung_chi_hanh_nghe, bang_cap_chuyen_mon, anh_dai_dien
     */
    public function updateMe(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!($user instanceof NhanVien)) {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ nhân viên mới có thể cập nhật hồ sơ của mình.',
            ], 403);
        }

        $data = $request->validate([
            'full_name'            => 'sometimes|string|max:255',
            'phone'                => 'sometimes|nullable|string|max:20',
            'address'              => 'sometimes|nullable|string|max:255',
            'chuc_danh'            => 'sometimes|nullable|string|max:255',
            'nam_kinh_nghiem'      => 'sometimes|nullable|integer|min:0|max:60',
            'chung_chi_hanh_nghe'  => 'sometimes|nullable|string|max:255',
            'bang_cap_chuyen_mon'  => 'sometimes|nullable|string',
            // avatar upload chưa làm multipart ở FE, nhưng vẫn cho phép set path/url nếu sau này cần
            'anh_dai_dien'         => 'sometimes|nullable|string|max:255',
        ]);

        $user->fill($data);
        $user->save();

        $payload = $user->fresh()->toArray();
        $payload['anh_dai_dien_url'] = UserImageHelper::getAvatarUrl($user->anh_dai_dien);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật hồ sơ thành công.',
            'data' => $payload,
        ]);
    }

    /**
     * Danh sách bác sĩ — dùng cho check-in, không cần quyền đặc biệt ngoài staff.only
     */
    public function danhSachBacSi()
    {
        $data = NhanVien::where('vai_tro', 'bac_si')
            ->where('trang_thai', 'hoat_dong')
            ->select('id', 'full_name', 'chuc_danh')
            ->orderBy('full_name')
            ->get();

        return response()->json(['status' => true, 'data' => $data]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        // Return all employees. Consider adding pagination if dataset grows.
        $query = NhanVien::query();
        if ($request->has('vai_tro')) {
            $query->where('vai_tro', $request->vai_tro);
        }
        $nhanViens = $query->get();

        // Thêm URL ảnh đại diện đầy đủ
        $data = $nhanViens->map(function ($nhanVien) {
            $item = $nhanVien->toArray();
            $item['anh_dai_dien_url'] = UserImageHelper::getAvatarUrl($nhanVien->anh_dai_dien);
            return $item;
        });

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\NhanVienRequest $request)
    {
        $data = $request->validated();

        // If no password provided, generate a secure random password and include it
        $plainPassword = null;
        if (empty($data['password'])) {
            $plainPassword = Str::random(12);
            $data['password'] = $plainPassword;
        }

        // Map vai_tro to PhanQuyen and assign phan_quyen_id
        $vaiTroMap = [
            'bac_si' => PhanQuyen::VAI_TRO_BAC_SI,
            'y_ta'   => PhanQuyen::VAI_TRO_DIEU_DUONG,
        ];
        $maVaiTro = $vaiTroMap[$data['vai_tro']] ?? null;
        if ($maVaiTro) {
            $phanQuyen = PhanQuyen::where('ma_vai_tro', $maVaiTro)->first();
            if ($phanQuyen) {
                $data['phan_quyen_id'] = $phanQuyen->id;
            }
        }

        // Create the employee
        $nhanVien = NhanVien::create($data);

        // Notify the created employee via email (and database if available)
        try {
            $nhanVien->notify(new NhanVienCreatedNotification($nhanVien, $plainPassword));
        } catch (\Throwable $e) {
            // swallow mail/notification exceptions to avoid breaking API
        }

        // Notify all admins (if any) about new employee via database (if supported)
        if (class_exists(Admin::class)) {
            try {
                $admins = Admin::all();
                foreach ($admins as $admin) {
                    $admin->notify(new NhanVienCreatedNotification($nhanVien));
                }
            } catch (\Throwable $e) {
                // ignore notification errors
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Tạo nhân viên thành công.',
            'data' => $nhanVien,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(NhanVien $nhanVien)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NhanVien $nhanVien)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NhanVien $nhanVien)
    {
        //
    }

    /**
     * Change password for a given NhanVien.
     * Admins can call this endpoint to set a new password for an employee.
     */
    public function changePassword(Request $request, NhanVien $nhanVien)
    {
        $data = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.string' => 'Mật khẩu không hợp lệ.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $nhanVien->password = Hash::make($data['password']);
        $nhanVien->save();

        return response()->json([
            'status' => true,
            'message' => 'Đổi mật khẩu thành công.',
        ]);
    }

    /**
     * Lock the employee account so they cannot authenticate (revoke tokens).
     */
    public function lockAccount(NhanVien $nhanVien)
    {
        $nhanVien->trang_thai = 'da_khoa';
        $nhanVien->save();

        // Revoke all existing tokens so current sessions are invalidated
        try {
            $nhanVien->tokens()->delete();
        } catch (\Throwable $e) {
            // ignore token deletion errors
        }

        return response()->json([
            'status' => true,
            'message' => 'Tài khoản đã bị khóa.',
        ]);
    }

    /**
     * Unlock the employee account so they can authenticate again.
     */
    public function unlockAccount(NhanVien $nhanVien)
    {
        $nhanVien->trang_thai = 'hoat_dong';
        $nhanVien->save();

        return response()->json([
            'status' => true,
            'message' => 'Tài khoản đã được mở khóa.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NhanVien $nhanVien)
    {
        //
    }

    /**
     * Đăng nhập cho nhân viên
     */
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

        $nhanVien = NhanVien::where('email', $credentials['email'])->first();

        // Kiểm tra email và password
        if (!$nhanVien || !Hash::check($credentials['password'], $nhanVien->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Email hoặc mật khẩu không đúng.',
            ], 401);
        }

        // Kiểm tra tài khoản có bị khóa không
        if (!$nhanVien->isActive()) {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
            ], 403);
        }

        try {
            $token = $nhanVien->createToken('api-token')->plainTextToken;
        } catch (\Exception $e) {
            Log::error('NhanVien token creation failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi tạo token đăng nhập.',
            ], 500);
        }

        // Load thông tin vai trò và quyền
        $nhanVien->load('phanQuyen');

        // Ẩn password
        if (method_exists($nhanVien, 'makeHidden')) {
            $nhanVien->makeHidden(['password']);
        }

        // Thêm URL ảnh đại diện đầy đủ
        $nhanVienData = $nhanVien->toArray();
        $nhanVienData['anh_dai_dien_url'] = UserImageHelper::getAvatarUrl($nhanVien->anh_dai_dien);

        // Xác định đường dẫn redirect dựa trên vai trò
        $redirectUrl = $this->getRedirectUrlByRole($nhanVien->vai_tro);

        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập thành công.',
            'data' => $nhanVienData,
            'token' => $token,
            'redirect_url' => $redirectUrl,
            'vai_tro_debug' => $nhanVien->vai_tro, // Thêm để debug
        ], 200);
    }

    /**
     * Xác định đường dẫn redirect dựa trên vai trò nhân viên
     */
    private function getRedirectUrlByRole(?string $vaiTro): string
    {
        // Nếu vai_tro null, trả về dashboard mặc định
        if (!$vaiTro) {
            return '/dashboard';
        }

        // Chuẩn hóa vai trò (lowercase và trim)
        $vaiTro = strtolower(trim($vaiTro));

        $roleRoutes = [
            'bac_si' => '/doctor/dashboard',
            'bacsi' => '/doctor/dashboard',
            'bác sĩ' => '/doctor/dashboard',
            'doctor' => '/doctor/dashboard',

            'dieu_duong' => '/nurse/dashboard',
            'dieuduong' => '/nurse/dashboard',
            'điều dưỡng' => '/nurse/dashboard',
            'y_ta' => '/nurse/dashboard',
            'yta' => '/nurse/dashboard',
            'y tá' => '/nurse/dashboard',
            'nurse' => '/nurse/dashboard',

            'le_tan' => '/receptionist/dashboard',
            'letan' => '/receptionist/dashboard',
            'lễ tân' => '/receptionist/dashboard',
            'receptionist' => '/receptionist/dashboard',

            'tro_ly' => '/assistant/dashboard',
            'troly' => '/assistant/dashboard',
            'trợ lý' => '/assistant/dashboard',
            'assistant' => '/assistant/dashboard',
        ];

        return $roleRoutes[$vaiTro] ?? '/dashboard';
    }

    /**
     * Đăng xuất cho nhân viên
     */
    public function dangXuat(Request $request): JsonResponse
    {
        $nhanVien = $request->user();

        if (!$nhanVien) {
            return response()->json([
                'status' => false,
                'message' => 'Chưa đăng nhập.',
            ], 401);
        }

        try {
            // Xóa token hiện tại
            $token = $request->user()->currentAccessToken();
            if ($token) {
                $token->delete();
            } else {
                // Xóa tất cả token của nhân viên này
                $nhanVien->tokens()->delete();
            }

            return response()->json([
                'status' => true,
                'message' => 'Đăng xuất thành công.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('NhanVien logout failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi đăng xuất.',
            ], 500);
        }
    }
}
