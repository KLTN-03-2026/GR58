# Hướng dẫn Backend - Lịch sử thanh toán Phiếu Chi

## 📋 Tổng quan

Tạo chức năng lưu trữ và hiển thị lịch sử thanh toán cho mỗi phiếu chi, giúp theo dõi các lần trả nợ dần.

## 🗄️ Database Migration

### 1. Tạo bảng `lich_su_thanh_toan_phieu_chi`

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
            $table->foreignId('phieu_chi_id')
                  ->constrained('phieu_chi')
                  ->onDelete('cascade')
                  ->comment('ID phiếu chi');

            $table->decimal('so_tien_thanh_toan', 15, 2)
                  ->comment('Số tiền thanh toán lần này');

            $table->enum('hinh_thuc_thanh_toan', ['tien_mat', 'chuyen_khoan', 'ca_hai'])
                  ->default('tien_mat')
                  ->comment('Hình thức thanh toán');

            $table->decimal('tien_mat', 15, 2)
                  ->default(0)
                  ->comment('Số tiền mặt');

            $table->decimal('tien_chuyen_khoan', 15, 2)
                  ->default(0)
                  ->comment('Số tiền chuyển khoản');

            $table->text('ghi_chu')
                  ->nullable()
                  ->comment('Ghi chú thanh toán');

            $table->decimal('so_tien_da_tra_truoc_do', 15, 2)
                  ->comment('Tổng đã trả trước đó');

            $table->decimal('so_tien_con_no_sau_thanh_toan', 15, 2)
                  ->comment('Còn nợ sau khi thanh toán lần này');

            $table->dateTime('ngay_thanh_toan')
                  ->comment('Ngày giờ thanh toán');

            $table->foreignId('nguoi_thanh_toan_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Người thực hiện thanh toán');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('phieu_chi_id');
            $table->index('ngay_thanh_toan');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lich_su_thanh_toan_phieu_chi');
    }
};
```

## 📝 Model

### LichSuThanhToanPhieuChi.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LichSuThanhToanPhieuChi extends Model
{
    use HasFactory, SoftDeletes;

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
        'nguoi_thanh_toan_id',
    ];

    protected $casts = [
        'so_tien_thanh_toan' => 'decimal:2',
        'tien_mat' => 'decimal:2',
        'tien_chuyen_khoan' => 'decimal:2',
        'so_tien_da_tra_truoc_do' => 'decimal:2',
        'so_tien_con_no_sau_thanh_toan' => 'decimal:2',
        'ngay_thanh_toan' => 'datetime',
    ];

    /**
     * Relationship với PhieuChi
     */
    public function phieuChi()
    {
        return $this->belongsTo(PhieuChi::class, 'phieu_chi_id');
    }

    /**
     * Relationship với User (người thanh toán)
     */
    public function nguoiThanhToan()
    {
        return $this->belongsTo(User::class, 'nguoi_thanh_toan_id');
    }

    /**
     * Get label cho hình thức thanh toán
     */
    public function getHinhThucThanhToanLabelAttribute()
    {
        return match($this->hinh_thuc_thanh_toan) {
            'tien_mat' => '💵 Tiền mặt',
            'chuyen_khoan' => '🏦 Chuyển khoản',
            'ca_hai' => '💳 Cả hai (Tiền mặt + Chuyển khoản)',
            default => $this->hinh_thuc_thanh_toan,
        };
    }
}
```

### Cập nhật PhieuChi Model

```php
// Thêm vào PhieuChi.php

/**
 * Relationship với lịch sử thanh toán
 */
public function lichSuThanhToan()
{
    return $this->hasMany(LichSuThanhToanPhieuChi::class, 'phieu_chi_id')
                ->orderBy('ngay_thanh_toan', 'desc');
}
```

## 🎯 Controller

### Thêm vào PhieuChiController.php

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\PhieuChi;
use App\Models\LichSuThanhToanPhieuChi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhieuChiController extends Controller
{
    /**
     * Lấy lịch sử thanh toán của phiếu chi
     */
    public function getLichSuThanhToan($id)
    {
        try {
            $phieuChi = PhieuChi::findOrFail($id);

            $lichSu = $phieuChi->lichSuThanhToan()
                ->with('nguoiThanhToan:id,ten,email')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Lấy lịch sử thanh toán thành công',
                'data' => $lichSu->map(function($item) {
                    return [
                        'id' => $item->id,
                        'so_tien_thanh_toan' => $item->so_tien_thanh_toan,
                        'hinh_thuc_thanh_toan' => $item->hinh_thuc_thanh_toan,
                        'hinh_thuc_thanh_toan_label' => $item->hinh_thuc_thanh_toan_label,
                        'tien_mat' => $item->tien_mat,
                        'tien_chuyen_khoan' => $item->tien_chuyen_khoan,
                        'ghi_chu' => $item->ghi_chu,
                        'so_tien_da_tra_truoc_do' => $item->so_tien_da_tra_truoc_do,
                        'so_tien_con_no_sau_thanh_toan' => $item->so_tien_con_no_sau_thanh_toan,
                        'ngay_thanh_toan' => $item->ngay_thanh_toan,
                        'nguoi_thanh_toan' => $item->nguoiThanhToan ? [
                            'id' => $item->nguoiThanhToan->id,
                            'ten' => $item->nguoiThanhToan->ten,
                            'email' => $item->nguoiThanhToan->email,
                        ] : null,
                        'created_at' => $item->created_at,
                    ];
                })
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lấy lịch sử thanh toán',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật phiếu chi (đã sửa để lưu lịch sử)
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $phieuChi = PhieuChi::findOrFail($id);

            // Lấy giá trị đã trả trước đó
            $soTienDaTraTruocDo = $phieuChi->so_tien_thanh_toan_ngay;
            $soTienConNoTruocDo = $phieuChi->so_tien_con_no;

            // Cập nhật phiếu chi
            $phieuChi->update($request->all());

            // Tính toán số tiền thanh toán thêm
            $soTienThanhToanThem = $phieuChi->so_tien_thanh_toan_ngay - $soTienDaTraTruocDo;

            // Nếu có thanh toán thêm, lưu vào lịch sử
            if ($soTienThanhToanThem > 0) {
                // Xác định hình thức thanh toán
                $hinhThucThanhToan = 'tien_mat';
                $tienMat = $request->input('tien_mat', 0);
                $tienChuyenKhoan = $request->input('tien_chuyen_khoan', 0);

                if ($tienMat > 0 && $tienChuyenKhoan > 0) {
                    $hinhThucThanhToan = 'ca_hai';
                } elseif ($tienChuyenKhoan > 0) {
                    $hinhThucThanhToan = 'chuyen_khoan';
                }

                LichSuThanhToanPhieuChi::create([
                    'phieu_chi_id' => $phieuChi->id,
                    'so_tien_thanh_toan' => $soTienThanhToanThem,
                    'hinh_thuc_thanh_toan' => $hinhThucThanhToan,
                    'tien_mat' => $tienMat - ($phieuChi->tien_mat - $soTienThanhToanThem),
                    'tien_chuyen_khoan' => $tienChuyenKhoan - ($phieuChi->tien_chuyen_khoan - $soTienThanhToanThem),
                    'ghi_chu' => $request->input('ghi_chu'),
                    'so_tien_da_tra_truoc_do' => $soTienDaTraTruocDo,
                    'so_tien_con_no_sau_thanh_toan' => $phieuChi->so_tien_con_no,
                    'ngay_thanh_toan' => now(),
                    'nguoi_thanh_toan_id' => auth()->id(),
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật phiếu chi thành công',
                'data' => $phieuChi->load('lichSuThanhToan')
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi cập nhật phiếu chi',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
```

## 🛣️ Routes

### Thêm vào api.php

```php
// Lịch sử thanh toán phiếu chi
Route::get('/phieu-chi/{id}/lich-su-thanh-toan', [PhieuChiController::class, 'getLichSuThanhToan']);
```

## 📊 Response Format

### GET /api/phieu-chi/{id}/lich-su-thanh-toan

**Success Response:**

```json
{
  "status": true,
  "message": "Lấy lịch sử thanh toán thành công",
  "data": [
    {
      "id": 1,
      "so_tien_thanh_toan": 5000000,
      "hinh_thuc_thanh_toan": "chuyen_khoan",
      "hinh_thuc_thanh_toan_label": "🏦 Chuyển khoản",
      "tien_mat": 0,
      "tien_chuyen_khoan": 5000000,
      "ghi_chu": "Trả đợt 1",
      "so_tien_da_tra_truoc_do": 0,
      "so_tien_con_no_sau_thanh_toan": 10000000,
      "ngay_thanh_toan": "2025-12-06T10:30:00.000000Z",
      "nguoi_thanh_toan": {
        "id": 1,
        "ten": "Kế toán Hoa",
        "email": "hoa@example.com"
      },
      "created_at": "2025-12-06T10:30:00.000000Z"
    }
  ]
}
```

## ✅ Testing

### 1. Chạy migration

```bash
php artisan migrate
```

### 2. Test API với Postman/Thunder Client

```
GET http://localhost:8000/api/phieu-chi/1/lich-su-thanh-toan
Authorization: Bearer {token}
```

### 3. Test thanh toán thêm

```
PUT http://localhost:8000/api/phieu-chi/1
Authorization: Bearer {token}
Content-Type: application/json

{
  "so_tien_thanh_toan_ngay": 10000000,
  "tien_mat": 3000000,
  "tien_chuyen_khoan": 7000000,
  "ghi_chu": "Thanh toán đợt 2"
}
```

## 🎨 Frontend Integration

Frontend đã sẵn sàng và sẽ tự động:

1. ✅ Gọi API `/phieu-chi/{id}/lich-su-thanh-toan` khi mở modal chi tiết
2. ✅ Hiển thị loading state
3. ✅ Hiển thị danh sách lịch sử thanh toán
4. ✅ Hiển thị empty state nếu chưa có lịch sử
5. ✅ Hiển thị chi tiết tiền mặt và chuyển khoản

## 📝 Notes

- Lịch sử thanh toán được lưu tự động mỗi khi cập nhật phiếu chi với số tiền tăng
- Soft delete được áp dụng để có thể khôi phục nếu cần
- Người thanh toán được lưu để audit trail
- Frontend tự động fallback về dữ liệu props nếu API chưa sẵn sàng
