# Chức Năng Thêm Danh Mục Bài Viết

## 📋 Giới Thiệu

Component `ThemDanhMuc` cho phép admin tạo danh mục mới cho bài viết với các tính năng:

- ✅ Form hai trường: Tên danh mục (bắt buộc) và Mô tả (tùy chọn)
- ✅ Validation dữ liệu frontend
- ✅ Gửi request POST đến backend API
- ✅ Xử lý lỗi từ backend
- ✅ Hiển thị loading state
- ✅ Thông báo success/error

## 🏗️ Cấu Trúc Component

### Props

```javascript
modelValue: {
  type: Boolean,
  default: false
}
```

- Để điều khiển hiển thị/ẩn modal dialog

### Emits

```javascript
["update:modelValue", "category-added", "close"];
```

| Event               | Mô tả                                                     |
| ------------------- | --------------------------------------------------------- |
| `update:modelValue` | Khi đóng/mở dialog                                        |
| `category-added`    | Khi tạo danh mục thành công, gửi kèm dữ liệu danh mục mới |
| `close`             | Khi đóng dialog                                           |

## 📝 Form Fields

### 1. Tên Danh Mục (ten_phan_loai) - Bắt buộc

- **Validation:**
  - Không được để trống
  - Minimum 3 ký tự
  - Maximum 255 ký tự
- **Backend validation:** Server sẽ kiểm tra unique

### 2. Mô Tả (mo_ta) - Tùy chọn

- **Validation:**
  - Maximum 1000 ký tự
  - Có thể để trống

## 🔌 API Integration

### Endpoint

```
POST /api/phan-loai-bai-viet
```

### Request Body

```json
{
  "ten_phan_loai": "Kiến thức Thú y",
  "mo_ta": "Các bài viết về kiến thức thú y cơ bản"
}
```

### Success Response

```json
{
  "status": true,
  "message": "Tạo phân loại bài viết thành công",
  "data": {
    "id": 1,
    "ten_phan_loai": "Kiến thức Thú y",
    "slug": "kien-thuc-thu-y",
    "mo_ta": "Các bài viết về kiến thức thú y cơ bản",
    "created_at": "2025-12-04T...",
    "updated_at": "2025-12-04T..."
  }
}
```

### Error Response

```json
{
  "status": false,
  "message": "Lỗi khi tạo phân loại bài viết",
  "error": "Tên danh mục đã tồn tại"
}
```

## 💻 Cách Sử Dụng

### 1. Import Component

```vue
<script setup>
import ThemDanhMuc from "@/components/Admin/TruyenThong/BaiViet/ThemDanhMuc/index.vue";

const isThemDanhMucOpen = ref(false);
</script>
```

### 2. Sử Dụng trong Template

```vue
<template>
  <!-- Nút mở dialog -->
  <button @click="isThemDanhMucOpen = true">Thêm Danh Mục</button>

  <!-- Dialog -->
  <div
    v-if="isThemDanhMucOpen"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
  >
    <ThemDanhMuc
      v-model="isThemDanhMucOpen"
      @category-added="handleCategoryAdded"
      @close="isThemDanhMucOpen = false"
    />
  </div>
</template>
```

### 3. Handle Event

```javascript
const handleCategoryAdded = (newCategory) => {
  console.log("Danh mục mới được tạo:", newCategory);

  // Cập nhật danh sách danh mục
  categories.value.push(newCategory);

  // Hoặc fetch lại danh sách từ server
  fetchCategories();
};
```

## 🎨 UI Features

### Loading State

- Button text thay đổi thành "Đang thêm..."
- Form inputs bị disabled
- Close button bị disabled
- Loading icon (⏳) hiển thị

### Success State

- Green success message hiển thị
- Dialog auto-close sau 1 giây
- `category-added` event được emit

### Error State

- Red error message hiển thị
- Field errors được hiển thị dưới input
- User có thể sửa lại và submit lại

## 🔍 Validation Logic

### Frontend Validation (tức thì)

```javascript
const validateForm = () => {
  // 1. Check empty
  if (!form.ten_phan_loai.trim()) {
    errors.ten_phan_loai = "Tên danh mục không được để trống";
  }

  // 2. Check min length
  if (form.ten_phan_loai.length < 3) {
    errors.ten_phan_loai = "Tên danh mục phải có ít nhất 3 ký tự";
  }

  // 3. Check max length
  if (form.ten_phan_loai.length > 255) {
    errors.ten_phan_loai = "Tên danh mục không được vượt quá 255 ký tự";
  }

  // 4. Check mo_ta length
  if (form.mo_ta && form.mo_ta.length > 1000) {
    errors.mo_ta = "Mô tả không được vượt quá 1000 ký tự";
  }
};
```

### Backend Validation

- Database unique constraint trên `ten_phan_loai`
- Laravel Request validation qua `StorePhanLoaiBaiVietRequest`

## 🌐 State Management

### Reactive State

```javascript
const form = ref({
  ten_phan_loai: "",
  mo_ta: "",
});
const isLoading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const errors = ref({});
```

### State Flow

1. User nhập dữ liệu → update `form`
2. User click "Thêm danh mục" → `validateForm()`
3. Nếu valid → `isLoading = true`
4. Gửi POST request → `client.post('/phan-loai-bai-viet', ...)`
5. Nhận response → cập nhật success/error message
6. Emit `category-added` event
7. Auto-close dialog hoặc giữ lại nếu error

## 📊 Console Logs

Khi tạo danh mục thành công:

```
✅ Category created: {id: 1, ten_phan_loai: "...", ...}
```

Khi có lỗi:

```
Error adding category: {...}
```

## 🐛 Troubleshooting

### Lỗi: "Tên danh mục đã tồn tại"

- Backend trả về unique constraint violation
- User cần nhập tên danh mục khác

### Lỗi: "Vui lòng kiểm tra lại các thông tin nhập"

- Có lỗi validation từ backend
- Kiểm tra console log để xem chi tiết

### Dialog không đóng

- Nếu không emit `update:modelValue`, thử emit manual `@close`
- Hoặc set `isThemDanhMucOpen = false` manually

## 📝 Backend Routes

```php
// GET - Lấy danh sách danh mục
Route::get('/phan-loai-bai-viet', [PhanLoaiBaiVietController::class, 'index']);

// GET - Lấy chi tiết danh mục
Route::get('/phan-loai-bai-viet/{phanLoaiBaiViet}', [PhanLoaiBaiVietController::class, 'show']);

// POST - Tạo danh mục (cần admin permission)
Route::post('/phan-loai-bai-viet', [PhanLoaiBaiVietController::class, 'store'])
  ->middleware(\App\Http\Middleware\EnsureAdmin::class);
```

## 🔐 Security

- POST endpoint được bảo vệ bằng `EnsureAdmin::class` middleware
- Chỉ admin mới có thể tạo danh mục mới
- Backend validate lại tất cả dữ liệu

## 📱 Responsive

Component được thiết kế responsive:

- `max-w-[510px]` - Chiều rộng max
- `w-full` - Chiều rộng đầy đủ trên màn hình nhỏ
- Tailwind classes để padding/margin responsive

## 🎯 Tối Ưu Hóa

1. **Input Validation:** Frontend validate trước khi gửi
2. **Error Handling:** Hiển thị lỗi specific cho từng field
3. **Loading State:** Disable form khi đang submit
4. **Auto-close:** Dialog tự đóng sau success
5. **Error Recovery:** User có thể sửa lại và submit lại
