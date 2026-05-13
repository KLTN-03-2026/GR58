<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schedule;
use App\Models\Admin;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/**
 * Create an admin record.
 */
Artisan::command('admin:create {email} {mat_khau} {ho_ten?} {anh_dai_dien?} {so_dien_thoai?} {dia_chi?} {trang_thai?}', function (string $email, string $mat_khau, ?string $ho_ten = null, ?string $anh_dai_dien = null, ?string $so_dien_thoai = null, ?string $dia_chi = null, ?string $trang_thai = null) {
    if (Admin::where('email', $email)->exists()) {
        $this->error("Admin with email {$email} already exists.");
        return 1;
    }

    $admin = Admin::create([
        'email'         => $email,
        'mat_khau'      => Hash::make($mat_khau),
        'ho_ten'        => $ho_ten ?? '',
        'anh_dai_dien'  => $anh_dai_dien,
        'so_dien_thoai' => $so_dien_thoai,
        'dia_chi'       => $dia_chi,
        'trang_thai'    => is_null($trang_thai) ? 1 : (int) $trang_thai,
    ]);

    $this->info("Admin {$email} created with id {$admin->id}.");
    return 0;
})->purpose('Create an admin with provided fields');

// ─── Lịch làm việc: tự động sinh tháng sau vào ngày 25 lúc 23:00 ────────────
Schedule::command('lich-lam-viec:generate', [
    now()->addMonth()->year,
    now()->addMonth()->month,
])->monthlyOn(25, '23:00');

// ─── Nhắc tiêm phòng hàng ngày lúc 8:00 sáng ────────────────────────────────
Schedule::command('thongbao:nhac-tiem-phong')->dailyAt('08:00');

// ─── Tự động hủy lịch hẹn quá 7 ngày chưa xử lý ─────────────────────────────
Schedule::command('lich-hen:cancel-overdue --days=7')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('✅ Scheduler: cancel-overdue chạy thành công.');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('❌ Scheduler: cancel-overdue thất bại.');
    });