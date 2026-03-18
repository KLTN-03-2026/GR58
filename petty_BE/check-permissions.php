<?php
/**
 * Script kiểm tra quyền chi tiết
 * Chạy: php check-permissions.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PhanQuyen;

echo "=== KIỂM TRA QUYỀN CHI TIẾT ===\n\n";

$phanQuyens = PhanQuyen::all();

foreach ($phanQuyens as $pq) {
    echo "Vai trò: {$pq->ten_vai_tro} (ID: {$pq->id})\n";
    echo "---\n";

    // Kiểm tra quyền phiếu chi
    echo "Phiếu chi:\n";
    echo "  - phieu_chi_xem: " . ($pq->phieu_chi_xem ? '✅' : '❌') . "\n";
    echo "  - phieu_chi_tao: " . ($pq->phieu_chi_tao ? '✅' : '❌') . "\n";
    echo "  - phieu_chi_sua: " . ($pq->phieu_chi_sua ? '✅' : '❌') . "\n";
    echo "  - phieu_chi_xoa: " . ($pq->phieu_chi_xoa ? '✅' : '❌') . "\n";
    echo "  - phieu_chi_xuat_pdf: " . ($pq->phieu_chi_xuat_pdf ? '✅' : '❌') . "\n";
    echo "  - phieu_chi_thanh_toan: " . ($pq->phieu_chi_thanh_toan ? '✅' : '❌') . "\n";

    // Kiểm tra quyền phiếu nhập kho
    echo "\nPhiếu nhập kho:\n";
    echo "  - phieu_nhap_kho_xem: " . ($pq->phieu_nhap_kho_xem ? '✅' : '❌') . "\n";
    echo "  - phieu_nhap_kho_tao: " . ($pq->phieu_nhap_kho_tao ? '✅' : '❌') . "\n";
    echo "  - phieu_nhap_kho_doi_trang_thai: " . ($pq->phieu_nhap_kho_doi_trang_thai ? '✅' : '❌') . "\n";
    echo "  - phieu_nhap_kho_xuat_pdf: " . ($pq->phieu_nhap_kho_xuat_pdf ? '✅' : '❌') . "\n";
    echo "  - phieu_nhap_kho_xoa: " . ($pq->phieu_nhap_kho_xoa ? '✅' : '❌') . "\n";

    echo "\n" . str_repeat("=", 50) . "\n\n";
}

// Kiểm tra user đầu tiên
echo "=== KIỂM TRA ADMIN ĐẦU TIÊN ===\n";
$admin = \App\Models\Admin::first();
if ($admin) {
    echo "Admin: {$admin->ho_ten}\n";
    echo "Email: {$admin->email}\n";
    echo "Vai trò ID: {$admin->phan_quyen_id}\n";

    if ($admin->phanQuyen) {
        echo "Tên vai trò: {$admin->phanQuyen->ten_vai_tro}\n";
        echo "Có quyền phieu_chi_xem: " . ($admin->hasPermission('phieu_chi_xem') ? '✅ CÓ' : '❌ KHÔNG') . "\n";
        echo "Có quyền phieu_nhap_kho_tao: " . ($admin->hasPermission('phieu_nhap_kho_tao') ? '✅ CÓ' : '❌ KHÔNG') . "\n";
    } else {
        echo "❌ CHƯA CÓ VAI TRÒ!\n";
    }
}

echo "\n✅ Kiểm tra hoàn tất!\n";
