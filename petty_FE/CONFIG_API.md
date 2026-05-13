# Cấu hình Backend API URL

## Thay đổi port backend

Nếu backend Laravel của bạn chạy trên port khác (ví dụ: 8001 thay vì 8000), bạn chỉ cần thay đổi trong file `.env`:

```bash
# File: petty_FE/.env
VITE_API_BASE=http://localhost:8001/api
```

## Các bước cấu hình

1. Copy file `.env.example` thành `.env` (nếu chưa có):
   ```bash
   cp .env.example .env
   ```

2. Chỉnh sửa `VITE_API_BASE` trong file `.env` để trỏ đến backend của bạn:
   ```
   VITE_API_BASE=http://localhost:8001/api
   ```

3. Khởi động lại dev server:
   ```bash
   npm run dev
   ```

## Lưu ý

- **Không cần** thay đổi code trong các file `.vue` hoặc `.js`
- Tất cả các API calls đều sử dụng biến môi trường `VITE_API_BASE`
- Vite proxy sẽ tự động forward requests `/api` và `/khoa` đến backend
- Khi deploy production, hãy cập nhật `VITE_API_BASE` trong file `.env.production`

## Ví dụ cấu hình

### Development (local)
```
VITE_API_BASE=http://localhost:8001/api
```

### Production
```
VITE_API_BASE=https://api.petty.vn/api
```

### Docker
```
VITE_API_BASE=http://backend:8000/api
```
