-- Script fix quyền cho tất cả Admin và NhanVien
-- Chạy trong phpMyAdmin hoặc MySQL command line

-- Cập nhật quyền cho vai trò "Quản lý" (ID = 1)
UPDATE phan_quyens
SET
    -- Phiếu chi
    phieu_chi_xem = 1,
    phieu_chi_tao = 1,
    phieu_chi_sua = 1,
    phieu_chi_xoa = 1,
    phieu_chi_xuat_pdf = 1,
    phieu_chi_thanh_toan = 1,

    -- Phiếu nhập kho
    phieu_nhap_kho_xem = 1,
    phieu_nhap_kho_tao = 1,
    phieu_nhap_kho_doi_trang_thai = 1,
    phieu_nhap_kho_xuat_pdf = 1,
    phieu_nhap_kho_xoa = 1,

    -- Hàng hóa
    hang_hoa_xem = 1,
    hang_hoa_tao = 1,
    hang_hoa_sua = 1,
    hang_hoa_xoa = 1,
    hang_hoa_doi_trang_thai = 1,

    -- Nhà cung cấp
    nha_cung_cap_xem = 1,
    nha_cung_cap_tao = 1,
    nha_cung_cap_sua = 1,
    nha_cung_cap_xoa = 1,
    nha_cung_cap_doi_trang_thai = 1,

    -- Kiểm kê
    kiem_ke_xem = 1,
    kiem_ke_tao = 1,
    kiem_ke_sua = 1,
    kiem_ke_xoa = 1
WHERE id = 1;

-- Gán vai trò "Quản lý" cho tất cả Admin nếu chưa có
UPDATE admins
SET phan_quyen_id = 1
WHERE phan_quyen_id IS NULL;

-- Gán vai trò "Bác sĩ" (ID = 2) cho NhanVien bác sĩ nếu chưa có
UPDATE nhan_viens
SET phan_quyen_id = 2
WHERE phan_quyen_id IS NULL AND chuc_vu LIKE '%Bác sĩ%';

-- Gán vai trò "Nhân viên" (ID = 3) cho NhanVien còn lại nếu chưa có
UPDATE nhan_viens
SET phan_quyen_id = 3
WHERE phan_quyen_id IS NULL;

-- Kiểm tra kết quả
SELECT
    'Admin' as loai,
    a.id,
    a.ho_ten as ten,
    a.phan_quyen_id,
    pq.ten_vai_tro
FROM admins a
LEFT JOIN phan_quyens pq ON a.phan_quyen_id = pq.id
UNION ALL
SELECT
    'NhanVien' as loai,
    nv.id,
    nv.full_name as ten,
    nv.phan_quyen_id,
    pq.ten_vai_tro
FROM nhan_viens nv
LEFT JOIN phan_quyens pq ON nv.phan_quyen_id = pq.id;
