# 🚀 Hướng dẫn Cài đặt Fix Lịch Làm Việc

## 📌 Bước 1: Cập nhật Backend (Laravel)

### Bước 1.1: Sao chép code LichLamViecController.php

Copy file `LichLamViecController_FIXED.php` và thay thế nội dung của file gốc:

**Đường dẫn:** `app/Http/Controllers/LichLamViecController.php`

**Thay đổi chính:**

- ✅ Hỗ trợ parameter names: `tungay`, `denngay` (từ Vue)
- ✅ Thêm logging để debug dễ hơn
- ✅ Thêm validation date format
- ✅ Xử lý fallback nếu parameter không khớp

---

### Bước 1.2: Cập nhật Routes (routes/api.php)

Mở file `routes/api.php` trong backend Laravel và tìm đến phần routes của LichLamViecController.

**Trước (sai):**

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('lich-lam-viec', LichLamViecController::class);
    // ❌ Cách này sẽ route GET /lich-lam-viec sang index() chứ không phải getMySchedule()
});
```

**Sau (đúng):**

```php
<?php

use App\Http\Controllers\LichLamViecController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // ✅ Custom routes (phải đặt TRƯỚC apiResource)
    Route::get('/lich-lam-viec', [LichLamViecController::class, 'getMySchedule']);
    Route::get('/lich-lam-viec/hom-nay', [LichLamViecController::class, 'getMyTodaySchedule']);
    Route::get('/lich-lam-viec/chi-tiet/{id}', [LichLamViecController::class, 'show']);
    Route::post('/lich-lam-viec', [LichLamViecController::class, 'store']);

    // Các routes khác (nếu cần)
    // Route::apiResource('lich-lam-viec', LichLamViecController::class, ['except' => ['index', 'store', 'show']]);
});
```

**Lý do:**

- Khi bạn dùng `apiResource()`, Laravel tự động map:
  - `GET /lich-lam-viec` → `index()`
  - `POST /lich-lam-viec` → `store()`
  - `GET /lich-lam-viec/{id}` → `show()`
- Nhưng bạn muốn `GET /lich-lam-viec` → `getMySchedule()`
- Giải pháp: Định nghĩa custom route **trước** apiResource()

---

### Bước 1.3: Test Backend API

1. **Khởi động Laravel server:**

   ```bash
   php artisan serve
   ```

2. **Mở Postman hoặc Thunder Client và test endpoint:**

   **URL:** `http://localhost:8000/api/lich-lam-viec`

   **Headers:**

   ```
   Authorization: Bearer YOUR_TOKEN
   Content-Type: application/json
   ```

   **Query Parameters:**

   ```
   tungay=2025-12-29
   denngay=2025-01-04
   ```

   **Hoặc:**

   ```
   start_date=2025-12-29
   end_date=2025-01-04
   ```

3. **Kiểm tra response:**

   ```json
   {
       "status": true,
       "data": {
           "nhan_vien": {
               "id": 1,
               "full_name": "Bác sĩ ABC",
               "email": "...",
               "vai_tro": "...",
               "chuc_danh": "..."
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
                   "lich_hen": [...]
               },
               // ... ngày khác
           ],
           "statistics": {
               "total_work_days": 3,
               "total_appointments": 5
           }
       }
   }
   ```

4. **Nếu schedule vẫn rỗng, kiểm tra database:**

   ```sql
   SELECT * FROM lich_lam_viec
   WHERE nhan_vien_id = 1
   AND ngay_lam BETWEEN '2025-12-29' AND '2025-01-04'
   LIMIT 10;
   ```

5. **Xem Laravel logs để debug:**

   ```bash
   tail -f storage/logs/laravel.log
   ```

   Sẽ thấy logs như:

   ```
   [2025-12-11 10:30:00] local.INFO: getMySchedule called {"user_id":1,"user_type":"App\\Models\\NhanVien","nhan_vien_id":1,"startDate":"2025-12-29","endDate":"2025-01-04"}
   [2025-12-11 10:30:00] local.INFO: Fetched lich_lam_viec {"count":3,"dates":["2025-12-29","2025-12-30","2025-12-31"]}
   ```

---

## 📌 Bước 2: Cập nhật Frontend (Vue)

### Bước 2.1: Kiểm tra lichLamViecService.ts

Mở file `src/services/lichLamViecService.ts` và đảm bảo nó gửi parameter đúng:

```typescript
// src/services/lichLamViecService.ts

import api from "@/utils/api";

export async function getMySchedule(startDate: string, endDate: string) {
  try {
    const response = await api.get("/lich-lam-viec", {
      params: {
        tungay: startDate, // ✅ Parameter name phải khớp
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

### Bước 2.2: Vue Component (index.vue) đã được cập nhật

✅ File `src/components/Doctor/LichLamViec/index.vue` đã có:

- 4 time slots (Sáng, Chiều, Tối, Phân công)
- Safe date parsing (xử lý timezone)
- Comprehensive logging
- Empty array handling

**Không cần thay đổi gì thêm!**

---

## 📌 Bước 3: Test End-to-End

### Test 1: Kiểm tra Admin đã phân công lịch chưa

1. **Truy cập Admin Dashboard:**

   - Vào trang "Quản lý Lịch"
   - Chọn 1 nhân viên
   - Gán schedule cho tuần 29/12 - 04/01

2. **Kiểm tra database (chắc chắn dữ liệu được lưu):**
   ```sql
   SELECT * FROM lich_lam_viec
   WHERE nhan_vien_id = 1
   ORDER BY ngay_lam DESC;
   ```

### Test 2: Test Endpoint API trực tiếp

```bash
# Dùng curl trong Terminal
curl -H "Authorization: Bearer YOUR_TOKEN" \
  "http://localhost:8000/api/lich-lam-viec?tungay=2025-12-29&denngay=2025-01-04"
```

### Test 3: Test trong Vue Component

1. **Mở Chrome DevTools (F12):**

   - Vào tab **Network**
   - Vào tab **Console**

2. **Trigger lệnh fetch:**

   - Bấm nút "Tuần trước" hoặc "Tuần sau"
   - Xem network request/response

3. **Kiểm tra console logs:**
   - Tìm các log từ `fetchScheduleData()`
   - Kiểm tra response có dữ liệu không

---

## 🔍 Troubleshooting

### ❌ Vấn đề: Schedule vẫn trống

**Nguyên nhân:**

1. Database không có dữ liệu LichLamViec
2. Route chưa được cập nhật đúng
3. Parameter name vẫn không khớp

**Giải pháp:**

```bash
# 1. Kiểm tra database
php artisan tinker
>>> \App\Models\LichLamViec::where('nhan_vien_id', 1)->count();
>>> \App\Models\LichLamViec::where('nhan_vien_id', 1)->get();

# 2. Kiểm tra routes
php artisan route:list | grep lich-lam-viec

# 3. Xem Laravel logs
tail -f storage/logs/laravel.log
```

---

### ❌ Vấn đề: 401 Unauthorized

**Nguyên nhân:** Token hết hạn hoặc không hợp lệ

**Giải pháp:**

- Login lại
- Kiểm tra token trong localStorage

---

### ❌ Vấn đề: 403 Forbidden

**Nguyên nhân:** User không phải NhanVien hoặc Admin

**Giải pháp:**

- Kiểm tra roles/permissions
- Xem controller logic

---

### ❌ Vấn đề: Parameter mismatch error

**Nguyên nhân:** Gửi parameter sai (ví dụ: gửi `tungay` nhưng PHP không support)

**Giải pháp:**

- Cập nhật controller theo file FIXED
- Hoặc cập nhật service để gửi `start_date`, `end_date`

---

## 📊 Checklist Kiểm tra

- [ ] Backend: Cập nhật `LichLamViecController.php`
- [ ] Backend: Cập nhật `routes/api.php`
- [ ] Backend: Kiểm tra database có dữ liệu không
- [ ] Backend: Test API endpoint trực tiếp
- [ ] Frontend: Kiểm tra `lichLamViecService.ts`
- [ ] Frontend: Kiểm tra `index.vue` có 4 time slots không
- [ ] Frontend: Mở DevTools xem logs
- [ ] Frontend: Test giao diện, xem schedule hiển thị không

---

## 🎯 Expected Result

Sau khi cập nhật, bạn sẽ thấy:

✅ **Ở Vue Component:**

- 4 hàng (Sáng, Chiều, Tối, Phân công)
- Các ô lịch hiển thị đúng
- Ngày 31/12 hiển thị được

✅ **Ở Console (DevTools):**

- Logs từ `fetchScheduleData()`
- Không có error
- Dữ liệu được parse đúng

✅ **Ở API Response:**

- Status: true
- Schedule có dữ liệu (không rỗng)
- Dates đúng format

---

## 📞 Support

Nếu vẫn có vấn đề:

1. Kiểm tra logs: `storage/logs/laravel.log`
2. Kiểm tra DevTools console
3. Kiểm tra API response (Postman)
4. Kiểm tra database directly
