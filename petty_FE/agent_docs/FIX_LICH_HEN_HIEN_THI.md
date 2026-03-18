# 🔧 FIX: Hiển thị Lịch Hẹn Khi Không Có Ca Làm Việc

## 🎯 Vấn đề

- **Triệu chứng:** Ngày 31/12/2025 có lịch hẹn khách hàng nhưng không hiển thị trên calendar
- **Nguyên nhân:** Logic chỉ xử lý và hiển thị khi có `lich_lam_viec` (ca được admin phân công)
- **Kết quả:** Lịch hẹn bị bỏ qua nếu bác sĩ không được phân công ca chính thức

## ✅ Giải pháp

### Thay đổi logic trong `updateCalendarData()`

**TRƯỚC:**

```javascript
if (shifts.length === 0) {
  console.log(`⚠️ No shifts assigned for ${daySchedule.date}`);
  // Không làm gì cả → Lịch hẹn bị mất!
}
```

**SAU:**

```javascript
// ✅ Kiểm tra lịch hẹn
const appointments = daySchedule.lich_hen || [];
const hasAppointments = appointments.length > 0;

if (shifts.length === 0) {
  console.log(`⚠️ No shifts assigned for ${daySchedule.date}`);

  // ✅ Nếu không có ca làm việc nhưng CÓ lịch hẹn → Tạo ca "Lịch hẹn"
  if (hasAppointments) {
    console.log(`📅 Found ${appointments.length} appointment(s) without shift`);

    // Tạo một "pseudo shift" để hiển thị lịch hẹn
    const firstAppointment = appointments[0];
    const appointmentTime = new Date(firstAppointment.ngay_gio);
    const hour = appointmentTime.getHours();

    // Xác định ca dựa trên giờ hẹn
    let slotIndex = 0; // Mặc định ca sáng
    let shiftType = "ca_sang";

    if (hour >= 7 && hour < 12) {
      slotIndex = 0; // Ca sáng (7h-12h)
      shiftType = "ca_sang";
    } else if (hour >= 13 && hour < 18) {
      slotIndex = 1; // Ca chiều (13h-18h)
      shiftType = "ca_chieu";
    } else if (hour >= 18 && hour < 22) {
      slotIndex = 2; // Ca tối (18h-22h)
      shiftType = "ca_toi";
    }

    const shiftData = {
      id: `appointment-${daySchedule.date}`,
      room: `Lịch hẹn (${appointments.length} khách)`,
      patients: appointments.length,
      status: getShiftStatus(daySchedule.date, shiftType),
      appointments: appointments,
      date: daySchedule.date,
      shift: shiftType,
      isAppointmentOnly: true, // Đánh dấu đây là lịch hẹn không có ca
    };

    timeSlots.value[slotIndex].schedule[dayIndex] = shiftData;
  }
}
```

## 📊 Kịch bản hoạt động

### Kịch bản 1: Có ca làm việc + Có lịch hẹn ✅

```
Admin phân công: Ca sáng (7h-12h)
Khách hàng đặt: Lịch hẹn 9h
→ Hiển thị: Ca sáng với 1 lịch hẹn
```

### Kịch bản 2: Không có ca làm việc + Có lịch hẹn ✅ (FIX MỚI)

```
Admin không phân công ca
Khách hàng đặt: Lịch hẹn 14h
→ Hiển thị: "Lịch hẹn (1 khách)" ở ca chiều
```

### Kịch bản 3: Không có ca làm việc + Không có lịch hẹn ✅

```
Admin không phân công ca
Không có lịch hẹn
→ Hiển thị: Ô trống (bình thường)
```

## 🎨 Hiển thị trên UI

```
┌─────────────────────────────────────┐
│  Thứ 2   Thứ 3   ...   CN (31/12)  │
├─────────────────────────────────────┤
│ Ca sáng                             │
│   -        -      ...      📅       │ ← Lịch hẹn (2 khách)
├─────────────────────────────────────┤
│ Ca chiều                            │
│   ✅       -      ...      -        │
├─────────────────────────────────────┤
│ Ca tối                              │
│   -        ✅     ...      -        │
└─────────────────────────────────────┘

Giải thích:
📅 = Lịch hẹn (không có ca làm việc chính thức)
✅ = Ca làm việc chính thức (được admin phân công)
-  = Trống
```

## 🔍 Debugging

### Console logs để kiểm tra

```javascript
// Khi có lịch hẹn nhưng không có ca làm việc:
"⚠️ No shifts assigned for 2025-12-31"
"📅 Found 2 appointment(s) without shift for 2025-12-31"
"Adding appointment-only shift to slot 1 (ca_chieu)"

// Tổng kết:
"📊 Calendar update summary:"
"  - Ca sáng (07:00 - 11:00): 3 shift(s)"
"  - Ca chiều (13:00 - 17:00): 4 shift(s)"  ← Bao gồm cả lịch hẹn
"  - Ca tối (18:00 - 22:00): 2 shift(s)"
```

## 🧪 Test Cases

### Test 1: Lịch hẹn lúc 9h sáng (không có ca làm việc)

```javascript
// Input từ backend
{
  date: "2025-12-31",
  lich_lam_viec: [],  // Rỗng
  lich_hen: [
    {
      id: 123,
      ngay_gio: "2025-12-31 09:00:00",
      khach_hang: "Nguyễn Văn A",
      thu_cung: "Chó Béo",
      dich_vu: "Tiêm phòng"
    }
  ]
}

// Expected output
timeSlots[0].schedule[6] = {  // Slot 0 = Ca sáng, Index 6 = CN
  id: "appointment-2025-12-31",
  room: "Lịch hẹn (1 khách)",
  patients: 1,
  status: "upcoming",
  appointments: [...],
  shift: "ca_sang",
  isAppointmentOnly: true
}
```

### Test 2: Nhiều lịch hẹn trong ngày

```javascript
// Input
{
  date: "2025-12-31",
  lich_lam_viec: [],
  lich_hen: [
    { ngay_gio: "2025-12-31 09:00:00", ... },  // 9h sáng
    { ngay_gio: "2025-12-31 14:00:00", ... },  // 2h chiều
    { ngay_gio: "2025-12-31 19:00:00", ... }   // 7h tối
  ]
}

// Expected: Hiển thị ở ca sáng (dựa vào appointment đầu tiên)
// room: "Lịch hẹn (3 khách)"
// patients: 3
```

## 📌 Lưu ý quan trọng

1. **ID của pseudo shift:** Sử dụng format `appointment-{date}` để tránh conflict với ID thật
2. **Flag `isAppointmentOnly`:** Giúp phân biệt lịch hẹn độc lập vs ca làm việc có lịch hẹn
3. **Xác định ca:** Dựa vào giờ của lịch hẹn đầu tiên trong ngày
4. **Không ảnh hưởng:** Các ca làm việc chính thức vẫn hoạt động bình thường

## 🚀 Cách kiểm tra

1. **Tạo lịch hẹn qua API khách hàng:**

```bash
POST /api/lich-hen
{
  "thu_cung_id": 1,
  "dich_vu_id": 2,
  "ngay_gio": "2025-12-31 14:00:00",
  "ghi_chu": "Khám định kỳ"
}
```

2. **KHÔNG phân công ca cho ngày 31/12**
3. **Đăng nhập bác sĩ → Xem lịch làm việc**
4. **Kết quả mong đợi:** Thấy "Lịch hẹn (1 khách)" ở ngày 31/12

---

**Tạo bởi:** GitHub Copilot  
**Ngày:** 12/12/2025  
**File đã sửa:** `src/components/Doctor/LichLamViec/index.vue`  
**Dòng:** 1100-1142
