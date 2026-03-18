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
            :src="iconCalendar"
            alt="Calendar icon"
            class="w-full h-full object-contain"
          />
        </div> -->
        <h2 class="font-semibold text-xl text-black">
          Kế hoạch điều trị & Tái khám
        </h2>
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
      <!-- Phân loại lịch nhắc -->
      <div class="flex flex-col gap-2">
        <div class="flex items-center gap-2">
          <!-- <img :src="iconBell" alt="Bell" class="w-4 h-4" /> -->
          <label class="font-medium text-sm text-gray-700">
            Phân loại lịch nhắc:
          </label>
        </div>
        <button
          class="bg-white border !border-gray-300 rounded-lg px-4 py-2.5 h-10 w-full flex items-center justify-between hover:bg-gray-50 transition-colors"
        >
          <span class="font-normal text-sm text-gray-900"> Tái khám </span>
          <span class="text-gray-500">▼</span>
        </button>
      </div>

      <!-- Ngày hẹn lại -->
      <div class="flex flex-col gap-3">
        <div class="flex items-center gap-2">
          <!-- <img :src="iconCalendarSmall" alt="Calendar" class="w-4 h-4" /> -->
          <label class="font-medium text-sm text-gray-700">
            Ngày hẹn lại:
          </label>
        </div>
        <div
          class="border !border-gray-300 rounded-lg px-6 py-2.5 flex items-center justify-center"
        >
          <input
            v-model="appointmentDate"
            type="text"
            placeholder="dd/mm/yyyy"
            class="font-normal text-sm text-gray-700 text-center bg-transparent border-none outline-none w-full focus:ring-0"
          />
        </div>
        <!-- Quick Date Buttons -->
        <div class="flex gap-2 flex-wrap">
          <button
            @click="addDays(3)"
            class="bg-white border !border-[#53eafd] rounded-full px-4 py-1.5 hover:bg-cyan-50 transition-colors"
          >
            <span class="font-normal text-xs text-gray-900"> +3 Ngày </span>
          </button>
          <button
            @click="addWeeks(1)"
            class="bg-white border !border-[#53eafd] rounded-full px-4 py-1.5 hover:bg-cyan-50 transition-colors"
          >
            <span class="font-normal text-xs text-gray-900"> +1 Tuần </span>
          </button>
          <button
            @click="addWeeks(2)"
            class="bg-white border !border-[#53eafd] rounded-full px-4 py-1.5 hover:bg-cyan-50 transition-colors"
          >
            <span class="font-normal text-xs text-gray-900"> +2 Tuần </span>
          </button>
          <button
            @click="addMonths(1)"
            class="bg-white border !border-[#53eafd] rounded-full px-4 py-1.5 hover:bg-cyan-50 transition-colors"
          >
            <span class="font-normal text-xs text-gray-900"> +1 Tháng </span>
          </button>
        </div>
      </div>

      <!-- Nội dung tái khám -->
      <div class="flex flex-col gap-2">
        <div class="flex items-center gap-2">
          <!-- <img :src="iconNote" alt="Note" class="w-4 h-4" /> -->
          <label class="font-medium text-sm text-gray-700">
            Nội dung tái khám:
          </label>
        </div>
        <input
          v-model="revisitContent"
          type="text"
          placeholder="Chọn hoặc nhập nội dung..."
          class="w-full h-10 px-3 py-2 bg-white border !border-gray-300 rounded-lg font-normal text-sm text-gray-700 outline-none focus:ring-2 focus:ring-cyan-400 transition-all"
        />
      </div>

      <!-- Ghi chú cho Lễ tân -->
      <div class="flex flex-col gap-2">
        <label class="font-medium text-sm text-gray-700">
          Ghi chú cho Lễ tân:
        </label>
        <textarea
          v-model="receptionNote"
          placeholder="VD: Nhớ dặn khách nhịn ăn sáng để xét nghiệm máu..."
          class="w-full h-20 px-3 py-2 bg-white border !border-gray-300 rounded-lg font-normal text-sm text-gray-700 outline-none focus:ring-2 focus:ring-cyan-400 transition-all resize-none"
        />
      </div>

      <!-- Checkbox: Thêm vào danh sách nhắc hẹn -->
      <div class="flex flex-col pt-3 border-t !border-gray-300">
        <label class="flex items-center gap-2 cursor-pointer">
          <input
            v-model="addToReminderList"
            type="checkbox"
            class="w-4 h-4 border-2 border-gray-300 rounded cursor-pointer accent-cyan-500"
          />
          <!-- <img :src="iconPhone" alt="Phone" class="w-4 h-4" /> -->
          <span class="font-normal text-sm text-gray-700">
            Thêm vào danh sách nhắc hẹn (Lễ tân gọi trước 1 ngày)
          </span>
        </label>
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
        @click="saveAppointment"
        class="bg-[#0092b8] rounded-lg px-4 py-2 h-10 flex items-center gap-2 hover:bg-[#007a9a] transition-colors"
      >
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
const iconCalendar =
  "https://www.figma.com/api/mcp/asset/d56517f1-8c6c-48e8-9228-c7c54f2151aa";
const iconClose =
  "https://www.figma.com/api/mcp/asset/9dafdc00-39e0-4ed7-8cd0-88e36969525b";
const iconBell =
  "https://www.figma.com/api/mcp/asset/453c5d60-c9d7-4b7e-b005-58aaca411b97";
const iconDropdown =
  "https://www.figma.com/api/mcp/asset/4d244ad1-01b7-4229-9aea-c8d88e7d1f08";
const iconCalendarSmall =
  "https://www.figma.com/api/mcp/asset/ddabfa1d-5891-4ee7-a039-dfb73e0de731";
const iconNote =
  "https://www.figma.com/api/mcp/asset/97231c4e-2b08-458e-9543-ee91ffc6bdc0";
const iconPhone =
  "https://www.figma.com/api/mcp/asset/d28f7113-41b8-4d51-9e33-9da27bb77b45";
const iconSave =
  "https://www.figma.com/api/mcp/asset/aa809ce0-78bc-4be4-9d60-45ffb8366d7c";

// Emits
defineEmits(["close", "save"]);

// State
const appointmentDate = ref("");
const revisitContent = ref("");
const receptionNote = ref("");
const addToReminderList = ref(false);

// Methods
const addDays = (days) => {
  const today = new Date();
  today.setDate(today.getDate() + days);
  appointmentDate.value = formatDate(today);
};

const addWeeks = (weeks) => {
  addDays(weeks * 7);
};

const addMonths = (months) => {
  const today = new Date();
  today.setMonth(today.getMonth() + months);
  appointmentDate.value = formatDate(today);
};

const formatDate = (date) => {
  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const year = date.getFullYear();
  return `${day}/${month}/${year}`;
};

const saveAppointment = () => {
  console.log("Save appointment:", {
    date: appointmentDate.value,
    content: revisitContent.value,
    note: receptionNote.value,
    reminder: addToReminderList.value,
  });
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
