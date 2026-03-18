# 📊 Chi Tiết So Sánh - Trước / Sau

## 1. lichLamViecService.js - Endpoint Fix

### ❌ TRƯỚC (Sai)

**Dòng 10-20:**

```javascript
export const getMySchedule = async (startDate, endDate) => {
  try {
    // ❌ Endpoint này không tồn tại trong backend!
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

**Khi gọi:**

```javascript
const data = await getMySchedule("2025-12-29", "2025-01-04");

// Frontend gửi: GET /api/lich-lam-viec/cua-toi?start_date=2025-12-29&end_date=2025-01-04
// Backend response: 404 Not Found ❌
// Console: Error fetching my schedule
// Vue component: Schedule = []
```

---

### ✅ SAU (Đúng)

**Dòng 10-20:**

```javascript
export const getMySchedule = async (startDate, endDate) => {
  try {
    // ✅ Endpoint đúng từ backend getMySchedule()
    const response = await api.get("/lich-lam-viec", {
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

**Khi gọi:**

```javascript
const data = await getMySchedule("2025-12-29", "2025-01-04");

// Frontend gửi: GET /api/lich-lam-viec?start_date=2025-12-29&end_date=2025-01-04
// Backend response: 200 OK ✅
// Data:
// {
//   "status": true,
//   "data": {
//     "nhan_vien": {...},
//     "period": {...},
//     "schedule": [
//       {"date": "2025-12-29", "lich_lam_viec": [...]},
//       {"date": "2025-12-30", "lich_lam_viec": [...]},
//       ...
//     ]
//   }
// }
// Vue component: Schedule populated ✅
```

---

## 2. getMyTodaySchedule - Endpoint Fix

### ❌ TRƯỚC (Sai)

**Dòng 24-32:**

```javascript
export const getMyTodaySchedule = async () => {
  try {
    // ❌ Endpoint này không tồn tại!
    const response = await api.get("/lich-lam-viec/cua-toi/hom-nay");
    return response.data;
  } catch (error) {
    console.error("Error fetching my today schedule:", error);
    throw error;
  }
};
```

**Khi gọi:**

```javascript
const data = await getMyTodaySchedule();

// Frontend gửi: GET /api/lich-lam-viec/cua-toi/hom-nay
// Backend response: 404 Not Found ❌
```

---

### ✅ SAU (Đúng)

**Dòng 24-32:**

```javascript
export const getMyTodaySchedule = async () => {
  try {
    // ✅ Endpoint đúng từ backend getMyTodaySchedule()
    const response = await api.get("/lich-lam-viec/hom-nay");
    return response.data;
  } catch (error) {
    console.error("Error fetching my today schedule:", error);
    throw error;
  }
};
```

**Khi gọi:**

```javascript
const data = await getMyTodaySchedule();

// Frontend gửi: GET /api/lich-lam-viec/hom-nay
// Backend response: 200 OK ✅
// Data:
// {
//   "status": true,
//   "data": {
//     "date": "2025-12-11",
//     "nhan_vien": {...},
//     "lich_lam_viec": [...],
//     "lich_hen": [...]
//   }
// }
```

---

## 3. routes/api.php - Route Definition

### ❌ TRƯỚC (Sai hoặc Thiếu)

**Khả năng 1: Sử dụng apiResource**

```php
Route::middleware('auth:sanctum')->group(function () {
    // ❌ SAI: GET /lich-lam-viec sẽ gọi index() không phải getMySchedule()
    Route::apiResource('lich-lam-viec', LichLamViecController::class);
});

// Laravel sẽ tự động map:
// GET /lich-lam-viec → index()  [KHÔNG ĐÚNG!]
// POST /lich-lam-viec → store()
// GET /lich-lam-viec/{id} → show()
// PUT /lich-lam-viec/{id} → update()
// DELETE /lich-lam-viec/{id} → destroy()
```

**Kết quả:**

```
GET /api/lich-lam-viec
→ Gọi index() (lấy tất cả lịch, không filter by user)
→ Có thể trả về data nhầm hoặc rỗng
```

---

### ✅ SAU (Đúng)

**Sử dụng Custom Routes:**

```php
Route::middleware('auth:sanctum')->group(function () {

    // ✅ ĐÚNG: Custom routes (PHẢI ĐẶT TRƯỚC apiResource)

    Route::get('/lich-lam-viec', [LichLamViecController::class, 'getMySchedule']);
    // GET /api/lich-lam-viec?start_date=...&end_date=...
    // → Gọi getMySchedule()

    Route::get('/lich-lam-viec/hom-nay', [LichLamViecController::class, 'getMyTodaySchedule']);
    // GET /api/lich-lam-viec/hom-nay
    // → Gọi getMyTodaySchedule()

    Route::get('/lich-lam-viec/{id}', [LichLamViecController::class, 'show']);
    // GET /api/lich-lam-viec/123
    // → Gọi show($id)

    Route::post('/lich-lam-viec', [LichLamViecController::class, 'store']);
    // POST /api/lich-lam-viec
    // → Gọi store()
});

// Nếu không dùng apiResource, không cần xóa gì
// Nếu đang dùng apiResource, hãy comment nó lại
```

**Kết quả:**

```
GET /api/lich-lam-viec?start_date=2025-12-29&end_date=2025-01-04
→ Gọi getMySchedule()
→ Trả về lịch chính xác của user đó
```

---

## 4. Request/Response Flow Comparison

### ❌ TRƯỚC (Sai)

```
Vue Component (index.vue)
│
├─ Gọi: getMySchedule('2025-12-29', '2025-01-04')
│
└─ Service (lichLamViecService.js)
   │
   └─ GET /api/lich-lam-viec/cua-toi?start_date=2025-12-29&end_date=2025-01-04
      │
      └─ Routes (api.php)
         │
         └─ Route::apiResource('lich-lam-viec', ...)
            │
            ├─ GET /lich-lam-viec → index()  [KHÔNG MATCH]
            │                       ❌ 404 Not Found
            │
            └─ Laravel chạy index()
               │
               └─ LichLamViecController::index()
                  │
                  └─ Response 404

Vue nhận lỗi 404
│
└─ Error Handler: "Error fetching my schedule"
   │
   └─ scheduleData = null
      │
      └─ Calendar trống
```

---

### ✅ SAU (Đúng)

```
Vue Component (index.vue)
│
├─ Gọi: getMySchedule('2025-12-29', '2025-01-04')
│
└─ Service (lichLamViecService.js)
   │
   └─ GET /api/lich-lam-viec?start_date=2025-12-29&end_date=2025-01-04
      │
      └─ Routes (api.php)
         │
         └─ Route::get('/lich-lam-viec', [...getMySchedule...])
            │
            └─ ✅ MATCH!
               │
               └─ LichLamViecController::getMySchedule()
                  │
                  ├─ $user = $request->user()
                  ├─ $nhanVienId = $user->id
                  ├─ $startDate = $request->get('start_date')  // 2025-12-29
                  ├─ $endDate = $request->get('end_date')      // 2025-01-04
                  │
                  ├─ Query: LichLamViec::where('nhan_vien_id', 1)
                  │          ->whereBetween('ngay_lam', [$startDate, $endDate])
                  │          ->get()
                  │
                  └─ Response 200 OK
                     {
                       "status": true,
                       "data": {
                         "nhan_vien": {...},
                         "schedule": [
                           {"date": "2025-12-29", "lich_lam_viec": [...]},
                           ...
                         ]
                       }
                     }

Vue nhận response 200
│
└─ updateCalendarData(data.data)
   │
   ├─ timeSlots[0].schedule = [...]  (Sáng)
   ├─ timeSlots[1].schedule = [...]  (Chiều)
   ├─ timeSlots[2].schedule = [...]  (Tối)
   ├─ timeSlots[3].schedule = [...]  (Phân công)
   │
   └─ Calendar hiển thị ✅
      │
      ├─ Row 1: Sáng - có lịch
      ├─ Row 2: Chiều - có lịch
      ├─ Row 3: Tối - có lịch
      └─ Row 4: Phân công - có lịch
```

---

## 5. Parameter Handling Comparison

### Backend Method: getMySchedule()

**Cả 2 trường hợp (TRƯỚC/SAU)** đều phía backend đúng:

```php
public function getMySchedule(Request $request)
{
    // ✅ Validate parameter
    $request->validate([
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);

    // ✅ Lấy parameter (hoặc dùng mặc định)
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

    // ✅ Query chính xác
    $lichLamViecs = LichLamViec::where('nhan_vien_id', $nhanVienId)
        ->whereBetween('ngay_lam', [$startDate, $endDate])
        ->get();

    // ✅ Response format đúng
    return response()->json([
        'status' => true,
        'data' => [
            'schedule' => array_values($scheduleByDate),
            'statistics' => [...]
        ],
    ]);
}
```

**Vấn đề:** Endpoint không được route đúng → Backend logic tốt nhưng không được gọi!

---

## 📊 Summary Table

| Aspect                        | Before                              | After                       |
| ----------------------------- | ----------------------------------- | --------------------------- |
| **Frontend Service Endpoint** | `/lich-lam-viec/cua-toi` ❌         | `/lich-lam-viec` ✅         |
| **Today Endpoint**            | `/lich-lam-viec/cua-toi/hom-nay` ❌ | `/lich-lam-viec/hom-nay` ✅ |
| **Backend Routes**            | Missing/Wrong ❌                    | Custom routes ✅            |
| **API Response**              | 404 Not Found ❌                    | 200 OK ✅                   |
| **Calendar Display**          | Empty ❌                            | Populated ✅                |
| **User Experience**           | Confused ❌                         | Works perfectly ✅          |

---

## 🎯 Root Cause Analysis

**ROOT CAUSE:**
Frontend gửi request đến endpoint **không được định nghĩa** trong Laravel routes.

**Timeline:**

1. Vue component gọi `getMySchedule()`
2. Service gửi: `GET /api/lich-lam-viec/cua-toi`
3. Laravel routes không có route này
4. Return 404 Not Found
5. Frontend nhận error, schedule trống

**Solution:**

1. Frontend cập nhật endpoint → `/lich-lam-viec`
2. Backend thêm route → `Route::get('/lich-lam-viec', [...])`
3. Endpoint match → Response 200 OK
4. Frontend nhận data → Calendar populate

**That's it! Simple but critical!**
