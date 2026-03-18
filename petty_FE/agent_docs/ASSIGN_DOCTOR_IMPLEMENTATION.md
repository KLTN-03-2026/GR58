# Hiển Thị Tên Bác Sĩ Đã Chọn - Implementation Summary

## 📋 Yêu Cầu

Sau khi gán bác sĩ cho lịch hẹn, hiển thị tên bác sĩ đã chọn lên giao diện.

## ✅ Giải Pháp Được Triển Khai

### 1. **PhanCongBacSi Modal Component**

**File:** `src/components/Admin/VanHanh/QuanLyLichHen/PhanCongBacSi/index.vue`

#### Thay Đổi:

- Cập nhật `handleSelectStaff()` function để:
  - Gửi `trang_thai: "da_xac_nhan"` trong PUT request để cập nhật trạng thái
  - Emit event với `status: "da_xac_nhan"` để thông báo status change

```javascript
const handleSelectStaff = async (staff) => {
  // ...
  const response = await client.put(`/lich-hen/${props.appointmentId}`, {
    nhan_vien_id: staff.id,
    trang_thai: "da_xac_nhan", // ← Thêm status update
  });

  if (response.data.status) {
    emit("assign", {
      appointmentId: props.appointmentId,
      staffId: staff.id,
      staffName: staff.name,
      status: "da_xac_nhan", // ← Emit status change
    });
  }
};
```

### 2. **QuanLyLichHen Appointment List Component**

**File:** `src/components/Admin/VanHanh/QuanLyLichHen/index.vue`

#### Thay Đổi A: updateAssignDoctorSubmit Function

```javascript
const handleAssignDoctorSubmit = (data) => {
  // Tìm appointment và cập nhật doctor info
  const appointmentIndex = appointments.value.findIndex(
    (apt) => apt.id === data.appointmentId
  );

  if (appointmentIndex !== -1) {
    // Update doctor info với avatar initial
    appointments.value[appointmentIndex].assignedStaff = {
      initial: data.staffName ? data.staffName.charAt(0) : "?",
      name: data.staffName,
      department: "Bác sĩ",
    };

    // Update status to confirmed
    if (data.status) {
      appointments.value[appointmentIndex].status = data.status;
    }
  }

  // Refresh từ server
  setTimeout(() => {
    fetchAppointments();
  }, 500);
};
```

#### Thay Đổi B: Status Label Handler

```javascript
const getStatusLabel = (status) => {
  const labels = {
    confirmed: "Đã xác nhận",
    da_xac_nhan: "Đã xác nhận", // ← Thêm support cho "da_xac_nhan"
    "in-progress": "Đang khám",
    pending: "Chờ xác nhận",
    completed: "Hoàn thành",
    cancelled: "Đã hủy",
  };
  return labels[status] || status;
};
```

#### Thay Đổi C: Status Badge Styling

```vue
<span
  :class="[
    'inline-flex items-center px-2 py-[3px] rounded-lg font-nunito font-medium text-xs leading-4',
    appointment.status === 'confirmed' || appointment.status === 'da_xac_nhan' // ← Support both
      ? 'bg-green-100 text-[#008236]'
      : /* other statuses */
  ]"
>
  {{ getStatusLabel(appointment.status) }}
</span>
```

## 🎯 Workflow Hoàn Chỉnh

```
1. Admin Click "Chưa gán" button
   ↓
2. PhanCongBacSi modal mở hiển thị danh sách bác sĩ
   ↓
3. Admin chọn một bác sĩ
   ↓
4. handleSelectStaff() gửi PUT request với:
   - nhan_vien_id: <doctor_id>
   - trang_thai: "da_xac_nhan"
   ↓
5. Backend cập nhật appointment
   ↓
6. emit("assign", {..., status: "da_xac_nhan"})
   ↓
7. handleAssignDoctorSubmit() cập nhật local state:
   - appointmentData.assignedStaff = {name, initial, department}
   - appointmentData.status = "da_xac_nhan"
   ↓
8. Table render tên bác sĩ + badge "Đã xác nhận" (green)
   ↓
9. fetchAppointments() refresh để lấy data mới từ server
```

## 📊 UI Changes

### Before Assignment

```
[⚠️] Chưa gán  |  Status: Chờ xác nhận (blue)
```

### After Assignment

```
[A] Name Bác Sĩ  |  Status: Đã xác nhận (green)
   Bác Sĩ

[✎] (Edit button để thay đổi bác sĩ)
```

## 🔄 Data Flow

### Appointment Object Structure:

```javascript
{
  id: "LH001",
  customer: "Nguyen Van A",
  pet: "Bella",
  service: "Phẫu thuật xương",
  time: "14:00",
  date: "20/11/2025",

  // Doctor info (displays after assignment)
  assignedStaff: {
    initial: "N",           // ← First letter of doctor name
    name: "Nguyen Van B",   // ← Doctor name
    department: "Bác sĩ"    // ← Title
  },

  // Status (changes from "pending" to "da_xac_nhan")
  status: "da_xac_nhan",    // ← "pending" → "da_xac_nhan" → "in-progress" → "completed"

  paymentStatus: "unpaid",
}
```

## 🔧 Backend Requirements

Backend `LichHenController::update()` method phải:

1. ✅ Accept `nhan_vien_id` parameter
2. ✅ Accept `trang_thai` parameter
3. ✅ Update appointment với 2 fields này
4. ✅ Return updated appointment data with relationships loaded
5. ✅ Call `transformData()` trước return (nếu có)

```php
// Backend expected behavior:
Route::match(['put', 'patch'], '/lich-hen/{lichHen}', [LichHenController::class, 'update']);

// PUT /lich-hen/LH001
// {
//   "nhan_vien_id": 5,
//   "trang_thai": "da_xac_nhan"
// }

// Response:
// {
//   "status": true,
//   "data": {
//     "id": "LH001",
//     "nhan_vien_id": 5,
//     "trang_thai": "da_xac_nhan",
//     "nhan_vien": {...},
//     ...
//   }
// }
```

## 📱 Features

✅ **Immediate UI Update** - Doctor name hiển thị ngay sau khi select  
✅ **Status Change** - Badge tự động chuyển từ "Chờ xác nhận" → "Đã xác nhận"  
✅ **Doctor Avatar** - Hiển thị initial của bác sĩ trong avatar circle  
✅ **Edit Button** - Admin có thể nhấn ✎ để thay đổi bác sĩ  
✅ **Server Sync** - Auto refresh từ server sau 500ms để lấy data mới nhất  
✅ **Error Handling** - Hiển thị lỗi nếu backend không phản hồi

## 🚀 Next Steps

1. Verify backend `LichHenController::update()` implementation
2. Test full workflow end-to-end
3. Optional: Add success toast notification
4. Optional: Add status update to customer appointment page (KhachHang/LichHen)
