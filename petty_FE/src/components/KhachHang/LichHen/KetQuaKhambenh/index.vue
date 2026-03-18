<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-[10px] border border-gray-200 w-full max-w-[512px] max-h-[90vh] overflow-y-auto">
      <div class="p-6 flex flex-col gap-4">
        <!-- Header -->
        <div class="flex items-center justify-center h-7 relative">
          <h2 class="font-bold text-lg text-black">Kết quả khám bệnh</h2>
          <button @click="closePopup" class="absolute right-0 w-7 h-7">
            <img :src="iconClose" alt="Close" class="w-full h-full" />
          </button>
        </div>

        <!-- Thông tin lịch khám -->
        <div class="bg-teal-50 rounded-[10px] p-4">
          <div class="grid grid-cols-3 gap-4">
            <div class="flex flex-col">
              <p class="text-base font-medium text-gray-500 mb-0.5">Ngày khám</p>
              <p class="text-base font-medium text-black">{{ result.date }}</p>
              <p class="text-base font-medium text-teal-600">{{ result.time }}</p>
            </div>
            <div class="flex flex-col">
              <p class="text-base font-medium text-gray-500 mb-0.5">Dịch vụ</p>
              <p class="text-base font-medium text-black">{{ result.serviceName }}</p>
            </div>
            <div class="flex flex-col">
              <p class="text-base font-medium text-gray-500 mb-0.5">Bác sĩ</p>
              <p class="text-base font-medium text-black">{{ result.doctorName }}</p>
              <p class="text-base font-medium text-gray-500">{{ result.doctorSpecialty }}</p>
            </div>
          </div>
        </div>

        <!-- Kết quả chẩn đoán -->
        <div class="flex flex-col gap-3">
          <div class="flex items-center gap-2">
            <img :src="iconDiagnosis" alt="Diagnosis" class="w-5 h-5" />
            <h4 class="text-base font-medium text-black">Kết quả chẩn đoán</h4>
          </div>
          <div class="bg-gray-200 rounded-[10px] p-4">
            <p class="text-base font-medium text-black leading-6">{{ result.diagnosis }}</p>
          </div>
        </div>

        <!-- Đơn thuốc -->
        <div class="flex flex-col gap-3">
          <div class="flex items-center gap-2">
            <img :src="iconPrescription" alt="Prescription" class="w-4 h-4" />
            <h4 class="text-base font-medium text-black">Đơn thuốc</h4>
          </div>
          <div class="flex flex-col gap-3">
            <div v-for="(medicine, index) in result.medicines" :key="index"
              class="bg-white border-l-4 border-teal-400 rounded-[10px] p-4 pl-5">
              <div class="grid grid-cols-3 gap-3">
                <div class="flex flex-col">
                  <p class="text-base font-medium text-gray-500 mb-0.5">Thuốc</p>
                  <p class="text-base font-medium text-black">{{ medicine.name }}</p>
                </div>
                <div class="flex flex-col">
                  <p class="text-base font-medium text-gray-500 mb-0.5">Liều dùng</p>
                  <p class="text-base font-medium text-black leading-6 whitespace-pre-wrap">{{ medicine.dosage }}</p>
                </div>
                <div class="flex flex-col">
                  <p class="text-base font-medium text-gray-500 mb-0.5">Hướng dẫn</p>
                  <p class="text-base font-medium text-black leading-6 whitespace-pre-wrap">{{ medicine.instruction }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Hướng dẫn sau khám -->
        <div class="flex flex-col gap-3">
          <div class="flex items-center gap-2">
            <img :src="iconGuideline" alt="Guideline" class="w-5 h-5" />
            <h4 class="text-base font-medium text-black">Hướng dẫn sau khám</h4>
          </div>
          <div class="bg-blue-50 border border-blue-200 rounded-[10px] px-7 py-3">
            <p class="text-base font-medium text-blue-900 leading-6 whitespace-pre-wrap">{{ result.postCareGuideline }}
            </p>
          </div>
        </div>

        <!-- Tập tin đính kèm -->
        <div class="flex flex-col gap-3">
          <div class="flex items-center gap-2">
            <img :src="iconAttachment" alt="Attachment" class="w-5 h-5" />
            <h4 class="text-base font-medium text-black">Tập tin đính kèm</h4>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div v-for="(file, index) in result.attachments" :key="index"
              class="bg-white rounded-[10px] px-4 py-0 flex items-center justify-between h-[88px]">
              <div class="flex items-center gap-3">
                <div :class="[
                  'rounded p-1',
                  file.type === 'image' ? 'bg-teal-100' : 'bg-red-50'
                ]">
                  <img :src="file.type === 'image' ? iconImage : iconPDF" alt="File icon" class="w-5 h-5" />
                </div>
                <div class="flex flex-col gap-1">
                  <p class="text-base font-medium text-black leading-6 whitespace-pre-wrap w-24">{{ file.name }}</p>
                  <p class="text-xs font-medium text-gray-500">{{ file.label }}</p>
                </div>
              </div>
              <button @click="downloadFile(file)"
                class="w-9 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                <img :src="iconDownload" alt="Download" class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  result: {
    type: Object,
    required: true,
  }
});

// Emits
const emit = defineEmits(['close', 'download']);

// Icons
const iconClose = 'https://www.figma.com/api/mcp/asset/4455c5c9-f11f-44a9-899a-ed860f6e5cee';
const iconDiagnosis = 'https://www.figma.com/api/mcp/asset/2d17862e-60fe-4b5c-9e38-052a253ddfd1';
const iconPrescription = 'https://www.figma.com/api/mcp/asset/3b72944e-354b-47cc-ae6f-4702726eb8f3';
const iconGuideline = 'https://www.figma.com/api/mcp/asset/b4fb9356-c123-4bee-b4ab-f98e89e040aa';
const iconAttachment = 'https://www.figma.com/api/mcp/asset/6f7b820a-ab56-489a-93e7-b189fdab72bd';
const iconDownload = 'https://www.figma.com/api/mcp/asset/bd85c46f-1fe3-4573-8a95-b760fe3fd635';
const iconImage = 'https://www.figma.com/api/mcp/asset/6f7b820a-ab56-489a-93e7-b189fdab72bd';
const iconPDF = 'https://www.figma.com/api/mcp/asset/0f6313d4-f69d-4af6-b089-65807640ab32';

// Methods
function closePopup() {
  emit('close');
}

function downloadFile(file) {
  emit('download', file);
  // Or handle download directly:
  // window.open(file.url, '_blank');
}
</script>

<style scoped>
/* Custom scrollbar for popup */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>