<template>
  <!-- Modal Overlay -->
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <!-- Modal Card -->
    <div
      class="bg-white border !border-gray-300 rounded-[10px] shadow-[0px_10px_15px_-3px_rgba(0,0,0,0.1),0px_4px_6px_-4px_rgba(0,0,0,0.1)] w-[510px] relative"
    >
      <!-- Close Button -->
      <button
        @click="$emit('close')"
        class="absolute right-6 top-4 w-4 h-4 opacity-70 hover:opacity-100 transition-opacity"
      >
        <CloseIcon />
      </button>

      <!-- Dialog Header -->
      <div class="px-6 pt-6 pb-0">
        <h2
          class="font-nunito font-semibold text-lg leading-[18px] text-neutral-950 tracking-tight mb-2"
        >
          Xác nhận hủy lịch hẹn
        </h2>
        <p class="font-nunito text-sm leading-5 text-[#717182] tracking-tight">
          Bạn có chắc chắn muốn hủy lịch hẹn {{ appointmentId }} ?
        </p>
      </div>

      <!-- Warning Alert -->
      <div
        class="mx-6 mt-[16px] bg-yellow-50 border !border-yellow-200 rounded-[10px] px-4 py-3"
      >
        <p class="font-nunito text-sm leading-5 text-[#364153] tracking-tight">
          ⚠️ Hủy lịch hẹn sẽ ảnh hưởng đến lịch trình của bác sĩ và khách hàng.
          Vui lòng xác nhận trước khi thực hiện.
        </p>
      </div>

      <!-- Dialog Footer -->
      <div class="px-6 pt-4 pb-6 flex items-center justify-end gap-2">
        <button
          @click="$emit('close')"
          class="bg-white border !border-gray-300 rounded-lg h-9 px-4 py-2 font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight hover:bg-gray-50 transition-colors"
        >
          Không
        </button>
        <button
          @click="handleConfirm"
          class="bg-[#e7000b] rounded-lg h-9 px-4 py-2 font-nunito font-medium text-sm leading-5 text-white tracking-tight hover:bg-[#c50009] transition-colors"
        >
          Có
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
// Icons SVG
import CloseIcon from "@/assets/svg/close.svg";

// Props
const props = defineProps({
  appointmentId: {
    type: String,
    default: "LH001234",
  },
});

// Emits
const emit = defineEmits(["close", "confirm"]);

// Methods
const handleConfirm = () => {
  emit("confirm", props.appointmentId);
};
</script>

<style scoped>
/* No additional styles needed */
</style>
