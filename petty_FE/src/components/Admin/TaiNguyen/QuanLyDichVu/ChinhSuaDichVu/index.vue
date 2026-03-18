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
          Chỉnh sửa dịch vụ
        </h2>
        <p class="text-sm leading-5 text-[#717182] tracking-tight">
          Cập nhật thông tin dịch vụ
        </p>
      </div>

      <!-- Form Content - 2 Column Layout -->
      <div class="grid grid-cols-2 gap-6 flex-1 overflow-y-auto pr-2">
        <!-- Left Column -->
        <div class="flex flex-col gap-4">
          <!-- Category -->
          <div class="relative">
            <div class="flex items-center justify-between mb-2">
              <label
                class="font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
              >
                Danh mục dịch vụ (*)
              </label>
              <button
                class="font-medium text-xs leading-4 text-[#009689] hover:text-[#007d72] transition-colors"
                @click="openCreateCategory"
              >
                Tạo nhóm mới
              </button>
            </div>
            <button
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-[13px] py-0.5 flex items-center justify-between w-full hover:bg-gray-200 transition-colors"
              @click="toggleCategoryDropdown"
            >
              <span
                class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight"
              >
                {{ formData.category }}
              </span>
              <ChevronDownIcon />
            </button>
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
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none"
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
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none"
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
              v-model="formData.priceDisplay"
              type="text"
              inputmode="numeric"
              @input="onPriceInput"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none"
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
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none"
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
              v-model="formData.mo_ta"
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
              v-model="formData.huong_dan"
              placeholder="Nhập hướng dẫn hoặc lưu ý cho khách hàng..."
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
            <div class="flex items-center gap-3">
              <!-- <span class="font-medium text-sm text-[#374151]">
                {{ formData.isActive ? "Kinh doanh" : "Ngừng" }}
              </span> -->
              <button
                type="button"
                class="relative w-8 h-5 rounded-full transition-colors flex-shrink-0"
                :class="formData.isActive ? 'bg-[#030213]' : 'bg-gray-300'"
                @click="toggleActive"
                @keydown.enter.prevent="toggleActive"
                tabindex="0"
                role="switch"
                :aria-checked="formData.isActive"
              >
                <span
                  class="absolute top-0.5 w-4 h-4 bg-white rounded-full transition-transform"
                  :class="formData.isActive ? 'left-[15px]' : 'left-0.5'"
                ></span>
              </button>
            </div>
          </div>

          <!-- Image Upload -->
          <div class="flex flex-col gap-2">
            <label
              class="font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
            >
              Ảnh đại diện
            </label>
            <div
              class="border-2 border-[#d1d5dc] border-solid rounded-[10px] h-40 relative overflow-hidden"
            >
              <img
                v-if="formData.image"
                :src="formData.image"
                alt="Service image"
                class="w-full h-full object-cover"
              />
              <button
                v-if="formData.image"
                class="absolute top-[10px] right-[10px] bg-[#d4183d] rounded-lg w-9 h-8 flex items-center justify-center hover:bg-[#b01430] transition-colors"
                @click="removeImage"
              >
                <TrashIcon class="w-4 h-4 text-white" />
              </button>
              <button
                class="absolute bottom-[10px] left-[10px] bg-[#009689] text-white rounded-md px-3 py-1 text-xs hover:bg-[#007d72] transition-colors"
                type="button"
                @click="
                  () => fileInput && fileInput.value && fileInput.value.click()
                "
              >
                {{ formData.image ? "Thay ảnh" : "Chọn ảnh" }}
              </button>
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
          @click="handleUpdate"
          :disabled="saving"
        >
          <span class="font-medium text-sm leading-5 text-white tracking-tight">
            <span v-if="!saving">Cập nhật</span>
            <span v-else>Đang cập nhật...</span>
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from "vue";
import api, { attachToken } from "@/utils/api";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
// Icon SVG
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import TrashIcon from "@/assets/svg/trash.svg";

const props = defineProps({
  service: {
    type: Object,
    required: true,
    default: () => ({
      id: "",
      department: "Spa & Grooming",
      category: "Cắt tỉa",
      name: "Cắt tỉa lông chó < 5kg",
      code: "SPA-CT-001",
      price: 200000,
      duration: 60,
      description: "",
      requireBooking: true,
      isActive: true,
      image:
        "https://www.figma.com/api/mcp/asset/3449db84-6f8e-45dd-9598-4ac9983daaa8",
    }),
  },
});

const emit = defineEmits(["close", "update", "openCreateCategory"]);

// State
const fileInput = ref(null);

// validation errors
const errors = reactive({});
const saving = ref(false);

const formData = reactive({
  department: props.service.department,
  category: props.service.category,
  name: props.service.name,
  code: props.service.code,
  // backend field is 'gia_tien'
  price: props.service.gia_tien ?? props.service.price,
  // backend field is 'thoi_gian_thuc_hien'
  duration: props.service.thoi_gian_thuc_hien ?? props.service.duration,
  // description (mo_ta) and guidance (huong_dan)
  mo_ta: props.service.mo_ta ?? props.service.description ?? null,
  huong_dan: props.service.huong_dan ?? null,
  requireBooking: props.service.requireBooking,
  // map backend trang_thai to boolean isActive
  isActive:
    typeof props.service.trang_thai !== "undefined"
      ? props.service.trang_thai === "kinh_doanh"
      : props.service.isActive,
  image: props.service.image,
  // holds the actual File when user selects a new image
  _file: null,
  // helper for showing formatted price
  priceDisplay: "",
});

// Methods
const toggleDepartmentDropdown = () => {
  console.log("Toggle department dropdown");
  // Implement dropdown logic here
};

const toggleCategoryDropdown = () => {
  console.log("Toggle category dropdown");
  // Implement dropdown logic here
};

const openCreateCategory = () => {
  emit("openCreateCategory");
};

const handleFileUpload = (event) => {
  const file = event.target.files?.[0];
  if (file) {
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
      showErrorToast("Lỗi", "Kích thước file phải nhỏ hơn 5MB");
      return;
    }

    // Validate file type
    const validTypes = ["image/png", "image/jpeg", "image/gif"];
    if (!validTypes.includes(file.type)) {
      showErrorToast("Lỗi", "Định dạng ảnh phải là PNG, JPG hoặc GIF");
      return;
    }

    // Create preview URL and keep the real File for upload
    const reader = new FileReader();
    reader.onload = (e) => {
      formData.image = e.target.result;
    };
    reader.readAsDataURL(file);
    formData._file = file;
  }
};

// Toggle service active state (keyboard + click)
const toggleActive = () => {
  formData.isActive = !formData.isActive;
};

// Normalize boolean -> backend trang_thai string
const getTrangThai = (active) => (active ? "kinh_doanh" : "ngung");

// Format number as VND currency string
const formatVND = (value) => {
  if (value === null || value === undefined || value === "") return "";
  const num = Number(value) || 0;
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
    maximumFractionDigits: 0,
  }).format(num);
};

// Keep numeric price in formData.price and formatted string in priceDisplay
const onPriceInput = (e) => {
  const raw = e.target.value || "";
  // Remove everything except digits
  const digits = raw.replace(/[^0-9]/g, "");
  const num = digits === "" ? null : parseInt(digits, 10);
  formData.price = num;
  formData.priceDisplay = num === null ? "" : formatVND(num);
};

// initialize priceDisplay
if (formData.price !== null && formData.price !== undefined) {
  formData.priceDisplay = formatVND(formData.price);
}

// Watch for prop changes and sync into formData (supports multiple naming variants)
watch(
  () => props.service,
  (s) => {
    if (!s) return;
    formData.department = s.department ?? formData.department;
    formData.category = s.category ?? formData.category;
    formData.name = s.name ?? formData.name;
    formData.code = s.code ?? formData.code;

    // price variants: gia_tien or price
    formData.price = s.gia_tien ?? s.price ?? formData.price;
    formData.priceDisplay =
      formData.price !== null && formData.price !== undefined
        ? formatVND(formData.price)
        : formData.priceDisplay;

    // duration variants
    formData.duration =
      s.thoi_gian_thuc_hien ?? s.duration ?? formData.duration;

    // mo_ta / description
    formData.mo_ta = s.mo_ta ?? s.moTa ?? s.description ?? formData.mo_ta;

    // huong_dan variants
    formData.huong_dan =
      s.huong_dan ?? s.huongDan ?? s.huongdan ?? formData.huong_dan;

    // status variants -> boolean isActive
    if (typeof s.trang_thai !== "undefined") {
      formData.isActive = s.trang_thai === "kinh_doanh";
    } else if (typeof s.trangThai !== "undefined") {
      formData.isActive = s.trangThai === "kinh_doanh";
    } else if (typeof s.isActive === "boolean") {
      formData.isActive = s.isActive;
    } else if (typeof s.active === "boolean") {
      formData.isActive = s.active;
    }

    formData.image = s.anh_dich_vu ?? s.image ?? formData.image;
  },
  { immediate: true, deep: true }
);

const removeImage = () => {
  formData.image = "";
  formData._file = null;
  if (fileInput.value) {
    fileInput.value.value = "";
  }
};

const handleCancel = () => {
  emit("close");
};

const handleUpdate = async () => {
  // clear prior errors
  Object.keys(errors).forEach((k) => delete errors[k]);

  // Client-side validation
  if (!formData.name) errors.name = "Vui lòng nhập tên dịch vụ";
  if (!formData.code) errors.code = "Vui lòng nhập mã dịch vụ";
  if (
    formData.price === null ||
    formData.price === undefined ||
    formData.price === ""
  )
    errors.price = "Vui lòng nhập giá bán";
  if (
    formData.duration === null ||
    formData.duration === undefined ||
    formData.duration === ""
  )
    errors.duration = "Vui lòng nhập thời gian thực hiện";

  if (Object.keys(errors).length > 0) return;

  saving.value = true;
  try {
    try {
      attachToken();
    } catch (e) {
      /* ignore */
    }

    // If user selected a new file, send multipart with _method=PUT so Laravel can handle file
    if (formData._file) {
      const fd = new FormData();
      fd.append("_method", "PUT");
      fd.append("file", formData._file);
      fd.append("ten", formData.name);
      fd.append("ma_dich_vu", formData.code);
      fd.append("gia_tien", formData.price);
      fd.append("thoi_gian_thuc_hien", formData.duration);
      fd.append("mo_ta", formData.mo_ta || "");
      fd.append("huong_dan", formData.huong_dan || "");
      fd.append("trang_thai", getTrangThai(formData.isActive));

      const res = await api.post(`/dich-vu/${props.service.id}`, fd, {
        headers: { "Content-Type": "multipart/form-data" },
      });

      if (res && res.data && res.data.status) {
        showSuccessToast("Thành công", "Cập nhật dịch vụ thành công.");
        emit("update", res.data.data);
        emit("close");
      } else {
        const msg =
          (res && res.data && res.data.message) || "Lỗi khi cập nhật dịch vụ.";
        showErrorToast("Lỗi", msg);
      }
    } else {
      // No new file: send JSON via PUT
      const payload = {
        ten: formData.name,
        ma_dich_vu: formData.code,
        gia_tien: formData.price,
        thoi_gian_thuc_hien: formData.duration,
        mo_ta: formData.mo_ta || null,
        huong_dan: formData.huong_dan || null,
        trang_thai: getTrangThai(formData.isActive),
        // keep existing anh_dich_vu if present
        anh_dich_vu: formData.image || null,
      };

      const res = await api.put(`/dich-vu/${props.service.id}`, payload);
      if (res && res.data && res.data.status) {
        showSuccessToast("Thành công", "Cập nhật dịch vụ thành công.");
        emit("update", res.data.data);
        emit("close");
      } else {
        const msg =
          (res && res.data && res.data.message) || "Lỗi khi cập nhật dịch vụ.";
        showErrorToast("Lỗi", msg);
      }
    }
  } catch (e) {
    console.error("update error", e);
    if (e && e.response && e.response.status === 422) {
      const respErrors = (e.response.data && e.response.data.errors) || {};
      Object.keys(respErrors).forEach((k) => {
        if (k === "ten") errors.name = respErrors[k].join(" ");
        else if (k === "ma_dich_vu") errors.code = respErrors[k].join(" ");
        else if (k === "gia_tien") errors.price = respErrors[k].join(" ");
        else if (k === "thoi_gian_thuc_hien")
          errors.duration = respErrors[k].join(" ");
        else errors[k] = respErrors[k].join(" ");
      });
    } else {
      const msg =
        e && e.response && e.response.data && e.response.data.message
          ? e.response.data.message
          : "Có lỗi khi cập nhật dịch vụ.";
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
