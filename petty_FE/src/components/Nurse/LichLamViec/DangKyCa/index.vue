<template>
  <teleport to="body">
    <transition name="fade">
      <div
        v-if="isOpen"
        class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 backdrop-blur-sm"
      >
        <transition name="modal">
          <div
            class="bg-white rounded-2xl w-full max-w-md shadow-xl overflow-hidden"
          >
            <!-- Header -->
            <div class="bg-[#5a9690] px-6 py-6">
              <h2 class="text-2xl font-bold text-white">Đăng ký ca trực</h2>
            </div>

            <!-- Close Button -->
            <button
              type="button"
              @click="closeModal"
              class="absolute top-4 right-4 z-20 p-2 text-gray-500 hover:text-gray-700 rounded-full transition-all"
            >
              <svg
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2.5"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>

            <!-- Form -->
            <div class="px-6 py-6 space-y-5">
              <form @submit.prevent="submitRegistration" class="space-y-5">
                <!-- Ngày Giờ -->
                <div>
                  <label class="block text-sm font-bold text-gray-800 mb-2">
                    Ngày giờ <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="formData.ngay_gio"
                    type="datetime-local"
                    required
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none text-gray-900 font-semibold"
                  />
                </div>

                <!-- Ghi Chú -->
                <div>
                  <label class="block text-sm font-bold text-gray-800 mb-2">
                    Ghi chú
                  </label>
                  <textarea
                    v-model="formData.ghi_chu"
                    placeholder="Nhập thông tin ca trực..."
                    rows="3"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none text-gray-900 font-semibold resize-none"
                  ></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-2">
                  <button
                    type="button"
                    @click="closeModal"
                    class="flex-1 px-4 py-3 bg-gray-200 text-gray-900 font-bold rounded-lg hover:bg-gray-300 transition-all"
                  >
                    Hủy
                  </button>
                  <button
                    type="submit"
                    :disabled="isSubmitting"
                    :class="[
                      'flex-1 px-4 py-3 rounded-lg font-bold transition-all text-white',
                      isSubmitting
                        ? 'bg-gray-400 cursor-not-allowed'
                        : 'bg-[#5a9690] hover:bg-[#5a9690]/80',
                    ]"
                  >
                    {{ isSubmitting ? "Đang gửi..." : "Đăng ký" }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </transition>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { ref } from "vue";
import api from "@/utils/api";
import { showSuccessToast, showErrorToast } from "@/utils/toast";

defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["close", "success"]);

const formData = ref({
  ngay_gio: "",
  ghi_chu: "",
});

const isSubmitting = ref(false);

const closeModal = () => {
  formData.value = {
    ngay_gio: "",
    ghi_chu: "",
  };
  emit("close");
};

const submitRegistration = async () => {
  if (!formData.value.ngay_gio) {
    showErrorToast("Vui lòng nhập ngày giờ");
    return;
  }

  isSubmitting.value = true;
  try {
    const datetimeValue = formData.value.ngay_gio;
    const formattedDateTime = datetimeValue.replace("T", " ") + ":00";

    const payload = {
      ngay_gio: formattedDateTime,
      ghi_chu: formData.value.ghi_chu || null,
    };

    const response = await api.post("/lich-dang-ky/dang-ky-nhan-vien", payload);

    if (response.data.success) {
      showSuccessToast("✨ Đăng ký thành công!");
      emit("success", response.data.data);
      closeModal();
    } else {
      showErrorToast(response.data.message || "Đăng ký thất bại");
    }
  } catch (error) {
    console.error("Registration error:", error);
    showErrorToast(
      error.response?.data?.message || error.message || "Lỗi khi đăng ký"
    );
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-20px);
}
</style>
