# ✨ Hướng dẫn Giao diện Xác nhận Đăng ký Ca

## 📋 Tổng quan

Tôi đã thêm một tab mới "Xác nhận đăng ký" vào giao diện **Quản lý Lịch Làm Việc** với giao diện đẹp, hiện đại và dễ sử dụng.

## 🎯 Các tính năng chính

### 1. **Tab Navigation**

- **Lịch làm việc**: Tab chính để quản lý lịch ca làm việc (lịch cũ)
- **Xác nhận đăng ký**: Tab mới để xem và xác nhận yêu cầu đăng ký ca từ nhân viên

### 2. **Giao diện Danh sách Xác nhận**

#### Filter Section

- **Trạng thái**: Lọc theo "Tất cả", "Chờ xác nhận", "Đã xác nhận", "Từ chối"
- **Tìm nhân viên**: Tìm kiếm theo tên nhân viên

#### Cards Hiển thị (Grid Layout)

Mỗi card hiển thị thông tin chi tiết:

- 👤 **Avatar & Tên nhân viên**
- 🎯 **Trạng thái** (Badge màu)
- 📅 **Ngày đăng ký**
- ⏱️ **Ca làm việc** (Sáng/Chiều/Tối)
- 🏥 **Phòng làm việc**
- ⏳ **Giờ làm dự kiến**
- 💬 **Ghi chú** (nếu có)

#### Action Buttons (Cho trạng thái "Chờ xác nhận")

- ✓ **Xác nhận**: Nút xanh
- ✕ **Từ chối**: Nút đỏ

### 3. **Modal Chi tiết**

Khi click vào card, một modal sẽ hiện lên với:

#### Header

- Tiêu đề: "Chi tiết yêu cầu"
- Nút đóng (X)

#### Content

- **Thông tin nhân viên**: Avatar, Tên, Chức vụ, ID
- **Trạng thái**: Badge hiển thị trạng thái hiện tại
- **Thông tin ca làm việc**:
  - 📅 Ngày (kèm gradient background)
  - ⏱️ Ca + Giờ cụ thể (có icon ☀️/🌤️/🌙)
  - 🏥 Phòng làm việc
  - ⏳ Giờ làm dự kiến
- **💬 Ghi chú**: Hiển thị nếu có
- **📤 Thời gian gửi**: Ngày yêu cầu được gửi

#### Footer Actions

- **Hủy**: Đóng modal mà không thay đổi
- **Từ chối** (Nút đỏ): Từ chối yêu cầu
- **Xác nhận** (Nút xanh): Xác nhận yêu cầu

---

## 🎨 Design Details

### Color Scheme

- **Teal/Cyan**: Màu chính (Primary)
- **Emerald**: Xác nhận (Success)
- **Amber**: Chờ xác nhận (Pending)
- **Red**: Từ chối (Reject)
- **Blue**: Ghi chú (Info)
- **Purple**: Phòng làm việc (Special)

### Typography & Spacing

- Font **Nunito** cho headers
- Spacing đều với Tailwind Grid System
- Responsive: Mobile, Tablet, Desktop

### Animations

- Hover effects trên cards
- Slide-up animation cho modal
- Smooth transitions

---

## 🔧 Cách sử dụng

### Bước 1: Chuyển sang tab

```
Click nút "Xác nhận đăng ký" ở phía trên
```

### Bước 2: Lọc & Tìm kiếm

```
- Chọn trạng thái từ dropdown
- Nhập tên nhân viên để tìm kiếm
```

### Bước 3: Xem chi tiết

```
Click vào card để mở modal chi tiết
```

### Bước 4: Xác nhận hoặc Từ chối

```
Option A: Click button trên card (chỉ khi chờ xác nhận)
Option B: Mở modal → Click button ở footer
```

---

## 📡 API Endpoints

### Fetch Confirmations

```
GET /dang-ky-ca
Parameters: per_page=500
```

### Update Status

```
PUT /dang-ky-ca/{id}
Body: { trang_thai: "confirmed" | "rejected" | "pending" }
```

### Expected Response Format

```json
{
  "data": {
    "data": [
      {
        "id": 1,
        "nhan_vien_id": 5,
        "nhan_vien": {
          "id": 5,
          "ho_ten": "Nguyễn Văn A",
          "chuc_vu": "Bác sĩ",
          "avatar": "https://..."
        },
        "ngay_dang_ky": "2025-12-11",
        "thoi_gian_truc": "ca_sang",
        "phong_truc": "Phòng khám 1",
        "ghi_chu": "Ghi chú của nhân viên",
        "trang_thai": "pending",
        "created_at": "2025-12-11T10:30:00",
        "ngay_gio": "2025-12-12 06:00:00"
      }
    ]
  }
}
```

---

## 📁 File Structure

```
src/components/Admin/NhanSu/QuanLyLichLamViec/
├── index.vue (Main component - updated)
├── PhanCaTruc/
│   └── index.vue
└── XacNhanDangKy/
    └── index.vue (New - Modal chi tiết)
```

---

## ✅ Checklist

- ✓ Tab navigation
- ✓ Filter & Search
- ✓ Card grid layout
- ✓ Status badges
- ✓ Modal chi tiết
- ✓ Approve/Reject actions
- ✓ API integration
- ✓ Toast notifications
- ✓ Responsive design
- ✓ Beautiful animations

---

## 🚀 Next Steps

1. **Backend Integration**: Đảm bảo API endpoint `/dang-ky-ca` có sẵn
2. **Data Mapping**: Verify data từ backend khớp với format mong đợi
3. **Testing**: Test các flow xác nhận/từ chối
4. **Customization**: Adjust colors/spacing nếu cần

---

## 💡 Tips

- Modal có animation slide-up mượt mà
- Cards clickable cho UX tốt hơn
- Status badges dễ nhận biết bằng màu sắc
- Loading states được xử lý (disabled buttons khi đang xử lý)

Enjoy! 🎉
