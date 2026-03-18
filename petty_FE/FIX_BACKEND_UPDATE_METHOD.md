# 🔧 Fix Backend: Thêm method UPDATE cho LichHen

## ❌ Vấn đề hiện tại

- Frontend gửi PUT request đến `/api/lich-hen/{id}` để cập nhật `nhan_vien_id` (phân công bác sĩ)
- Backend không có method `update()` trong LichHenController
- Lỗi: `"The PUT method is not supported for route api/lich-hen/1. Supported methods: GET, HEAD, DELETE."`

## ✅ Giải pháp

### 1. Thêm route vào `routes/api.php` (ĐÃ THỰC HIỆN)

```php
Route::match(['put', 'patch'], '/lich-hen/{lichHen}', [LichHenController::class, 'update']);
```

### 2. Thêm method `update()` vào `LichHenController.php`

Mở file: `C:\xampp\htdocs\PETTY_VCMS_BE\app\Http\Controllers\LichHenController.php`

Thêm method này vào class (sau method `show()` hoặc `updateNgayGio()`):

```php
/**
 * Update lịch hẹn (gán bác sĩ hoặc cập nhật thông tin)
 *
 * @param \Illuminate\Http\Request $request
 * @param \App\Models\LichHen $lichHen
 * @return \Illuminate\Http\JsonResponse
 */
public function update(Request $request, LichHen $lichHen)
{
    try {
        $validated = $request->validate([
            'nhan_vien_id' => 'nullable|exists:nhan_vien,id',
            'trang_thai' => 'nullable|in:pending,confirmed,in-progress,completed,cancelled',
            'ghi_chu' => 'nullable|string',
        ]);

        // Cập nhật các trường được phép
        if (isset($validated['nhan_vien_id'])) {
            $lichHen->nhan_vien_id = $validated['nhan_vien_id'];
        }

        if (isset($validated['trang_thai'])) {
            $lichHen->trang_thai = $validated['trang_thai'];
        }

        if (isset($validated['ghi_chu'])) {
            $lichHen->ghi_chu = $validated['ghi_chu'];
        }

        $lichHen->save();

        // Load relationships để trả lại dữ liệu đầy đủ
        $lichHen->load(['khach_hang', 'thu_cung', 'dich_vu', 'nhan_vien', 'thanh_toan']);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật lịch hẹn thành công',
            'data' => $this->transformData($lichHen),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Lỗi khi cập nhật lịch hẹn: ' . $e->getMessage(),
        ], 500);
    }
}
```

## 📝 Giải thích:

- **`nhan_vien_id`**: ID của bác sĩ để phân công (được phép null nếu chưa gán)
- **`trang_thai`**: Trạng thái lịch hẹn (pending, confirmed, in-progress, completed, cancelled)
- **`ghi_chu`**: Ghi chú thêm về lịch hẹn
- **`load(['khach_hang', 'thu_cung', 'dich_vu', 'nhan_vien', 'thanh_toan'])`**: Load relationships
- **`transformData()`**: Sử dụng method transformData() đã có để convert response

## 🧪 Test

Sau khi thêm method:

1. ✅ Click "Chưa gán" trên lịch hẹn
2. ✅ Chọn bác sĩ từ modal
3. ✅ Kiểm tra response: phải thành công (status: true)
4. ✅ Kiểm tra database: `nhan_vien_id` trong bảng `lich_hen` phải được cập nhật
5. ✅ Kiểm tra Frontend: "Phụ trách" column phải hiển thị tên bác sĩ

## 💡 Lưu ý

- Nếu bạn muốn chỉ cho phép cập nhật `nhan_vien_id` mà không cho cập nhật các trường khác, có thể bỏ các điều kiện khác
- Kiểm tra xem table `nhan_vien` có cột `id` không (để validate foreign key)
- Nên thêm middleware kiểm tra quyền admin nếu cần (như ở các route khác)
