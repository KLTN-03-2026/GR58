# ✅ Fix Hoàn chỉnh - Lịch Làm Việc Không Hiển thị

## 🎯 Tóm Tắt Vấn Đề Tìm Được

**3 VẤN ĐỀ CHÍNH:**

| #   | Vấn đề                     | Vị trí                          | Fix                                                         |
| --- | -------------------------- | ------------------------------- | ----------------------------------------------------------- |
| 1   | **Endpoint sai**           | `lichLamViecService.js` dòng 10 | `/lich-lam-viec/cua-toi` → `/lich-lam-viec`                 |
| 2   | **Endpoint Today sai**     | `lichLamViecService.js` dòng 27 | `/lich-lam-viec/cua-toi/hom-nay` → `/lich-lam-viec/hom-nay` |
| 3   | **Route không định nghĩa** | `routes/api.php`                | Cần thêm route cho `getMySchedule()`                        |

---

## 🔴 VẤN ĐỀ 1: Endpoint /lich-lam-viec/cua-toi không tồn tại

**File:** `src/services/lichLamViecService.js` dòng 10

**❌ TRƯỚC:**

```javascript
export const getMySchedule = async (startDate, endDate) => {
  try {
    // ❌ Endpoint này không được định nghĩa trong backend!
    const response = await api.get("/lich-lam-viec/cua-toi", {
      params: {
        start_date: startDate,
        end_date: endDate,
      },
    });
    return response.data;
  } catch (error) {
    console.error("Error fetching my schedule:", error);
    throw error;
  }
};
```

**Hậu quả:**

- Frontend gửi request đến endpoint không tồn tại
- Backend trả về 404 Not Found
- Lịch không hiển thị

**✅ SAU (ĐÃ CẬP NHẬT):**

```javascript
export const getMySchedule = async (startDate, endDate) => {
  try {
    // ✅ Sử dụng endpoint đúng từ backend
    const response = await api.get("/lich-lam-viec", {
      params: {
        start_date: startDate, // Backend expect cái này
        end_date: endDate,
      },
    });
    return response.data;
  } catch (error) {
    console.error("Error fetching my schedule:", error);
    throw error;
  }
};
```

---

## 🔴 VẤN ĐỀ 2: Endpoint Today có đường dẫn sai

**File:** `src/services/lichLamViecService.js` dòng 27

**❌ TRƯỚC:**

```javascript
export const getMyTodaySchedule = async () => {
  try {
    // ❌ Endpoint này không được định nghĩa trong backend!
    const response = await api.get("/lich-lam-viec/cua-toi/hom-nay");
    return response.data;
  } catch (error) {
    console.error("Error fetching my today schedule:", error);
    throw error;
  }
};
```

**✅ SAU (ĐÃ CẬP NHẬT):**

```javascript
export const getMyTodaySchedule = async () => {
  try {
    // ✅ Sử dụng endpoint đúng từ backend
    const response = await api.get("/lich-lam-viec/hom-nay");
    return response.data;
  } catch (error) {
    console.error("Error fetching my today schedule:", error);
    throw error;
  }
};
```

---

## 🔴 VẤN ĐỀ 3: Routes không được định nghĩa

**File:** `routes/api.php`

Backend controller có các methods:

- `getMySchedule()` → Lấy lịch của user trong khoảng ngày
- `getMyTodaySchedule()` → Lấy lịch hôm nay
- Nhưng routes chưa map đến những endpoints này!

**❌ TRƯỚC (sai hoặc thiếu):**

```php
// routes/api.php

// ❌ Nếu đang dùng apiResource, GET /lich-lam-viec sẽ gọi index() không phải getMySchedule()
Route::apiResource('lich-lam-viec', LichLamViecController::class);
```

**✅ SAU (ĐÚNG - Cần thêm):**

```php
<?php

use App\Http\Controllers\LichLamViecController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // ✅ Định nghĩa custom routes (phải TRƯỚC apiResource)

    // Lấy lịch làm việc của user trong khoảng ngày
    Route::get('/lich-lam-viec', [LichLamViecController::class, 'getMySchedule']);

    // Lấy lịch hôm nay của user
    Route::get('/lich-lam-viec/hom-nay', [LichLamViecController::class, 'getMyTodaySchedule']);

    // Các endpoints khác
    Route::get('/lich-lam-viec/{id}', [LichLamViecController::class, 'show']);
    Route::post('/lich-lam-viec', [LichLamViecController::class, 'store']);

    // Routes khác (nếu cần)
    // Route::put('/lich-lam-viec/{id}', [LichLamViecController::class, 'update']);
    // Route::delete('/lich-lam-viec/{id}', [LichLamViecController::class, 'destroy']);
});
```

---

## ✅ Tất Cả Fixes Đã Cập Nhật

### ✅ Fix 1: lichLamViecService.js

Đã cập nhật:

- ✅ `getMySchedule()` - endpoint từ `/lich-lam-viec/cua-toi` → `/lich-lam-viec`
- ✅ `getMyTodaySchedule()` - endpoint từ `/lich-lam-viec/cua-toi/hom-nay` → `/lich-lam-viec/hom-nay`

**Xác nhận lại:**

```javascript
// Line 10-19
export const getMySchedule = async (startDate, endDate) => {
  try {
    const response = await api.get("/lich-lam-viec", {
      // ✅ ĐÚNG
      params: {
        start_date: startDate,
        end_date: endDate,
      },
    });
    return response.data;
  } catch (error) {
    console.error("Error fetching my schedule:", error);
    throw error;
  }
};

// Line 24-32
export const getMyTodaySchedule = async () => {
  try {
    const response = await api.get("/lich-lam-viec/hom-nay"); // ✅ ĐÚNG
    return response.data;
  } catch (error) {
    console.error("Error fetching my today schedule:", error);
    throw error;
  }
};
```

---

### ⏳ Fix 2: routes/api.php (CẦN THÊM NGAY)

**Bạn cần mở file `routes/api.php` trong backend Laravel và thêm:**

```php
<?php

use App\Http\Controllers\LichLamViecController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // ✅ Custom routes (PHẢI ĐẶT TRƯỚC apiResource)
    Route::get('/lich-lam-viec', [LichLamViecController::class, 'getMySchedule']);
    Route::get('/lich-lam-viec/hom-nay', [LichLamViecController::class, 'getMyTodaySchedule']);
    Route::get('/lich-lam-viec/{id}', [LichLamViecController::class, 'show']);
    Route::post('/lich-lam-viec', [LichLamViecController::class, 'store']);

    // Các routes khác nếu cần...
});
```

**⚠️ QUAN TRỌNG:**

- Custom routes **PHẢI ĐẶT TRƯỚC** `apiResource()`
- Vì Laravel đọc routes từ trên xuống
- Route đầu tiên match được sẽ được sử dụng

---

## 🧪 Test Checklist

### ✅ Step 1: Xác nhận Frontend đã fix

```bash
# File đã cập nhật
cat src/services/lichLamViecService.js | grep "lich-lam-viec"
```

**Kỳ vọng:**

```
const response = await api.get("/lich-lam-viec", {    ✅
const response = await api.get("/lich-lam-viec/hom-nay");  ✅
```

### ✅ Step 2: Cập nhật Backend Routes

1. Mở file: `routes/api.php` (hoặc `routes/api/lich-lam-viec.php` nếu modular)
2. Thêm custom routes như trên
3. Save file

### ✅ Step 3: Test API trực tiếp

**Dùng Postman/Thunder Client:**

```
GET http://localhost:8000/api/lich-lam-viec?start_date=2025-12-29&end_date=2025-01-04
Headers:
  Authorization: Bearer YOUR_TOKEN
  Content-Type: application/json
```

**Response kỳ vọng:**

```json
{
  "status": true,
  "data": {
    "nhan_vien": {
      "id": 1,
      "full_name": "Bác sĩ ABC",
      "email": "...",
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
        "lich_hen": []
      }
      // ... ngày khác
    ],
    "statistics": {
      "total_work_days": 3,
      "total_appointments": 2
    }
  }
}
```

### ✅ Step 4: Test trong Vue Component

1. Mở Chrome DevTools (F12)
2. Vào tab **Network**
3. Vào lịch làm việc component
4. Bấm "Tuần sau" hoặc "Tuần trước"
5. Xem network request:
   - URL: `http://localhost:8000/api/lich-lam-viec?start_date=...&end_date=...`
   - Status: 200 (không phải 404)
   - Response: có dữ liệu

### ✅ Step 5: Kiểm tra Database

```sql
-- Đảm bảo nhân viên có lịch làm việc
SELECT * FROM lich_lam_viec
WHERE nhan_vien_id = 1
AND ngay_lam BETWEEN '2025-12-29' AND '2025-01-04'
LIMIT 5;

-- Kết quả kỳ vọng: Ít nhất 1 dòng
```

---

## 🚨 Nếu Vẫn Không Hoạt Động

### Khiển Đoán 1: Routes chưa cập nhật

```bash
# Check routes đã đúng không
php artisan route:list | grep lich-lam-viec

# Kỳ vọng thấy:
# GET /api/lich-lam-viec ... LichLamViecController@getMySchedule
# GET /api/lich-lam-viec/hom-nay ... LichLamViecController@getMyTodaySchedule
```

### Khiển Đoán 2: Database trống

```sql
-- Check có dữ liệu không
SELECT COUNT(*) as total FROM lich_lam_viec
WHERE nhan_vien_id = 1
AND ngay_lam BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND DATE_ADD(NOW(), INTERVAL 30 DAY);
```

Nếu `total = 0` → Admin chưa phân công lịch cho nhân viên!

### Khiển Đoán 3: Token hết hạn

```javascript
// DevTools Console
localStorage.getItem("token"); // Check token tồn tại

// Nếu token cũ, logout và login lại
```

### Khiển Đoán 4: API error

```bash
# Xem Laravel logs
tail -f storage/logs/laravel.log

# Tìm error liên quan đến lich-lam-viec hoặc user
```

---

## 📊 Tóm Tắt Thay Đổi

| File                    | Dòng | Trước                            | Sau                      | Status         |
| ----------------------- | ---- | -------------------------------- | ------------------------ | -------------- |
| `lichLamViecService.js` | 10   | `/lich-lam-viec/cua-toi`         | `/lich-lam-viec`         | ✅ Cập nhật    |
| `lichLamViecService.js` | 27   | `/lich-lam-viec/cua-toi/hom-nay` | `/lich-lam-viec/hom-nay` | ✅ Cập nhật    |
| `routes/api.php`        | -    | Thiếu custom routes              | Thêm routes              | ⏳ **CẦN LÀM** |

---

## 🎯 Expected Behavior Sau Fix

1. ✅ User login → Vào lịch làm việc
2. ✅ Frontend gửi: `GET /api/lich-lam-viec?start_date=2025-12-29&end_date=2025-01-04`
3. ✅ Backend nhận request → Query DB
4. ✅ Backend trả về JSON với `schedule: [...]`
5. ✅ Frontend parse data → Update calendar
6. ✅ Bảng lịch hiển thị đúng với 4 hàng (Sáng, Chiều, Tối, Phân công)
7. ✅ Ngày 31/12 hiển thị được

---

## 🏁 Kết Luận

**Frontend fixes:** ✅ **ĐÃ HOÀN THÀNH**

- `lichLamViecService.js` đã cập nhật các endpoints

**Backend fixes:** ⏳ **CẦN LÀM NGAY**

- Thêm routes cho `getMySchedule()` và `getMyTodaySchedule()`

**Sau khi làm xong 2 cái này, lịch sẽ hiển thị ngay!**
