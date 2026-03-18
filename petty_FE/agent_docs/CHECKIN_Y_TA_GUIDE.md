# 🏥 Hướng dẫn Check-in Y tá - Hiển thị Bác sĩ được phân công

## 📋 Tổng quan

Tính năng Check-in dành cho Y tá với khả năng **hiển thị thông tin bác sĩ đã được phân công** cho mỗi lịch hẹn.

## ✨ Tính năng đã triển khai

### 1. **Service Layer** (`lichHenService.js`)

```javascript
// API Endpoints
-checkInAppointment(id) - // Check-in lịch hẹn
  getAppointmentsWaitingCheckIn() - // Lấy DS chờ check-in
  getCheckedInAppointments(); // Lấy DS đã check-in
```

### 2. **Component Y tá** (`Nurse/LichHen/index.vue`)

#### **3 Tabs chính:**

1. **Danh sách lịch hẹn** - Tab hiện tại (giữ nguyên)
2. **🆕 Chờ Check-in** - Lịch hẹn đã xác nhận, chờ khách đến
3. **🆕 Đã Check-in** - Lịch hẹn đã check-in hôm nay
4. **Lịch Nhắc Hẹn** - Tab reminder (giữ nguyên)

#### **Tab "Chờ Check-in" - Hiển thị:**

```
┌─────┬──────────────┬────────────┬──────────┬─────────┬────────────────┬──────────┬─────────┐
│ STT │ Thời gian hẹn│ Khách hàng │ Thú cưng │ Dịch vụ │ Bác sĩ phụ trác│ Trạng thái│ Thao tác│
├─────┼──────────────┼────────────┼──────────┼─────────┼────────────────┼──────────┼─────────┤
│  1  │ 31/12/2025   │ Nguyễn V.A │ Quốc     │ Khám    │ [Avatar] Bác sĩ│ Đã xác   │ Check-in│
│     │ 18:30        │ Bác Sĩ     │          │ nha khoa│ Nguyễn Văn An  │ nhận     │ [Nút]   │
│     │              │            │          │         │ (Bác Sĩ)       │          │         │
└─────┴──────────────┴────────────┴──────────┴─────────┴────────────────┴──────────┴─────────┘
```

**Cột "Bác sĩ phụ trách" hiển thị:**

- ✅ **Có bác sĩ**: Avatar + Tên + Vai trò
  - Avatar tròn màu xanh với chữ cái đầu
  - Tên đầy đủ (full_name)
  - Vai trò (vai_tro) - ví dụ: "Bác Sĩ"
- ⚠️ **Chưa phân công**:
  - Hiển thị chữ nghiêng "Chưa phân công" màu xám

#### **Tab "Đã Check-in" - Hiển thị:**

```
┌─────┬─────────────────┬────────────┬──────────┬─────────┬────────────────┬──────────┐
│ STT │ Thời gian CI    │ Khách hàng │ Thú cưng │ Dịch vụ │ Bác sĩ phụ trác│ Trạng thái│
├─────┼─────────────────┼────────────┼──────────┼─────────┼────────────────┼──────────┤
│  1  │ ✓ 18:35 (CI)    │ Nguyễn V.A │ Quốc     │ Khám    │ BS Nguyễn V.An │ Đang khám│
│     │ Hẹn: 18:30      │            │          │         │                │          │
└─────┴─────────────────┴────────────┴──────────┴─────────┴────────────────┴──────────┘
```

### 3. **Check-in Modal** (`CheckInModal.vue`)

Modal hiển thị đầy đủ thông tin trước khi check-in:

```
┌────────────────────────────────────────┐
│ 🏥 Xác nhận Check-in                   │
├────────────────────────────────────────┤
│                                        │
│ 👤 Khách hàng: Nguyễn Văn An          │
│ 🐾 Thú cưng: Quốc                     │
│ 📋 Dịch vụ: Khám nha khoa             │
│ ⏰ Thời gian hẹn: 31/12/2025 18:30    │
│ ─────────────────────────────────────  │
│ 👨‍⚕️ Bác sĩ phụ trách:                 │
│    [N] Nguyễn Văn An                  │
│        Bác Sĩ                         │
│                                        │
│ ⚠️  Sau khi check-in, trạng thái      │
│     chuyển sang "Đang khám"           │
│                                        │
│ 🕐 Thời gian check-in:                │
│    12/12/2025, 18:35:42              │
│                                        │
│         [Hủy]  [✓ Xác nhận Check-in]  │
└────────────────────────────────────────┘
```

**Nếu chưa phân công bác sĩ:**

```
│ ⚠️  Bác sĩ phụ trách:                 │
│     Chưa được phân công bác sĩ        │
```

## 🔄 Luồng hoạt động

### **1. Y tá vào tab "Chờ Check-in"**

```javascript
// Tự động gọi API
GET /lich-hen-cho-check-in?per_page=100

// Response
{
  "status": true,
  "data": [
    {
      "id": 1,
      "ngay_gio": "2025-12-31 18:30:00",
      "khach_hang": "Nguyễn Văn An",
      "thu_cung": { "ten_thu_cung": "Quốc" },
      "dich_vu": { "ten": "Khám nha khoa" },
      "nhan_vien": {
        "id": 5,
        "full_name": "Nguyễn Văn An",
        "vai_tro": "Bác Sĩ"
      },
      "trang_thai": "confirmed",
      "thoi_gian_checkin": null
    }
  ]
}
```

### **2. Y tá click "Check-in"**

```javascript
// Mở modal với thông tin đầy đủ
- Hiển thị avatar bác sĩ
- Hiển thị tên bác sĩ (nhan_vien.full_name)
- Hiển thị vai trò (nhan_vien.vai_tro)
```

### **3. Y tá xác nhận Check-in**

```javascript
// Gọi API
POST /lich-hen/{id}/check-in

// Backend xử lý:
1. Kiểm tra vai trò Y tá
2. Ghi nhận thời gian check-in
3. Chuyển trạng thái: confirmed → in-progress
4. Gán nhan_vien_id (nếu chưa có)

// Response
{
  "status": true,
  "message": "Check-in lịch hẹn thành công",
  "data": {
    "id": 1,
    "trang_thai": "in-progress",
    "thoi_gian_checkin": "2025-12-12 18:35:42",
    "nhan_vien": {...}
  },
  "checked_in_by": {
    "id": 3,
    "full_name": "Y tá Lan",
    "vai_tro": "y_ta"
  }
}
```

### **4. Tự động cập nhật UI**

```javascript
// Xóa khỏi tab "Chờ Check-in"
// Thêm vào tab "Đã Check-in"
// Hiển thị toast thành công
```

## 🎨 Thiết kế UI

### **Avatar Bác sĩ**

```vue
<div class="w-8 h-8 rounded-full bg-[#009689] flex items-center justify-center">
  <span class="font-nunito font-semibold text-xs text-white">
    {{ nhan_vien.full_name?.charAt(0) || 'N' }}
  </span>
</div>
```

### **Badge Trạng thái**

- 🟢 **Đã xác nhận**: `bg-green-100 text-green-800`
- 🟡 **Chờ xác nhận**: `bg-yellow-100 text-yellow-800`
- 🔵 **Đang khám**: `bg-blue-100 text-blue-800`
- ✅ **Hoàn thành**: `bg-green-100 text-green-800`

## 📊 Dữ liệu Backend

### **Backend API trả về (LichHenController)**

```php
// Relationship được load
$lichHen->load([
  'khachHang',   // Tên khách hàng
  'thuCung',     // Thông tin thú cưng
  'dichVu',      // Dịch vụ
  'nhanVien',    // ⭐ Bác sĩ phụ trách
  'thanhToan'
]);

// Transform data
$data = [
  'id' => 1,
  'khach_hang' => 'Nguyễn Văn An', // Thay vì khach_hang_id
  'thu_cung' => [...],
  'dich_vu' => [...],
  'nhan_vien' => [  // ⭐ Thông tin bác sĩ
    'id' => 5,
    'full_name' => 'Bác sĩ Nguyễn Văn An',
    'vai_tro' => 'Bác Sĩ',
    'email' => 'doctor@example.com'
  ],
  'ngay_gio' => '2025-12-31 18:30:00',
  'trang_thai' => 'confirmed',
  'thoi_gian_checkin' => null
];
```

## 🔍 Debugging

### **Console logs được thêm:**

```javascript
// Khi load dữ liệu
✅ Loaded waiting check-in appointments: 5
✅ Loaded checked-in appointments: 3

// Khi check-in thành công
✅ Check-in successful: {id: 1, trang_thai: 'in-progress', ...}
```

### **Kiểm tra API Response:**

```javascript
// Trong browser console
fetch("/api/lich-hen-cho-check-in", {
  headers: {
    Authorization: "Bearer " + token,
  },
})
  .then((r) => r.json())
  .then(console.log);

// Kiểm tra nhan_vien có tồn tại không
console.log(response.data[0].nhan_vien);
// Expected: {id: 5, full_name: "...", vai_tro: "..."}
```

## 📱 Responsive Design

- **Desktop**: Hiển thị đầy đủ avatar + tên + vai trò
- **Tablet**: Ẩn avatar, chỉ hiển thị tên
- **Mobile**: Chỉ hiển thị tên viết tắt

## ⚙️ Configuration

### **Số lượng items mỗi trang:**

```javascript
// index.vue
per_page: 100; // Load nhiều để tránh phân trang
```

### **Auto-refresh:**

```javascript
// Có thể thêm setInterval để tự động làm mới
setInterval(() => {
  if (activeTab.value === "check-in") {
    fetchWaitingCheckInList();
  }
}, 30000); // 30 giây
```

## 🚨 Xử lý Lỗi

### **Nếu API trả về lỗi 403:**

```
"Chỉ nhân viên có vai trò Y tá mới có thể thực hiện check-in"
→ Kiểm tra vai_tro của user đăng nhập
```

### **Nếu không có bác sĩ (nhan_vien = null):**

```
→ Hiển thị "Chưa phân công" thay vì lỗi
→ Vẫn cho phép check-in
```

### **Nếu đã check-in rồi:**

```
"Lịch hẹn đã được check-in lúc: 12/12/2025 18:35:42"
→ Backend trả về thông báo + thời gian check-in
```

## 📝 Testing Checklist

- [ ] Tab "Chờ Check-in" hiển thị danh sách
- [ ] Cột "Bác sĩ phụ trách" hiển thị đúng
  - [ ] Avatar + Tên (có bác sĩ)
  - [ ] "Chưa phân công" (không có bác sĩ)
- [ ] Modal check-in hiển thị thông tin bác sĩ
- [ ] Click "Xác nhận Check-in" thành công
- [ ] Chuyển từ tab "Chờ" sang tab "Đã Check-in"
- [ ] Tab "Đã Check-in" hiển thị thời gian check-in
- [ ] Nút "Làm mới" hoạt động
- [ ] Badge số lượng hiển thị đúng

## 🎯 Kết quả

✅ **Y tá có thể:**

1. Xem danh sách lịch hẹn chờ check-in
2. **Biết được bác sĩ nào sẽ khám** (từ cột "Bác sĩ phụ trách")
3. Check-in nhanh chóng với 1 click
4. Xem lịch sử check-in trong ngày
5. Biết lịch nào chưa phân công bác sĩ

✅ **Bác sĩ được hiển thị:**

- Avatar với chữ cái đầu tên
- Tên đầy đủ (full_name)
- Vai trò (Bác Sĩ, Y tá, etc.)

---

**Tác giả:** GitHub Copilot  
**Ngày:** 12/12/2025  
**Version:** 1.0.0
