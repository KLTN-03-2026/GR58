# Fix Backend: Support All Appointments Query

## 🔴 Vấn Đề

Frontend (Admin) cần lấy **tất cả lịch hẹn** từ hệ thống, nhưng hiện tại endpoint `GET /lich-hen` chỉ trả về appointments của **khách hàng hiện tại** (do có `->where('khach_hang_id', $user->id)`).

## 📍 Vị Trí Code Cần Fix

**File:** `app/Http/Controllers/LichHenController.php`  
**Method:** `index()`  
**Dòng:** ~150-170

## ✅ Giải Pháp

### Bước 1: Update `index()` Method

Thay đổi logic để kiểm tra query param `all=1`:

```php
public function index(Request $request): JsonResponse
{
    $query = LichHen::with(['thuCung', 'dichVu', 'nhanVien', 'thanhToan', 'khachHang']);

    // If admin requests all appointments (all=1 param), return all
    if (! $request->filled('all')) {
        // Otherwise, return only customer's own appointments
        $user = $request->user();
        $query->where('khach_hang_id', $user->id);
    }

    // ... rest of filters
    if ($request->filled('pet_name')) {
        // ...
    }

    // ...rest of code

    return response()->json([
        'status' => true,
        'data' => $query->get()->map(fn($item) => $this->transformData($item)),
    ]);
}
```

### Bước 2: Cập Nhật Logic Lọc

Kiểm tra `$request->filled('all')` trước khi áp dụng filter `khach_hang_id`:

```php
public function index(Request $request): JsonResponse
{
    $user = $request->user();

    // Check if requesting all appointments (admin)
    $isAdmin = $request->filled('all');

    $query = LichHen::with(['thuCung', 'dichVu', 'nhanVien', 'thanhToan', 'khachHang']);

    // Only filter by customer if not admin
    if (!$isAdmin) {
        $query->where('khach_hang_id', $user->id);
    }

    // Filter by pet name
    if ($request->filled('pet_name')) {
        $petName = $request->get('pet_name');
        $query->whereHas('thuCung', function ($q) use ($petName) {
            $q->where('ten_thu_cung', 'like', '%' . $petName . '%');
        });
    }

    // Filter by service id
    if ($request->filled('dich_vu_id')) {
        $query->where('dich_vu_id', $request->get('dich_vu_id'));
    } elseif ($request->filled('dich_vu_name')) {
        $dvName = $request->get('dich_vu_name');
        $query->whereHas('dichVu', function ($q) use ($dvName) {
            $q->where('ten', 'like', '%' . $dvName . '%');
        });
    }

    // Filter by status
    if ($request->filled('trang_thai')) {
        $query->where('trang_thai', $request->get('trang_thai'));
    }

    // ... date filters ...

    $data = $query->get();

    return response()->json([
        'status' => true,
        'data' => $data->map(fn($item) => $this->transformData($item))->toArray(),
    ]);
}
```

## 🔍 Backend Response Expected

```json
{
  "status": true,
  "data": [
    {
      "id": "LH001",
      "ngay_gio": "2025-12-04 14:00:00",
      "khach_hang": "Nguyễn Văn A",
      "thu_cung": {
        "id": 1,
        "ten_thu_cung": "Bella"
      },
      "dich_vu": {
        "id": 5,
        "ten": "Cắt móng"
      },
      "nhan_vien": {
        "id": 3,
        "ho_ten": "Nguyễn Văn B",
        "chuc_vu": "Bác Sĩ"
      },
      "trang_thai": "confirmed",
      "thanh_toan": {
        "id": 1,
        "trang_thai": "paid"
      }
    },
    ...
  ]
}
```

## 🧪 Test

### Test từ Frontend:

```javascript
// GET /lich-hen?all=1
// Response: Tất cả appointments từ hệ thống

// GET /lich-hen
// Response: Chỉ appointments của khách hàng hiện tại
```

### Test từ Browser DevTools:

```
GET http://localhost:8000/api/lich-hen?all=1
Response: [
  { id: "LH001", nhan_vien: {...}, ... },
  { id: "LH002", nhan_vien: {...}, ... },
  ...
]
```

## ⚙️ Implementation Steps

1. **Tìm method `index()` trong LichHenController.php**
2. **Thêm check:** `if ($request->filled('all'))`
3. **Move filter logic:** Chỉ apply `khach_hang_id` filter nếu không có `all` param
4. **Ensure relationships:** Kiểm tra có `load(['nhanVien', 'khachHang', ...])` không
5. **Test:** Gọi endpoint từ Postman/API client với `?all=1` param

## 📝 Code Changes Summary

```diff
- public function index(Request $request): JsonResponse
- {
-     $user = $request->user();
-     $query = LichHen::with(['thuCung', 'dichVu', 'nhanVien', 'thanhToan'])
-         ->where('khach_hang_id', $user->id);

+ public function index(Request $request): JsonResponse
+ {
+     $user = $request->user();
+     $query = LichHen::with(['thuCung', 'dichVu', 'nhanVien', 'thanhToan', 'khachHang']);
+
+     // Only filter by customer if not requesting all appointments
+     if (!$request->filled('all')) {
+         $query->where('khach_hang_id', $user->id);
+     }
```

## 🎯 Expected Result

✅ Frontend gọi `/lich-hen?all=1` → nhận tất cả appointments  
✅ Admin table hiển thị tất cả lịch hẹn  
✅ Tên bác sĩ `nhan_vien.ho_ten` hiển thị đúng  
✅ Status badge hiển thị đúng
