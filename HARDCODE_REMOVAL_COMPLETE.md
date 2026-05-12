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
- **2 scripts** tự động hóa
- **4 files** hướng dẫn

---

## 🔧 Scripts đã tạo

### 1. `sync-ports.sh` - Đồng bộ port tự động
```bash
./sync-ports.sh 8001
```
- Cập nhật `APP_URL` trong `petty_BE/.env`
- Cập nhật `VITE_API_BASE` trong `petty_FE/.env`
- Clear Laravel cache
- Hỗ trợ symlink

### 2. `update-database-urls.sh` - Cập nhật URLs trong database
```bash
./update-database-urls.sh
```
- Tìm và thay thế URLs có port 8000 trong bảng `khach_hangs`
- Cập nhật cột `anh_dai_dien`

---

## 🚀 Cách sử dụng

### Khởi động với port mặc định (8001)

```bash
# 1. Đồng bộ port
./sync-ports.sh 8001

# 2. Khởi động Backend
cd petty_BE
php artisan config:clear
php artisan serve --port=8001

# 3. Khởi động Frontend (terminal mới)
cd petty_FE
npm run dev
```

### Khởi động với port khác (ví dụ: 8002)

```bash
# 1. Đồng bộ port
./sync-ports.sh 8002

# 2. Khởi động Backend
cd petty_BE
php artisan config:clear
php artisan serve --port=8002

# 3. Khởi động Frontend (terminal mới)
cd petty_FE
npm run dev
```

---

## 📝 Files đã sửa

### Backend
- `petty_BE/.env` - Sử dụng `APP_URL` động
- `petty_BE/app/Helpers/PetImageHelper.php` - Dùng `url()` helper
- `petty_BE/app/Helpers/UserImageHelper.php` - Dùng `url()` helper
- `petty_BE/app/Http/Controllers/ThuCungController.php` - Dùng `url()` helper
- `petty_BE/app/Http/Controllers/KhachHangController.php` - Dùng `UserImageHelper`

### Frontend
- `petty_FE/.env` - Sử dụng `VITE_API_BASE`
- `petty_FE/vite.config.js` - Đọc port từ env
- `petty_FE/src/utils/api.js` - Dùng `import.meta.env.VITE_API_BASE`
- `petty_FE/src/views/customer/login/index.vue` - Dùng env variable
- `petty_FE/src/views/customer/register/index.vue` - Dùng env variable
- **+32 files khác** (xem `petty_FE/CHANGELOG_HARDCODE_FIX.md`)

### Database
- Bảng `khach_hangs` - Cập nhật 2 records có URLs với port 8000

---

## ✨ Lợi ích

✅ **Linh hoạt**: Chạy với bất kỳ port nào (8001, 8002, 9000, ...)  
✅ **Tự động**: Script đồng bộ cấu hình  
✅ **Không hardcode**: Tất cả URLs đều động  
✅ **Dễ maintain**: Chỉ cần sửa 1 file .env  
✅ **Team-friendly**: Mỗi dev có thể dùng port riêng  
✅ **Database clean**: URLs trong database đã được cập nhật

---

## 🔍 Kiểm tra cấu hình hiện tại

```bash
# Backend
cat petty_BE/.env | grep APP_URL

# Frontend
cat petty_FE/.env | grep VITE_API_BASE

# Database
cd petty_BE
php artisan tinker --execute="echo \App\Models\KhachHang::find(2)->anh_dai_dien;"
```

---

## 🐛 Troubleshooting

### Lỗi: ERR_CONNECTION_REFUSED
```bash
# 1. Kiểm tra Backend đang chạy
curl http://localhost:8001/api

# 2. Kiểm tra port khớp nhau
cat petty_BE/.env | grep APP_URL
cat petty_FE/.env | grep VITE_API_BASE

# 3. Restart cả 2 servers
```

### Storage images không load
```bash
# 1. Clear cache Laravel
cd petty_BE
php artisan config:clear

# 2. Kiểm tra database URLs
php artisan tinker --execute="\$c = \App\Models\KhachHang::find(2); echo \$c->anh_dai_dien;"

# 3. Nếu vẫn có port 8000, chạy update script
cd ..
./update-database-urls.sh

# 4. Khởi động lại Backend
cd petty_BE
php artisan serve --port=8001
```

### API calls đi sai port
```bash
# 1. Kiểm tra frontend .env
cat petty_FE/.env | grep VITE_API_BASE

# 2. Khởi động lại Frontend
cd petty_FE
npm run dev

# 3. Hard refresh browser (Ctrl+Shift+R)
```

---

## 📚 Tài liệu liên quan

- `README_PORT_CONFIGURATION.md` - Tổng quan
- `PORT_CONFIGURATION_GUIDE.md` - Hướng dẫn chi tiết
- `petty_FE/CONFIG_API.md` - Cấu hình API frontend
- `petty_FE/CHANGELOG_HARDCODE_FIX.md` - Chi tiết 37 files đã sửa
- `petty_BE/START_SERVER_GUIDE.md` - Hướng dẫn backend

---

## 📅 Ngày hoàn thành: 2026-05-12

**Tất cả hardcoded URLs đã được loại bỏ! Bạn có thể chạy Petty với bất kỳ port nào! 🎉**

---

## 🔄 Update 2026-05-12 — DB now stores relative paths

### Vấn đề đã giải quyết thêm

✅ **Database**: Chuyển từ lưu full URL sang relative path  
✅ **Helpers**: Thống nhất API `resolveUrl` cho mọi loại ảnh  
✅ **Migration**: One-shot normalize existing data  
✅ **Backward compat**: Hỗ trợ cả full URL legacy và external URLs (Google OAuth)

### Thay đổi chính

- **ImageHelper::resolveUrl()** - Generic resolver cho mọi file upload
- **PetImageHelper::getImageUrl()** - Pet-specific với default fallback
- **UserImageHelper::getAvatarUrl()** - Refactored để dùng ImageHelper
- **Migration** `normalize_upload_paths_to_relative` - Bóc tách URLs trong DB
- **Xóa** `update-database-urls.sh` - Không còn cần thiết

### Lợi ích mới

✅ **Không cần update DB** khi đổi port - chỉ cần `./sync-ports.sh`  
✅ **Helpers thống nhất** - 3 case (empty/full URL/relative) xử lý đồng nhất  
✅ **Google OAuth safe** - External URLs không bị bóc tách  
✅ **Migration idempotent** - Chạy nhiều lần không ảnh hưởng

### Cách sử dụng mới

```bash
# Đổi port - KHÔNG cần chạy update-database-urls.sh nữa
./sync-ports.sh 9000
cd petty_BE && php artisan serve --port=9000
cd petty_FE && npm run dev
```

Ảnh tự động build URL từ `APP_URL` hiện tại!
