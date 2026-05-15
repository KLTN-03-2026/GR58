<template>
  <div
    class="bg-white flex flex-col pb-4 pt-6 px-6 rounded-[14px] shadow-lg w-full max-h-[90vh]"
  >
    <!-- Header -->
    <div
      class="flex items-center justify-between border-b !border-gray-300 pb-4 mb-4"
    >
      <div class="flex items-center gap-3">
        <h2 class="font-semibold text-xl text-black">Hẹn Tái Khám</h2>
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
      <!-- Follow-up Card -->
      <div
        class="bg-cyan-50 border !border-cyan-200 rounded-lg p-4 flex flex-col gap-4"
      >
        <!-- Date Selection -->
        <div class="grid grid-cols-2 gap-3">
          <div class="flex flex-col gap-2">
            <label class="font-medium text-xs text-gray-700">
              Ngày tái khám *
            </label>
            <input
              v-model="followUp.date"
              type="date"
              :min="minDate"
              class="w-full h-10 px-3 py-2 bg-white border !border-gray-300 rounded-lg font-normal text-sm text-gray-900 outline-none focus:ring-2 focus:ring-cyan-400 transition-all"
            />
          </div>
          <div class="flex flex-col gap-2">
            <label class="font-medium text-xs text-gray-700">
              Thời gian *
            </label>
            <input
              v-model="followUp.time"
              type="time"
              class="w-full h-10 px-3 py-2 bg-white border !border-gray-300 rounded-lg font-normal text-sm text-gray-900 outline-none focus:ring-2 focus:ring-cyan-400 transition-all"
            />
          </div>
        </div>

        <!-- Quick Date Buttons -->
        <div class="flex flex-col gap-2">
          <label class="font-medium text-xs text-gray-700">
            Chọn nhanh
          </label>
          <div class="flex gap-2 flex-wrap">
            <button
              v-for="quick in quickDates"
              :key="quick.label"
              @click="setQuickDate(quick.days)"
              class="bg-white border !border-cyan-300 rounded-md px-3 py-1.5 hover:bg-cyan-50 transition-colors"
            >
              <span class="font-normal text-xs text-gray-900">
                {{ quick.label }}
              </span>
            </button>
          </div>
        </div>

        <!-- Reason -->
        <div class="flex flex-col gap-2">
          <label class="font-medium text-xs text-gray-700">
            Lý do tái khám
          </label>
          <textarea
            v-model="followUp.reason"
            placeholder="VD: Kiểm tra kết quả xét nghiệm, theo dõi tình trạng sau điều trị..."
            rows="3"
            class="w-full px-3 py-2 bg-white border !border-gray-300 rounded-lg font-normal text-sm text-gray-700 outline-none focus:ring-2 focus:ring-cyan-400 transition-all resize-none"
          />
        </div>

        <!-- Note -->
        <div class="flex flex-col gap-2">
          <label class="font-medium text-xs text-gray-700">
            Ghi chú thêm
          </label>
          <textarea
            v-model="followUp.note"
            placeholder="VD: Mang theo kết quả xét nghiệm, nhịn ăn trước khi đến..."
            rows="2"
            class="w-full px-3 py-2 bg-white border !border-gray-300 rounded-lg font-normal text-sm text-gray-700 outline-none focus:ring-2 focus:ring-cyan-400 transition-all resize-none"
          />
        </div>

        <!-- Reminder -->
        <div class="flex items-center gap-2">
          <input
            v-model="followUp.sendReminder"
            type="checkbox"
            id="reminder"
            class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-cyan-500"
          />
          <label for="reminder" class="text-sm text-gray-700">
            Gửi nhắc nhở cho khách hàng
          </label>
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
        @click="saveFollowUp"
        class="bg-cyan-600 rounded-lg px-4 py-2 h-10 flex items-center gap-2 hover:bg-cyan-700 transition-colors"
      >
        <span class="font-medium text-sm text-white"> Lưu lịch hẹn </span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
import CloseIcon from "@/assets/svg/close.svg";

const emit = defineEmits(["close", "save"]);

const followUp = ref({
  date: "",
  time: "09:00",
  reason: "",
  note: "",
  sendReminder: true,
});

const quickDates = [
  { label: "3 ngày sau", days: 3 },
  { label: "1 tuần sau", days: 7 },
  { label: "2 tuần sau", days: 14 },
  { label: "1 tháng sau", days: 30 },
];

const minDate = computed(() => {
  const today = new Date();
  return today.toISOString().split("T")[0];
});

const setQuickDate = (days) => {
  const date = new Date();
  date.setDate(date.getDate() + days);
  followUp.value.date = date.toISOString().split("T")[0];
};

const saveFollowUp = () => {
  if (!followUp.value.date) {
    showErrorToast("Lỗi", "Vui lòng chọn ngày tái khám");
    return;
  }

  if (!followUp.value.time) {
    showErrorToast("Lỗi", "Vui lòng chọn thời gian");
    return;
  }

  emit("save", followUp.value);
  showSuccessToast("Thành công", "Đã lưu lịch hẹn tái khám");
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}
</style>
