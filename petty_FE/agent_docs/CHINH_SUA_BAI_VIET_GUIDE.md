# Hướng Dẫn Chức Năng Chỉnh Sửa Bài Viết

## 📋 Tính Năng Đã Hoàn Thành

### 1. Fetch Dữ Liệu Bài Viết

- ✅ Lấy dữ liệu bài viết từ API `/bai-viet/{id}`
- ✅ Hiển thị tất cả thông tin: tiêu đề, nội dung, tóm tắt, ảnh đại diện
- ✅ Hiển thị thông tin tác giả (nhan_vien)
- ✅ Hiển thị danh mục (phan_loai)
- ✅ Hiển thị trạng thái (published/draft/hidden)
- ✅ Loading state trong khi tải dữ liệu

### 2. Fetch Danh Mục

- ✅ Lấy danh sách danh mục từ API `/phan-loai-bai-viet`
- ✅ Hiển thị dropdown để chọn danh mục
- ✅ Hiển thị danh mục hiện tại của bài viết

### 3. Chỉnh Sửa Thông Tin

- ✅ Sửa tiêu đề (ten_bai_viet)
- ✅ Sửa nội dung (noi_dung)
- ✅ Sửa tóm tắt (mo_ta)
- ✅ Sửa slug (đường dẫn tĩnh)
- ✅ Sửa danh mục (phan_loai_bai_viet_id)
- ✅ Sửa ảnh đại diện (anh_bai_viet)
- ✅ Sửa trạng thái (trang_thai)
- ✅ Auto-generate slug từ tiêu đề (tiếng Việt support)

### 4. Cập Nhật Bài Viết

- ✅ Gửi PATCH request tới `/bai-viet/{id}`
- ✅ Validation form (tiêu đề, nội dung bắt buộc)
- ✅ Hiển thị lỗi khi cập nhật thất bại
- ✅ Hiển thị thành công sau khi cập nhật
- ✅ Redirect về danh sách bài viết sau 1.5 giây

### 5. UI/UX Features

- ✅ Loading state khi fetch dữ liệu
- ✅ Error state khi có lỗi
- ✅ Success state khi cập nhật thành công
- ✅ Disable button "Cập nhật" khi đang lưu
- ✅ Hiển thị trạng thái và tác giả bài viết
- ✅ Nút xóa ảnh đại diện

## 🔌 API Endpoints

### Lấy Thông Tin Bài Viết

```
GET /api/bai-viet/{id}
```

**Response:**

```json
{
  "status": true,
  "message": "Lấy chi tiết bài viết thành công",
  "data": {
    "id": 1,
    "ten_bai_viet": "Tiêu đề",
    "slug": "slug",
    "noi_dung": "Nội dung",
    "mo_ta": "Tóm tắt",
    "anh_bai_viet": "URL",
    "trang_thai": "published",
    "nhan_vien_id": 1,
    "phan_loai_bai_viet_id": 1,
    "nhan_vien": { "id": 1, "ho_va_ten": "Tên" },
    "phan_loai": { "id": 1, "ten_phan_loai": "Danh mục" },
    "created_at": "2024-12-04",
    "updated_at": "2024-12-04"
  }
}
```

### Cập Nhật Bài Viết

```
PATCH /api/bai-viet/{id}
```

**Request Body:**

```json
{
  "ten_bai_viet": "Tiêu đề mới",
  "slug": "slug-moi",
  "noi_dung": "Nội dung mới",
  "mo_ta": "Tóm tắt mới",
  "trang_thai": "published",
  "phan_loai_bai_viet_id": 1,
  "anh_bai_viet": "URL ảnh"
}
```

### Lấy Danh Sách Danh Mục

```
GET /api/phan-loai-bai-viet
```

**Response:**

```json
{
  "status": true,
  "message": "...",
  "data": [
    {
      "id": 1,
      "ten_phan_loai": "Kiến thức Thú y",
      "slug": "...",
      "mo_ta": "..."
    },
    { "id": 2, "ten_phan_loai": "Tin tức", "slug": "...", "mo_ta": "..." }
  ]
}
```

## 📁 File Structure

```
src/components/Admin/TruyenThong/BaiViet/
├── index.vue                    # Danh sách bài viết
├── ThemBaiMoi/
│   └── index.vue               # Tạo bài viết mới
├── ChinhSuaBaiViet/
│   └── index.vue               # Chỉnh sửa bài viết ✅ (VỪA FIX)
├── XoaBaiViet/
│   └── index.vue               # Modal xóa bài viết
└── ThemDanhMuc/
    └── index.vue               # Modal thêm danh mục
```

## 🔄 Component Flow

```
BaiViet List (index.vue)
    ↓
[Click Edit Button] → /admin/bai-viet/chinh-sua/{id}
    ↓
ChinhSuaBaiViet (ChinhSuaBaiViet/index.vue)
    ↓
[onMounted] → fetchPost() + fetchCategories()
    ↓
[Form populated with data]
    ↓
[Edit & Save] → updatePost() → PATCH /bai-viet/{id}
    ↓
[Success] → Redirect to /admin/bai-viet
```

## 📝 Form Validation

- **Tiêu đề**: Bắt buộc, không được để trống
- **Nội dung**: Bắt buộc, không được để trống
- **Slug**: Tự động generate từ tiêu đề
- **Danh mục**: Optional (có thể để không chọn)
- **Ảnh đại diện**: Optional

## 🎨 Styling

- **Input fields**: Tailwind CSS with focus states
- **Editor toolbar**: Giữ nguyên (chưa implement functionality)
- **Buttons**: Green (#00a63e) for primary actions
- **Error messages**: Red background (#ff4444)
- **Success messages**: Green background (#00a63e)

## 🔒 Security

- ✅ Middleware `EnsureAdmin` bảo vệ endpoint
- ✅ Validation trên backend (Laravel)
- ✅ Validation trên frontend (Vue)
- ✅ Sanitized input (Vietnamese slug)

## 📊 Console Logs

Khi edit bài viết, check console (F12) để xem:

```
📝 Post loaded: {...}     // Dữ liệu từ API
✅ Categories loaded: [...] // Danh sách danh mục
📤 Saving post: {...}     // Payload được gửi
✅ Post updated successfully // Thành công
❌ Error updating post: ... // Lỗi nếu có
```

## 🐛 Troubleshooting

### Lỗi: "Không thể tải bài viết"

- Kiểm tra ID bài viết có tồn tại không
- Kiểm tra token authentication
- Kiểm tra console log lỗi

### Lỗi: "Cập nhật thất bại"

- Kiểm tra validation errors trong response
- Kiểm tra tiêu đề và nội dung không được để trống
- Kiểm tra category ID có tồn tại không

### Slug không tự động update

- Kiểm tra watcher `watch(() => post.value.title)`
- Kiểm tra hàm `convertToSlug()` chạy đúng không
- Mở console để debug

## 🚀 Next Steps (Chưa Implement)

- [ ] Upload ảnh đại diện (giống như ThemBaiMoi)
- [ ] Editor WYSIWYG (formatting toolbar functionality)
- [ ] Preview bài viết
- [ ] Schedule publish (đăng vào lúc nhất định)
- [ ] Categories multiple select (thay vì single)
- [ ] Tags support
- [ ] Version history / Undo
- [ ] Auto-save drafts

## 📞 Contact & Support

Nếu có lỗi:

1. Mở DevTools (F12)
2. Kiểm tra Console logs
3. Kiểm tra Network tab
4. Báo cáo error message

---

**Created:** 2024-12-04  
**Status:** ✅ Fully Functional  
**Version:** 1.0
