# Debug: Chỉ Hiển Thị Lịch Đăng Ký, Không Hiển Thị Lịch Của Tôi

## 🐛 Vấn đề

- Tab "Lịch của tôi" chỉ hiển thị lịch đăng ký (phân công từ admin)
- Lịch chính thức (lich_lam_viec từ API) **KHÔNG HIỂN THỊ**
- Bảng lịch tuần trống hoặc chỉ có lịch phân công

## 🔍 Nguyên nhân Có Thể

### Nguyên nhân 1: API `/lich-lam-viec` Không Trả Dữ Liệu

**Biểu hiệu:**

- DevTools Console: không thấy log "Processing schedule"
- `data.schedule` undefined hoặc rỗng

**Kiểm tra:**

```javascript
// DevTools Console
fetch("/api/lich-lam-viec?tungay=2024-12-29&denngay=2025-01-04")
  .then((r) => r.json())
  .then((d) => console.log(d));
```

### Nguyên nhân 2: `lich_lam_viec` Undefined trong Response

**Biểu hiệu:**

- Log "No lich_lam_viec for YYYY-MM-DD" xuất hiện

**Kiểm tra:**

- API trả về `lich_lam_viec` hay một field khác?
- Tên field có thay đổi không?

### Nguyên nhân 3: `lich_lam_viec` Rỗng (Array Trống)

**Biểu hiệu:**

- Log "Processing schedule" có, nhưng "shifts: []"
- Dữ liệu chưa được admin phân công

### Nguyên nhân 4: Timezone Parse Issues

**Biểu hiệu:**

- `dayIndex` không đúng, data thêm vào sai cột
- Lịch hiển thị ở ngày sai

## 🛠️ Debug Steps

### Bước 1: Kiểm Tra Console Logs

```
1. Mở DevTools (F12)
2. Vào tab Console
3. Refresh trang
4. Tìm logs:
   - "fetchScheduleData response:" → Xem dữ liệu API
   - "Schedule data to update:" → Xem data được xử lý
   - "Processing schedule" → Xem schedule array
   - "Processing day..." → Xem từng ngày
   - "No lich_lam_viec..." → Field không tồn tại
```

### Bước 2: Kiểm Tra API Response

```javascript
// Copy vào Console
const weekStart = "2024-12-29";
const weekEnd = "2025-01-04";
fetch(`/api/lich-lam-viec?tungay=${weekStart}&denngay=${weekEnd}`)
  .then((r) => r.json())
  .then((d) => {
    console.log("Full response:", d);
    if (d.data && d.data.schedule) {
      d.data.schedule.forEach((day) => {
        console.log(`${day.date}:`, day);
      });
    }
  });
```

### Bước 3: Kiểm Tra Tên Field

```javascript
// Xem tất cả fields trong response
const response = ... // từ bước 2
console.log("Day fields:", Object.keys(response.data.schedule[0]));
// Tìm field chứa lịch làm việc:
// - lich_lam_viec?
// - shifts?
// - work_shifts?
// - tương tự?
```

### Bước 4: Kiểm Tra Dữ Liệu Thực Tế

```javascript
// Xem toàn bộ response formatting
const day = response.data.schedule[0];
console.log(JSON.stringify(day, null, 2));
```

## 📝 Log Output Examples

### ✅ Đúng (Dữ liệu có lịch)

```
fetchScheduleData response: {
  status: true,
  data: {
    nhan_vien: {...},
    schedule: [
      {
        date: "2024-12-29",
        lich_lam_viec: [{id: 1, thoi_gian_truc: "ca_sang", ...}],
        lich_hen: [...]
      },
      ...
    ]
  }
}
Processing schedule [
  {date: "2024-12-29", lich_lam_viec: Array(1), ...},
  ...
]
Processing day 2024-12-29, shifts: [
  {id: 1, thoi_gian_truc: "ca_sang", ...}
]
```

### ❌ Sai (Không có lịch)

```
fetchScheduleData response: {
  status: true,
  data: {
    schedule: [
      {
        date: "2024-12-29",
        lich_lam_viec: [],  // ← RỖNG!
        lich_hen: []
      },
      ...
    ]
  }
}
```

### ❌ Sai (Field không tồn tại)

```
updateCalendarData: Processing schedule [
  {date: "2024-12-29", shifts: [...], ...}  // ← "shifts" không phải "lich_lam_viec"!
]
No lich_lam_viec for 2024-12-29
```

## 🔧 Có Thể Phải Fix

### Nếu API Trả Về Field Khác

**Hiện tại:**

```javascript
daySchedule.lich_lam_viec.forEach((shift) => {
```

**Nếu API dùng `shifts`:**

```javascript
(daySchedule.lich_lam_viec || daySchedule.shifts || []).forEach((shift) => {
```

### Nếu API Trả Về Toàn Bộ Schedule Khác

**Ví dụ: API trả về array shifts thẳng, không phải nested:**

```javascript
// Phải adjust parselogic
```

## 🚀 Tạm Thời Work Around

Nếu không thể xác định nguyên nhân, thử:

```javascript
const updateCalendarData = (data) => {
  if (!data) {
    console.warn("No data provided");
    return;
  }

  // Support multiple response formats
  let schedule = data.schedule || data.data || data;

  if (!Array.isArray(schedule)) {
    console.warn("Schedule is not array:", schedule);
    return;
  }

  // Reset time slots
  timeSlots.value.forEach((slot) => {
    slot.schedule = [null, null, null, null, null, null, null];
  });

  // Process each day
  schedule.forEach((daySchedule) => {
    if (!daySchedule || !daySchedule.date) {
      console.warn("Invalid day schedule:", daySchedule);
      return;
    }

    const date = new Date(daySchedule.date);
    const dayOfWeek = date.getDay();
    const dayIndex = dayOfWeek === 0 ? 6 : dayOfWeek - 1;

    // Support multiple field names
    const shifts =
      daySchedule.lich_lam_viec ||
      daySchedule.shifts ||
      daySchedule.work_shifts ||
      [];

    console.log(`Day ${daySchedule.date}: ${shifts.length} shifts`);

    shifts.forEach((shift) => {
      // ... rest of logic
    });
  });
};
```

## 💡 Hướng Dẫn Quy Trình

1. **Refresh trang → F12 Console → Đọc logs**
2. **Nếu thấy "No lich_lam_viec" → API dùng field khác**
3. **Copy API URL từ Network Tab → Test ở console**
4. **Xem structure của response → Adapt code**
5. **Report lại dữ liệu API để fix**

## 📌 Chú Ý

- Các logs mới đã thêm để tracking
- Không cần clear browser cache
- Có thể refresh để thấy logs mới
- Khác admin backend có thể fix response format
