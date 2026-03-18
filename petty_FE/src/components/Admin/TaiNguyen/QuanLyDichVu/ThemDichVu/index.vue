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
                <!-- small spinner when loading categories -->
                <svg
                  v-if="loadingCats"
                  class="animate-spin h-4 w-4 text-gray-500 flex-shrink-0"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
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
            <!-- inline category error -->
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
              placeholder="200000"
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

          <!-- Image Upload -->
          <div class="flex flex-col gap-2">
            <label
              class="font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
            >
              Ảnh đại diện
            </label>
            <div
              class="border-2 border-dashed border-[#d1d5dc] rounded-[10px] h-40 flex flex-col items-center justify-center gap-2 cursor-pointer hover:border-gray-400 transition-colors"
              @click="triggerFileInput"
            >
              <p class="text-sm leading-6 text-[#4a5565] tracking-tight">
                Click để chọn ảnh
              </p>
              <p class="text-xs leading-4 text-[#99a1af]">
                PNG, JPG, GIF (Max 5MB)
              </p>
            </div>
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
// API origin (strip trailing /api) so we can build absolute URLs for uploaded files
const API_BASE = import.meta.env.VITE_API_BASE || "http://127.0.0.1:8000/api";
const API_ORIGIN = API_BASE.replace(/\/api\/?$/, "");
import { showErrorToast, showSuccessToast } from "@/utils/toast";
// Icon SVG
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
const emit = defineEmits(["close", "save"]);

// State
const selectedDepartment = ref("");
const selectedCategory = ref("");
const selectedCategoryId = ref(null);
const showCategoryDropdown = ref(false);
const categories = ref([]);
const loadingCats = ref(false);
// validation errors from server or client
const errors = reactive({});
const fileInput = ref(null);

const formData = reactive({
  name: "",
  code: "",
  price: null,
  duration: null,
  description: "",
  instructions: "",
  requireBooking: true,
  isActive: true,
  image: null,
});
const saving = ref(false);

// formatted price (VND) shown in the input while storing numeric value in formData.price
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
    } catch (e) {
      return String(v);
    }
  },
  set(val) {
    // remove non-digit characters
    const digits = String(val).replace(/[^0-9]/g, "");
    if (digits === "") formData.price = null;
    else formData.price = parseInt(digits, 10);
  },
});

// Icon URLs from Figma (expire in 7 days)
const iconChevronDown =
  "https://www.figma.com/api/mcp/asset/c1f4500a-976f-4d96-9aa9-7e5957c97728";
const iconUpload =
  "https://www.figma.com/api/mcp/asset/f875eb22-4532-4e8e-a61d-a50ba158caec";

// Methods
const toggleDepartmentDropdown = () => {
  console.log("Toggle department dropdown");
  // Implement department dropdown logic here
};

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
    showErrorToast("Lỗi", "Không tải được danh mục dịch vụ. Vui lòng thử lại.");
  } finally {
    loadingCats.value = false;
  }
};

const toggleCategoryDropdown = async () => {
  // Allow selecting category without requiring a department
  showCategoryDropdown.value = !showCategoryDropdown.value;
  if (showCategoryDropdown.value && categories.value.length === 0) {
    await fetchCategories();
  }
};

const selectCategory = (c) => {
  selectedCategory.value = c.ten_nhom;
  selectedCategoryId.value = c.id;
  showCategoryDropdown.value = false;
  // clear prior category error
  if (errors.category) delete errors.category;
};

const triggerFileInput = () => {
  fileInput.value?.click();
};

const handleFileUpload = (event) => {
  const file = event.target.files?.[0];
  if (file) {
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
      alert("File size must be less than 5MB");
      return;
    }

    // Validate file type
    const validTypes = ["image/png", "image/jpeg", "image/gif"];
    if (!validTypes.includes(file.type)) {
      alert("File type must be PNG, JPG, or GIF");
      return;
    }

    formData.image = file;
    console.log("File uploaded:", file.name);
  }
};

// Ensure price input only accepts digits. This will strip non-digits on paste/input
const onPriceInput = (event) => {
  const raw = event.target.value || "";
  const digits = String(raw).replace(/[^0-9]/g, "");
  // Update computed setter via its .value to normalize and store numeric value
  try {
    formattedPrice.value = digits;
  } catch (e) {
    // fallback: directly set formData.price
    formData.price = digits === "" ? null : parseInt(digits, 10);
  }
};

// Prevent non-numeric keys (allow navigation, editing, and clipboard shortcuts)
const priceKeydown = (e) => {
  const allowed = [
    "Backspace",
    "Delete",
    "ArrowLeft",
    "ArrowRight",
    "Tab",
    "Enter",
  ];
  if (allowed.includes(e.key)) return;
  if (e.ctrlKey || e.metaKey) return; // allow copy/paste/select all
  // allow digits
  if (/^[0-9]$/.test(e.key)) return;
  // otherwise prevent
  e.preventDefault();
};

const handleCancel = () => {
  emit("close");
};

const handleSave = async () => {
  // clear prior errors
  Object.keys(errors).forEach((k) => delete errors[k]);

  // Client-side validation: set errors to show inline instead of alert
  if (!selectedCategory.value) {
    errors.category = "Vui lòng chọn danh mục dịch vụ";
  }
  if (!formData.name) errors.name = "Vui lòng nhập tên dịch vụ";
  if (!formData.code) errors.code = "Vui lòng nhập mã dịch vụ";
  if (!formData.price && formData.price !== 0)
    errors.price = "Vui lòng nhập giá bán";
  if (!formData.duration && formData.duration !== 0)
    errors.duration = "Vui lòng nhập thời gian thực hiện";

  if (Object.keys(errors).length > 0) {
    // don't proceed if client-side errors
    return;
  }

  saving.value = true;
  try {
    // attach token if present
    try {
      attachToken();
    } catch (_) {}

    // If image selected, upload it first and get a path
    let imagePath = null;
    if (formData.image) {
      try {
        const fd = new FormData();
        fd.append("file", formData.image);
        const upRes = await api.post("/upload", fd, {
          headers: { "Content-Type": "multipart/form-data" },
        });
        // support various response shapes
        if (upRes && upRes.data) {
          imagePath =
            (upRes.data.data &&
              (upRes.data.data.path || upRes.data.data.url)) ||
            upRes.data.path ||
            upRes.data.url ||
            null;
        }
        if (!imagePath) {
          // fallback: some APIs return the stored filename under data.file
          if (upRes && upRes.data && upRes.data.data && upRes.data.data.file)
            imagePath = upRes.data.data.file;
        }
        // normalize to absolute URL if backend returned a relative path
        if (imagePath && !/^https?:\/\//i.test(imagePath)) {
          if (!imagePath.startsWith("/")) imagePath = "/" + imagePath;
          imagePath = API_ORIGIN + imagePath;
        }
      } catch (ue) {
        console.error("upload error", ue);
        // if upload fails, show message and abort
        const msg =
          ue && ue.response && ue.response.data && ue.response.data.message
            ? ue.response.data.message
            : "Không thể tải ảnh lên. Vui lòng thử lại.";
        showErrorToast("Lỗi upload", msg);
        saving.value = false;
        return;
      }
    }

    // Build payload matching backend fields
    const payload = {
      ten: formData.name,
      gia_tien: formData.price,
      thoi_gian_thuc_hien: formData.duration,
      mo_ta: formData.description || null,
      ma_dich_vu: formData.code || null,
      huong_dan: formData.instructions || null,
      trang_thai: formData.isActive ? "kinh_doanh" : "ngung",
      danh_muc_id: selectedCategoryId.value || null,
      anh_dich_vu: imagePath || null,
    };

    const res = await api.post("/dich-vu", payload);
    if (res && res.data && res.data.status) {
      const created = res.data.data;
      // emit created backend item so parent can update list
      emit("save", created);
      // show success and close modal
      showSuccessToast("Thành công", "Tạo dịch vụ thành công.");
      // auto-close modal
      emit("close");
    } else {
      const msg =
        (res && res.data && res.data.message) || "Lỗi khi tạo dịch vụ.";
      showErrorToast("Lỗi", msg);
    }
  } catch (e) {
    console.error("create service error", e);
    // show server-side validation errors inline when available
    if (e && e.response && e.response.status === 422) {
      const respErrors = (e.response.data && e.response.data.errors) || {};
      Object.keys(respErrors).forEach((k) => {
        // map backend field names to our form keys if necessary
        // backend may return 'ten', 'gia_tien', 'thoi_gian_thuc_hien', 'danh_muc_id', etc.
        if (k === "ten") errors.name = respErrors[k].join(" ");
        else if (k === "ma_dich_vu") errors.code = respErrors[k].join(" ");
        else if (k === "gia_tien") errors.price = respErrors[k].join(" ");
        else if (k === "thoi_gian_thuc_hien")
          errors.duration = respErrors[k].join(" ");
        else if (k === "danh_muc_id") errors.category = respErrors[k].join(" ");
        else errors[k] = respErrors[k].join(" ");
      });
      // focus first error could be implemented by user if desired
    } else {
      const msg =
        e && e.response && e.response.data && e.response.data.message
          ? e.response.data.message
          : "Không thể tạo dịch vụ. Vui lòng kiểm tra quyền hoặc thử lại.";
      showErrorToast("Lỗi", msg);
    }
  } finally {
    saving.value = false;
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
