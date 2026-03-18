# Hướng Dẫn Debug - Lấy Tên Khách Hàng

## Vấn đề

Tên khách hàng (`appointment.customer`) không hiển thị đúng trong bảng lịch hẹn.

## Cách Debug

### 1. Mở DevTools

- Nhấn `F12` để mở Developer Tools
- Vào tab **Console**

### 2. Xem dữ liệu từ API

Dữ liệu debug sẽ hiển thị trong Console:

```
Customer data structure: { id: 1, ngay_gio: "...", khach_hang: {...}, ... }
khach_hang object: { id: 1, ho_ten: "Nguyễn Văn A", ... } // hoặc { name: "...", ... }
thu_cung object: { id: 1, ten_thu_cung: "Milo", ... } // hoặc { name: "...", ... }
```

### 3. Có thể các trường là:

- `khach_hang.ho_ten` ✅ (nếu dùng Tiếng Việt)
- `khach_hang.name` (nếu dùng English)
- `khach_hang.customer_name`
- Hoặc bất kỳ field nào khác

## Các trường cần kiểm tra

| Loại           | Trường Backend                               | Trường Hiển Thị |
| -------------- | -------------------------------------------- | --------------- |
| **Khách hàng** | `khach_hang.ho_ten` hoặc `khach_hang.name`   | Tên khách hàng  |
| **Thú cưng**   | `thu_cung.ten_thu_cung` hoặc `thu_cung.name` | Tên thú cưng    |
| **Dịch vụ**    | `dich_vu.ten` hoặc `dich_vu.name`            | Tên dịch vụ     |
| **Nhân viên**  | `nhan_vien.ho_ten` hoặc `nhan_vien.name`     | Tên nhân viên   |

## Giải pháp

Sau khi xem console, báo cáo lại cấu trúc chính xác và tôi sẽ sửa `transformAppointment()` function.

### Ví dụ Response từ Backend

**Trường hợp 1 - Tiếng Việt:**

```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "ngay_gio": "2025-11-20 09:00:00",
      "khach_hang": {
        "id": 1,
        "ho_ten": "Nguyễn Văn A"
      },
      "thu_cung": {
        "id": 1,
        "ten_thu_cung": "Milo"
      },
      "dich_vu": {
        "id": 1,
        "ten": "Khám tổng quát"
      },
      "nhan_vien": {
        "id": 1,
        "ho_ten": "BS. Nguyễn Văn B",
        "chuc_vu": "Bác sĩ"
      },
      "trang_thai": "confirmed"
    }
  ]
}
```

**Trường hợp 2 - English (nếu khác):**

```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "ngay_gio": "2025-11-20 09:00:00",
      "khach_hang": {
        "id": 1,
        "name": "Nguyễn Văn A"
      },
      "thu_cung": {
        "id": 1,
        "name": "Milo"
      },
      "dich_vu": {
        "id": 1,
        "name": "Khám tổng quát"
      }
    }
  ]
}
```

## Hành động tiếp theo

1. Mở F12 Console
2. Chụp ảnh output debug
3. Báo cáo cấu trúc thực từ API
4. Tôi sẽ sửa code tương ứng
