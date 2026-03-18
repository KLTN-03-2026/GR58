# 🎨 PUBLISH ARTICLE FEATURE - VISUAL OVERVIEW

## 📊 Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│         Viết Bài Mới (Write New Article) Page               │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────────────────────────────────────────────┐   │
│  │                   MAIN CONTENT (Left)                │   │
│  ├──────────────────────────────────────────────────────┤   │
│  │ Title Input                  [Tiêu đề bài viết]     │   │
│  ├──────────────────────────────────────────────────────┤   │
│  │ Slug Input         [petty.vn/] [cong-thuc-chua...]  │   │
│  ├──────────────────────────────────────────────────────┤   │
│  │ Editor Toolbar   [B] [I] [U] [H1] [H2] [H3] ...     │   │
│  ├──────────────────────────────────────────────────────┤   │
│  │ Content Editor                                       │   │
│  │ ┌────────────────────────────────────────────────┐  │   │
│  │ │  [Rich text editor area - min 525px height]   │  │   │
│  │ │                                                │  │   │
│  │ │  Nhập nội dung bài viết...                    │  │   │
│  │ └────────────────────────────────────────────────┘  │   │
│  ├──────────────────────────────────────────────────────┤   │
│  │ Excerpt (Tóm tắt)                                    │   │
│  │ ┌────────────────────────────────────────────────┐  │   │
│  │ │ Mô tả ngắn hiển thị khi share Facebook...    │  │   │
│  │ └────────────────────────────────────────────────┘  │   │
│  │ Gợi ý: 150-160 ký tự để hiển thị tốt              │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                               │
│  ┌──────────────────────────────────────────────────────┐   │
│  │             SIDEBAR (Right - 353px)                  │   │
│  ├──────────────────────────────────────────────────────┤   │
│  │ 📤 PUBLISH (Đăng)                                    │   │
│  │ ┌──────────────────────────────────────────────────┐ │   │
│  │ │ ❌ Error Message (if any)                       │ │   │
│  │ │ ✅ Success Message (if success)                 │ │   │
│  │ │                                                  │ │   │
│  │ │ ┌────────────────────────────────────────────┐  │ │   │
│  │ │ │ ✓ Xuất bản (Publish Button)               │  │ │   │
│  │ │ │ Enabled when:                              │  │ │   │
│  │ │ │ - Title ≥5 chars                           │  │ │   │
│  │ │ │ - Content ≥10 chars                        │  │ │   │
│  │ │ │ - Category ≥1 selected                     │  │ │   │
│  │ │ └────────────────────────────────────────────┘  │ │   │
│  │ └──────────────────────────────────────────────────┘ │   │
│  ├──────────────────────────────────────────────────────┤   │
│  │ 📂 CATEGORIES (Phân loại)                            │   │
│  │ ┌──────────────────────────────────────────────────┐ │   │
│  │ │ ☑ 🩺 Kiến thức Thú y                          │ │   │
│  │ │ ☑ 📢 Tin tức & Sự kiện                        │ │   │
│  │ │ ☐ 🎁 Khuyến mãi                               │ │   │
│  │ │ ┌────────────────────────────────────────────┐ │ │   │
│  │ │ │ ⊕ Thêm danh mục mới                      │ │ │   │
│  │ │ └────────────────────────────────────────────┘ │ │   │
│  │ └──────────────────────────────────────────────────┘ │   │
│  ├──────────────────────────────────────────────────────┤   │
│  │ 🖼️ FEATURED IMAGE (Ảnh đại diện)                   │   │
│  │ ┌──────────────────────────────────────────────────┐ │   │
│  │ │ ┌────────────────────────────────────────────┐  │ │   │
│  │ │ │ [IMAGE UPLOAD AREA - 152px height]       │  │ │   │
│  │ │ │                                            │  │ │   │
│  │ │ │  📷                                        │  │ │   │
│  │ │ │  Chưa có ảnh                             │  │ │   │
│  │ │ │  ┌──────────────────────────────┐       │  │ │   │
│  │ │ │  │ ↑ Upload ảnh               │       │  │ │   │
│  │ │ │  └──────────────────────────────┘       │  │ │   │
│  │ │ └────────────────────────────────────────────┘  │ │   │
│  │ │ OR (after upload):                              │ │   │
│  │ │ ┌────────────────────────────────────────────┐  │ │   │
│  │ │ │ [✕ IMAGE PREVIEW]                        │  │ │   │
│  │ │ │ ┌────────────────────────────────────────┐ │  │ │   │
│  │ │ │ │ [Image displayed here - 152px]        │ │  │ │   │
│  │ │ │ └────────────────────────────────────────┘ │  │ │   │
│  │ │ └────────────────────────────────────────────┘  │ │   │
│  │ │ Gợi ý: 1200x630px (tỷ lệ 16:9)                │ │   │
│  │ └──────────────────────────────────────────────────┘ │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔄 Data Flow Diagram

```
User Action                  Component State              API Call

[Click Upload Ảnh]
         │
         ├──> imageInput.click()
         │
[Select Image File]
         │
         ├──> handleImageUpload(event)
         │    ├─ Validate file type (image/*)
         │    ├─ Validate file size (≤5MB)
         │    └─ uploadProgress: 0 → 100%
         │
         ├──────────────────> POST /upload-image ──> [Backend]
         │                   FormData {image: file}   Stores
         │                                            Returns URL
         │
[Receive Response]
         │
         ├─> featuredImage = response.data.url
         │
[Show Image Preview]
         │
         └─> Render with Remove Button (✕)

────────────────────────────────────────────────────────────────

[Fill Title, Content, Select Category]
         │
         ├─> Computed: canPublish checked every keystroke
         │
         └─> Button: enabled/disabled based on canPublish

────────────────────────────────────────────────────────────────

[Click Xuất Bản]
         │
         ├─> publishArticle()
         │    ├─ Validate: canPublish === true
         │    ├─ Generate slug (if not provided)
         │    ├─ isPublishing = true
         │    └─ Button shows "Đang xuất bản..." + spinner
         │
         ├──────────────────> POST /bai-viet ──────> [Backend]
         │                   {                         Validates
         │                     ten_bai_viet,           Creates
         │                     slug,                   Returns article
         │                     noi_dung,
         │                     mo_ta,
         │                     anh_bai_viet,
         │                     phan_loai_bai_viet_id
         │                   }
         │
[Response Received]
         │
         ├─ Success?
         │  ├─ YES: publishSuccess = true
         │  │        setTimeout → router.push() [Redirect after 2s]
         │  │
         │  └─ NO:  publishError = error message
         │         Form stays open for retry
         │
         └─> isPublishing = false
```

---

## 🎯 State Management Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                   Component State (ref)                      │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│ Content Fields:                                               │
│ ├─ title: ""                                                 │
│ ├─ slug: ""                                                  │
│ ├─ content: ""                                               │
│ └─ excerpt: ""                                               │
│                                                               │
│ Categories:                                                   │
│ ├─ categories: []              ← Loaded from API             │
│ ├─ selectedCategories: []      ← User selected              │
│ └─ isLoadingCategories: false  ← Loading state              │
│                                                               │
│ Image Upload:                                                 │
│ ├─ featuredImage: ""           ← Image URL from API         │
│ ├─ imageInput: ref             ← File input element         │
│ ├─ uploadProgress: 0           ← 0-100%                     │
│ ├─ uploadError: ""             ← Error message              │
│ └─ isUploadingImage: false     ← Loading state              │
│                                                               │
│ Publishing:                                                   │
│ ├─ isPublishing: false         ← Loading state              │
│ ├─ publishError: ""            ← Error message              │
│ └─ publishSuccess: ""          ← Success message            │
│                                                               │
│ Modal:                                                        │
│ └─ isAddCategoryModalOpen: false                             │
│                                                               │
├─────────────────────────────────────────────────────────────┤
│                   Computed Properties                         │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│ canPublish: computed (() => {                                │
│   return title.length >= 5 &&                               │
│          content.length >= 10 &&                            │
│          selectedCategories.length > 0                      │
│ })                                                            │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

---

## 📋 Form Validation Flow

```
┌────────────────────────────────────────────────────────┐
│            Form Validation Decision Tree                │
└────────────────────────────────────────────────────────┘

                    START
                      │
                      ▼
            Title length ≥ 5?
              ├─ NO ──> INVALID ─────┐
              └─ YES ──────┐         │
                           ▼         │
            Content length ≥ 10?     │
              ├─ NO ──> INVALID ─────┤
              └─ YES ──────┐         │
                           ▼         │
            Categories count ≥ 1?   │
              ├─ NO ──> INVALID ─────┤
              └─ YES ──────┐         │
                           ▼         │
                        VALID        │
                           │         │
                           ▼         ▼
                    ┌──────────────────┐
                    │ Button State:    │
                    │ ENABLED / DISABLED
                    └──────────────────┘
```

---

## 🔌 API Integration Diagram

```
Frontend (Vue 3)              Backend (Laravel)

[ThemBaiMoi.vue]           [BaiVietController]
      │                              │
      │─ GET ────────────> /api/phan-loai-bai-viet
      │                              │
      │<─ 200 {categories} ─────────│
      │  Categories displayed        │
      │                              │
      │─ POST ────────────> /api/upload-image
      │  FormData {image}            │
      │                              │ [ImageController]
      │<─ 200 {url} ───────────────│
      │  Image preview shown         │
      │                              │
      │─ POST ────────────> /api/bai-viet
      │  {ten_bai_viet,              │
      │   slug,                      │ Validate input
      │   noi_dung,                  │ Create article
      │   anh_bai_viet,              │ Load relationships
      │   phan_loai_bai_viet_id}    │
      │                              │
      │<─ 201 {article} ───────────│
      │  Success + Redirect          │
      │                              │
```

---

## 🎨 Button State Diagram

```
┌──────────────────────────────────────────────────────┐
│              "Xuất Bản" Button States                 │
└──────────────────────────────────────────────────────┘

1. DISABLED (Gray, 50% opacity)
   ┌────────────────────────────────┐
   │      ✓ Xuất bản               │  ← Pointer not-allowed
   └────────────────────────────────┘
   Conditions:
   - Title < 5 chars
   - Content < 10 chars
   - No category selected

2. ENABLED (Green, 100% opacity)
   ┌────────────────────────────────┐
   │      ✓ Xuất bản               │  ← Pointer:pointer
   └────────────────────────────────┘
   Conditions:
   - Title ≥ 5 chars
   - Content ≥ 10 chars
   - ≥ 1 category selected

3. LOADING (Green, with spinner)
   ┌────────────────────────────────┐
   │      ⏳ Đang xuất bản...      │  ← Pointer not-allowed
   └────────────────────────────────┘
   When: POST request in progress

4. HOVER (Green, darker shade)
   ┌────────────────────────────────┐
   │      ✓ Xuất bản               │  ← Background: #008c35
   └────────────────────────────────┘
   When: Mouse over enabled button
```

---

## 📈 Upload Progress Visualization

```
File Selected
    │
    ▼
Validation
    │
    ├─ File type OK?  ─── NO ──> Error: "Chọn file ảnh"
    │
    ├─ File size OK? ─── NO ──> Error: "File quá lớn"
    │
    └─ YES
        │
        ▼
    Start Upload
        │
        ├─ Progress: [▁▁▁▁▁▁▁▁▁▁] 0%
        ├─ Progress: [██▁▁▁▁▁▁▁▁] 20%
        ├─ Progress: [██████▁▁▁▁] 60%
        ├─ Progress: [█████████▁] 90%
        └─ Progress: [██████████] 100%

        ▼
    Response: { url: "..." }
        │
        ▼
    Image Preview
    ┌──────────────────┐
    │ [Image Preview]  │
    │       ✕          │ ← Remove button
    └──────────────────┘
```

---

## 🔐 Security Layers Diagram

```
┌─────────────────────────────────────────────────────┐
│           Security Implementation                    │
└─────────────────────────────────────────────────────┘

Frontend (Client-side):
┌─────────────────────────┐
│ 1. File Type Check      │ ─── image/* only
│ 2. File Size Check      │ ─── ≤ 5MB
│ 3. Form Validation      │ ─── Field validation
│ 4. XSS Prevention       │ ─── Vue escaping
│ 5. CSRF Token          │ ─── Axios headers
└─────────────────────────┘
         │
         ▼
Backend (Server-side):
┌─────────────────────────┐
│ 1. Auth Check           │ ─── Login required
│ 2. Admin Check          │ ─── EnsureAdmin middleware
│ 3. MIME Type Verify     │ ─── Server validation
│ 4. File Scanning        │ ─── Malware detection
│ 5. Permission Check     │ ─── File permissions
│ 6. Rate Limiting        │ ─── Request throttling
└─────────────────────────┘
```

---

## 📊 Success Flow Sequence Diagram

```
User          Browser           Frontend         Backend        Redirect
 │               │                  │              │              │
 │─ Fill Form ──▶│                  │              │              │
 │               │─ Validate ───────▶│              │              │
 │               │◀─ Valid ──────────│              │              │
 │               │                   │              │              │
 │─ Upload Image │                   │              │              │
 │               │─ Select File ────▶│              │              │
 │               │  ─ Progress Bar   │              │              │
 │               │  ─ Preview        │              │              │
 │               │◀─ Image Ready ────│              │              │
 │               │                   │              │              │
 │─ Click Publish│                   │              │              │
 │               │─ POST /bai-viet ─▶│──────────────▶│              │
 │               │  ─ Loading state  │              │─ Validate   │
 │               │  ─ Disable button │              │─ Create    │
 │               │                   │◀─ 201 OK ────│ Load rels  │
 │               │◀─ Success Msg ────│              │              │
 │               │  ─ Green alert    │              │              │
 │               │  ─ Auto-close     │              │              │
 │               │                   │              │              │
 │               │                   ├─ 2 seconds ──▶│──────────▶│
 │               │◀─ Redirect Cmd ───│              │           Redirect
 │               │                   │              │           Success
 │◀─ Redirected ─│                   │              │              │
```

---

## ❌ Error Flow Sequence Diagram

```
User          Browser           Frontend         Backend
 │               │                  │              │
 │─ Fill Form ──▶│                  │              │
 │               │─ Validate ───────▶│              │
 │               │◀─ Valid ──────────│              │
 │               │                   │              │
 │─ Click Publish│                   │              │
 │               │─ POST /bai-viet ─▶│──────────────▶│
 │               │  ─ Loading state  │              │─ Validate
 │               │  ─ Disable button │              │─ Error!
 │               │                   │◀─ 422 Error ─│
 │               │◀─ Error Msg ──────│              │
 │               │  ─ Red alert      │              │
 │               │  ─ Form stays open│              │
 │               │  ─ Button enabled │              │
 │               │                   │              │
 │─ Fix & Retry ▶│                   │              │
 │               │─ POST /bai-viet ─▶│──────────────▶│
 │               │                   │              │ ✓ Success
 │               │◀─ Success Msg ────│◀─ 201 OK ────│
 │               │  ─ Redirect       │              │
```

---

## 🎯 Component Lifecycle Diagram

```
Component Mount
      │
      ▼
┌──────────────┐
│ onMounted()  │
└──────────────┘
      │
      ├──▶ fetchCategories()
      │    ├─ GET /phan-loai-bai-viet
      │    └─ Store in categories ref
      │
      └──▶ Set up reactive state
           ├─ title, content, excerpt
           ├─ featuredImage
           ├─ selectedCategories
           └─ error/success messages

User Interaction
      │
      ├──▶ Edit form → Computed canPublish updates
      ├──▶ Upload image → handleImageUpload()
      ├──▶ Select category → selectedCategories updates
      └──▶ Click Publish → publishArticle()

Component Cleanup
      │
      ├──▶ Modal closes → Form resets
      └──▶ Navigation away → State cleared
```

---

## 📱 Responsive Layout

```
Desktop (Full Width)           Mobile (Responsive)
┌─────────────────────┐       ┌──────────────────┐
│  Main (flex-1)      │       │ Main              │
│ ┌─────────────────┐ │       │ ┌────────────────┐│
│ │ Title           │ │       │ │ Title          ││
│ │ Slug            │ │       │ │ Slug           ││
│ │ Editor          │ │       │ │ Editor         ││
│ │ (min-h: 525px)  │ │       │ │ (responsive)   ││
│ │ Excerpt         │ │       │ │ Excerpt        ││
│ └─────────────────┘ │       │ └────────────────┘│
├─────────────────────┤       ├──────────────────┤
│ Sidebar (w-353px)   │  ──▶  │ Sidebar          │
│ ┌─────────────────┐ │       │ (w-full)         │
│ │ Publish         │ │       │ ┌────────────────┐│
│ │ Categories      │ │       │ │ Publish        ││
│ │ Image           │ │       │ │ Categories     ││
│ └─────────────────┘ │       │ │ Image          ││
└─────────────────────┘       │ └────────────────┘│
                              └──────────────────┘
```

---

## 🎬 Summary: Key Interactions

| Interaction     | State Before           | Action          | State After                            |
| --------------- | ---------------------- | --------------- | -------------------------------------- |
| Type Title      | title=""               | Type 5+ chars   | title="..." canPublish=true            |
| Type Content    | content=""             | Type 10+ chars  | content="..." canPublish=true          |
| Remove Category | selectedCategories=[1] | Click uncheck   | selectedCategories=[] canPublish=false |
| Upload Image    | featuredImage=""       | Select file     | featuredImage="url" uploadProgress=100 |
| Remove Image    | featuredImage="url"    | Click ✕         | featuredImage="" uploadError=""        |
| Click Publish   | isPublishing=false     | Click button    | isPublishing=true button disabled      |
| Get Success     | isPublishing=true      | API returns 201 | publishSuccess="Thành công" redirect   |
| Get Error       | isPublishing=true      | API returns 422 | publishError="..." form stays open     |

---

**Status:** ✅ FRONTEND COMPLETE

**Last Updated:** 2025-12-04

**Ready for:** Backend integration and testing
