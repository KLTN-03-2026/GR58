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
            :src="iconPrescription"
            alt="Prescription icon"
            class="w-full h-full object-contain"
          />
        </div> -->
        <h2 class="font-semibold text-xl text-black">Đơn thuốc</h2>
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
          <span class="text-gray-500">▼</span>
        </button>
        <div
          class="flex-1 bg-gray-50 border !border-gray-300 rounded-lg overflow-hidden"
        >
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Tìm tên thuốc..."
            class="w-full h-10 px-4 py-2 font-normal text-sm text-gray-700 bg-transparent border-none outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset"
          />
        </div>
      </div>

      <!-- Medicine Item Card -->
      <div
        class="bg-green-50 border !border-[#b9f8cf] rounded-lg p-4 flex flex-col gap-4"
      >
        <!-- Medicine Header -->
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-2">
            <p class="font-bold text-sm text-black">1. Augmentin 500mg</p>
            <p class="font-normal text-xs text-[#00a63e]">(Tồn: 20)</p>
          </div>
          <button
            @click="removeMedicine(0)"
            class="w-6 h-6 flex items-center justify-center rounded hover:bg-green-100 transition-colors"
          >
            <span class="text-gray-500 text-lg">×</span>
          </button>
        </div>

        <!-- Form Fields -->
        <div class="grid grid-cols-2 gap-3">
          <!-- Số lượng -->
          <div class="flex flex-col gap-2">
            <label class="font-medium text-xs text-gray-700"> Số lượng </label>
            <input
              v-model="medicine.quantity"
              type="text"
              class="w-full h-10 px-3 py-2 bg-white border !border-gray-300 rounded-lg font-normal text-sm text-gray-900 outline-none focus:ring-2 focus:ring-green-400 transition-all"
            />
          </div>
          <!-- Đơn vị -->
          <div class="flex flex-col gap-2">
            <label class="font-medium text-xs text-gray-700"> Đơn vị </label>
            <input
              v-model="medicine.unit"
              type="text"
              class="w-full h-10 px-3 py-2 bg-white border !border-gray-300 rounded-lg font-normal text-sm text-gray-900 outline-none focus:ring-2 focus:ring-green-400 transition-all"
            />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <!-- Liều sử dụng -->
          <div class="flex flex-col gap-2">
            <label class="font-medium text-xs text-gray-700">
              Liều sử dụng
            </label>
            <input
              v-model="medicine.dosage"
              type="text"
              placeholder="VD: 1 viên/lần"
              class="w-full h-10 px-3 py-2 bg-white border !border-gray-300 rounded-lg font-normal text-sm text-gray-700 outline-none focus:ring-2 focus:ring-green-400 transition-all"
            />
          </div>
          <!-- Tần suất -->
          <div class="flex flex-col gap-2">
            <label class="font-medium text-xs text-gray-700"> Tần suất </label>
            <input
              v-model="medicine.frequency"
              type="text"
              placeholder="VD: 2 lần/ngày"
              class="w-full h-10 px-3 py-2 bg-white border !border-gray-300 rounded-lg font-normal text-sm text-gray-700 outline-none focus:ring-2 focus:ring-green-400 transition-all"
            />
          </div>
        </div>

        <!-- Thời gian dùng -->
        <div class="flex flex-col gap-2">
          <label class="font-medium text-xs text-gray-700">
            Thời gian dùng
          </label>
          <input
            v-model="medicine.duration"
            type="text"
            placeholder="VD: 5 ngày, 1 tuần..."
            class="w-full h-10 px-3 py-2 bg-white border !border-gray-300 rounded-lg font-normal text-sm text-gray-700 outline-none focus:ring-2 focus:ring-green-400 transition-all"
          />
        </div>

        <!-- Ghi chú / Cách dùng -->
        <div class="flex flex-col gap-2">
          <label class="font-medium text-xs text-gray-700">
            Ghi chú / Cách dùng
          </label>
          <input
            v-model="medicine.note"
            type="text"
            placeholder="VD: Sáng 1 viên, Tối 1 viên, uống sau ăn..."
            class="w-full h-10 px-3 py-2 bg-white border !border-gray-300 rounded-lg font-normal text-sm text-gray-700 outline-none focus:ring-2 focus:ring-green-400 transition-all"
          />
          <!-- Quick Buttons -->
          <div class="flex gap-2 mt-1">
            <button
              @click="addQuickNote('Sáng-Chiều')"
              class="bg-white border !border-[#7bf1a8] rounded-md px-3 py-1.5 hover:bg-green-50 transition-colors"
            >
              <span class="font-normal text-xs text-gray-900">
                Sáng-Chiều
              </span>
            </button>
            <button
              @click="addQuickNote('Ăn xong')"
              class="bg-white border !border-[#7bf1a8] rounded-md px-3 py-1.5 hover:bg-green-50 transition-colors"
            >
              <span class="font-normal text-xs text-gray-900"> Ăn xong </span>
            </button>
            <button
              @click="addQuickNote('Uống')"
              class="bg-white border !border-[#7bf1a8] rounded-md px-3 py-1.5 hover:bg-green-50 transition-colors"
            >
              <span class="font-normal text-xs text-gray-900"> Uống </span>
            </button>
          </div>
        </div>
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
        <span class="font-medium text-sm text-gray-900"> Hủy </span>
      </button>
      <button
        @click="savePrescription"
        class="bg-[#00a63e] rounded-lg px-4 py-2 h-10 flex items-center gap-2 hover:bg-[#009235] transition-colors"
      >
        <span class="font-medium text-sm text-white"> Lưu đơn thuốc </span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
//Icon SVG
import CloseIcon from "@/assets/svg/close.svg";

// Icons from Figma
const iconPrescription =
  "https://www.figma.com/api/mcp/asset/070b7955-1183-4e99-952e-175a8e234def";
const iconClose =
  "https://www.figma.com/api/mcp/asset/f8ef81cf-e862-41ee-83fb-c93822b94adf";
const iconDropdown =
  "https://www.figma.com/api/mcp/asset/f1366ed3-34fa-487f-b380-5714d8f764d9";
const iconDelete =
  "https://www.figma.com/api/mcp/asset/057ab505-4096-444e-9013-041c4f48096a";
const iconSave =
  "https://www.figma.com/api/mcp/asset/00ef23cf-9674-483a-aa44-fa6c196e8ab4";

// Emits
defineEmits(["close", "save"]);

// State
const searchQuery = ref("");
const medicine = ref({
  name: "Augmentin 500mg",
  stock: 20,
  quantity: "1",
  unit: "Vỉ",
  dosage: "",
  frequency: "",
  duration: "",
  note: "",
});

// Methods
const removeMedicine = (index) => {
  console.log("Remove medicine at index:", index);
  // TODO: Implement remove medicine logic
};

const addQuickNote = (text) => {
  if (medicine.value.note) {
    medicine.value.note += ", " + text;
  } else {
    medicine.value.note = text;
  }
};

const savePrescription = () => {
  console.log("Save prescription:", medicine.value);
  // TODO: Implement save logic
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
