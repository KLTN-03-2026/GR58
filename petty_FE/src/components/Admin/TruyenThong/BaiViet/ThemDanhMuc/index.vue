<template>
  <div
    class="bg-white border !border-gray-300 rounded-[10px] shadow-lg w-full max-w-[510px] relative"
    role="dialog"
    aria-labelledby="dialog-title"
    aria-describedby="dialog-description"
  >
    <!-- Header -->
    <div class="flex flex-col gap-2 px-6 pt-6">
      <h2
        id="dialog-title"
        class="text-lg font-semibold text-neutral-950 tracking-tight"
      >
        Thêm danh mục mới
      </h2>
      <p id="dialog-description" class="text-sm text-[#717182] leading-5">
        Tạo danh mục mới cho bài viết
      </p>
    </div>

    <!-- Content -->
    <div class="flex flex-col gap-5 px-6 mt-8">
      <!-- Category Name -->
      <div class="flex flex-col gap-2">
        <label for="category-name" class="text-sm font-medium text-neutral-950">
          Tên danh mục <span class="text-red-500">*</span>
        </label>
        <input
          id="category-name"
          v-model="form.ten_phan_loai"
          type="text"
          placeholder="Ví dụ: Kiến thức Thú y, Tin tức..."
          class="w-full h-9 px-3 py-1 bg-[#f3f3f5] border-0 rounded-lg text-sm text-neutral-950 placeholder:text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689]"
          :disabled="isLoading"
        />
        <p v-if="errors.ten_phan_loai" class="text-xs text-red-500">
          {{ errors.ten_phan_loai }}
        </p>
      </div>

      <!-- Description -->
      <div class="flex flex-col gap-2">
        <label
          for="category-description"
          class="text-sm font-medium text-neutral-950"
        >
          Mô tả
        </label>
        <textarea
          id="category-description"
          v-model="form.mo_ta"
          placeholder="Nhập mô tả danh mục (tùy chọn)..."
          rows="3"
          class="w-full px-3 py-2 bg-[#f3f3f5] border-0 rounded-lg text-sm text-neutral-950 placeholder:text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689] resize-none"
          :disabled="isLoading"
        />
        <p v-if="errors.mo_ta" class="text-xs text-red-500">
          {{ errors.mo_ta }}
        </p>
      </div>

      <!-- Error Message -->
      <div
        v-if="errorMessage"
        class="bg-red-50 border border-red-200 rounded-lg px-3 py-2"
      >
        <p class="text-sm text-red-600">{{ errorMessage }}</p>
      </div>

      <!-- Success Message -->
      <div
        v-if="successMessage"
        class="bg-green-50 border border-green-200 rounded-lg px-3 py-2"
      >
        <p class="text-sm text-green-600">{{ successMessage }}</p>
      </div>
    </div>

    <!-- Footer -->
    <div class="flex items-center justify-end gap-2 px-6 py-6 mt-8">
      <button
        @click="closeDialog"
        :disabled="isLoading"
        class="h-9 px-4 bg-white border !border-gray-300 rounded-lg text-sm font-medium text-neutral-950 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Hủy
      </button>
      <button
        @click="addCategory"
        :disabled="!form.ten_phan_loai.trim() || isLoading"
        class="h-9 px-4 bg-[#009689] rounded-lg text-sm font-medium text-white hover:bg-[#008177] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
      >
        <span v-if="isLoading" class="inline-block animate-spin">⏳</span>
        {{ isLoading ? "Đang thêm..." : "Thêm danh mục" }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import client from "../../../../../utils/api.js";

// Props
const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
});

// Emits
const emit = defineEmits(["update:modelValue", "category-added", "close"]);

// State
const form = ref({
  ten_phan_loai: "",
  mo_ta: "",
});
const isLoading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const errors = ref({});

// Icons
const iconPlus =
  "https://www.figma.com/api/mcp/asset/8e49ba51-073d-4b1c-9e55-7a621afa4eab";
const iconClose =
  "https://www.figma.com/api/mcp/asset/4328a1eb-684c-4f6b-8ce6-b3a3dfe83234";

// Methods
const closeDialog = () => {
  form.value = {
    ten_phan_loai: "",
    mo_ta: "",
  };
  errors.value = {};
  errorMessage.value = "";
  successMessage.value = "";
  emit("update:modelValue", false);
  emit("close");
};

const validateForm = () => {
  errors.value = {};
  let isValid = true;

  // Validate ten_phan_loai
  if (!form.value.ten_phan_loai.trim()) {
    errors.value.ten_phan_loai = "Tên danh mục không được để trống";
    isValid = false;
  } else if (form.value.ten_phan_loai.trim().length < 3) {
    errors.value.ten_phan_loai = "Tên danh mục phải có ít nhất 3 ký tự";
    isValid = false;
  } else if (form.value.ten_phan_loai.trim().length > 255) {
    errors.value.ten_phan_loai = "Tên danh mục không được vượt quá 255 ký tự";
    isValid = false;
  }

  // Validate mo_ta
  if (form.value.mo_ta && form.value.mo_ta.length > 1000) {
    errors.value.mo_ta = "Mô tả không được vượt quá 1000 ký tự";
    isValid = false;
  }

  return isValid;
};

const addCategory = async () => {
  errorMessage.value = "";
  successMessage.value = "";

  // Validate form
  if (!validateForm()) {
    return;
  }

  isLoading.value = true;

  try {
    const response = await client.post("/phan-loai-bai-viet", {
      ten_phan_loai: form.value.ten_phan_loai.trim(),
      mo_ta: form.value.mo_ta.trim() || null,
    });

    if (response.data.status) {
      successMessage.value =
        response.data.message || "Thêm danh mục thành công";
      console.log("✅ Category created:", response.data.data);

      // Emit event with new category data
      emit("category-added", response.data.data);

      // Close dialog after 1 second
      setTimeout(() => {
        closeDialog();
      }, 1000);
    } else {
      errorMessage.value = response.data.message || "Lỗi khi thêm danh mục";
    }
  } catch (error) {
    console.error("Error adding category:", error);

    if (error.response?.data?.errors) {
      // Backend validation errors
      const backendErrors = error.response.data.errors;
      for (const [key, messages] of Object.entries(backendErrors)) {
        errors.value[key] = Array.isArray(messages) ? messages[0] : messages;
      }
      errorMessage.value = "Vui lòng kiểm tra lại các thông tin nhập";
    } else if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message;
    } else if (error.message) {
      errorMessage.value = error.message;
    } else {
      errorMessage.value = "Lỗi không xác định khi thêm danh mục";
    }
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
/* Nunito Sans font family */
* {
  font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI",
    Roboto, "Helvetica Neue", Arial, sans-serif;
}
</style>
