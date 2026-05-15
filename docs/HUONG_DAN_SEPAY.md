# Hướng dẫn test thanh toán SePay

## Tổng quan

Hệ thống Petty tích hợp SePay để thanh toán qua chuyển khoản ngân hàng. Khi khách quét QR và chuyển khoản, SePay tự động detect giao dịch → gửi webhook → hệ thống xác nhận thanh toán thành công.

Mỗi thành viên cần tự tạo tài khoản SePay riêng để test độc lập.

---

## Phần 1: Tạo tài khoản SePay (làm 1 lần)

### 1.1 Đăng ký

1. Truy cập https://my.sepay.vn/register
2. Đăng ký bằng email cá nhân
3. Khi được hỏi "Bạn cần gì từ SePay?" → chọn **"Chỉ cần chia sẻ biến động số dư"**
4. Xác nhận email

### 1.2 Liên kết tài khoản ngân hàng

1. Đăng nhập SePay → vào **"Ngân hàng"**
2. Nhấn **"Thêm tài khoản"**
3. Chọn ngân hàng bạn đang dùng (MB Bank, VPBank, Vietcombank, Techcombank, v.v.)
4. Liên kết qua **API Banking** (SePay hướng dẫn cụ thể cho từng bank)
5. Sau khi liên kết thành công, ghi nhớ:
   - **Mã ngân hàng** (VD: `MB`, `VPB`, `VCB`, `TCB`)
   - **Số tài khoản** của bạn
   - **Tên chủ tài khoản** (viết hoa không dấu, VD: `NGUYEN VAN A`)

### 1.3 Lấy API Key

1. Vào trang quản lý tài khoản ngân hàng vừa liên kết
2. Nhấn **"Tích hợp Webhook"**
3. Trang mới sẽ hiện **API Key** → copy lại

### 1.4 Cài ngrok

Ngrok tạo URL public để SePay gửi webhook về máy local của bạn.

**macOS:**
```bash
brew install ngrok
```

**Windows:**
- Tải từ https://ngrok.com/download
- Giải nén, thêm vào PATH

**Linux:**
```bash
snap install ngrok
```

Sau khi cài xong, đăng ký tài khoản miễn phí tại https://ngrok.com và authenticate:
```bash
ngrok config add-authtoken <token-của-bạn>
```

---

## Phần 2: Cấu hình project

### 2.1 Cập nhật file .env

Mở `petty_BE/.env`, tìm block SePay và điền thông tin **của bạn**:

```env
# SePay — Thanh toán chuyển khoản ngân hàng
SEPAY_API_KEY=<API key bạn lấy từ bước 1.3>
SEPAY_BANK_CODE=<mã ngân hàng: MB, VPB, VCB, TCB, ...>
SEPAY_ACCOUNT_NUMBER=<số tài khoản ngân hàng của bạn>
SEPAY_ACCOUNT_NAME="<tên chủ TK viết hoa không dấu>"
SEPAY_WEBHOOK_URL=<sẽ điền sau khi chạy ngrok>
SEPAY_PAYMENT_EXPIRY_MINUTES=15
```

Ví dụ:
```env
SEPAY_API_KEY=ABC123XYZ456...
SEPAY_BANK_CODE=MB
SEPAY_ACCOUNT_NUMBER=0123456789
SEPAY_ACCOUNT_NAME="NGUYEN VAN A"
SEPAY_WEBHOOK_URL=https://xxxx.ngrok-free.app/api/webhook/sepay
SEPAY_PAYMENT_EXPIRY_MINUTES=15
```

> **Quan trọng:** Tên có khoảng trắng phải đặt trong dấu ngoặc kép `"..."`.

### 2.2 Chạy migration (lần đầu)

```bash
cd petty_BE && php artisan migrate --path=database/migrations/2026_05_15_000001_add_sepay_fields_to_thanh_toans_table.php
```

---

## Phần 3: Chạy và test

### 3.1 Khởi động (cần 3 terminal)

```bash
# Terminal 1: Backend
cd petty_BE && php artisan serve

# Terminal 2: Frontend
cd petty_FE && npm run dev

# Terminal 3: ngrok (chạy ở folder nào cũng được)
ngrok http 8000
```

### 3.2 Cập nhật webhook URL

Sau khi ngrok chạy, nó hiện URL kiểu:
```
Forwarding   https://abc123.ngrok-free.app -> http://localhost:8000
```

**Bước A** — Cập nhật `.env`:
```env
SEPAY_WEBHOOK_URL=https://abc123.ngrok-free.app/api/webhook/sepay
```

**Bước B** — Cập nhật trên SePay dashboard:
1. Vào trang quản lý tài khoản ngân hàng
2. Nhấn **"Tích hợp Webhook"**
3. Dán URL: `https://abc123.ngrok-free.app/api/webhook/sepay`
4. Lưu

**Bước C** — Restart backend (để load .env mới):
- Ctrl+C terminal backend → chạy lại `php artisan serve`

> Mỗi lần restart ngrok, URL đổi → lặp lại bước A, B, C.

### 3.3 Test flow hoàn chỉnh

#### Nurse (tại quầy):
1. Đăng nhập Nurse/Admin
2. Chọn lịch hẹn đã hoàn thành khám → **Thanh toán**
3. Chọn **"Chuyển khoản (QR)"** → modal QR hiện lên
4. Dùng app ngân hàng **trên điện thoại khác** quét QR → chuyển khoản
5. Chờ 30-60 giây → UI tự chuyển "Thanh toán thành công"

#### Customer (online):
1. Đăng nhập Khách hàng
2. Vào **Thanh toán** → chọn hóa đơn pending
3. Nhấn **"Xác nhận & Thanh toán"** → chọn **"Chuyển khoản ngân hàng"**
4. Quét QR → chuyển khoản → UI tự cập nhật

#### Test nhanh (không chuyển tiền thật):
1. Tạo giao dịch như trên (QR hiện lên)
2. Nhấn nút **"Xác nhận đã nhận tiền"** trên modal
3. Hệ thống xác nhận ngay lập tức

---

## Phần 4: Mã ngân hàng phổ biến

| Ngân hàng | Mã (SEPAY_BANK_CODE) |
|-----------|---------------------|
| MB Bank | `MB` |
| VPBank | `VPB` |
| Vietcombank | `VCB` |
| Techcombank | `TCB` |
| BIDV | `BIDV` |
| Agribank | `AGR` |
| TPBank | `TPB` |
| ACB | `ACB` |
| Sacombank | `STB` |
| VietinBank | `CTG` |

Danh sách đầy đủ: https://my.sepay.vn/docs/bank-code

---

## Phần 5: Xử lý sự cố

| Vấn đề | Nguyên nhân | Giải pháp |
|--------|-------------|-----------|
| QR hiện nhưng không tự xác nhận | Webhook chưa đến backend | Kiểm tra: ngrok chạy? URL webhook trên SePay đúng? Backend đang serve? |
| Ngrok log báo 401 | API key không khớp | Đảm bảo `SEPAY_API_KEY` trong .env = key trên SePay dashboard |
| Ngrok log báo 200 nhưng UI không cập nhật | Content không match | Xem log: `tail -20 storage/logs/laravel.log \| grep SePay` |
| "Chức năng chưa được cấu hình" | Thiếu env vars | Điền đầy đủ SEPAY_* → restart backend |
| .env parse error | Tên có space không có quote | Thêm ngoặc kép: `SEPAY_ACCOUNT_NAME="TEN CUA BAN"` |
| Giao dịch hết hạn | Quá 15 phút | Nhấn "Tạo lại" hoặc tạo giao dịch mới |
| CleanMyMac báo ngrok malware | False positive (ngrok là proxy tool) | Nhấn "Ignore" — ngrok an toàn, cài qua Homebrew chính thức |

### Debug webhook

Kiểm tra log Laravel:
```bash
tail -30 petty_BE/storage/logs/laravel.log | grep "SePay"
```

Kiểm tra ngrok traffic (mở browser):
```
http://127.0.0.1:4040
```
Đây là dashboard ngrok local — hiện tất cả request đi qua, kèm body + response.

---

## Phần 6: Cấu trúc code liên quan

```
petty_BE/
├── config/sepay.php                           # Config đọc từ .env
├── app/Services/SepayService.php              # Logic: sinh QR URL, verify webhook, match transaction
├── app/Http/Controllers/SepayController.php   # API: tạo payment, webhook handler, polling, confirm
├── app/Console/Commands/ExpirePayments.php    # Schedule: auto-expire giao dịch quá 15 phút
└── routes/api.php                             # Route definitions

petty_FE/
├── src/services/sepayService.js               # Axios calls: createPayment, checkStatus, confirmManual
└── src/components/payment/PaymentQrModal.vue  # UI: QR + thông tin CK + countdown + polling + confirm
```

## API Endpoints

| Method | URL | Auth | Mô tả |
|--------|-----|------|--------|
| POST | `/api/thanh-toan/chuyen-khoan` | Sanctum | Tạo giao dịch pending, trả QR info |
| POST | `/api/webhook/sepay` | API Key (header) | Nhận callback từ SePay khi có giao dịch |
| GET | `/api/thanh-toan/{id}/trang-thai` | Sanctum | Frontend polling trạng thái (mỗi 3s) |
| POST | `/api/thanh-toan/{id}/confirm` | Sanctum + Staff | Staff xác nhận thủ công đã nhận tiền |
