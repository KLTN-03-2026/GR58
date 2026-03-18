<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white rounded-[10px] border !border-gray-300 p-6 w-full max-w-[512px] flex flex-col gap-4"
    >
      <!-- Header -->
      <div class="flex flex-col gap-2">
        <div class="flex items-center justify-between h-7 relative">
          <h2 class="font-bold text-lg text-black">Xác nhận hủy lịch hẹn</h2>
          <button @click="closePopup" class="absolute right-0 w-7 h-7">
            <CloseIcon />
          </button>
        </div>
        <p class="text-sm font-medium text-gray-500">
          Bạn có chắc chắn muốn hủy lịch hẹn {{ appointment.serviceName }} cho
          {{ appointment.petName }} không?
        </p>
      </div>

      <!-- Main Content -->
      <div class="flex flex-col gap-6">
        <!-- Alert Box - Hiển thị theo trạng thái -->
        <div
          :class="[
            'flex items-center gap-4 px-6 py-2 rounded-[10px] border',
            alertClass,
          ]"
        >
          <InforIcon class="w-6 h-6" :class="alertTextClass" />
          <p class="text-sm font-semibold" :class="alertTextClass">
            {{ alertMessage }}
          </p>
        </div>

        <!-- Lý do hủy -->
      </div>

      <!-- Footer -->
      <div class="flex gap-2">
        <button
          @click="keepAppointment"
          class="flex-1 px-4 py-2 bg-[#5a9690] text-white text-sm font-semibold rounded-lg hover:bg-[#5a9690]/80"
        >
          Giữ lại lịch hẹn
        </button>
        <button
          @click="confirmCancel"
          class="flex-1 px-4 py-2 bg-white border border-red-200 text-red-600 text-sm font-semibold rounded-lg hover:bg-red-50"
        >
          Xác nhận Hủy
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
//Icon SVG
import CloseIcon from "@/assets/svg/close.svg";
import InforIcon from "@/assets/svg/infor.svg";

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  appointment: {
    type: Object,
    required: true,
    // Format: { petName, serviceName, dateTime, status, paidAmount }
  },
  cancelStatus: {
    type: String,
    default: "late", // 'late' | 'refundable' | 'unpaid'
    validator: (value) => ["late", "refundable", "unpaid"].includes(value),
  },
});

// Emits
const emit = defineEmits(["close", "keep", "cancel"]);

// State
const selectedReason = ref(null);

// Cancel reasons
const cancelReasons = [
  { value: "busy", label: "Bận đột xuất" },
  { value: "found_other", label: "Tìm được chỗ khác" },
  { value: "other", label: "Khác" },
];

// Computed properties for alert styling
const alertClass = computed(() => {
  switch (props.cancelStatus) {
    case "late":
      return "bg-red-50 !border-red-200";
    case "refundable":
      return "bg-blue-50 !border-blue-300";
    case "unpaid":
      return "bg-gray-50 !border-gray-200";
    default:
      return "bg-gray-50 !border-gray-200";
  }
});

const alertTextClass = computed(() => {
  switch (props.cancelStatus) {
    case "late":
      return "text-red-900";
    case "refundable":
      return "text-blue-800";
    case "unpaid":
      return "text-gray-700";
    default:
      return "text-gray-700";
  }
});

const alertIcon = computed(() => {
  switch (props.cancelStatus) {
    case "late":
      return iconAlertRed;
    case "refundable":
      return iconAlertBlue;
    case "unpaid":
      return iconAlertGray;
    default:
      return iconAlertGray;
  }
});

const alertMessage = computed(() => {
  switch (props.cancelStatus) {
    case "late":
      return "Do bạn hủy sát giờ hẹn (quy định < 24h), số tiền trả trước sẽ không được hoàn lại.";
    case "refundable":
      return `Số tiền ${
        props.appointment.paidAmount || "180.000"
      }đ đã thanh toán sẽ được hoàn về ví của bạn trong 24h.`;
    case "unpaid":
      return "Bạn chưa thanh toán cho lịch hẹn này.";
    default:
      return "Bạn chưa thanh toán cho lịch hẹn này.";
  }
});

// Methods
function selectReason(reason) {
  selectedReason.value = reason;
}

function closePopup() {
  emit("close");
  resetForm();
}

function keepAppointment() {
  emit("keep");
  resetForm();
}

function confirmCancel() {
  const cancelData = {
    appointmentId: props.appointment.id,
    reason: selectedReason.value,
    cancelStatus: props.cancelStatus,
  };

  emit("cancel", cancelData);
  resetForm();
}

function resetForm() {
  selectedReason.value = null;
}
</script>

<style scoped>
/* Custom radio button styling */
label {
  user-select: none;
}
</style>