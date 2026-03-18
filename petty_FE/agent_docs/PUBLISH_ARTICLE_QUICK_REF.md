# 🚀 PUBLISH ARTICLE - QUICK REFERENCE

## ✨ What's New

```
✅ Image Upload
   - File selection with input
   - Progress bar (0-100%)
   - File validation (type + size)
   - Image preview with remove button

✅ Publish Button
   - Form validation
   - Loading state
   - Error/success messages
   - Auto-redirect on success

✅ Form Validation
   - Title: ≥5 characters
   - Content: ≥10 characters
   - Category: ≥1 selected
   - Button disabled until valid
```

---

## 🎯 How to Use

### 1. Fill Article Form

```
Title:      "Công thức chữa bệnh cho chó" (≥5 chars)
Content:    "Nội dung chi tiết..." (≥10 chars)
Excerpt:    "Mô tả ngắn..." (optional)
Slug:       Auto-generated (optional)
Category:   Select at least 1 (required)
Image:      Upload (optional)
```

### 2. Upload Featured Image

```
1. Click "Upload ảnh" button
2. Select image from computer
3. Progress bar shows upload progress
4. Image preview appears
5. Click ✕ to remove if needed
```

### 3. Publish Article

```
1. Fill all required fields
2. "Xuất bản" button becomes enabled
3. Click "Xuất bản"
4. Loading state shows "Đang xuất bản..."
5. Success message: "Xuất bản bài viết thành công!"
6. Redirects to articles list after 2 seconds
```

---

## 📊 Component Features

| Feature         | Status | Notes              |
| --------------- | ------ | ------------------ |
| Image upload    | ✅     | With progress bar  |
| Image preview   | ✅     | Shows after upload |
| Remove image    | ✅     | Click ✕ button     |
| Form validation | ✅     | Frontend checks    |
| Slug generation | ✅     | Auto from title    |
| Category select | ✅     | Multiple selection |
| Publish API     | ✅     | POST /bai-viet     |
| Error handling  | ✅     | Detailed messages  |
| Success message | ✅     | Auto-redirect      |
| Loading states  | ✅     | Button + spinner   |

---

## 🔑 Key Methods

### Image Upload

```javascript
handleImageUpload(event);
// Validates and uploads image
// Shows progress 0-100%
// Stores URL in featuredImage
```

### Remove Image

```javascript
removeFeaturedImage();
// Clears featured image
// Resets file input
// Clears error messages
```

### Publish Article

```javascript
publishArticle();
// Validates form
// Generates slug
// Sends POST /bai-viet
// Redirects on success
```

### Generate Slug

```javascript
generateSlug(text);
// Converts: "Công thức chữa bệnh"
// To slug: "cong-thuc-chua-benh"
```

---

## 📋 Validation Rules

### Title

```
✓ Must be provided
✓ Minimum 5 characters
✓ Maximum 255 characters (backend)
```

### Content

```
✓ Must be provided
✓ Minimum 10 characters
✓ No maximum limit enforced
```

### Excerpt

```
✓ Optional
✓ Maximum 1000 characters (backend)
✓ Recommended 150-160 chars for social media
```

### Category

```
✓ At least 1 must be selected
✓ Sends first selected ID to backend
```

### Image

```
✓ Optional
✓ Must be image/* type
✓ Maximum 5MB file size
✓ Recommended 1200x630px
```

---

## 🔌 API Integration

### Upload Image

```
Endpoint: POST /api/upload-image
Method: FormData
Field: image (file)

Response: { status: true, data: { url: '...' } }
```

### Publish Article

```
Endpoint: POST /api/bai-viet
Method: JSON

Payload:
{
  "ten_bai_viet": "...",      // Required
  "slug": "...",              // Required
  "noi_dung": "...",          // Required
  "mo_ta": "...",             // Optional
  "anh_bai_viet": "...",      // Optional
  "trang_thai": "published",  // Fixed
  "phan_loai_bai_viet_id": 1  // Required
}

Response: {
  status: true,
  message: "...",
  data: { ... }
}
```

---

## 🧪 Test Scenarios

### Test 1: Upload Image

```
Input: Select a valid image file
Expected: Progress bar, preview, remove button
```

### Test 2: Validate Title

```
Input: Title < 5 chars
Expected: "Xuất bản" button disabled
```

### Test 3: Validate Content

```
Input: Content < 10 chars
Expected: "Xuất bản" button disabled
```

### Test 4: Select Category

```
Input: No category selected
Expected: "Xuất bản" button disabled
→ Select category
Expected: "Xuất bản" button enabled
```

### Test 5: Publish Success

```
Input: All fields valid
Action: Click "Xuất bản"
Expected:
- "Đang xuất bản..." message
- After 2 seconds: redirect to articles list
```

### Test 6: Publish Error

```
Input: API returns error
Expected: Error message displayed
- Form stays open
- Can fix and retry
```

---

## 🎨 UI States

### Button States

**Disabled (Form Invalid)**

```
Title < 5 chars OR
Content < 10 chars OR
No category selected
↓
"Xuất bản" button disabled (50% opacity)
```

**Enabled (Form Valid)**

```
All required fields filled correctly
↓
"Xuất bản" button enabled (100% opacity, hover effect)
```

**Publishing**

```
User clicks button
↓
Button text: "Đang xuất bản..."
Loading spinner: ⏳
Button disabled: true
```

**Success**

```
Article published
↓
Message: "Xuất bản bài viết thành công!"
Success color: Green (#009689)
Auto-redirect: After 2 seconds
```

**Error**

```
API returns error
↓
Message: Error details
Error color: Red (#dc2626)
Form stays open: User can retry
```

---

## 💻 Usage Example

```vue
<template>
  <!-- Article form -->
  <div>
    <!-- Title input -->
    <input v-model="title" placeholder="Tiêu đề..." />

    <!-- Content editor -->
    <textarea v-model="content" placeholder="Nội dung..." />

    <!-- Featured image upload -->
    <div>
      <button v-if="!featuredImage" @click="$refs.imageInput.click()">
        Upload ảnh
      </button>
      <img v-else :src="featuredImage" />
      <button v-if="featuredImage" @click="removeFeaturedImage">Remove</button>

      <input ref="imageInput" type="file" @change="handleImageUpload" hidden />
    </div>

    <!-- Upload progress -->
    <div v-if="uploadProgress > 0" class="progress-bar">
      {{ uploadProgress }}%
    </div>

    <!-- Publish button -->
    <button @click="publishArticle" :disabled="!canPublish || isPublishing">
      {{ isPublishing ? "Đang xuất bản..." : "Xuất bản" }}
    </button>

    <!-- Messages -->
    <div v-if="publishError" class="error">{{ publishError }}</div>
    <div v-if="publishSuccess" class="success">{{ publishSuccess }}</div>
  </div>
</template>
```

---

## 📝 Supported File Types

**Images:**

- image/jpeg (.jpg, .jpeg)
- image/png (.png)
- image/gif (.gif)
- image/webp (.webp)
- image/svg+xml (.svg)
- image/x-icon (.ico)
- And all other image/\* types

**File Size Limits:**

- Maximum: 5MB (5,242,880 bytes)
- Recommended: 1-2MB for optimal performance

**Image Dimensions:**

- Recommended: 1200x630px (16:9 ratio)
- Minimum: 600x315px (for proper display)
- No strict maximum (will be responsive)

---

## 🔐 Security Features

✅ **Frontend Validation**

- File type check (image/\*)
- File size check (≤5MB)
- XSS prevention (framework handles)

✅ **Backend Security** (Expected)

- MIME type verification
- File scanning for malware
- Sanitized filename storage
- Proper permission checks (EnsureAdmin middleware)

---

## ⚡ Performance Notes

1. **Progress Bar**

   - Updates every 200ms
   - Simulated progress while uploading
   - Actual completion percentage shown

2. **Image Preview**

   - Loaded immediately from URL
   - Responsive sizing
   - Cached by browser

3. **Form Submission**
   - Debounced validation
   - Single API call per submit
   - No duplicate submissions (button disabled during load)

---

## 🎯 Complete Workflow

```
Step 1: Open "Viết bài mới"
        ↓
Step 2: Fill form fields
        ├─ Title
        ├─ Content
        └─ Categories
        ↓
Step 3: Upload featured image (optional)
        ├─ Click "Upload ảnh"
        ├─ Select image
        ├─ See progress bar
        └─ See preview
        ↓
Step 4: Review form validity
        └─ "Xuất bản" button becomes enabled
        ↓
Step 5: Click "Xuất bản"
        ├─ Button shows "Đang xuất bản..."
        ├─ Loading spinner appears
        └─ Data sent to API
        ↓
Step 6: Success/Error
        ├─ Success: Show message + redirect
        └─ Error: Show message, keep form open
        ↓
Done!
```

---

## 📞 Support

**Error Messages:**

| Message                         | Cause          | Solution                 |
| ------------------------------- | -------------- | ------------------------ |
| "Vui lòng điền đầy đủ..."       | Form invalid   | Fill all required fields |
| "Kích thước file..."            | File >5MB      | Use smaller image        |
| "Vui lòng chọn một file ảnh..." | Not image      | Select image file        |
| "Lỗi khi tải lên ảnh"           | Upload failed  | Check network, retry     |
| "Lỗi khi xuất bản..."           | Publish failed | Check form, try again    |

---

**Status:** ✅ PRODUCTION READY

**Last Updated:** 2025-12-04

**Version:** 1.0.0
