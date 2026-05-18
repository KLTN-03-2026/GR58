<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use App\Models\Admin;
use App\Models\PhanQuyen;
use App\Models\LichLamViec;
use App\Models\LichHen;
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
use Carbon\Carbon;

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
    public function danhSachBacSi(Request $request)
    {
        $request->validate([
            'ngay_gio' => 'nullable|date',
        ]);

        if ($request->filled('ngay_gio')) {
            $data = $this->getDoctorsOnDuty(Carbon::parse($request->ngay_gio));
        } else {
            $data = NhanVien::where('vai_tro', 'bac_si')
                ->where('trang_thai', 'hoat_dong')
                ->select('id', 'full_name', 'chuc_danh')
                ->orderBy('full_name')
                ->get();
        }

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function goiYBacSi(Request $request)
    {
        $request->validate([
            'ngay_gio' => 'required|date',
        ]);

        $ngayGio = Carbon::parse($request->ngay_gio);
        $dateStr = $ngayGio->toDateString();
        $bacSiTruc = $this->getDoctorsOnDuty($ngayGio);

        if ($bacSiTruc->isEmpty()) {
            return response()->json([
                'status' => true,
                'data' => null,
                'message' => 'Không có bác sĩ trực vào khung giờ này.',
            ]);
        }

        // Count current appointments for each doctor in the same time slot
        $bacSiVoiTai = $bacSiTruc->map(function ($bacSi) use ($ngayGio, $dateStr) {
            $soLichHen = LichHen::where('nhan_vien_id', $bacSi['id'])
                ->whereIn('trang_thai', ['confirmed', 'in-progress'])
                ->where('ngay_gio', '>=', $ngayGio->copy()->subMinutes(59)->format('Y-m-d H:i:s'))
                ->where('ngay_gio', '<', $ngayGio->copy()->addMinutes(60)->format('Y-m-d H:i:s'))
                ->whereDate('ngay_gio', $dateStr)
                ->count();

            return [
                'id' => $bacSi['id'],
                'full_name' => $bacSi['full_name'],
                'chuc_danh' => $bacSi['chuc_danh'] ?? null,
                'so_lich_hen' => $soLichHen,
            ];
        });

        // Suggest doctor with least appointments
        $goiY = $bacSiVoiTai->sortBy('so_lich_hen')->first();

        return response()->json([
            'status' => true,
            'data' => [
                'goi_y' => $goiY,
                'tat_ca_bac_si' => $bacSiVoiTai->values(),
            ],
        ]);
    }

    private function getDoctorsOnDuty(Carbon $dateTime)
    {
        [$shiftDate, $shiftKey] = $this->resolveShiftContext($dateTime);

        return LichLamViec::whereDate('ngay_lam', $shiftDate->toDateString())
            ->where('thoi_gian_truc', $shiftKey)
            ->whereHas('nhanVien', fn ($q) => $q->where('vai_tro', 'bac_si')->where('trang_thai', 'hoat_dong'))
            ->with('nhanVien:id,full_name,chuc_danh')
            ->get()
            ->pluck('nhanVien')
            ->filter()
            ->unique('id')
            ->sortBy('full_name')
            ->values()
            ->map(fn ($doctor) => [
                'id' => $doctor->id,
                'full_name' => $doctor->full_name,
                'chuc_danh' => $doctor->chuc_danh,
            ]);
    }

    private function resolveShiftContext(Carbon $dateTime): array
    {
        $hour = (int) $dateTime->format('H');

        if ($hour >= 8 && $hour < 17) {
            return [$dateTime->copy(), LichLamViec::CA_SANG];
        }

        if ($hour >= 17) {
            return [$dateTime->copy(), LichLamViec::CA_TOI];
        }

        return [$dateTime->copy()->subDay(), LichLamViec::CA_TOI];
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

        // Bắt buộc đổi mật khẩu lần đầu đăng nhập
        $data['must_change_password'] = true;

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
     * Admin / staff (with permission nhan_vien_sua) can update an employee profile.
     */
    public function update(NhanVienRequest $request, NhanVien $nhanVien)
    {
        $data = $request->validated();

        // Never overwrite password through this endpoint — use changePassword instead.
        unset($data['password'], $data['password_confirmation']);

        // Re-map vai_tro -> phan_quyen_id if vai_tro changed
        if (array_key_exists('vai_tro', $data) && $data['vai_tro'] !== null) {
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
        }

        $nhanVien->fill($data);
        $nhanVien->save();

        // If account was just locked, revoke tokens to invalidate active sessions
        if (($data['trang_thai'] ?? null) === 'da_khoa') {
            try {
                $nhanVien->tokens()->delete();
            } catch (\Throwable $e) {
                // ignore token deletion errors
            }
        }

        $payload = $nhanVien->fresh()->toArray();
        $payload['anh_dai_dien_url'] = UserImageHelper::getAvatarUrl($nhanVien->anh_dai_dien);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật nhân viên thành công.',
            'data' => $payload,
        ]);
    }

    /**
     * Đổi mật khẩu lần đầu (self-service, không cần mật khẩu cũ).
     * Chỉ dùng được khi must_change_password = true.
     */
    public function firstChangePassword(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!($user instanceof NhanVien)) {
            return response()->json(['status' => false, 'message' => 'Không hợp lệ.'], 403);
        }

        $data = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required'  => 'Mật khẩu mới là bắt buộc.',
            'password.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user->password              = $data['password'];
        $user->must_change_password  = false;
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Đổi mật khẩu thành công. Chào mừng bạn!',
        ]);
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
            'must_change_password' => (bool) $nhanVien->must_change_password,
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
