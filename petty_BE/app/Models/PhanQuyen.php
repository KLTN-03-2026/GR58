<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhanQuyen extends Model
{
    use HasFactory;

    protected $table = 'phan_quyens';

    protected $fillable = [
        'ma_vai_tro',
        'ten_vai_tro',
        'mo_ta',
        // Lịch hẹn
        'lich_hen_xem',
        'lich_hen_tao',
        'lich_hen_sua',
        'lich_hen_xoa',
        'lich_hen_xac_nhan',
        // Lịch làm việc
        'lich_lam_viec_xem',
        'lich_lam_viec_tao',
        // Lịch nhắc
        'lich_nhac_xem',
        'lich_nhac_tao',
        'lich_nhac_sua',
        'lich_nhac_xoa',
        'lich_nhac_gui',
        // Tài chính
        'tai_chinh_xem_doanh_thu',
        'tai_chinh_thu_tien',
        'tai_chinh_hoan_tien',
        // Phiếu chi
        'phieu_chi_xem',
        'phieu_chi_tao',
        'phieu_chi_sua',
        'phieu_chi_xoa',
        'phieu_chi_xuat_pdf',
        'phieu_chi_thanh_toan',
        // Hàng hóa
        'hang_hoa_xem',
        'hang_hoa_tao',
        'hang_hoa_sua',
        'hang_hoa_xoa',
        'hang_hoa_doi_trang_thai',
        // Danh mục hàng hóa
        'danh_muc_hang_hoa_xem',
        'danh_muc_hang_hoa_tao',
        'danh_muc_hang_hoa_sua',
        'danh_muc_hang_hoa_xoa',
        // Phiếu nhập kho
        'phieu_nhap_kho_xem',
        'phieu_nhap_kho_tao',
        'phieu_nhap_kho_doi_trang_thai',
        'phieu_nhap_kho_xuat_pdf',
        'phieu_nhap_kho_xoa',
        // Kiểm kê
        'kiem_ke_xem',
        'kiem_ke_tao',
        'kiem_ke_sua',
        'kiem_ke_xoa',
        // Thú cưng
        'thu_cung_xem',
        'thu_cung_tao',
        'thu_cung_sua',
        'thu_cung_xoa',
        // Hồ sơ bệnh án
        'ho_so_benh_an_xem',
        'ho_so_benh_an_tao',
        'ho_so_benh_an_sua',
        'ho_so_benh_an_xoa',
        // Dịch vụ
        'dich_vu_xem',
        'dich_vu_tao',
        'dich_vu_sua',
        'dich_vu_xoa',
        // Danh mục dịch vụ
        'danh_muc_dich_vu_xem',
        'danh_muc_dich_vu_tao',
        'danh_muc_dich_vu_sua',
        'danh_muc_dich_vu_xoa',
        // Khách hàng
        'khach_hang_xem',
        'khach_hang_sua',
        // Nhân viên
        'nhan_vien_xem',
        'nhan_vien_tao',
        'nhan_vien_doi_mat_khau',
        'nhan_vien_khoa_tai_khoan',
        'nhan_vien_mo_khoa_tai_khoan',
        // Khoa
        'khoa_tao',
        // Nhà cung cấp
        'nha_cung_cap_xem',
        'nha_cung_cap_tao',
        'nha_cung_cap_sua',
        'nha_cung_cap_xoa',
        'nha_cung_cap_doi_trang_thai',
        // Bài viết
        'bai_viet_xem',
        'bai_viet_tao',
        'bai_viet_sua',
        'bai_viet_xoa',
        // Phân loại bài viết
        'phan_loai_bai_viet_xem',
        'phan_loai_bai_viet_tao',
        'phan_loai_bai_viet_sua',
        'phan_loai_bai_viet_xoa',
        // Khuyến mãi
        'khuyen_mai_xem',
        'khuyen_mai_tao',
        'khuyen_mai_sua',
        'khuyen_mai_xoa',
        'khuyen_mai_doi_trang_thai',
        // Upload
        'upload_file',
    ];

    protected $casts = [
        // Cast tất cả boolean fields
        'lich_hen_xem' => 'boolean',
        'lich_hen_tao' => 'boolean',
        'lich_hen_sua' => 'boolean',
        'lich_hen_xoa' => 'boolean',
        'lich_hen_xac_nhan' => 'boolean',
        'lich_lam_viec_xem' => 'boolean',
        'lich_lam_viec_tao' => 'boolean',
        'lich_nhac_xem' => 'boolean',
        'lich_nhac_tao' => 'boolean',
        'lich_nhac_sua' => 'boolean',
        'lich_nhac_xoa' => 'boolean',
        'lich_nhac_gui' => 'boolean',
        'tai_chinh_xem_doanh_thu' => 'boolean',
        'tai_chinh_thu_tien' => 'boolean',
        'tai_chinh_hoan_tien' => 'boolean',
        'phieu_chi_xem' => 'boolean',
        'phieu_chi_tao' => 'boolean',
        'phieu_chi_sua' => 'boolean',
        'phieu_chi_xoa' => 'boolean',
        'phieu_chi_xuat_pdf' => 'boolean',
        'phieu_chi_thanh_toan' => 'boolean',
        'hang_hoa_xem' => 'boolean',
        'hang_hoa_tao' => 'boolean',
        'hang_hoa_sua' => 'boolean',
        'hang_hoa_xoa' => 'boolean',
        'hang_hoa_doi_trang_thai' => 'boolean',
        'danh_muc_hang_hoa_xem' => 'boolean',
        'danh_muc_hang_hoa_tao' => 'boolean',
        'danh_muc_hang_hoa_sua' => 'boolean',
        'danh_muc_hang_hoa_xoa' => 'boolean',
        'phieu_nhap_kho_xem' => 'boolean',
        'phieu_nhap_kho_tao' => 'boolean',
        'phieu_nhap_kho_doi_trang_thai' => 'boolean',
        'phieu_nhap_kho_xuat_pdf' => 'boolean',
        'phieu_nhap_kho_xoa' => 'boolean',
        'kiem_ke_xem' => 'boolean',
        'kiem_ke_tao' => 'boolean',
        'kiem_ke_sua' => 'boolean',
        'kiem_ke_xoa' => 'boolean',
        'thu_cung_xem' => 'boolean',
        'thu_cung_tao' => 'boolean',
        'thu_cung_sua' => 'boolean',
        'thu_cung_xoa' => 'boolean',
        'ho_so_benh_an_xem' => 'boolean',
        'ho_so_benh_an_tao' => 'boolean',
        'ho_so_benh_an_sua' => 'boolean',
        'ho_so_benh_an_xoa' => 'boolean',
        'dich_vu_xem' => 'boolean',
        'dich_vu_tao' => 'boolean',
        'dich_vu_sua' => 'boolean',
        'dich_vu_xoa' => 'boolean',
        'danh_muc_dich_vu_xem' => 'boolean',
        'danh_muc_dich_vu_tao' => 'boolean',
        'danh_muc_dich_vu_sua' => 'boolean',
        'danh_muc_dich_vu_xoa' => 'boolean',
        'khach_hang_xem' => 'boolean',
        'khach_hang_sua' => 'boolean',
        'nhan_vien_xem' => 'boolean',
        'nhan_vien_tao' => 'boolean',
        'nhan_vien_doi_mat_khau' => 'boolean',
        'nhan_vien_khoa_tai_khoan' => 'boolean',
        'nhan_vien_mo_khoa_tai_khoan' => 'boolean',
        'khoa_tao' => 'boolean',
        'nha_cung_cap_xem' => 'boolean',
        'nha_cung_cap_tao' => 'boolean',
        'nha_cung_cap_sua' => 'boolean',
        'nha_cung_cap_xoa' => 'boolean',
        'nha_cung_cap_doi_trang_thai' => 'boolean',
        'bai_viet_xem' => 'boolean',
        'bai_viet_tao' => 'boolean',
        'bai_viet_sua' => 'boolean',
        'bai_viet_xoa' => 'boolean',
        'phan_loai_bai_viet_xem' => 'boolean',
        'phan_loai_bai_viet_tao' => 'boolean',
        'phan_loai_bai_viet_sua' => 'boolean',
        'phan_loai_bai_viet_xoa' => 'boolean',
        'khuyen_mai_xem' => 'boolean',
        'khuyen_mai_tao' => 'boolean',
        'khuyen_mai_sua' => 'boolean',
        'khuyen_mai_xoa' => 'boolean',
        'khuyen_mai_doi_trang_thai' => 'boolean',
        'upload_file' => 'boolean',
    ];

    // Constants cho các vai trò
    public const VAI_TRO_ADMIN = 'Admin';
    public const VAI_TRO_BAC_SI = 'Bac_si';
    public const VAI_TRO_DIEU_DUONG = 'Dieu_duong';
    public const VAI_TRO_LE_TAN = 'Le_tan';
    public const VAI_TRO_TRO_LY = 'Tro_ly';

    /**
     * Kiểm tra quyền
     */
    public function hasPermission($permission)
    {
        return $this->$permission === true;
    }

    /**
     * Lấy tất cả quyền của vai trò
     */
    public function getAllPermissions()
    {
        $permissions = [];
        foreach ($this->fillable as $field) {
            if (
                strpos($field, '_xem') !== false ||
                strpos($field, '_tao') !== false ||
                strpos($field, '_sua') !== false ||
                strpos($field, '_xoa') !== false ||
                strpos($field, '_doi_') !== false ||
                strpos($field, 'upload_') !== false
            ) {
                if ($this->$field) {
                    $permissions[] = $field;
                }
            }
        }
        return $permissions;
    }
}
