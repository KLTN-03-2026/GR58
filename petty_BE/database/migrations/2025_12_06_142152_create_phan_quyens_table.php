<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('phan_quyens', function (Blueprint $table) {
            $table->id();
            $table->string('ma_vai_tro')->unique(); // Admin, Bac_si, Dieu_duong, Le_tan, Tro_ly
            $table->string('ten_vai_tro'); // Tên hiển thị
            $table->text('mo_ta')->nullable(); // Mô tả vai trò

            // ========== NHÓM LỊCH HẸN ==========
            $table->boolean('lich_hen_xem')->default(false);
            $table->boolean('lich_hen_tao')->default(false);
            $table->boolean('lich_hen_sua')->default(false);
            $table->boolean('lich_hen_xoa')->default(false);
            $table->boolean('lich_hen_xac_nhan')->default(false);

            // ========== NHÓM LỊCH LÀM VIỆC ==========
            $table->boolean('lich_lam_viec_xem')->default(false);
            $table->boolean('lich_lam_viec_tao')->default(false);

            // ========== NHÓM TÀI CHÍNH ==========
            $table->boolean('tai_chinh_xem_doanh_thu')->default(false);
            $table->boolean('tai_chinh_thu_tien')->default(false);
            $table->boolean('tai_chinh_hoan_tien')->default(false);

            // ========== NHÓM PHIẾU CHI ==========
            $table->boolean('phieu_chi_xem')->default(false);
            $table->boolean('phieu_chi_tao')->default(false);
            $table->boolean('phieu_chi_sua')->default(false);
            $table->boolean('phieu_chi_xoa')->default(false);
            $table->boolean('phieu_chi_xuat_pdf')->default(false);
            $table->boolean('phieu_chi_thanh_toan')->default(false);

            // ========== NHÓM KHO THUỐC / HÀNG HÓA ==========
            $table->boolean('hang_hoa_xem')->default(false);
            $table->boolean('hang_hoa_tao')->default(false);
            $table->boolean('hang_hoa_sua')->default(false);
            $table->boolean('hang_hoa_xoa')->default(false);
            $table->boolean('hang_hoa_doi_trang_thai')->default(false);

            // ========== NHÓM DANH MỤC HÀNG HÓA ==========
            $table->boolean('danh_muc_hang_hoa_xem')->default(false);
            $table->boolean('danh_muc_hang_hoa_tao')->default(false);
            $table->boolean('danh_muc_hang_hoa_sua')->default(false);
            $table->boolean('danh_muc_hang_hoa_xoa')->default(false);

            // ========== NHÓM PHIẾU NHẬP KHO ==========
            $table->boolean('phieu_nhap_kho_xem')->default(false);
            $table->boolean('phieu_nhap_kho_tao')->default(false);
            $table->boolean('phieu_nhap_kho_doi_trang_thai')->default(false);
            $table->boolean('phieu_nhap_kho_xuat_pdf')->default(false);
            $table->boolean('phieu_nhap_kho_xoa')->default(false);

            // ========== NHÓM KIỂM KÊ ==========
            $table->boolean('kiem_ke_xem')->default(false);
            $table->boolean('kiem_ke_tao')->default(false);
            $table->boolean('kiem_ke_sua')->default(false);
            $table->boolean('kiem_ke_xoa')->default(false);

            // ========== NHÓM BỆNH ÁN / THÚ CƯNG ==========
            $table->boolean('thu_cung_xem')->default(false);
            $table->boolean('thu_cung_tao')->default(false);
            $table->boolean('thu_cung_sua')->default(false);
            $table->boolean('thu_cung_xoa')->default(false);

            // ========== NHÓM DỊCH VỤ ==========
            $table->boolean('dich_vu_xem')->default(false);
            $table->boolean('dich_vu_tao')->default(false);
            $table->boolean('dich_vu_sua')->default(false);
            $table->boolean('dich_vu_xoa')->default(false);

            // ========== NHÓM DANH MỤC DỊCH VỤ ==========
            $table->boolean('danh_muc_dich_vu_xem')->default(false);
            $table->boolean('danh_muc_dich_vu_tao')->default(false);
            $table->boolean('danh_muc_dich_vu_sua')->default(false);
            $table->boolean('danh_muc_dich_vu_xoa')->default(false);

            // ========== NHÓM KHÁCH HÀNG ==========
            $table->boolean('khach_hang_xem')->default(false);
            $table->boolean('khach_hang_sua')->default(false);

            // ========== NHÓM NHÂN VIÊN ==========
            $table->boolean('nhan_vien_xem')->default(false);
            $table->boolean('nhan_vien_tao')->default(false);
            $table->boolean('nhan_vien_doi_mat_khau')->default(false);
            $table->boolean('nhan_vien_khoa_tai_khoan')->default(false);
            $table->boolean('nhan_vien_mo_khoa_tai_khoan')->default(false);

            // ========== NHÓM KHOA ==========
            $table->boolean('khoa_tao')->default(false);

            // ========== NHÓM NHÀ CUNG CẤP ==========
            $table->boolean('nha_cung_cap_xem')->default(false);
            $table->boolean('nha_cung_cap_tao')->default(false);
            $table->boolean('nha_cung_cap_sua')->default(false);
            $table->boolean('nha_cung_cap_xoa')->default(false);
            $table->boolean('nha_cung_cap_doi_trang_thai')->default(false);

            // ========== NHÓM BÀI VIẾT ==========
            $table->boolean('bai_viet_xem')->default(false);
            $table->boolean('bai_viet_tao')->default(false);
            $table->boolean('bai_viet_sua')->default(false);
            $table->boolean('bai_viet_xoa')->default(false);

            // ========== NHÓM PHÂN LOẠI BÀI VIẾT ==========
            $table->boolean('phan_loai_bai_viet_xem')->default(false);
            $table->boolean('phan_loai_bai_viet_tao')->default(false);
            $table->boolean('phan_loai_bai_viet_sua')->default(false);
            $table->boolean('phan_loai_bai_viet_xoa')->default(false);

            // ========== NHÓM KHUYẾN MÃI ==========
            $table->boolean('khuyen_mai_xem')->default(false);
            $table->boolean('khuyen_mai_tao')->default(false);
            $table->boolean('khuyen_mai_sua')->default(false);
            $table->boolean('khuyen_mai_xoa')->default(false);
            $table->boolean('khuyen_mai_doi_trang_thai')->default(false);

            // ========== NHÓM FILE/UPLOAD ==========
            $table->boolean('upload_file')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phan_quyens');
    }
};
