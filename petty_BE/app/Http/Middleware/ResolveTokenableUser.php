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
     *
     * Hỗ trợ token từ:
     *   1. Authorization: Bearer <token>  (API call thông thường)
     *   2. ?token=<token>                 (window.open export tab mới)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ưu tiên Bearer header, fallback sang query param
        $token = $request->bearerToken() ?? $request->query('token');

        if ($token) {
            try {
                $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

                if ($tokenModel && $tokenModel->tokenable) {
                    $request->setUserResolver(fn() => $tokenModel->tokenable);

                    // Nếu token đến từ query param (export tab),
                    // inject vào header để auth:sanctum middleware hoạt động bình thường
                    if (!$request->bearerToken()) {
                        $request->headers->set('Authorization', 'Bearer ' . $token);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error resolving tokenable user: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}