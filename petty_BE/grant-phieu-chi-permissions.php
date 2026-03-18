<?php
/**
 * Script cấp quyền phiếu chi cho các vai trò
 * Chạy: php grant-phieu-chi-permissions.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PhanQuyen;

echo "=== CẤP QUYỀN PHIẾU CHI CHO CÁC VAI TRÒ ===\n\n";

// 1. Bác sĩ - Có thể xem phiếu chi
echo "1. Cấp quyền cho vai trò 'Bác sĩ'...\n";
$bacSi = PhanQuyen::find(2);
if ($bacSi) {
    $bacSi->update([
        'phieu_chi_xem' => true,
        'phieu_chi_xuat_pdf' => true,
    ]);
    echo "   ✅ Bác sĩ có thể: Xem, Xuất PDF phiếu chi\n";
} else {
    echo "   ❌ Không tìm thấy vai trò Bác sĩ\n";
}

// 2. Điều dưỡng - Có thể xem và tạo phiếu chi
echo "\n2. Cấp quyền cho vai trò 'Điều dưỡng'...\n";
$dieuDuong = PhanQuyen::find(3);
if ($dieuDuong) {
    $dieuDuong->update([
        'phieu_chi_xem' => true,
        'phieu_chi_tao' => true,
        'phieu_chi_xuat_pdf' => true,
    ]);
    echo "   ✅ Điều dưỡng có thể: Xem, Tạo, Xuất PDF phiếu chi\n";
} else {
    echo "   ❌ Không tìm thấy vai trò Điều dưỡng\n";
}

// 3. Lễ tân - Có thể xem phiếu chi
echo "\n3. Cấp quyền cho vai trò 'Lễ tân'...\n";
$leTan = PhanQuyen::find(4);
if ($leTan) {
    $leTan->update([
        'phieu_chi_xem' => true,
        'phieu_chi_xuat_pdf' => true,
    ]);
    echo "   ✅ Lễ tân có thể: Xem, Xuất PDF phiếu chi\n";
} else {
    echo "   ❌ Không tìm thấy vai trò Lễ tân\n";
}

// 4. Trợ lý - Đã có đủ quyền
echo "\n4. Vai trò 'Trợ lý' đã có đủ quyền ✅\n";

echo "\n=== TỔNG KẾT ===\n";
$phanQuyens = PhanQuyen::all();
foreach ($phanQuyens as $pq) {
    echo "\n{$pq->ten_vai_tro}:\n";
    echo "  - Xem phiếu chi: " . ($pq->phieu_chi_xem ? '✅' : '❌') . "\n";
    echo "  - Tạo phiếu chi: " . ($pq->phieu_chi_tao ? '✅' : '❌') . "\n";
    echo "  - Sửa phiếu chi: " . ($pq->phieu_chi_sua ? '✅' : '❌') . "\n";
}

echo "\n✅ HOÀN TẤT! Vui lòng đăng xuất và đăng nhập lại.\n";
