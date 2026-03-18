<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  Tên quyền cần kiểm tra (vd: 'lich_hen_xem')
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        // Nếu chưa đăng nhập
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để tiếp tục'
            ], 401);
        }

        // Nếu là Khách hàng (User/KhachHang), bỏ qua kiểm tra quyền (cho phép truy cập)
        if ($user instanceof \App\Models\KhachHang || $user instanceof \App\Models\User) {
            return $next($request);
        }

        // AUTO-FIX: Tự động gán quyền nếu chưa có
        if (($user instanceof \App\Models\Admin || $user instanceof \App\Models\NhanVien) && empty($user->phan_quyen_id)) {
            \Log::warning("User #{$user->id} chưa có phan_quyen_id, tự động gán...");

            if ($user instanceof \App\Models\Admin) {
                $user->update(['phan_quyen_id' => 1]); // Admin mặc định
                $user->refresh();
            } elseif ($user instanceof \App\Models\NhanVien) {
                $roleMap = [
                    'Bác sĩ' => 2,
                    'Điều dưỡng' => 3,
                    'Lễ tân' => 4,
                ];
                $user->update(['phan_quyen_id' => $roleMap[$user->vai_tro] ?? 2]);
                $user->refresh();
            }
        }

        // Kiểm tra xem user có phương thức hasPermission không (cho Admin và NhanVien)
        if (!method_exists($user, 'hasPermission')) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xác định quyền của người dùng'
            ], 403);
        }

        // Kiểm tra quyền cho Admin và NhanVien
        try {
            if (!$user->hasPermission($permission)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện chức năng này',
                    'required_permission' => $permission
                ], 403);
            }
        } catch (\Exception $e) {
            // Nếu có lỗi khi check permission (ví dụ: phanQuyen null)
            \Log::error("Lỗi kiểm tra quyền: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Tài khoản chưa được phân quyền. Vui lòng liên hệ quản trị viên.',
                'error' => $e->getMessage()
            ], 403);
        }

        return $next($request);
    }
}
