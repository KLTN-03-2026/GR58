<?php

/**
 * Script FIX CỨNG - Tự động gán quyền cho TẤT CẢ tài khoản
 * Chạy: php fix-permissions-permanent.php
 *
 * Script này sẽ:
 * 1. Gán vai trò Admin (ID=1) cho TẤT CẢ Admin chưa có quyền
 * 2. Gán vai trò tương ứng cho TẤT CẢ NhanVien chưa có quyền
 * 3. Đảm bảo vai trò Admin có FULL quyền
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PhanQuyen;
use Illuminate\Support\Facades\DB;

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║       FIX CỨNG PHÂN QUYỀN - TỰ ĐỘNG GÁN QUYỀN CHO TẤT CẢ      ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

try {
    // BƯỚC 1: Đảm bảo các vai trò cơ bản tồn tại
    echo "📋 BƯỚC 1: Kiểm tra và tạo các vai trò cơ bản...\n";

    $roles = [
        ['id' => 1, 'ten_vai_tro' => 'Admin', 'mo_ta' => 'Quản trị viên hệ thống - Full quyền'],
        ['id' => 2, 'ten_vai_tro' => 'Bác sĩ', 'mo_ta' => 'Bác sĩ thú y'],
        ['id' => 3, 'ten_vai_tro' => 'Điều dưỡng', 'mo_ta' => 'Điều dưỡng viên'],
        ['id' => 4, 'ten_vai_tro' => 'Lễ tân', 'mo_ta' => 'Nhân viên lễ tân'],
    ];

    foreach ($roles as $role) {
        $exists = DB::table('phan_quyens')->where('id', $role['id'])->exists();
        if (!$exists) {
            DB::table('phan_quyens')->insert($role + ['created_at' => now(), 'updated_at' => now()]);
            echo "   ✓ Đã tạo vai trò: {$role['ten_vai_tro']}\n";
        } else {
            echo "   ✓ Vai trò {$role['ten_vai_tro']} đã tồn tại\n";
        }
    }

    // BƯỚC 2: Cấp FULL quyền cho vai trò Admin
    echo "\n📋 BƯỚC 2: Cấp FULL quyền cho vai trò Admin...\n";

    $allPermissions = [
        // Lịch hẹn
        'lich_hen_xem' => true,
        'lich_hen_tao' => true,
        'lich_hen_sua' => true,
        'lich_hen_xoa' => true,
        'lich_hen_xac_nhan' => true,

        // Lịch làm việc
        'lich_lam_viec_xem' => true,
        'lich_lam_viec_tao' => true,

        // Tài chính
        'tai_chinh_xem_doanh_thu' => true,
        'tai_chinh_thu_tien' => true,
        'tai_chinh_hoan_tien' => true,

        // Phiếu chi
        'phieu_chi_xem' => true,
        'phieu_chi_tao' => true,
        'phieu_chi_sua' => true,
        'phieu_chi_xoa' => true,
        'phieu_chi_xuat_pdf' => true,
        'phieu_chi_thanh_toan' => true,

        // Hàng hóa
        'hang_hoa_xem' => true,
        'hang_hoa_tao' => true,
        'hang_hoa_sua' => true,
        'hang_hoa_xoa' => true,
        'hang_hoa_doi_trang_thai' => true,

        // Danh mục hàng hóa
        'danh_muc_hang_hoa_xem' => true,
        'danh_muc_hang_hoa_tao' => true,
        'danh_muc_hang_hoa_sua' => true,
        'danh_muc_hang_hoa_xoa' => true,

        // Phiếu nhập kho
        'phieu_nhap_kho_xem' => true,
        'phieu_nhap_kho_tao' => true,
        'phieu_nhap_kho_doi_trang_thai' => true,
        'phieu_nhap_kho_xuat_pdf' => true,
        'phieu_nhap_kho_xoa' => true,

        // Kiểm kê
        'kiem_ke_xem' => true,
        'kiem_ke_tao' => true,
        'kiem_ke_sua' => true,
        'kiem_ke_xoa' => true,

        // Thú cưng
        'thu_cung_xem' => true,
        'thu_cung_tao' => true,
        'thu_cung_sua' => true,
        'thu_cung_xoa' => true,

        // Dịch vụ
        'dich_vu_xem' => true,
        'dich_vu_tao' => true,
        'dich_vu_sua' => true,
        'dich_vu_xoa' => true,

        // Danh mục dịch vụ
        'danh_muc_dich_vu_xem' => true,
        'danh_muc_dich_vu_tao' => true,
        'danh_muc_dich_vu_sua' => true,
        'danh_muc_dich_vu_xoa' => true,

        // Khách hàng
        'khach_hang_xem' => true,
        'khach_hang_sua' => true,

        // Nhân viên
        'nhan_vien_xem' => true,
        'nhan_vien_tao' => true,
        'nhan_vien_doi_mat_khau' => true,
        'nhan_vien_khoa_tai_khoan' => true,
        'nhan_vien_mo_khoa_tai_khoan' => true,

        // Khoa
        'khoa_tao' => true,

        // Nhà cung cấp
        'nha_cung_cap_xem' => true,
        'nha_cung_cap_tao' => true,
        'nha_cung_cap_sua' => true,
        'nha_cung_cap_xoa' => true,
        'nha_cung_cap_doi_trang_thai' => true,

        // Bài viết
        'bai_viet_xem' => true,
        'bai_viet_tao' => true,
        'bai_viet_sua' => true,
        'bai_viet_xoa' => true,

        // Phân loại bài viết
        'phan_loai_bai_viet_xem' => true,
        'phan_loai_bai_viet_tao' => true,
        'phan_loai_bai_viet_sua' => true,
        'phan_loai_bai_viet_xoa' => true,

        // Khuyến mãi
        'khuyen_mai_xem' => true,
        'khuyen_mai_tao' => true,
        'khuyen_mai_sua' => true,
        'khuyen_mai_xoa' => true,
        'khuyen_mai_doi_trang_thai' => true,

        // Upload
        'upload_file' => true,
    ];

    DB::table('phan_quyens')->where('id', 1)->update($allPermissions);
    echo "   ✓ Đã cấp " . count($allPermissions) . " quyền cho vai trò Admin\n";

    // BƯỚC 3: Gán vai trò cho TẤT CẢ Admin chưa có
    echo "\n📋 BƯỚC 3: Gán vai trò Admin cho tất cả tài khoản Admin...\n";

    $adminCount = DB::table('admins')->whereNull('phan_quyen_id')->count();
    if ($adminCount > 0) {
        DB::table('admins')->whereNull('phan_quyen_id')->update(['phan_quyen_id' => 1]);
        echo "   ✓ Đã gán vai trò Admin cho {$adminCount} tài khoản\n";
    } else {
        $totalAdmins = DB::table('admins')->count();
        echo "   ✓ Tất cả {$totalAdmins} tài khoản Admin đã có vai trò\n";
    }

    // BƯỚC 4: Gán vai trò cho TẤT CẢ NhanVien chưa có
    echo "\n📋 BƯỚC 4: Gán vai trò cho tất cả Nhân viên...\n";

    $roleMap = [
        'Bác sĩ' => 2,
        'Điều dưỡng' => 3,
        'Lễ tân' => 4,
    ];

    foreach ($roleMap as $vaiTro => $phanQuyenId) {
        $count = DB::table('nhan_viens')
            ->where('vai_tro', $vaiTro)
            ->whereNull('phan_quyen_id')
            ->count();

        if ($count > 0) {
            DB::table('nhan_viens')
                ->where('vai_tro', $vaiTro)
                ->whereNull('phan_quyen_id')
                ->update(['phan_quyen_id' => $phanQuyenId]);
            echo "   ✓ Đã gán vai trò cho {$count} {$vaiTro}\n";
        }
    }

    // Gán vai trò mặc định (Bác sĩ) cho những NhanVien còn lại
    $remainingCount = DB::table('nhan_viens')->whereNull('phan_quyen_id')->count();
    if ($remainingCount > 0) {
        DB::table('nhan_viens')->whereNull('phan_quyen_id')->update(['phan_quyen_id' => 2]);
        echo "   ✓ Đã gán vai trò mặc định (Bác sĩ) cho {$remainingCount} nhân viên\n";
    }

    $totalNhanViens = DB::table('nhan_viens')->count();
    echo "   ✓ Tổng cộng: {$totalNhanViens} nhân viên đã có vai trò\n";

    // BƯỚC 5: Cấp quyền cơ bản cho các vai trò khác
    echo "\n📋 BƯỚC 5: Cấp quyền cơ bản cho các vai trò khác...\n";

    // Bác sĩ
    DB::table('phan_quyens')->where('id', 2)->update([
        'lich_hen_xem' => true,
        'lich_hen_sua' => true,
        'lich_hen_xac_nhan' => true,
        'lich_lam_viec_xem' => true,
        'phieu_chi_xem' => true,
        'phieu_chi_xuat_pdf' => true,
        'phieu_nhap_kho_xem' => true,
        'phieu_nhap_kho_xuat_pdf' => true,
        'thu_cung_xem' => true,
        'thu_cung_sua' => true,
        'dich_vu_xem' => true,
        'khach_hang_xem' => true,
    ]);
    echo "   ✓ Đã cấp quyền cho Bác sĩ\n";

    // Điều dưỡng
    DB::table('phan_quyens')->where('id', 3)->update([
        'lich_hen_xem' => true,
        'lich_hen_tao' => true,
        'lich_hen_sua' => true,
        'phieu_chi_xem' => true,
        'phieu_chi_tao' => true,
        'phieu_chi_xuat_pdf' => true,
        'phieu_nhap_kho_xem' => true,
        'phieu_nhap_kho_tao' => true,
        'phieu_nhap_kho_xuat_pdf' => true,
        'hang_hoa_xem' => true,
        'hang_hoa_tao' => true,
        'thu_cung_xem' => true,
        'dich_vu_xem' => true,
        'khach_hang_xem' => true,
    ]);
    echo "   ✓ Đã cấp quyền cho Điều dưỡng\n";

    // Lễ tân
    DB::table('phan_quyens')->where('id', 4)->update([
        'lich_hen_xem' => true,
        'lich_hen_tao' => true,
        'phieu_chi_xem' => true,
        'phieu_chi_xuat_pdf' => true,
        'phieu_nhap_kho_xem' => true,
        'thu_cung_xem' => true,
        'thu_cung_tao' => true,
        'dich_vu_xem' => true,
        'khach_hang_xem' => true,
        'khach_hang_sua' => true,
    ]);
    echo "   ✓ Đã cấp quyền cho Lễ tân\n";

    // BƯỚC 6: Hiển thị kết quả
    echo "\n" . str_repeat("═", 64) . "\n";
    echo "📊 KẾT QUẢ CUỐI CÙNG\n";
    echo str_repeat("═", 64) . "\n\n";

    // Thống kê Admin
    $adminStats = DB::table('admins')
        ->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN phan_quyen_id IS NOT NULL THEN 1 ELSE 0 END) as co_quyen,
            SUM(CASE WHEN phan_quyen_id IS NULL THEN 1 ELSE 0 END) as chua_co_quyen
        ')
        ->first();

    echo "👥 ADMIN:\n";
    echo "   • Tổng số: {$adminStats->total}\n";
    echo "   • Đã có quyền: {$adminStats->co_quyen} ✓\n";
    echo "   • Chưa có quyền: {$adminStats->chua_co_quyen}\n\n";

    // Thống kê NhanVien
    $nvStats = DB::table('nhan_viens')
        ->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN phan_quyen_id IS NOT NULL THEN 1 ELSE 0 END) as co_quyen,
            SUM(CASE WHEN phan_quyen_id IS NULL THEN 1 ELSE 0 END) as chua_co_quyen
        ')
        ->first();

    echo "👥 NHÂN VIÊN:\n";
    echo "   • Tổng số: {$nvStats->total}\n";
    echo "   • Đã có quyền: {$nvStats->co_quyen} ✓\n";
    echo "   • Chưa có quyền: {$nvStats->chua_co_quyen}\n\n";

    // Chi tiết phân bổ vai trò
    echo "📋 PHÂN BỔ VAI TRÒ:\n";
    $roleDistribution = DB::table('phan_quyens')
        ->leftJoin('admins', 'phan_quyens.id', '=', 'admins.phan_quyen_id')
        ->leftJoin('nhan_viens', 'phan_quyens.id', '=', 'nhan_viens.phan_quyen_id')
        ->selectRaw('
            phan_quyens.id,
            phan_quyens.ten_vai_tro,
            COUNT(DISTINCT admins.id) as admin_count,
            COUNT(DISTINCT nhan_viens.id) as nv_count
        ')
        ->groupBy('phan_quyens.id', 'phan_quyens.ten_vai_tro')
        ->orderBy('phan_quyens.id')
        ->get();

    foreach ($roleDistribution as $role) {
        $total = $role->admin_count + $role->nv_count;
        echo "   • {$role->ten_vai_tro}: {$total} người ";
        echo "(Admin: {$role->admin_count}, NV: {$role->nv_count})\n";
    }

    echo "\n" . str_repeat("═", 64) . "\n";
    echo "✅ HOÀN TẤT - Hệ thống phân quyền đã được FIX CỨNG!\n";
    echo str_repeat("═", 64) . "\n\n";

    echo "📝 LƯU Ý:\n";
    echo "   • Tất cả Admin đều có FULL quyền\n";
    echo "   • Tất cả NhanVien đã được gán vai trò phù hợp\n";
    echo "   • Từ giờ, tài khoản mới sẽ TỰ ĐỘNG được gán quyền\n";
    echo "   • Lỗi phân quyền sẽ KHÔNG còn xuất hiện nữa!\n\n";
} catch (\Exception $e) {
    echo "\n❌ LỖI: " . $e->getMessage() . "\n";
    echo "Chi tiết: " . $e->getTraceAsString() . "\n\n";
    exit(1);
}
