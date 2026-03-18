# 📄 Hướng dẫn Setup Xuất PDF Phiếu Chi

## 🎯 Backend Setup

### 1. Cài đặt Package Laravel DomPDF

```bash
composer require barryvdh/laravel-dompdf
```

### 2. Publish config (Optional)

```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### 3. Tạo thư mục views cho PDF

Tạo file: `resources/views/pdf/phieu-chi.blade.php`

Copy nội dung từ file `PDF_VIEW_PHIEU_CHI.blade.php` trong project này vào file trên.

### 4. Đảm bảo Controller có method exportPdf

Controller `PhieuChiController.php` đã có method `exportPdf($id)` như code bạn cung cấp.

### 5. Đảm bảo Route đã được đăng ký

```php
Route::get('/phieu-chi/{id}/pdf', [PhieuChiController::class, 'exportPdf']);
```

## ✅ Frontend Setup

Frontend đã được cập nhật và sẵn sàng:

1. ✅ Nút "Xuất PDF" đã được thêm vào component `ChiTietPhieuChi`
2. ✅ Function `exportToPDF()` gọi API `/phieu-chi/{id}/pdf`
3. ✅ Tự động download file PDF về máy
4. ✅ Error handling đầy đủ

## 🔧 Cấu hình DomPDF (Optional)

Trong file `config/dompdf.php`:

```php
return [
    'show_warnings' => false,
    'public_path' => public_path(),
    'convert_entities' => true,
    'options' => [
        'font_dir' => storage_path('fonts/'),
        'font_cache' => storage_path('fonts/'),
        'temp_dir' => sys_get_temp_dir(),
        'chroot' => realpath(base_path()),
        'enable_font_subsetting' => false,
        'pdf_backend' => 'CPDF',
        'default_media_type' => 'screen',
        'default_paper_size' => 'a4',
        'default_font' => 'sans-serif',
        'dpi' => 96,
        'enable_php' => false,
        'enable_javascript' => true,
        'enable_remote' => true,
        'font_height_ratio' => 1.1,
        'enable_html5_parser' => true,
    ],
];
```

## 🚀 Test API

### 1. Test với Postman/Thunder Client

```
GET http://localhost:8000/api/phieu-chi/1/pdf
Authorization: Bearer {token}
```

Kết quả: File PDF được download

### 2. Test trên Frontend

1. Mở trang Phiếu Chi
2. Click "Xem chi tiết" trên một phiếu chi
3. Click nút "Xuất PDF"
4. File PDF sẽ tự động download

## 📋 Cấu trúc PDF

PDF sẽ bao gồm các phần:

### Header

- Tiêu đề "PHIẾU CHI"
- Mã phiếu chi
- Ngày xuất file

### Thông tin phiếu chi

- Loại chi (với badge màu)
- Nội dung chi
- Nhà cung cấp / Đối tượng nhận tiền
- Ngày chi
- Người tạo
- Trạng thái
- Ghi chú

### Tổng quan tài chính

- Tổng giá trị
- Đã trả (màu xanh)
- Còn nợ (màu đỏ)

### Lịch sử thanh toán (Table)

- STT (Lần 1, 2, 3...)
- Ngày giờ thanh toán
- Hình thức thanh toán
  - Chi tiết tiền mặt/chuyển khoản (nếu chọn cả hai)
- Số tiền
- Ghi chú

### Footer

- Chữ ký người lập phiếu
- Chữ ký kế toán
- Thông tin xuất file

## 🎨 Styling

PDF sử dụng:

- Font: DejaVu Sans (hỗ trợ tiếng Việt tốt)
- Màu chủ đạo: #009689 (xanh lá)
- Layout: A4 portrait
- Responsive table với alternating row colors
- Badge với màu sắc phù hợp

## 🐛 Troubleshooting

### Lỗi: "Class 'Barryvdh\DomPDF\Facade\Pdf' not found"

**Giải pháp:**

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Lỗi: Font không hiển thị tiếng Việt

**Giải pháp:** Thêm vào đầu file blade:

```php
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
    body {
        font-family: 'DejaVu Sans', 'Roboto', sans-serif;
    }
</style>
```

### Lỗi: CORS khi download PDF

**Giải pháp:** Kiểm tra `config/cors.php`:

```php
'paths' => ['api/*'],
'allowed_origins' => ['*'],
'supports_credentials' => true,
```

### Lỗi: Memory limit exceeded

**Giải pháp:** Tăng memory trong `php.ini`:

```ini
memory_limit = 256M
```

Hoặc trong controller:

```php
ini_set('memory_limit', '256M');
```

## 📱 Demo Flow

```
User clicks "Xuất PDF"
↓
Frontend: Gọi API GET /phieu-chi/{id}/pdf
↓
Backend:
  - Load phiếu chi + relationships
  - Load lịch sử thanh toán
  - Render blade view
  - Generate PDF với DomPDF
  - Return PDF file
↓
Frontend:
  - Nhận file PDF
  - Tạo blob URL
  - Trigger download
  - Cleanup
↓
File PDF được lưu vào máy người dùng
```

## 📦 File Structure

```
backend/
├── app/Http/Controllers/
│   └── PhieuChiController.php (method exportPdf)
├── resources/views/pdf/
│   └── phieu-chi.blade.php (PDF template)
└── routes/api.php (route đã đăng ký)

frontend/
└── src/components/Admin/TaiChinhHoaDon/PhieuChi/
    └── ChiTietPhieuChi/
        └── index.vue (có nút Xuất PDF + logic)
```

## ✅ Checklist

Backend:

- [ ] Cài đặt `barryvdh/laravel-dompdf`
- [ ] Tạo file `resources/views/pdf/phieu-chi.blade.php`
- [ ] Method `exportPdf()` trong controller
- [ ] Route `/phieu-chi/{id}/pdf` đã đăng ký
- [ ] Test API với Postman

Frontend:

- [x] Nút "Xuất PDF" đã được thêm
- [x] Function `exportToPDF()` đã implement
- [x] Error handling
- [x] Loading state (implicit qua browser download)

## 🎉 Ready to use!

Sau khi setup backend xong, chức năng xuất PDF sẽ hoạt động ngay lập tức!
