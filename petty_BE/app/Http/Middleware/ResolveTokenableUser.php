<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTokenableUser
{
    /**
     * Middleware chạy TRƯỚC auth:sanctum để resolve user từ token
     * cho tất cả models (Admin, NhanVien, KhachHang)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Lấy token từ header
        $token = $request->bearerToken();

        if ($token) {
            try {
                // Tìm token trong database
                $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

                if ($tokenModel && $tokenModel->tokenable) {
                    // Set user resolver để trả về đúng model (Admin, NhanVien, KhachHang)
                    $request->setUserResolver(fn() => $tokenModel->tokenable);
                }
            } catch (\Exception $e) {
                \Log::error('Error resolving tokenable user: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}
