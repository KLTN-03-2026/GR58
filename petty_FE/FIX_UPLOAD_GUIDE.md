# 🔧 FIX UPLOAD ẢNH - CHI TIẾT CÁC THAY ĐỔI

## 📋 Tóm Tắt Vấn Đề

Upload ảnh không hoạt động vì:

1. **Frontend gửi field `image` nhưng backend chỉ nhận `file`** ❌
2. **Content-Type header được set thủ công sai cách** ❌
3. **Không có thư mục lưu trữ `articles`** ❌
4. **Thiếu logging để debug** ❌

## ✅ CÁC FIX ĐÃ THỰC HIỆN

### 1️⃣ Backend - UploadController.php

**File:** `app/Http/Controllers/UploadController.php`

**Thay đổi:**

```php
// ❌ CỦ: Chỉ nhận 'file'
'file' => 'required|file|image|mimes:jpg,jpeg,png,gif|max:5120'

// ✅ MỚI: Nhận 'image' và 'file' (fallback)
'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
'file' => 'sometimes|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
```

**Bổ sung:**

- Thêm logging để debug (Log::info, Log::error)
- Hỗ trợ webp format
- Lưu vào thư mục `articles` thay vì `dichvu/images`
- Thêm error handling tốt hơn

### 2️⃣ Frontend - ThemBaiMoi/index.vue

**File:** `src/components/Admin/TruyenThong/BaiViet/ThemBaiMoi/index.vue`

**Thay đổi 1: Xóa headers thủ công**

```javascript
// ❌ CỦ:
const response = await client.post("/upload-image", formData, {
  headers: {
    "Content-Type": "multipart/form-data",
  },
});

// ✅ MỚI:
const response = await client.post("/upload-image", formData);
```

✨ **Lý do:** Axios tự động set Content-Type=multipart/form-data + boundary khi FormData được gửi

**Thay đổi 2: Error handling tốt hơn**

```javascript
} catch (error) {
  // ✅ Thêm logging chi tiết
  console.error("Error response:", error.response?.data);
  console.error("Error status:", error.response?.status);

  // ✅ Xử lý các lỗi cụ thể
  if (error.response?.status === 401 || error.response?.status === 403) {
    uploadError.value = "Không có quyền tải lên ảnh. Vui lòng đăng nhập lại.";
  } else if (error.response?.status === 422) {
    uploadError.value = "File không hợp lệ. " + (error.response?.data?.message || "Vui lòng chọn file ảnh hợp lệ.");
  } else {
    uploadError.value = error.response?.data?.message || error.message || "Lỗi khi tải lên ảnh";
  }
}
```

### 3️⃣ Backend - Tạo Thư Mục

**Command:**

```bash
mkdir storage/app/public/articles
```

✨ **Kết quả:** Symlink `public/storage -> storage/app/public` đã tồn tại

### 4️⃣ Backend - Storage Configuration

✅ **Đã kiểm tra:** Symlink `public/storage` đã tồn tại

```
[C:\xampp\htdocs\PETTY_VCMS_BE\public\storage] link already exists
```

## 🧪 CÁCH TEST

### Bước 1: Mở DevTools

```
F12 → Console Tab
```

### Bước 2: Thử upload ảnh

1. Vào `/admin/bai-viet/them-moi`
2. Click chọn ảnh
3. Chọn file ảnh (jpg, png, gif, webp - ≤5MB)
4. Kiểm tra console

### Bước 3: Đọc Logs

**Console sẽ in ra:**

```javascript
// Thành công:
✅ Image uploaded: http://127.0.0.1:8000/storage/articles/1701695423_abc123.jpg

// Lỗi 401/403:
❌ Error uploading image: (error object)
Error response: {status: false, message: "Không có quyền"}
Error status: 403

// Lỗi 422 (validation):
Error response: {status: false, errors: {image: ["The image field is required"]}}
Error status: 422
```

### Bước 3: Kiểm tra Backend Logs

```bash
# Windows PowerShell
tail -f C:\xampp\htdocs\PETTY_VCMS_BE\storage\logs\laravel.log

# Hoặc:
Get-Content -Path C:\xampp\htdocs\PETTY_VCMS_BE\storage\logs\laravel.log -Tail 50 -Wait
```

**Log output sẽ hiển thị:**

```
[2025-12-04 14:30:00] local.INFO: Upload endpoint called {"user":"App\Models\Admin","user_id":1,"user_type":"App\Models\Admin","files":["image"]}
[2025-12-04 14:30:00] local.INFO: File uploaded successfully {"path":"articles/1701695423_abc123.jpg","url":"http://127.0.0.1:8000/storage/articles/1701695423_abc123.jpg"}
```

## 🔍 SỰ CỐ THƯỜNG GẶP

| Lỗi                             | Nguyên Nhân                       | Giải Pháp                                             |
| ------------------------------- | --------------------------------- | ----------------------------------------------------- |
| 401 Unauthorized                | Không có token hoặc token hết hạn | Đăng nhập lại                                         |
| 403 Forbidden                   | User không phải Admin             | Dùng tài khoản Admin                                  |
| 422 Validation Error            | File không hợp lệ hoặc field sai  | Kiểm tra file (jpg/png/gif/webp ≤5MB)                 |
| 500 Internal Server Error       | Thư mục không tồn tại             | Chạy `php artisan storage:link`                       |
| Ảnh upload nhưng không hiển thị | URL sai hoặc storage chưa public  | Kiểm tra symlink hoặc thử hard refresh (Ctrl+Shift+R) |

## 📝 CÓ THỂ CẦN KIỂM TRA THÊM

### Nếu vẫn không work:

1. **Kiểm tra token admin:**

   ```javascript
   // Console browser
   localStorage.getItem("auth_token");
   localStorage.getItem("auth_user");
   ```

2. **Kiểm tra URL API:**

   ```javascript
   // Console browser
   // Trong network tab, tìm request POST /upload-image
   // Kiểm tra Headers: Authorization: Bearer [token]
   // Kiểm tra Response status
   ```

3. **Kiểm tra storage permissions:**

   ```bash
   # Windows PowerShell
   Get-Acl "C:\xampp\htdocs\PETTY_VCMS_BE\storage\app\public\articles"
   ```

4. **Test bằng curl:**

   ```bash
   $token = "your_admin_token"
   $imagePath = "C:\path\to\image.jpg"

   curl -X POST `
     -H "Authorization: Bearer $token" `
     -F "image=@$imagePath" `
     http://127.0.0.1:8000/api/upload-image
   ```

## ✨ FEATURES

- ✅ Upload ảnh jpg, png, gif, webp
- ✅ Tối đa 5MB
- ✅ Progress bar real-time
- ✅ Preview ảnh
- ✅ Xóa ảnh
- ✅ Error messages chi tiết
- ✅ Admin-only (middleware EnsureAdmin)
- ✅ Lưu vào `storage/app/public/articles`
- ✅ URL công cộng via symlink

## 🎯 NEXT STEPS

1. Test upload ảnh
2. Kiểm tra logs
3. Publish bài viết với ảnh
4. Kiểm tra redirect hoạt động (đã fix ở lần trước)
5. Kiểm tra bài viết hiển thị đúng trên trang chủ
