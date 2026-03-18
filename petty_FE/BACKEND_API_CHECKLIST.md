# 🔧 BACKEND API REQUIREMENTS - INTEGRATION CHECKLIST

## 📋 Required Endpoints

### 1. Upload Image Endpoint ❌ (NEEDS TO BE CREATED)

**Location:** Must add to routes or controller

```php
// In routes/api.php
Route::post('/upload-image', [ImageController::class, 'store'])->middleware('auth');
```

**Controller Method:**

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'image' => 'required|image|mimes:jpeg,png,gif,webp,svg|max:5120', // 5MB
            ]);

            // Store image
            $path = $request->file('image')->store('bai-viet', 'public');
            $url = asset('storage/' . $path);

            return response()->json([
                'status' => true,
                'message' => 'Tải lên ảnh thành công',
                'data' => [
                    'url' => $url,
                    'path' => $path,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tải lên ảnh',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

**Request:**

```
POST /api/upload-image
Content-Type: multipart/form-data

Form Data:
- image: <file>
- Authorization: Bearer <token>
```

**Response (200):**

```json
{
  "status": true,
  "message": "Tải lên ảnh thành công",
  "data": {
    "url": "https://petty.vn/storage/bai-viet/...",
    "path": "bai-viet/..."
  }
}
```

**Error Response (422):**

```json
{
  "status": false,
  "message": "The image field must be an image.",
  "errors": {
    "image": ["The image field must be an image."]
  }
}
```

---

### 2. Create Article Endpoint ✅ (ALREADY EXISTS)

**Location:** `routes/api.php` - Line with:

```php
Route::post('/bai-viet', [BaiVietController::class, 'store'])->middleware('EnsureAdmin::class');
```

**Controller:** `App/Http/Controllers/BaiVietController.php`

**Method:** `store(StoreBaiVietRequest $request)`

**Request Body:**

```json
{
  "ten_bai_viet": "Công thức chữa bệnh cho chó",
  "slug": "cong-thuc-chua-benh-cho-cho",
  "noi_dung": "Nội dung bài viết...",
  "mo_ta": "Mô tả ngắn",
  "anh_bai_viet": "https://petty.vn/storage/bai-viet/...",
  "trang_thai": "published",
  "phan_loai_bai_viet_id": 1
}
```

**Response (201):**

```json
{
  "status": true,
  "message": "Tạo bài viết thành công",
  "data": {
    "id": 1,
    "ten_bai_viet": "Công thức chữa bệnh cho chó",
    "slug": "cong-thuc-chua-benh-cho-cho",
    "noi_dung": "Nội dung bài viết...",
    "mo_ta": "Mô tả ngắn",
    "anh_bai_viet": "https://petty.vn/storage/bai-viet/...",
    "trang_thai": "published",
    "nhan_vien_id": 1,
    "phan_loai_bai_viet_id": 1,
    "created_at": "2025-12-04T10:30:00",
    "updated_at": "2025-12-04T10:30:00",
    "nhanVien": { ... },
    "phanLoai": { ... }
  }
}
```

**Error Response (422):**

```json
{
  "status": false,
  "message": "Validation error",
  "errors": {
    "ten_bai_viet": ["The ten_bai_viet field is required."],
    "noi_dung": ["The noi_dung field is required."]
  }
}
```

---

## ✅ Integration Checklist

### Database Setup

- [ ] `bai_viets` table has these columns:

  ```
  id (PK)
  ten_bai_viet (string, 255)
  slug (string, 255, unique)
  noi_dung (text)
  mo_ta (text, nullable)
  anh_bai_viet (string, nullable) - URL or path
  trang_thai (string) - 'published', 'hidden', 'draft'
  nhan_vien_id (FK → nhan_viels.id)
  phan_loai_bai_viet_id (FK → phan_loai_bai_viets.id)
  created_at
  updated_at
  ```

- [ ] `phan_loai_bai_viets` table exists with:

  ```
  id (PK)
  ten_phan_loai (string, 255)
  slug (string, 255, unique)
  mo_ta (text, nullable)
  created_at
  updated_at
  ```

- [ ] Relationships defined in models:

  ```php
  // BaiViet model
  public function nhanVien() {
    return $this->belongsTo(NhanVien::class);
  }

  public function phanLoai() {
    return $this->belongsTo(PhanLoaiBaiViet::class);
  }
  ```

### Routes

- [ ] `/api/bai-viet` POST endpoint exists
- [ ] `/api/upload-image` POST endpoint created
- [ ] `/api/phan-loai-bai-viet` GET endpoint exists
- [ ] Proper middleware applied:
  - [ ] `EnsureAdmin` on POST /bai-viet
  - [ ] `auth` on POST /upload-image

### Controllers

- [ ] `BaiVietController` exists with:

  - [ ] `index()` - GET all articles
  - [ ] `store()` - POST create article ✅
  - [ ] `show()` - GET single article
  - [ ] `update()` - PUT update article
  - [ ] `destroy()` - DELETE article

- [ ] `ImageController` created with:
  - [ ] `store()` - POST upload image ❌ **NEEDS TO BE CREATED**

### Validation

- [ ] `StoreBaiVietRequest` exists with validation rules:

  ```php
  'ten_bai_viet' => 'required|string|max:255',
  'slug' => 'required|string|max:255|unique:bai_viets',
  'noi_dung' => 'required|string',
  'mo_ta' => 'nullable|string|max:1000',
  'anh_bai_viet' => 'nullable|string|url', // or just string if it's a path
  'trang_thai' => 'required|in:published,hidden,draft',
  'phan_loai_bai_viet_id' => 'required|exists:phan_loai_bai_viets,id',
  ```

- [ ] Image upload validation:
  ```php
  'image' => 'required|image|mimes:jpeg,png,gif,webp,svg|max:5120',
  ```

### File Storage

- [ ] Storage disk configured in `config/filesystems.php`
- [ ] Public storage symlink created:
  ```bash
  php artisan storage:link
  ```
- [ ] Upload directory permissions set correctly:
  ```
  storage/app/public/bai-viet/
  ```
- [ ] Image URLs returned as full URLs (not just paths)

### Authentication

- [ ] User authentication working
- [ ] `nhan_vien_id` correctly set from `auth()->id()`
- [ ] Admin check working via `EnsureAdmin` middleware
- [ ] Token/session passed from frontend

### Error Handling

- [ ] 422 Validation errors properly formatted
- [ ] 500 Internal errors have try-catch blocks
- [ ] Error messages in Vietnamese
- [ ] Field-level errors in `errors` object

### Response Format

- [ ] All responses use:

  ```json
  {
    "status": true|false,
    "message": "...",
    "data": {...}  // or null
  }
  ```

- [ ] Relationships eager-loaded with `->with()`
- [ ] Sensitive data not exposed

### CORS & Security

- [ ] CORS configured if frontend on different domain
- [ ] CSRF protection (if applicable)
- [ ] File size limits enforced
- [ ] File type validation enforced
- [ ] Admin authorization enforced

---

## 🛠️ Implementation Guide

### Step 1: Create ImageController

**File:** `app/Http/Controllers/ImageController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Store uploaded image
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'image' => 'required|image|mimes:jpeg,png,gif,webp,svg|max:5120',
            ]);

            // Get file from request
            $file = $request->file('image');

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Store in public disk under bai-viet directory
            $path = $file->storeAs('bai-viet', $filename, 'public');

            // Get full URL
            $url = asset('storage/' . $path);

            return response()->json([
                'status' => true,
                'message' => 'Tải lên ảnh thành công',
                'data' => [
                    'url' => $url,
                    'path' => $path,
                    'filename' => $filename,
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tải lên ảnh',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

### Step 2: Add Route

**File:** `routes/api.php`

```php
// Add this line (usually near other image/file routes)
Route::post('/upload-image', [ImageController::class, 'store'])->middleware('auth');
```

### Step 3: Create Migration (if needed)

The `bai_viets` table should already exist, but verify it has:

```php
Schema::create('bai_viets', function (Blueprint $table) {
    $table->id();
    $table->string('ten_bai_viet');
    $table->string('slug')->unique();
    $table->longText('noi_dung');
    $table->text('mo_ta')->nullable();
    $table->string('anh_bai_viet')->nullable(); // Store URL or path
    $table->enum('trang_thai', ['published', 'hidden', 'draft'])->default('draft');
    $table->foreignId('nhan_vien_id')->constrained('nhan_viels');
    $table->foreignId('phan_loai_bai_viet_id')->constrained('phan_loai_bai_viets');
    $table->timestamps();
});
```

### Step 4: Setup Storage

```bash
# Create symlink for public storage (if not already done)
php artisan storage:link

# Set permissions
chmod -R 755 storage/app/public
chmod -R 755 public/storage
```

### Step 5: Test Endpoints

**Test Upload Image:**

```bash
curl -X POST http://localhost/api/upload-image \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "image=@/path/to/image.jpg"
```

**Test Create Article:**

```bash
curl -X POST http://localhost/api/bai-viet \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "ten_bai_viet": "Test Article",
    "slug": "test-article",
    "noi_dung": "Article content here...",
    "mo_ta": "Short description",
    "anh_bai_viet": "https://...",
    "trang_thai": "published",
    "phan_loai_bai_viet_id": 1
  }'
```

---

## 📝 Expected Frontend -> Backend Flow

```
Frontend                          Backend
   |                                |
   |-- 1. POST /upload-image ------>|
   |<----- 200 { url: "..." } -------|
   |                                |
   |-- 2. POST /bai-viet ---------->|
   |     (with anh_bai_viet: url)   |
   |<----- 201 { data: {...} } -----|
   |                                |
```

---

## ✨ Frontend Assumptions

The frontend expects:

1. **Image Upload Response:**

   ```json
   {
     "status": true,
     "data": {
       "url": "https://petty.vn/storage/bai-viet/1733302200_abc123.jpg"
     }
   }
   ```

2. **Article Create Response:**

   ```json
   {
     "status": true,
     "message": "Tạo bài viết thành công",
     "data": {
       "id": 1,
       "ten_bai_viet": "...",
       "slug": "...",
       ...
     }
   }
   ```

3. **Error Response:**
   ```json
   {
     "status": false,
     "message": "Error description",
     "errors": {
       "field_name": ["Error message"]
     }
   }
   ```

---

## 🧪 Test Checklist

- [ ] Image upload works with valid image
- [ ] Image upload rejects non-image files
- [ ] Image upload rejects >5MB files
- [ ] Image stored in correct directory
- [ ] Image URL returned correctly
- [ ] Article creation with all fields works
- [ ] Article creation rejects missing required fields
- [ ] Article slug auto-generated (if implemented on backend)
- [ ] Article relationships loaded correctly
- [ ] Admin-only access enforced
- [ ] All error messages in Vietnamese
- [ ] Response format consistent

---

## 📊 Status

| Component        | Status | Notes                      |
| ---------------- | ------ | -------------------------- |
| Get Categories   | ✅     | Already implemented        |
| Create Category  | ✅     | Already implemented        |
| Create Article   | ✅     | Already implemented        |
| **Upload Image** | ❌     | **NEEDS TO BE CREATED**    |
| Database Schema  | ✅     | Should exist               |
| Validation       | ✅     | StoreBaiVietRequest exists |
| Error Handling   | ✅     | Try-catch in place         |
| Relationships    | ✅     | With eager loading         |

---

## 🎯 Next Steps

1. **Create ImageController** with upload logic
2. **Add route** for `/upload-image` POST
3. **Setup file storage** permissions
4. **Test upload** endpoint
5. **Verify response** format
6. **Test full flow** from frontend

---

**Last Updated:** 2025-12-04
**Priority:** HIGH - Blocks publish feature
**Estimated Time:** 30 minutes to implement
