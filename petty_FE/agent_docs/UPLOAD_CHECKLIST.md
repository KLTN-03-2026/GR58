# ✅ CHECKLIST - UPLOAD ẢNH & LƯU VÀO DB

## 🔧 BACKEND FIXES

- [x] **UploadController.php**

  - [x] Nhận `image` field (thay vì chỉ `file`)
  - [x] Validate: image, ≤5MB, formats: jpg/png/gif/webp
  - [x] Store vào `storage/app/public/articles/`
  - [x] Trả về URL công cộng
  - [x] Thêm logging (Log::info, Log::error)

- [x] **BaiVietController.php**

  - [x] Validate `anh_bai_viet` field (URL)
  - [x] Nhận slug từ frontend (không overwrite)
  - [x] Nhận `phan_loai_bai_viet_id` (required)
  - [x] Error handling: ValidationException, generic Exception
  - [x] Return proper JSON responses

- [x] **BaiViet Model**

  - [x] `anh_bai_viet` trong $fillable array
  - [x] Relationships: nhanVien, phanLoai

- [x] **Routes (api.php)**

  - [x] POST /upload-image dengan EnsureAdmin middleware ✅
  - [x] POST /bai-viet với EnsureAdmin middleware ✅

- [x] **Storage Setup**
  - [x] Symlink: `public/storage` → `storage/app/public`
  - [x] Directory: `storage/app/public/articles/` created

## 🎨 FRONTEND FIXES

- [x] **handleImageUpload() function**

  - [x] Validate file: type, size ≤5MB
  - [x] FormData.append("image", file)
  - [x] POST /upload-image without custom Content-Type
  - [x] Set `featuredImage.value = response.data.data.url`
  - [x] Progress bar simulation
  - [x] Error handling: 401/403, 422, generic
  - [x] Reset file input on success

- [x] **publishArticle() function**

  - [x] Validate form fields
  - [x] Include `anh_bai_viet: featuredImage.value`
  - [x] Include `phan_loai_bai_viet_id: selectedCategories[0]`
  - [x] Include `slug` from `generateSlug(title)`
  - [x] POST /bai-viet with all required fields
  - [x] Redirect to /admin/bai-viet on success
  - [x] Error handling & display

- [x] **Form validation (canPublish computed)**

  - [x] Check: title ≥5 chars
  - [x] Check: content ≥10 chars
  - [x] Check: category selected

- [x] **UI/UX**
  - [x] Upload button
  - [x] File input (accept="image/\*")
  - [x] Progress bar
  - [x] Image preview
  - [x] Remove image button
  - [x] Error messages
  - [x] Loading indicators

## 🧪 TEST CHECKLIST

### Upload Image

- [ ] Click "Chọn ảnh"
- [ ] Select jpg/png/gif/webp file (≤5MB)
- [ ] File uploads successfully
  - [ ] Progress bar shows
  - [ ] Console: ✅ Image uploaded: [URL]
  - [ ] Image preview displays
  - [ ] URL in `featuredImage` variable
- [ ] File stored in: `storage/app/public/articles/[filename]`
- [ ] URL accessible via: `http://127.0.0.1:8000/storage/articles/[filename]`

### Publish Article

- [ ] Fill all required fields (title ≥5, content ≥10, category)
- [ ] Upload image
- [ ] Click "Xuất bản"
  - [ ] Console: ✅ Article published
  - [ ] Redirect after 2 seconds
- [ ] Check DevTools Network:
  - [ ] POST /upload-image → 200
  - [ ] POST /bai-viet → 201
- [ ] Check Database:
  ```sql
  SELECT * FROM bai_viets ORDER BY id DESC LIMIT 1;
  -- Should show anh_bai_viet with URL
  ```

### Error Scenarios

- [ ] Upload file >5MB → Error: "Kích thước không được vượt quá 5MB"
- [ ] Upload non-image file → Error: "Vui lòng chọn một file ảnh hợp lệ"
- [ ] Not logged in as Admin → Error: 403 Forbidden
- [ ] Missing required field → Error: validation message
- [ ] Publish without image → Should still work (anh_bai_viet nullable)

## 📊 RESPONSE FORMATS

### Upload Success

```json
{
  "status": true,
  "message": "Tải lên ảnh thành công",
  "data": {
    "url": "http://127.0.0.1:8000/storage/articles/1701695423_abc.jpg",
    "path": "articles/1701695423_abc.jpg",
    "file": "1701695423_abc.jpg"
  }
}
```

### Upload Error (validation)

```json
{
  "status": false,
  "message": "Lỗi xác thực file",
  "errors": {
    "image": ["The image field is required."]
  }
}
```

### Publish Success

```json
{
  "status": true,
  "message": "Tạo bài viết thành công",
  "data": {
    "id": 1,
    "ten_bai_viet": "Tiêu đề",
    "slug": "tieu-de",
    "noi_dung": "Nội dung...",
    "mo_ta": "Mô tả...",
    "anh_bai_viet": "http://127.0.0.1:8000/storage/articles/1701695423_abc.jpg",
    "trang_thai": "published",
    "nhan_vien_id": 1,
    "phan_loai_bai_viet_id": 1,
    "nhanVien": {...},
    "phanLoai": {...}
  }
}
```

### Publish Error (validation)

```json
{
  "status": false,
  "message": "Lỗi xác thực dữ liệu",
  "errors": {
    "ten_bai_viet": ["The ten_bai_viet field is required."],
    "phan_loai_bai_viet_id": ["The selected phan_loai_bai_viet_id is invalid."]
  }
}
```

## 🔧 FILES CHANGED

### Backend

1. ✅ `app/Http/Controllers/UploadController.php` - Fixed
2. ✅ `app/Http/Controllers/BaiVietController.php` - Fixed
3. ✅ `app/Models/BaiViet.php` - Verified (no changes needed)
4. ✅ `routes/api.php` - Verified (routes exist)
5. ✅ `storage/app/public/articles/` - Created

### Frontend

1. ✅ `src/components/Admin/TruyenThong/BaiViet/ThemBaiMoi/index.vue` - Fixed
   - Removed Content-Type header
   - Added better error handling
   - Sending anh_bai_viet in payload

## ❓ POTENTIAL ISSUES & SOLUTIONS

| Issue              | Check                 | Solution                       |
| ------------------ | --------------------- | ------------------------------ |
| 401 Unauthorized   | Token expired         | Logout & login again           |
| 403 Forbidden      | Not admin user        | Verify user type in DB         |
| 404 Not Found      | Route missing         | Check api.php routes           |
| File not saving    | Permissions           | Check storage folder chmod     |
| URL not accessible | Symlink broken        | Run `php artisan storage:link` |
| Image preview 404  | Wrong URL format      | Check URL in response          |
| DB field null      | Field not in fillable | Verify BaiViet model           |

## 📝 ENVIRONMENT VARIABLES

```env
# .env
APP_URL=http://127.0.0.1:8000
FILESYSTEM_DISK=public

# Frontend .env
VITE_API_BASE=http://127.0.0.1:8000/api
```

## 🎯 NEXT STEPS

1. **Test everything in checklist**
2. **Fix any remaining issues**
3. **Deploy to production** (if all tests pass)
4. **Monitor logs** for errors

---

**Last Updated:** 2025-12-04
**Status:** ✅ Ready for Testing
