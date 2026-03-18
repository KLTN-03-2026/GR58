<?php

/**
 * Script cập nhật quyền Lịch Nhắc và Hồ Sơ Bệnh Án
 * Chạy: php update-lich-nhac-permissions.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║     CẬP NHẬT QUYỀN LỊCH NHẮC VÀ HỒ SƠ BỆNH ÁN CHO ADMIN       ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

try {
    // Cập nhật quyền cho Admin (ID = 1)
    echo "📋 Cập nhật quyền cho vai trò Admin...\n";

    $updated = DB::table('phan_quyens')
        ->where('id', 1)
        ->update([
            // Lịch nhắc
            'lich_nhac_xem' => true,
            'lich_nhac_tao' => true,
            'lich_nhac_sua' => true,
            'lich_nhac_xoa' => true,
            'lich_nhac_gui' => true,

            // Hồ sơ bệnh án
            'ho_so_benh_an_xem' => true,
            'ho_so_benh_an_tao' => true,
            'ho_so_benh_an_sua' => true,
            'ho_so_benh_an_xoa' => true,
        ]);

    echo "   ✓ Đã cập nhật quyền cho Admin\n\n";

    // Đảm bảo tất cả Admin có phan_quyen_id = 1
    echo "📋 Gán vai trò Admin cho tất cả tài khoản Admin...\n";

    $adminUpdated = DB::table('admins')
        ->whereNull('phan_quyen_id')
        ->orWhere('phan_quyen_id', '!=', 1)
        ->update(['phan_quyen_id' => 1]);

    if ($adminUpdated > 0) {
        echo "   ✓ Đã cập nhật {$adminUpdated} tài khoản Admin\n\n";
    } else {
        echo "   ✓ Tất cả Admin đã có vai trò đúng\n\n";
    }

    // Kiểm tra kết quả
    echo "📊 KIỂM TRA KẾT QUẢ:\n";
    echo str_repeat("═", 64) . "\n\n";

    $admins = DB::table('admins')
        ->leftJoin('phan_quyens', 'admins.phan_quyen_id', '=', 'phan_quyens.id')
        ->select(
            'admins.id',
            'admins.ho_ten',
            'admins.email',
            'admins.phan_quyen_id',
            'phan_quyens.ten_vai_tro',
            'phan_quyens.lich_nhac_xem',
            'phan_quyens.lich_nhac_tao',
            'phan_quyens.ho_so_benh_an_xem',
            'phan_quyens.ho_so_benh_an_tao'
        )
        ->get();

    foreach ($admins as $admin) {
        echo "👤 {$admin->ho_ten} ({$admin->email})\n";
        echo "   Vai trò: " . ($admin->ten_vai_tro ?? 'CHƯA CÓ') . "\n";
        echo "   Lịch nhắc - Xem: " . ($admin->lich_nhac_xem ? '✓' : '✗') .
            ", Tạo: " . ($admin->lich_nhac_tao ? '✓' : '✗') . "\n";
        echo "   Hồ sơ BA - Xem: " . ($admin->ho_so_benh_an_xem ? '✓' : '✗') .
            ", Tạo: " . ($admin->ho_so_benh_an_tao ? '✓' : '✗') . "\n\n";
    }

    echo str_repeat("═", 64) . "\n";
    echo "✅ HOÀN TẤT - Tất cả Admin đã có FULL quyền!\n";
    echo str_repeat("═", 64) . "\n\n";
} catch (\Exception $e) {
    echo "\n❌ LỖI: " . $e->getMessage() . "\n";
    echo "Chi tiết: " . $e->getTraceAsString() . "\n\n";
    exit(1);
}
