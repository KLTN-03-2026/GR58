<template>
  <div
    class="relative bg-white border !border-gray-200 rounded-[10px] w-full max-h-[85vh] overflow-hidden flex flex-col"
  >
    <div class="flex flex-col p-6 flex-1 overflow-hidden">
      <!-- Header -->
      <div class="flex flex-col gap-2 mb-4">
        <h2
          class="font-semibold text-lg leading-[18px] text-neutral-950 tracking-tight"
        >
          Thêm dịch vụ mới
        </h2>
        <p class="text-sm leading-5 text-[#717182] tracking-tight">
          Nhập đầy đủ thông tin dịch vụ
        </p>
      </div>

      <!-- Form Content - 2 Column Layout -->
      <div class="grid grid-cols-2 gap-6 flex-1 overflow-y-auto pr-2">
        <!-- Left Column -->
        <div class="flex flex-col gap-4">
          <!-- Step 1: Select Category -->
          <div class="relative">
            <label
              class="font-medium text-sm leading-[14px] text-neutral-950 tracking-tight mb-2 block"
            >
              Bước 1: Chọn Danh mục dịch vụ (*)
            </label>
            <button
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-[13px] py-0.5 flex items-center justify-between w-full transition-colors hover:bg-gray-200 cursor-pointer"
              @click="toggleCategoryDropdown"
            >
              <span class="flex items-center gap-2">
                <span
                  class="text-sm leading-5 text-[#717182] tracking-tight truncate"
                >
                  {{ selectedCategory || "Vui lòng chọn danh mục dịch vụ" }}
                </span>
                <svg
                  v-if="loadingCats"
                  class="animate-spin h-4 w-4 text-gray-500 flex-shrink-0"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12" cy="12" r="10"
                    stroke="currentColor" stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                  ></path>
                </svg>
              </span>
              <ChevronDownIcon />
            </button>
            <!-- Category dropdown -->
            <div
              v-if="showCategoryDropdown"
              class="absolute top-[70px] left-0 w-full z-50"
            >
              <div
                class="bg-white border border-gray-200 rounded-lg max-h-48 overflow-auto shadow"
              >
                <button
                  v-for="c in categories"
                  :key="c.id"
                  @click="selectCategory(c)"
                  class="w-full text-left px-3 py-2 hover:bg-gray-100 transition-colors text-sm"
                >
                  {{ c.ten_nhom }}
                </button>
                <div v-if="loadingCats" class="p-3 text-xs text-gray-500">
                  Đang tải...
                </div>
                <div
                  v-if="!loadingCats && categories.length === 0"
                  class="p-3 text-xs text-gray-500"
                >
                  Chưa có danh mục
                </div>
              </div>
            </div>
            <div v-if="errors.category" class="text-xs text-red-600 mt-1">
              {{ errors.category }}
            </div>
          </div>

          <!-- Service Name -->
          <div class="flex flex-col gap-2">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
            >
              Tên dịch vụ (*)
            </label>
            <input
              v-model="formData.name"
              type="text"
              placeholder="Ví dụ: Cắt tỉa lông chó < 5kg"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182]"
            />
            <div v-if="errors.name" class="text-xs text-red-600">
              {{ errors.name }}
            </div>
          </div>

          <!-- Service Code -->
          <div class="flex flex-col gap-2">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
            >
              Mã Dịch Vụ (*)
            </label>
            <input
              v-model="formData.code"
              type="text"
              placeholder="Ví dụ: SPA-CT-001"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182]"
            />
            <div v-if="errors.code" class="text-xs text-red-600">
              {{ errors.code }}
            </div>
          </div>

          <!-- Price -->
          <div class="flex flex-col gap-2">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
            >
              Giá bán (VNĐ) (*)
            </label>
            <input
              v-model="formattedPrice"
              type="text"
              inputmode="numeric"
              placeholder="200,000 ₫"
              @input="onPriceInput"
              @keydown="priceKeydown"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182]"
            />
            <div v-if="errors.price" class="text-xs text-red-600">
              {{ errors.price }}
            </div>
          </div>

          <!-- Duration -->
          <div class="flex flex-col gap-2">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
            >
              Thời gian (phút) (*)
            </label>
            <input
              v-model.number="formData.duration"
              type="number"
              placeholder="60"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182]"
            />
            <div v-if="errors.duration" class="text-xs text-red-600">
              {{ errors.duration }}
            </div>
            <p class="font-nunito text-xs leading-4 text-[#6a7282]">
              Quan trọng để xếp lịch
            </p>
          </div>
        </div>

        <!-- Right Column -->
        <div class="flex flex-col gap-4">
          <!-- Description -->
          <div class="flex flex-col gap-2">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
            >
              Mô tả
            </label>
            <textarea
              v-model="formData.description"
              placeholder="Nhập mô tả chi tiết về dịch vụ..."
              rows="3"
              class="bg-[#f3f3f5] border-none rounded-lg px-3 py-2 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] resize-none"
            ></textarea>
          </div>

          <!-- Instructions -->
          <div class="flex flex-col gap-2">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
            >
              Hướng dẫn
            </label>
            <textarea
              v-model="formData.instructions"
              placeholder="Nhập hướng dẫn sử dụng dịch vụ..."
              rows="3"
              class="bg-[#f3f3f5] border-none rounded-lg px-3 py-2 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] resize-none"
            ></textarea>
          </div>

          <!-- Status -->
          <div
            class="bg-gray-50 rounded-[10px] px-3 py-3 flex items-center justify-between"
          >
            <div class="flex flex-col">
              <label
                class="font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
              >
                Trạng thái
              </label>
              <p class="text-xs leading-4 text-[#4a5565]">
                Cho phép KH đặt dịch vụ này
              </p>
            </div>
            <button
              type="button"
              class="relative w-8 h-5 rounded-full transition-colors flex-shrink-0"
              :class="formData.isActive ? 'bg-[#030213]' : 'bg-gray-300'"
              @click="formData.isActive = !formData.isActive"
            >
              <span
                class="absolute top-0.5 w-4 h-4 bg-white rounded-full transition-transform"
                :class="formData.isActive ? 'left-[15px]' : 'left-0.5'"
              ></span>
            </button>
          </div>

          <!-- ✅ Image Upload với preview -->
          <div class="flex flex-col gap-2">
            <label
              class="font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
            >
              Ảnh đại diện
            </label>
            <div
              class="border-2 border-[#d1d5dc] border-solid rounded-[10px] h-40 relative overflow-hidden"
              :class="!imagePreview ? 'border-dashed flex flex-col items-center justify-center gap-2 cursor-pointer hover:border-gray-400 transition-colors' : ''"
              @click="!imagePreview && triggerFileInput()"
            >
              <!-- Preview ảnh đã chọn -->
              <img
                v-if="imagePreview"
                :src="imagePreview"
                alt="Preview"
                class="w-full h-full object-cover"
              />

              <!-- Placeholder khi chưa chọn ảnh -->
              <template v-if="!imagePreview">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
                <p class="text-sm leading-6 text-[#4a5565] tracking-tight">Click để chọn ảnh</p>
                <p class="text-xs leading-4 text-[#99a1af]">PNG, JPG, GIF (Max 5MB)</p>
              </template>

              <!-- Nút xóa ảnh -->
              <button
                v-if="imagePreview"
                class="absolute top-[10px] right-[10px] bg-[#d4183d] rounded-lg w-9 h-8 flex items-center justify-center hover:bg-[#b01430] transition-colors"
                type="button"
                @click.stop="removeImage"
              >
                <TrashIcon class="w-4 h-4 text-white" />
              </button>

              <!-- Nút thay ảnh -->
              <button
                v-if="imagePreview"
                class="absolute bottom-[10px] left-[10px] bg-[#009689] text-white rounded-md px-3 py-1 text-xs hover:bg-[#007d72] transition-colors"
                type="button"
                @click.stop="triggerFileInput"
              >
                Thay ảnh
              </button>
            </div>

            <!-- Hidden file input -->
            <input
              ref="fileInput"
              type="file"
              accept="image/png,image/jpeg,image/gif,image/webp"
              class="hidden"
              @change="handleFileUpload"
            />
          </div>
        </div>
      </div>

      <!-- Footer Buttons -->
      <div class="flex gap-2 justify-end mt-4 pt-4 border-t border-gray-200">
        <button
          class="bg-white border !border-gray-300 rounded-lg h-9 px-[17px] py-[9px] flex items-center justify-center hover:bg-gray-50 transition-colors"
          @click="handleCancel"
        >
          <span
            class="font-medium text-sm leading-5 text-neutral-950 tracking-tight"
          >
            Hủy
          </span>
        </button>
        <button
          class="bg-[#5a9690] rounded-lg h-9 px-4 py-2 flex items-center justify-center hover:bg-[#007d72] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          @click="handleSave"
          :disabled="saving"
        >
          <span class="font-medium text-sm leading-5 text-white tracking-tight">
            <span v-if="!saving">Lưu lại</span>
            <span v-else>Đang lưu...</span>
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from "vue";
import api, { attachToken } from "@/utils/api";
import { showErrorToast, showSuccessToast } from "@/utils/toast";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import TrashIcon from "@/assets/svg/trash.svg";

const emit = defineEmits(["close", "save"]);

// ─── State ────────────────────────────────────────────────────────────
const selectedCategory   = ref("");
const selectedCategoryId = ref(null);
const showCategoryDropdown = ref(false);
const categories   = ref([]);
const loadingCats  = ref(false);
const errors       = reactive({});
const fileInput    = ref(null);
const saving       = ref(false);

// ✅ Tách preview URL và file object
const imagePreview = ref("");   // base64 preview để hiển thị
const imageFile    = ref(null); // File object thực để upload

const formData = reactive({
  name:         "",
  code:         "",
  price:        null,
  duration:     null,
  description:  "",
  instructions: "",
  isActive:     true,
});

// ─── Formatted price ──────────────────────────────────────────────────
const formattedPrice = computed({
  get() {
    const v = formData.price;
    if (v === null || v === undefined || v === "") return "";
    try {
      return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
        maximumFractionDigits: 0,
      }).format(Number(v));
    } catch {
      return String(v);
    }
  },
  set(val) {
    const digits = String(val).replace(/[^0-9]/g, "");
    formData.price = digits === "" ? null : parseInt(digits, 10);
  },
});

const onPriceInput = (event) => {
  const digits = (event.target.value || "").replace(/[^0-9]/g, "");
  formData.price = digits === "" ? null : parseInt(digits, 10);
};

const priceKeydown = (e) => {
  const allowed = ["Backspace", "Delete", "ArrowLeft", "ArrowRight", "Tab", "Enter"];
  if (allowed.includes(e.key) || e.ctrlKey || e.metaKey) return;
  if (/^[0-9]$/.test(e.key)) return;
  e.preventDefault();
};

// ─── Category ─────────────────────────────────────────────────────────
const fetchCategories = async () => {
  loadingCats.value = true;
  try {
    const res = await api.get("/danh-muc-dich-vu");
    const items = (res && res.data && res.data.data) || [];
    categories.value = items.map((i) => ({
      id: i.id,
      ten_nhom: i.ten_nhom,
      mo_ta: i.mo_ta,
    }));
  } catch (e) {
    console.error("fetchCategories error", e);
    showErrorToast("Lỗi", "Không tải được danh mục dịch vụ.");
  } finally {
    loadingCats.value = false;
  }
};

const toggleCategoryDropdown = async () => {
  showCategoryDropdown.value = !showCategoryDropdown.value;
  if (showCategoryDropdown.value && categories.value.length === 0) {
    await fetchCategories();
  }
};

const selectCategory = (c) => {
  selectedCategory.value   = c.ten_nhom;
  selectedCategoryId.value = c.id;
  showCategoryDropdown.value = false;
  if (errors.category) delete errors.category;
};

// ─── Image Upload ─────────────────────────────────────────────────────
const triggerFileInput = () => {
  fileInput.value?.click();
};

const handleFileUpload = (event) => {
  const file = event.target.files?.[0];
  if (!file) return;

  if (file.size > 5 * 1024 * 1024) {
    showErrorToast("Lỗi", "Kích thước file phải nhỏ hơn 5MB");
    return;
  }

  const validTypes = ["image/png", "image/jpeg", "image/gif", "image/webp"];
  if (!validTypes.includes(file.type)) {
    showErrorToast("Lỗi", "Định dạng ảnh phải là PNG, JPG, GIF hoặc WEBP");
    return;
  }

  // ✅ Tạo preview bằng FileReader
  const reader = new FileReader();
  reader.onload = (e) => {
    imagePreview.value = e.target.result;
  };
  reader.readAsDataURL(file);

  // ✅ Lưu File object để upload
  imageFile.value = file;
};

const removeImage = () => {
  imagePreview.value = "";
  imageFile.value    = null;
  if (fileInput.value) fileInput.value.value = "";
};

// ─── Submit ───────────────────────────────────────────────────────────
const handleCancel = () => emit("close");

const handleSave = async () => {
  Object.keys(errors).forEach((k) => delete errors[k]);

  if (!selectedCategory.value) errors.category = "Vui lòng chọn danh mục dịch vụ";
  if (!formData.name)          errors.name     = "Vui lòng nhập tên dịch vụ";
  if (!formData.code)          errors.code     = "Vui lòng nhập mã dịch vụ";
  if (formData.price === null || formData.price === undefined)
    errors.price = "Vui lòng nhập giá bán";
  if (!formData.duration)
    errors.duration = "Vui lòng nhập thời gian thực hiện";

  if (Object.keys(errors).length > 0) return;

  saving.value = true;
  try {
    try { attachToken(); } catch (_) {}

    // ✅ Gửi thẳng file vào store() — không cần endpoint /upload riêng
    if (imageFile.value) {
      const fd = new FormData();
      fd.append("anh_dich_vu_file", imageFile.value); // field BE đã hỗ trợ
      fd.append("ten",                  formData.name);
      fd.append("ma_dich_vu",           formData.code);
      fd.append("gia_tien",             formData.price);
      fd.append("thoi_gian_thuc_hien",  formData.duration);
      fd.append("mo_ta",                formData.description  || "");
      fd.append("huong_dan",            formData.instructions || "");
      fd.append("trang_thai",           formData.isActive ? "kinh_doanh" : "ngung");
      fd.append("danh_muc_id",          selectedCategoryId.value || "");

      const res = await api.post("/dich-vu", fd, {
        headers: { "Content-Type": "multipart/form-data" },
      });

      if (res?.data?.status) {
        showSuccessToast("Thành công", "Tạo dịch vụ thành công.");
        emit("save", res.data.data);
        emit("close");
      } else {
        showErrorToast("Lỗi", res?.data?.message || "Lỗi khi tạo dịch vụ.");
      }
    } else {
      // Không có ảnh → gửi JSON bình thường
      const payload = {
        ten:                 formData.name,
        gia_tien:            formData.price,
        thoi_gian_thuc_hien: formData.duration,
        mo_ta:               formData.description  || null,
        ma_dich_vu:          formData.code         || null,
        huong_dan:           formData.instructions || null,
        trang_thai:          formData.isActive ? "kinh_doanh" : "ngung",
        danh_muc_id:         selectedCategoryId.value || null,
      };

      const res = await api.post("/dich-vu", payload);
      if (res?.data?.status) {
        showSuccessToast("Thành công", "Tạo dịch vụ thành công.");
        emit("save", res.data.data);
        emit("close");
      } else {
        showErrorToast("Lỗi", res?.data?.message || "Lỗi khi tạo dịch vụ.");
      }
    }
  } catch (e) {
    console.error("create service error", e);
    if (e?.response?.status === 422) {
      const respErrors = e.response.data?.errors || {};
      Object.keys(respErrors).forEach((k) => {
        if (k === "ten")                  errors.name     = respErrors[k].join(" ");
        else if (k === "ma_dich_vu")      errors.code     = respErrors[k].join(" ");
        else if (k === "gia_tien")        errors.price    = respErrors[k].join(" ");
        else if (k === "thoi_gian_thuc_hien") errors.duration = respErrors[k].join(" ");
        else if (k === "danh_muc_id")    errors.category = respErrors[k].join(" ");
        else errors[k] = respErrors[k].join(" ");
      });
    } else {
      const msg = e?.response?.data?.message || "Không thể tạo dịch vụ. Vui lòng thử lại.";
      showErrorToast("Lỗi", msg);
    }
  } finally {
    saving.value = false;
  }
};
</script>

<style scoped>
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