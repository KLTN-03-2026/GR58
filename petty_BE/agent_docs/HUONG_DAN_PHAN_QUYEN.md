# Hướng dẫn Hệ thống Phân quyền

## Tổng quan
Hệ thống phân quyền giúp kiểm soát quyền truy cập của Admin và Nhân viên vào các chức năng trong hệ thống.

## Cấu trúc

### 1. Bảng phan_quyens
Lưu trữ các vai trò và quyền hạn tương ứng:
- **ma_vai_tro**: Mã vai trò (admin, bac_si, dieu_duong, le_tan, tro_ly)
- **ten_vai_tro**: Tên hiển thị của vai trò
- **120+ quyền boolean**: lich_hen_xem, lich_hen_tao, hang_hoa_sua, ...

### 2. Liên kết với Admin và NhanVien
- Mỗi Admin/NhanVien có 1 vai trò (phan_quyen_id)
- Vai trò xác định quyền của họ

## 5 Vai trò mặc định

### 1. Admin
- Có tất cả các quyền (all permissions = true)
- Quản lý toàn bộ hệ thống

### 2. Bác sĩ
- Xem lịch hẹn, lịch làm việc
- Quản lý hồ sơ bệnh án
- Xem danh sách thuốc, dịch vụ
- Không quản lý kho, tài chính

### 3. Điều dưỡng
- Xem lịch hẹn
- Quản lý kho thuốc (nhập kho, kiểm kê)
- Hỗ trợ bác sĩ
- Không quản lý tài chính

### 4. Lễ tân
- Quản lý lịch hẹn (tạo, xem, sửa, xóa, xác nhận)
- Xem khách hàng, thú cưng
- Quản lý thanh toán
- Không quản lý kho, nhân viên

### 5. Trợ lý
- Xem lịch hẹn
- Xem hàng hóa, dịch vụ
- Hỗ trợ công việc hành chính cơ bản
- Quyền hạn hạn chế nhất

## Cách sử dụng

### API Quản lý Phân quyền

#### 1. Lấy danh sách tất cả vai trò
```
GET /api/phan-quyen
Headers: Authorization: Bearer {token}
```

Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "ma_vai_tro": "admin",
      "ten_vai_tro": "Admin",
      "lich_hen_xem": true,
      "lich_hen_tao": true,
      ...
    }
  ]
}
```

#### 2. Xem chi tiết vai trò
```
GET /api/phan-quyen/{id}
Headers: Authorization: Bearer {token}
```

#### 3. Cập nhật quyền của vai trò
```
PUT /api/phan-quyen/{id}
Headers: 
  Authorization: Bearer {token}
  Content-Type: application/json

Body:
{
  "ten_vai_tro": "Bác sĩ Chuyên khoa",
  "lich_hen_xem": true,
  "lich_hen_tao": false,
  "benh_an_xem": true,
  "benh_an_tao": true
}
```

#### 4. Lấy danh sách tất cả quyền (cho UI)
```
GET /api/phan-quyen/danh-sach/tat-ca-quyen
Headers: Authorization: Bearer {token}
```

Response: Trả về cấu trúc nhóm quyền để hiển thị checkbox trên UI:
```json
{
  "success": true,
  "data": {
    "lich_hen": {
      "label": "Lịch hẹn",
      "permissions": {
        "lich_hen_xem": "Xem lịch hẹn",
        "lich_hen_tao": "Tạo lịch hẹn",
        ...
      }
    },
    ...
  }
}
```

### Gán vai trò cho Admin/Nhân viên

Khi tạo hoặc cập nhật Admin/NhanVien, thêm field `phan_quyen_id`:

```json
POST /api/nhan-vien
{
  "full_name": "Nguyễn Văn A",
  "email": "nva@example.com",
  "vai_tro": "bac_si",
  "phan_quyen_id": 2,
  ...
}
```

### Kiểm tra quyền trong code

#### Trong Model (Admin/NhanVien):
```php
$user = auth()->user();
if ($user->hasPermission('lich_hen_tao')) {
    // Cho phép tạo lịch hẹn
}
```

#### Trong Routes (đã áp dụng):
```php
Route::post('/lich-hen', [LichHenController::class, 'store'])
    ->middleware('permission:lich_hen_tao');
```

#### Trong Controller:
```php
if (!$request->user()->hasPermission('hang_hoa_sua')) {
    return response()->json([
        'success' => false,
        'message' => 'Bạn không có quyền sửa hàng hóa'
    ], 403);
}
```

## Danh sách quyền theo nhóm

### Lịch hẹn (5 quyền)
- lich_hen_xem
- lich_hen_tao
- lich_hen_sua
- lich_hen_xoa
- lich_hen_xac_nhan

### Lịch làm việc (2 quyền)
- lich_lam_viec_xem
- lich_lam_viec_tao

### Tài chính (3 quyền)
- tai_chinh_xem_doanh_thu
- tai_chinh_thu_tien
- tai_chinh_hoan_tien

### Phiếu chi (6 quyền)
- phieu_chi_xem
- phieu_chi_tao
- phieu_chi_sua
- phieu_chi_xoa
- phieu_chi_xuat_pdf
- phieu_chi_thanh_toan

### Hàng hóa (5 quyền)
- hang_hoa_xem
- hang_hoa_tao
- hang_hoa_sua
- hang_hoa_xoa
- hang_hoa_doi_trang_thai

### Danh mục hàng hóa (4 quyền)
- danh_muc_hang_hoa_xem
- danh_muc_hang_hoa_tao
- danh_muc_hang_hoa_sua
- danh_muc_hang_hoa_xoa

### Phiếu nhập kho (5 quyền)
- phieu_nhap_kho_xem
- phieu_nhap_kho_tao
- phieu_nhap_kho_doi_trang_thai
- phieu_nhap_kho_xuat_pdf
- phieu_nhap_kho_xoa

### Kiểm kê (4 quyền)
- kiem_ke_xem
- kiem_ke_tao
- kiem_ke_sua
- kiem_ke_xoa

### Thú cưng (4 quyền)
- thu_cung_xem
- thu_cung_tao
- thu_cung_sua
- thu_cung_xoa

### Dịch vụ (4 quyền)
- dich_vu_xem
- dich_vu_tao
- dich_vu_sua
- dich_vu_xoa

### Danh mục dịch vụ (4 quyền)
- danh_muc_dich_vu_xem
- danh_muc_dich_vu_tao
- danh_muc_dich_vu_sua
- danh_muc_dich_vu_xoa

### Khách hàng (2 quyền)
- khach_hang_xem
- khach_hang_sua

### Nhân viên (5 quyền)
- nhan_vien_xem
- nhan_vien_tao
- nhan_vien_doi_mat_khau
- nhan_vien_khoa_tai_khoan
- nhan_vien_mo_khoa_tai_khoan

### Khoa (1 quyền)
- khoa_tao

### Nhà cung cấp (5 quyền)
- nha_cung_cap_xem
- nha_cung_cap_tao
- nha_cung_cap_sua
- nha_cung_cap_xoa
- nha_cung_cap_doi_trang_thai

### Bài viết (4 quyền)
- bai_viet_xem
- bai_viet_tao
- bai_viet_sua
- bai_viet_xoa

### Phân loại bài viết (4 quyền)
- phan_loai_bai_viet_xem
- phan_loai_bai_viet_tao
- phan_loai_bai_viet_sua
- phan_loai_bai_viet_xoa

### Khuyến mãi (5 quyền)
- khuyen_mai_xem
- khuyen_mai_tao
- khuyen_mai_sua
- khuyen_mai_xoa
- khuyen_mai_doi_trang_thai

### Upload (1 quyền)
- upload_file

### Bệnh án (4 quyền)
- benh_an_xem
- benh_an_tao
- benh_an_sua
- benh_an_xoa

## Các routes đã được áp dụng phân quyền

### Đã áp dụng middleware permission:
✅ Lịch hẹn - tất cả CRUD
✅ Lịch làm việc - xem và tạo
✅ Nhân viên - tất cả operations
✅ Khoa - tạo
✅ Hàng hóa - tất cả CRUD
✅ Dịch vụ - tất cả CRUD
✅ Phiếu nhập kho - tất cả operations
✅ Kiểm kê - tất cả CRUD
✅ Phiếu chi - tất cả operations

### Còn dùng EnsureAdmin (cần chuyển đổi nếu muốn):
- Danh mục dịch vụ
- Danh mục hàng hóa
- Upload file
- Phân loại bài viết
- Bài viết
- Nhà cung cấp
- Khuyến mãi
- Phân quyền (chỉ admin mới được quản lý)

## Lưu ý quan trọng

1. **Tất cả routes có middleware permission phải đăng nhập**
   - Routes đã trong group `middleware('auth:sanctum')`

2. **Khi tắt quyền**
   - Admin tắt quyền `lich_hen_tao` của vai trò "Lễ tân"
   - Tất cả lễ tân sẽ không tạo được lịch hẹn
   - API trả về 403 với message: "Bạn không có quyền thực hiện chức năng này"

3. **Thêm quyền mới**
   - Thêm column trong migration
   - Thêm vào $fillable và $casts trong model PhanQuyen
   - Cập nhật seeder để gán quyền cho vai trò phù hợp
   - Áp dụng middleware vào route

4. **Vai trò Admin luôn có full quyền**
   - Tất cả quyền = true trong seeder
   - Nên giữ nguyên để admin luôn quản lý được hệ thống

## Test thử

1. Đăng nhập với tài khoản Lễ tân
2. Thử gọi API tạo hàng hóa (không có quyền)
   - Kết quả: 403 Forbidden
3. Thử gọi API tạo lịch hẹn (có quyền)
   - Kết quả: 200 OK, tạo thành công

## Troubleshooting

### Lỗi "Bạn không có quyền thực hiện chức năng này"
- Kiểm tra user đã có phan_quyen_id chưa
- Kiểm tra quyền tương ứng trong bảng phan_quyens = true
- Kiểm tra tên quyền trong middleware khớp với DB

### User chưa có vai trò
- Chạy seeder: `php artisan db:seed --class=AssignDefaultRolesSeeder`
- Hoặc update thủ công: `UPDATE admins SET phan_quyen_id = 1 WHERE id = X`

### Thêm quyền mới không hoạt động
- Clear cache: `php artisan cache:clear`
- Kiểm tra migration đã chạy
- Kiểm tra tên quyền trong middleware khớp với column trong DB
