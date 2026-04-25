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
 * Usage:
 *  php artisan admin:create email mat_khau [ho_ten] [anh_dai_dien] [so_dien_thoai] [dia_chi] [trang_thai]
 */
Artisan::command('admin:create {email} {mat_khau} {ho_ten?} {anh_dai_dien?} {so_dien_thoai?} {dia_chi?} {trang_thai?}', function (string $email, string $mat_khau, ?string $ho_ten = null, ?string $anh_dai_dien = null, ?string $so_dien_thoai = null, ?string $dia_chi = null, ?string $trang_thai = null) {
    // Check existing admin by email
    if (Admin::where('email', $email)->exists()) {
        $this->error("Admin with email {$email} already exists.");
        return 1;
    }

    $admin = Admin::create([
        'email' => $email,
        'mat_khau' => Hash::make($mat_khau),
        'ho_ten' => $ho_ten ?? '',
        'anh_dai_dien' => $anh_dai_dien,
        'so_dien_thoai' => $so_dien_thoai,
        'dia_chi' => $dia_chi,
        'trang_thai' => is_null($trang_thai) ? 1 : (int) $trang_thai,
    ]);

    $this->info("Admin {$email} created with id {$admin->id}.");
    return 0;
})->purpose('Create an admin with provided fields');

// Tự động sinh lịch làm việc tháng sau vào ngày 25 hàng tháng lúc 23:00
Schedule::command('lich-lam-viec:generate', [
    now()->addMonth()->year,
    now()->addMonth()->month,
])->monthlyOn(25, '23:00');
