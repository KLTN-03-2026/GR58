<template>
  <!-- Modal Overlay -->
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    @click.self="closeModal"
  >
    <!-- Modal Content -->
    <div class="bg-white flex flex-col gap-8 rounded-[10px] shadow-[0px_20px_25px_-5px_rgba(0,0,0,0.1),0px_8px_10px_-6px_rgba(0,0,0,0.1)] w-[800px] px-6 pt-6 pb-0">
      <!-- Dialog Header -->
      <div class="flex flex-col gap-2 w-full">
        <h2 class="font-normal text-lg leading-7 text-neutral-950 tracking-[-0.4395px]">
          Xác nhận đổi lượt khám
        </h2>
        <div class="flex gap-1 items-center flex-wrap">
          <p class="font-normal text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]">
            Bệnh nhân
          </p>
          <p class="font-bold text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]">
            {{ currentPatient }}
          </p>
          <p class="font-normal text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]">
            chưa check-in. Bạn có muốn đẩy {{ currentPatient }} xuống cuối hàng đợi và mời
          </p>
          <p class="font-bold text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]">
            {{ nextPatient }}
          </p>
          <p class="font-normal text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]">
            lên khám trước không?
          </p>
        </div>
      </div>

      <!-- Info Box -->
      <div class="bg-blue-50 border border-[#bedbff] rounded-[10px] px-[17px] py-[17px]">
        <div class="flex gap-1 items-center">
          <p class="font-bold text-sm leading-5 text-[#1c398e] tracking-[-0.1504px]">
            Lưu ý:
          </p>
          <p class="font-normal text-sm leading-5 text-[#1c398e] tracking-[-0.1504px]">
            {{ currentPatient }} sẽ được xếp xuống cuối danh sách chờ. Khi khách đến, bạn có thể dùng nút "Khám ngay" để ưu tiên.
          </p>
        </div>
      </div>

      <!-- Dialog Footer -->
      <div class="flex gap-2 h-9 items-center justify-end w-full">
        <button
          class="bg-white border border-[rgba(0,0,0,0.1)] rounded-lg h-9 px-[17px] py-[9px] hover:bg-gray-50 transition-colors"
          @click="closeModal"
        >
          <span class="font-medium text-sm leading-5 text-neutral-950 tracking-[-0.1504px]">
            Hủy
          </span>
        </button>
        <button
          class="bg-[#155dfc] rounded-lg h-9 px-4 py-2 hover:bg-blue-700 transition-colors"
          @click="confirmChange"
        >
          <span class="font-medium text-sm leading-5 text-white tracking-[-0.1504px]">
            Đồng ý
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  currentPatient: {
    type: String,
    default: 'Milo'
  },
  nextPatient: {
    type: String,
    default: 'Lu'
  }
});

// Emits
const emit = defineEmits(['close', 'confirm']);

// Methods
const closeModal = () => {
  emit('close');
};

const confirmChange = () => {
  emit('confirm', {
    currentPatient: props.currentPatient,
    nextPatient: props.nextPatient
  });
  closeModal();
};
</script>

<style scoped>
/* Modal animation can be added here if needed */
</style>
