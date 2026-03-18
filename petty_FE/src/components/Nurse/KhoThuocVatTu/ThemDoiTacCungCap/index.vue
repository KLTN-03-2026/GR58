<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white border !border-gray-300 rounded-[10px] shadow-lg w-[510px] max-h-[90vh] overflow-y-auto relative"
    >
      <!-- Header -->
      <div class="flex flex-col gap-2 px-6 pt-6 pb-4">
        <h2 class="font-semibold text-lg text-black">Thêm đối tác cung cấp</h2>
        <p class="text-sm text-gray-500 tracking-tight">
          Thêm nhà cung cấp mới vào hệ thống
        </p>
      </div>

      <!-- Content -->
      <div class="flex flex-col gap-4 px-6 py-1">
        <!-- Company Information Section -->
        <div class="flex flex-col gap-3 pb-4 border-b !border-gray-300">
          <h3 class="text-sm text-[#364153] tracking-tight">
            Thông tin Công ty
          </h3>

          <div class="grid grid-cols-2 gap-4">
            <!-- Supplier Code -->
            <div class="flex flex-col gap-0">
              <label class="text-sm text-gray-500 tracking-tight mb-0">
                Mã NCC (Optional)
              </label>
              <input
                v-model="formData.code"
                type="text"
                placeholder="Tự động tạo"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
              />
            </div>

            <!-- Supplier Name -->
            <div class="flex flex-col gap-0">
              <label class="text-sm text-gray-500 tracking-tight mb-0">
                Tên nhà cung cấp (*)
              </label>
              <input
                v-model="formData.name"
                type="text"
                placeholder="VD: Công ty TNHH ABC"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
              />
            </div>
          </div>

          <!-- Address -->
          <div class="flex flex-col gap-0">
            <label class="text-sm text-gray-500 tracking-tight mb-0">
              Địa chỉ
            </label>
            <input
              v-model="formData.address"
              type="text"
              placeholder="123 Đường A, Quận B, TP.HCM"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
            />
          </div>

          <!-- Tax Code -->
          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-0">
              <label class="text-sm text-gray-500 tracking-tight mb-0">
                Mã số thuế (Optional)
              </label>
              <input
                v-model="formData.taxCode"
                type="text"
                placeholder="0123456789"
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
              <label class="text-sm text-gray-500 tracking-tight mb-0">
                Tên người liên hệ
              </label>
              <input
                v-model="formData.contactName"
                type="text"
                placeholder="VD: Nguyễn Văn A"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
              />
            </div>

            <!-- Phone -->
            <div class="flex flex-col gap-0">
              <label class="text-sm text-gray-500 tracking-tight mb-0">
                Số điện thoại (*)
              </label>
              <input
                v-model="formData.phone"
                type="tel"
                placeholder="0909123456"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
              />
            </div>
          </div>

          <!-- Email -->
          <div class="flex flex-col gap-0">
            <label class="text-sm text-gray-500 tracking-tight mb-0">
              Email
            </label>
            <input
              v-model="formData.email"
              type="email"
              placeholder="email@example.com"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182] mt-0"
            />
          </div>

          <!-- Notes -->
          <div class="flex flex-col gap-0">
            <label class="text-sm text-gray-500 tracking-tight mb-0">
              Ghi chú
            </label>
            <textarea
              v-model="formData.notes"
              placeholder="VD: Chuyên cung cấp vắc-xin, giao hàng thứ 3 hàng tuần..."
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
          class="bg-white border !border-gray-300 rounded-lg h-9 px-3 py-2 hover:bg-gray-50 transition-colors"
          :disabled="isSubmitting"
        >
          <span class="font-medium text-sm text-black">Hủy</span>
        </button>
        <button
          @click="handleSubmit"
          class="bg-[#5a9690] rounded-lg h-9 px-4 py-2 hover:bg-[#5a9690]/80 transition-colors"
          :disabled="!isFormValid || isSubmitting"
          :class="{
            'opacity-50 cursor-not-allowed': !isFormValid || isSubmitting,
          }"
        >
          <span class="font-medium text-sm text-white">
            {{ isSubmitting ? "Đang thêm..." : "Thêm" }}
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { createNhaCungCap } from "@/utils/nhaCungCap";
import { showSuccessToast, showErrorToast } from "@/utils/toast";

// Props
defineProps({
  // No props needed for now
});

// Emits
const emit = defineEmits(["close", "save"]);

// Icons from Figma (expire in 7 days)
const iconClose =
  "https://www.figma.com/api/mcp/asset/5a461a93-59b4-41f3-8860-d79136cb7347";

// Form Data
const formData = ref({
  code: "", // Sẽ tự động tạo từ backend
  name: "",
  address: "",
  taxCode: "",
  contactName: "",
  phone: "",
  email: "",
  notes: "",
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
      ma_nha_cung_cap: formData.value.code || undefined, // Backend sẽ tự tạo nếu không có
      ten_nha_cung_cap: formData.value.name,
      ten_nguoi_lien_he: formData.value.contactName,
      so_dien_thoai: formData.value.phone,
      dia_chi: formData.value.address,
      email: formData.value.email,
      ma_so_thue: formData.value.taxCode,
      mo_ta: formData.value.notes,
      trang_thai: "hoat_dong",
    };

    // Gọi API
    const response = await createNhaCungCap(payload);

    if (response.status) {
      showSuccessToast(response.message || "Thêm nhà cung cấp thành công");
      emit("save", response.data);
      emit("close");
    } else {
      showErrorToast(response.message || "Có lỗi xảy ra khi thêm nhà cung cấp");
    }
  } catch (error) {
    console.error("Error creating supplier:", error);

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
        error.response?.data?.message || "Có lỗi xảy ra khi thêm nhà cung cấp"
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
