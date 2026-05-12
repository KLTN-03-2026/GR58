# Hướng dẫn chạy Backend với Port động

## Cách sử dụng

### 1. Chạy với port mặc định (8001)
```bash
cd petty_BE
./start-server.sh
```

### 2. Chạy với port tùy chỉnh
```bash
cd petty_BE
./start-server.sh 8002
```

Hoặc:
```bash
cd petty_BE
./start-server.sh 9000
```

## Script tự động làm gì?

1. ✅ Tự động cập nhật `APP_URL` trong `.env` theo port bạn chọn
2. ✅ Clear config cache của Laravel
3. ✅ Khởi động server với port đã chọn

## Ví dụ

```bash
# Chạy trên port 8002
./start-server.sh 8002

# Output:
# 🚀 Starting Laravel server on port 8002...
# ✅ Updated APP_URL to http://localhost:8002
# Laravel development server started: http://127.0.0.1:8002
```

## Lưu ý

- Sau khi đổi port backend, nhớ cập nhật `VITE_API_BASE` trong `petty_FE/.env`:
  ```
  VITE_API_BASE=http://localhost:8002/api
  ```

- Hoặc dùng script tự động (xem file `sync-ports.sh`)
