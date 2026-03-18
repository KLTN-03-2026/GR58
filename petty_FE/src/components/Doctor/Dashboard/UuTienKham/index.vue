<template>
  <!-- Modal Overlay -->
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    @click.self="closeModal"
  >
    <!-- Modal Content -->
    <div class="bg-white flex flex-col gap-8 rounded-[10px] shadow-[0px_20px_25px_-5px_rgba(0,0,0,0.1),0px_8px_10px_-6px_rgba(0,0,0,0.1)] w-[700px] px-6 pt-6 pb-0">
      <!-- Dialog Header -->
      <div class="flex flex-col gap-2 w-full">
        <h2 class="font-normal text-lg leading-7 text-neutral-950 tracking-[-0.4395px]">
          Ưu tiên khám ngay
        </h2>
        <div class="flex gap-1 items-center flex-wrap">
          <p class="font-normal text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]">
            Bạn có muốn đưa
          </p>
          <p class="font-bold text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]">
            {{ patientName }}
          </p>
          <p class="font-normal text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]">
            lên đầu hàng chờ để khám ngay không?
          </p>
        </div>
      </div>

      <!-- Success Info Box -->
      <div class="bg-green-50 border border-[#b9f8cf] rounded-[10px] px-[17px] py-[17px]">
        <div class="flex gap-1 items-center">
          <img :src="icons.checkCircle" alt="" class="w-4 h-4 flex-shrink-0" />
          <p class="font-bold text-sm leading-5 text-[#0d542b] tracking-[-0.1504px]">
            {{ patientName }}
          </p>
          <p class="font-normal text-sm leading-5 text-[#0d542b] tracking-[-0.1504px]">
            đã check-in và sẵn sàng vào khám. Bệnh nhân này sẽ được ưu tiên khám trước {{ currentPatient }}.
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
          class="bg-[#00a63e] rounded-lg h-9 px-4 py-2 hover:bg-green-700 transition-colors"
          @click="confirmPriority"
        >
          <span class="font-medium text-sm leading-5 text-white tracking-[-0.1504px]">
            Khám ngay
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

// Icon from Figma
const icons = {
  checkCircle: 'https://www.figma.com/api/mcp/asset/2cc37089-3786-4d3d-8e5e-735cbd6352e4'
};

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  patientName: {
    type: String,
    default: 'Lu'
  },
  currentPatient: {
    type: String,
    default: 'Milo'
  }
});

// Emits
const emit = defineEmits(['close', 'confirm']);

// Methods
const closeModal = () => {
  emit('close');
};

const confirmPriority = () => {
  emit('confirm', {
    patientName: props.patientName,
    currentPatient: props.currentPatient
  });
  closeModal();
};
</script>

<style scoped>
/* Modal animation can be added here if needed */
</style>
