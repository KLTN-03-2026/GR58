<!-- 
  Example: Cách sử dụng ThemDanhMuc component
  File này là reference - copy logic vào parent component của bạn
-->

<template>
  <div class="w-full min-h-screen p-6">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="font-semibold text-2xl leading-9 text-neutral-950">
        Quản Lý Danh Mục Bài Viết
      </h1>
      <p class="text-base leading-6 text-[#4a5565] tracking-tight">
        Tạo và quản lý danh mục bài viết của hệ thống
      </p>
    </div>

    <!-- Thêm Danh Mục Button -->
    <div class="mb-6">
      <button
        @click="isThemDanhMucOpen = true"
        class="bg-[#009689] rounded-lg h-9 px-4 flex items-center gap-2 hover:bg-[#008177] transition-colors"
      >
        <img src="..." alt="Plus" class="w-4 h-4" />
        <span class="text-sm font-medium text-white">Thêm Danh Mục Mới</span>
      </button>
    </div>

    <!-- Danh Sách Danh Mục -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
      <div v-if="categories.length === 0" class="text-center py-8">
        <p class="text-gray-500">
          Chưa có danh mục nào. Hãy tạo danh mục đầu tiên!
        </p>
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="category in categories"
          :key="category.id"
          class="border border-gray-200 rounded-lg p-4 flex items-center justify-between hover:bg-gray-50"
        >
          <div class="flex-1">
            <h3 class="font-medium text-neutral-950">
              {{ category.ten_phan_loai }}
            </h3>
            <p v-if="category.mo_ta" class="text-sm text-gray-600 mt-1">
              {{ category.mo_ta }}
            </p>
            <p class="text-xs text-gray-400 mt-2">
              Slug:
              <code class="bg-gray-100 px-2 py-1 rounded">{{
                category.slug
              }}</code>
            </p>
          </div>
          <div class="flex items-center gap-2">
            <button
              @click="editCategory(category)"
              class="px-3 py-1 text-sm border border-blue-200 text-blue-600 rounded hover:bg-blue-50"
            >
              Sửa
            </button>
            <button
              @click="deleteCategory(category.id)"
              class="px-3 py-1 text-sm border border-red-200 text-red-600 rounded hover:bg-red-50"
            >
              Xóa
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal: Thêm/Sửa Danh Mục -->
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
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import ThemDanhMuc from "@/components/Admin/TruyenThong/BaiViet/ThemDanhMuc/index.vue";
import client from "@/utils/api.js";

// State
const categories = ref([]);
const isThemDanhMucOpen = ref(false);
const isLoading = ref(false);

// Fetch categories from API
const fetchCategories = async () => {
  isLoading.value = true;
  try {
    const response = await client.get("/phan-loai-bai-viet");
    if (response.data.status) {
      categories.value = response.data.data;
      console.log("✅ Categories loaded:", categories.value);
    }
  } catch (error) {
    console.error("Error fetching categories:", error);
  } finally {
    isLoading.value = false;
  }
};

// Handle new category added
const handleCategoryAdded = (newCategory) => {
  console.log("📌 New category added:", newCategory);

  // Option 1: Add to list immediately
  categories.value.push(newCategory);

  // Option 2: Or fetch again from server (uncomment if needed)
  // fetchCategories()
};

// Edit category (placeholder)
const editCategory = (category) => {
  console.log("Edit category:", category);
  // TODO: Implement edit functionality
};

// Delete category (placeholder)
const deleteCategory = (categoryId) => {
  if (confirm("Bạn có chắc muốn xóa danh mục này?")) {
    console.log("Delete category:", categoryId);
    // TODO: Implement delete functionality
  }
};

// Load categories on mount
onMounted(() => {
  fetchCategories();
});
</script>

<style scoped>
/* Styles here if needed */
</style>
