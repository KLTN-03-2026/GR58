<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffOnly
{
    /**
     * Handle an incoming request.
     * Chỉ cho phép Admin và NhanVien truy cập
     * Chặn KhachHang/User
     */
    public function handle(Request $request, Closure $next): Response
    {
        // LUÔN TRY TÌM USER TỪ TOKEN TRƯỚC
        $token = $request->bearerToken();
        $user = null;

        if ($token) {
            try {
                $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($tokenModel && $tokenModel->tokenable) {
                    $user = $tokenModel->tokenable;
                }
            } catch (\Exception $e) {
                \Log::error('Error finding token: ' . $e->getMessage());
            }
        }

        // Fallback to request->user() if token resolution failed
        if (!$user) {
            $user = $request->user();
        }

        // Debug: log user info
        \Log::debug('StaffOnly middleware - Final user:', [
            'user_class' => $user ? get_class($user) : null,
            'user_id' => $user ? $user->id : null,
            'is_admin' => $user instanceof \App\Models\Admin,
            'is_nhanvien' => $user instanceof \App\Models\NhanVien,
            'is_khachhang' => $user instanceof \App\Models\KhachHang,
        ]);

        // Nếu vẫn không có user
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để tiếp tục'
            ], 401);
        }

        // Chỉ cho phép Admin và NhanVien
        if ($user instanceof \App\Models\Admin || $user instanceof \App\Models\NhanVien) {
            // Update request user nếu lấy từ token
            if ($token) {
                $request->setUserResolver(fn() => $user);
            }
            return $next($request);
        }

        // Chặn Khách hàng
        return response()->json([
            'success' => false,
            'message' => 'Chỉ quản trị viên và nhân viên mới có thể truy cập chức năng này.',
            'debug' => [
                'user_class' => $user ? get_class($user) : null,
                'user_id' => $user ? $user->id : null,
            ]
        ], 403);
    }
}
