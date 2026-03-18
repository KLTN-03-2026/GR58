<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\NhanVien;
use App\Models\KhachHang;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class MultiModelAuthProvider extends ServiceProvider
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
        // Custom Sanctum user provider để hỗ trợ multiple models
        // Khi auth:sanctum xác thực, nó sẽ tìm user từ PersonalAccessToken->tokenable
        // thay vì chỉ từ 1 model mặc định

        // Patch để Sanctum có thể resolve user từ bất kỳ model nào
        \Illuminate\Support\Facades\Auth::extend('sanctum.tokenable', function ($app, $name, array $config) {
            return new class {
                public function user($request)
                {
                    // Lấy token từ request
                    $token = $request->bearerToken();

                    if ($token) {
                        $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                        if ($tokenModel && $tokenModel->tokenable) {
                            return $tokenModel->tokenable;
                        }
                    }

                    return null;
                }

                public function validate(array $credentials = [])
                {
                    return false;
                }

                public function hash($value)
                {
                    return hash('sha256', $value);
                }
            };
        });
    }
}
