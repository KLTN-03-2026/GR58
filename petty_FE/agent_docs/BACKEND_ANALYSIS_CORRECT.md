# 🔍 Phân tích Backend LichLamViecController (Chính xác)

## ✅ Kết luận: Backend ĐÃ ĐÚNG

File PHP bạn cung cấp có code **HOÀN TOÀN ĐÚNG**. Vấn đề nằm ở **Frontend**, không phải Backend.

---

## 📊 Phân tích Chi tiết

### ✅ Method: getMySchedule() (Dòng 233-318)

**Điểm tốt:**

```php
public function getMySchedule(Request $request): JsonResponse
{
    // ✅ 1. Lấy user đang đăng nhập
    $user = $request->user();

    // ✅ 2. Kiểm tra authorization (chỉ NhanVien hoặc Admin)
    if (!($user instanceof \App\Models\NhanVien) && !($user instanceof \App\Models\Admin)) {
        return response()->json([
            'status' => false,
            'message' => 'Chỉ nhân viên và quản trị viên mới có thể xem lịch làm việc.',
        ], 403);
    }

    // ✅ 3. Hỗ trợ Admin xem lịch của nhân viên khác
    if ($user instanceof \App\Models\Admin) {
        if (!$request->has('nhan_vien_id')) {
            return response()->json([
                'status' => false,
                'message' => 'Admin phải cung cấp nhan_vien_id để xem lịch.',
            ], 400);
        }
        $nhanVienId = $request->get('nhan_vien_id');
    } else {
        // ✅ 4. NhanVien chỉ xem lịch của chính mình
        $nhanVienId = $user->id;
    }

    // ✅ 5. Validate parameters
    $request->validate([
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);

    // ✅ 6. Lấy dates từ request hoặc dùng mặc định
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

    // ✅ 7. Query lịch làm việc trong khoảng ngày
    $lichLamViecs = LichLamViec::where('nhan_vien_id', $nhanVienId)
        ->whereBetween('ngay_lam', [$startDate, $endDate])
        ->orderBy('ngay_lam', 'asc')
        ->orderBy('thoi_gian_truc', 'asc')
        ->get();

    // ✅ 8. Query lịch hẹn trong khoảng ngày
    $lichHens = \App\Models\LichHen::with(['khachHang', 'thuCung', 'dichVu'])
        ->where('nhan_vien_id', $nhanVienId)
        ->whereBetween('ngay_gio', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
        ->orderBy('ngay_gio', 'asc')
        ->get();

    // ✅ 9. Xử lý và group dữ liệu theo ngày
    $scheduleByDate = [];

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

    // ✅ 10. Format response chính xác
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
            'schedule' => array_values($scheduleByDate),  // ← Array các ngày
            'statistics' => [
                'total_work_days' => count($lichLamViecs),
                'total_appointments' => count($lichHens),
            ],
        ],
    ]);
}
```

**Nhận xét:**

- ✅ Code logic chính xác
- ✅ Authorization đúng
- ✅ Date handling tốt
- ✅ Response format phù hợp

---

## 🚀 Backend Expected Response Format

Khi Frontend gửi:

```
GET /api/lich-lam-viec?start_date=2025-12-29&end_date=2025-01-04
Headers: Authorization: Bearer TOKEN
```

Backend sẽ trả về:

```json
{
  "status": true,
  "data": {
    "nhan_vien": {
      "id": 1,
      "full_name": "Bác sĩ Nguyễn Văn A",
      "email": "bac_si_a@clinic.com",
      "vai_tro": "nhan_vien",
      "chuc_danh": "Bác sĩ"
    },
    "period": {
      "start_date": "2025-12-29",
      "end_date": "2025-01-04"
    },
    "schedule": [
      {
        "date": "2025-12-29",
        "lich_lam_viec": [
          {
            "id": 123,
            "thoi_gian_truc": "ca_sang",
            "ghi_chu": "Phòng A"
          }
        ],
        "lich_hen": [
          {
            "id": 456,
            "ngay_gio": "2025-12-29 08:30:00",
            "trang_thai": "da_xac_nhan",
            "khach_hang": "Chủ thú cưng",
            "thu_cung": "Tên thú cưng",
            "dich_vu": "Tiêm ngừa",
            "ghi_chu": null
          }
        ]
      },
      {
        "date": "2025-12-30",
        "lich_lam_viec": [
          {
            "id": 124,
            "thoi_gian_truc": "ca_chieu",
            "ghi_chu": "Phòng B"
          }
        ],
        "lich_hen": []
      }
      // ... ngày khác
    ],
    "statistics": {
      "total_work_days": 3,
      "total_appointments": 5
    }
  }
}
```

---

## ⚠️ Vấn đề ở Frontend

### ❌ VẤN ĐỀ 1: lichLamViecService.ts

**Cần kiểm tra:** File `src/services/lichLamViecService.ts`

Nó cần phải gửi parameter chính xác:

```typescript
// ✅ ĐÚNG
export async function getMySchedule(startDate: string, endDate: string) {
  return api.get("/lich-lam-viec", {
    params: {
      start_date: startDate, // ← Khớp với backend expect
      end_date: endDate,
    },
  });
}
```

**Nếu hiện tại là:**

```typescript
// ❌ SAI
export async function getMySchedule(startDate: string, endDate: string) {
  return api.get("/lich-lam-viec", {
    params: {
      tungay: startDate, // ← KHÔNG KHỚP
      denngay: endDate,
    },
  });
}
```

Thì đó là lý do **lịch không hiển thị**!

---

### ❌ VẤN ĐỀ 2: Routes (routes/api.php)

Backend endpoint `/lich-lam-viec` phải map đến `getMySchedule()`:

**routes/api.php:**

```php
Route::middleware('auth:sanctum')->group(function () {
    // ✅ ĐÚNG - Custom route trước apiResource
    Route::get('/lich-lam-viec', [LichLamViecController::class, 'getMySchedule']);
    Route::get('/lich-lam-viec/hom-nay', [LichLamViecController::class, 'getMyTodaySchedule']);
    Route::post('/lich-lam-viec', [LichLamViecController::class, 'store']);
    Route::get('/lich-lam-viec/{id}', [LichLamViecController::class, 'show']);
});
```

**Nếu đang dùng:**

```php
// ❌ SAI
Route::apiResource('lich-lam-viec', LichLamViecController::class);
// → GET /lich-lam-viec sẽ gọi index() chứ không phải getMySchedule()
```

---

## ✅ GIẢI PHÁP

### Fix 1: Cập nhật lichLamViecService.ts

```typescript
// src/services/lichLamViecService.ts

import api from "@/utils/api";

export async function getMySchedule(startDate: string, endDate: string) {
  try {
    const response = await api.get("/lich-lam-viec", {
      params: {
        start_date: startDate, // ← Backend expect cái này
        end_date: endDate,
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

### Fix 2: Cập nhật routes/api.php

```php
<?php

use App\Http\Controllers\LichLamViecController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // ✅ Custom routes (phải đặt TRƯỚC apiResource)
    Route::get('/lich-lam-viec', [LichLamViecController::class, 'getMySchedule']);
    Route::get('/lich-lam-viec/hom-nay', [LichLamViecController::class, 'getMyTodaySchedule']);
    Route::get('/lich-lam-viec/{id}', [LichLamViecController::class, 'show']);
    Route::post('/lich-lam-viec', [LichLamViecController::class, 'store']);

    // Nếu cần các routes khác (PUT, DELETE, etc)
    // Route::put('/lich-lam-viec/{id}', [LichLamViecController::class, 'update']);
    // Route::delete('/lich-lam-viec/{id}', [LichLamViecController::class, 'destroy']);
});
```

---

### Fix 3: Vue Component index.vue (đã đúng)

✅ File `src/components/Doctor/LichLamViec/index.vue` **không cần thay đổi** vì nó đã:

- Có 4 time slots (Sáng, Chiều, Tối, Phân công)
- Xử lý date parsing an toàn
- Có comprehensive logging
- Xử lý empty array

**Chỉ cần theo dõi:**

- Backend response có dữ liệu không
- Parameters được gửi đúng không

---

## 🧪 Test Checklist

- [ ] Check file `lichLamViecService.ts` - parameter đúng chưa?
- [ ] Check `routes/api.php` - route mapping đúng chưa?
- [ ] Test API: `GET /api/lich-lam-viec?start_date=2025-12-29&end_date=2025-01-04`
- [ ] Kiểm tra database có dữ liệu không:
  ```sql
  SELECT * FROM lich_lam_viec
  WHERE nhan_vien_id = 1
  AND ngay_lam BETWEEN '2025-12-29' AND '2025-01-04';
  ```
- [ ] DevTools Network tab xem request/response
- [ ] DevTools Console xem logs từ Vue
- [ ] Kiểm tra Authorization header có token không

---

## 🎯 Expected Behavior Sau Fix

1. **Vue component gọi service:**

   ```javascript
   const data = await getMySchedule("2025-12-29", "2025-01-04");
   ```

2. **Service gửi API request:**

   ```
   GET /api/lich-lam-viec?start_date=2025-12-29&end_date=2025-01-04
   Authorization: Bearer TOKEN
   ```

3. **Backend nhận và xử lý:**

   ```php
   $startDate = $request->get('start_date'); // = '2025-12-29'
   $endDate = $request->get('end_date');     // = '2025-01-04'

   $lichLamViecs = LichLamViec::where('nhan_vien_id', 1)
       ->whereBetween('ngay_lam', [$startDate, $endDate])
       ->get();  // ← Query chính xác
   ```

4. **Backend trả về response với dữ liệu:**

   ```json
   {
       "status": true,
       "data": {
           "schedule": [
               {"date": "2025-12-29", "lich_lam_viec": [...]},
               {"date": "2025-12-30", "lich_lam_viec": [...]},
               // ...
           ]
       }
   }
   ```

5. **Vue component cập nhật calendar:**
   - 4 time slots được populate
   - Ngày 31/12 hiển thị đúng
   - Lịch visible cho nhân viên

---

## 📝 Tóm tắt

| Thành phần                          | Status     | Action                         |
| ----------------------------------- | ---------- | ------------------------------ |
| Backend `LichLamViecController.php` | ✅ Đúng    | Không cần thay đổi             |
| Frontend `index.vue`                | ✅ Đúng    | Không cần thay đổi             |
| Service `lichLamViecService.ts`     | ❌ Cần fix | Kiểm tra & sửa parameter names |
| Routes `routes/api.php`             | ❌ Cần fix | Cập nhật routing               |

**Kết luận:** Backend của bạn làm rất tốt. Chỉ cần fix Frontend để parameter khớp là lịch sẽ hiển thị!
