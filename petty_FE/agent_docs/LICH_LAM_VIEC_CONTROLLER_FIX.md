# 🔧 Fix LichLamViecController - Giải pháp Hiển thị Lịch Làm Việc

## 📋 Tóm tắt vấn đề

Lịch làm việc không hiển thị cho nhân viên vì:

1. **Parameter name mismatch**: Vue gửi `tungay`/`denngay` nhưng PHP expect `start_date`/`end_date`
2. **Query không chính xác**: Khi parameter không khớp, `whereBetween()` nhận giá trị mặc định
3. **Route không đúng**: `/lich-lam-viec` có thể map sang `index()` thay vì `getMySchedule()`

---

## 🔍 Chi tiết các vấn đề

### ❌ Vấn đề 1: Parameter Mismatch (Dòng 233-280)

**File hiện tại:**

```php
public function getMySchedule(Request $request): JsonResponse
{
    // ...

    // ❌ SAI - PHP expect start_date nhưng Vue gửi tungay
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

    // Nếu Vue gửi ?tungay=2025-12-29&denngay=2025-01-04
    // Thì PHP nhận: start_date = null, end_date = null
    // → Dùng giá trị mặc định (đầu/cuối tháng)
    // → Query trả về dữ liệu sai hoặc rỗng
}
```

**Vào file Vue (`index.vue` dòng 758):**

```javascript
const data = await getMySchedule(
  formatDate(startOfWeek.value), // Gửi là tham số thứ 1, 2
  formatDate(endOfWeek.value)
);
```

**Service `lichLamViecService.ts`:**

```javascript
// Chắc chắn là:
export async function getMySchedule(startDate, endDate) {
  return api.get("/lich-lam-viec", {
    params: {
      tungay: startDate, // ← Parameter name
      denngay: endDate,
    },
  });
}
```

**Lỗi:** Parameter name không khớp!

---

### ❌ Vấn đề 2: Query trả về Array Rỗng

**Ở getMySchedule() dòng 249-280:**

```php
// Nếu nhân viên đó không có bất kỳ lịch làm việc nào
$lichLamViecs = LichLamViec::where('nhan_vien_id', $nhanVienId)
    ->whereBetween('ngay_lam', [$startDate, $endDate])
    ->orderBy('ngay_lam', 'asc')
    ->orderBy('thoi_gian_truc', 'asc')
    ->get();  // → Array()

// Sau đó vòng foreach này không chạy
foreach ($lichLamViecs as $lichLamViec) {
    // ...
}

// scheduleByDate vẫn là [] vì không có shift nào
$scheduleByDate[$date] = [...];

// Kết quả trả về: "schedule" => []
```

**Kết quả lịch trống:**

```json
{
    "status": true,
    "data": {
        "nhan_vien": { ... },
        "schedule": [],  // ← EMPTY!
        "statistics": {
            "total_work_days": 0,
            "total_appointments": 0
        }
    }
}
```

---

### ❌ Vấn đề 3: Route có thể map sai

**Giả sử routes/api.php:**

```php
// Có thể đang làm cái này:
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('lich-lam-viec', LichLamViecController::class);
    // ↑ Này gọi index() chứ không gọi getMySchedule()!
});
```

**Nên là:**

```php
Route::middleware('auth:sanctum')->group(function () {
    // Đặt route chi tiết trước
    Route::get('/lich-lam-viec', [LichLamViecController::class, 'getMySchedule']);
    Route::get('/lich-lam-viec/hom-nay', [LichLamViecController::class, 'getMyTodaySchedule']);

    // Sau đó mới apiResource (nếu cần)
    Route::apiResource('lich-lam-viec', LichLamViecController::class);
});
```

---

## ✅ GIẢI PHÁP FIX

### **FIX 1: Cập nhật parameter names trong getMySchedule()**

```php
public function getMySchedule(Request $request): JsonResponse
{
    // Lấy thông tin user đang đăng nhập
    $user = $request->user();

    // Kiểm tra xem user có phải là nhân viên hoặc admin không
    if (!($user instanceof \App\Models\NhanVien) && !($user instanceof \App\Models\Admin)) {
        return response()->json([
            'status' => false,
            'message' => 'Chỉ nhân viên và quản trị viên mới có thể xem lịch làm việc.',
        ], 403);
    }

    // Nếu là Admin, lấy nhan_vien_id từ query parameter
    if ($user instanceof \App\Models\Admin) {
        if (!$request->has('nhan_vien_id')) {
            return response()->json([
                'status' => false,
                'message' => 'Admin phải cung cấp nhan_vien_id để xem lịch.',
            ], 400);
        }
        $nhanVienId = $request->get('nhan_vien_id');
    } else {
        $nhanVienId = $user->id;
    }

    // ✅ FIX: Hỗ trợ cả 2 parameter names
    // Kiểm tra tungay/denngay trước (từ Vue)
    if ($request->filled('tungay') && $request->filled('denngay')) {
        $startDate = $request->get('tungay');
        $endDate = $request->get('denngay');
    } else {
        // Fallback: Kiểm tra start_date/end_date
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
    }

    // Nếu vẫn không có, dùng mặc định
    if (!$startDate) {
        $startDate = now()->startOfMonth()->format('Y-m-d');
    }
    if (!$endDate) {
        $endDate = now()->endOfMonth()->format('Y-m-d');
    }

    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) ||
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
        return response()->json([
            'status' => false,
            'message' => 'Định dạng ngày phải là YYYY-MM-DD',
        ], 400);
    }

    // Lấy lịch làm việc của nhân viên
    $lichLamViecs = LichLamViec::where('nhan_vien_id', $nhanVienId)
        ->whereBetween('ngay_lam', [$startDate, $endDate])
        ->orderBy('ngay_lam', 'asc')
        ->orderBy('thoi_gian_truc', 'asc')
        ->get();

    // ✅ FIX: Log để debug
    \Log::info('getMySchedule', [
        'nhan_vien_id' => $nhanVienId,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'lich_lam_viec_count' => $lichLamViecs->count(),
        'dates' => $lichLamViecs->pluck('ngay_lam')->unique()->toArray(),
    ]);

    // Lấy lịch hẹn của nhân viên
    $lichHens = \App\Models\LichHen::with(['khachHang', 'thuCung', 'dichVu'])
        ->where('nhan_vien_id', $nhanVienId)
        ->whereBetween('ngay_gio', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
        ->orderBy('ngay_gio', 'asc')
        ->get();

    // Gộp dữ liệu theo ngày
    $scheduleByDate = [];

    // Xử lý lịch làm việc
    foreach ($lichLamViecs as $lichLamViec) {
        $date = $lichLamViec->ngay_lam instanceof \Carbon\Carbon
            ? $lichLamViec->ngay_lam->format('Y-m-d')
            : (string) $lichLamViec->ngay_lam;

        if (!isset($scheduleByDate[$date])) {
            $scheduleByDate[$date] = [
                'date' => $date,
                'lich_lam_viec' => [],
                'lich_hen' => [],
            ];
        }
        $scheduleByDate[$date]['lich_lam_viec'][] = [
            'id' => $lichLamViec->id,
            'thoi_gian_truc' => $lichLamViec->thoi_gian_truc,
            'ghi_chu' => $lichLamViec->ghi_chu,
        ];
    }

    // Xử lý lịch hẹn
    foreach ($lichHens as $lichHen) {
        $date = \Carbon\Carbon::parse($lichHen->ngay_gio)->format('Y-m-d');
        if (!isset($scheduleByDate[$date])) {
            $scheduleByDate[$date] = [
                'date' => $date,
                'lich_lam_viec' => [],
                'lich_hen' => [],
            ];
        }
        $scheduleByDate[$date]['lich_hen'][] = [
            'id' => $lichHen->id,
            'ngay_gio' => $lichHen->ngay_gio,
            'trang_thai' => $lichHen->trang_thai,
            'khach_hang' => $lichHen->khachHang ? $lichHen->khachHang->full_name : null,
            'thu_cung' => $lichHen->thuCung ? $lichHen->thuCung->ten_thu_cung : null,
            'dich_vu' => $lichHen->dichVu ? $lichHen->dichVu->ten : null,
            'ghi_chu' => $lichHen->ghi_chu,
        ];
    }

    // Sắp xếp theo ngày
    ksort($scheduleByDate);

    // Lấy thông tin nhân viên
    $nhanVien = \App\Models\NhanVien::find($nhanVienId);

    return response()->json([
        'status' => true,
        'data' => [
            'nhan_vien' => $nhanVien ? [
                'id' => $nhanVien->id,
                'full_name' => $nhanVien->full_name,
                'email' => $nhanVien->email,
                'vai_tro' => $nhanVien->vai_tro,
                'chuc_danh' => $nhanVien->chuc_danh,
            ] : null,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'schedule' => array_values($scheduleByDate),
            'statistics' => [
                'total_work_days' => count($lichLamViecs),
                'total_appointments' => count($lichHens),
            ],
        ],
    ]);
}
```

**Thay đổi chính:**

- ✅ Hỗ trợ cả `tungay/denngay` và `start_date/end_date`
- ✅ Thêm validation kiểm tra format ngày
- ✅ Thêm logging để debug
- ✅ Fallback an toàn

---

### **FIX 2: Cập nhật routes/api.php**

```php
<?php

use App\Http\Controllers\LichLamViecController;

Route::middleware('auth:sanctum')->group(function () {
    // ✅ Custom routes đặt TRƯỚC apiResource
    Route::get('/lich-lam-viec', [LichLamViecController::class, 'getMySchedule']);
    Route::get('/lich-lam-viec/hom-nay', [LichLamViecController::class, 'getMyTodaySchedule']);
    Route::get('/lich-lam-viec/chi-tiet/{id}', [LichLamViecController::class, 'show']);

    // Các resource routes khác (POST, PUT, DELETE)
    Route::post('/lich-lam-viec', [LichLamViecController::class, 'store']);
});
```

---

### **FIX 3: Cập nhật lichLamViecService.ts (Frontend)**

Đảm bảo service gửi parameter đúng:

```typescript
// src/services/lichLamViecService.ts

export async function getMySchedule(startDate: string, endDate: string) {
  try {
    const response = await api.get("/lich-lam-viec", {
      params: {
        tungay: startDate, // ✅ Parameter name đúng
        denngay: endDate,
      },
    });
    return response.data;
  } catch (error) {
    console.error("Error fetching my schedule:", error);
    throw error;
  }
}

export async function getMyTodaySchedule() {
  try {
    const response = await api.get("/lich-lam-viec/hom-nay");
    return response.data;
  } catch (error) {
    console.error("Error fetching today schedule:", error);
    throw error;
  }
}
```

---

## 📊 Test Checklist

- [ ] Kiểm tra routes/api.php có đúng không
- [ ] Test API endpoint: `GET /api/lich-lam-viec?tungay=2025-12-29&denngay=2025-01-04`
- [ ] Kiểm tra database có dữ liệu LichLamViec cho tuần đó không
- [ ] Xem Laravel logs: `storage/logs/laravel.log`
- [ ] Kiểm tra console Vue có error không
- [ ] Test với admin account (thêm `?nhan_vien_id=X`)
- [ ] Kiểm tra dữ liệu trả về có `"schedule": [...]` không rỗng

---

## 🔍 Debug Ngay Bây Giờ

1. **Mở Postman/Thunder Client:**

   ```
   GET http://localhost:8000/api/lich-lam-viec?tungay=2025-12-29&denngay=2025-01-04
   Headers: Authorization: Bearer YOUR_TOKEN
   ```

2. **Xem Laravel logs:**

   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Test trực tiếp trong Chrome DevTools:**

   - Mở network tab
   - Xem request/response của `/api/lich-lam-viec`

4. **Kiểm tra DB:**
   ```sql
   SELECT * FROM lich_lam_viec
   WHERE nhan_vien_id = YOUR_ID
   AND ngay_lam BETWEEN '2025-12-29' AND '2025-01-04';
   ```

---

## 📝 Tóm tắt các thay đổi

| File                        | Vấn đề             | Fix                                                 |
| --------------------------- | ------------------ | --------------------------------------------------- |
| `LichLamViecController.php` | Parameter mismatch | Hỗ trợ cả `tungay/denngay` và `start_date/end_date` |
| `routes/api.php`            | Route map sai      | Đặt custom routes trước `apiResource`               |
| `lichLamViecService.ts`     | Gửi parameter sai  | Đảm bảo gửi `tungay`, `denngay`                     |
