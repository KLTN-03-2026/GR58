<!-- 
  📌 COMPLETE WORKING EXAMPLE
  Copy this entire component into your project and use it as a parent component.
  This shows how to integrate the ThemDanhMuc modal with a category list.
-->

<template>
  <div class="w-full min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-neutral-950 mb-2">
        Quản Lý Danh Mục Bài Viết
      </h1>
      <p class="text-gray-600">Tạo, sửa và quản lý danh mục bài viết</p>
    </div>

    <!-- Action Bar -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
      <button
        @click="openThemDanhMuc"
        class="bg-[#009689] text-white px-4 py-2 rounded-lg hover:bg-[#008177] transition-colors flex items-center gap-2"
      >
        <span>➕</span>
        Thêm Danh Mục Mới
      </button>
    </div>

    <!-- Status Messages -->
    <div
      v-if="statusMessage"
      class="mb-6 p-4 rounded-lg"
      :class="
        statusMessage.type === 'success'
          ? 'bg-green-50 text-green-700'
          : 'bg-red-50 text-red-700'
      "
    >
      {{ statusMessage.text }}
    </div>

    <!-- Categories List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <!-- Loading State -->
      <div v-if="isLoadingCategories" class="p-8 text-center">
        <div class="inline-block animate-spin">⏳</div>
        <p class="mt-2 text-gray-600">Đang tải danh sách...</p>
      </div>

      <!-- Empty State -->
      <div
        v-else-if="categories.length === 0"
        class="p-8 text-center text-gray-500"
      >
        <p class="text-lg">Chưa có danh mục nào</p>
        <p class="text-sm mt-2">
          Hãy tạo danh mục đầu tiên bằng cách click nút "Thêm Danh Mục Mới"
        </p>
      </div>

      <!-- Categories Table -->
      <table v-else class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
              ID
            </th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
              Tên Danh Mục
            </th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
              Slug
            </th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
              Mô Tả
            </th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
              Ngày Tạo
            </th>
            <th
              class="px-6 py-3 text-right text-sm font-semibold text-gray-900"
            >
              Thao Tác
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr
            v-for="category in categories"
            :key="category.id"
            class="hover:bg-gray-50 transition-colors"
          >
            <td class="px-6 py-4 text-sm text-gray-900">{{ category.id }}</td>
            <td class="px-6 py-4 text-sm font-medium text-gray-900">
              {{ category.ten_phan_loai }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">
              <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{
                category.slug
              }}</code>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">
              {{
                category.mo_ta
                  ? category.mo_ta.length > 50
                    ? category.mo_ta.substring(0, 50) + "..."
                    : category.mo_ta
                  : "-"
              }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">
              {{ formatDate(category.created_at) }}
            </td>
            <td class="px-6 py-4 text-right">
              <button
                @click="editCategory(category)"
                class="text-blue-600 hover:text-blue-900 mr-3 text-sm"
              >
                Sửa
              </button>
              <button
                @click="deleteCategory(category.id)"
                class="text-red-600 hover:text-red-900 text-sm"
              >
                Xóa
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal: Thêm Danh Mục -->
    <div
      v-if="isThemDanhMucOpen"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
    >
      <ThemDanhMuc
        v-model="isThemDanhMucOpen"
        @category-added="handleCategoryAdded"
        @close="handleThemDanhMucClose"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import ThemDanhMuc from "@/components/Admin/TruyenThong/BaiViet/ThemDanhMuc/index.vue";
import client from "@/utils/api.js";

// State
const categories = ref([]);
const isThemDanhMucOpen = ref(false);
const isLoadingCategories = ref(false);
const statusMessage = ref(null);

// Format date helper
const formatDate = (dateString) => {
  if (!dateString) return "-";
  const date = new Date(dateString);
  return date.toLocaleDateString("vi-VN", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  });
};

// Show status message
const showStatus = (message, type = "success") => {
  statusMessage.value = { text: message, type };
  setTimeout(() => {
    statusMessage.value = null;
  }, 3000);
};

// Fetch categories from API
const fetchCategories = async () => {
  isLoadingCategories.value = true;
  try {
    const response = await client.get("/phan-loai-bai-viet");
    if (response.data.status) {
      categories.value = response.data.data;
      console.log("✅ Categories loaded:", categories.value);
    } else {
      console.error("Error:", response.data.message);
    }
  } catch (error) {
    console.error("Error fetching categories:", error);
    showStatus("Lỗi khi tải danh mục", "error");
  } finally {
    isLoadingCategories.value = false;
  }
};

// Open "Thêm danh mục" modal
const openThemDanhMuc = () => {
  isThemDanhMucOpen.value = true;
};

// Handle category added event
const handleCategoryAdded = (newCategory) => {
  console.log("📌 New category:", newCategory);

  // Add to local list
  categories.value.unshift(newCategory);

  // Show success message
  showStatus(
    `✅ Danh mục "${newCategory.ten_phan_loai}" được tạo thành công`,
    "success"
  );
};

// Handle modal close
const handleThemDanhMucClose = () => {
  isThemDanhMucOpen.value = false;
};

// Edit category (placeholder)
const editCategory = (category) => {
  console.log("Edit category:", category);
  // TODO: Implement edit modal
  showStatus("Chức năng sửa danh mục sẽ được cập nhật", "info");
};

// Delete category
const deleteCategory = (categoryId) => {
  if (!confirm("Bạn có chắc muốn xóa danh mục này?")) return;

  console.log("Delete category:", categoryId);
  // TODO: Implement delete
  showStatus("Chức năng xóa danh mục sẽ được cập nhật", "info");
};

// Load categories on mount
onMounted(() => {
  fetchCategories();
});
</script>

<style scoped>
/* Add any component-specific styles here */
</style>

<!-- 
  ============================================
  HOW TO USE THIS COMPONENT:
  ============================================
  
  1. Copy this file to your project
  2. Make sure ThemDanhMuc component is imported correctly
  3. Ensure @/utils/api.js exists and is configured
  
  FEATURES:
  ✅ Display all categories in a table
  ✅ Click "Thêm Danh Mục Mới" to open modal
  ✅ Fill form and submit
  ✅ New category appears in table
  ✅ Status messages for feedback
  ✅ Loading states
  ✅ Error handling
  
  NEXT STEPS:
  - Implement edit category functionality
  - Implement delete category functionality
  - Add pagination if categories grow
  - Add search/filter
  - Add sorting
  
  ============================================
-->
