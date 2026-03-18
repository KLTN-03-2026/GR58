# Hiển Thị Tên Bác Sĩ Ở Cột "Phụ Trách" - Tóm Tắt Hoàn Thành

## ✅ Tính Năng Đã Hoàn Thiện

### Column "Phụ Trách" (Assigned Staff)

```
┌─────────────────────────────────────────┐
│ Phụ Trách                               │
├─────────────────────────────────────────┤
│                                         │
│ TRƯỚC GÁN BÁC SĨ:                      │
│ [⚠️] Chưa gán  (button orange)         │
│                                         │
│ SAU GÁN BÁC SĨ:                        │
│ [N] Nguyễn Văn B                      │
│     Bác Sĩ                            │
│     [✎] (Edit button)                │
│                                         │
└─────────────────────────────────────────┘
```

## 📊 Data Flow

### 1. Fetch Từ Backend

```javascript
// GET /lich-hen
{
  "id": "LH001",
  "nhan_vien": {
    "id": 5,
    "ho_ten": "Nguyễn Văn B",
    "chuc_vu": "Bác Sĩ"
  },
  "trang_thai": "confirmed"
}
```

### 2. Transform Data

```javascript
transformAppointment(data) {
  let assignedStaff = null;
  if (data.nhan_vien) {
    assignedStaff = {
      initial: "N",              // First letter
      name: "Nguyễn Văn B",      // Doctor name
      department: "Bác Sĩ"       // Position
    };
  }

  return {
    ...
    assignedStaff: assignedStaff,
    ...
  };
}
```

### 3. Render Trong Table

```vue
<!-- Assigned Staff Column -->
<td v-if="appointment.assignedStaff">
  <div class="flex items-center gap-3">
    <!-- Avatar -->
    <div class="w-8 h-8 rounded-full bg-[#cbfbf1]">
      {{ appointment.assignedStaff.initial }}  <!-- "N" -->
    </div>
    
    <!-- Info -->
    <div class="flex flex-col">
      <span>{{ appointment.assignedStaff.name }}</span>      <!-- "Nguyễn Văn B" -->
      <span>{{ appointment.assignedStaff.department }}</span>  <!-- "Bác Sĩ" -->
    </div>
    
    <!-- Edit -->
    <button>[✎]</button>
  </div>
</td>

<!-- When no doctor assigned -->
<td v-else>
  <button @click="handleAssignDoctor(appointment.id)">
    [⚠️] Chưa gán
  </button>
</td>
```

## 🔄 Khi Gán Bác Sĩ

### Step 1: Click "Chưa gán"

→ Mở modal PhanCongBacSi

### Step 2: Chọn Bác Sĩ

```javascript
// PhanCongBacSi emits:
emit("assign", {
  appointmentId: "LH001",
  staffId: 5,
  staffName: "Nguyễn Văn B", // ← Tên bác sĩ được truyền
  status: "confirmed",
});
```

### Step 3: Update Local State

```javascript
handleAssignDoctorSubmit(data) {
  appointments.value[index].assignedStaff = {
    initial: "N",                  // First letter từ data.staffName
    name: "Nguyễn Văn B",          // data.staffName
    department: "Bác Sĩ"
  };

  appointments.value[index].status = "confirmed";

  // Refresh từ server
  fetchAppointments();
}
```

### Step 4: Table Re-render

→ Column "Phụ Trách" hiển thị tên bác sĩ + avatar + edit button

## 📝 Files & Code

### File: `src/components/Admin/VanHanh/QuanLyLichHen/index.vue`

**1. transformAppointment() - Dòng ~454**

```javascript
let assignedStaff = null;
if (data.nhan_vien) {
  assignedStaff = {
    initial: data.nhan_vien.ho_ten ? data.nhan_vien.ho_ten.charAt(0) : "",
    name: data.nhan_vien.ho_ten || "Chưa xác định",
    department: data.nhan_vien.chuc_vu || "",
  };
}
```

**2. Column Template - Dòng ~230-261**

```vue
<td class="px-2 py-[8px]">
  <div v-if="appointment.assignedStaff" class="flex items-center gap-3">
    <div class="w-8 h-8 rounded-full bg-[#cbfbf1]">
      {{ appointment.assignedStaff.initial }}
    </div>
    <div class="flex flex-col">
      <span>{{ appointment.assignedStaff.name }}</span>
      <span>{{ appointment.assignedStaff.department }}</span>
    </div>
    <button>[✎]</button>
  </div>
  <button v-else @click="handleAssignDoctor(appointment.id)">
    [⚠️] Chưa gán
  </button>
</td>
```

**3. handleAssignDoctorSubmit() - Dòng ~574**

```javascript
const handleAssignDoctorSubmit = (data) => {
  const appointmentIndex = appointments.value.findIndex(
    (apt) => apt.id === data.appointmentId
  );

  if (appointmentIndex !== -1) {
    appointments.value[appointmentIndex].assignedStaff = {
      initial: data.staffName ? data.staffName.charAt(0) : "?",
      name: data.staffName,
      department: "Bác Sĩ",
    };

    if (data.status) {
      appointments.value[appointmentIndex].status = data.status;
    }
  }

  setTimeout(() => {
    fetchAppointments();
  }, 500);
};
```

### File: `src/components/Admin/VanHanh/QuanLyLichHen/PhanCongBacSi/index.vue`

**handleSelectStaff() - Dòng ~365**

```javascript
const handleSelectStaff = async (staff) => {
  // ...
  emit("assign", {
    appointmentId: props.appointmentId,
    staffId: staff.id,
    staffName: staff.name, // ← Emit tên bác sĩ
    status: "confirmed",
  });
};
```

## 🎨 UI Display

### Trạng Thái Trước

```
Mã lịch  │ Thời gian │ Khách hàng │ Dịch vụ │ Phụ trách        │ Trạng thái
────────────────────────────────────────────────────────────────────
LH001    │ 14:00    │ Anh A     │ Cắt móng │ [⚠️] Chưa gán   │ Chờ xác nhận
```

### Trạng Thái Sau

```
Mã lịch  │ Thời gian │ Khách hàng │ Dịch vụ │ Phụ trách           │ Trạng thái
──────────────────────────────────────────────────────────────────────────
LH001    │ 14:00    │ Anh A     │ Cắt móng │ [N] Nguyễn Văn B   │ Đã xác nhận
         │          │           │          │     Bác Sĩ [✎]     │ (green)
```

## ✨ Features

✅ Hiển thị tên bác sĩ đã chọn  
✅ Hiển thị avatar với initial (chữ cái đầu)  
✅ Hiển thị chức vụ (Bác Sĩ)  
✅ Có button Edit để thay đổi bác sĩ  
✅ "Chưa gán" button khi chưa có bác sĩ  
✅ Status tự động chuyển sang "Đã xác nhận" (green badge)  
✅ Auto-refresh từ server để lấy data mới nhất

## 🧪 Test

```
1. Mở trang Quản lý Lịch Hẹn
2. Tìm lịch hẹn có "Chưa gán"
3. Click button "Chưa gán"
4. Chọn một bác sĩ từ modal
5. ✅ Kết quả:
   - Tên bác sĩ hiển thị ở column "Phụ trách"
   - Avatar + initial hiển thị
   - Status badge chuyển thành "Đã xác nhận" (green)
   - Edit button [✎] xuất hiện
```

## 🔗 Related Components

- **PhanCongBacSi**: Modal để chọn bác sĩ, emit tên bác sĩ được chọn
- **QuanLyLichHen**: Hiển thị danh sách lịch hẹn, cập nhật UI khi gán bác sĩ
- **API**: GET /lich-hen lấy danh sách, PUT /lich-hen/{id} cập nhật bác sĩ
