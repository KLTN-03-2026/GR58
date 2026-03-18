# 🔧 Fix Lỗi Không Hiển Thị Lịch Admin Phân Công

## 📋 Tóm Tắt Vấn Đề

**Triệu chứng:** Lịch mà admin phân công cho bác sĩ/nhân viên không hiển thị trên giao diện "Lịch của tôi"

**Nguyên nhân gốc rễ:**

1. Frontend chỉ xử lý 3 loại ca tiêu chuẩn: `ca_sang`, `ca_chieu`, `ca_toi`
2. Khi admin phân công ca với `thoi_gian_truc` khác (hoặc giá trị tùy chỉnh), function `getTimeSlotIndex()` trả về `-1` → ca bị bỏ qua
3. Dòng 4 "Phân công" chỉ hiển thị ca từ `/lich-dang-ky`, KHÔNG hiển thị ca từ `/lich-lam-viec`

## ✅ Giải Pháp Đã Triển Khai

### 1. **Cập Nhật Logic Xử Lý Slot (updateCalendarData)**

**File:** `src/components/Doctor/LichLamViec/index.vue`

**Thay đổi:**

```javascript
// ❌ TRƯỚC (bỏ qua ca không thuộc 3 loại tiêu chuẩn)
shifts.forEach((shift) => {
  const slotIndex = getTimeSlotIndex(shift.thoi_gian_truc);
  if (slotIndex !== -1 && timeSlots.value[slotIndex]) {
    // Chỉ xử lý ca_sang, ca_chieu, ca_toi
  }
});

// ✅ SAU (tất cả ca đều được hiển thị)
shifts.forEach((shift) => {
  let slotIndex = getTimeSlotIndex(shift.thoi_gian_truc);

  // Nếu không thuộc 3 ca tiêu chuẩn, đưa vào slot "Phân công"
  if (slotIndex === -1) {
    slotIndex = 3; // Slot "Phân công"
  }

  if (timeSlots.value[slotIndex]) {
    // Xử lý shift...
  }
});
```

### 2. **Hỗ Trợ Nhiều Ca Trong Cùng 1 Ô**

**Vấn đề:** Một ngày có thể có nhiều ca phân công (admin gán 2-3 ca khác nhau)

**Giải pháp:**

```javascript
// Kiểm tra nếu ô đã có ca
if (timeSlots.value[slotIndex].schedule[dayIndex]) {
  const existing = timeSlots.value[slotIndex].schedule[dayIndex];
  if (Array.isArray(existing)) {
    existing.push(shiftData); // Thêm vào mảng
  } else {
    timeSlots.value[slotIndex].schedule[dayIndex] = [existing, shiftData]; // Tạo mảng mới
  }
} else {
  timeSlots.value[slotIndex].schedule[dayIndex] = shiftData; // Ca đầu tiên
}
```

### 3. **Cập Nhật Template Hiển Thị**

**File:** `src/components/Doctor/LichLamViec/index.vue` (template section)

**Thay đổi:**

```vue
<!-- ❌ TRƯỚC: Chỉ xử lý single shift -->
<div v-if="daySchedule" :class="...">
  <span>{{ daySchedule.room }}</span>
</div>

<!-- ✅ SAU: Xử lý cả single và multiple shifts -->
<template v-if="daySchedule">
  <!-- Single shift -->
  <div v-if="!Array.isArray(daySchedule)" :class="...">
    <span>{{ daySchedule.room }}</span>
  </div>

  <!-- Multiple shifts -->
  <div v-else :class="...">
    <span>{{ daySchedule[0].room }}</span>
    <span v-if="daySchedule.length > 1">
      +{{ daySchedule.length - 1 }} ca khác
    </span>
  </div>
</template>
```

### 4. **Cập Nhật getShiftName Helper**

```javascript
// ❌ TRƯỚC
const getShiftName = (thoi_gian_truc) => {
  const names = { ca_sang: "Ca Sáng", ca_chieu: "Ca Chiều", ca_toi: "Ca Tối" };
  return names[thoi_gian_truc] || thoi_gian_truc; // Trả về giá trị lạ
};

// ✅ SAU
const getShiftName = (thoi_gian_truc) => {
  const names = { ca_sang: "Ca Sáng", ca_chieu: "Ca Chiều", ca_toi: "Ca Tối" };
  return names[thoi_gian_truc] || "Phân công"; // Luôn có tên dễ hiểu
};
```

### 5. **Thêm Console Logging Chi Tiết**

```javascript
console.log("📅 Starting to process schedule data...");
console.log(`✅ Found ${shifts.length} shift(s) for ${daySchedule.date}`);
console.log(
  `Admin-assigned shift (non-standard time) for ${daySchedule.date}:`,
  shift.thoi_gian_truc
);

console.log("📊 Calendar update summary:");
timeSlots.value.forEach((slot, idx) => {
  const count = slot.schedule.filter((s) => s !== null).length;
  console.log(`  - ${slot.name} (${slot.time}): ${count} shift(s)`);
});
```

## 🧪 Cách Kiểm Tra

### Bước 1: Mở Chrome DevTools Console

1. Vào trang **Lịch làm việc** (Doctor/Lịch làm việc)
2. Mở DevTools (F12) → tab Console
3. Refresh trang

### Bước 2: Kiểm Tra Log

Bạn sẽ thấy:

```
📅 Starting to process schedule data...
✅ Found 2 shift(s) for 2025-12-09
Admin-assigned shift (non-standard time) for 2025-12-09: ca_phan_cong
📊 Calendar update summary:
  - Sáng (8h-12h): 1 shift(s)
  - Chiều (13h-17h): 0 shift(s)
  - Tối (18h-21h): 1 shift(s)
  - Phân công (Các giờ khác): 1 shift(s)
```

### Bước 3: Kiểm Tra Giao Diện

1. Xem lịch tuần hiện tại
2. **Dòng 4 "Phân công"** phải hiển thị các ca admin đã gán
3. Nếu một ngày có nhiều ca phân công → hiển thị "+X ca khác"

## 🔍 Debugging

### Nếu vẫn không hiển thị:

**1. Kiểm tra API Response:**

```javascript
// Trong Console, xem response từ API
// Tìm log: "fetchScheduleData response:"
```

**2. Kiểm tra Structure:**

```javascript
{
  status: true,
  data: {
    schedule: [
      {
        date: "2025-12-09",
        lich_lam_viec: [
          {
            id: 123,
            thoi_gian_truc: "ca_phan_cong",  // ← Giá trị bất kỳ
            ghi_chu: "Phòng A"
          }
        ]
      }
    ]
  }
}
```

**3. Kiểm tra Backend:**

```bash
# Gọi API trực tiếp với Postman/Thunder Client
GET http://localhost:8000/api/lich-lam-viec?start_date=2025-12-09&end_date=2025-12-15
Authorization: Bearer YOUR_TOKEN
```

**4. Kiểm tra Database:**

```sql
SELECT * FROM lich_lam_viec
WHERE nhan_vien_id = YOUR_ID
AND ngay_lam BETWEEN '2025-12-09' AND '2025-12-15';
```

## 📊 Kết Quả Mong Đợi

### Trước Fix:

```
┌─────────┬─────┬─────┬─────┐
│ Sáng    │  ✓  │     │  ✓  │
│ Chiều   │     │  ✓  │     │
│ Tối     │  ✓  │     │     │
│ Phân công│    │     │     │  ← TRỐNG (dù admin đã phân)
└─────────┴─────┴─────┴─────┘
```

### Sau Fix:

```
┌─────────┬─────┬─────┬─────┐
│ Sáng    │  ✓  │     │  ✓  │
│ Chiều   │     │  ✓  │     │
│ Tối     │  ✓  │     │     │
│ Phân công│  ✓  │  ✓  │  ✓  │  ← HIỂN THỊ đầy đủ
└─────────┴─────┴─────┴─────┘
```

## 🎯 Các Trường Hợp Được Xử Lý

### ✅ Case 1: Ca tiêu chuẩn

- `ca_sang` → Dòng 1 "Sáng"
- `ca_chieu` → Dòng 2 "Chiều"
- `ca_toi` → Dòng 3 "Tối"

### ✅ Case 2: Ca tùy chỉnh

- `ca_phan_cong` → Dòng 4 "Phân công"
- `ca_khac` → Dòng 4 "Phân công"
- Bất kỳ giá trị nào không phải 3 ca trên → Dòng 4

### ✅ Case 3: Nhiều ca cùng ngày cùng slot

- Hiển thị ca đầu tiên
- Thêm label "+X ca khác" ở dưới

### ✅ Case 4: Kết hợp ca admin + ca đăng ký

- Ca từ `/lich-lam-viec` (admin phân) + Ca từ `/lich-dang-ky` (tự đăng ký)
- Cả 2 đều hiển thị trong dòng "Phân công"

## 📁 Files Đã Chỉnh Sửa

| File                                          | Số dòng thay đổi | Mô tả                 |
| --------------------------------------------- | ---------------- | --------------------- |
| `src/components/Doctor/LichLamViec/index.vue` | ~50 lines        | Main logic + template |

## 🚀 Deployment

1. **Development:**

   ```bash
   npm run dev
   ```

2. **Production:**

   ```bash
   npm run build
   ```

3. **Test:**
   - Login với tài khoản bác sĩ có lịch admin phân
   - Vào "Lịch của tôi"
   - Kiểm tra dòng "Phân công" có hiển thị

## 📞 Hỗ Trợ

Nếu vẫn gặp vấn đề:

1. Kiểm tra Console log (F12)
2. Kiểm tra Network tab → API `/lich-lam-viec` response
3. Kiểm tra database có dữ liệu chưa
4. Kiểm tra backend routes đã thêm chưa (xem `ROUTES_API_LICH_LAM_VIEC.php`)

---

**Ngày cập nhật:** 2025-12-11  
**Version:** 2.0  
**Status:** ✅ HOÀN THÀNH
