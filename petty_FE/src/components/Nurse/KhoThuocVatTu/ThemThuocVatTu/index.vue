<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white border !border-gray-300 rounded-[10px] shadow-lg w-[510px] relative"
    >
      <!-- Header -->
      <div class="flex flex-col gap-2 px-6 pt-6 pb-4">
        <h2 class="font-semibold text-lg text-black">Thêm Hàng Hoá mới</h2>
        <p class="text-sm text-[#717182] tracking-tight">
          Tạo mã hàng mới trong hệ thống
        </p>
      </div>

      <!-- Form Content -->
      <div class="px-6 py-1">
        <div class="flex flex-col gap-[66px]">
          <!-- Row 1: Code & Name -->
          <div class="grid grid-cols-2 gap-2">
            <!-- Code -->
            <div class="flex flex-col gap-0">
              <label class="font-medium text-sm text-black mb-0">
                Mã hàng hoá
              </label>
              <input
                v-model="formData.code"
                type="text"
                placeholder="VD: MED001"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
              />
            </div>

            <!-- Name -->
            <div class="flex flex-col gap-0">
              <label class="font-medium text-sm text-black mb-0">
                Tên mặt hàng
              </label>
              <input
                v-model="formData.name"
                type="text"
                placeholder="VD: Zoletil 50"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
              />
            </div>
          </div>

          <!-- Row 2: Category & Unit -->
          <div class="grid grid-cols-2 gap-4 -mt-[50px]">
            <!-- Category -->
            <div class="flex flex-col gap-0 relative">
              <label class="font-medium text-sm text-black mb-0">
                Phân loại
              </label>
              <button
                @click="toggleCategoryDropdown"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-[13px] py-0.5 flex items-center justify-between hover:bg-gray-200 transition-colors mt-0 text-left"
              >
                <span class="font-medium text-sm text-black tracking-tight">{{
                  formData.categoryName
                }}</span>
                <ChevronDownIcon />
              </button>

              <!-- Category Dropdown -->
              <div
                v-if="showCategoryDropdown"
                class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-48 overflow-y-auto"
              >
                <div
                  v-for="category in categories"
                  :key="category.id"
                  @click="selectCategory(category)"
                  class="px-3 py-2 hover:bg-gray-100 cursor-pointer font-medium text-sm text-black"
                >
                  {{ category.ten_danh_muc_hang_hoa || category.name }}
                </div>
                <div
                  v-if="categories.length === 0"
                  class="px-3 py-2 font-nunito text-sm text-gray-500 text-center"
                >
                  Không có danh mục
                </div>
              </div>
            </div>

            <!-- Unit -->
            <div class="flex flex-col gap-0">
              <label class="font-medium text-sm text-black mb-0">
                Đơn vị tính
              </label>
              <input
                v-model="formData.unit"
                type="text"
                placeholder="VD: Lọ, Viên, ml"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
              />
            </div>
          </div>

          <!-- Row 3: Cost Price & Sale Price -->
          <div class="grid grid-cols-2 gap-4 -mt-[50px]">
            <!-- Cost Price -->
            <div class="flex flex-col gap-0">
              <label class="font-medium text-sm text-black mb-0">
                Giá vốn (VNĐ)
              </label>
              <input
                v-model.number="formData.costPrice"
                type="number"
                placeholder="50000"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
              />
              <p class="font-nunito text-xs leading-4 text-[#6a7282] mt-1">
                {{ formatPriceVND(formData.costPrice) }}
              </p>
            </div>

            <!-- Sale Price -->
            <div class="flex flex-col gap-0">
              <label class="font-medium text-sm text-black mb-0">
                Giá bán (VNĐ)
              </label>
              <input
                v-model.number="formData.salePrice"
                type="number"
                placeholder="150000"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
              />
              <p class="font-nunito text-xs leading-4 text-[#6a7282] mt-1">
                {{ formatPriceVND(formData.salePrice) }}
              </p>
            </div>
          </div>

          <!-- Minimum Stock -->
          <div class="flex flex-col gap-0 -mt-[50px]">
            <label class="font-medium text-sm text-black mb-0">
              Định mức tối thiểu
            </label>
            <input
              v-model.number="formData.minStock"
              type="number"
              placeholder="10"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
            />
            <p class="font-nunito text-xs leading-4 text-[#6a7282] mt-1">
              Hệ thống sẽ cảnh báo khi tồn kho thấp hơn mức này
            </p>
          </div>

          <!-- Image Upload -->
          <div class="flex flex-col gap-0 -mt-[34px]">
            <label class="font-medium text-sm text-black mb-0">
              Ảnh sản phẩm
            </label>
            <div
              class="border-2 border-dashed border-[#d1d5dc] rounded-[10px] h-48 flex flex-col items-center justify-center gap-2 cursor-pointer hover:border-gray-400 transition-colors mt-0 relative overflow-hidden bg-white"
              @click="triggerFileInput"
            >
              <!-- Preview Image -->
              <img
                v-if="formData.imagePreviewUrl"
                :src="formData.imagePreviewUrl"
                alt="Preview"
                class="absolute inset-0 w-full h-full object-cover"
              />

              <!-- Upload Content (shown when no image selected) -->
              <div
                v-if="!formData.imagePreviewUrl"
                class="flex flex-col items-center justify-center gap-2 relative z-10"
              >
                <p
                  class="font-medium text-base leading-6 text-[#4a5565] tracking-tight"
                >
                  Click để chọn ảnh
                </p>
                <p class="font-medium text-xs leading-4 text-[#99a1af]">
                  PNG, JPG, GIF (Max 5MB)
                </p>
              </div>

              <!-- Clear Button (shown when image selected) -->
              <button
                v-if="formData.imagePreviewUrl"
                @click.stop="clearImage"
                class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-lg px-3 py-1 text-xs font-medium z-20"
              >
                Xóa ảnh
              </button>

              <input
                ref="fileInput"
                type="file"
                accept="image/png,image/jpeg,image/gif"
                class="hidden"
                @change="handleFileUpload"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div
        class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-200/60"
      >
        <button
          @click="$emit('close')"
          :disabled="isLoading"
          class="bg-white border !border-gray-300 rounded-lg h-9 px-[17px] py-[9px] hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span
            class="font-medium text-sm leading-4 text-neutral-950 tracking-tight"
            >Hủy</span
          >
        </button>
        <button
          @click="handleSubmit"
          class="bg-[#5a9690] rounded-lg h-9 px-4 py-2 hover:bg-[#5a9690]/80 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          :disabled="!isFormValid || isLoading"
        >
          <span
            v-if="!isLoading"
            class="font-medium text-sm leading-4 text-white tracking-tight"
            >Thêm hàng hóa</span
          >
          <span
            v-else
            class="font-medium text-sm leading-4 text-white tracking-tight"
            >Đang thêm...</span
          >
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { createHangHoa, uploadHangHoaImage } from "@/utils/hangHoa";
import { listDanhMucHangHoa } from "@/utils/danhMucHangHoa";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
// Icon SVG
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
// Props
defineProps({
  // No props needed for now
});

// Emits
const emit = defineEmits(["close", "save"]);

// Refs
const fileInput = ref(null);
const isLoading = ref(false);
const categories = ref([]);
const showCategoryDropdown = ref(false);

// Form Data
const formData = ref({
  code: "",
  name: "",
  categoryId: null,
  categoryName: "Chọn phân loại",
  unit: "",
  costPrice: null,
  salePrice: null,
  minStock: null,
  image: null,
  imageUrl: null,
  imagePreviewUrl: null,
});

// Computed
const isFormValid = computed(() => {
  return (
    formData.value.code &&
    formData.value.name &&
    formData.value.unit &&
    formData.value.costPrice !== null &&
    formData.value.salePrice !== null &&
    formData.value.minStock !== null &&
    formData.value.categoryId
  );
});

// Format price to VND display
const formatPriceVND = (price) => {
  if (!price) return "0 ₫";
  return `${parseInt(price).toLocaleString("vi-VN")} ₫`;
};

// Methods - Load categories
const loadCategories = async () => {
  try {
    categories.value = await listDanhMucHangHoa();
  } catch (error) {
    console.error("Lỗi tải danh mục:", error);
  }
};

// Methods - Category dropdown
const toggleCategoryDropdown = () => {
  showCategoryDropdown.value = !showCategoryDropdown.value;
  if (showCategoryDropdown.value && categories.value.length === 0) {
    loadCategories();
  }
};

const selectCategory = (category) => {
  formData.value.categoryId = category.id;
  formData.value.categoryName = category.ten_danh_muc_hang_hoa || category.name;
  showCategoryDropdown.value = false;
};

const triggerFileInput = () => {
  fileInput.value?.click();
};

const clearImage = () => {
  formData.value.image = null;
  formData.value.imagePreviewUrl = null;
  if (fileInput.value) {
    fileInput.value.value = "";
  }
};

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    // Check file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
      showErrorToast("Lỗi", "Kích thước file không được vượt quá 5MB");
      return;
    }

    // Check file type
    const validTypes = ["image/png", "image/jpeg", "image/gif"];
    if (!validTypes.includes(file.type)) {
      showErrorToast("Lỗi", "Chỉ chấp nhận file PNG, JPG, GIF");
      return;
    }

    formData.value.image = file;

    // Create preview URL
    const reader = new FileReader();
    reader.onload = (e) => {
      formData.value.imagePreviewUrl = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const handleSubmit = async () => {
  if (!isFormValid.value) {
    showErrorToast("Lỗi", "Vui lòng điền đầy đủ thông tin");
    return;
  }

  isLoading.value = true;
  try {
    let imageUrl = null;

    // Nếu có file hình ảnh, upload trước
    if (formData.value.image) {
      try {
        const uploadResult = await uploadHangHoaImage(formData.value.image);
        imageUrl = uploadResult.url || uploadResult.path || null;
      } catch (uploadError) {
        console.warn("Lỗi tải lên hình ảnh:", uploadError);
        // Tiếp tục với hàng hóa mà không có hình ảnh
      }
    }

    // Prepare payload for backend
    const payload = {
      code: formData.value.code,
      name: formData.value.name,
      unit: formData.value.unit,
      costPrice: formData.value.costPrice,
      salePrice: formData.value.salePrice,
      minStock: formData.value.minStock,
      categoryId: formData.value.categoryId,
      imageUrl: imageUrl,
      status: "hoat_dong",
    };

    // Call API to create hang hoa
    const result = await createHangHoa(payload);

    if (result) {
      showSuccessToast("Thành công", "Thêm hàng hóa thành công");
      emit("save", result);
      emit("close");
    } else {
      showErrorToast("Lỗi", "Không thể thêm hàng hóa");
    }
  } catch (error) {
    console.error("Lỗi:", error);
    const errorMessage =
      error.response?.data?.message || error.message || "Có lỗi xảy ra";
    showErrorToast("Lỗi", errorMessage);
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
/* Remove number input arrows */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
  appearance: textfield;
}
</style>
