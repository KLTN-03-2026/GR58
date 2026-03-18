# FE API Integration - Quản Lý Lịch Hẹn

## Các thay đổi chính

### 1. **Import API Client**

```javascript
import client from "../../../../utils/api.js";
import { ref, onMounted, computed } from "vue";
```

### 2. **Thêm State mới**

- `isLoading`: Theo dõi trạng thái tải dữ liệu
- `error`: Lưu trữ thông báo lỗi
- `appointments`: Dữ liệu lịch hẹn từ API

### 3. **Hàm Transform Dữ Liệu**

#### `formatDateTime(ngayGio)`

Chuyển đổi định dạng datetime từ backend sang UI format:

- **Input**: `"2025-11-20 09:00:00"` (từ DB)
- **Output**: `{ date: "20/11/2025", time: "09:00" }`

#### `transformAppointment(data)`

Chuyển đổi dữ liệu từ API sang format UI:

```javascript
{
  id: data.id,                              // ID lịch hẹn
  time: "09:00",                            // Giờ từ ngay_gio
  date: "20/11/2025",                       // Ngày từ ngay_gio
  customer: data.khach_hang?.ho_ten,        // Tên khách hàng
  pet: data.thu_cung?.ten_thu_cung,         // Tên thú cưng
  service: data.dich_vu?.ten,               // Tên dịch vụ
  assignedStaff: {...},                     // Thông tin nhân viên
  status: data.trang_thai,                  // Trạng thái lịch hẹn
  paymentStatus: data.thanh_toan?.trang_thai // Trạng thái thanh toán
}
```

### 4. **Fetch Dữ Liệu từ Backend**

#### `fetchAppointments()`

```javascript
// Gọi API endpoint: GET /admin/lich-hen
const response = await client.get("/admin/lich-hen");
// Transform dữ liệu và lưu vào appointments.value
```

**Lưu ý**: Endpoint này tùy thuộc vào routing của bạn. Nếu khác, hãy thay đổi URL tương ứng.

### 5. **Filtered Appointments**

```javascript
const filteredAppointments = computed(() => {
  // Tìm kiếm theo ID, Khách hàng, Pet, Dịch vụ
  if (!searchQuery.value) return appointments.value;

  const query = searchQuery.value.toLowerCase();
  return appointments.value.filter(
    (apt) =>
      apt.id.toLowerCase().includes(query) ||
      apt.customer.toLowerCase().includes(query) ||
      apt.pet.toLowerCase().includes(query) ||
      apt.service.toLowerCase().includes(query)
  );
});
```

### 6. **Lifecycle Hook**

```javascript
onMounted(() => {
  fetchAppointments(); // Tự động tải dữ liệu khi component mount
});
```

### 7. **Cập nhật Methods**

#### `handleCancelAppointmentConfirm(id)`

Giờ đây gọi API để xóa lịch hẹn:

```javascript
const handleCancelAppointmentConfirm = async (id) => {
  try {
    await client.delete(`/admin/lich-hen/${id}`);
    isCancelAppointmentModalOpen.value = false;
    fetchAppointments(); // Làm mới danh sách
  } catch (err) {
    console.error("Error cancelling appointment:", err);
  }
};
```

#### `handleCreateAppointment(data)` & `handleAssignDoctorSubmit(data)`

Thêm gọi `fetchAppointments()` sau khi hoàn thành để làm mới danh sách

## Mapping Dữ Liệu

| Backend Field           | UI Display            | Component                                           |
| ----------------------- | --------------------- | --------------------------------------------------- |
| `id`                    | Mã lịch hẹn           | `{{ appointment.id }}`                              |
| `ngay_gio`              | Thời gian + Ngày      | `{{ appointment.time }}` & `{{ appointment.date }}` |
| `khach_hang.ho_ten`     | Tên khách hàng        | `{{ appointment.customer }}`                        |
| `thu_cung.ten_thu_cung` | Tên thú cưng          | `{{ appointment.pet }}`                             |
| `dich_vu.ten`           | Tên dịch vụ           | `{{ appointment.service }}`                         |
| `nhan_vien`             | Thông tin nhân viên   | `appointment.assignedStaff`                         |
| `trang_thai`            | Trạng thái lịch hẹn   | `{{ appointment.status }}`                          |
| `thanh_toan.trang_thai` | Trạng thái thanh toán | `{{ appointment.paymentStatus }}`                   |

## API Endpoints sử dụng

```
GET    /lich-hen              - Lấy danh sách lịch hẹn
POST   /lich-hen              - Tạo lịch hẹn (nếu cần)
GET    /lich-hen/{id}         - Xem chi tiết lịch hẹn
DELETE /lich-hen/{id}         - Xóa/Hủy lịch hẹn
PATCH  /lich-hen/{id}/ngay-gio - Cập nhật thời gian
```

## Cần chỉnh sửa

1. **✅ URL endpoint**: Đã cập nhật từ `/admin/lich-hen` sang `/lich-hen` (đúng với backend route)

2. **Xử lý lỗi**: Bạn có thể thêm toast messages để thông báo cho user khi có lỗi

3. **Bảo mật**: Nếu endpoint này yêu cầu authorization cho admin only, kiểm tra middleware backend

4. **Status & Payment Status**: Kiểm tra giá trị của `trang_thai` và trạng thái thanh toán từ backend để map đúng với labels

## Hướng dẫn testing

1. Đảm bảo backend đang chạy
2. Kiểm tra `VITE_API_BASE` trong file `.env` hoặc `vite.config.js`
3. Mở DevTools (F12) xem Network tab để theo dõi các API calls
4. Kiểm tra Console để xem các error messages
