<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\ThuCung;
use App\Models\Admin;
use App\Models\NhanVien;
use App\Policies\ThuCungPolicy;
use App\Observers\AdminObserver;
use App\Observers\NhanVienObserver;
use App\Services\SepayService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SepayService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register ThuCung policy so authorization can use $this->authorize()
        Gate::policy(ThuCung::class, ThuCungPolicy::class);

        // Register observers để tự động gán quyền
        Admin::observe(AdminObserver::class);
        NhanVien::observe(NhanVienObserver::class);
        \App\Models\LichHen::observe(\App\Observers\LichHenObserver::class);
        \App\Models\PhieuKham::observe(\App\Observers\PhieuKhamObserver::class);
        \App\Models\PhieuChi::observe(\App\Observers\PhieuChiObserver::class);
        \App\Models\PhieuNhapKho::observe(\App\Observers\PhieuNhapKhoObserver::class);
    }
}
