## 🔍 DEBUG LỖI "CHỈ QUẢN TRỊ VIÊN VÀ NHÂN VIÊN..."

### ✅ Kết quả kiểm tra:
- Admin có vai trò đúng: ✅
- Token còn hạn: ✅
- Middleware logic đúng: ✅

### 🐛 Vấn đề có thể xảy ra:

#### 1. Frontend đang dùng token cũ/sai
**Giải pháp:**
1. Mở DevTools (F12) → Application/Storage
2. Xóa hết localStorage
3. Xóa hết cookies
4. Reload trang (Ctrl+Shift+R)
5. Đăng nhập lại

#### 2. Frontend gửi sai header
**Kiểm tra trong DevTools (F12) → Network:**
- Chọn request bị lỗi 403
- Xem tab "Headers"
- Kiểm tra có dòng: `Authorization: Bearer <token>` không?

**Nếu KHÔNG có → Fix frontend:**
```javascript
// Đảm bảo axios gửi token
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

// Hoặc trong mỗi request:
axios.get('/api/endpoint', {
  headers: {
    'Authorization': `Bearer ${localStorage.getItem('token')}`
  }
});
```

#### 3. Request đến sai endpoint
**Kiểm tra URL trong DevTools → Network:**
- Khách hàng gọi `/api/lich-hen` ✅
- Admin gọi `/api/lich-hen-all` ✅
- Không được nhầm lẫn!

#### 4. Token bị invalidate
**Đăng nhập lại để lấy token mới:**
```
POST http://127.0.0.1:8000/api/admin/dang-nhap
{
  "email": "admin1@example.com",
  "password": "password"
}
```

Lưu token từ response và dùng token mới.

#### 5. Config Sanctum (hiếm gặp)
**Kiểm tra file `.env`:**
```env
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,localhost:5173,localhost:5174
SESSION_DRIVER=cookie
SESSION_DOMAIN=localhost
```

### 🧪 Test API bằng Postman/Thunder Client:

1. **Login để lấy token:**
```
POST http://127.0.0.1:8000/api/admin/dang-nhap
Body (JSON):
{
  "email": "admin1@example.com",
  "password": "password"
}
```

2. **Test endpoint bị lỗi:**
```
GET http://127.0.0.1:8000/api/[endpoint-bị-lỗi]
Headers:
  Authorization: Bearer <token-từ-bước-1>
  Accept: application/json
```

Nếu API test thành công → Lỗi ở frontend
Nếu API test vẫn lỗi → Lỗi ở backend (cần log chi tiết)

### 📝 Lấy log chi tiết:
Thêm log vào `StaffOnly.php`:
```php
\Log::info('StaffOnly middleware', [
    'user' => $user,
    'user_class' => get_class($user),
    'is_admin' => $user instanceof \App\Models\Admin,
    'is_nhan_vien' => $user instanceof \App\Models\NhanVien
]);
```

Rồi xem file `storage/logs/laravel.log`
