-- Script cấp quyền phiếu chi cho các vai trò cần thiết
-- Chạy trong phpMyAdmin hoặc MySQL command line

-- 1. Cấp quyền cho vai trò "Bác sĩ" (ID=2)
-- Bác sĩ có thể xem phiếu chi liên quan đến khám bệnh
UPDATE phan_quyens
SET phieu_chi_xem = 1,
    phieu_chi_xuat_pdf = 1
WHERE id = 2;

-- 2. Cấp quyền cho vai trò "Điều dưỡng" (ID=3)
-- Điều dưỡng có thể xem và tạo phiếu chi
UPDATE phan_quyens
SET phieu_chi_xem = 1,
    phieu_chi_tao = 1,
    phieu_chi_xuat_pdf = 1
WHERE id = 3;

-- 3. Cấp quyền cho vai trò "Lễ tân" (ID=4)
-- Lễ tân có thể xem phiếu chi
UPDATE phan_quyens
SET phieu_chi_xem = 1,
    phieu_chi_xuat_pdf = 1
WHERE id = 4;

-- 4. Vai trò "Trợ lý" (ID=5) đã có đủ quyền

-- Kiểm tra kết quả
SELECT
    id,
    ten_vai_tro,
    phieu_chi_xem,
    phieu_chi_tao,
    phieu_chi_sua,
    phieu_chi_xoa,
    phieu_chi_xuat_pdf,
    phieu_chi_thanh_toan
FROM phan_quyens
ORDER BY id;
