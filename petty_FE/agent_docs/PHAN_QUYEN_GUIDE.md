# 🔐 Hệ Thống Phân Quyền - Hướng Dẫn Đầy Đủ

## 📋 Tổng Quan

Hệ thống phân quyền cho phép quản trị viên cấu hình các quyền truy cập cho từng vai trò trong phòng khám.

---

## 🎯 Các Vai Trò Mặc Định

### 👑 Admin (Quản trị viên)

- **Màu sắc**: Xanh dương
- **Mô tả**: Toàn quyền quản lý hệ thống
- **Quyền đặc biệt**: Có thể phân quyền cho các vai trò khác

### 👨‍⚕️ Bác sĩ

- **Màu sắc**: Xanh lá
- **Mô tả**: Khám bệnh, kê đơn, quản lý bệnh án
- **Phạm vi**: Lịch hẹn, bệnh án, dịch vụ

### 👤 Nhân viên

- **Màu sắc**: Tím
- **Mô tả**: Hỗ trợ và quản lý
- **Phạm vi**: Khách hàng, thanh toán, lịch hẹn

### 👩‍⚕️ Y tá

- **Màu sắc**: Hồng
- **Mô tả**: Hỗ trợ bác sĩ
- **Phạm vi**: Xem thông tin, hỗ trợ khám

### 📋 Lễ tân

- **Màu sắc**: Cam
- **Mô tả**: Tiếp nhận khách hàng
- **Phạm vi**: Lịch hẹn, khách hàng

---

## 📦 Nhóm Quyền Chi Tiết

### 1️⃣ Nhóm Lịch Hẹn

```
- lich_hen_xem: Xem danh sách lịch hẹn
- lich_hen_tao: Tạo lịch hẹn mới
- lich_hen_sua: Chỉnh sửa lịch hẹn
- lich_hen_xoa: Xóa lịch hẹn
- lich_hen_xac_nhan: Xác nhận/hủy lịch hẹn
```

### 2️⃣ Nhóm Lịch Làm Việc

```
- lich_lam_viec_xem: Xem lịch làm việc
- lich_lam_viec_tao: Tạo lịch làm việc
```

### 3️⃣ Nhóm Tài Chính

```
- tai_chinh_xem_doanh_thu: Xem báo cáo doanh thu
- tai_chinh_thu_tien: Thu tiền từ khách hàng
- tai_chinh_hoan_tien: Hoàn tiền cho khách hàng
```

### 4️⃣ Nhóm Phiếu Chi

```
- phieu_chi_xem: Xem danh sách phiếu chi
- phieu_chi_tao: Tạo phiếu chi mới
- phieu_chi_sua: Chỉnh sửa phiếu chi
- phieu_chi_xoa: Xóa phiếu chi
- phieu_chi_xuat_pdf: Xuất phiếu chi PDF
- phieu_chi_thanh_toan: Thanh toán phiếu chi
```

### 5️⃣ Nhóm Kho Thuốc

```
- hang_hoa_xem: Xem tồn kho
- hang_hoa_tao: Thêm thuốc/vật tư mới
- hang_hoa_sua: Chỉnh sửa thông tin
- hang_hoa_xoa: Xóa thuốc/vật tư
- hang_hoa_doi_trang_thai: Đổi trạng thái hoạt động
```

### 6️⃣ Nhóm Nhập Kho

```
- phieu_nhap_kho_xem: Xem phiếu nhập
- phieu_nhap_kho_tao: Tạo phiếu nhập kho
- phieu_nhap_kho_doi_trang_thai: Duyệt/từ chối phiếu
- phieu_nhap_kho_xuat_pdf: Xuất phiếu nhập PDF
- phieu_nhap_kho_xoa: Xóa phiếu nhập
```

### 7️⃣ Nhóm Kiểm Kê

```
- kiem_ke_xem: Xem phiếu kiểm kê
- kiem_ke_tao: Tạo phiếu kiểm kê
- kiem_ke_sua: Điều chỉnh kiểm kê
- kiem_ke_xoa: Xóa phiếu kiểm kê
```

### 8️⃣ Nhóm Bệnh Án

```
- thu_cung_xem: Xem chi tiết bệnh án
- thu_cung_tao: Tạo hồ sơ bệnh án
- thu_cung_sua: Kê đơn thuốc
- thu_cung_xoa: Xóa bệnh án
```

### 9️⃣ Nhóm Khác

```
- dich_vu_xem: Xem danh sách dịch vụ
- khach_hang_xem: Xem thông tin khách hàng
- nhan_vien_xem: Xem danh sách nhân viên
- upload_file: Upload hình ảnh/tài liệu
```

---

## 🚀 API Endpoints

### 1. Lấy danh sách vai trò

```http
GET /api/phan-quyen
Authorization: Bearer {token}
```

**Response:**

```json
{
  "success": true,
  "message": "Lấy danh sách phân quyền thành công",
  "data": [
    {
      "id": 1,
      "ma_vai_tro": "admin",
      "ten_vai_tro": "Admin",
      "lich_hen_xem": 1,
      "lich_hen_tao": 1,
      ...
    }
  ]
}
```

### 2. Lấy chi tiết vai trò

```http
GET /api/phan-quyen/{id}
Authorization: Bearer {token}
```

### 3. Cập nhật quyền

```http
PATCH /api/phan-quyen/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "lich_hen_xem": 1,
  "lich_hen_tao": 0,
  "tai_chinh_xem_doanh_thu": 1
}
```

### 4. Lấy danh sách tất cả quyền

```http
GET /api/phan-quyen/danh-sach/tat-ca-quyen
Authorization: Bearer {token}
```

---

## 💻 Cấu Trúc Code

### 1. API Client (`src/utils/phanQuyen.js`)

```javascript
import apiClient from "@/config/apiClient";

export const phanQuyenAPI = {
  getAll() {
    return apiClient.get("/phan-quyen");
  },

  getById(id) {
    return apiClient.get(`/phan-quyen/${id}`);
  },

  update(id, data) {
    return apiClient.patch(`/phan-quyen/${id}`, data);
  },

  getAllPermissions() {
    return apiClient.get("/phan-quyen/danh-sach/tat-ca-quyen");
  },
};
```

### 2. Component (`src/components/Admin/CauHinh/PhanQuyen/index.vue`)

**Features:**

- ✅ Hiển thị danh sách vai trò với accordion
- ✅ Group quyền theo chức năng
- ✅ Toggle checkbox cho từng quyền
- ✅ Đếm số quyền đã bật
- ✅ Quick actions: Bật/Tắt tất cả
- ✅ Lưu từng vai trò hoặc lưu tất cả
- ✅ Loading states và error handling

### 3. Router (`src/router/index.js`)

```javascript
{
  path: "phan-quyen",
  component: () => import("../components/Admin/CauHinh/PhanQuyen/index.vue"),
}
```

### 4. Menu Config (`src/config/menuConfig.js`)

```javascript
{
  key: 'phanQuyen',
  label: 'Phân quyền',
  icon: 'https://www.figma.com/api/mcp/asset/10c0e793-cad4-4588-83be-8f9c526e34ee',
  path: '/admin/phan-quyen',
  type: 'single'
}
```

---

## 🎨 Giao Diện

### Header

- Tiêu đề: "Phân quyền"
- Button: "Lưu tất cả thay đổi" (màu xanh lá)

### Role Card

Mỗi vai trò hiển thị:

- **Icon màu**: Theo vai trò (emoji)
- **Tên vai trò**: Font chữ lớn, đậm
- **Mô tả**: Text màu xám nhạt
- **Số quyền**: "X quyền được bật"
- **Chevron icon**: Mở/đóng accordion

### Permissions Grid

- Layout: 3 cột responsive (1 col trên mobile)
- Mỗi nhóm có:
  - **Header**: Icon + tên nhóm
  - **Checkboxes**: Danh sách quyền với label

### Quick Actions

- ✓ Bật tất cả (màu xanh lá)
- ✗ Tắt tất cả (màu đỏ)
- Lưu vai trò này (màu teal)

### Info Cards (Bottom)

3 thẻ thông tin về vai trò chính:

- Admin (xanh dương)
- Bác sĩ (xanh lá)
- Nhân viên (tím)

---

## 📱 Responsive Design

### Desktop (lg: 1024px+)

- Permissions grid: 3 cột
- Role cards: Full width
- Info cards: 3 cột

### Tablet (md: 768px+)

- Permissions grid: 2 cột
- Info cards: 3 cột

### Mobile (< 768px)

- Permissions grid: 1 cột
- Info cards: 1 cột
- Buttons stack vertically

---

## 🔄 Workflow Sử Dụng

### Cách 1: Chỉnh sửa từng vai trò

1. Vào `/admin/phan-quyen`
2. Click vào vai trò muốn chỉnh sửa
3. Accordion mở ra → hiển thị các nhóm quyền
4. Toggle checkbox theo nhu cầu
5. Click "Lưu vai trò này"
6. Hệ thống gọi API `PATCH /phan-quyen/{id}`

### Cách 2: Bật/Tắt nhanh

1. Click accordion để mở vai trò
2. Click "✓ Bật tất cả" để enable tất cả quyền
3. Hoặc "✗ Tắt tất cả" để disable tất cả
4. Click "Lưu vai trò này"

### Cách 3: Lưu hàng loạt

1. Mở nhiều vai trò
2. Chỉnh sửa quyền cho từng vai trò
3. Click "Lưu tất cả thay đổi" ở header
4. Confirm popup
5. Hệ thống lưu tuần tự tất cả vai trò

---

## ⚠️ Lưu Ý Quan Trọng

### 1. Bảo Mật

- ❗ Chỉ Admin mới có thể truy cập phân quyền
- ❗ Middleware `EnsureAdmin` bảo vệ tất cả API
- ❗ Không cho phép tự phân quyền cho chính mình

### 2. Database

- Bảng: `phan_quyen`
- Mỗi quyền là 1 column type `tinyint` (0 hoặc 1)
- `ma_vai_tro`: Unique identifier (admin, bac_si, etc.)

### 3. Performance

- Sử dụng `PATCH` thay vì `PUT` để chỉ gửi fields thay đổi
- Accordion giúp giảm DOM render
- Lazy load permissions chỉ khi mở accordion

### 4. UX

- ✅ Loading spinner khi tải dữ liệu
- ✅ Disable buttons khi đang lưu
- ✅ Alert thông báo thành công/lỗi
- ✅ Confirm trước khi lưu tất cả
- ✅ Auto-expand vai trò đầu tiên

---

## 🐛 Xử Lý Lỗi

### Lỗi API

```javascript
try {
  const response = await phanQuyenAPI.update(id, data);
  if (!response.data.success) {
    alert("Có lỗi xảy ra: " + response.data.message);
  }
} catch (error) {
  if (error.response?.data?.message) {
    alert("Có lỗi xảy ra: " + error.response.data.message);
  } else {
    alert("Có lỗi xảy ra khi lưu quyền!");
  }
}
```

### Validation

- Backend tự động validate quyền hợp lệ
- Frontend không validate vì quyền là boolean (0/1)

---

## 🎯 Các Tình Huống Thực Tế

### Tình huống 1: Thêm Y tá mới

```
Quyền cần bật:
✓ lich_hen_xem
✓ thu_cung_xem
✓ khach_hang_xem
✓ dich_vu_xem

Quyền tắt:
✗ tai_chinh_xem_doanh_thu
✗ phieu_chi_*
✗ hang_hoa_xoa
```

### Tình huống 2: Bác sĩ mới

```
Quyền cần bật:
✓ lich_hen_xem
✓ lich_hen_xac_nhan
✓ thu_cung_* (tất cả)
✓ dich_vu_xem
✓ khach_hang_xem

Quyền tắt:
✗ tai_chinh_*
✗ phieu_chi_*
✗ hang_hoa_xoa
```

### Tình huống 3: Kế toán

```
Quyền cần bật:
✓ tai_chinh_* (tất cả)
✓ phieu_chi_* (tất cả)
✓ hang_hoa_xem

Quyền tắt:
✗ thu_cung_*
✗ lich_lam_viec_tao
```

---

## 🔮 Tính Năng Tương Lai

### Phase 2

- [ ] Tạo vai trò tùy chỉnh
- [ ] Xóa vai trò (nếu không có user)
- [ ] Copy quyền từ vai trò khác
- [ ] Lịch sử thay đổi quyền

### Phase 3

- [ ] Phân quyền theo phòng ban
- [ ] Quyền tạm thời (có thời hạn)
- [ ] Quyền theo địa điểm
- [ ] Advanced permissions (CRUD granular)

---

## 📞 Support

- **Developer**: GitHub Copilot
- **Date**: December 6, 2025
- **Version**: 1.0.0

---

## ✅ Checklist Hoàn Thành

- [x] Tạo API client (`phanQuyen.js`)
- [x] Tạo component phân quyền
- [x] Thêm route `/admin/phan-quyen`
- [x] Thêm menu item vào sidebar
- [x] Implement load danh sách vai trò
- [x] Implement load danh sách quyền
- [x] Implement update quyền
- [x] Implement toggle checkbox
- [x] Implement quick actions
- [x] Implement lưu tất cả
- [x] Responsive design
- [x] Loading states
- [x] Error handling
- [x] Viết tài liệu

---

**Happy Coding! 🚀**
