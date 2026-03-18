## 🔍 HƯỚNG DẪN FIX LỖI QUYỀN

### Nguyên nhân:
User đang đăng nhập **KHÔNG PHẢI Admin** (có thể là Bác sĩ, Điều dưỡng, Lễ tân, Trợ lý) nên không có quyền `phieu_chi_xem`.

### Giải pháp:

#### Option 1: Cấp quyền cho vai trò hiện tại

Nếu user cần xem phiếu chi, chạy lệnh sau (thay `{VAI_TRO_ID}` bằng ID vai trò):

```sql
-- Cấp quyền phiếu chi cho vai trò Bác sĩ (ID=2)
UPDATE phan_quyens 
SET phieu_chi_xem = 1,
    phieu_chi_tao = 1,
    phieu_chi_sua = 1,
    phieu_chi_xuat_pdf = 1
WHERE id = 2;

-- Hoặc cho Điều dưỡng (ID=3)
UPDATE phan_quyens 
SET phieu_chi_xem = 1,
    phieu_chi_tao = 1
WHERE id = 3;

-- Hoặc cho Lễ tân (ID=4)
UPDATE phan_quyens 
SET phieu_chi_xem = 1
WHERE id = 4;
```

#### Option 2: Đổi vai trò user thành Admin

Nếu user này PHẢI là Admin, chạy lệnh sau (thay `{USER_ID}` bằng ID của user):

```sql
-- Nếu là bảng admins
UPDATE admins SET phan_quyen_id = 1 WHERE id = {USER_ID};

-- Nếu là bảng nhan_viens
UPDATE nhan_viens SET phan_quyen_id = 1 WHERE id = {USER_ID};
```

#### Option 3: Xóa middleware phân quyền (KHÔNG KHUYẾN KHÍCH)

Chỉ dùng tạm thời để test:

```php
// routes/api.php
// Tìm dòng:
Route::get('/phieu-chi', [PhieuChiController::class, 'index'])->middleware(['staff.only', 'permission:phieu_chi_xem']);

// Đổi thành:
Route::get('/phieu-chi', [PhieuChiController::class, 'index'])->middleware(['staff.only']);
```

### ✅ Sau khi fix:
1. **ĐĂNG XUẤT**
2. **ĐĂNG NHẬP LẠI**
3. Test lại

### 🔎 Kiểm tra user hiện tại:
Gọi API: `GET /api/user` (với token Bearer) để xem:
- `user_type`: admin / nhan_vien / khach_hang
- `phan_quyen_id`: ID vai trò
- `has_phieu_nhap_kho_tao`: Trạng thái quyền
