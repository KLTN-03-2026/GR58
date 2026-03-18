# ✅ FIX UPLOAD ẢNH VÀ LƯU VÀO anh_bai_viet - HOÀN TẤT

## 📋 VẤN ĐỀ ĐÃ FIX

### 1. Upload ảnh

- ❌ Backend chỉ nhận `file` field
- ✅ FIX: Cập nhật validator nhận `image` field

### 2. Lưu URL ảnh vào DB

- ❌ Frontend gửi `anh_bai_viet` nhưng không được lưu
- ✅ FIX: Backend BaiVietController nhận `anh_bai_viet` từ payload

### 3. Content-Type header

- ❌ Set thủ công multipart/form-data
- ✅ FIX: Xóa, để Axios tự động set

## 🔧 CÁC THAY ĐỔI

### Backend: UploadController.php ✅

```php
// ✅ Nhận 'image' field từ frontend
'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',

// ✅ Lưu vào thư mục 'articles'
$path = $file->store('articles', 'public');

// ✅ Trả về URL đầy đủ
$publicUrl = url(Storage::url($path));
```

**Response format:**

```json
{
  "status": true,
  "message": "Tải lên ảnh thành công",
  "data": {
    "url": "http://127.0.0.1:8000/storage/articles/1701695423_abc123.jpg",
    "path": "articles/1701695423_abc123.jpg",
    "file": "1701695423_abc123.jpg"
  }
}
```

### Backend: BaiVietController.php ✅

```php
// ✅ Validate anh_bai_viet (URL)
'anh_bai_viet' => 'nullable|string|url',

// ✅ Payload chứa URL ảnh
$payload = {
  'ten_bai_viet': 'Tiêu đề',
  'noi_dung': 'Nội dung...',
  'mo_ta': 'Mô tả...',
  'anh_bai_viet': 'http://127.0.0.1:8000/storage/articles/1701695423_abc123.jpg',
  'phan_loai_bai_viet_id': 1
}

// ✅ BaiViet::create($validated) sẽ lưu vào DB
```

### Backend: BaiViet Model ✅

```php
protected $fillable = [
    'ten_bai_viet',
    'slug',
    'noi_dung',
    'mo_ta',
    'anh_bai_viet', // ✅ Đã có
    'trang_thai',
    'nhan_vien_id',
    'phan_loai_bai_viet_id',
];
```

### Frontend: ThemBaiMoi/index.vue ✅

```javascript
// ✅ Gửi ảnh lên trước
const response = await client.post("/upload-image", formData);
const imageUrl = response.data.data.url;
featuredImage.value = imageUrl;

// ✅ Sau đó publish bài viết với URL ảnh
const payload = {
  ten_bai_viet: title.value,
  anh_bai_viet: featuredImage.value, // ← URL ảnh
  // ... other fields
};
const publishResponse = await client.post("/bai-viet", payload);
```

## 🧪 FLOW HOÀN CHỈNH

```
User Upload Ảnh
      ↓
1. Frontend: FormData + 'image' field
      ↓
2. POST /upload-image + token
      ↓
3. Backend UploadController:
   - Validate image
   - Store → storage/app/public/articles/
   - Generate URL
   - Return JSON response
      ↓
4. Frontend: Set featuredImage.value = URL
   Preview ảnh hiển thị
      ↓
User Publish Bài Viết
      ↓
5. Frontend: Payload chứa anh_bai_viet: URL
      ↓
6. POST /bai-viet + token + payload
      ↓
7. Backend BaiVietController:
   - Validate payload (include anh_bai_viet URL)
   - BaiViet::create($validated)
   - Lưu vào DB
   - Return JSON response
      ↓
8. Frontend: Redirect to /admin/bai-viet
      ↓
✅ Bài viết + ảnh đã lưu
```

## 📊 DATABASE SCHEMA

```sql
CREATE TABLE bai_viets (
    id INT PRIMARY KEY,
    ten_bai_viet VARCHAR(255) NOT NULL,
    slug VARCHAR(255),
    noi_dung LONGTEXT,
    mo_ta TEXT,
    anh_bai_viet VARCHAR(255), -- ✅ URL ảnh được lưu tại đây
    trang_thai VARCHAR(50), -- published, hidden
    nhan_vien_id INT,
    phan_loai_bai_viet_id INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## ✨ FEATURES HỖ TRỢ

- ✅ Upload ảnh: jpg, png, gif, webp (≤5MB)
- ✅ Tự động generate filename unique
- ✅ Lưu vào public storage (accessible via URL)
- ✅ Lưu URL vào `anh_bai_viet` field
- ✅ Admin-only (middleware EnsureAdmin)
- ✅ Error handling chi tiết
- ✅ Progress bar upload
- ✅ Image preview
- ✅ Remove ảnh option

## 🧪 TEST NGAY

### Bước 1: Upload ảnh

```
GET http://localhost:5173/admin/bai-viet/them-moi
→ Chọn ảnh → Upload
→ Kiểm tra console: ✅ Image uploaded: [URL]
```

### Bước 2: Publish bài viết

```
→ Nhập tiêu đề, nội dung
→ Chọn danh mục
→ Nhấn "Xuất bản"
→ Kiểm tra console: ✅ Article published: {...}
→ Tự động redirect sau 2s
```

### Bước 3: Kiểm tra DB

```sql
SELECT * FROM bai_viets WHERE id = [latest_id];
-- Sẽ thấy anh_bai_viet chứa URL ảnh
```

### Bước 4: Kiểm tra public storage

```
C:\xampp\htdocs\PETTY_VCMS_BE\storage\app\public\articles\
-- Sẽ thấy file ảnh đã lưu
```

## 🔍 DEBUG TIPS

**Nếu vẫn không hoạt động:**

1. **Kiểm tra UploadController logs:**

   ```
   tail -f C:\xampp\htdocs\PETTY_VCMS_BE\storage\logs\laravel.log
   ```

2. **Kiểm tra Network tab (DevTools):**

   - POST /upload-image → Status 200, Response chứa URL?
   - POST /bai-viet → Status 201, Response data.data.anh_bai_viet?

3. **Kiểm tra DB:**

   ```
   SELECT * FROM bai_viets ORDER BY created_at DESC LIMIT 1;
   ```

4. **Kiểm tra file permissions:**
   ```
   Get-Acl "C:\xampp\htdocs\PETTY_VCMS_BE\storage\app\public\articles"
   ```

## ✅ VALIDATION RULES

| Field                 | Rule                | Lỗi nếu                                         |
| --------------------- | ------------------- | ----------------------------------------------- |
| ten_bai_viet          | required, ≥5 chars  | Để trống hoặc < 5 ký tự                         |
| noi_dung              | required, ≥10 chars | Để trống hoặc < 10 ký tự                        |
| phan_loai_bai_viet_id | required, exists    | Không chọn danh mục hoặc danh mục không tồn tại |
| anh_bai_viet          | nullable, URL       | Format URL không đúng (nếu có giá trị)          |
| slug                  | nullable, ≤255      | Không được lưu (backend auto-generate)          |

## 🎯 STATUS

✅ **COMPLETE**

- Upload ảnh
- Lưu URL vào DB
- Redirect sau publish
- Error handling
- Logging

❓ **Chưa test**

- Cần test end-to-end thực tế

---

**Tiếp theo:** Test upload + publish bài viết, kiểm tra DB xem `anh_bai_viet` có URL ảnh không.
