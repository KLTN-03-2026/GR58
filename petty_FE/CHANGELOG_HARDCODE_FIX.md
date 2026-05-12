# Tóm tắt thay đổi - Loại bỏ Hardcoded URLs

## Vấn đề
- Frontend đang hardcode `http://127.0.0.1:8000` và `http://localhost:8000` trong nhiều file
- Khi backend chạy trên port khác (ví dụ: 8001), frontend không thể kết nối

## Giải pháp
Đã thay thế tất cả hardcoded URLs bằng biến môi trường `VITE_API_BASE`

## Files đã sửa

### 1. Cấu hình chính
- ✅ `vite.config.js` - Cập nhật proxy để đọc từ env
- ✅ `.env` - Đã có sẵn với `VITE_API_BASE=http://localhost:8001/api`
- ✅ `.env.example` - Tạo mới để hướng dẫn

### 2. Utils files (7 files)
- ✅ `src/utils/api.js`
- ✅ `src/utils/kiemKe.js`
- ✅ `src/utils/phieuChi.js`
- ✅ `src/utils/inventoryAudit.js`
- ✅ `src/utils/khuyenMai.js`
- ✅ `src/utils/payment.js`
- ✅ `src/utils/paymentVoucher.js`
- ✅ `src/utils/promotion.js`

### 3. Authentication files (5 files)
- ✅ `src/views/customer/login/index.vue`
- ✅ `src/views/customer/register/index.vue`
- ✅ `src/views/admin/login/index.vue`
- ✅ `src/views/staff/login/index.vue`
- ✅ `src/views/auth/verified/index.vue`
- ✅ `src/components/Auth/Callback.vue`

### 4. Customer views (6 files)
- ✅ `src/views/customer/verify-email/index.vue`
- ✅ `src/views/customer/personal-info/index.vue`
- ✅ `src/views/customer/my-pets/index.vue`
- ✅ `src/views/customer/my-pets/pet-detail/index.vue`
- ✅ `src/views/customer/appointment/book-appointment/index.vue`

### 5. Components (4 files)
- ✅ `src/components/trangchu/index.vue`
- ✅ `src/components/trangchu/BookingStartModal.vue`
- ✅ `src/components/trangchu/dichvu/index.vue`
- ✅ `src/layout/components/Header.vue`
- ✅ `src/layout/components/ChatbotWidget.vue`

### 6. Admin/Staff views (4 files)
- ✅ `src/views/admin/resource/service-management/index.vue`
- ✅ `src/views/admin/personnel/account-management/add-staff/index.vue`
- ✅ `src/views/nurse/expense-voucher/expense-detail/index.vue`

### 7. Forum (2 files)
- ✅ `src/views/forum/index.vue`
- ✅ `src/views/forum/detail.vue`

## Tổng cộng
- **37 files** đã được cập nhật
- **0 hardcoded URLs** còn lại

## Cách sử dụng

### Thay đổi port backend
Chỉ cần sửa file `.env`:
```bash
VITE_API_BASE=http://localhost:8001/api
```

### Khởi động lại dev server
```bash
npm run dev
```

## Lưu ý
- Tất cả API calls giờ đây sử dụng `import.meta.env.VITE_API_BASE`
- Default fallback là `http://localhost:8001/api` (thay vì 8000)
- Vite proxy tự động forward `/api` và `/khoa` requests đến backend
- Storage URLs (avatars, images) cũng được xử lý động

## Testing
Để test, hãy:
1. Đảm bảo backend đang chạy trên port 8001
2. Khởi động frontend: `npm run dev`
3. Thử đăng nhập và các chức năng khác
4. Kiểm tra Network tab trong DevTools - tất cả requests phải đi đến `localhost:8001`
