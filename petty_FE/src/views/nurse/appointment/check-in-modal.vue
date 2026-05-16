<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50"
    @click.self="closeModal"
  >
    <div
      class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden"
    >
      <!-- Header -->
      <div :class="[
        'px-6 py-4 transition-colors duration-300',
        prepayRequired
          ? 'bg-gradient-to-r from-amber-500 to-orange-500'
          : 'bg-gradient-to-r from-[#009689] to-[#00786f]'
      ]">
        <div class="flex items-center justify-between">
          <h3 class="font-nunito font-semibold text-xl text-white">
            {{ prepayRequired ? 'Yêu cầu thanh toán' : 'Xác nhận Check-in' }}
          </h3>
          <button
            @click="closeModal"
            class="text-white hover:bg-white/20 rounded-lg p-1 transition-colors"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Prepay Required Warning State -->
      <div v-if="prepayRequired" class="p-6 space-y-5">
        <div class="flex flex-col items-center text-center py-4">
          <div class="w-16 h-16 rounded-full bg-amber-50 border-2 !border-amber-200 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <p class="font-nunito font-semibold text-lg text-gray-900 mb-2">
            Chưa thể check-in
          </p>
          <p class="font-nunito text-sm text-gray-600 leading-relaxed max-w-[280px]">
            Dịch vụ phẫu thuật yêu cầu thanh toán trước khi thực hiện. Vui lòng thu phí dịch vụ trước.
          </p>
        </div>

        <div class="bg-amber-50 border !border-amber-200 rounded-xl p-3">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <p class="font-nunito text-xs text-amber-800">
              Sau khi thanh toán, hệ thống sẽ tự động check-in lịch hẹn này.
            </p>
          </div>
        </div>

        <!-- Action buttons -->
        <div class="flex items-center gap-3 pt-2">
          <button
            @click="cancelPrepay"
            class="flex-1 px-4 py-2.5 rounded-xl font-nunito font-medium text-sm text-gray-700 bg-white border !border-gray-300 hover:bg-gray-50 transition-colors"
          >
            Huỷ
          </button>
          <button
            @click="handlePayFirst"
            class="flex-1 px-4 py-2.5 rounded-xl font-nunito font-semibold text-sm text-white bg-[#009689] hover:bg-[#007d72] transition-colors flex items-center justify-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Thu tiền ngay
          </button>
        </div>
      </div>

      <!-- Normal Check-in Content -->
      <template v-else>
        <!-- Content -->
        <div class="p-6 space-y-4">
          <!-- Appointment Info -->
          <div class="bg-blue-50 rounded-lg p-4 space-y-2">
            <div class="flex items-start gap-3">
              <svg
                class="w-5 h-5 text-[#009689] mt-0.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                />
              </svg>
              <div class="flex-1">
                <p class="font-nunito text-sm text-gray-600">Khách hàng</p>
                <p class="font-nunito font-semibold text-base text-gray-900">
                  {{ appointment?.khach_hang?.full_name || "N/A" }}
                </p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <svg
                class="w-5 h-5 text-[#009689] mt-0.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <div class="flex-1">
                <p class="font-nunito text-sm text-gray-600">Thú cưng</p>
                <p class="font-nunito font-semibold text-base text-gray-900">
                  {{ appointment?.thu_cung?.ten_thu_cung || "N/A" }}
                </p>
                <p v-if="appointment?.thu_cung?.loai_thu_cung || appointment?.thu_cung?.giong_thu_cung" class="font-nunito text-xs text-gray-500">
                  {{ [appointment?.thu_cung?.loai_thu_cung, appointment?.thu_cung?.giong_thu_cung].filter(Boolean).join(' - ') }}
                </p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <svg
                class="w-5 h-5 text-[#009689] mt-0.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
              </svg>
              <div class="flex-1">
                <p class="font-nunito text-sm text-gray-600">Dịch vụ</p>
                <p class="font-nunito font-semibold text-base text-gray-900">
                  {{ appointment?.dich_vus?.length ? appointment.dich_vus.map(d => d.ten).join(", ") : (appointment?.dich_vu?.ten_dich_vu || appointment?.dich_vu?.ten || "N/A") }}
                </p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <svg
                class="w-5 h-5 text-[#009689] mt-0.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <div class="flex-1">
                <p class="font-nunito text-sm text-gray-600">Thời gian hẹn</p>
                <p class="font-nunito font-semibold text-base text-gray-900">
                  {{ formatDateTime(appointment?.ngay_gio) }}
                </p>
              </div>
            </div>

            <!-- Bác sĩ phụ trách -->
            <div class="flex items-start gap-3 pt-4 border-t border-blue-200">
              <svg
                class="w-5 h-5 text-[#009689] mt-0.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <div class="flex-1">
                <p class="font-nunito text-sm text-gray-600 mb-2">Bác sĩ phụ trách</p>
                <select
                  v-model="selectedDoctor"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent font-nunito text-sm"
                  :disabled="loading"
                >
                  <option value="">-- Vui lòng chọn bác sĩ --</option>
                  <option v-for="bs in doctors" :key="bs.id" :value="String(bs.id)">
                    {{ bs.full_name }}
                    <span v-if="bs.chuc_danh"> - {{ bs.chuc_danh }}</span>
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Warning Message -->
          <div
            class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-start gap-3"
          >
            <svg
              class="w-5 h-5 text-amber-600 mt-0.5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
              />
            </svg>
            <div class="flex-1">
              <p class="font-nunito text-sm text-amber-800">
                Sau khi check-in, bệnh nhân sẽ vào hàng chờ khám. Thao tác này không thể hoàn tác.
              </p>
            </div>
          </div>

          <!-- Current Time Display -->
          <div class="bg-gray-50 rounded-lg p-4">
            <p class="font-nunito text-sm text-gray-600 mb-1">
              Thời gian check-in
            </p>
            <p class="font-nunito font-bold text-lg text-[#009689]">
              {{ currentTime }}
            </p>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
          <button
            @click="closeModal"
            :disabled="loading"
            class="px-4 py-2.5 rounded-lg font-nunito font-medium text-sm text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Hủy
          </button>
          <button
            @click="handleCheckIn"
            :disabled="loading"
            class="px-6 py-2.5 rounded-lg font-nunito font-medium text-sm text-white bg-[#009689] hover:bg-[#007d72] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          >
            <svg
              v-if="loading"
              class="animate-spin w-4 h-4"
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
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            <svg
              v-else
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 13l4 4L19 7"
              />
            </svg>
            <span>{{ loading ? "Đang xử lý..." : "Xác nhận Check-in" }}</span>
          </button>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onUnmounted } from "vue";
import { checkInAppointment } from "@/services/lichHenService";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
import api from "@/utils/api";

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  appointment: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(["close", "success", "pay-first"]);

const loading = ref(false);
const currentTime = ref("");
const prepayRequired = ref(false);
let timeInterval = null;

const doctors = ref([]);
const selectedDoctor = ref("");

const fetchDoctors = async () => {
  try {
    const res = await api.get('/bac-si/danh-sach');
    if (res.data?.status) {
      doctors.value = res.data.data || [];
    }
  } catch (e) {
    console.error("Failed to fetch doctors:", e);
  }
};

// Update current time every second
const updateCurrentTime = () => {
  const now = new Date();
  currentTime.value = now.toLocaleString("vi-VN", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    hour12: false,
  });
};

// Watch modal open/close
watch(
  () => props.isOpen,
  async (newVal) => {
    if (newVal) {
      prepayRequired.value = false;
      updateCurrentTime();
      timeInterval = setInterval(updateCurrentTime, 1000);
      await fetchDoctors();
      selectedDoctor.value = props.appointment?.nhan_vien_id
        ? String(props.appointment.nhan_vien_id)
        : "";
    } else {
      if (timeInterval) {
        clearInterval(timeInterval);
        timeInterval = null;
      }
    }
  }
);

// Watch appointment thay đổi để cập nhật lại selectedDoctor
watch(
  () => props.appointment,
  (newAppt) => {
    if (props.isOpen && newAppt) {
      selectedDoctor.value = newAppt.nhan_vien_id
        ? String(newAppt.nhan_vien_id)
        : "";
    }
  }
);

// Cleanup on unmount
onUnmounted(() => {
  if (timeInterval) {
    clearInterval(timeInterval);
  }
});

const formatDateTime = (datetime) => {
  if (!datetime) return "N/A";

  try {
    const date = new Date(datetime);
    return date.toLocaleString("vi-VN", {
      year: "numeric",
      month: "2-digit",
      day: "2-digit",
      hour: "2-digit",
      minute: "2-digit",
      hour12: false,
    });
  } catch (e) {
    return datetime;
  }
};

const closeModal = () => {
  if (!loading.value) {
    prepayRequired.value = false;
    emit("close");
  }
};

const cancelPrepay = () => {
  prepayRequired.value = false;
};

const handlePayFirst = () => {
  emit("pay-first", props.appointment);
  prepayRequired.value = false;
  emit("close");
};

const handleCheckIn = async () => {
  if (!props.appointment || !props.appointment.id) {
    showErrorToast("Không tìm thấy thông tin lịch hẹn");
    return;
  }

  loading.value = true;

  try {
    const payload = {};
    if (selectedDoctor.value) {
      payload.nhan_vien_id = selectedDoctor.value;
    }

    const response = await checkInAppointment(props.appointment.id, payload);

    if (response.status) {
      showSuccessToast(response.message || "Check-in thành công");
      emit("success", response.data);
      emit("close");
    } else {
      showErrorToast(response.message || "Check-in thất bại");
    }
  } catch (error) {
    console.error("Check-in error:", error);

    if (error.response?.data?.error_code === 'SURGERY_PREPAY_REQUIRED') {
      prepayRequired.value = true;
    } else {
      const errorMsg =
        error.response?.data?.message || "Có lỗi xảy ra khi check-in";
      showErrorToast(errorMsg);
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.bg-white {
  animation: slideDown 0.3s ease-out;
}
</style>
