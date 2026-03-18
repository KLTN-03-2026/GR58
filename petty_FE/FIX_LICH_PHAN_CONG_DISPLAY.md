# Fix Hiển Thị Lịch Phân Công Admin

## 🎯 Mục đích

Fix lỗi không hiển thị lịch phân công (lịch đăng ký được duyệt) mà chỉ hiển thị lịch đăng ký chờ duyệt.

## 📋 Vấn đề

- Component chỉ có 3 ca làm việc trong lịch (Sáng, Chiều, Tối)
- Lịch phân công từ admin (approved shifts) không có slot để hiển thị
- Lịch phân công bị bỏ qua hoặc không hiển thị trên giao diện

## ✅ Giải pháp

### 1. Thêm slot mới cho lịch phân công

**File**: `src/components/Doctor/LichLamViec/index.vue`

**Dòng 1146-1163**: Khởi tạo `timeSlots` với 4 ca (thêm ca "Phân công")

```javascript
const timeSlots = ref([
  {
    name: "Sáng",
    time: "8h-12h",
    schedule: [null, null, null, null, null, null, null],
  },
  {
    name: "Chiều",
    time: "13h-17h",
    schedule: [null, null, null, null, null, null, null],
  },
  {
    name: "Tối",
    time: "18h-21h",
    schedule: [null, null, null, null, null, null, null],
  },
  {
    name: "Phân công", // ⭐ MỚI: Slot cho lịch phân công
    time: "Các giờ khác",
    schedule: [null, null, null, null, null, null, null],
  },
]);
```

### 2. Cập nhật logic fetch lịch phân công

**Dòng 800-900** (hàm `fetchScheduleData`):

- Label thay đổi từ "Đăng ký" → "Phân công" để phân biệt rõ
- Logic xử lý slot 4 (index 3) đã được cập nhật để:
  - Tự động tạo slot nếu chưa tồn tại
  - Hỗ trợ nhiều lịch phân công trong cùng một ngày (sử dụng array)
  - Đặt status là "upcoming" cho tất cả phân công

## 📊 Kết quả

### Trước sửa:

- Lịch 1: Sáng (8h-12h)
- Lịch 2: Chiều (13h-17h)
- Lịch 3: Tối (18h-21h)
- ❌ Lịch phân công: KHÔNG HIỂN THỊ

### Sau sửa:

- Lịch 1: Sáng (8h-12h)
- Lịch 2: Chiều (13h-17h)
- Lịch 3: Tối (18h-21h)
- ✅ Lịch 4: Phân công (Các giờ khác) - **HIỂN THỊ ĐỦ CẢ 2 LOẠI LỊCH**

## 🔄 Cách hoạt động

1. Khi load tab "Lịch làm việc":

   - Fetch lịch chính thức từ endpoint `/lich-lam-viec`
   - Fetch lịch đã đăng ký/phân công từ endpoint `/lich-dang-ky`

2. Xử lý dữ liệu:

   - Các ca Sáng/Chiều/Tối được hiển thị ở 3 slot đầu
   - Các lịch phân công đã duyệt được hiển thị ở slot "Phân công"

3. Hiển thị:
   - Tất cả 4 ca đều được render trên bảng lịch tuần
   - Có thể phân biệt bằng tên ca: "Phân công HH:mm" vs "Sáng/Chiều/Tối"

## 🧪 Testing

**Kiểm tra hiển thị**:

1. Đăng nhập vào tài khoản bác sĩ
2. Vào tab "Lịch làm việc"
3. Xem tất cả 4 hàng lịch:
   - ✅ Hàng "Sáng" - Lịch chính thức
   - ✅ Hàng "Chiều" - Lịch chính thức
   - ✅ Hàng "Tối" - Lịch chính thức
   - ✅ Hàng "Phân công" - Lịch phân công từ admin

**Kiểm tra dữ liệu**:

1. Mở DevTools Console (F12)
2. Xem log "Fetched registered shifts" để verify dữ liệu được fetch
3. Xem structure của `timeSlots` để confirm slot 4 tồn tại

## 🐛 Debug nếu vẫn không hiển thị

Nếu lịch phân công vẫn không hiển thị, kiểm tra:

1. **Endpoint `/lich-dang-ky` có trả dữ liệu không?**

   ```javascript
   // DevTools Console:
   fetch("/api/lich-dang-ky?per_page=500")
     .then((r) => r.json())
     .then((d) => console.log(d));
   ```

2. **Dữ liệu có status "da_xac_nhan" không?**

   - Chỉ các lịch có `trang_thai === 'da_xac_nhan'` mới hiển thị

3. **Ngày tháng có trong khoảng tuần không?**
   - Kiểm tra `ngay_gio` của lịch phân công

## 📝 Ghi chú

- Không sửa phần "Đăng ký ca" tab - chỉ sửa tab "Lịch làm việc"
- Hỗ trợ multiple phân công trong cùng ngày bằng cách tạo array
- Tất cả phân công hiển thị với status "upcoming" (không có past/ongoing)
