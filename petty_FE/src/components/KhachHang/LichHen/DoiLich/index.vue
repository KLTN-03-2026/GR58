<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
  >
    <div
      class="bg-white rounded-[10px] border !border-black/15 w-full max-w-[512px] max-h-[90vh] flex flex-col"
    >
      <!-- Fixed Header -->
      <div class="flex-shrink-0 p-6 pb-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h2 class="font-bold text-lg text-black">Đổi lịch hẹn</h2>
          <button @click="closePopup" class="w-7 h-7">
            <CloseIcon />
          </button>
        </div>
      </div>

      <!-- Scrollable Content -->
      <div class="flex-1 overflow-y-auto px-6 py-4 flex flex-col gap-4">
        <!-- Thông tin lịch cũ -->
        <div class="bg-gray-100 rounded-[10px] p-4">
          <div class="flex gap-4">
            <div class="flex-1">
              <p class="text-sm font-semibold text-gray-500 mb-1">Thú cưng</p>
              <p class="text-base font-semibold text-black">
                {{ oldAppointment.petName }}
              </p>
            </div>
            <div class="flex-1">
              <p class="text-sm font-semibold text-gray-500 mb-1">Dịch vụ</p>
              <p class="text-base font-semibold text-black">
                {{ oldAppointment.serviceName }}
              </p>
            </div>
          </div>
        </div>

        <!-- Chọn ngày khám -->
        <div class="flex flex-col gap-2">
          <label class="text-sm font-semibold text-black">Chọn ngày khám</label>
          <div class="border !border-gray-300 rounded-lg p-3">
            <!-- Calendar Header -->
            <div class="flex items-center justify-between mb-3">
              <button
                @click="previousMonth"
                :disabled="!canGoPrevious"
                :class="[
                  'w-7 h-7 border border-gray-300 rounded-lg flex items-center justify-center',
                  !canGoPrevious && 'opacity-50',
                ]"
              >
                <ChevronLeftIcon />
              </button>
              <p class="text-sm font-semibold text-black">
                {{ currentMonthYear }}
              </p>
              <button
                @click="nextMonth"
                :disabled="!canGoNext"
                :class="[
                  'w-7 h-7 border !border-gray-300 rounded-lg flex items-center justify-center',
                  !canGoNext && 'opacity-50',
                ]"
              >
                <ChevronRightIcon />
              </button>
            </div>

            <!-- Week Days -->
            <div class="grid grid-cols-7 gap-0 mb-2">
              <div
                v-for="day in weekDays"
                :key="day"
                class="text-center text-sm font-medium text-gray-500 h-5"
              >
                {{ day }}
              </div>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-0">
              <button
                v-for="date in calendarDates"
                :key="date.key"
                @click="selectDate(date)"
                :disabled="date.isDisabled"
                :class="[
                  'w-8 h-8 rounded-lg text-sm font-medium flex items-center justify-center',
                  date.isSelected
                    ? 'bg-black text-white'
                    : date.isOtherMonth
                    ? 'text-gray-500 opacity-50'
                    : 'text-black',
                  !date.isSelected && date.isPastOldDate
                    ? 'bg-gray-300 opacity-50'
                    : '',
                  date.isDisabled ? 'cursor-not-allowed opacity-50' : '',
                  !date.isSelected && !date.isDisabled
                    ? 'hover:bg-gray-100'
                    : '',
                ]"
              >
                {{ date.date }}
              </button>
            </div>
          </div>
        </div>

        <!-- Chọn giờ khám -->
        <div class="flex flex-col gap-2">
          <label class="text-sm font-semibold text-black">Chọn giờ khám</label>
          <div class="grid grid-cols-4 gap-2">
            <button
              v-for="slot in timeSlots"
              :key="slot.time"
              @click="selectTime(slot)"
              :disabled="slot.isBooked"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-semibold border',
                slot.isBooked
                  ? '!border-gray-300 text-black opacity-50 cursor-not-allowed'
                  : '',
                slot.time === selectedTime
                  ? 'bg-[#487874] text-white border-none'
                  : 'border-gray-300 text-black hover:bg-gray-50',
              ]"
            >
              {{ slot.time }}
            </button>
          </div>
          <p class="text-sm font-medium text-gray-500">
            * Các khung giờ bị mờ đã kín lịch
          </p>
        </div>

        <!-- Alert box - So sánh lịch cũ và mới -->
        <div
          v-if="selectedDate && selectedTime"
          class="bg-blue-50 border !border-blue-200 rounded-[10px] p-3 flex items-center gap-2"
        >
          <InfoIcon class="w-4 h-4 text-blue-900 flex-shrink-0" />
          <div class="flex items-center gap-2 text-sm flex-wrap">
            <div class="flex items-center gap-3">
              <span class="font-bold text-blue-900">Cũ:</span>
              <span class="font-semibold text-blue-900">{{
                oldAppointment.dateTime
              }}</span>
            </div>
            <ArrowRightIcon class="w-4 h-4 text-blue-900 flex-shrink-0" />
            <div class="flex items-center gap-3">
              <span class="font-bold text-blue-900">Mới:</span>
              <span class="font-semibold text-blue-900">{{ newDateTime }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Fixed Footer -->
      <div class="flex-shrink-0 p-6 pt-4 border-t border-gray-200">
        <div class="flex justify-end">
          <button
            @click="saveChanges"
            :disabled="!canSave"
            :class="[
              'px-4 py-2 rounded-lg text-sm font-semibold text-white',
              canSave
                ? 'bg-[#5a9690] hover:bg-[#5a9690]/80'
                : 'bg-gray-400 cursor-not-allowed',
            ]"
          >
            Lưu thay đổi
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import CloseIcon from "@/assets/svg/close.svg";
import ChevronLeftIcon from "@/assets/svg/chevron-left.svg";
import ChevronRightIcon from "@/assets/svg/chevron-right.svg";
import InfoIcon from "@/assets/svg/infor.svg";
import ArrowRightIcon from "@/assets/svg/arrow-right.svg";

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  oldAppointment: {
    type: Object,
    required: true,
    // Format: { petName, serviceName, dateTime, date, time }
  },
});

// Emits
const emit = defineEmits(["close", "save"]);

// State
const currentDate = ref(new Date());
const selectedDate = ref(null);
const selectedTime = ref(null);

// Week days
const weekDays = ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"];

// Time slots
const timeSlots = ref([
  { time: "08:00", isBooked: false },
  { time: "08:30", isBooked: false },
  { time: "09:00", isBooked: true },
  { time: "09:30", isBooked: false },
  { time: "10:00", isBooked: false },
  { time: "10:30", isBooked: true },
  { time: "11:00", isBooked: false },
  { time: "11:30", isBooked: false },
  { time: "13:00", isBooked: false },
  { time: "13:30", isBooked: false },
  { time: "14:00", isBooked: true },
  { time: "14:30", isBooked: false },
  { time: "15:00", isBooked: false },
  { time: "15:30", isBooked: false },
  { time: "16:00", isBooked: false },
  { time: "16:30", isBooked: false },
  { time: "17:00", isBooked: false },
]);

// Computed
const currentMonthYear = computed(() => {
  const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];
  return `${
    months[currentDate.value.getMonth()]
  } ${currentDate.value.getFullYear()}`;
});

const calendarDates = computed(() => {
  const year = currentDate.value.getFullYear();
  const month = currentDate.value.getMonth();

  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);
  const prevLastDay = new Date(year, month, 0);

  const firstDayWeek = firstDay.getDay();
  const lastDate = lastDay.getDate();
  const prevLastDate = prevLastDay.getDate();

  const dates = [];

  // Previous month dates
  for (let i = firstDayWeek - 1; i >= 0; i--) {
    const date = prevLastDate - i;
    dates.push({
      date,
      month: month - 1,
      year: month === 0 ? year - 1 : year,
      isOtherMonth: true,
      isDisabled: true,
      isPastOldDate: false,
      isSelected: false,
      key: `prev-${date}`,
    });
  }

  // Current month dates
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  // Parse old appointment date
  const oldDate = parseOldAppointmentDate();

  for (let date = 1; date <= lastDate; date++) {
    const currentDateObj = new Date(year, month, date);
    currentDateObj.setHours(0, 0, 0, 0);

    const isPast = currentDateObj < today;
    const isPastOldDate = oldDate && currentDateObj <= oldDate;
    const isSelected =
      selectedDate.value &&
      selectedDate.value.date === date &&
      selectedDate.value.month === month &&
      selectedDate.value.year === year;

    dates.push({
      date,
      month,
      year,
      isOtherMonth: false,
      isDisabled: isPast || isPastOldDate,
      isPastOldDate: isPastOldDate && !isPast,
      isSelected,
      key: `current-${date}`,
    });
  }

  // Next month dates
  const remainingDays = 42 - dates.length;
  for (let date = 1; date <= remainingDays; date++) {
    dates.push({
      date,
      month: month + 1,
      year: month === 11 ? year + 1 : year,
      isOtherMonth: true,
      isDisabled: true,
      isPastOldDate: false,
      isSelected: false,
      key: `next-${date}`,
    });
  }

  return dates;
});

const canGoPrevious = computed(() => {
  const today = new Date();
  const currentYear = currentDate.value.getFullYear();
  const currentMonth = currentDate.value.getMonth();

  return (
    currentYear > today.getFullYear() ||
    (currentYear === today.getFullYear() && currentMonth > today.getMonth())
  );
});

const canGoNext = computed(() => {
  const maxDate = new Date();
  maxDate.setMonth(maxDate.getMonth() + 3);

  const currentYear = currentDate.value.getFullYear();
  const currentMonth = currentDate.value.getMonth();

  return (
    currentYear < maxDate.getFullYear() ||
    (currentYear === maxDate.getFullYear() && currentMonth < maxDate.getMonth())
  );
});

const newDateTime = computed(() => {
  if (!selectedDate.value || !selectedTime.value) return "";

  const day = String(selectedDate.value.date).padStart(2, "0");
  const month = String(selectedDate.value.month + 1).padStart(2, "0");
  const year = selectedDate.value.year;

  return `${selectedTime.value} - ${day}/${month}/${year}`;
});

const canSave = computed(() => {
  return selectedDate.value && selectedTime.value;
});

// Methods
function parseOldAppointmentDate() {
  if (!props.oldAppointment.date) return null;

  // Assuming format: "DD/MM/YYYY"
  const parts = props.oldAppointment.date.split("/");
  if (parts.length === 3) {
    const day = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10) - 1;
    const year = parseInt(parts[2], 10);
    const date = new Date(year, month, day);
    date.setHours(0, 0, 0, 0);
    return date;
  }
  return null;
}

function previousMonth() {
  if (canGoPrevious.value) {
    const newDate = new Date(currentDate.value);
    newDate.setMonth(newDate.getMonth() - 1);
    currentDate.value = newDate;
  }
}

function nextMonth() {
  if (canGoNext.value) {
    const newDate = new Date(currentDate.value);
    newDate.setMonth(newDate.getMonth() + 1);
    currentDate.value = newDate;
  }
}

function selectDate(date) {
  if (!date.isDisabled) {
    selectedDate.value = {
      date: date.date,
      month: date.month,
      year: date.year,
    };
  }
}

function selectTime(slot) {
  if (!slot.isBooked) {
    selectedTime.value = slot.time;
  }
}

function closePopup() {
  emit("close");
  resetForm();
}

function saveChanges() {
  if (!canSave.value) return;

  const changeData = {
    oldDateTime: props.oldAppointment.dateTime,
    newDate: `${String(selectedDate.value.date).padStart(2, "0")}/${String(
      selectedDate.value.month + 1
    ).padStart(2, "0")}/${selectedDate.value.year}`,
    newTime: selectedTime.value,
    newDateTime: newDateTime.value,
  };

  emit("save", changeData);
  resetForm();
}

function resetForm() {
  selectedDate.value = null;
  selectedTime.value = null;
  currentDate.value = new Date();
}

// Watch for popup open
watch(
  () => props.isOpen,
  (newVal) => {
    if (newVal) {
      resetForm();
    }
  }
);
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