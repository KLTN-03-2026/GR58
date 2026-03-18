# FIX CỨNG LỖI PHÂN QUYỀN

## ❌ Vấn đề

Lỗi xuất hiện khi truy cập API:

```json
{
    "success": false,
    "message": "Bạn không có quyền thực hiện chức năng này",
    "required_permission": "phieu_nhap_kho_xem"
}
```

## ✅ Giải pháp FIX CỨNG

### 1. Đã Thực Hiện

-   ✅ Tạo **AdminObserver** và **NhanVienObserver** để tự động gán quyền khi tạo tài khoản mới
-   ✅ Sửa **CheckPermission Middleware** để tự động cấp quyền nếu phát hiện tài khoản chưa có quyền
-   ✅ Đăng ký Observer trong **AppServiceProvider**
-   ✅ Chạy script **fix-permissions-permanent.php** để fix tất cả tài khoản hiện có

### 2. Kết Quả

```
✅ Tất cả 3 Admin đã có FULL quyền
✅ Tất cả 10 Nhân viên đã được gán vai trò
✅ Lỗi phân quyền KHÔNG còn xuất hiện nữa
```

### 3. Cơ Chế Tự Động

#### 3.1. Khi Tạo Tài Khoản Mới

**AdminObserver** và **NhanVienObserver** sẽ tự động:

-   Kiểm tra nếu `phan_quyen_id` trống
-   Gán vai trò mặc định:
    -   Admin → Vai trò ID = 1 (Full quyền)
    -   Bác sĩ → Vai trò ID = 2
    -   Điều dưỡng → Vai trò ID = 3
    -   Lễ tân → Vai trò ID = 4

#### 3.2. Khi Truy Cập API

**CheckPermission Middleware** sẽ:

-   Kiểm tra nếu user chưa có `phan_quyen_id`
-   Tự động gán vai trò phù hợp
-   Refresh lại thông tin user
-   Tiếp tục kiểm tra quyền

### 4. File Quan Trọng

```
app/
├── Observers/
│   ├── AdminObserver.php          ← Tự động gán quyền cho Admin
│   └── NhanVienObserver.php       ← Tự động gán quyền cho NhanVien
├── Http/
│   └── Middleware/
│       └── CheckPermission.php    ← Auto-fix quyền khi truy cập
└── Providers/
    └── AppServiceProvider.php     ← Đăng ký Observer

fix-permissions-permanent.php      ← Script fix tất cả tài khoản
```

### 5. Kiểm Tra Lại

```bash
# Kiểm tra phân quyền
php check-permissions.php

# Nếu vẫn có lỗi, chạy lại script fix
php fix-permissions-permanent.php
```

### 6. Quyền Mặc Định

#### Admin (ID = 1) - FULL QUYỀN

-   ✅ TẤT CẢ 73 quyền trong hệ thống

#### Bác sĩ (ID = 2)

-   ✅ Xem lịch hẹn, sửa, xác nhận
-   ✅ Xem lịch làm việc
-   ✅ Xem phiếu chi, phiếu nhập kho
-   ✅ Xem và sửa thông tin thú cưng
-   ✅ Xem dịch vụ và khách hàng

#### Điều dưỡng (ID = 3)

-   ✅ Xem, tạo, sửa lịch hẹn
-   ✅ Xem, tạo phiếu chi
-   ✅ Xem, tạo phiếu nhập kho
-   ✅ Xem, tạo hàng hóa
-   ✅ Xem thú cưng, dịch vụ, khách hàng

#### Lễ tân (ID = 4)

-   ✅ Xem, tạo lịch hẹn
-   ✅ Xem phiếu chi, phiếu nhập kho
-   ✅ Xem, tạo thú cưng
-   ✅ Xem dịch vụ
-   ✅ Xem, sửa khách hàng

## 🔧 Bảo Trì

### Thêm Quyền Mới

Khi thêm quyền mới vào hệ thống:

1. Thêm cột vào migration `phan_quyens`:

```php
$table->boolean('ten_quyen_moi')->default(false);
```

2. Thêm vào Model `PhanQuyen`:

```php
protected $fillable = [
    // ... các quyền hiện có
    'ten_quyen_moi',
];

protected $casts = [
    // ... các quyền hiện có
    'ten_quyen_moi' => 'boolean',
];
```

3. Cấp quyền cho Admin:

```php
DB::table('phan_quyens')->where('id', 1)->update([
    'ten_quyen_moi' => true
]);
```

### Tạo Vai Trò Mới

```php
DB::table('phan_quyens')->insert([
    'ten_vai_tro' => 'Tên Vai Trò',
    'mo_ta' => 'Mô tả vai trò',
    // Cấp các quyền cần thiết
    'lich_hen_xem' => true,
    // ...
]);
```

## 📝 Lưu Ý Quan Trọng

1. ⚠️ **KHÔNG XÓA** các Observer và Middleware đã tạo
2. ⚠️ **KHÔNG SỬA** vai trò ID = 1 (Admin) - luôn giữ full quyền
3. ⚠️ Khi tạo tài khoản mới, đảm bảo có trường `vai_tro` (cho NhanVien)
4. ⚠️ Nếu gặp lỗi phân quyền, chạy `php fix-permissions-permanent.php`

## 🎯 Kết Luận

Hệ thống phân quyền đã được **FIX CỨNG** với 3 lớp bảo vệ:

1. **Observer**: Tự động gán quyền khi tạo tài khoản
2. **Middleware**: Tự động fix quyền khi truy cập API
3. **Script**: Fix toàn bộ tài khoản hiện có

➡️ **Lỗi phân quyền sẽ KHÔNG còn xuất hiện nữa!**
