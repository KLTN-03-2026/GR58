# 🔧 HƯỚNG DẪN SỬA LỊCH ĐĂNG KÝ CA LÀM VIỆC - FRONTEND

## 📋 Tổng quan

Sửa lại frontend để phù hợp với backend API mới cho chức năng **Lịch đăng ký ca làm việc**.

---

## 🎯 Các thay đổi chính

### 1. **Tạo Service mới: `lichDangKyService.js`**

File: `src/services/lichDangKyService.js`

#### Các API endpoints:

| Chức năng                  | Endpoint                                 | Method | Người dùng      |
| -------------------------- | ---------------------------------------- | ------ | --------------- |
| Nhân viên đăng ký ca       | `/lich-dang-ky/dang-ky-nhan-vien`        | POST   | NhanVien        |
| Lấy lịch của tôi           | `/lich-dang-ky/lich-cua-toi`             | GET    | NhanVien, Admin |
| Lấy tất cả lịch đăng ký    | `/lich-dang-ky`                          | GET    | NhanVien, Admin |
| Lấy lịch chưa xác nhận     | `/lich-dang-ky/chua-xac-nhan`            | GET    | NhanVien, Admin |
| Lấy lịch đã xác nhận       | `/lich-dang-ky/da-xac-nhan`              | GET    | NhanVien, Admin |
| Lấy ca đã xác nhận của tôi | `/lich-dang-ky/ca-da-xac-nhan-cua-toi`   | GET    | NhanVien, Admin |
| Lấy danh sách từ chối      | `/lich-dang-ky/danh-sach-tu-choi`        | GET    | NhanVien, Admin |
| Xác nhận lịch              | `/lich-dang-ky/{id}/xac-nhan`            | PATCH  | Admin           |
| Từ chối lịch               | `/lich-dang-ky/{id}/tu-choi`             | PATCH  | Admin           |
| Đổi trạng thái             | `/lich-dang-ky/{id}/doi-trang-thai`      | PATCH  | Admin           |
| Chi tiết lịch              | `/lich-dang-ky/{id}`                     | GET    | NhanVien, Admin |
| Cập nhật lịch              | `/lich-dang-ky/{id}`                     | PUT    | NhanVien, Admin |
| Xóa lịch                   | `/lich-dang-ky/{id}`                     | DELETE | NhanVien, Admin |
| Danh sách theo nhân viên   | `/lich-dang-ky/danh-sach-theo-nhan-vien` | GET    | Admin           |
| Lấy trạng thái             | `/lich-dang-ky/trang-thai`               | GET    | All             |

---

## 📦 Service Functions

### 1. Nhân viên đăng ký ca làm việc

```javascript
import { dangKyNhanVien } from "@/services/lichDangKyService";

const submitRegistration = async () => {
  try {
    const response = await dangKyNhanVien({
      ngay_gio: "2025-12-15 14:30:00",
      ghi_chu: "Ca chiều phòng khám 1",
    });

    console.log("Đăng ký thành công:", response.data);
    // response.data: { id, ngay_gio, ghi_chu, trang_thai: 'chua_xac_nhan', ... }
  } catch (error) {
    console.error("Lỗi đăng ký:", error);
  }
};
```

### 2. Lấy lịch đăng ký của nhân viên

```javascript
import { getLichCuaToi } from "@/services/lichDangKyService";

// Nhân viên xem lịch của mình
const fetchMyShifts = async () => {
  const response = await getLichCuaToi({
    trang_thai: "chua_xac_nhan", // Optional filter
    per_page: 20,
  });

  console.log("Lịch của tôi:", response.data);
};

// Admin xem lịch của nhân viên cụ thể
const fetchEmployeeShifts = async (nhanVienId) => {
  const response = await getLichCuaToi({
    nhan_vien_id: nhanVienId,
    per_page: 20,
  });

  console.log("Lịch nhân viên:", response.data);
};
```

### 3. Lấy ca đã xác nhận (đã duyệt)

```javascript
import { getCaDaXacNhanCuaToi } from "@/services/lichDangKyService";

const fetchApprovedShifts = async () => {
  const response = await getCaDaXacNhanCuaToi({
    tu_ngay: "2025-12-01",
    den_ngay: "2025-12-31",
    per_page: 500,
  });

  const approvedShifts = response.data?.data || response.data || [];
  console.log("Ca đã duyệt:", approvedShifts);
};
```

### 4. Admin xác nhận/từ chối lịch

```javascript
import { xacNhanLich, tuChoiLich } from "@/services/lichDangKyService";

// Xác nhận lịch
const confirmShift = async (id) => {
  const response = await xacNhanLich(id, {
    admin_id: currentUser.id,
    lich_lam_viec_id: 123, // Optional
  });

  console.log("Xác nhận thành công:", response.data);
  // response.data.trang_thai === "da_xac_nhan"
};

// Từ chối lịch
const rejectShift = async (id) => {
  const response = await tuChoiLich(id, {
    admin_id: currentUser.id,
    ghi_chu: "Lý do từ chối...",
  });

  console.log("Từ chối thành công:", response.data);
  // response.data.trang_thai === "tu_choi"
};
```

---

## 🔄 FLOW HOẠT ĐỘNG

### 1️⃣ **Nhân viên đăng ký ca làm việc**

```
Nhân viên (Doctor/LichLamViec/index.vue)
    ↓
Click "Đăng ký ca"
    ↓
Mở Modal (DangKyCa/index.vue)
    ↓
Nhập: ngay_gio, ghi_chu
    ↓
POST /lich-dang-ky/dang-ky-nhan-vien
{
    "ngay_gio": "2025-12-15 14:30:00",
    "ghi_chu": "Ca chiều phòng 1"
}
    ↓
Backend tạo record với:
{
    "nhan_vien_id": user.id (auto),
    "trang_thai": "chua_xac_nhan",
    ...
}
    ↓
Response: success = true, data = {...}
    ↓
Frontend: showSuccessToast() + refresh danh sách
```

### 2️⃣ **Admin duyệt ca đăng ký**

```
Admin (QuanLyLichLamViec/index.vue)
    ↓
GET /lich-dang-ky/chua-xac-nhan
    ↓
Hiển thị danh sách chờ duyệt
    ↓
Click "Duyệt" một lịch
    ↓
PATCH /lich-dang-ky/{id}/xac-nhan
{
    "admin_id": admin.id,
    "lich_lam_viec_id": 123
}
    ↓
Backend update: trang_thai = "da_xac_nhan"
    ↓
Frontend: Lịch chuyển sang "Đã duyệt"
```

### 3️⃣ **Hiển thị ca đã duyệt lên lịch làm việc**

```
Doctor/LichLamViec/index.vue
    ↓
fetchScheduleData() được gọi
    ↓
Bước 1: GET /lich-lam-viec/cua-toi
    ├── Lấy lịch làm việc chính thức (admin phân công)
    └── Lấy lịch hẹn khách hàng
    ↓
Bước 2: GET /lich-dang-ky/ca-da-xac-nhan-cua-toi
    └── Lấy ca đăng ký đã được admin duyệt
    ↓
Merge cả 3 loại vào calendar:
    ├── Lịch làm việc chính thức → Ca sáng/chiều/tối
    ├── Lịch hẹn → Hiển thị số khách
    └── Ca đăng ký đã duyệt → Slot "Phân công"
    ↓
Hiển thị trên UI calendar
```

---

## 📊 CẤU TRÚC DỮ LIỆU

### Request: Nhân viên đăng ký ca

```javascript
POST /lich-dang-ky/dang-ky-nhan-vien

{
  "ngay_gio": "2025-12-15 14:30:00",  // Required, format: Y-m-d H:i:s, must be future
  "ghi_chu": "Ca chiều phòng 1"       // Optional
}
```

### Response: Đăng ký thành công

```javascript
{
  "success": true,
  "message": "Đăng ký lịch làm việc thành công, chờ xác nhận",
  "data": {
    "id": 123,
    "ngay_gio": "2025-12-15 14:30:00",
    "ghi_chu": "Ca chiều phòng 1",
    "trang_thai": "chua_xac_nhan",  // chưa xác nhận
    "nhan_vien_id": 5,
    "admin_id": null,
    "lich_lam_viec_id": null,
    "khach_hang_id": null,
    "thu_cung_id": null,
    "dich_vu_id": null,
    "created_at": "2025-12-12 10:00:00",
    "updated_at": "2025-12-12 10:00:00",
    "nhanVien": {
      "id": 5,
      "full_name": "Bs. Nguyễn Văn A",
      "email": "nguyenvana@email.com"
    }
  }
}
```

### Response: Lấy danh sách lịch chưa xác nhận

```javascript
GET /lich-dang-ky/chua-xac-nhan

{
  "success": true,
  "message": "Lấy danh sách lịch chưa xác nhận thành công",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 123,
        "ngay_gio": "2025-12-15 14:30:00",
        "ghi_chu": "Ca chiều phòng 1",
        "trang_thai": "chua_xac_nhan",
        "ten_nhan_vien": "Bs. Nguyễn Văn A",  // full_name
        "email_nhan_vien": "nguyenvana@email.com",
        "nhan_vien_id": 5,
        "created_at": "2025-12-12 10:00:00"
      }
    ],
    "total": 10,
    "per_page": 15
  }
}
```

### Response: Lấy ca đã xác nhận

```javascript
GET /lich-dang-ky/ca-da-xac-nhan-cua-toi

{
  "success": true,
  "message": "Lấy danh sách ca đã đăng ký thành công",
  "data": {
    "data": [
      {
        "id": 124,
        "ngay_gio": "2025-12-15 14:30:00",
        "ghi_chu": "Ca chiều phòng 1",
        "trang_thai": "da_xac_nhan",  // Đã xác nhận
        "nhan_vien_id": 5,
        "admin_id": 1,
        "lich_lam_viec_id": 456,
        "nhanVien": {...},
        "admin": {...},
        "lichLamViec": {...}
      }
    ]
  }
}
```

---

## 🎨 HIỂN THỊ TRÊN UI

### DangKyCa Modal (DangKyCa/index.vue)

```vue
<template>
  <div class="modal">
    <h2>Đăng ký ca trực</h2>
    <form @submit.prevent="submitRegistration">
      <!-- Ngày Giờ (Required) -->
      <input v-model="formData.ngay_gio" type="datetime-local" required />

      <!-- Ghi Chú (Optional) -->
      <textarea
        v-model="formData.ghi_chu"
        placeholder="Nhập thông tin ca trực..."
      ></textarea>

      <button type="submit">Đăng ký</button>
    </form>
  </div>
</template>

<script setup>
const formData = ref({
  ngay_gio: "",
  ghi_chu: "",
});

const submitRegistration = async () => {
  // Convert "2025-12-15T14:30" → "2025-12-15 14:30:00"
  const formattedDateTime = formData.value.ngay_gio.replace("T", " ") + ":00";

  const payload = {
    ngay_gio: formattedDateTime,
    ghi_chu: formData.value.ghi_chu || null,
  };

  const response = await api.post("/lich-dang-ky/dang-ky-nhan-vien", payload);

  if (response.data.success) {
    showSuccessToast("✨ Đăng ký thành công!");
    emit("success", response.data.data);
    closeModal();
  }
};
</script>
```

### Hiển thị trên Calendar

```
┌────────────────────────────────────────────────┐
│  T2    T3    T4    T5    T6    T7    CN        │
├────────────────────────────────────────────────┤
│ Ca sáng (7h-12h)                               │
│  ✅    -     ✅    -     -     -     📅        │
├────────────────────────────────────────────────┤
│ Ca chiều (13h-18h)                             │
│  -     ✅    -     📅    -     -     -         │
├────────────────────────────────────────────────┤
│ Ca tối (18h-22h)                               │
│  -     -     -     -     ✅    -     -         │
├────────────────────────────────────────────────┤
│ Phân công (Ca đăng ký)                         │
│  -     🔵    -     -     -     🔵    -         │
└────────────────────────────────────────────────┘

Legend:
✅ = Ca làm việc chính thức (admin phân công)
📅 = Lịch hẹn khách hàng
🔵 = Ca đăng ký đã được admin duyệt
```

---

## 🔍 CÁC TRẠNG THÁI (trang_thai)

| Giá trị         | Tên hiển thị | Ý nghĩa                                    |
| --------------- | ------------ | ------------------------------------------ |
| `chua_xac_nhan` | ⏳ Chờ duyệt | Nhân viên mới đăng ký, chờ admin xét duyệt |
| `da_xac_nhan`   | ✅ Đã duyệt  | Admin đã phê duyệt, ca chính thức          |
| `tu_choi`       | ❌ Từ chối   | Admin từ chối ca đăng ký                   |

---

## ⚠️ LƯU Ý QUAN TRỌNG

### 1. Format Datetime

- **Frontend input:** `type="datetime-local"` → `"2025-12-15T14:30"`
- **Convert trước khi gửi:** `.replace("T", " ") + ":00"` → `"2025-12-15 14:30:00"`
- **Backend expect:** `Y-m-d H:i:s` format
- **Backend validate:** `date_format:Y-m-d H:i:s|after:now`

### 2. Auto-fill Fields

Khi nhân viên đăng ký qua `/dang-ky-nhan-vien`:

- ✅ **Backend tự động set:** `nhan_vien_id` từ `$request->user()->id`
- ✅ **Backend tự động set:** `trang_thai` = `"chua_xac_nhan"`
- ❌ **Không cần gửi:** `nhan_vien_id`, `trang_thai`, `admin_id`, `lich_lam_viec_id`

### 3. Permission Check

- **Nhân viên:**
  - ✅ Đăng ký ca của chính mình
  - ✅ Xem lịch của chính mình
  - ❌ Không thể duyệt/từ chối
- **Admin:**
  - ✅ Xem tất cả lịch đăng ký
  - ✅ Xác nhận/từ chối lịch
  - ✅ Xem lịch theo nhân viên
  - ⚠️ Phải truyền `nhan_vien_id` khi gọi `/lich-cua-toi`

### 4. Response Structure

```javascript
// Endpoint trả về pagination
{
  success: true,
  data: {
    current_page: 1,
    data: [...],     // ← Actual data here
    total: 50,
    per_page: 15
  }
}

// Endpoint trả về trực tiếp
{
  success: true,
  data: [...]        // ← Actual data here
}

// Frontend xử lý:
const items = response.data?.data || response.data || [];
```

---

## ✅ CHECKLIST HOÀN THÀNH

- [x] Tạo `lichDangKyService.js` với đầy đủ functions
- [x] Update `Doctor/LichLamViec/index.vue` import service mới
- [x] Sửa `fetchScheduleData()` dùng `getCaDaXacNhanCuaToi()`
- [x] Sửa `fetchRegisteredShifts()` dùng `getDanhSachLichDangKy()`
- [x] Giữ nguyên `DangKyCa/index.vue` (đã đúng)
- [x] Tạo tài liệu hướng dẫn đầy đủ

---

## 🚀 CÁC API ENDPOINTS CHO ADMIN

### Admin - Quản lý lịch đăng ký

```javascript
// Xem tất cả lịch chưa xác nhận
GET /lich-dang-ky/chua-xac-nhan
Response: Danh sách { id, ngay_gio, ten_nhan_vien, ... }

// Xác nhận một lịch
PATCH /lich-dang-ky/{id}/xac-nhan
Body: { admin_id, lich_lam_viec_id }

// Từ chối một lịch
PATCH /lich-dang-ky/{id}/tu-choi
Body: { admin_id, ghi_chu }

// Đổi trạng thái bất kỳ
PATCH /lich-dang-ky/{id}/doi-trang-thai
Body: { trang_thai, admin_id, ghi_chu }

// Xem danh sách theo nhân viên
GET /lich-dang-ky/danh-sach-theo-nhan-vien
Response: [{ nhan_vien: {...}, thong_ke: {...}, danh_sach_lich: [...] }]
```

---

**Tạo bởi:** GitHub Copilot  
**Ngày:** 12/12/2025  
**Files đã tạo/sửa:**

- ✅ `src/services/lichDangKyService.js` (NEW)
- ✅ `src/components/Doctor/LichLamViec/index.vue` (UPDATED)
- ✅ `src/components/Doctor/LichLamViec/DangKyCa/index.vue` (OK - Không cần sửa)
