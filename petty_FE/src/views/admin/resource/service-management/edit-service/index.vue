<template>
  <div
    class="relative bg-white border !border-gray-200 rounded-[10px] w-full max-h-[85vh] overflow-hidden flex flex-col"
  >
    <div class="flex flex-col p-6 flex-1 overflow-hidden">
      <!-- Header -->
      <div class="flex flex-col gap-2 mb-4">
        <h2 class="font-semibold text-lg leading-[18px] text-neutral-950 tracking-tight">
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
              <label class="font-medium text-sm leading-[14px] text-neutral-950 tracking-tight">
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
              <span class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight">
                {{ formData.category || 'Chọn danh mục...' }}
              </span>
              <ChevronDownIcon />
            </button>
            <div v-if="showCategoryDropdown" class="absolute top-[70px] left-0 w-full z-50">
              <div class="bg-white border border-gray-200 rounded-lg max-h-48 overflow-auto shadow">
                <button
                  v-for="c in categories" :key="c.id"
                  @click="selectCategory(c)"
                  class="w-full text-left px-3 py-2 hover:bg-gray-100 transition-colors text-sm"
                >{{ c.ten_nhom }}</button>
                <div v-if="loadingCats" class="p-3 text-xs text-gray-500">Đang tải...</div>
                <div v-if="!loadingCats && categories.length === 0" class="p-3 text-xs text-gray-500">Chưa có danh mục</div>
              </div>
            </div>
          </div>

          <!-- Service Name -->
          <div class="flex flex-col gap-2">
            <label class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight">Tên dịch vụ (*)</label>
            <input v-model="formData.name" type="text"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none" />
            <div v-if="errors.name" class="text-xs text-red-600">{{ errors.name }}</div>
          </div>

          <!-- Service Code -->
          <div class="flex flex-col gap-2">
            <label class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight">Mã Dịch Vụ (*)</label>
            <input v-model="formData.code" type="text"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none" />
            <div v-if="errors.code" class="text-xs text-red-600">{{ errors.code }}</div>
          </div>

          <!-- Price -->
          <div class="flex flex-col gap-2">
            <label class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight">Giá bán (VNĐ) (*)</label>
            <input v-model="formData.priceDisplay" type="text" inputmode="numeric"
              @input="onPriceInput"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none" />
            <div v-if="errors.price" class="text-xs text-red-600">{{ errors.price }}</div>
          </div>

          <!-- Duration -->
          <div class="flex flex-col gap-2">
            <label class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight">Thời gian (phút) (*)</label>
            <input v-model.number="formData.duration" type="number"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none" />
            <div v-if="errors.duration" class="text-xs text-red-600">{{ errors.duration }}</div>
            <p class="font-nunito text-xs leading-4 text-[#6a7282]">Quan trọng để xếp lịch</p>
          </div>
        </div>

        <!-- Right Column -->
        <div class="flex flex-col gap-4">
          <!-- Description -->
          <div class="flex flex-col gap-2">
            <label class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight">Mô tả</label>
            <textarea v-model="formData.mo_ta" rows="3"
              placeholder="Nhập mô tả chi tiết về dịch vụ..."
              class="bg-[#f3f3f5] border-none rounded-lg px-3 py-2 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] resize-none"></textarea>
          </div>

          <!-- Instructions -->
          <div class="flex flex-col gap-2">
            <label class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight">Hướng dẫn</label>
            <textarea v-model="formData.huong_dan" rows="3"
              placeholder="Nhập hướng dẫn hoặc lưu ý cho khách hàng..."
              class="bg-[#f3f3f5] border-none rounded-lg px-3 py-2 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] resize-none"></textarea>
          </div>

          <!-- Status -->
          <div class="bg-gray-50 rounded-[10px] px-3 py-3 flex items-center justify-between">
            <div class="flex flex-col">
              <label class="font-medium text-sm leading-[14px] text-neutral-950 tracking-tight">Trạng thái</label>
              <p class="text-xs leading-4 text-[#4a5565]">Cho phép KH đặt dịch vụ này</p>
            </div>
            <button type="button"
              class="relative w-8 h-5 rounded-full transition-colors flex-shrink-0"
              :class="formData.isActive ? 'bg-[#030213]' : 'bg-gray-300'"
              @click="formData.isActive = !formData.isActive"
            >
              <span class="absolute top-0.5 w-4 h-4 bg-white rounded-full transition-transform"
                :class="formData.isActive ? 'left-[15px]' : 'left-0.5'"></span>
            </button>
          </div>

          <!-- Image Upload -->
          <div class="flex flex-col gap-2">
            <label class="font-medium text-sm leading-[14px] text-neutral-950 tracking-tight">Ảnh đại diện</label>
            <div
              class="border-2 border-[#d1d5dc] rounded-[10px] h-40 relative overflow-hidden"
              :class="!imagePreview ? 'border-dashed flex flex-col items-center justify-center gap-2 cursor-pointer hover:border-gray-400 transition-colors' : 'border-solid'"
              @click="!imagePreview && triggerFileInput()"
            >
              <!-- Preview -->
              <img v-if="imagePreview" :src="imagePreview" alt="Service image" class="w-full h-full object-cover" />

              <!-- Placeholder -->
              <template v-if="!imagePreview">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-sm text-[#4a5565]">Click để chọn ảnh</p>
                <p class="text-xs text-[#99a1af]">PNG, JPG, GIF (Max 5MB)</p>
              </template>

              <!-- Delete button -->
              <button v-if="imagePreview"
                class="absolute top-[10px] right-[10px] bg-[#d4183d] rounded-lg w-9 h-8 flex items-center justify-center hover:bg-[#b01430] transition-colors"
                type="button" @click.stop="removeImage"
              >
                <TrashIcon class="w-4 h-4 text-white" />
              </button>

              <!-- Replace button -->
              <button v-if="imagePreview"
                class="absolute bottom-[10px] left-[10px] bg-[#009689] text-white rounded-md px-3 py-1 text-xs hover:bg-[#007d72] transition-colors"
                type="button" @click.stop="triggerFileInput"
              >Thay ảnh</button>
            </div>

            <input ref="fileInput" type="file"
              accept="image/png,image/jpeg,image/gif,image/webp"
              class="hidden" @change="handleFileUpload" />
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex gap-2 justify-end mt-4 pt-4 border-t border-gray-200">
        <button
          class="bg-white border !border-gray-300 rounded-lg h-9 px-[17px] py-[9px] flex items-center justify-center hover:bg-gray-50 transition-colors"
          @click="$emit('close')"
        >
          <span class="font-medium text-sm leading-5 text-neutral-950 tracking-tight">Hủy</span>
        </button>
        <button
          class="bg-[#5a9690] rounded-lg h-9 px-4 py-2 flex items-center justify-center hover:bg-[#007d72] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          @click="handleUpdate" :disabled="saving"
        >
          <span class="font-medium text-sm leading-5 text-white tracking-tight">
            {{ saving ? 'Đang cập nhật...' : 'Cập nhật' }}
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import api, { attachToken } from "@/utils/api";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import TrashIcon from "@/assets/svg/trash.svg";

const props = defineProps({
  service: { type: Object, required: true, default: () => ({}) },
});
const emit = defineEmits(["close", "update", "openCreateCategory"]);

// ─── State ────────────────────────────────────────────────────────────
const saving     = ref(false);
const fileInput  = ref(null);
const errors     = reactive({});
const imagePreview  = ref("");
const imageFile     = ref(null);
const imageRemoved  = ref(false);

const formData = reactive({
  category:     "",
  categoryId:   null,
  name:         "",
  code:         "",
  price:        null,
  priceDisplay: "",
  duration:     null,
  mo_ta:        "",
  huong_dan:    "",
  isActive:     true,
});

const categories           = ref([]);
const loadingCats          = ref(false);
const showCategoryDropdown = ref(false);

// ✅ formatVND phải khai báo TRƯỚC watch để tránh lỗi khi immediate:true
const formatVND = (value) => {
  if (value === null || value === undefined || value === "") return "";
  return new Intl.NumberFormat("vi-VN", {
    style: "currency", currency: "VND", maximumFractionDigits: 0,
  }).format(Number(value));
};

// ─── Sync props → formData (sau khi formatVND đã được khai báo) ───────
watch(
  () => props.service,
  (s) => {
    if (!s) return;
    formData.category   = s.category   ?? s.ten_nhom ?? "";
    formData.categoryId = s.danh_muc_id ?? s.categoryId ?? null;
    formData.name       = s.name  ?? s.ten         ?? "";
    formData.code       = s.code  ?? s.ma_dich_vu  ?? "";
    formData.price      = s.price ?? s.gia_tien    ?? null;
    formData.priceDisplay = formData.price !== null ? formatVND(formData.price) : "";
    formData.duration   = s.duration ?? s.thoi_gian_thuc_hien ?? null;
    formData.mo_ta      = s.mo_ta    ?? s.description ?? "";
    formData.huong_dan  = s.huong_dan ?? "";
    formData.isActive   = s.trang_thai === "kinh_doanh"
      || s.status === "active"
      || (typeof s.isActive === "boolean" ? s.isActive : true);

    const imgUrl = s.anh_dich_vu ?? s.image ?? "";
    imagePreview.value  = imgUrl;
    imageFile.value     = null;
    imageRemoved.value  = false;
  },
  { immediate: true, deep: true }
);

// ─── Category ─────────────────────────────────────────────────────────
const fetchCategories = async () => {
  loadingCats.value = true;
  try {
    const res   = await api.get("/danh-muc-dich-vu");
    const items = res?.data?.data || [];
    categories.value = items.map((i) => ({ id: i.id, ten_nhom: i.ten_nhom }));
  } catch {
    showErrorToast("Lỗi", "Không tải được danh mục.");
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
  formData.category   = c.ten_nhom;
  formData.categoryId = c.id;
  showCategoryDropdown.value = false;
};

const openCreateCategory = () => emit("openCreateCategory");

// ─── Image ────────────────────────────────────────────────────────────
const triggerFileInput = () => fileInput.value?.click();

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

  const reader = new FileReader();
  reader.onload = (e) => { imagePreview.value = e.target.result; };
  reader.readAsDataURL(file);

  imageFile.value    = file;
  imageRemoved.value = false;
};

const removeImage = () => {
  imagePreview.value = "";
  imageFile.value    = null;
  imageRemoved.value = true;
  if (fileInput.value) fileInput.value.value = "";
};

// ─── Price ────────────────────────────────────────────────────────────
const onPriceInput = (e) => {
  const digits = (e.target.value || "").replace(/[^0-9]/g, "");
  formData.price        = digits === "" ? null : parseInt(digits, 10);
  formData.priceDisplay = formData.price !== null ? formatVND(formData.price) : "";
};

// ─── Submit ───────────────────────────────────────────────────────────
const handleUpdate = async () => {
  Object.keys(errors).forEach((k) => delete errors[k]);

  if (!formData.name)     errors.name     = "Vui lòng nhập tên dịch vụ";
  if (!formData.code)     errors.code     = "Vui lòng nhập mã dịch vụ";
  if (formData.price === null || formData.price === undefined)
    errors.price = "Vui lòng nhập giá bán";
  if (!formData.duration) errors.duration = "Vui lòng nhập thời gian thực hiện";
  if (Object.keys(errors).length > 0) return;

  saving.value = true;
  try {
    try { attachToken(); } catch (_) {}

    const trangThai = formData.isActive ? "kinh_doanh" : "ngung";

    if (imageFile.value) {
      // Có ảnh mới → multipart
      const fd = new FormData();
      fd.append("_method",             "PUT");
      fd.append("anh_dich_vu_file",    imageFile.value);
      fd.append("ten",                 formData.name);
      fd.append("ma_dich_vu",          formData.code);
      fd.append("gia_tien",            formData.price);
      fd.append("thoi_gian_thuc_hien", formData.duration);
      fd.append("mo_ta",               formData.mo_ta     || "");
      fd.append("huong_dan",           formData.huong_dan || "");
      fd.append("trang_thai",          trangThai);
      if (formData.categoryId) fd.append("danh_muc_id", formData.categoryId);

      const res = await api.post(`/dich-vu/${props.service.id}`, fd, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      if (res?.data?.status) {
        showSuccessToast("Thành công", "Cập nhật dịch vụ thành công.");
        emit("update", res.data.data);
        emit("close");
      } else {
        showErrorToast("Lỗi", res?.data?.message || "Lỗi khi cập nhật.");
      }
    } else {
      // Không có ảnh mới → JSON
      const payload = {
        ten:                  formData.name,
        ma_dich_vu:           formData.code,
        gia_tien:             formData.price,
        thoi_gian_thuc_hien:  formData.duration,
        mo_ta:                formData.mo_ta     || null,
        huong_dan:            formData.huong_dan || null,
        trang_thai:           trangThai,
        danh_muc_id:          formData.categoryId || null,
        anh_dich_vu:          imageRemoved.value ? null : (imagePreview.value || null),
      };

      const res = await api.put(`/dich-vu/${props.service.id}`, payload);
      if (res?.data?.status) {
        showSuccessToast("Thành công", "Cập nhật dịch vụ thành công.");
        emit("update", res.data.data);
        emit("close");
      } else {
        showErrorToast("Lỗi", res?.data?.message || "Lỗi khi cập nhật.");
      }
    }
  } catch (e) {
    console.error("update error", e);
    if (e?.response?.status === 422) {
      const respErrors = e.response.data?.errors || {};
      Object.keys(respErrors).forEach((k) => {
        if (k === "ten")                      errors.name     = respErrors[k].join(" ");
        else if (k === "ma_dich_vu")          errors.code     = respErrors[k].join(" ");
        else if (k === "gia_tien")            errors.price    = respErrors[k].join(" ");
        else if (k === "thoi_gian_thuc_hien") errors.duration = respErrors[k].join(" ");
        else errors[k] = respErrors[k].join(" ");
      });
    } else {
      showErrorToast("Lỗi", e?.response?.data?.message || "Có lỗi khi cập nhật dịch vụ.");
    }
  } finally {
    saving.value = false;
  }
};

onMounted(() => fetchCategories());
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