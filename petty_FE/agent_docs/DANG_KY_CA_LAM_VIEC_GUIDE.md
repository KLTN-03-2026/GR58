# 🚀 Hướng dẫn Chức năng Gửi Đăng ký Ca cho Bác sĩ

## 📋 Tổng quan

Đã tạo chức năng cho phép bác sĩ (nhân viên) tự động gửi đăng ký ca làm việc qua giao diện web.

---

## ✨ Tính năng chính

### 1. **Tab "Đăng ký ca trực"**

- Cho phép bác sĩ chọn các ca làm việc từ danh sách có sẵn
- Hiển thị tần suất, số ca đã đăng ký, và yêu cầu tối thiểu

### 2. **Modal Đăng ký Ca**

Modal popup hiển thị:

- **Header**: Tiêu đề "Đăng ký ca làm việc" với nút đóng
- **Danh sách ca trống**: Các ca chưa đủ bác sĩ
  - Ngày (T2, T3, ... CN)
  - Ca làm việc (☀️ Sáng, 🌤️ Chiều, 🌙 Tối)
  - Phòng khám
  - Giờ làm
  - Checkbox để chọn ca
- **Ghi chú tùy chọn**: Text area để bác sĩ nhập ghi chú
- **Nút hành động**:
  - ❌ Hủy: Đóng modal mà không gửi
  - ✅ Gửi đăng ký: Gửi yêu cầu

### 3. **Status Hiển thị**

- 🟢 Chờ xác nhận: Ca vừa đăng ký
- 🟡 Đã duyệt: Ca đã được admin phê duyệt
- 🔴 Từ chối: Ca bị admin từ chối

---

## 🎯 Quy trình sử dụng

### Bước 1: Mở Tab Đăng ký

```
1. Click vào tab "Đăng ký ca trực"
2. Xem các ca chưa đủ bác sĩ
3. Nhập ghi chú (nếu cần)
4. Click nút "🚀 Gửi đăng ký"
```

### Bước 2: Chọn Ca

```
1. Modal sẽ hiện lên
2. Xem danh sách ca trống
3. Bấm vào ca để chọn
4. Dấu ✓ sẽ hiện ra khi chọn
```

### Bước 3: Gửi Đăng ký

```
1. Điền ghi chú (nếu cần)
2. Bấm nút "Gửi đăng ký"
3. Chờ xác nhận (dấu tải sẽ hiện)
4. Nhận thông báo thành công
```

---

## 🔧 API Endpoints

### Fetch Available Shifts

```http
GET /lich-lam-viec
Params:
  - tu_ngay: YYYY-MM-DD
  - den_ngay: YYYY-MM-DD
  - per_page: 50
```

### Submit Registration

```http
POST /lich-dang-ky/dang-ky-nhan-vien
Body:
{
  "ngay_gio": "2025-12-12 06:00:00",
  "lich_lam_viec_id": 123,
  "ghi_chu": "Ghi chú tùy chọn"
}
```

### Response (Success)

```json
{
  "success": true,
  "message": "Đăng ký lịch làm việc thành công",
  "data": {
    "id": 1,
    "nhan_vien_id": 5,
    "ngay_gio": "2025-12-12 06:00:00",
    "trang_thai": "da_xac_nhan",
    "ghi_chu": "...",
    "nhan_vien": { ... },
    "lich_lam_viec": { ... }
  }
}
```

---

## 📁 File Structure

```
src/components/Doctor/LichLamViec/
├── index.vue (Main component - updated)
│   ├── Imports DangKyCa component
│   ├── showRegisterModal state
│   ├── Button "🚀 Gửi đăng ký"
│   └── Modal integration
└── DangKyCa/
    └── index.vue (New - Registration modal)
        ├── fetchAvailableShifts()
        ├── selectShift()
        └── submitRegistration()
```

---

## 🎨 Design Details

### Color Scheme

- **Teal/Cyan**: Nút chính (Primary action)
- **Emerald**: Ca Sáng (Morning)
- **Amber**: Ca Chiều (Afternoon)
- **Slate**: Ca Tối (Night)
- **Blue**: Form/Info
- **Green**: Success

### UI Components

- Modal popup với animation slide-up
- Shift cards clickable với highlight khi chọn
- Textarea cho ghi chú
- Loading state (dấu tải trên button)
- Toast notifications (success/error)

### Responsive Design

- Responsive trên mobile, tablet, desktop
- Max width 2xl trên modal
- Padding/spacing phù hợp

---

## ✅ Features Implemented

- ✓ Modal component tách biệt (`DangKyCa/index.vue`)
- ✓ Fetch available shifts từ backend
- ✓ Select shift với UI feedback
- ✓ Optional notes field
- ✓ Submit registration qua API
- ✓ Loading state & error handling
- ✓ Success/error toast notifications
- ✓ Modal auto-close sau khi submit
- ✓ Refresh schedule data sau submit
- ✓ Responsive design
- ✓ Beautiful animations

---

## 🚀 Cách hoạt động

### 1. User bấm "🚀 Gửi đăng ký"

```javascript
showRegisterModal = true;
```

### 2. Modal mở và fetch ca trống

```javascript
fetchAvailableShifts();
// GET /lich-lam-viec?tu_ngay=...&den_ngay=...
```

### 3. User chọn ca và điền ghi chú

```javascript
selectShift(shift);
formData.ghi_chu = "...";
```

### 4. User bấm "Gửi đăng ký"

```javascript
submitRegistration();
// POST /lich-dang-ky/dang-ky-nhan-vien
```

### 5. Backend tự động xác nhận

```php
$data['trang_thai'] = 'da_xac_nhan'; // Auto confirm
```

### 6. Modal đóng, schedule refresh

```javascript
emit("success");
fetchScheduleData();
```

---

## 🔐 Validation

**Frontend:**

- Bắt buộc chọn ca
- Ghi chú là tùy chọn (nullable)
- Loading state trên button

**Backend:**

- Kiểm tra user là nhân viên
- Validate lich_lam_viec_id tồn tại
- Validate ngay_gio format
- Tự động set nhan_vien_id = current user
- Tự động set trang_thai = 'da_xac_nhan'

---

## 📱 Giao diện

### Modal Header

```
┌─────────────────────────────────┐
│ 🔷 Đăng ký ca làm việc       ✕ │
│    Chọn ca làm việc bạn muốn │
│    đăng ký                   │
└─────────────────────────────────┘
```

### Shift Cards

```
┌─────────────────────────────────┐
│ T2, 10/12                    ✓ │
│ ☀️ Sáng (06:00-12:00)        │
│ 📍 Phòng khám 1              │
│ ⏱️ 8 giờ                      │
└─────────────────────────────────┘
```

### Footer Buttons

```
┌──────────────────────────────────┐
│ [Hủy]  [Gửi đăng ký] →         │
└──────────────────────────────────┘
```

---

## 🐛 Troubleshooting

### Modal không hiện

- Kiểm tra `showRegisterModal = true`
- Kiểm tra props `isOpen`

### Shifts không load

- Kiểm tra API `/lich-lam-viec`
- Kiểm tra query params date format
- Xem console error

### Submit fail

- Kiểm tra API `/lich-dang-ky/dang-ky-nhan-vien`
- Kiểm tra auth token
- Xem response error message

---

## 🔄 Next Steps

1. **Test API Integration**: Đảm bảo backend endpoints hoạt động
2. **Test UI/UX**: Kiểm tra trên các devices khác nhau
3. **Test Validation**: Test các edge cases
4. **Test Notifications**: Verify toast messages
5. **Test Permissions**: Verify middleware 'staff.only' hoạt động

Enjoy! 🎉
