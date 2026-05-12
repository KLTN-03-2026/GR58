# ✅ Hoàn thành - Loại bỏ Hardcoded URLs

## 🎯 Vấn đề đã giải quyết

✅ **Frontend**: Loại bỏ tất cả hardcoded `http://127.0.0.1:8000` và `http://localhost:8000`  
✅ **Backend**: Loại bỏ hardcoded port trong `APP_URL`  
✅ **Database**: Cập nhật URLs trong database từ port 8000 sang 8001  
✅ **Tự động hóa**: Script đồng bộ port giữa Backend và Frontend

---

## 📊 Thống kê

- **37 files** frontend đã được cập nhật
- **2 customer records** trong database đã được cập nhật
- **0 hardcoded URLs** còn lại
- **2 scripts** tự động hóa (sync-ports.sh, update-database-urls.sh)
- **5 files** hướng dẫn

---

## 🚀 Cách sử dụng

### Cách 1: Dùng script tự động (Khuyến nghị)

```bash
# Đồng bộ port cho cả Backend và Frontend
./sync-ports.sh 8001

# Khởi động Backend
cd petty_BE
php artisan config:clear
php artisan serve --port=8001

# Khởi động Frontend (terminal mới)
cd petty_FE
npm run dev
```

### Cách 2: Thủ công

**Backend:**
```bash
# Sửa petty_BE/.env
APP_URL=http://localhost:8001

# Khởi động
cd petty_BE
php artisan config:clear
php artisan serve --port=8001
```

**Frontend:**
```bash
# Sửa petty_FE/.env
VITE_API_BASE=http://localhost:8001/api

# Khởi động
cd petty_FE
npm run dev
```

---

## 📝 Ví dụ với các port khác

### Port 8002
```bash
./sync-ports.sh 8002
cd petty_BE && php artisan serve --port=8002
```

### Port 9000
```bash
./sync-ports.sh 9000
cd petty_BE && php artisan serve --port=9000
```

### Port 3000
```bash
./sync-ports.sh 3000
cd petty_BE && php artisan serve --port=3000
```

---

## 📂 Files đã tạo

1. **`sync-ports.sh`** - Script đồng bộ port tự động
2. **`update-database-urls.sh`** - Script cập nhật URLs trong database
3. **`PORT_CONFIGURATION_GUIDE.md`** - Hướng dẫn chi tiết
4. **`HARDCODE_REMOVAL_COMPLETE.md`** - Tài liệu tổng kết
5. **`petty_FE/CONFIG_API.md`** - Hướng dẫn cấu hình API
6. **`petty_FE/.env.example`** - Template cho .env
7. **`petty_FE/CHANGELOG_HARDCODE_FIX.md`** - Chi tiết các file đã sửa
8. **`petty_BE/start-server.sh`** - Script khởi động backend
9. **`petty_BE/START_SERVER_GUIDE.md`** - Hướng dẫn backend

---

## ✨ Lợi ích

✅ **Linh hoạt**: Chạy với bất kỳ port nào  
✅ **Tự động**: Script đồng bộ cấu hình  
✅ **Không hardcode**: Tất cả URLs đều động  
✅ **Dễ maintain**: Chỉ cần sửa 1 file .env  
✅ **Team-friendly**: Mỗi dev có thể dùng port riêng

---

## 🔍 Kiểm tra cấu hình hiện tại

```bash
# Backend
cat petty_BE/.env | grep APP_URL

# Frontend
cat petty_FE/.env | grep VITE_API_BASE
```

---

## 🐛 Troubleshooting

### Lỗi: ERR_CONNECTION_REFUSED
```bash
# Kiểm tra Backend đang chạy
curl http://localhost:8001/api

# Kiểm tra port khớp nhau
cat petty_BE/.env | grep APP_URL
cat petty_FE/.env | grep VITE_API_BASE
```

### Storage images không load
```bash
# 1. Clear cache Laravel
cd petty_BE
php artisan config:clear

# 2. Kiểm tra URLs trong database
php artisan tinker --execute="echo \App\Models\KhachHang::find(2)->anh_dai_dien;"

# 3. Nếu vẫn thấy port 8000, chạy update script
cd ..
./update-database-urls.sh

# 4. Khởi động lại Backend
cd petty_BE
php artisan serve --port=8001
```

### API calls đi sai port
```bash
# Khởi động lại Frontend
cd petty_FE
npm run dev
```

---

## 📅 Ngày hoàn thành: 2026-05-12

**Tất cả đã sẵn sàng! Bạn có thể chạy Petty với bất kỳ port nào bạn muốn! 🎉**
