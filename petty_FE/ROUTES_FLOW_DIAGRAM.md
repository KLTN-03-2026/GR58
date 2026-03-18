# 🗺️ PETTY VCMS - Routes & Navigation Flow

## 📍 Routes

### Admin - Bài Viết (Posts)

```
/admin/bai-viet
├── GET   → Danh sách bài viết
├── POST  → Tạo bài viết mới
│
├── /them-moi
│   ├── GET  → Form tạo bài viết
│   └── POST → Save bài viết
│
├── /chinh-sua/:id
│   ├── GET   → Form chỉnh sửa bài viết ✅ NEW
│   └── PATCH → Update bài viết ✅ NEW
│
└── /xoa/:id
    └── DELETE → Xóa bài viết
```

---

## 🔀 Navigation Flow

### 1️⃣ Danh Sách Bài Viết (List)

```
Page: /admin/bai-viet
Component: src/components/Admin/TruyenThong/BaiViet/index.vue

┌─────────────────────────────────────┐
│    Quản Lý Bài Viết                 │
│  ┌─────────────────┐  [Viết Bài Mới]│
│  │ Search Box      │                 │
│  │ Filters (2)     │                 │
│  └─────────────────┘                 │
│                                      │
│  ┌──────────────────────────────────┐│
│  │ Tiêu Đề      │ Danh Mục │ Ngày  ││
│  │─────────────────────────────────││
│  │ Bài 1        │ Kiến thức│ 4/12  │ [✏️ 🗑️]
│  │ Bài 2        │ Tin tức  │ 3/12  │ [✏️ 🗑️]
│  │ Bài 3        │ (none)   │ 2/12  │ [✏️ 🗑️]
│  └──────────────────────────────────┘│
└─────────────────────────────────────┘
        ↓                    ↓
    [Click ✏️]      [Click Viết Bài Mới]
        ↓                    ↓
    /chinh-sua/1        /them-moi
```

### 2️⃣ Tạo Bài Viết (Create)

```
Page: /admin/bai-viet/them-moi
Component: src/components/Admin/TruyenThong/BaiViet/ThemBaiMoi/index.vue

┌──────────────────────────────────────┐
│ ← Quản Lý Bài Viết │ Viết Bài Mới   │
├──────────────────────────────────────┤
│ Tiêu đề:                             │
│ [Input tiêu đề]                      │
│                                      │
│ Slug:                                │
│ petty.vn/ [auto-slug]                │
│                                      │
│ ┌──────────────────────────────────┐ │
│ │ EDITOR TOOLBAR                   │ │
│ │ [B][I][U][H1][H2][H3][Lists]...  │ │
│ │                                  │ │
│ │ [Nội dung bài viết...]           │ │
│ │                                  │ │
│ └──────────────────────────────────┘ │
│                                      │
│ Tóm tắt:                             │
│ [Textarea 3 lines]                   │
│                    SIDEBAR:          │
│                    ┌──────────────┐  │
│                    │ Xuất Bản     │  │
│                    │ [Publish]    │  │
│                    ├──────────────┤  │
│                    │ Phân Loại    │  │
│                    │ [Checkboxes] │  │
│                    ├──────────────┤  │
│                    │ Ảnh Đại Diện │  │
│                    │ [Upload Area]│  │
│                    └──────────────┘  │
└──────────────────────────────────────┘
            ↓
    [Click Xuất Bản]
            ↓
      POST /bai-viet
            ↓
    Redirect to /admin/bai-viet
```

### 3️⃣ Chỉnh Sửa Bài Viết (Edit) ✅ NEW

```
Page: /admin/bai-viet/chinh-sua/1
Component: src/components/Admin/TruyenThong/BaiViet/ChinhSuaBaiViet/index.vue

┌──────────────────────────────────────┐
│ ← Quản Lý Bài Viết │ Chỉnh Sửa Bài  │
├──────────────────────────────────────┤
│ Tiêu đề:                             │
│ [Cách chăm sóc chó Poodle (editable)]│
│                                      │
│ Slug:                                │
│ petty.vn/ [cach-cham-soc-cho-poodle] │
│            (auto-update khi edit)    │
│                                      │
│ ┌──────────────────────────────────┐ │
│ │ EDITOR TOOLBAR                   │ │
│ │ [B][I][U][H1][H2][H3][Lists]...  │ │
│ │                                  │ │
│ │ [Nội dung bài viết đầy đủ...]    │ │
│ │                                  │ │
│ └──────────────────────────────────┘ │
│                                      │
│ Tóm tắt:                             │
│ [Mô tả hiện tại...]                  │
│                    SIDEBAR:          │
│                    ┌──────────────┐  │
│                    │ Đăng         │  │
│                    │ [✓ Cập Nhật] │  │
│                    │              │  │
│                    │ Trạng Thái:  │  │
│                    │ Đã xuất bản  │  │
│                    │ Tác Giả:     │  │
│                    │ Nguyễn Văn A │  │
│                    ├──────────────┤  │
│                    │ Phân Loại    │  │
│                    │ [Dropdown ▼] │  │
│                    │ Hiện tại:    │  │
│                    │ Kiến thức    │  │
│                    ├──────────────┤  │
│                    │ Ảnh Đại Diện │  │
│                    │ [Image 128px]│  │
│                    │ [x Xóa]      │  │
│                    │ 1200x630px   │  │
│                    └──────────────┘  │
└──────────────────────────────────────┘
             ↓
     [Click Cập Nhật]
             ↓
      PATCH /bai-viet/1
             ↓
    ✅ Cập nhật thành công!
             ↓
      Redirect (1.5s) to /admin/bai-viet
```

### 4️⃣ Xóa Bài Viết (Delete)

```
List Page: /admin/bai-viet
         ↓
    [Click 🗑️]
         ↓
  Modal Xác Nhận:
  ┌─────────────────────────┐
  │ Xóa Bài Viết            │
  │ Bạn có chắc chắn xóa    │
  │ "Tiêu đề bài viết"?     │
  │                         │
  │ [Hủy]  [Xác Nhận]       │
  └─────────────────────────┘
         ↓
    [Click Xác Nhận]
         ↓
    DELETE /bai-viet/1
         ↓
    Refresh danh sách
```

---

## 📊 State Management

### Global State Variables

```javascript
// List Page (index.vue)
posts[] = []                    // Tất cả bài viết
categories[] = []               // Tất cả danh mục
searchQuery = ""                // Từ khóa tìm kiếm
selectedCategoryFilter = null   // Lọc danh mục
selectedStatusFilter = null     // Lọc trạng thái
isLoading = false              // Loading indicator
filteredPosts = []             // Computed: tìm kiếm + lọc

// Edit Page (ChinhSuaBaiViet/index.vue)
post = {
  id, title, slug, content, excerpt,
  status, statusLabel,
  categoryId, categoryName,
  thumbnail, nhanVienName
}
categories[] = []              // Danh mục cho dropdown
isLoading = false             // Loading post data
isSaving = false              // Saving to backend
saveError = ""                // Error message
saveSuccess = ""              // Success message
```

---

## 🔄 Data Flow

### Fetch List

```
Component Mount
    ↓
onMounted()
    ├─ fetchPosts()  → GET /bai-viet
    │   └─ Map data  → posts[]
    └─ fetchCategories()  → GET /phan-loai-bai-viet
        └─ Save data → categories[]
    ↓
Computed filteredPosts
    ├─ Search by: title, summary, author
    ├─ Filter by: category, status
    └─ Return filtered result
    ↓
Template renders filteredPosts
```

### Edit Post

```
User clicks [✏️] on list
    ↓
Navigate to /admin/bai-viet/chinh-sua/1
    ↓
Component Mount
    ├─ isLoading = true
    ├─ fetchPost(id=1)  → GET /bai-viet/1
    │   └─ Populate: post object
    ├─ fetchCategories()  → GET /phan-loai-bai-viet
    │   └─ Populate: categories[]
    └─ isLoading = false
    ↓
Form displays with data
    ↓
User edits fields
    ├─ title input  → titleWatcher()
    │   └─ slug auto-update
    ├─ content textarea
    ├─ excerpt textarea
    └─ category dropdown
    ↓
[Click Cập Nhật]
    ↓
updatePost()
    ├─ Validate: title, content required
    ├─ isSaving = true
    ├─ PATCH /bai-viet/1  [payload]
    ├─ If success:
    │   ├─ showSuccess()
    │   ├─ setTimeout(redirect, 1500)
    │   └─ router.push('/admin/bai-viet')
    └─ If error:
        └─ showError(message)
```

---

## 🎯 Component Interaction

```
┌─────────────────────────────────────────────────────┐
│           App.vue (Router Setup)                    │
├─────────────────────────────────────────────────────┤
│                                                      │
│  Router:                                             │
│  /admin/bai-viet → index.vue (List)                 │
│  /admin/bai-viet/them-moi → ThemBaiMoi/index.vue    │
│  /admin/bai-viet/chinh-sua/:id → ChinhSua/index.vue │
│                                                      │
│  ┌──────────────┐      ┌──────────────┐             │
│  │ List Page    │────→ │ Edit Page    │             │
│  │ index.vue    │ :id  │ ChinhSua/    │             │
│  │              │←──── │ index.vue    │             │
│  └──────────────┘      └──────────────┘             │
│        ↓                                             │
│        └─→ [click ✏️] → navigate → [Component Mount]│
│                                                      │
│  ┌──────────────┐      ┌──────────────┐             │
│  │ List Page    │      │ Create Page  │             │
│  │ index.vue    │←────→ │ ThemBaiMoi/  │             │
│  │              │      │ index.vue    │             │
│  └──────────────┘      └──────────────┘             │
│        ↓                                             │
│        └─→ [click +] → navigate → [Component Mount] │
│                                                      │
└─────────────────────────────────────────────────────┘
```

---

## 📡 API Communication

### Request/Response Pattern

```
Frontend Component
    ↓
axios (via client.js)
    ↓
Backend API (/api/...)
    ├─ /bai-viet GET|POST
    ├─ /bai-viet/:id GET|PATCH|DELETE
    └─ /phan-loai-bai-viet GET
    ↓
Response (JSON)
    ├─ status: boolean
    ├─ message: string
    └─ data: object|array
    ↓
Frontend handles response
    ├─ Success: update UI
    └─ Error: show message
```

---

## 🔐 Authentication Flow

```
Login
    ↓
Get Token (Sanctum)
    ↓
Store in localStorage/session
    ↓
API Requests
    └─ Header: Authorization: Bearer {token}
    ↓
Backend Middleware
    ├─ auth:sanctum → Verify token
    └─ ensure.admin → Check role
    ↓
If authorized → Process request
If not → Return 401/403 error
```

---

## 🎨 UI States Timeline

### List Page

```
Initial Load
    ↓ [isLoading = true]
Loading...
    ↓ [API response received]
Display posts
    ↓
User interacts (search/filter/edit/delete)
    ↓
Update state
    ↓
Re-render with filtered data
```

### Edit Page

```
Component Mount
    ↓ [isLoading = true]
Loading...
    ↓ [fetchPost + fetchCategories]
Show form (pre-filled)
    ↓
User edits
    ↓ [watch title → updateSlug]
Real-time slug update
    ↓
User clicks Cập Nhật
    ↓ [isSaving = true, button disabled]
Saving...
    ↓ [PATCH response received]
Success! Cập nhật bài viết thành công!
    ↓ [1.5s timeout]
Redirect to list
```

---

## 📋 Validation Flow

### Edit Form Validation

```
User fills form
    ↓
User clicks [✓ Cập Nhật]
    ↓
updatePost() called
    ↓
if !post.title.trim()
    └─ Error: "Vui lòng nhập tiêu đề"
       Stop execution ❌
    ↓
if !post.content.trim()
    └─ Error: "Vui lòng nhập nội dung"
       Stop execution ❌
    ↓
Validation passed ✅
    ↓
PATCH /bai-viet/{id}
    ↓
Backend validation
    ├─ ten_bai_viet: required
    ├─ noi_dung: required
    └─ ... (other field rules)
    ↓
If all valid → Update
If invalid → Return 422 with errors
```

---

## 🚀 Performance Optimization

```
onMounted:
  - Fetch post data (1 API call)
  - Fetch categories (1 API call)
  - Total: 2 concurrent API calls

Watch:
  - Title changes → Auto-generate slug
  - No additional API calls

Update:
  - Validate client-side first
  - Then send 1 PATCH request
  - No unnecessary re-fetches
```

---

## 📊 Data Transformation

### Backend → Frontend (Show)

```
Backend Response:
{
  ten_bai_viet: "Cách chăm sóc chó Poodle",
  noi_dung: "Content...",
  mo_ta: "Summary...",
  phan_loai: {
    ten_phan_loai: "Kiến thức"
  },
  nhan_vien: {
    ho_va_ten: "Nguyễn Văn A"
  }
}
    ↓ (Transform)
Component State:
{
  title: "Cách chăm sóc chó Poodle",
  content: "Content...",
  excerpt: "Summary...",
  categoryName: "Kiến thức",
  nhanVienName: "Nguyễn Văn A"
}
    ↓ (Display)
Template renders fields
```

### Frontend → Backend (Save)

```
Component State:
{
  title: "New Title",
  content: "New Content",
  categoryId: 1,
  status: "published"
}
    ↓ (Transform)
Request Payload:
{
  ten_bai_viet: "New Title",
  noi_dung: "New Content",
  phan_loai_bai_viet_id: 1,
  trang_thai: "published"
}
    ↓ (Send)
PATCH /bai-viet/1
```

---

**Created:** 2024-12-04  
**Version:** 1.0  
**Status:** ✅ Complete
