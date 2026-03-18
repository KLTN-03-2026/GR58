# Fix Backend - Thêm khach_hang Relationship

## Vấn đề

Backend LichHenController không load `khach_hang` relationship, khiến FE không thể lấy tên khách hàng.

## Giải pháp - Fix Backend Controller

### File: `app/Http/Controllers/LichHenController.php`

**Hãy sửa dòng này trong method `index()`:**

```php
// ❌ HIỆN TẠI (Thiếu khach_hang)
$query = LichHen::with(['thuCung', 'dichVu', 'nhanVien', 'thanhToan'])
    ->where('khach_hang_id', $user->id);

// ✅ SỬA THÀNH (Thêm khach_hang)
$query = LichHen::with(['thuCung', 'dichVu', 'nhanVien', 'thanhToan', 'khachHang'])
    ->where('khach_hang_id', $user->id);
```

### Cũng sửa method `show()`

```php
// ❌ HIỆN TẠI
return response()->json([
    'status' => true,
    'data' => $lichHen->load(['thuCung', 'dichVu', 'nhanVien', 'thanhToan', 'khachHang']),
]);

// ✅ ĐÃ ĐÚNG (khachHang đã có)
return response()->json([
    'status' => true,
    'data' => $lichHen->load(['thuCung', 'dichVu', 'nhanVien', 'thanhToan', 'khachHang']),
]);
```

## Chi tiết

| Phần      | Hiện tại                                         | Cần thêm      |
| --------- | ------------------------------------------------ | ------------- |
| `index()` | `['thuCung', 'dichVu', 'nhanVien', 'thanhToan']` | `'khachHang'` |
| `show()`  | ✅ Đã có                                         | ✅ Đã có      |
| `store()` | ✅ Có `.load()`                                  | ✅ Đã có      |

## Các field sẽ được load

```
khach_hang: {
  id: 1,
  ho_ten: "Nguyễn Văn A",    // ✅ Đây là field cần lấy
  email: "...",
  so_dien_thoai: "...",
  ...
}
```

## Sau khi fix backend

1. Thêm `'khachHang'` vào `.with()` ở method `index()`
2. Làm mới trang FE (F5)
3. Console sẽ show: `khach_hang object: { id: 1, ho_ten: "...", ...}`
4. Tên khách hàng sẽ hiển thị đúng ✅
