# Hướng dẫn Migration sang RBAC chuẩn

## Tổng quan

Hệ thống phân quyền đã được refactor từ **Role-based với hard-coded permissions** sang **RBAC chuẩn** (Role-Based Access Control) với cấu trúc linh hoạt và dễ mở rộng.

## Thay đổi chính

### Cấu trúc Database

**Trước (Old System):**
```
phan_quyens:
- id
- ma_vai_tro
- ten_vai_tro
- lich_hen_xem (boolean)
- lich_hen_tao (boolean)
- ... (100+ cột boolean)

admins/nhan_viens:
- phan_quyen_id -> phan_quyens.id
```

**Sau (New RBAC System):**
```
roles:
- id
- name (admin, bac_si, y_ta, le_tan, tro_ly, khach_hang)
- display_name
- description

permissions:
- id
- name (lich_hen.xem, lich_hen.tao, lich_hen.sua, lich_hen.xoa)
- display_name
- group (lich_hen, tai_chinh, hang_hoa, etc.)
- description

role_permission (pivot table):
- role_id
- permission_id

admins/nhan_viens/khach_hangs:
- role_id -> roles.id
- phan_quyen_id (deprecated, sẽ xóa sau)
```

## Các bước Migration

### 1. Chạy Migration

```bash
php artisan migrate
```

Migration sẽ tạo 3 bảng mới: `roles`, `permissions`, `role_permission`

### 2. Chạy Seeder

```bash
php artisan db:seed --class=RBACSeeder
```

Seeder sẽ:
- Tạo 6 roles: admin, bac_si, y_ta, le_tan, tro_ly, khach_hang
- Tạo 100+ permissions theo nhóm chức năng
- Gán quyền mặc định cho từng role

### 3. Update Models

Thêm trait `HasRole` vào các User models:

```php
// app/Models/Admin.php
use App\Traits\HasRole;

class Admin extends Authenticatable
{
    use HasRole;
    
    // ... existing code
}

// app/Models/NhanVien.php
use App\Traits\HasRole;

class NhanVien extends Authenticatable
{
    use HasRole;
    
    // ... existing code
}

// app/Models/KhachHang.php
use App\Traits\HasRole;

class KhachHang extends Authenticatable
{
    use HasRole;
    
    // ... existing code
}
```

### 4. Migrate Data từ hệ thống cũ

Tạo script để chuyển dữ liệu từ `phan_quyen_id` sang `role_id`:

```php
// Trong tinker hoặc tạo migration riêng
use App\Models\Admin;
use App\Models\NhanVien;
use App\Models\Role;

// Map phan_quyen_id cũ sang role mới
$roleMapping = [
    1 => 'admin',      // Admin
    2 => 'bac_si',     // Bác sĩ
    3 => 'y_ta',       // Y tá/Điều dưỡng
    4 => 'le_tan',     // Lễ tân
    5 => 'tro_ly',     // Trợ lý
];

// Migrate Admins
Admin::whereNotNull('phan_quyen_id')->each(function($admin) use ($roleMapping) {
    $roleName = $roleMapping[$admin->phan_quyen_id] ?? 'admin';
    $role = Role::where('name', $roleName)->first();
    if ($role) {
        $admin->update(['role_id' => $role->id]);
    }
});

// Migrate NhanViens
NhanVien::whereNotNull('phan_quyen_id')->each(function($nhanVien) use ($roleMapping) {
    $roleName = $roleMapping[$nhanVien->phan_quyen_id] ?? 'le_tan';
    $role = Role::where('name', $roleName)->first();
    if ($role) {
        $nhanVien->update(['role_id' => $role->id]);
    }
});
```

## Sử dụng RBAC mới

### Trong Controller

```php
// Kiểm tra role
if ($user->hasRole('admin')) {
    // Admin logic
}

// Kiểm tra permission
if ($user->hasPermission('lich_hen.xem')) {
    // Show appointments
}

// Kiểm tra nhiều permissions
if ($user->hasAnyPermission(['lich_hen.xem', 'lich_hen.tao'])) {
    // User có ít nhất 1 trong 2 quyền
}

if ($user->hasAllPermissions(['lich_hen.xem', 'lich_hen.sua'])) {
    // User có cả 2 quyền
}

// Lấy tất cả permissions của user
$permissions = $user->getPermissions();
```

### Trong Routes

```php
// Kiểm tra role
Route::middleware(['auth:admin', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});

// Kiểm tra permission
Route::middleware(['auth:admin', 'permission:lich_hen.xem'])->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index']);
});
```

### Trong Blade/Vue (API Response)

```php
// Controller trả về permissions cho frontend
public function getAuthUser(Request $request)
{
    $user = $request->user();
    
    return response()->json([
        'user' => $user,
        'role' => $user->role,
        'permissions' => $user->getPermissions()->pluck('name'),
    ]);
}
```

## Quản lý Permissions động

### Gán quyền cho Role

```php
$role = Role::where('name', 'bac_si')->first();
$role->givePermission('lich_hen.xoa'); // Thêm quyền xóa lịch hẹn cho bác sĩ
```

### Thu hồi quyền

```php
$role = Role::where('name', 'le_tan')->first();
$role->revokePermission('tai_chinh.xem_doanh_thu'); // Thu hồi quyền xem doanh thu
```

### Gán role cho user

```php
$admin = Admin::find(1);
$admin->assignRole('admin');

// Hoặc
$nhanVien = NhanVien::find(5);
$nhanVien->assignRole('bac_si');
```

## Danh sách Permissions

### Nhóm Lịch hẹn
- `lich_hen.xem` - Xem lịch hẹn
- `lich_hen.tao` - Tạo lịch hẹn
- `lich_hen.sua` - Sửa lịch hẹn
- `lich_hen.xoa` - Xóa lịch hẹn
- `lich_hen.xac_nhan` - Xác nhận lịch hẹn

### Nhóm Tài chính
- `tai_chinh.xem_doanh_thu` - Xem doanh thu
- `tai_chinh.thu_tien` - Thu tiền
- `tai_chinh.hoan_tien` - Hoàn tiền

### Nhóm Hàng hóa
- `hang_hoa.xem` - Xem hàng hóa
- `hang_hoa.tao` - Tạo hàng hóa
- `hang_hoa.sua` - Sửa hàng hóa
- `hang_hoa.xoa` - Xóa hàng hóa
- `hang_hoa.doi_trang_thai` - Đổi trạng thái hàng hóa

### Nhóm Nhân viên
- `nhan_vien.xem` - Xem nhân viên
- `nhan_vien.tao` - Tạo nhân viên
- `nhan_vien.doi_mat_khau` - Đổi mật khẩu nhân viên
- `nhan_vien.khoa_tai_khoan` - Khóa tài khoản
- `nhan_vien.mo_khoa_tai_khoan` - Mở khóa tài khoản

... (xem đầy đủ trong RBACSeeder.php)

## Lợi ích của RBAC mới

1. **Linh hoạt**: Thêm/xóa permissions không cần alter table
2. **Dễ bảo trì**: Tách biệt roles và permissions
3. **Mở rộng**: Dễ dàng thêm role mới hoặc permission mới
4. **Động**: Có thể thay đổi quyền của role trong runtime
5. **Chuẩn**: Tuân thủ best practices của RBAC

## Rollback (nếu cần)

Nếu cần rollback về hệ thống cũ:

```bash
php artisan migrate:rollback --step=1
```

Lưu ý: Cột `phan_quyen_id` vẫn được giữ lại trong quá trình migration để đảm bảo backward compatibility.

## Testing

```bash
# Test trong tinker
php artisan tinker

$admin = Admin::first();
$admin->hasRole('admin'); // true
$admin->hasPermission('lich_hen.xem'); // true
$admin->getPermissions(); // Collection of all permissions
```

## Lưu ý quan trọng

1. **Không xóa cột `phan_quyen_id`** ngay lập tức - giữ lại để backward compatibility
2. **Update tất cả Controllers** để sử dụng permission names mới (dấu chấm thay vì gạch dưới)
3. **Update Frontend** để check permissions với format mới
4. **Test kỹ** trước khi deploy lên production

## Hỗ trợ

Nếu có vấn đề, liên hệ team dev hoặc tạo issue trên Git.
