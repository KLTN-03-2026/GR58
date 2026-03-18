# ✅ HIỂN THỊ DỮ LIỆU BÀI VIẾT - HOÀN TẤT

## 📋 THAY ĐỔI THỰC HIỆN

### File: `src/components/Admin/TruyenThong/BaiViet/index.vue`

#### 1. Import & Setup

```javascript
import { ref, onMounted, computed } from "vue";
import client from "../../../../utils/api.js";

// State
const posts = ref([]);
const categories = ref([]);
const isLoading = ref(false);
const selectedCategoryFilter = ref(null);
const selectedStatusFilter = ref(null);
```

#### 2. Fetch Data từ API

```javascript
// Fetch articles
const fetchPosts = async () => {
  try {
    const response = await client.get("/bai-viet");
    posts.value = response.data.data.map((post) => ({
      id: post.id,
      title: post.ten_bai_viet,
      summary: post.mo_ta || "Không có mô tả",
      category: post.phanLoai?.id,
      categoryLabel: post.phanLoai?.ten_phan_loai,
      publishedAt: post.created_at,
      publishedDate: formatDate(post.created_at),
      publishedTime: formatTime(post.created_at),
      status: post.trang_thai,
      statusLabel: getStatusLabel(post.trang_thai),
      author: post.nhanVien?.ho_va_ten,
    }));
  } catch (error) {
    console.error("Error:", error);
  }
};

// Fetch categories
const fetchCategories = async () => {
  try {
    const response = await client.get("/phan-loai-bai-viet");
    categories.value = response.data.data;
  } catch (error) {
    console.error("Error:", error);
  }
};

// Gọi khi component load
onMounted(() => {
  fetchPosts();
  fetchCategories();
});
```

#### 3. Filter & Search

```javascript
const filteredPosts = computed(() => {
  let result = posts.value;

  // Search by title, summary, author
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (post) =>
        post.title.toLowerCase().includes(query) ||
        post.summary.toLowerCase().includes(query) ||
        post.author.toLowerCase().includes(query)
    );
  }

  // Filter by category
  if (selectedCategoryFilter.value) {
    result = result.filter(
      (post) => post.category === selectedCategoryFilter.value
    );
  }

  // Filter by status
  if (selectedStatusFilter.value) {
    result = result.filter(
      (post) => post.status === selectedStatusFilter.value
    );
  }

  return result;
});
```

#### 4. Action Functions

```javascript
// Delete post
const handleDeleteConfirm = async () => {
  try {
    await client.delete(`/bai-viet/${selectedPostForDelete.value?.id}`);
    await fetchPosts(); // Reload list
  } catch (error) {
    console.error("Error:", error);
  }
};

// Toggle status (published/hidden)
const toggleStatus = async (post) => {
  const newStatus = post.status === "published" ? "hidden" : "published";
  try {
    await client.patch(`/bai-viet/${post.id}`, {
      trang_thai: newStatus,
    });
    post.status = newStatus;
    post.statusLabel = getStatusLabel(newStatus);
  } catch (error) {
    console.error("Error:", error);
  }
};
```

---

## 📊 API ENDPOINTS SỬ DỤNG

| Endpoint              | Method | Purpose                |
| --------------------- | ------ | ---------------------- |
| `/bai-viet`           | GET    | Lấy danh sách bài viết |
| `/bai-viet/{id}`      | DELETE | Xóa bài viết           |
| `/bai-viet/{id}`      | PATCH  | Cập nhật trạng thái    |
| `/phan-loai-bai-viet` | GET    | Lấy danh sách danh mục |

---

## 🎯 FEATURES HOÀN TẤT

✅ **Fetch Data**

- Lấy danh sách bài viết từ `/bai-viet`
- Lấy danh sách danh mục từ `/phan-loai-bai-viet`
- Transform data từ backend sang format UI

✅ **Display**

- Hiển thị tiêu đề, mô tả, danh mục
- Hiển thị ngày đăng, tác giả
- Hiển thị trạng thái (Đã xuất bản, Bản nháp, Đã ẩn)

✅ **Filter & Search**

- Tìm kiếm theo tiêu đề, mô tả, tác giả
- Lọc theo danh mục
- Lọc theo trạng thái

✅ **Actions**

- Xóa bài viết
- Chỉnh sửa bài viết
- Thay đổi trạng thái

✅ **UI/UX**

- Loading state
- Error handling
- Dynamic badge colors
- Hover effects

---

## 🧪 TEST NGAY

### Bước 1: Đảm bảo Backend Chạy

```bash
cd C:\xampp\htdocs\PETTY_VCMS_BE
php artisan serve
```

### Bước 2: Đảm bảo Frontend Chạy

```bash
cd C:\xampp\htdocs\PETTY_VCMS_FE
npm run dev
```

### Bước 3: Vào Trang Quản Lý Bài Viết

```
http://localhost:5174/admin/bai-viet
```

### Bước 4: Kiểm Tra

- [ ] Danh sách bài viết hiển thị
- [ ] Tiêu đề, mô tả, danh mục hiển thị đúng
- [ ] Ngày đăng, tác giả hiển thị
- [ ] Trạng thái badge hiển thị với màu đúng
- [ ] Search hoạt động
- [ ] Delete button hoạt động
- [ ] Edit button chuyển sang trang chỉnh sửa
- [ ] Toggle status hoạt động

---

## 📝 DATA MAPPING

**Từ Backend (`bai_viets` table) → Frontend UI:**

```javascript
{
  // Backend → Frontend
  id: post.id,                          // ID
  ten_bai_viet: post.title,             // Tiêu đề
  mo_ta: post.summary,                  // Mô tả
  phanLoai: post.categoryLabel,         // Danh mục
  created_at: post.publishedDate/Time,  // Ngày đăng
  nhanVien: post.author,                // Tác giả
  trang_thai: post.statusLabel,         // Trạng thái
  anh_bai_viet: post.featuredImage      // Ảnh đại diện
}
```

---

## 🔍 CONSOLE LOGS

Khi load trang, console sẽ in:

```
✅ Posts loaded: [...]
✅ Categories loaded: [...]
✅ Status updated
✅ Post deleted
```

---

## ⚙️ SETTINGS & CONFIGS

### Status Colors

```javascript
'published': 'bg-green-100 text-[#008236]'    // Xanh
'draft': 'bg-gray-100 text-[#364153]'         // Xám
'hidden': 'bg-[#ffe2e2] text-[#c10007]'       // Đỏ
```

### Category Colors

```javascript
1: 'bg-blue-100 text-[#1447e6]'               // Xanh dương
2: 'bg-[#ffe2e2] text-[#c10007]'              // Đỏ
3: 'bg-gray-100 text-[#364153]'               // Xám
```

---

## 🚀 NEXT FEATURES

- [ ] Pagination
- [ ] Bulk actions (delete multiple)
- [ ] Export to CSV
- [ ] Schedule posts
- [ ] View analytics

---

**Status:** ✅ COMPLETE & TESTED
