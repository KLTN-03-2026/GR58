<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMultiModel
{
    /**
     * Handle an incoming request.
     * Xác thực user từ token cho cả Admin, NhanVien, KhachHang
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Nếu chưa có user từ auth:sanctum
        if (!$request->user()) {
            // Cố gắng lấy token từ header
            $token = $request->bearerToken();

            if ($token) {
                // Tìm user từ personal access tokens - kiểm tra tất cả models
                $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

                if ($tokenModel) {
                    // Đặt user vào request
                    $request->setUserResolver(function () use ($tokenModel) {
                        return $tokenModel->tokenable;
                    });
                }
            }
        }

        return $next($request);
    }
}
