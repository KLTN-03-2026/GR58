<template>
  <div
    class="bg-white flex flex-col pb-4 pt-6 px-6 rounded-[14px] shadow-lg w-full max-h-[90vh]"
  >
    <!-- Header -->
    <div
      class="flex items-center justify-between border-b !border-gray-300 pb-4 mb-4"
    >
      <div class="flex items-center gap-3">
        <!-- <div class="w-6 h-6 flex-shrink-0">
          <img
            :src="iconMedical"
            alt="Medical icon"
            class="w-full h-full object-contain"
          />
        </div> -->
        <h2 class="font-semibold text-xl text-black">Chỉ định cận lâm sàng</h2>
      </div>
      <button
        @click="$emit('close')"
        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
      >
        <CloseIcon />
      </button>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col gap-4 flex-1 overflow-y-auto">
      <!-- Search Bar -->
      <div class="flex gap-3">
        <button
          class="bg-white border !border-gray-300 rounded-lg px-4 py-2 flex items-center gap-2 hover:bg-gray-50 transition-colors"
        >
          <span class="font-medium text-sm text-gray-900"> Tất cả </span>
          <!-- <img :src="iconDropdown" alt="Dropdown" class="w-4 h-4" /> -->
          <span class="text-gray-500">▼</span>
        </button>
        <div
          class="flex-1 bg-gray-50 border !border-gray-300 rounded-lg overflow-hidden"
        >
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Tìm dịch vụ..."
            class="w-full h-10 px-4 py-2 font-normal text-sm text-gray-700 bg-transparent border-none outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset"
          />
        </div>
      </div>

      <!-- Service Item -->
      <div
        class="bg-yellow-50 border !border-[#ffdf20] rounded-lg p-4 flex flex-col gap-3"
      >
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-2">
            <p class="font-bold text-sm text-black">1. Chụp X-Quang Xương</p>
            <span
              class="bg-[#f0b100] rounded-md px-2 py-1 text-white font-medium text-xs"
            >
              Chờ TH
            </span>
          </div>
          <button
            @click="removeService(0)"
            class="w-6 h-6 flex items-center justify-center rounded hover:bg-yellow-100 transition-colors"
          >
            <!-- <img :src="iconDelete" alt="Delete" class="w-4 h-4" /> -->
            <span class="text-gray-500 text-lg">×</span>
          </button>
        </div>
        <input
          v-model="serviceNote"
          type="text"
          placeholder="Nhập vị trí/ghi chú (VD: Chân trái, nghi gãy...)"
          class="w-full h-10 px-3 py-2 font-normal text-sm text-gray-700 bg-white border !border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-yellow-400 transition-all"
        />
      </div>
    </div>

    <!-- Footer Actions -->
    <div
      class="flex items-center justify-end gap-3 border-t !border-gray-300 pt-4 mt-4"
    >
      <button
        @click="$emit('close')"
        class="bg-white border !border-gray-300 rounded-lg px-4 py-2 h-10 hover:bg-gray-50 transition-colors"
      >
        <span class="font-medium text-sm text-gray-900"> Đóng </span>
      </button>
      <button
        @click="saveOrder"
        class="bg-[#9810fa] rounded-lg px-4 py-2 h-10 flex items-center gap-2 hover:bg-[#8510d9] transition-colors"
      >
        <!-- <img :src="iconSave" alt="Save" class="w-4 h-4" /> -->
        <span class="font-medium text-sm text-white"> Lưu </span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
//Icon SVG
import CloseIcon from "@/assets/svg/close.svg";
// Icons from Figma
const iconMedical =
  "https://www.figma.com/api/mcp/asset/0d2876a2-36d4-4b0f-80d3-29be73c5a87d";
const iconClose =
  "https://www.figma.com/api/mcp/asset/f252da64-6184-4f1e-9ec3-346ae2105723";
const iconDropdown =
  "https://www.figma.com/api/mcp/asset/943f38e7-c1f6-484c-840b-5263e147016f";
const iconDelete =
  "https://www.figma.com/api/mcp/asset/8a0bb606-0c10-4fcc-ac6e-f4d306d81ea9";
const iconSave =
  "https://www.figma.com/api/mcp/asset/c9baa527-1c6c-4a47-b3b1-204154418876";

// Emits
defineEmits(["close", "submit", "save"]);

// State
const searchQuery = ref("");
const serviceNote = ref("chân phải");

// Methods
const removeService = (index) => {
  console.log("Remove service at index:", index);
  // TODO: Implement remove service logic
};

const saveOrder = () => {
  console.log("Submit order with note:", serviceNote.value);
  // TODO: Implement submit logic
};
</script>

<style scoped>
/* Ensure Nunito Sans font is loaded */
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}

.font-nunito {
  font-family: "Nunito Sans", sans-serif;
}
</style>
