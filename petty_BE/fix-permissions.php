<?php
/**
 * Script fix quyền nhanh
 * Chạy: php fix-permissions.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PhanQuyen;
use App\Models\Admin;
use App\Models\NhanVien;

echo "=== FIX QUYỀN CHO HỆ THỐNG ===\n\n";

// 1. Cập nhật quyền cho vai trò "Quản lý" (ID = 1)
echo "1. Cập nhật quyền cho vai trò 'Quản lý'...\n";
$quanLy = PhanQuyen::find(1);
if ($quanLy) {
    $quanLy->update([
        // Phiếu chi
        'phieu_chi_xem' => true,
        'phieu_chi_tao' => true,
        'phieu_chi_sua' => true,
        'phieu_chi_xoa' => true,
        'phieu_chi_xuat_pdf' => true,
        'phieu_chi_thanh_toan' => true,

        // Phiếu nhập kho
        'phieu_nhap_kho_xem' => true,
        'phieu_nhap_kho_tao' => true,
        'phieu_nhap_kho_doi_trang_thai' => true,
        'phieu_nhap_kho_xuat_pdf' => true,
        'phieu_nhap_kho_xoa' => true,

        // Hàng hóa
        'hang_hoa_xem' => true,
        'hang_hoa_tao' => true,
        'hang_hoa_sua' => true,
        'hang_hoa_xoa' => true,
        'hang_hoa_doi_trang_thai' => true,

        // Nhà cung cấp
        'nha_cung_cap_xem' => true,
        'nha_cung_cap_tao' => true,
        'nha_cung_cap_sua' => true,
        'nha_cung_cap_xoa' => true,
        'nha_cung_cap_doi_trang_thai' => true,

        // Kiểm kê
        'kiem_ke_xem' => true,
        'kiem_ke_tao' => true,
        'kiem_ke_sua' => true,
        'kiem_ke_xoa' => true,
    ]);
    echo "   ✅ Đã cập nhật quyền cho vai trò 'Quản lý'\n";
} else {
    echo "   ❌ Không tìm thấy vai trò 'Quản lý'\n";
}

// 2. Gán vai trò cho Admin chưa có phân quyền
echo "\n2. Gán vai trò cho Admin...\n";
$adminsWithoutRole = Admin::whereNull('phan_quyen_id')->get();
foreach ($adminsWithoutRole as $admin) {
    $admin->update(['phan_quyen_id' => 1]); // Gán vai trò Quản lý
    echo "   ✅ Đã gán vai trò 'Quản lý' cho Admin: {$admin->ho_ten}\n";
}
if ($adminsWithoutRole->isEmpty()) {
    echo "   ℹ️  Tất cả Admin đã có vai trò\n";
}

// 3. Gán vai trò cho NhanVien chưa có phân quyền
echo "\n3. Gán vai trò cho Nhân viên...\n";
$nhanViensWithoutRole = NhanVien::whereNull('phan_quyen_id')->get();
foreach ($nhanViensWithoutRole as $nv) {
    // Nếu là bác sĩ thì gán vai trò Bác sĩ (ID = 2), không thì gán Nhân viên (ID = 3)
    $roleId = (stripos($nv->chuc_vu, 'bác sĩ') !== false || stripos($nv->chuc_vu, 'bac si') !== false) ? 2 : 3;
    $nv->update(['phan_quyen_id' => $roleId]);
    $roleName = $roleId == 2 ? 'Bác sĩ' : 'Nhân viên';
    echo "   ✅ Đã gán vai trò '{$roleName}' cho: {$nv->full_name} ({$nv->chuc_vu})\n";
}
if ($nhanViensWithoutRole->isEmpty()) {
    echo "   ℹ️  Tất cả Nhân viên đã có vai trò\n";
}

// 4. Hiển thị tổng kết
echo "\n=== TỔNG KẾT ===\n";
echo "Tổng số Admin: " . Admin::count() . "\n";
echo "Admin có vai trò: " . Admin::whereNotNull('phan_quyen_id')->count() . "\n";
echo "Tổng số Nhân viên: " . NhanVien::count() . "\n";
echo "Nhân viên có vai trò: " . NhanVien::whereNotNull('phan_quyen_id')->count() . "\n";

echo "\n✅ HOÀN TẤT! Vui lòng đăng xuất và đăng nhập lại.\n";
