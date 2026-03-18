# 🚀 Quick Start: Thêm Danh Mục Bài Viết

## 1️⃣ Import Component

```vue
<script setup>
import ThemDanhMuc from "@/components/Admin/TruyenThong/BaiViet/ThemDanhMuc/index.vue";
import { ref } from "vue";

const isThemDanhMucOpen = ref(false);
</script>
```

## 2️⃣ Add to Template

```vue
<template>
  <!-- Button to open modal -->
  <button @click="isThemDanhMucOpen = true">Thêm Danh Mục Mới</button>

  <!-- Modal Container -->
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

## 3️⃣ Handle Event

```javascript
const handleCategoryAdded = (newCategory) => {
  console.log("Category created:", newCategory);
  // Update your list
  categories.value.push(newCategory);
};
```

## 📋 Component Props & Events

### Props

```javascript
modelValue: Boolean; // Controls dialog visibility
```

### Events

```javascript
"update:modelValue"; // When dialog opens/closes
"category-added"; // When category created successfully (emits new category data)
"close"; // When dialog closes
```

## 🎯 Form Structure

### Fields

1. **ten_phan_loai** (Category Name) - Required

   - Min: 3 characters
   - Max: 255 characters
   - Unique in database

2. **mo_ta** (Description) - Optional
   - Max: 1000 characters

### Backend Generates

- `slug`: Auto-generated from `ten_phan_loai`
- `created_at`, `updated_at`: Timestamps

## 🔗 API Endpoint

```
POST /api/phan-loai-bai-viet
```

**Request:**

```json
{
  "ten_phan_loai": "string",
  "mo_ta": "string or null"
}
```

**Response (Success - 201):**

```json
{
  "status": true,
  "message": "Tạo phân loại bài viết thành công",
  "data": {
    "id": 1,
    "ten_phan_loai": "...",
    "slug": "...",
    "mo_ta": "...",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

## ⚡ Features

✅ Frontend validation (instant feedback)
✅ Backend validation (server-side security)
✅ Loading state (disable form while submitting)
✅ Error handling (field & general errors)
✅ Success feedback (green message)
✅ Auto-close (closes after success)
✅ Form reset (clears on close)
✅ Event system (easy parent integration)

## 🎨 UI States

### Empty State

- Button disabled until name entered
- Placeholder text for guidance

### Loading State

- Button shows "Đang thêm..."
- Form inputs disabled
- Close button disabled

### Success State

- Green success message
- Dialog auto-closes after 1 second
- Event emitted with new data

### Error State

- Red error message
- Field-level errors highlighted
- Form stays open for correction
- User can retry

## 🧪 Quick Test

1. Click "Thêm danh mục"
2. Try submit empty → See "Tên danh mục không được để trống"
3. Enter "AB" → See "Tên danh mục phải có ít nhất 3 ký tự"
4. Enter "Kiến thức Thú y"
5. Click "Thêm danh mục"
6. See green success message
7. Dialog closes automatically
8. Check console: `✅ Category created: {...}`

## 📝 In Browser Console

### Success Log

```
✅ Category created: {id: 1, ten_phan_loai: "...", slug: "...", ...}
```

### Check Network

- Open DevTools → Network tab
- Submit form
- Find POST request to `/phan-loai-bai-viet`
- Check request body & response

## 🔐 Security Notes

- ✅ Backend protected with `EnsureAdmin::class` middleware
- ✅ Only authenticated admins can create categories
- ✅ All inputs validated on server
- ✅ CSRF protection enabled
- ✅ SQL injection prevention (Laravel ORM)

## 🐛 Troubleshooting

| Issue              | Solution                                        |
| ------------------ | ----------------------------------------------- |
| Modal doesn't open | Check `v-if="isThemDanhMucOpen"` is in template |
| API error 403      | Login as admin user                             |
| API error 422      | Check validation - name must be 3-255 chars     |
| Event not fired    | Check `@category-added="handler"` spelling      |
| Dialog won't close | Try `@close="isThemDanhMucOpen = false"`        |
| Form doesn't reset | Check `closeDialog()` is called properly        |

## 📁 Related Files

**Component:**

- `src/components/Admin/TruyenThong/BaiViet/ThemDanhMuc/index.vue`

**Backend:**

- `app/Http/Controllers/PhanLoaiBaiVietController.php`
- `routes/api.php` (line with POST /phan-loai-bai-viet)

**Documentation:**

- `THEM_DANH_MUC_BAI_VIET_GUIDE.md` (Full guide)
- `THEM_DANH_MUC_CHECKLIST.md` (Testing checklist)
- `EXAMPLE_THEM_DANH_MUC_USAGE.vue` (Usage example)

## ✨ Example Parent Component

```vue
<template>
  <div>
    <button @click="isThemDanhMucOpen = true">+ Thêm Danh Mục</button>

    <div
      v-if="isThemDanhMucOpen"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    >
      <ThemDanhMuc
        v-model="isThemDanhMucOpen"
        @category-added="(category) => categories.push(category)"
      />
    </div>

    <div>
      <div v-for="cat in categories" :key="cat.id">
        <h3>{{ cat.ten_phan_loai }}</h3>
        <p>{{ cat.mo_ta }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import ThemDanhMuc from "@/components/Admin/TruyenThong/BaiViet/ThemDanhMuc/index.vue";

const isThemDanhMucOpen = ref(false);
const categories = ref([]);
</script>
```

---

✅ **Ready to use!** Just copy the import and template code into your parent component.
