# 🔧 Tóm tắt Fix Check-in Y tá

## 📌 Vấn đề đã khắc phục

### 1. **Không hiển thị danh sách lịch hẹn**

- **Lỗi cũ**: Tab "Danh sách lịch hẹn" chỉ hiển thị mock data tĩnh
- **Giải pháp**: Thêm API call để fetch dữ liệu thật từ backend

### 2. **Check-in không hoạt động đúng**

- **Lỗi cũ**: Chỉ cập nhật local state, không gọi API backend
- **Giải pháp**: Tích hợp với `checkInAppointment` API và refresh tất cả các tab

---

## ✅ Các thay đổi đã thực hiện

### **1. Import thêm API Service**

```javascript
import {
  getAppointmentsWaitingCheckIn,
  getCheckedInAppointments,
  getAllAppointments, // ⭐ NEW
} from "@/services/lichHenService";
```

### **2. Thêm State mới**

```javascript
// Main appointments list state
const appointments = ref([]);
const loadingAppointments = ref(false);
```

### **3. Xóa Mock Data**

```javascript
// ❌ Đã xóa: const appointments = ref([...mock data...])
// ✅ Thay bằng: const appointments = ref([])
```

### **4. Thêm Function `fetchAllAppointments()`**

Fetch danh sách lịch hẹn từ API:

```javascript
const fetchAllAppointments = async () => {
  loadingAppointments.value = true;
  try {
    const today = new Date().toISOString().split('T')[0];
    const response = await getAllAppointments({
      per_page: 100,
      from_date: today, // Lấy lịch từ hôm nay
    });

    if (response.status && response.data) {
      // Transform data từ backend sang UI format
      appointments.value = appointmentsList.map(app => {
        // Parse thời gian
        const appointmentTime = ...;
        const checkInTime = ...;

        // Xác định trạng thái
        let status = 'upcoming';
        if (app.trang_thai === 'in-progress') status = 'examining';
        if (app.trang_thai === 'completed') status = 'payment';

        return {
          id: app.id,
          appointmentTime,
          checkInTime,
          petName: app.thu_cung?.ten_thu_cung,
          ownerName: app.khach_hang,
          service: app.dich_vu?.ten,
          doctor: app.nhan_vien?.full_name || 'Chưa phân công',
          status,
          originalData: app, // ⭐ Lưu data gốc
        };
      });

      updateFilterCounts(); // Cập nhật số lượng filter
    }
  } catch (error) {
    showErrorToast('Không thể tải danh sách lịch hẹn');
  }
};
```

**Mapping Backend → Frontend:**
| Backend Field | Frontend Field | Xử lý |
|--------------|----------------|-------|
| `ngay_gio` | `appointmentTime` | Parse date → "HH:mm" |
| `thoi_gian_checkin` | `checkInTime` | Parse date → "HH:mm" |
| `thu_cung.ten_thu_cung` | `petName` | Direct mapping |
| `khach_hang` | `ownerName` | Direct mapping (transformed by backend) |
| `dich_vu.ten` | `service` | Direct mapping |
| `nhan_vien.full_name` | `doctor` | "Chưa phân công" nếu null |
| `trang_thai` | `status` | pending/confirmed → upcoming<br>in-progress → examining<br>completed → payment |

### **5. Cập nhật Function `checkIn()`**

Sử dụng `originalData` từ API:

```javascript
const checkIn = (appointment) => {
  // Nếu có originalData (từ API), dùng nó
  if (appointment.originalData) {
    selectedAppointment.value = appointment.originalData;
  } else {
    // Fallback cho format cũ
    selectedAppointment.value = { id: appointment.id, ...appointment };
  }
  isCheckInModalOpen.value = true;
};
```

### **6. Cập nhật `handleCheckInSuccess()`**

Refresh tất cả 3 tabs sau khi check-in:

```javascript
const handleCheckInSuccess = (updatedAppointment) => {
  // 1. Remove khỏi "Chờ Check-in"
  waitingCheckInAppointments.value = waitingCheckInAppointments.value.filter(
    (app) => app.id !== updatedAppointment.id
  );

  // 2. Add vào "Đã Check-in"
  checkedInAppointments.value.unshift(updatedAppointment);

  // 3. Update trong "Danh sách lịch hẹn"
  const index = appointments.value.findIndex((a) => a.id === updatedAppointment.id);
  if (index !== -1) {
    appointments.value[index].checkInTime = ...;
    appointments.value[index].status = "examining";
    appointments.value[index].rowColor = "bg-orange-50";
    appointments.value[index].action = "payment";
    appointments.value[index].originalData = updatedAppointment;
  }

  // 4. Refresh tất cả lists
  fetchWaitingCheckInList();
  fetchCheckedInList();
  fetchAllAppointments(); // ⭐ NEW
  updateFilterCounts();    // ⭐ NEW
};
```

### **7. Thêm Function `updateFilterCounts()`**

Tự động cập nhật số lượng trong filter badges:

```javascript
const updateFilterCounts = () => {
  const all = appointments.value.length;
  const upcoming = appointments.value.filter(
    (a) => a.status === "upcoming"
  ).length;
  const arrived = appointments.value.filter(
    (a) => a.status === "arrived"
  ).length;
  const examining = appointments.value.filter(
    (a) => a.status === "examining"
  ).length;
  const payment = appointments.value.filter(
    (a) => a.status === "payment"
  ).length;

  filters.value = [
    { key: "all", label: "Tất cả", count: all, icon: null },
    { key: "upcoming", label: "Sắp tới", count: upcoming, icon: iconUpcoming },
    { key: "arrived", label: "Đã đến", count: arrived, icon: iconArrived },
    {
      key: "examining",
      label: "Đang khám",
      count: examining,
      icon: iconExamining,
    },
    {
      key: "payment",
      label: "Chờ thanh toán",
      count: payment,
      icon: iconPayment,
    },
  ];
};
```

### **8. Cập nhật Lifecycle Hooks**

```javascript
// Watch tab changes
watch(activeTab, (newTab) => {
  if (newTab === "check-in") fetchWaitingCheckInList();
  else if (newTab === "checked-in") fetchCheckedInList();
  else if (newTab === "list") fetchAllAppointments(); // ⭐ NEW
});

// On mount
onMounted(() => {
  fetchAllAppointments(); // ⭐ Load ngay khi vào trang

  if (activeTab.value === "check-in") fetchWaitingCheckInList();
  else if (activeTab.value === "checked-in") fetchCheckedInList();
});
```

### **9. Thêm Loading & Empty States vào Template**

```vue
<!-- Loading State -->
<div v-if="loadingAppointments" class="...">
  <svg class="animate-spin ...">...</svg>
  <p>Đang tải danh sách...</p>
</div>

<!-- Empty State -->
<div v-else-if="filteredAppointments.length === 0" class="...">
  <svg>...</svg>
  <p>Không có lịch hẹn nào</p>
</div>

<!-- Table -->
<div v-else class="...">
  <table>...</table>
</div>
```

### **10. Thêm nút "Làm mới"**

```vue
<div class="px-6 flex items-center justify-between">
  <h2>Danh sách lịch hẹn</h2>
  
  <!-- ⭐ Nút Làm mới -->
  <button @click="fetchAllAppointments" class="...">
    <svg>🔄</svg>
    <span>Làm mới</span>
  </button>
</div>
```

---

## 🔄 Luồng hoạt động mới

### **Khi vào trang:**

```
1. onMounted()
   ↓
2. fetchAllAppointments()
   ↓
3. API: GET /lich-hen-all?per_page=100&from_date=2025-12-12
   ↓
4. Transform data: Backend format → UI format
   ↓
5. appointments.value = [...]
   ↓
6. updateFilterCounts()
   ↓
7. Hiển thị trong table
```

### **Khi check-in:**

```
1. Click "Check-in" button
   ↓
2. checkIn(appointment) → Lấy originalData
   ↓
3. Mở CheckInModal với appointment
   ↓
4. User xác nhận
   ↓
5. API: POST /lich-hen/{id}/check-in
   ↓
6. handleCheckInSuccess(updatedAppointment)
   ↓
7. Update 3 lists:
   - waitingCheckInAppointments (remove)
   - checkedInAppointments (add)
   - appointments (update status)
   ↓
8. Refresh all lists từ API
   ↓
9. updateFilterCounts()
   ↓
10. Toast success message
```

---

## 📊 Dữ liệu Backend

### **API Response Structure:**

```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "ngay_gio": "2025-12-31 18:30:00",
      "khach_hang": "Nguyễn Văn An",
      "thu_cung": {
        "ten_thu_cung": "Quốc",
        "giong_loai": "Chó Poodle"
      },
      "dich_vu": {
        "ten": "Khám nha khoa"
      },
      "nhan_vien": {
        "id": 5,
        "full_name": "Bác sĩ Nguyễn Văn An",
        "vai_tro": "Bác Sĩ"
      },
      "trang_thai": "confirmed",
      "thoi_gian_checkin": null,
      "ghi_chu": "Khách hàng đặt lịch trước"
    }
  ]
}
```

### **UI Transformed Format:**

```javascript
{
  id: 1,
  appointmentTime: "18:30",
  checkInTime: null,
  source: "scheduled",
  petName: "Quốc",
  hasMale: false,
  ownerName: "Nguyễn Văn An",
  service: "Khám nha khoa",
  doctor: "Bác sĩ Nguyễn Văn An",
  room: "Phòng 102",
  status: "upcoming",
  action: "checkin",
  rowColor: "bg-blue-50",
  originalData: { /* full backend data */ }
}
```

---

## 🎨 UI Changes

### **Tab "Danh sách lịch hẹn":**

- ✅ Hiển thị dữ liệu thật từ API
- ✅ Loading spinner khi fetch data
- ✅ Empty state khi không có data
- ✅ Nút "Làm mới" để reload
- ✅ Filter counts tự động cập nhật
- ✅ Check-in button hoạt động với API thật

### **Check-in Modal:**

- ✅ Nhận dữ liệu từ `originalData`
- ✅ Hiển thị thông tin bác sĩ (nếu có)
- ✅ Gọi API khi xác nhận
- ✅ Refresh tất cả tabs sau khi thành công

### **Tab "Chờ Check-in" & "Đã Check-in":**

- ✅ Giữ nguyên chức năng cũ
- ✅ Tự động cập nhật khi check-in từ tab "Danh sách"

---

## 🐛 Bug Fixes

### **1. Duplicate appointments state**

- ❌ Trước: Có 2 `const appointments = ref([])`
- ✅ Sau: Chỉ 1 state, xóa mock data

### **2. Check-in không cập nhật backend**

- ❌ Trước: Chỉ update local state
- ✅ Sau: Gọi API `checkInAppointment(id)`

### **3. Filter counts không đúng**

- ❌ Trước: Hard-coded numbers
- ✅ Sau: Tự động tính từ `appointments.value`

### **4. Không refresh sau check-in**

- ❌ Trước: Chỉ update 2 tabs
- ✅ Sau: Refresh cả 3 tabs

---

## 🧪 Testing

### **Checklist:**

- [ ] Tab "Danh sách lịch hẹn" hiển thị data từ API
- [ ] Loading spinner xuất hiện khi fetch
- [ ] Empty state hiển thị khi không có lịch
- [ ] Nút "Làm mới" hoạt động
- [ ] Filter counts chính xác
- [ ] Click "Check-in" mở modal
- [ ] Modal hiển thị đúng thông tin
- [ ] Xác nhận check-in gọi API
- [ ] Toast success xuất hiện
- [ ] Lịch biến mất khỏi tab "Chờ Check-in"
- [ ] Lịch xuất hiện ở tab "Đã Check-in"
- [ ] Trạng thái trong tab "Danh sách" chuyển sang "Đang khám"
- [ ] Badge filter cập nhật số lượng

### **Test Cases:**

**TC1: Load danh sách lịch hẹn**

```
1. Mở trang Y tá → Lịch hẹn
2. Kiểm tra: Loading spinner → Danh sách hiển thị
3. Expected: Có lịch hẹn từ ngày hôm nay trở đi
```

**TC2: Check-in từ tab "Danh sách lịch hẹn"**

```
1. Click nút "Check-in" ở lịch có trạng thái "Sắp tới"
2. Modal mở → Xác nhận
3. Expected:
   - Toast "Check-in thành công"
   - Trạng thái chuyển → "Đang khám"
   - Badge "Đang khám" tăng +1
   - Badge "Sắp tới" giảm -1
```

**TC3: Check-in từ tab "Chờ Check-in"**

```
1. Chuyển sang tab "Chờ Check-in"
2. Click "Check-in" → Xác nhận
3. Expected:
   - Lịch biến mất khỏi danh sách
   - Tab "Đã Check-in" có thêm lịch mới
   - Tab "Danh sách" cập nhật trạng thái
```

---

## 📝 Notes

1. **API Endpoint**: `/lich-hen-all` (for staff) thay vì `/lich-hen` (for customer)
2. **Date Filter**: Default lấy từ hôm nay (`from_date: today`)
3. **Per Page**: 100 items để tránh phân trang
4. **originalData**: Lưu full backend response để dùng khi check-in
5. **Transform Logic**: Convert backend format → UI format trong `fetchAllAppointments()`

---

**🎉 Kết quả:**  
✅ Danh sách lịch hẹn hiển thị data thật từ backend  
✅ Check-in hoạt động đầy đủ với API integration  
✅ Tất cả 3 tabs đồng bộ với nhau  
✅ Filter counts tự động cập nhật  
✅ UX mượt mà với loading states

---

**Ngày cập nhật:** 12/12/2025  
**Version:** 2.0.0
