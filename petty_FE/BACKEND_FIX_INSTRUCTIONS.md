# 🔧 FIX BACKEND - LichHenController.php

## Vấn đề

Backend không load relationship `khachHang`, nên khách hàng không được trả về trong API response.

## Giải pháp

### ✏️ File: `app/Http/Controllers/LichHenController.php`

#### **Thay đổi 1: Method `index()` - Dòng 55**

```php
// ❌ HIỆN TẠI
$query = LichHen::with(['thuCung', 'dichVu', 'nhanVien', 'thanhToan'])
    ->where('khach_hang_id', $user->id);

// ✅ SỬA THÀNH
$query = LichHen::with(['thuCung', 'dichVu', 'nhanVien', 'thanhToan', 'khachHang'])
    ->where('khach_hang_id', $user->id);
```

**Giải thích**: Thêm `'khachHang'` vào danh sách relationships để load thông tin khách hàng.

---

### 📋 Toàn bộ method `index()` sau khi sửa:

```php
/**
 * Display a listing of the authenticated customer's appointments.
 */
public function index(Request $request): JsonResponse
{
    $user = $request->user();
    // ✅ Thêm 'khachHang' vào đây
    $query = LichHen::with(['thuCung', 'dichVu', 'nhanVien', 'thanhToan', 'khachHang'])
        ->where('khach_hang_id', $user->id);

    // Filter by pet name (thu_cung.ten_thu_cung)
    if ($request->filled('pet_name')) {
        $petName = $request->get('pet_name');
        $query->whereHas('thuCung', function ($q) use ($petName) {
            $q->where('ten_thu_cung', 'like', '%' . $petName . '%');
        });
    }

    // Filter by service id or service name
    if ($request->filled('dich_vu_id')) {
        $query->where('dich_vu_id', $request->get('dich_vu_id'));
    } elseif ($request->filled('dich_vu_name')) {
        $dvName = $request->get('dich_vu_name');
        $query->whereHas('dichVu', function ($q) use ($dvName) {
            $q->where('ten', 'like', '%' . $dvName . '%');
        });
    }

    // Filter by trạng thái
    if ($request->filled('trang_thai')) {
        $query->where('trang_thai', $request->get('trang_thai'));
    }

    // Filter by time range: from_date and to_date (accepts date or datetime)
    try {
        $from = $request->filled('from_date') ? Carbon::parse($request->get('from_date')) : null;
        $to = $request->filled('to_date') ? Carbon::parse($request->get('to_date')) : null;
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => \Illuminate\Support\Facades\Lang::get('messages.invalid_date_format'),
        ], 422);
    }

    if ($from && $to) {
        // include whole day for to date if only date provided
        $query->whereBetween('ngay_gio', [$from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s')]);
    } elseif ($from) {
        $query->where('ngay_gio', '>=', $from->format('Y-m-d H:i:s'));
    } elseif ($to) {
        $query->where('ngay_gio', '<=', $to->format('Y-m-d H:i:s'));
    }

    $query->orderBy('ngay_gio', 'asc');

    // pagination or full list
    if ($request->has('per_page')) {
        $perPage = (int) $request->get('per_page', 15);
        $data = $query->paginate($perPage);
    } else {
        $data = $query->get();
    }

    return response()->json([
        'status' => true,
        'data' => $data,
    ]);
}
```

---

## ✅ Kiểm tra các method khác

### Method `show()` - ✅ KHÔNG cần sửa

```php
// Đã có 'khachHang'
$lichHen->load(['thuCung', 'dichVu', 'nhanVien', 'thanhToan', 'khachHang'])
```

### Method `store()` - ✅ KHÔNG cần sửa

```php
// Đã có 'khachHang'
$lichHen->load(['thuCung', 'dichVu', 'khachHang'])
```

### Method `updateNgayGio()` - ✅ KHÔNG cần sửa

```php
// Đã có 'khachHang'
$lichHen->fresh()->load(['thuCung', 'dichVu', 'khachHang'])
```

---

## 📊 Tóm tắt thay đổi

| Method            | Hiện tại                                         | Cần thêm      | Sau sửa |
| ----------------- | ------------------------------------------------ | ------------- | ------- |
| `index()`         | `['thuCung', 'dichVu', 'nhanVien', 'thanhToan']` | `'khachHang'` | ✅      |
| `show()`          | ✅ Đúng                                          | -             | ✅      |
| `store()`         | ✅ Đúng                                          | -             | ✅      |
| `updateNgayGio()` | ✅ Đúng                                          | -             | ✅      |

---

## 🚀 Sau khi sửa

1. Save file backend
2. Backend sẽ tự động compile/reload (tùy setup)
3. Mở lại FE trong browser (Ctrl+R hoặc Cmd+R)
4. Console sẽ show: `khach_hang object: { id: 1, ho_ten: "Nguyễn Văn A", ... }`
5. ✅ Tên khách hàng sẽ hiển thị đúng trong bảng!

---

## 🐛 Nếu vẫn không được

Kiểm tra:

1. **Model relationship**: `LichHen` model có định nghĩa `khachHang()` relationship chưa?

   ```php
   public function khachHang()
   {
       return $this->belongsTo(KhachHang::class, 'khach_hang_id');
   }
   ```

2. **Database**: Có `khach_hang_id` column trong bảng `lich_hen` chưa?

3. **API Response**: Mở DevTools xem response có `khach_hang` object chưa?
