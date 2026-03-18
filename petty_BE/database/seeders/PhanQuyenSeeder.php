<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PhanQuyen;

class PhanQuyenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('phan_quyens')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. ADMIN - Có tất cả quyền
        DB::table('phan_quyens')->insert([
            'ma_vai_tro' => PhanQuyen::VAI_TRO_ADMIN,
            'ten_vai_tro' => 'Admin',
            'mo_ta' => 'Quản trị viên - Có toàn quyền quản lý hệ thống',
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
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. BÁC SĨ - Quyền khám bệnh, kê đơn, xem thông tin
        DB::table('phan_quyens')->insert([
            'ma_vai_tro' => PhanQuyen::VAI_TRO_BAC_SI,
            'ten_vai_tro' => 'Bác sĩ',
            'mo_ta' => 'Bác sĩ - Khám bệnh, kê đơn, quản lý bệnh án',
            // Lịch hẹn
            'lich_hen_xem' => true,
            'lich_hen_tao' => false,
            'lich_hen_sua' => true,
            'lich_hen_xoa' => false,
            'lich_hen_xac_nhan' => true,
            // Lịch làm việc
            'lich_lam_viec_xem' => true,
            'lich_lam_viec_tao' => false,
            // Thú cưng / Bệnh án
            'thu_cung_xem' => true,
            'thu_cung_tao' => false,
            'thu_cung_sua' => true,
            'thu_cung_xoa' => false,
            // Dịch vụ
            'dich_vu_xem' => true,
            'dich_vu_tao' => false,
            'dich_vu_sua' => false,
            'dich_vu_xoa' => false,
            // Khách hàng
            'khach_hang_xem' => true,
            'khach_hang_sua' => false,
            // Hàng hóa (xem thuốc)
            'hang_hoa_xem' => true,
            'hang_hoa_tao' => false,
            'hang_hoa_sua' => false,
            'hang_hoa_xoa' => false,
            'hang_hoa_doi_trang_thai' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. ĐIỀU DƯỠNG - Hỗ trợ bác sĩ, quản lý kho thuốc
        DB::table('phan_quyens')->insert([
            'ma_vai_tro' => PhanQuyen::VAI_TRO_DIEU_DUONG,
            'ten_vai_tro' => 'Điều dưỡng',
            'mo_ta' => 'Điều dưỡng - Hỗ trợ khám bệnh, quản lý thuốc',
            // Lịch hẹn
            'lich_hen_xem' => true,
            'lich_hen_tao' => false,
            'lich_hen_sua' => true,
            'lich_hen_xoa' => false,
            'lich_hen_xac_nhan' => false,
            // Lịch làm việc
            'lich_lam_viec_xem' => true,
            'lich_lam_viec_tao' => false,
            // Thú cưng
            'thu_cung_xem' => true,
            'thu_cung_tao' => false,
            'thu_cung_sua' => false,
            'thu_cung_xoa' => false,
            // Hàng hóa
            'hang_hoa_xem' => true,
            'hang_hoa_tao' => false,
            'hang_hoa_sua' => false,
            'hang_hoa_xoa' => false,
            'hang_hoa_doi_trang_thai' => false,
            // Phiếu nhập kho
            'phieu_nhap_kho_xem' => true,
            'phieu_nhap_kho_tao' => true,
            'phieu_nhap_kho_doi_trang_thai' => false,
            'phieu_nhap_kho_xuat_pdf' => true,
            'phieu_nhap_kho_xoa' => false,
            // Kiểm kê
            'kiem_ke_xem' => true,
            'kiem_ke_tao' => true,
            'kiem_ke_sua' => true,
            'kiem_ke_xoa' => false,
            // Khách hàng
            'khach_hang_xem' => true,
            'khach_hang_sua' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. LỄ TÂN - Đặt lịch, thu tiền, quản lý khách hàng
        DB::table('phan_quyens')->insert([
            'ma_vai_tro' => PhanQuyen::VAI_TRO_LE_TAN,
            'ten_vai_tro' => 'Lễ tân',
            'mo_ta' => 'Lễ tân - Đặt lịch hẹn, thu tiền, quản lý khách hàng',
            // Lịch hẹn
            'lich_hen_xem' => true,
            'lich_hen_tao' => true,
            'lich_hen_sua' => true,
            'lich_hen_xoa' => true,
            'lich_hen_xac_nhan' => false,
            // Lịch làm việc
            'lich_lam_viec_xem' => true,
            'lich_lam_viec_tao' => false,
            // Tài chính
            'tai_chinh_xem_doanh_thu' => true,
            'tai_chinh_thu_tien' => true,
            'tai_chinh_hoan_tien' => true,
            // Thú cưng
            'thu_cung_xem' => true,
            'thu_cung_tao' => true,
            'thu_cung_sua' => true,
            'thu_cung_xoa' => false,
            // Dịch vụ
            'dich_vu_xem' => true,
            'dich_vu_tao' => false,
            'dich_vu_sua' => false,
            'dich_vu_xoa' => false,
            // Khách hàng
            'khach_hang_xem' => true,
            'khach_hang_sua' => true,
            // Khuyến mãi
            'khuyen_mai_xem' => true,
            'khuyen_mai_tao' => false,
            'khuyen_mai_sua' => false,
            'khuyen_mai_xoa' => false,
            'khuyen_mai_doi_trang_thai' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. TRỢ LÝ - Hỗ trợ hành chính, quản lý kho
        DB::table('phan_quyens')->insert([
            'ma_vai_tro' => PhanQuyen::VAI_TRO_TRO_LY,
            'ten_vai_tro' => 'Trợ lý',
            'mo_ta' => 'Trợ lý - Hỗ trợ công việc hành chính, quản lý kho',
            // Lịch hẹn
            'lich_hen_xem' => true,
            'lich_hen_tao' => false,
            'lich_hen_sua' => false,
            'lich_hen_xoa' => false,
            'lich_hen_xac_nhan' => false,
            // Hàng hóa
            'hang_hoa_xem' => true,
            'hang_hoa_tao' => true,
            'hang_hoa_sua' => true,
            'hang_hoa_xoa' => false,
            'hang_hoa_doi_trang_thai' => false,
            // Phiếu nhập kho
            'phieu_nhap_kho_xem' => true,
            'phieu_nhap_kho_tao' => true,
            'phieu_nhap_kho_doi_trang_thai' => false,
            'phieu_nhap_kho_xuat_pdf' => true,
            'phieu_nhap_kho_xoa' => false,
            // Kiểm kê
            'kiem_ke_xem' => true,
            'kiem_ke_tao' => true,
            'kiem_ke_sua' => true,
            'kiem_ke_xoa' => false,
            // Phiếu chi
            'phieu_chi_xem' => true,
            'phieu_chi_tao' => true,
            'phieu_chi_sua' => true,
            'phieu_chi_xoa' => false,
            'phieu_chi_xuat_pdf' => true,
            'phieu_chi_thanh_toan' => false,
            // Nhà cung cấp
            'nha_cung_cap_xem' => true,
            'nha_cung_cap_tao' => false,
            'nha_cung_cap_sua' => false,
            'nha_cung_cap_xoa' => false,
            'nha_cung_cap_doi_trang_thai' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
