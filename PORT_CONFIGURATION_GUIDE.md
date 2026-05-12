# 🚀 Hướng dẫn chạy Petty với Port tùy chỉnh

## Cách nhanh nhất - Dùng script tự động

### 1. Đồng bộ port cho cả Backend và Frontend
```bash
# Từ thư mục gốc Petty-customer/
./sync-ports.sh 8002
```

Script này sẽ tự động:
- ✅ Cập nhật `APP_URL` trong `petty_BE/.env`
- ✅ Cập nhật `VITE_API_BASE` trong `petty_FE/.env`
- ✅ Hiển thị hướng dẫn khởi động

### 2. Khởi động Backend
```bash
cd petty_BE
php artisan config:clear
php artisan serve --port=8002
```

### 3. Khởi động Frontend (terminal mới)
```bash
cd petty_FE
npm run dev
```

---

## Cách thủ công

### Backend
1. Sửa `petty_BE/.env`:
   ```
   APP_URL=http://localhost:8002
   ```

2. Khởi động:
   ```bash
   cd petty_BE
   php artisan config:clear
   php artisan serve --port=8002
   ```

### Frontend
1. Sửa `petty_FE/.env`:
   ```
   VITE_API_BASE=http://localhost:8002/api
   ```

2. Khởi động:
   ```bash
   cd petty_FE
   npm run dev
   ```

---

## Ví dụ với các port khác nhau

### Port 8001 (mặc định)
```bash
./sync-ports.sh 8001
cd petty_BE && php artisan serve --port=8001
cd petty_FE && npm run dev
```

### Port 9000
```bash
./sync-ports.sh 9000
cd petty_BE && php artisan serve --port=9000
cd petty_FE && npm run dev
```

### Port 3000
```bash
./sync-ports.sh 3000
cd petty_BE && php artisan serve --port=3000
cd petty_FE && npm run dev
```

---

## Kiểm tra cấu hình hiện tại

```bash
# Backend
cat petty_BE/.env | grep APP_URL

# Frontend
cat petty_FE/.env | grep VITE_API_BASE
```

---

## Lưu ý quan trọng

1. **Luôn đồng bộ port** giữa Backend và Frontend
2. **Khởi động lại** cả 2 server sau khi đổi port
3. **Clear cache** Laravel: `php artisan config:clear`
4. **Restart Vite** dev server để áp dụng env mới

---

## Troubleshooting

### Lỗi: ERR_CONNECTION_REFUSED
- ✅ Kiểm tra Backend đang chạy: `curl http://localhost:8002/api`
- ✅ Kiểm tra port trong `.env` khớp với port server đang chạy

### Lỗi: Storage images không load
- ✅ Kiểm tra `APP_URL` trong `petty_BE/.env`
- ✅ Chạy: `php artisan config:clear`
- ✅ Khởi động lại Backend

### Lỗi: API calls đi sai port
- ✅ Kiểm tra `VITE_API_BASE` trong `petty_FE/.env`
- ✅ Khởi động lại Frontend: `npm run dev`
