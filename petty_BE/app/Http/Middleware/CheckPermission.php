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
     * @param  string  $permission  Permission name to check (e.g., 'lich_hen.xem')
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

        // Nếu là Khách hàng, bỏ qua kiểm tra quyền (hoặc có thể kiểm tra quyền riêng)
        if ($user instanceof \App\Models\KhachHang || $user instanceof \App\Models\User) {
            return $next($request);
        }

        // Kiểm tra xem user có phương thức hasPermission không
        if (!method_exists($user, 'hasPermission')) {
            return response()->json([
                'success' => false,
                'message' => 'User model does not support permission checking.'
            ], 500);
        }

        // Kiểm tra quyền
        try {
            if (!$user->hasPermission($permission)) {
                Log::warning("User #{$user->id} attempted to access without permission: {$permission}");
                
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện chức năng này',
                    'required_permission' => $permission
                ], 403);
            }
        } catch (\Exception $e) {
            Log::error("Permission check error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Tài khoản chưa được phân quyền. Vui lòng liên hệ quản trị viên.',
                'error' => $e->getMessage()
            ], 403);
        }

        return $next($request);
    }
}
