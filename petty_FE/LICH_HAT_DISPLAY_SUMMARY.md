# ✅ Hiển thị Lịch Hẹn trong Phân ca Làm Việc

## 📝 Tóm tắt thay đổi

### Component: `QuanLyLichLamViec/index.vue`

#### 1️⃣ **State**

```javascript
const appointments = ref({}); // lich_hen (lịch hẹn khách hàng)
```

#### 2️⃣ **Helper Function**

```javascript
const getAppointmentsForCell = (staff, dateStr) =>
  (appointments.value[staff.id] || {})[dateStr] || [];
```

#### 3️⃣ **Fetch Function**

```javascript
const fetchAppointmentsForWeek = async () => {
  // Fetch từ /lich-hen
  // Transform data để lấy:
  //   - customer: tên khách hàng
  //   - service: tên dịch vụ
  //   - pet: tên thú cưng
  //   - time: thời gian
  //   - status: trạng thái
  // Nhóm theo nhan_vien_id (bác sĩ) + ngày
};
```

#### 4️⃣ **Template - Hiển thị Lịch Hẹn**

```vue
<!-- Appointments (Lịch hẹn) -->
<div v-if="getAppointmentsForCell(staff, day.dateStr).length" class="space-y-2 p-3">
  <div
    v-for="appointment in getAppointmentsForCell(staff, day.dateStr)"
    :key="appointment.id"
    class="bg-blue-50 rounded-xl shadow-sm border-2 border-blue-400 p-3 flex flex-col"
  >
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <!-- Calendar icon -->
      </div>
      <span class="text-xs px-2.5 py-1 rounded-full text-white font-bold bg-blue-600">
        Lịch hẹn
      </span>
    </div>
    <div class="text-xs text-gray-600 mt-1 truncate">
      {{ appointment.customer }} - {{ appointment.service }}
    </div>
  </div>
</div>
```

## 🎨 Giao Diện

### Hai loại card được hiển thị:

#### 1. **Ca Làm Việc (trắng)**

- Border: Xanh lá (Sáng) / Vàng (Chiều) / Xám (Tối)
- Hiển thị: Phòng trực + loại ca

#### 2. **Lịch Hẹn (xanh dương)**

- Border: Xanh dương
- Hiển thị: Tên khách hàng + tên dịch vụ

## 🔍 Dữ liệu Hiển Thị

Mỗi lịch hẹn hiển thị:

- ✅ **Tên khách hàng** (`appointment.customer`)
- ✅ **Tên dịch vụ** (`appointment.service`)
- ✅ **Tên thú cưng** (`appointment.pet`)
- ✅ **Thời gian** (`appointment.time`)
- ✅ **Trạng thái** (`appointment.status`)

## 🔄 Luồng Dữ liệu

```
API GET /lich-hen
    ↓
API Response: { id, ngay_gio, nhan_vien_id, khach_hang, dich_vu, thu_cung, trang_thai }
    ↓
Transform: Lấy tên từ relationships
    ↓
Group by: nhan_vien_id + date
    ↓
Template: Hiển thị appointment.customer + appointment.service
```

## 📱 Điều Kiện Hiển Thị

Lịch hẹn sẽ hiển thị nếu:

1. ✅ Bác sĩ (`nhan_vien_id`) được gán
2. ✅ Lịch hẹn nằm trong tuần hiện tại
3. ✅ Backend trả về relationship `khach_hang` và `dich_vu`

## ⚠️ Lưu Ý

Nếu không thấy lịch hẹn hiển thị, kiểm tra:

1. Xem console (F12) → Network → `/lich-hen`
2. Kiểm tra response: có `nhan_vien_id` không?
3. Kiểm tra: có `khach_hang` object hoặc string không?
4. Kiểm tra: có `dich_vu` object không?

## 🧪 Debug

Mở DevTools (F12) → Console → xem logs:

- `📅 All appointments from API:` - Tất cả lịch hẹn từ backend
- `✓ Appointment:` - Mỗi lịch hẹn được transform
- `📍 Grouped appointments by doctor:` - Dữ liệu cuối cùng được hiển thị
