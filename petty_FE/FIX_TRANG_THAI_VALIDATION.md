# Fix Lỗi Validation: trang_thai

## 🔴 Vấn Đề

```
{status: false, message: "Lỗi xác thực dữ liệu", errors: {trang_thai: ["validation.in"]}}
```

Lỗi `validation.in` có nghĩa là giá trị `trang_thai` không nằm trong danh sách các giá trị hợp lệ.

## 🔍 Nguyên Nhân

Frontend đang gửi: `trang_thai: "da_xac_nhan"`

Nhưng Backend chỉ chấp nhận: `pending, confirmed, in-progress, completed, cancelled`

## ✅ Giải Pháp

### 1. **PhanCongBacSi Component - Thay Đổi Giá Trị**

```javascript
// ❌ BEFORE
const response = await client.put(`/lich-hen/${props.appointmentId}`, {
  nhan_vien_id: staff.id,
  trang_thai: "da_xac_nhan", // ← KHÔNG hợp lệ
});

// ✅ AFTER
const response = await client.put(`/lich-hen/${props.appointmentId}`, {
  nhan_vien_id: staff.id,
  trang_thai: "confirmed", // ← Giá trị hợp lệ
});
```

### 2. **Emit Status Correctly**

```javascript
emit("assign", {
  appointmentId: props.appointmentId,
  staffId: staff.id,
  staffName: staff.name,
  status: "confirmed", // ← Đúng giá trị
});
```

### 3. **Backend Validation Rule**

```php
'trang_thai' => 'nullable|in:pending,confirmed,in-progress,completed,cancelled'
```

## 📊 Giá Trị Hợp Lệ

| Giá Trị       | Tiếng Việt   | Tính Năng                            |
| ------------- | ------------ | ------------------------------------ |
| `pending`     | Chờ xác nhận | Trạng thái ban đầu                   |
| `confirmed`   | Đã xác nhận  | **Khi gán bác sĩ** ← Sử dụng cái này |
| `in-progress` | Đang khám    | Khi appointment bắt đầu              |
| `completed`   | Hoàn thành   | Khi appointment kết thúc             |
| `cancelled`   | Đã hủy       | Khi hủy appointment                  |

## 🎯 Status Flow

```
pending (Chờ xác nhận - blue)
   ↓ [Gán bác sĩ]
confirmed (Đã xác nhận - green) ✓
   ↓ [Khám bệnh bắt đầu]
in-progress (Đang khám - purple)
   ↓ [Khám bệnh kết thúc]
completed (Hoàn thành - gray)
```

## 📝 Files Đã Cập Nhật

✅ `src/components/Admin/VanHanh/QuanLyLichHen/PhanCongBacSi/index.vue`

- Thay `trang_thai: "da_xac_nhan"` → `trang_thai: "confirmed"`
- Thay `status: "da_xac_nhan"` → `status: "confirmed"` trong emit

✅ `src/components/Admin/VanHanh/QuanLyLichHen/index.vue`

- Cập nhật `getStatusLabel()` để handle `confirmed`
- Cập nhật status badge styling cho `confirmed` status

## 🧪 Test

1. Click "Chưa gán" trên một lịch hẹn
2. Chọn một bác sĩ từ danh sách
3. ✅ Nên thấy:
   - Doctor name hiển thị
   - Status badge chuyển thành "Đã xác nhận" (green)
   - Không lỗi "validation.in"

## 🚀 Expected Result

```
✅ PUT /lich-hen/LH001
   {
     "nhan_vien_id": 5,
     "trang_thai": "confirmed"  ← Hợp lệ
   }

✅ Response:
   {
     "status": true,
     "data": { ... }
   }

✅ UI Update:
   - Show doctor name
   - Status: "Đã xác nhận" (green)
```
