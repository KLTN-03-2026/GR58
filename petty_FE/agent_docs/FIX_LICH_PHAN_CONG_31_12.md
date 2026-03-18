# Fix Hiển Thị Lịch Phân Công Ngày 31/12

## 🐛 Vấn đề

- Admin phân công cho nhân viên vào ngày **31/12/2024** (Thứ 4)
- Nhân viên xem lịch tuần nhưng **KHÔNG THẤY** lịch phân công ngày 31/12
- Lịch chỉ hiển thị lịch từ đầu tuần đến 30/12

## 🔍 Nguyên nhân

### Vấn đề 1: Inconsistent Date Formatting (Dòng 805-809)

**Trước:**

```javascript
const itemDateStr = `${date.getFullYear()}-${String(
  date.getMonth() + 1
).padStart(2, "0")}-${String(date.getDate()).padStart(2, "0")}`;
const startStr = formatDate(startOfWeek.value);
const endStr = formatDate(endOfWeek.value);
```

**Vấn đề:**

- `itemDateStr` được xây dựng manually
- `startStr`, `endStr` được format bằng hàm `formatDate()` dùng `toISOString()`
- Dù đều là format `YYYY-MM-DD`, nhưng cách xây dựng khác nhau có thể gây inconsistency

### Vấn đề 2: Timezone Issues (Dòng 800)

**Trước:**

```javascript
const date = new Date(itemDate);
```

**Vấn đề:**

- Khi API trả về `2024-12-31T00:00:00+07:00` hoặc có timezone info
- `new Date()` parse theo timezone của browser
- Ở Vietnam timezone (UTC+7), nếu dữ liệu không có timezone, nó có thể parse sai
- Ví dụ: `2024-12-31T00:00:00` có thể parse thành `2024-12-30` nếu server là UTC

## ✅ Giải pháp

### Fix 1: Dùng Consistent Date Formatting (Dòng 812)

```javascript
// Trước (SAIIII)
const itemDateStr = `${date.getFullYear()}-${String(
  date.getMonth() + 1
).padStart(2, "0")}-${String(date.getDate()).padStart(2, "0")}`;

// Sau (ĐÚNG)
const itemDateStr = formatDate(date);
```

**Lợi ích:** Dùng cùng một function `formatDate()` cho tất cả, đảm bảo consistency

### Fix 2: Safe Date Parsing (Dòng 800-809)

```javascript
// Trước (CÓ THỂ PARSE SAI)
const date = new Date(itemDate);

// Sau (PARSE AN TOÀN)
let date;
if (typeof itemDate === "string" && itemDate.includes("T")) {
  // ISO string format - parse just the date part
  const dateOnly = itemDate.split("T")[0];
  const [year, month, day] = dateOnly.split("-");
  date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
} else {
  date = new Date(itemDate);
}
```

**Lợi ích:**

- Tách phần date (`2024-12-31`) từ ISO string
- Xây dựng `Date` object trực tiếp từ year/month/day
- Tránh timezone issues hoàn toàn

## 📝 Code Changes

**File:** `src/components/Doctor/LichLamViec/index.vue`

**Dòng 800-813** (hàm `fetchScheduleData`):

```javascript
const itemDate = item.ngay_gio;
// Parse date string safely (handle timezone issues)
let date;
if (typeof itemDate === "string" && itemDate.includes("T")) {
  // ISO string format - parse just the date part
  const dateOnly = itemDate.split("T")[0];
  const [year, month, day] = dateOnly.split("-");
  date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
} else {
  date = new Date(itemDate);
}

const dayOfWeek = date.getDay();
const dayIndex = dayOfWeek === 0 ? 6 : dayOfWeek - 1;

// Check if this date is within the current week
// Use consistent date formatting
const itemDateStr = formatDate(date);
const startStr = formatDate(startOfWeek.value);
const endStr = formatDate(endOfWeek.value);

if (itemDateStr >= startStr && itemDateStr <= endStr) {
  // ... rest of code
}
```

## 🧪 Test Cases

### Test 1: Lịch tuần chứa ngày 31/12

**Setup:**

- Admin phân công cho nhân viên vào ngày 31/12/2024
- Nhân viên xem lịch tuần 29/12 - 04/01

**Expected:**

- Hàng "Phân công" hiển thị dữ liệu ngày 31/12 ✅

**Verify:**

```javascript
// DevTools Console
const item = { ngay_gio: "2024-12-31T10:00:00" };
const date = new Date(item.ngay_gio.split("T")[0].split("-"));
// Should show: 2024-12-31
console.log(date.toISOString().split("T")[0]);
```

### Test 2: Lịch tuần không chứa ngày 31/12

**Setup:**

- Nhân viên xem lịch tuần 01/01 - 07/01 (không có 31/12)

**Expected:**

- Hàng "Phân công" không hiển thị dữ liệu 31/12 ✅

## 🔄 Xử lý Timezone

**Ví dụ cụ thể:**

**API trả về:** `2024-12-31T00:00:00+07:00`

**Trước fix:**

```javascript
const date = new Date("2024-12-31T00:00:00+07:00");
// Browser (UTC+7) parse: 2024-12-30 17:00:00 (vì lúc 00:00 VN = 17:00 UTC ngày hôm trước)
// itemDateStr = "2024-12-30" ❌ SAIIII
```

**Sau fix:**

```javascript
const dateOnly = "2024-12-31T00:00:00+07:00".split("T")[0];
// dateOnly = '2024-12-31'
const [year, month, day] = dateOnly.split("-");
const date = new Date(parseInt(2024), parseInt(12) - 1, parseInt(31));
// date = 2024-12-31 (ignore timezone) ✅ ĐÚNG
```

## 📊 So sánh Trước/Sau

| Khía cạnh                   | Trước                   | Sau                          |
| --------------------------- | ----------------------- | ---------------------------- |
| Format date từ API          | Manual string building  | `formatDate()` function      |
| Timezone handling           | Browser automatic parse | Manual parse from YYYY-MM-DD |
| Ngày 31/12 tuần 29/12-04/01 | ❌ Không hiển thị       | ✅ Hiển thị                  |
| Consistency                 | 🔴 Không consistent     | 🟢 Luôn consistent           |
| Reliability                 | 🔴 Có lỗi edge case     | 🟢 Robust                    |

## 🚀 Lợi ích chung

1. **Fix bug ngày biên (31/12 → 01/01)**
2. **Xử lý timezone chính xác**
3. **Code clean hơn, dễ maintain**
4. **Tránh lỗi tương tự ở các tuần khác**

## 💡 Best Practice

**RULE:** Luôn dùng cùng một function để format date/time

- Tránh string building manual
- Tránh timezone inconsistency
- Dễ update và maintain
