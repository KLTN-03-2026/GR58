# 🚀 QUICK START - UPLOAD ẢNH

## 🎯 TÓM TẮT LỖI VÀ FIX

### Vấn Đề Gốc

```
Upload ảnh không hoạt động
↓
frontend gửi 'image' field → backend chỉ nhận 'file' field
↓
Fix: Cập nhật validator backend để nhận cả 'image' và 'file'
```

### FIX #1: Backend UploadController

✅ **Đã sửa:** Thêm field `image` vào validator

```php
'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
```

### FIX #2: Frontend Headers

✅ **Đã sửa:** Xóa Content-Type header thủ công

```javascript
// Sai: headers: { "Content-Type": "multipart/form-data" }
// Đúng: Không cần, Axios tự động set
```

### FIX #3: Directory & Logging

✅ **Đã tạo:** `storage/app/public/articles/`
✅ **Đã thêm:** Logging trong UploadController

## 🧪 TEST NGAY

### 1. Chạy Dev Server

```bash
# Frontend (nếu chưa chạy)
cd C:\xampp\htdocs\PETTY_VCMS_FE
npm run dev

# Backend (nếu chưa chạy)
cd C:\xampp\htdocs\PETTY_VCMS_BE
php artisan serve
```

### 2. Vào Form Upload Ảnh

```
http://localhost:5173/admin/bai-viet/them-moi
```

### 3. Upload Ảnh

1. Click chọn ảnh
2. Chọn file (jpg/png/gif/webp, ≤5MB)
3. **Mở DevTools (F12) → Console**
4. Kiểm tra output

### 4. Kiểm Tra Kết Quả

**✅ Thành công:**

```
✅ Image uploaded: http://127.0.0.1:8000/storage/articles/1701695423_abc123.jpg
```

→ Ảnh sẽ hiển thị trong preview

**❌ Lỗi:**

```
❌ Error uploading image: ...
Error response: {status: false, message: "..."}
Error status: 401
```

→ Xem bảng troubleshooting dưới đây

## 🔧 TROUBLESHOOTING

| Error                | Nguyên Nhân              | Giải Pháp                        |
| -------------------- | ------------------------ | -------------------------------- |
| **401 Unauthorized** | Token admin không hợp lệ | Đăng xuất rồi đăng nhập lại      |
| **403 Forbidden**    | User không phải Admin    | Kiểm tra user type trong DB      |
| **422 Validation**   | File không hợp lệ        | Chọn jpg/png/gif/webp, ≤5MB      |
| **500 Server Error** | Thư mục không tồn tại    | Chạy: `php artisan storage:link` |
| Ảnh upload nhưng 404 | Symlink không hoạt động  | Restart server Laravel           |

## 📊 FLOW DIAGRAM

```
Browser                           Laravel
─────────────────────────────────────────
1. User chọn file
   ↓
2. Frontend validate (size, type)
   ↓
3. POST /api/upload-image + file + token
   ↓                                  ↓
   [FormData]               4. Check auth:sanctum
                              ↓
                           5. Check EnsureAdmin middleware
                              ↓
                           6. Validate file
                              ↓
                           7. Store → storage/app/public/articles/
                              ↓
   ← JSON response ← 8. Generate public URL
                        ↓
4. Frontend set featuredImage.value = url
   ↓
5. Preview hiển thị ảnh
```

## 💾 FILES THAY ĐỔI

### Backend

- ✏️ `app/Http/Controllers/UploadController.php` - Thêm field 'image', logging
- ✅ `storage/app/public/articles/` - Tạo thư mục mới

### Frontend

- ✏️ `src/components/Admin/TruyenThong/BaiViet/ThemBaiMoi/index.vue`
  - Xóa Content-Type header
  - Thêm error handling tốt hơn
  - Thêm logging chi tiết

## 🎓 KEY POINTS

1. **FormData không cần Content-Type** - Axios tự động set
2. **Field name phải match** - Frontend gửi `image` ↔ Backend nhận `image`
3. **Middleware EnsureAdmin** - Phải đăng nhập bằng Admin
4. **Storage symlink** - Cần để access ảnh qua URL công cộng
5. **Logging is your friend** - Check logs khi error

## 📞 NEXT ISSUES

Nếu upload hoạt động nhưng vẫn có vấn đề:

- [ ] Check ảnh có lưu vào đúng thư mục
- [ ] Check URL trả về có chính xác
- [ ] Check image preview hiển thị
- [ ] Check publish bài viết với ảnh
- [ ] Check ảnh hiển thị trên trang chủ
