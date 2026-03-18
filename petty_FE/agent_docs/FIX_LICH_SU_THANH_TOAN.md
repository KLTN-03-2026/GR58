# 🔧 Hướng dẫn Fix Lỗi Lịch Sử Thanh Toán Phiếu Chi

## ❌ Vấn đề hiện tại

- Frontend không lấy được lịch sử thanh toán từ API
- Backend đã có method `getLichSuThanhToan()` nhưng có thể chưa đăng ký route

## ✅ Giải pháp

### 1. Kiểm tra Route Backend (Laravel)

Mở file `routes/api.php` và đảm bảo có route sau:

```php
Route::middleware('auth:sanctum')->group(function () {
    // ... các route khác ...

    // Routes cho Phiếu Chi
    Route::prefix('phieu-chi')->group(function () {
        Route::get('/', [PhieuChiController::class, 'index']);
        Route::post('/', [PhieuChiController::class, 'store']);
        Route::get('/{id}', [PhieuChiController::class, 'show']);
        Route::put('/{id}', [PhieuChiController::class, 'update']);
        Route::delete('/{id}', [PhieuChiController::class, 'destroy']);

        // ⭐ QUAN TRỌNG: Thêm 2 routes này
        Route::get('/{id}/lich-su-thanh-toan', [PhieuChiController::class, 'getLichSuThanhToan']);
        Route::post('/{id}/thanh-toan-them', [PhieuChiController::class, 'thanhToanThem']);
        Route::get('/{id}/pdf', [PhieuChiController::class, 'exportPdf']);
    });
});
```

### 2. Kiểm tra Model LichSuThanhToanPhieuChi

Tạo file `app/Models/LichSuThanhToanPhieuChi.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichSuThanhToanPhieuChi extends Model
{
    use HasFactory;

    protected $table = 'lich_su_thanh_toan_phieu_chi';

    protected $fillable = [
        'phieu_chi_id',
        'so_tien_thanh_toan',
        'hinh_thuc_thanh_toan',
        'tien_mat',
        'tien_chuyen_khoan',
        'ghi_chu',
        'so_tien_da_tra_truoc_do',
        'so_tien_con_no_sau_thanh_toan',
        'ngay_thanh_toan',
        'admin_id',
        'nhan_vien_id',
        'nguoi_thanh_toan_type',
    ];

    protected $casts = [
        'so_tien_thanh_toan' => 'decimal:0',
        'tien_mat' => 'decimal:0',
        'tien_chuyen_khoan' => 'decimal:0',
        'so_tien_da_tra_truoc_do' => 'decimal:0',
        'so_tien_con_no_sau_thanh_toan' => 'decimal:0',
        'ngay_thanh_toan' => 'datetime',
    ];

    protected $appends = ['hinh_thuc_thanh_toan_label', 'nguoi_thanh_toan_info'];

    // Boot method để tự động set người thanh toán
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lichSu) {
            $user = auth()->user();
            if ($user) {
                if ($user instanceof \App\Models\Admin) {
                    $lichSu->admin_id = $user->id;
                    $lichSu->nguoi_thanh_toan_type = 'admin';
                } elseif ($user instanceof \App\Models\NhanVien) {
                    $lichSu->nhan_vien_id = $user->id;
                    $lichSu->nguoi_thanh_toan_type = 'nhan_vien';
                }
            }
        });
    }

    // Relationships
    public function phieuChi()
    {
        return $this->belongsTo(PhieuChi::class, 'phieu_chi_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }

    // Accessor: Label hình thức thanh toán
    public function getHinhThucThanhToanLabelAttribute()
    {
        $labels = [
            'tien_mat' => 'Tiền mặt',
            'chuyen_khoan' => 'Chuyển khoản',
            'ca_hai' => 'Cả hai (Tiền mặt + Chuyển khoản)',
        ];

        return $labels[$this->hinh_thuc_thanh_toan] ?? 'Không xác định';
    }

    // Accessor: Thông tin người thanh toán
    public function getNguoiThanhToanInfoAttribute()
    {
        if ($this->nguoi_thanh_toan_type === 'admin' && $this->admin) {
            return [
                'id' => $this->admin->id,
                'name' => $this->admin->ho_ten,
                'email' => $this->admin->email,
                'type' => 'admin',
            ];
        } elseif ($this->nguoi_thanh_toan_type === 'nhan_vien' && $this->nhanVien) {
            return [
                'id' => $this->nhanVien->id,
                'name' => $this->nhanVien->full_name,
                'email' => $this->nhanVien->email,
                'type' => 'nhan_vien',
            ];
        }

        return null;
    }
}
```

### 3. Thêm Relationship vào Model PhieuChi

Mở `app/Models/PhieuChi.php` và thêm:

```php
public function lichSuThanhToan()
{
    return $this->hasMany(LichSuThanhToanPhieuChi::class, 'phieu_chi_id')
        ->orderBy('created_at', 'desc');
}
```

### 4. Tạo Migration cho Bảng lịch_su_thanh_toan_phieu_chi

Chạy lệnh:

```bash
php artisan make:migration create_lich_su_thanh_toan_phieu_chi_table
```

Nội dung migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lich_su_thanh_toan_phieu_chi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phieu_chi_id')->constrained('phieu_chis')->onDelete('cascade');
            $table->decimal('so_tien_thanh_toan', 15, 0);
            $table->enum('hinh_thuc_thanh_toan', ['tien_mat', 'chuyen_khoan', 'ca_hai']);
            $table->decimal('tien_mat', 15, 0)->default(0);
            $table->decimal('tien_chuyen_khoan', 15, 0)->default(0);
            $table->text('ghi_chu')->nullable();
            $table->decimal('so_tien_da_tra_truoc_do', 15, 0)->default(0);
            $table->decimal('so_tien_con_no_sau_thanh_toan', 15, 0)->default(0);
            $table->timestamp('ngay_thanh_toan');

            // Polymorphic: người thanh toán có thể là admin hoặc nhân viên
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->foreignId('nhan_vien_id')->nullable()->constrained('nhan_viens')->onDelete('set null');
            $table->enum('nguoi_thanh_toan_type', ['admin', 'nhan_vien'])->nullable();

            $table->timestamps();

            $table->index(['phieu_chi_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lich_su_thanh_toan_phieu_chi');
    }
};
```

Chạy migration:

```bash
php artisan migrate
```

## 🧪 Kiểm tra

### 1. Test API trực tiếp

Mở Postman hoặc browser console:

```javascript
// Test endpoint lịch sử thanh toán
fetch("http://127.0.0.1:8000/api/phieu-chi/1/lich-su-thanh-toan", {
  headers: {
    Authorization: "Bearer " + localStorage.getItem("token"),
    Accept: "application/json",
  },
})
  .then((r) => r.json())
  .then((data) => console.log(data));
```

Response mong đợi:

```json
{
  "status": true,
  "message": "Lấy lịch sử thanh toán thành công",
  "data": [
    {
      "id": 1,
      "so_tien_thanh_toan": 5000000,
      "hinh_thuc_thanh_toan": "tien_mat",
      "hinh_thuc_thanh_toan_label": "Tiền mặt",
      "tien_mat": 5000000,
      "tien_chuyen_khoan": 0,
      "ghi_chu": "Thanh toán lần 1",
      "so_tien_da_tra_truoc_do": 0,
      "so_tien_con_no_sau_thanh_toan": 10000000,
      "ngay_thanh_toan": "2024-12-06T10:30:00.000000Z",
      "nguoi_thanh_toan": {
        "id": 1,
        "name": "Admin",
        "email": "admin@example.com",
        "type": "admin"
      },
      "created_at": "2024-12-06T10:30:00.000000Z"
    }
  ]
}
```

### 2. Kiểm tra trong Frontend

Mở Chi Tiết Phiếu Chi và xem Console:

```
🔄 Loading payment history for ID: 1
📥 API Response: { status: true, data: [...] }
✅ Đã tải lịch sử thanh toán: [...]
```

## 📝 Các lệnh cần chạy

```bash
# 1. Tạo migration
php artisan make:migration create_lich_su_thanh_toan_phieu_chi_table

# 2. Chạy migration
php artisan migrate

# 3. Clear cache (nếu cần)
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# 4. Xem danh sách routes
php artisan route:list | grep phieu-chi
```

## ⚠️ Lưu ý quan trọng

1. **Route Order**: Đặt route `/phieu-chi/{id}/lich-su-thanh-toan` TRƯỚC route `/phieu-chi/{id}` để tránh conflict
2. **Migration**: Đảm bảo bảng `lich_su_thanh_toan_phieu_chi` tồn tại trước khi test
3. **Permissions**: Kiểm tra middleware `auth:sanctum` đang hoạt động
4. **Token**: Đảm bảo token hợp lệ trong localStorage

## 🎯 Kết quả mong đợi

Sau khi hoàn thành các bước trên:

✅ API endpoint `/phieu-chi/{id}/lich-su-thanh-toan` hoạt động  
✅ Frontend load được lịch sử thanh toán  
✅ Hiển thị danh sách thanh toán với đầy đủ thông tin  
✅ Xuất PDF bao gồm lịch sử thanh toán  
✅ Console log rõ ràng để debug

## 🔍 Debug nếu vẫn lỗi

Nếu vẫn gặp lỗi 404, kiểm tra:

```bash
# Xem tất cả routes
php artisan route:list

# Tìm route phieu-chi
php artisan route:list | grep "phieu-chi"

# Kết quả mong đợi:
# GET|HEAD   api/phieu-chi/{id}/lich-su-thanh-toan ... PhieuChiController@getLichSuThanhToan
```

Nếu không thấy route, cần thêm vào `routes/api.php` như hướng dẫn ở Bước 1.
