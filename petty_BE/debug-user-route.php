<?php

/**
 * Script debug để kiểm tra user đang đăng nhập
 * Thêm vào route để test: Route::get('/debug-user', function(Request $request) { ... });
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('auth:sanctum')->get('/api/debug-user', function (Request $request) {
    $user = $request->user();

    return response()->json([
        'authenticated' => $user ? true : false,
        'user' => $user,
        'user_class' => $user ? get_class($user) : null,
        'is_admin' => $user instanceof \App\Models\Admin,
        'is_nhanvien' => $user instanceof \App\Models\NhanVien,
        'is_khachhang' => $user instanceof \App\Models\KhachHang,
        'is_user' => $user instanceof \App\Models\User,
        'token_abilities' => $request->user() ? $request->user()->currentAccessToken()?->abilities : null,
    ]);
});
