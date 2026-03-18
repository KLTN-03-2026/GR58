# 📝 So sánh Trước/Sau - Phần Quan trọng

## 1️⃣ getMySchedule() - Parameter Handling

### ❌ TRƯỚC (SAI)

```php
public function getMySchedule(Request $request): JsonResponse
{
    // ...

    // ❌ Chỉ check start_date/end_date
    $request->validate([
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);

    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

    // ❌ NẾU VUE GỬII: ?tungay=2025-12-29&denngay=2025-01-04
    // TẠI SẠO:
    // - $request->get('start_date') = null (parameter không khớp)
    // - Dùng mặc định: startOfMonth() = 2025-12-01
    // - Query trả về dữ liệu tháng 12 thay vì tuần
}
```

**Hậu quả:**

```javascript
// Vue gửi
?tungay=2025-12-29&denngay=2025-01-04

// PHP nhận
startDate = null → mặc định = 2025-12-01
endDate = null → mặc định = 2025-12-31

// Query
SELECT * FROM lich_lam_viec
WHERE nhan_vien_id = 1
AND ngay_lam BETWEEN '2025-12-01' AND '2025-12-31'
// ← SAIS! Query cho cả tháng chứ không phải tuần

// Nếu không có data cho tháng → Array rỗng
// Nếu có data khác → Dữ liệu nhầm
```

---

### ✅ SAU (ĐÚNG)

```php
public function getMySchedule(Request $request): JsonResponse
{
    // ...

    // ✅ Hỗ trợ CẢ 2 parameter names
    if ($request->filled('tungay') && $request->filled('denngay')) {
        $startDate = $request->get('tungay');      // ← VUE gửi cái này
        $endDate = $request->get('denngay');
    } else {
        // Fallback cho parameter khác
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

    // ✅ Thêm validation
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
        return response()->json([
            'status' => false,
            'message' => 'Định dạng ngày start_date phải là YYYY-MM-DD',
        ], 400);
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
        return response()->json([
            'status' => false,
            'message' => 'Định dạng ngày end_date phải là YYYY-MM-DD',
        ], 400);
    }

    // ✅ Thêm logging
    Log::info('getMySchedule called', [
        'user_id' => $user->id,
        'nhan_vien_id' => $nhanVienId,
        'startDate' => $startDate,    // 2025-12-29
        'endDate' => $endDate,        // 2025-01-04
    ]);

    // ✅ Query sẽ ĐÚNG
    $lichLamViecs = LichLamViec::where('nhan_vien_id', $nhanVienId)
        ->whereBetween('ngay_lam', [$startDate, $endDate])
        ->get();

    // Logs: Fetched lich_lam_viec {"count":3,"dates":["2025-12-29","2025-12-30","2025-12-31"]}
}
```

**Hợp lý:**

```javascript
// Vue gửi
?tungay=2025-12-29&denngay=2025-01-04

// PHP nhận
$request->get('tungay') = '2025-12-29' ✅
$request->get('denngay') = '2025-01-04' ✅

// Query
SELECT * FROM lich_lam_viec
WHERE nhan_vien_id = 1
AND ngay_lam BETWEEN '2025-12-29' AND '2025-01-04'
// ← ĐÚNG! Lấy đúng tuần

// Result: ["2025-12-29", "2025-12-30", "2025-12-31", "2026-01-01", "2026-01-02", "2026-01-03", "2026-01-04"]
```

---

## 2️⃣ Routes - Route Mapping

### ❌ TRƯỚC (SAI)

**File: routes/api.php**

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('lich-lam-viec', LichLamViecController::class);
    // ❌ Laravel tự động map:
    // GET /lich-lam-viec → index()
    // POST /lich-lam-viec → store()
    // GET /lich-lam-viec/{id} → show()
    // PUT /lich-lam-viec/{id} → update()
    // DELETE /lich-lam-viec/{id} → destroy()

    // Nhưng bạn cần:
    // GET /lich-lam-viec → getMySchedule()  ← KHÔNG ĐƯỢC!
});

// Kết quả: GET /lich-lam-viec sẽ gọi index()
public function index(Request $request): JsonResponse
{
    // ❌ index() không có logic để lấy lịch của user đang đăng nhập
    // Nó chỉ lấy theo filter params: ngay_lam, nhan_vien_id, thoi_gian_truc
    // Nếu không có filter → Trả về TẤT CẢ lịch (không filtered by user)

    $query = LichLamViec::with('nhanVien');
    // ❌ Không check user!
}
```

---

### ✅ SAU (ĐÚNG)

**File: routes/api.php**

```php
Route::middleware('auth:sanctum')->group(function () {
    // ✅ Đặt custom routes TRƯỚC apiResource
    Route::get('/lich-lam-viec', [LichLamViecController::class, 'getMySchedule']);
    Route::get('/lich-lam-viec/hom-nay', [LichLamViecController::class, 'getMyTodaySchedule']);
    Route::get('/lich-lam-viec/chi-tiet/{id}', [LichLamViecController::class, 'show']);
    Route::post('/lich-lam-viec', [LichLamViecController::class, 'store']);

    // Các routes khác (nếu cần)
    // Route::apiResource('lich-lam-viec', LichLamViecController::class, ['except' => ['index', 'store', 'show']]);
});

// Kết quả: GET /lich-lam-viec sẽ gọi getMySchedule()
public function getMySchedule(Request $request): JsonResponse
{
    // ✅ Lấy user đang đăng nhập
    $user = $request->user();

    // ✅ Lấy lịch của chính user đó
    $nhanVienId = $user->id;

    // ✅ Query chính xác
    $lichLamViecs = LichLamViec::where('nhan_vien_id', $nhanVienId)
        ->whereBetween('ngay_lam', [$startDate, $endDate])
        ->get();
}
```

---

## 3️⃣ Frontend - Logging & Empty Handling

### ❌ TRƯỚC (VỀ CƠNG BẢN SẢN PHẨM)

```javascript
// Vue: index.vue dòng 758
const data = await getMySchedule(
  formatDate(startOfWeek.value),
  formatDate(endOfWeek.value)
);

// ❌ Không có logging khi trả về data rỗng
if (data.status) {
  scheduleData.value = data.data;
  updateCalendarData(data.data);
}

// ❌ Khi data.data.schedule = []
// Calendar sẽ hoàn toàn trống, không biết lý do
```

---

### ✅ SAU (ĐÚNG)

```javascript
// Vue: index.vue dòng 758
const data = await getMySchedule(
  formatDate(startOfWeek.value),
  formatDate(endOfWeek.value)
);

// ✅ Detailed logging
console.log("fetchScheduleData response:", data);

if (data.status) {
  scheduleData.value = data.data;
  console.log("Schedule data to update:", data.data);
  updateCalendarData(data.data);

  // ...
}

// ✅ updateCalendarData với error handling
const updateCalendarData = (data) => {
  if (!data || !data.schedule) {
    console.warn("updateCalendarData: No data or schedule", data);
    return; // ← Không crash app
  }

  console.log("updateCalendarData: Processing schedule", data.schedule);

  data.schedule.forEach((daySchedule) => {
    // ✅ Log mỗi ngày
    console.log(
      `Processing day ${daySchedule.date}, shifts:`,
      daySchedule.lich_lam_viec
    );

    // ✅ Xử lý array rỗng
    const shifts = daySchedule.lich_lam_viec || [];

    if (!Array.isArray(shifts)) {
      console.warn(
        `lich_lam_viec is not array for ${daySchedule.date}:`,
        daySchedule.lich_lam_viec
      );
      return;
    }

    if (shifts.length === 0) {
      console.log(`No shifts assigned for ${daySchedule.date}`);
    }
  });
};
```

---

## 📊 Tóm tắt Sự Khác Biệt

| Khía cạnh            | Trước                        | Sau                                              |
| -------------------- | ---------------------------- | ------------------------------------------------ |
| Parameter Support    | Chỉ `start_date`, `end_date` | Cả `tungay`/`denngay` và `start_date`/`end_date` |
| Parameter Fallback   | Không                        | ✅ Có mặc định hợp lý                            |
| Validation           | Không                        | ✅ Kiểm tra format ngày                          |
| Logging              | Không                        | ✅ Chi tiết từng bước                            |
| Route Mapping        | Sai (apiResource)            | ✅ Custom routes rõ ràng                         |
| Empty Array Handling | Có thể crash                 | ✅ Graceful fallback                             |
| Date Parse           | Có vấn đề timezone           | ✅ Safe parsing                                  |
| Error Messages       | Chung chung                  | ✅ Chi tiết & descriptive                        |

---

## 🎯 Key Takeaway

**Lý do lịch không hiển thị:**

1. **Vue gửi parameter:** `?tungay=2025-12-29&denngay=2025-01-04`
2. **PHP nhận bị mất:** `$request->get('start_date')` = null
3. **Query sai:** Lấy data tháng 12 thay vì tuần 29/12-04/01
4. **Kết quả:** Array trống hoặc dữ liệu sai
5. **Frontend:** Hiển thị bảng lịch trống

**Giải pháp:**
✅ Hỗ trợ parameter names đúng → Query chính xác → Dữ liệu đúng → Hiển thị đúng
