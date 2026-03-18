# 📋 Hướng dẫn Đăng ký Ca làm việc cho Bác sĩ

## 🎯 Tổng quan

Tài liệu này hướng dẫn chi tiết về chức năng đăng ký ca làm việc của bác sĩ thông qua giao diện lịch trực.

## 📁 File liên quan

- **Component chính**: `src/components/Doctor/LichLamViec/index.vue`
- **Modal đăng ký**: `src/components/Doctor/LichLamViec/DangKyCa/index.vue`
- **Backend Controller**: Laravel `LichDangKyController.php`

## 🔌 API Endpoints sử dụng

### 1. Lấy danh sách ca làm việc có sẵn

```
GET /api/lich-lam-viec
```

**Query Parameters:**

- `tu_ngay`: Ngày bắt đầu (YYYY-MM-DD)
- `den_ngay`: Ngày kết thúc (YYYY-MM-DD)
- `per_page`: Số lượng bản ghi (mặc định: 100)

**Response:**

```json
{
  "success": true,
  "message": "Lấy danh sách lịch làm việc thành công",
  "data": {
    "data": [
      {
        "id": 1,
        "ngay_lam": "2024-12-16",
        "thoi_gian_truc": "ca_sang",
        "phong_truc": "Phòng khám 1",
        "ghi_chu": "Ca trực thường"
      }
    ]
  }
}
```

### 2. Lấy danh sách đăng ký của tôi

```
GET /api/lich-dang-ky/lich-cua-toi
```

**Query Parameters:**

- `tu_ngay`: Ngày bắt đầu
- `den_ngay`: Ngày kết thúc
- `trang_thai`: Trạng thái (optional)
- `per_page`: Số lượng bản ghi

**Response:**

```json
{
  "success": true,
  "message": "Lấy danh sách lịch của bạn thành công",
  "data": {
    "data": [
      {
        "id": 1,
        "nhan_vien_id": 5,
        "lich_lam_viec_id": 10,
        "ngay_gio": "2024-12-16 06:00:00",
        "trang_thai": "da_xac_nhan",
        "ghi_chu": "",
        "lich_lam_viec": {
          "id": 10,
          "ngay_lam": "2024-12-16",
          "thoi_gian_truc": "ca_sang"
        }
      }
    ]
  }
}
```

### 3. Đăng ký ca làm việc

```
POST /api/lich-dang-ky/dang-ky-nhan-vien
```

**Headers:**

```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**

```json
{
  "ngay_gio": "2024-12-16 06:00:00",
  "lich_lam_viec_id": 10,
  "ghi_chu": "Tôi muốn đăng ký ca này"
}
```

**Response:**

```json
{
  "success": true,
  "message": "Đăng ký lịch làm việc thành công",
  "data": {
    "id": 1,
    "nhan_vien_id": 5,
    "lich_lam_viec_id": 10,
    "ngay_gio": "2024-12-16 06:00:00",
    "trang_thai": "da_xac_nhan",
    "ghi_chu": "Tôi muốn đăng ký ca này"
  }
}
```

## 🎨 Giao diện người dùng

### Tab "Đăng ký ca trực"

#### 1. Bảng lịch đăng ký

- **Cột đầu tiên**: Ca trực (Sáng, Chiều, Tối)
- **7 cột tiếp theo**: Các ngày trong tuần (Thứ 2 - CN)
- **Mỗi ô**: Trạng thái ca làm việc

#### 2. Trạng thái của ô

- **Empty (Trống)**: Có thể đăng ký
  - Icon: ➕ (Plus circle)
  - Màu: Trắng, viền xám
- **Selected (Đang chọn)**: Đã chọn để đăng ký
  - Icon: ✓ (Check circle blue)
  - Màu: Xanh nhạt, viền xanh dương
- **Approved (Đã duyệt)**: Admin đã phê duyệt
  - Icon: ✓ (Check circle green)
  - Màu: Xanh lá nhạt, viền xanh lá
  - Text: "Đã duyệt"
- **Full (Đã kín)**: Đã đủ bác sĩ đăng ký
  - Icon: 🔒 (Lock icon)
  - Màu: Xám, viền xám đậm
  - Text: "Full"

#### 3. Thống kê

```
Đã đăng ký: 5 ca
Yêu cầu tối thiểu: 4 ca/tuần
```

#### 4. Tổng kết

```
Tổng cộng đã chọn: 4 ca mới + 1 ca đã duyệt = 5 ca
✓ Đủ yêu cầu
```

#### 5. Nút hành động

- **Lưu nháp**: Lưu các lựa chọn (chưa implement backend)
- **🚀 Gửi đăng ký**: Gửi tất cả ca đã chọn lên server

## 💻 Code Implementation

### State Management

```javascript
// Available shifts and registrations
const availableShiftsData = ref([]);
const myRegistrations = ref([]);
const selectedShiftsForRegistration = ref([]);

// Shifts grid (3 rows x 7 days)
const shifts = ref([
  {
    id: 1,
    name: "Sáng",
    time: "8h-12h",
    days: ["empty", "empty", "empty", "empty", "empty", "empty", "empty"],
  },
  {
    id: 2,
    name: "Chiều",
    time: "13h-17h",
    days: ["empty", "empty", "empty", "empty", "empty", "empty", "empty"],
  },
  {
    id: 3,
    name: "Tối",
    time: "18h-21h",
    days: ["empty", "empty", "empty", "empty", "empty", "empty", "empty"],
  },
]);
```

### Fetch Functions

#### fetchAvailableShiftsForRegistration()

```javascript
const fetchAvailableShiftsForRegistration = async () => {
  loading.value = true;
  try {
    // 1. Lấy danh sách ca làm việc có sẵn trong tuần
    const response = await api.get("/lich-lam-viec", {
      params: {
        tu_ngay: formatDate(startOfWeek.value),
        den_ngay: formatDate(endOfWeek.value),
        per_page: 100,
      },
    });
    availableShiftsData.value = response.data?.data?.data || [];

    // 2. Lấy danh sách đăng ký của tôi
    await fetchMyRegistrations();

    // 3. Cập nhật bảng lịch
    updateShiftsGrid();
  } catch (error) {
    showErrorToast("Lỗi", "Không thể tải danh sách ca làm việc");
  } finally {
    loading.value = false;
  }
};
```

#### fetchMyRegistrations()

```javascript
const fetchMyRegistrations = async () => {
  try {
    const response = await api.get("/lich-dang-ky/lich-cua-toi", {
      params: {
        tu_ngay: formatDate(startOfWeek.value),
        den_ngay: formatDate(endOfWeek.value),
        per_page: 100,
      },
    });
    myRegistrations.value = response.data?.data?.data || [];
  } catch (error) {
    console.error("Error fetching my registrations:", error);
  }
};
```

### Update Functions

#### updateShiftsGrid()

```javascript
const updateShiftsGrid = () => {
  // 1. Reset tất cả ô về trạng thái "empty"
  shifts.value.forEach((shift) => {
    shift.days = [
      "empty",
      "empty",
      "empty",
      "empty",
      "empty",
      "empty",
      "empty",
    ];
  });

  // 2. Đánh dấu các ca đã đăng ký
  myRegistrations.value.forEach((registration) => {
    if (
      registration.trang_thai === "da_xac_nhan" &&
      registration.lich_lam_viec
    ) {
      // Đánh dấu "approved"
      const shift = registration.lich_lam_viec;
      const dayIndex = getDayIndexFromDate(shift.ngay_lam);
      const shiftIndex = getShiftIndexFromType(shift.thoi_gian_truc);

      if (dayIndex !== -1 && shiftIndex !== -1) {
        shifts.value[shiftIndex].days[dayIndex] = "approved";
      }
    } else if (
      registration.trang_thai === "chua_xac_nhan" &&
      registration.lich_lam_viec
    ) {
      // Đánh dấu "selected" (đang chờ duyệt)
      const shift = registration.lich_lam_viec;
      const dayIndex = getDayIndexFromDate(shift.ngay_lam);
      const shiftIndex = getShiftIndexFromType(shift.thoi_gian_truc);

      if (dayIndex !== -1 && shiftIndex !== -1) {
        shifts.value[shiftIndex].days[dayIndex] = "selected";
      }
    }
  });

  // 3. Cập nhật thống kê
  updateRegistrationCounts();
};
```

### Toggle Function

#### toggleShift()

```javascript
function toggleShift(shiftId, dayIndex, currentStatus) {
  // Không cho phép thay đổi ca đã duyệt hoặc đã kín
  if (currentStatus === "approved" || currentStatus === "full") {
    return;
  }

  const shift = shifts.value.find((s) => s.id === shiftId);
  if (shift) {
    if (currentStatus === "selected") {
      // Bỏ chọn
      shift.days[dayIndex] = "empty";

      // Xóa khỏi danh sách selectedShiftsForRegistration
      const shiftDate = getDateFromDayIndex(dayIndex);
      selectedShiftsForRegistration.value =
        selectedShiftsForRegistration.value.filter(
          (s) => !(s.shiftType === shift.name && s.date === shiftDate)
        );
    } else {
      // Chọn ca
      shift.days[dayIndex] = "selected";

      // Thêm vào danh sách selectedShiftsForRegistration
      const shiftDate = getDateFromDayIndex(dayIndex);
      const availableShift = availableShiftsData.value.find(
        (s) =>
          s.ngay_lam === shiftDate &&
          getShiftTypeFromTimeSlot(s.thoi_gian_truc) === shift.name
      );

      if (availableShift) {
        selectedShiftsForRegistration.value.push({
          shiftId: availableShift.id,
          shiftType: shift.name,
          date: shiftDate,
          dayIndex: dayIndex,
        });
      }
    }
  }
}
```

### Submit Function

#### submitAllRegistrations()

```javascript
const submitAllRegistrations = async () => {
  if (selectedShiftsForRegistration.value.length === 0) {
    showErrorToast("Lỗi", "Vui lòng chọn ít nhất một ca để đăng ký");
    return;
  }

  loading.value = true;
  try {
    // Tạo array of promises cho tất cả các ca đã chọn
    const promises = selectedShiftsForRegistration.value.map(async (shift) => {
      const availableShift = availableShiftsData.value.find(
        (s) => s.id === shift.shiftId
      );
      if (!availableShift) return;

      const payload = {
        ngay_gio: `${availableShift.ngay_lam} ${
          availableShift.thoi_gian_truc === "ca_sang"
            ? "06:00:00"
            : availableShift.thoi_gian_truc === "ca_chieu"
            ? "13:00:00"
            : "19:00:00"
        }`,
        lich_lam_viec_id: availableShift.id,
        ghi_chu: "",
      };

      return api.post("/lich-dang-ky/dang-ky-nhan-vien", payload);
    });

    // Gửi tất cả đăng ký cùng lúc
    await Promise.all(promises);

    showSuccessToast(
      "Thành công",
      `Đã gửi đăng ký ${selectedShiftsForRegistration.value.length} ca làm việc`
    );

    // Xóa danh sách đã chọn và refresh dữ liệu
    selectedShiftsForRegistration.value = [];
    await fetchAvailableShiftsForRegistration();
  } catch (error) {
    const errorMsg = error.response?.data?.message || "Không thể gửi đăng ký";
    showErrorToast("Lỗi", errorMsg);
  } finally {
    loading.value = false;
  }
};
```

## 🔄 Flow hoạt động

### 1. Khởi tạo

```
User chuyển sang tab "Đăng ký ca trực"
   ↓
Watch activeTab trigger
   ↓
fetchAvailableShiftsForRegistration()
   ↓
GET /api/lich-lam-viec (ca có sẵn)
   ↓
fetchMyRegistrations()
   ↓
GET /api/lich-dang-ky/lich-cua-toi (ca đã đăng ký)
   ↓
updateShiftsGrid() (cập nhật UI)
```

### 2. Chọn ca

```
User click vào ô trống
   ↓
toggleShift(shiftId, dayIndex, "empty")
   ↓
shift.days[dayIndex] = "selected"
   ↓
Thêm vào selectedShiftsForRegistration[]
   ↓
UI cập nhật: hiển thị icon ✓ xanh dương
```

### 3. Bỏ chọn ca

```
User click vào ô đang chọn
   ↓
toggleShift(shiftId, dayIndex, "selected")
   ↓
shift.days[dayIndex] = "empty"
   ↓
Xóa khỏi selectedShiftsForRegistration[]
   ↓
UI cập nhật: hiển thị icon ➕
```

### 4. Gửi đăng ký

```
User click "🚀 Gửi đăng ký"
   ↓
submitAllRegistrations()
   ↓
Validate: selectedShiftsForRegistration.length > 0
   ↓
Loop qua từng ca đã chọn
   ↓
POST /api/lich-dang-ky/dang-ky-nhan-vien (từng ca)
   ↓
Promise.all() chờ tất cả request hoàn thành
   ↓
showSuccessToast()
   ↓
Clear selectedShiftsForRegistration[]
   ↓
fetchAvailableShiftsForRegistration() (refresh data)
```

## 🎯 Helper Functions

### getDayIndexFromDate()

Chuyển đổi ngày (YYYY-MM-DD) thành index trong tuần (0-6)

```javascript
const getDayIndexFromDate = (dateStr) => {
  const date = new Date(dateStr);
  const dayOfWeek = date.getDay();
  return dayOfWeek === 0 ? 6 : dayOfWeek - 1; // Monday = 0, Sunday = 6
};
```

### getShiftIndexFromType()

Chuyển đổi loại ca (ca_sang, ca_chieu, ca_toi) thành index (0-2)

```javascript
const getShiftIndexFromType = (thoi_gian_truc) => {
  const mapping = {
    ca_sang: 0,
    ca_chieu: 1,
    ca_toi: 2,
  };
  return mapping[thoi_gian_truc] !== undefined ? mapping[thoi_gian_truc] : -1;
};
```

### getDateFromDayIndex()

Chuyển đổi index trong tuần (0-6) thành ngày (YYYY-MM-DD)

```javascript
const getDateFromDayIndex = (dayIndex) => {
  const date = new Date(startOfWeek.value);
  date.setDate(date.getDate() + dayIndex);
  return formatDate(date);
};
```

## 🚨 Error Handling

### Các trường hợp lỗi

1. **Không load được ca làm việc**

   ```javascript
   showErrorToast("Lỗi", "Không thể tải danh sách ca làm việc");
   ```

2. **Không chọn ca nào**

   ```javascript
   showErrorToast("Lỗi", "Vui lòng chọn ít nhất một ca để đăng ký");
   ```

3. **Gửi đăng ký thất bại**
   ```javascript
   const errorMsg = error.response?.data?.message || "Không thể gửi đăng ký";
   showErrorToast("Lỗi", errorMsg);
   ```

## 📝 Lưu ý quan trọng

### 1. Trạng thái không thể thay đổi

- **Approved (Đã duyệt)**: Admin đã phê duyệt, bác sĩ không thể hủy
- **Full (Đã kín)**: Ca đã đủ người, không thể đăng ký thêm

### 2. Mapping thời gian

```javascript
ca_sang  → 06:00:00 (8h-12h)
ca_chieu → 13:00:00 (13h-17h)
ca_toi   → 19:00:00 (18h-21h)
```

### 3. Authentication

- Tất cả API đều cần Bearer token
- Backend tự động lấy `nhan_vien_id` từ user đang đăng nhập
- Middleware: `staff.only`

### 4. Validation Backend

```php
'ngay_gio' => 'required|date',
'lich_lam_viec_id' => 'required|exists:lich_lam_viecs,id',
'ghi_chu' => 'nullable|string',
```

## 🎨 UI/UX Features

### 1. Loading State

```html
<button
  @click="submitAllRegistrations"
  :disabled="selectedShiftsForRegistration.length === 0 || loading"
>
  {{ loading ? "Đang gửi..." : "🚀 Gửi đăng ký" }}
</button>
```

### 2. Dynamic Badge

```html
<div
  :class="totalShiftsCount >= minRequirement ? 'bg-green-100' : 'bg-red-100'"
>
  <span>
    {{ totalShiftsCount >= minRequirement ? '✓ Đủ yêu cầu' : '⚠ Chưa đủ yêu cầu'
    }}
  </span>
</div>
```

### 3. Disabled Button

```html
<button
  :disabled="selectedShiftsForRegistration.length === 0 || loading"
  :class="[
    selectedShiftsForRegistration.length === 0 || loading
      ? 'bg-gray-400 cursor-not-allowed'
      : 'bg-[#155dfc] hover:bg-blue-700'
  ]"
></button>
```

## 🔧 Testing Checklist

- [ ] Load trang → ca đã đăng ký hiển thị đúng
- [ ] Click ô trống → chuyển sang "selected"
- [ ] Click ô "selected" → chuyển về "empty"
- [ ] Click ô "approved" → không thay đổi
- [ ] Click ô "full" → không thay đổi
- [ ] Đếm ca mới đúng
- [ ] Đếm ca đã duyệt đúng
- [ ] Tổng số ca đúng
- [ ] Badge "Đủ yêu cầu" hiển thị đúng
- [ ] Gửi đăng ký thành công → hiển thị toast
- [ ] Gửi đăng ký thất bại → hiển thị lỗi
- [ ] Sau gửi đăng ký → refresh data
- [ ] Chuyển tuần → load ca mới
- [ ] Loading state hoạt động

## 📚 Tài liệu liên quan

- [DANG_KY_CA_LAM_VIEC_GUIDE.md](./DANG_KY_CA_LAM_VIEC_GUIDE.md) - Modal đăng ký chi tiết
- [XAC_NHAN_DANG_KY_GUIDE.md](./XAC_NHAN_DANG_KY_GUIDE.md) - Admin xác nhận đăng ký

---

**Ngày tạo**: 11/12/2024  
**Phiên bản**: 1.0  
**Tác giả**: Development Team
