<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white border !border-gray-300 rounded-[10px] shadow-lg w-[510px] max-h-[90vh] overflow-y-auto relative"
    >
      <!-- Close Button -->
      <button
        @click="$emit('close')"
        class="absolute right-6 top-4 w-4 h-4 opacity-70 hover:opacity-100 transition-opacity"
      >
        <CloseIcon />
      </button>

      <!-- Header -->
      <div class="flex flex-col gap-2 px-6 pt-6 pb-4">
        <h2
          class="font-nunito font-semibold text-lg leading-[18px] text-neutral-950 tracking-tight"
        >
          Sửa thông tin Nhà cung cấp
        </h2>
        <p class="font-nunito text-sm leading-5 text-[#717182] tracking-tight">
          Cập nhật thông tin nhà cung cấp
        </p>
      </div>

      <!-- Content -->
      <div class="flex flex-col gap-4 px-6 py-4">
        <!-- Company Information Section -->
        <div class="flex flex-col gap-3 pb-4 border-b border-gray-200/60">
          <h3
            class="font-nunito text-sm leading-5 text-[#364153] tracking-tight"
          >
            Thông tin Công ty
          </h3>

          <div class="grid grid-cols-2 gap-4">
            <!-- Supplier Code -->
            <div class="flex flex-col gap-0">
              <label
                class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight mb-0"
              >
                Mã NCC
              </label>
              <input
                v-model="formData.code"
                type="text"
                disabled
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-[#717182] tracking-tight outline-none opacity-50 mt-0"
              />
            </div>

            <!-- Supplier Name -->
            <div class="flex flex-col gap-0">
              <label
                class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight mb-0"
              >
                Tên nhà cung cấp (*)
              </label>
              <input
                v-model="formData.name"
                type="text"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
              />
            </div>
          </div>

          <!-- Address -->
          <div class="flex flex-col gap-0">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight mb-0"
            >
              Địa chỉ
            </label>
            <input
              v-model="formData.address"
              type="text"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
            />
          </div>

          <!-- Tax Code -->
          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-0">
              <label
                class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight mb-0"
              >
                Mã số thuế (Optional)
              </label>
              <input
                v-model="formData.taxCode"
                type="text"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
              />
            </div>
          </div>
        </div>

        <!-- Contact Information Section -->
        <div class="flex flex-col gap-3">
          <h3
            class="font-nunito text-sm leading-5 text-[#364153] tracking-tight"
          >
            Thông tin Người liên hệ (Sales)
          </h3>

          <div class="grid grid-cols-2 gap-4">
            <!-- Contact Name -->
            <div class="flex flex-col gap-0">
              <label
                class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight mb-0"
              >
                Tên người liên hệ
              </label>
              <input
                v-model="formData.contactName"
                type="text"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
              />
            </div>

            <!-- Phone -->
            <div class="flex flex-col gap-0">
              <label
                class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight mb-0"
              >
                Số điện thoại (*)
              </label>
              <input
                v-model="formData.phone"
                type="tel"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
              />
            </div>
          </div>

          <!-- Email -->
          <div class="flex flex-col gap-0">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight mb-0"
            >
              Email
            </label>
            <input
              v-model="formData.email"
              type="email"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
            />
          </div>

          <!-- Notes -->
          <div class="flex flex-col gap-0">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight mb-0"
            >
              Ghi chú
            </label>
            <textarea
              v-model="formData.notes"
              rows="3"
              class="bg-[#f3f3f5] border-none rounded-lg px-3 py-2 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] resize-none mt-0"
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div
        class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-200/60"
      >
        <button
          @click="$emit('close')"
          class="bg-white border border-gray-200/60 rounded-lg h-9 px-[17px] py-[9px] hover:bg-gray-50 transition-colors"
        >
          <span
            class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
            >Hủy</span
          >
        </button>
        <button
          @click="handleSubmit"
          class="bg-[#5a9690] rounded-lg h-9 px-4 py-2 hover:bg-[#007d72] transition-colors"
          :disabled="!isFormValid || isSubmitting"
          :class="{
            'opacity-50 cursor-not-allowed': !isFormValid || isSubmitting,
          }"
        >
          <span class="font-nunito font-medium text-sm leading-5 text-white">
            {{ isSubmitting ? "Đang cập nhật..." : "Cập nhật" }}
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { updateNhaCungCap } from "@/utils/nhaCungCap";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
//Icon SVG
import CloseIcon from "@/assets/svg/close.svg";
// Props
const props = defineProps({
  supplier: {
    type: Object,
    required: true,
  },
});

// Emits
const emit = defineEmits(["close", "save"]);

// Form Data - pre-populated with supplier data
const formData = ref({
  code: props.supplier.code || props.supplier._original?.ma_nha_cung_cap,
  name: props.supplier.name || props.supplier._original?.ten_nha_cung_cap,
  address: props.supplier.address || props.supplier._original?.dia_chi,
  taxCode: props.supplier.taxCode || props.supplier._original?.ma_so_thue || "",
  contactName:
    props.supplier.contact || props.supplier._original?.ten_nguoi_lien_he,
  phone: props.supplier.phone || props.supplier._original?.so_dien_thoai,
  email: props.supplier.email || props.supplier._original?.email || "",
  notes: props.supplier.notes || props.supplier._original?.mo_ta || "",
});

// Loading state
const isSubmitting = ref(false);

// Computed
const isFormValid = computed(() => {
  return formData.value.name && formData.value.phone;
});

// Methods
const handleSubmit = async () => {
  if (!isFormValid.value || isSubmitting.value) return;

  try {
    isSubmitting.value = true;

    // Chuẩn bị dữ liệu gửi lên backend
    const payload = {
      ma_nha_cung_cap: formData.value.code,
      ten_nha_cung_cap: formData.value.name,
      ten_nguoi_lien_he: formData.value.contactName,
      so_dien_thoai: formData.value.phone,
      dia_chi: formData.value.address,
      email: formData.value.email,
      ma_so_thue: formData.value.taxCode,
      mo_ta: formData.value.notes,
    };

    // Gọi API
    const response = await updateNhaCungCap(props.supplier.id, payload);

    if (response.status) {
      showSuccessToast(response.message || "Cập nhật nhà cung cấp thành công");
      emit("save", response.data);
      emit("close");
    } else {
      showErrorToast(
        response.message || "Có lỗi xảy ra khi cập nhật nhà cung cấp"
      );
    }
  } catch (error) {
    console.error("Error updating supplier:", error);

    // Xử lý lỗi validation từ backend
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      if (errors) {
        const firstError = Object.values(errors)[0];
        showErrorToast(firstError[0] || "Dữ liệu không hợp lệ");
      } else {
        showErrorToast(error.response.data.message || "Dữ liệu không hợp lệ");
      }
    } else {
      showErrorToast(
        error.response?.data?.message ||
          "Có lỗi xảy ra khi cập nhật nhà cung cấp"
      );
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
/* Custom scrollbar for modal */
div::-webkit-scrollbar {
  width: 8px;
}

div::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

div::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

div::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
