# HƯỚNG DẪN SỬA LỖI PHÂN QUYỀN KHI PULL CODE TỪ GITHUB

## 🔧 Vấn đề

Khi pull code từ GitHub về, có thể gặp lỗi:

-   Lỗi phân quyền middleware (403 Forbidden)
-   Không thể truy cập các endpoint API
-   Lỗi "Tài khoản chưa được phân quyền"

## ✅ Giải pháp

### Cách 1: Chạy Script Tự Động (Khuyên dùng)

```powershell
# Mở PowerShell tại thư mục project và chạy:
.\fix-permissions.ps1
```

Script này sẽ tự động:

1. Tắt git file mode tracking
2. Cấp quyền cho thư mục storage
3. Cấp quyền cho bootstrap/cache
4. Xóa và tạo lại cache Laravel

### Cách 2: Chạy Từng Lệnh Thủ Công

```powershell
# 1. Tắt git tracking file permissions
git config core.fileMode false

# 2. Cấp quyền cho storage (Windows)
icacls storage /grant Everyone:F /T /C /Q
icacls bootstrap\cache /grant Everyone:F /T /C /Q

# 3. Xóa cache Laravel
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 4. Tạo lại cache
php artisan config:cache
php artisan route:cache

# 5. Dump autoload
composer dump-autoload
```

### Cách 3: Cho Linux/Mac

```bash
# 1. Tắt git file mode
git config core.fileMode false

# 2. Cấp quyền
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache

# 3. Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 4. Recreate cache
php artisan config:cache
php artisan route:cache

# 5. Dump autoload
composer dump-autoload
```

## 📝 Kiểm tra sau khi sửa

### 1. Kiểm tra middleware hoạt động

Test với Postman hoặc API client:

```bash
# Test endpoint public (không cần token)
GET http://localhost/api/dich-vu

# Test endpoint cần đăng nhập (với token khách hàng)
GET http://localhost/api/thu-cung
Authorization: Bearer {khach_hang_token}

# Test endpoint staff only (với token admin/nhân viên)
GET http://localhost/api/lich-hen
Authorization: Bearer {admin_token}
```

### 2. Kiểm tra các middleware

-   `CheckPermission` - Kiểm tra quyền hạn chi tiết
-   `StaffOnly` - Chỉ cho phép Admin và NhanVien

### 3. Response mong đợi

**Khách hàng truy cập endpoint staff only:**

```json
{
    "success": false,
    "message": "Chỉ quản trị viên và nhân viên mới có thể truy cập chức năng này.",
    "status": 403
}
```

**Nhân viên không có quyền:**

```json
{
    "success": false,
    "message": "Bạn không có quyền thực hiện chức năng này",
    "required_permission": "lich_hen_tao",
    "status": 403
}
```

## 🚨 Lưu ý quan trọng

### 1. Không push file có quyền sai

```powershell
# Luôn check trước khi push
git status

# Chỉ commit các file code, không commit file cache
git add app/
git add routes/
git add config/
# KHÔNG add storage/ hoặc bootstrap/cache/
```

### 2. Cấu hình .gitignore đúng

File `.gitignore` đã có:

```
/storage/*.key
/storage/framework/*
/bootstrap/cache/*
```

### 3. Config git toàn cục

Để tránh lỗi cho tất cả project:

```powershell
git config --global core.fileMode false
```

## 🔍 Debug thêm

### Kiểm tra middleware đã được đăng ký chưa

File `bootstrap/app.php` phải có:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'permission' => \App\Http\Middleware\CheckPermission::class,
        'staff.only' => \App\Http\Middleware\StaffOnly::class,
    ]);
})
```

### Kiểm tra routes

File `routes/api.php` - các route staff only phải có:

```php
Route::get('/lich-hen', [LichHenController::class, 'index'])
    ->middleware(['staff.only', 'permission:lich_hen_xem']);
```

### Kiểm tra model có phương thức hasPermission

Trong `app/Models/Admin.php` và `app/Models/NhanVien.php`:

```php
public function hasPermission(string $permission): bool
{
    if (!$this->phanQuyen) {
        return false;
    }

    $permissions = $this->phanQuyen->permissions ?? [];
    return in_array($permission, $permissions) ||
           ($permissions[$permission] ?? false) === true;
}
```

## 📞 Hỗ trợ

Nếu vẫn gặp lỗi sau khi làm theo hướng dẫn:

1. Kiểm tra log Laravel: `storage/logs/laravel.log`
2. Kiểm tra Apache/Nginx error log
3. Kiểm tra console browser (F12) nếu lỗi từ frontend
4. Test bằng Postman để loại trừ lỗi frontend

## 🎯 Tổng kết

**Nguyên nhân lỗi phân quyền khi pull:**

1. Git tracking file mode (executable permissions)
2. Cache Laravel cũ không được clear
3. Middleware chưa được đăng ký đúng
4. Route không áp dụng middleware đúng

**Giải pháp:**

1. Tắt git file mode: `git config core.fileMode false`
2. Clear cache: `php artisan cache:clear`
3. Cấp quyền thư mục: `icacls storage /grant Everyone:F /T /C /Q`
4. Kiểm tra middleware và routes

**Phòng tránh:**

1. Luôn chạy script fix-permissions.ps1 sau khi pull
2. Không commit file trong storage/ và bootstrap/cache/
3. Config git global để tắt file mode tracking
4. Có file .gitignore đầy đủ
