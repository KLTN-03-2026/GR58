# 🚀 QUICK START: Chỉnh Sửa Bài Viết

## 📌 Cách Sử Dụng

### 1. Từ Danh Sách Bài Viết

```
Trang: /admin/bai-viet
↓
Click nút 📝 (Edit) trên hàng bài viết
↓
Tự động navigate tới: /admin/bai-viet/chinh-sua/{id}
```

### 2. Trang Chỉnh Sửa

```
URL: http://localhost:3000/admin/bai-viet/chinh-sua/1
```

### 3. Form Chỉnh Sửa

```
┌─────────────────────────────────────────────────────┐
│ Tiêu đề bài viết                                    │
├─────────────────────────────────────────────────────┤
│ Đường dẫn: petty.vn/ [slug-tu-dong-generate]        │
├─────────────────────────────────────────────────────┤
│                    EDITOR                            │
│  [B] [I] [U] ... [H1] [H2] [H3] ... [Chèn ảnh]    │
│ ┌───────────────────────────────────────────────┐  │
│ │ Nội dung bài viết (Tinos font)                │  │
│ │                                               │  │
│ │                                               │  │
│ └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│ Tóm tắt (150-160 ký tự gợi ý)                      │
│ [textarea 3 lines]                                 │
└─────────────────────────────────────────────────────┘
         SIDEBAR
    ┌──────────────┐
    │ Đăng (Publish) │
    │ [✓ Cập nhật]  │
    │ Trạng thái:   │
    │ Đã xuất bản  │
    │ Tác giả:      │
    │ Tên Nhân Viên│
    ├──────────────┤
    │ Phân loại     │
    │ [Dropdown]    │
    │ Hiện tại:     │
    │ Danh mục      │
    ├──────────────┤
    │ Ảnh đại diện  │
    │ [Image 128px] │
    │ [x Xóa]       │
    │ 1200x630px    │
    └──────────────┘
```

---

## 🎯 Các Bước Chỉnh Sửa

### Bước 1: Chỉnh Sửa Tiêu Đề

```
Input: [Tiêu đề cũ]
Edit: [Tiêu đề mới]
→ Slug tự động update
```

**Ví dụ:**

```
Trước: "Cách chăm sóc chó Poodle"
Slug: "cach-cham-soc-cho-poodle"

Sau: "Hướng dẫn chăm sóc chó Poodle đầy đủ"
Slug: "huong-dan-cham-soc-cho-poodle-day-du"  ← Auto update
```

### Bước 2: Chỉnh Sửa Nội Dung

```
- Click vào textarea EDITOR
- Chỉnh sửa nội dung
- Toolbar có sẵn (chưa hoạt động, có thể code sau)
```

### Bước 3: Chỉnh Sửa Tóm Tắt

```
- Gợi ý: 150-160 ký tự
- Dùng cho social media sharing (Facebook, Zalo)
- Nếu bỏ trống sẽ tự lấy 160 ký tự đầu của nội dung
```

### Bước 4: Chọn Danh Mục

```
Sidebar → Phân loại → [Dropdown]
- Chọn từ danh sách có sẵn
- Hoặc bỏ trống (không bắt buộc)
- Hiển thị danh mục hiện tại
```

### Bước 5: Quản Lý Ảnh Đại Diện

```
Sidebar → Ảnh đại diện
- Hiển thị ảnh hiện tại (nếu có)
- Nút [x Xóa] để xóa ảnh
- Gợi ý kích thước: 1200x630px (16:9)

Để thêm/thay ảnh mới:
Chưa implement → cần thêm nút "Upload" (tương tự ThemBaiMoi)
```

### Bước 6: Lưu Thay Đổi

```
Sidebar → Nút [✓ Cập nhật]
- Click → PATCH /bai-viet/{id}
- Validation: Tiêu đề & Nội dung bắt buộc
- Success → "Cập nhật bài viết thành công!"
- Redirect → /admin/bai-viet (sau 1.5 giây)
```

---

## ⚠️ Validation Rules

### Bắt Buộc

- ✅ **Tiêu đề** (ten_bai_viet) - Không được để trống
- ✅ **Nội dung** (noi_dung) - Không được để trống

### Optional

- ℹ️ **Tóm tắt** (mo_ta) - Có thể bỏ trống
- ℹ️ **Danh mục** (phan_loai_bai_viet_id) - Có thể bỏ trống
- ℹ️ **Ảnh** (anh_bai_viet) - Có thể bỏ trống

---

## 🔄 Data Flow

```
User Click Edit Button
    ↓
URL: /admin/bai-viet/chinh-sua/{id}
    ↓
Component Mount
    ↓
[onMounted]
    ├─ fetchPost()        → GET /bai-viet/{id}
    │  └─ Load data into form
    └─ fetchCategories()  → GET /phan-loai-bai-viet
       └─ Load dropdown options
    ↓
Form Display
    ├─ Title, Content, Summary filled
    ├─ Category selected (if any)
    ├─ Image displayed (if any)
    ├─ Status & Author shown
    └─ Ready for editing
    ↓
User Edit & Click "Cập nhật"
    ↓
[updatePost]
    ├─ Validate form
    ├─ Prepare payload
    ├─ PATCH /bai-viet/{id}
    ├─ Show success message
    └─ Redirect to list
    ↓
/admin/bai-viet (List page)
```

---

## 🐛 Troubleshooting

### ❌ Lỗi: "Đang tải bài viết..."

**Nguyên nhân:** API không trả về dữ liệu  
**Giải pháp:**

1. Mở DevTools (F12)
2. Kiểm tra Console tab
3. Kiểm tra Network tab → /bai-viet/{id} response
4. Verify ID trong URL có đúng không
5. Verify token authentication

### ❌ Lỗi: "Vui lòng nhập tiêu đề bài viết"

**Nguyên nhân:** Tiêu đề trống  
**Giải pháp:** Nhập tiêu đề trước khi cập nhật

### ❌ Lỗi: "Vui lòng nhập nội dung bài viết"

**Nguyên nhân:** Nội dung trống  
**Giải pháp:** Nhập nội dung trước khi cập nhật

### ❌ Slug không auto-update

**Nguyên nhân:** Watcher không hoạt động  
**Giải pháp:**

1. Mở DevTools Console
2. Kiểm tra khi thay title có log không
3. Verify `convertToSlug()` function
4. Refresh trang và thử lại

### ❌ Dropdown danh mục trống

**Nguyên nhân:** API `/phan-loai-bai-viet` không có dữ liệu  
**Giải pháp:**

1. Tạo danh mục mới (button "Thêm danh mục")
2. Kiểm tra database có record không
3. Kiểm tra API response

### ❌ Ảnh không hiển thị

**Nguyên nhân:** URL ảnh invalid hoặc storage không accessible  
**Giải pháp:**

1. Kiểm tra file có tồn tại không
2. Kiểm tra permission của file
3. Verify symlink `public/storage` hoạt động

---

## 🔧 Console Debug Logs

Khi chỉnh sửa bài viết, F12 → Console sẽ in ra:

```javascript
// Khi load bài viết
📝 Post loaded: {
  id: 1,
  ten_bai_viet: "...",
  slug: "...",
  noi_dung: "...",
  ...
}

// Khi load danh mục
✅ Categories loaded: [
  { id: 1, ten_phan_loai: "Kiến thức" },
  { id: 2, ten_phan_loai: "Tin tức" }
]

// Khi cập nhật
📤 Saving post: {
  ten_bai_viet: "...",
  slug: "...",
  noi_dung: "...",
  ...
}

// Sau khi thành công
✅ Post updated successfully
```

---

## 📊 API Endpoints Used

```
GET /api/bai-viet/{id}
  Fetch post data

PATCH /api/bai-viet/{id}
  Update post

GET /api/phan-loai-bai-viet
  Fetch categories
```

---

## 🎨 UI States

### Loading

```
[Loading icon / Text]
"Đang tải bài viết..."
```

### Error

```
[Red background]
❌ Error message
```

### Success

```
[Green background]
✅ Cập nhật bài viết thành công!
[Auto redirect after 1.5s]
```

### Normal

```
Form with all fields editable
Button: [✓ Cập nhật] (enabled)
```

### Saving

```
Button: [Đang lưu...] (disabled)
All fields: read-only
```

---

## ✨ Features

- ✅ Auto-generate slug từ title
- ✅ Vietnamese character support
- ✅ Real-time validation
- ✅ Loading states
- ✅ Error messages
- ✅ Success notification
- ✅ Auto-redirect
- ✅ Category dropdown
- ✅ Show author info
- ✅ Show status info

---

## ⏳ Future Features (TODO)

- [ ] Upload ảnh đại diện (button "Upload")
- [ ] Editor toolbar functionality
- [ ] Preview bài viết
- [ ] Schedule publish
- [ ] Multiple categories
- [ ] Tags support
- [ ] Revisions/History

---

## 📞 Support

Nếu có vấn đề:

1. Mở DevTools (F12)
2. Check Console tab
3. Check Network tab (API calls)
4. Báo cáo error message

---

**Version:** 1.0  
**Status:** ✅ Production Ready  
**Last Updated:** 2024-12-04
