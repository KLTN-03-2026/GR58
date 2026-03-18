# Hướng Dẫn Đăng Nhập Nhân Viên

## 📋 Tổng Quan

Hệ thống hỗ trợ đăng nhập cho **3 loại người dùng**:

1. **Khách hàng** - Người dùng thông thường
2. **Admin** - Quản trị viên hệ thống
3. **Nhân viên** - Nhân viên phòng khám với các vai trò khác nhau

## 🔐 API Đăng Nhập

### 1. Đăng Nhập Admin

**Endpoint:** `POST /api/admin/dang-nhap`

**Request Body:**

```json
{
    "email": "admin@example.com",
    "password": "password123"
}
```

**Response Success (200):**

```json
{
  "status": true,
  "message": "Đăng nhập thành công.",
  "data": {
    "id": 1,
    "ho_ten": "Admin Hệ Thống",
    "email": "admin@example.com",
    "phan_quyen_id": 1,
    "phan_quyen": {
      "id": 1,
      "ma_vai_tro": "admin",
      "ten_vai_tro": "Admin",
      "lich_hen_xem": true,
      "lich_hen_tao": true,
      ...
    }
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz123456"
}
```

---

### 2. Đăng Nhập Nhân Viên

**Endpoint:** `POST /api/nhan-vien/dang-nhap`

**Request Body:**

```json
{
    "email": "bacsi@example.com",
    "password": "password123"
}
```

**Response Success (200):**

```json
{
  "status": true,
  "message": "Đăng nhập thành công.",
  "data": {
    "id": 1,
    "full_name": "Bác sĩ Nguyễn Văn A",
    "email": "bacsi@example.com",
    "vai_tro": "bac_si",
    "trang_thai": "hoat_dong",
    "phan_quyen_id": 2,
    "phan_quyen": {
      "id": 2,
      "ma_vai_tro": "bac_si",
      "ten_vai_tro": "Bác sĩ",
      "lich_hen_xem": true,
      "lich_hen_tao": false,
      "hang_hoa_xoa": false,
      ...
    }
  },
  "token": "2|zyxwvutsrqponmlkjihgfedcba654321",
  "redirect_url": "/doctor/dashboard"
}
```

**Response Error - Tài khoản bị khóa (403):**

```json
{
    "status": false,
    "message": "Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên."
}
```

**Response Error - Sai email/password (401):**

```json
{
    "status": false,
    "message": "Email hoặc mật khẩu không đúng."
}
```

---

### 3. Đăng Xuất

**Endpoint:** `POST /api/admin/dang-xuat` (cho Admin)
**Endpoint:** `POST /api/nhan-vien/dang-xuat` (cho Nhân viên)

**Headers:**

```
Authorization: Bearer {token}
```

**Response Success (200):**

```json
{
    "status": true,
    "message": "Đăng xuất thành công."
}
```

---

### 4. Lấy Thông Tin User Hiện Tại

**Endpoint:** `GET /api/user`

**Headers:**

```
Authorization: Bearer {token}
```

**Response Success (200):**

```json
{
  "status": true,
  "data": {
    "id": 1,
    "full_name": "Bác sĩ Nguyễn Văn A",
    "email": "bacsi@example.com",
    "phan_quyen": {
      "id": 2,
      "ma_vai_tro": "bac_si",
      "ten_vai_tro": "Bác sĩ",
      "lich_hen_xem": true,
      "lich_hen_tao": false,
      ...
    }
  },
  "user_type": "nhan_vien"
}
```

## 👥 Các Vai Trò Nhân Viên

### 1. Bác Sĩ (`bac_si`)

**Redirect URL:** `/doctor/dashboard`

**Quyền mặc định:**

-   ✅ Xem lịch hẹn
-   ✅ Xem hồ sơ bệnh án
-   ✅ Tạo/sửa hồ sơ bệnh án
-   ✅ Kê đơn thuốc
-   ❌ Xóa hàng hóa
-   ❌ Quản lý phiếu chi

### 2. Điều Dưỡng (`dieu_duong`)

**Redirect URL:** `/nurse/dashboard`

**Quyền mặc định:**

-   ✅ Xem lịch hẹn
-   ✅ Xem kho thuốc
-   ✅ Tạo phiếu nhập kho
-   ✅ Kiểm kê
-   ❌ Xóa nhân viên
-   ❌ Xem tài chính

### 3. Lễ Tân (`le_tan`)

**Redirect URL:** `/receptionist/dashboard`

**Quyền mặc định:**

-   ✅ Xem lịch hẹn
-   ✅ Tạo lịch hẹn
-   ✅ Xác nhận lịch hẹn
-   ✅ Thu tiền
-   ✅ Quản lý khách hàng
-   ❌ Xem hồ sơ bệnh án
-   ❌ Quản lý kho

### 4. Trợ Lý (`tro_ly`)

**Quyền mặc định:**

-   ✅ Xem lịch hẹn
-   ✅ Xem kho thuốc
-   ✅ Tạo phiếu chi
-   ❌ Xóa dữ liệu
-   ❌ Quản lý nhân viên

## 🔒 Kiểm Tra Quyền

Khi nhân viên gọi API, hệ thống tự động kiểm tra quyền:

### Ví dụ 1: Lễ tân cố xóa hàng hóa

```bash
# Lễ tân đăng nhập
POST /api/nhan-vien/dang-nhap
{
  "email": "letan@example.com",
  "password": "123456"
}

# Lễ tân cố xóa hàng hóa (KHÔNG CÓ QUYỀN)
DELETE /api/hang-hoa/1
Authorization: Bearer {token_le_tan}

# Response: 403 Forbidden
{
  "success": false,
  "message": "Bạn không có quyền thực hiện chức năng này",
  "required_permission": "hang_hoa_xoa"
}
```

### Ví dụ 2: Bác sĩ xem lịch hẹn

```bash
# Bác sĩ đăng nhập
POST /api/nhan-vien/dang-nhap
{
  "email": "bacsi@example.com",
  "password": "123456"
}

# Bác sĩ xem lịch hẹn (CÓ QUYỀN)
GET /api/lich-hen
Authorization: Bearer {token_bac_si}

# Response: 200 OK
{
  "status": true,
  "data": [...]
}
```

## 🛠️ Quản Lý Quyền (Admin Only)

### Xem tất cả vai trò

```bash
GET /api/phan-quyen
Authorization: Bearer {admin_token}
```

### Tắt quyền tạo lịch hẹn của Lễ tân

```bash
PUT /api/phan-quyen/4
Authorization: Bearer {admin_token}

{
  "lich_hen_tao": false
}
```

### Bật quyền xóa hàng hóa cho Điều dưỡng

```bash
PUT /api/phan-quyen/3
Authorization: Bearer {admin_token}

{
  "hang_hoa_xoa": true
}
```

## 📊 Luồng Hoạt Động

```
1. Nhân viên đăng nhập
   ↓
2. Server kiểm tra email/password
   ↓
3. Server kiểm tra trạng thái tài khoản (hoat_dong/da_khoa)
   ↓
4. Server tạo token và trả về thông tin user + vai trò
   ↓
5. Client lưu token
   ↓
6. Nhân viên gọi API với token
   ↓
7. Middleware kiểm tra quyền dựa trên vai trò
   ↓
8. Nếu có quyền: Thực hiện chức năng
   Nếu không có quyền: Trả về 403 Forbidden
```

## 🚀 Test API

### Postman Collection

**1. Đăng nhập Admin:**

```
POST http://localhost:8000/api/admin/dang-nhap
Body: {
  "email": "admin@example.com",
  "password": "password"
}
```

**2. Đăng nhập Bác sĩ:**

```
POST http://localhost:8000/api/nhan-vien/dang-nhap
Body: {
  "email": "bacsi@example.com",
  "password": "password"
}
```

**3. Lấy thông tin user:**

```
GET http://localhost:8000/api/user
Headers: Authorization: Bearer {token}
```

**4. Test quyền - Xem lịch hẹn:**

```
GET http://localhost:8000/api/lich-hen
Headers: Authorization: Bearer {token}
```

**5. Test quyền - Xóa hàng hóa (chỉ Admin có quyền):**

```
DELETE http://localhost:8000/api/hang-hoa/1
Headers: Authorization: Bearer {token}
```

## 📝 Lưu Ý Quan Trọng

1. **Token có giá trị vô thời hạn** cho đến khi đăng xuất hoặc admin khóa tài khoản
2. **Mỗi vai trò có quyền khác nhau** - Admin có thể thay đổi quyền qua API
3. **Tài khoản bị khóa** sẽ không đăng nhập được và tất cả token hiện tại bị thu hồi
4. **Middleware tự động kiểm tra quyền** - không cần code thêm logic ở controller
5. **Phân biệt Admin và NhanVien** qua field `user_type` trong response `/api/user`

## 🔧 Troubleshooting

### Lỗi: "Email hoặc mật khẩu không đúng"

-   Kiểm tra email và password
-   Password phải khớp với hash trong database

### Lỗi: "Tài khoản của bạn đã bị khóa"

-   Admin đã khóa tài khoản
-   Liên hệ admin để mở khóa: `PATCH /api/nhan-vien/{id}/mo-khoa`

### Lỗi: "Bạn không có quyền thực hiện chức năng này"

-   Vai trò không có quyền này
-   Admin cần bật quyền: `PUT /api/phan-quyen/{role_id}`

### Lỗi: "Chưa đăng nhập"

-   Token không hợp lệ hoặc đã hết hạn
-   Cần đăng nhập lại
