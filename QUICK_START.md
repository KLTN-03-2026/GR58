# 🚀 Quick Start - Chạy Petty với Port tùy chỉnh

## Cách nhanh nhất

```bash
# 1. Đồng bộ port (ví dụ: 8001)
./sync-ports.sh 8001

# 2. Khởi động Backend
cd petty_BE
php artisan config:clear
php artisan serve --port=8001

# 3. Khởi động Frontend (terminal mới)
cd petty_FE
npm run dev
```

## Đổi sang port khác

```bash
# Ví dụ: Đổi sang port 8002
./sync-ports.sh 8002
cd petty_BE && php artisan serve --port=8002
```

## Nếu gặp lỗi images không load

```bash
# Cập nhật URLs trong database
./update-database-urls.sh

# Restart backend
cd petty_BE
php artisan serve --port=8001
```

## 📚 Tài liệu đầy đủ

- **`HARDCODE_REMOVAL_COMPLETE.md`** - Tổng quan chi tiết
- **`PORT_CONFIGURATION_GUIDE.md`** - Hướng dẫn đầy đủ
- **`README_PORT_CONFIGURATION.md`** - Thông tin tổng hợp

---

✅ **Đã loại bỏ tất cả hardcoded URLs!**  
✅ **Chạy được với bất kỳ port nào!**  
✅ **Database đã được cập nhật!**
