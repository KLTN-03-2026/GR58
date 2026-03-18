<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\NhanVien;
use App\Models\KhachHang;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class SanctumServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Đăng ký các models có thể dùng token
        Sanctum::usePersonalAccessTokenModel(\Laravel\Sanctum\PersonalAccessToken::class);
    }
}
