<template>
  <div class="relative w-full h-full px-8 py-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-1 mb-6">
      <h1 class="text-2xl font-semibold text-black">Lịch làm việc</h1>
      <p class="text-base font-medium text-gray-500">
        Quản lý lịch trực và đăng ký ca làm việc
      </p>
    </div>

    <!-- Tabs -->
    <div class="border-b !border-gray-300 mb-6">
      <div class="flex gap-8 relative">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'pb-3 flex items-center gap-2 relative',
            tab.id === activeTab ? 'text-[#155dfc]' : 'text-gray-600',
          ]"
        >
          <!-- <img :src="icons[tab.icon]" alt="" class="w-4 h-4" /> -->
          <span class="text-base font-medium">
            {{ tab.label }}
          </span>
          <div
            v-if="tab.id === activeTab"
            class="absolute left-0 bottom-0 w-full h-0.5 bg-[#155dfc]"
          />
        </button>
      </div>
    </div>

    <!-- My Schedule Card -->
    <div
      v-if="activeTab === 'my-schedule'"
      class="bg-white border !border-gray-300 rounded-[14px] shadow-sm overflow-hidden"
    >
      <!-- Header with gradient background -->
      <div
        class="bg-gradient-to-r from-[#f0fdfa] to-[#f0fdf4] px-6 pt-4 pb-4 flex flex-col gap-3"
      >
        <!-- Week Navigation -->
        <div class="flex items-center justify-between">
          <button
            @click="goToPreviousWeek"
            class="bg-white border !border-gray-300 rounded-lg h-9 px-4 flex items-center gap-2 hover:bg-gray-50 transition-colors"
          >
            <!-- <img :src="icons.chevronLeft" alt="" class="w-4 h-4" /> -->
            <span class="text-sm font-medium text-black"> ← Tuần trước </span>
          </button>

          <div class="flex items-center gap-2">
            <!-- <img :src="icons.calendar" alt="" class="w-5 h-5" /> -->
            <span class="text-base font-semibold text-black">
              {{ weekRange }}
            </span>
          </div>

          <button
            @click="goToNextWeek"
            class="bg-white border !border-gray-300 rounded-lg h-9 px-4 flex items-center gap-2 hover:bg-gray-50 transition-colors"
          >
            <span class="text-sm font-medium text-black"> Tuần sau → </span>
            <!-- <img :src="icons.chevronRight" alt="" class="w-4 h-4" /> -->
          </button>
        </div>

        <!-- Status Info & View Toggle -->
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <span class="text-sm font-normal text-gray-600">
                Ca tuần này:
              </span>
              <span class="text-sm font-semibold text-[#009689]">
                {{ weekShifts }}
              </span>
            </div>
            <div class="w-px h-4 bg-gray-300" />
            <div class="flex items-center gap-2">
              <span class="text-sm font-normal text-gray-600">
                Tổng giờ làm:
              </span>
              <span class="text-sm font-semibold text-[#009689]">
                {{ totalHours }}h
              </span>
            </div>
          </div>

          <!-- View Toggle -->
          <div class="flex items-center gap-2">
            <button
              @click="viewMode = 'week'"
              :class="[
                'h-9 px-4 rounded-lg flex items-center gap-2 transition-colors',
                viewMode === 'week'
                  ? 'bg-[#030213] text-white'
                  : 'bg-white border !border-gray-300 text-black hover:bg-gray-50',
              ]"
            >
              <!-- <img
                :src="
                  viewMode === 'week'
                    ? icons.calendarWhite
                    : icons.calendarBlack
                "
                alt=""
                class="w-4 h-4"
              /> -->
              <span class="text-sm font-medium">Lịch Tuần</span>
            </button>
            <button
              @click="viewMode = 'month'"
              :class="[
                'h-9 px-4 rounded-lg flex items-center gap-2 transition-colors',
                viewMode === 'month'
                  ? 'bg-[#030213] text-white'
                  : 'bg-white border !border-gray-300 text-black hover:bg-gray-50',
              ]"
            >
              <span class="text-sm font-medium">Lịch Tháng</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Calendar Table -->
      <div class="px-6 py-6 flex flex-col gap-6">
        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center h-64">
          <div
            class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#155dfc]"
          ></div>
        </div>

        <!-- Week View -->
        <div v-if="viewMode === 'week'" class="overflow-auto">
          <table class="w-full border-collapse">
            <thead>
              <tr>
                <th
                  class="bg-gray-50 border !border-gray-300 h-20 px-2 text-center text-xs font-bold text-gray-700"
                  style="width: 90px"
                >
                  Giờ / Ngày
                </th>
                <th
                  v-for="day in calendarDays"
                  :key="day.label"
                  class="bg-gray-50 border !border-gray-300 h-20 px-2 text-center"
                  :style="{ width: day.width }"
                >
                  <div
                    class="flex flex-col items-center justify-center gap-0.5"
                  >
                    <span class="text-xs font-bold text-black">
                      {{ day.label }}
                    </span>
                    <span class="text-xs font-semibold text-gray-500">
                      {{ day.date }}
                    </span>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="timeSlot in timeSlots" :key="timeSlot.name">
                <td class="bg-gray-50 border !border-gray-300 h-24 text-center">
                  <div
                    class="flex flex-col items-center justify-center gap-0.5"
                  >
                    <span class="text-xs font-semibold text-black">
                      {{ timeSlot.name }}
                    </span>
                    <span class="text-xs font-normal text-gray-500">
                      {{ timeSlot.time }}
                    </span>
                  </div>
                </td>
                <td
                  v-for="(daySchedule, dayIndex) in timeSlot.schedule"
                  :key="dayIndex"
                  class="border !border-gray-300 h-24 p-1.5"
                >
                  <!-- FIX: Xử lý cả single shift và multiple shifts (array) -->
                  <template v-if="daySchedule">
                    <!-- Single shift -->
                    <div
                      v-if="!Array.isArray(daySchedule)"
                      :class="[
                        'h-full border-2 rounded-lg p-2 flex flex-col justify-center',
                        getScheduleCellClass(daySchedule.status, daySchedule.shift),
                        daySchedule.status === 'ongoing' ? 'animate-pulse' : '',
                      ]"
                    >
                      <span
                        class="text-xs font-semibold text-[#364153] truncate"
                        style="font-family: 'Inter', sans-serif"
                      >
                        {{ daySchedule.room }}
                      </span>
                    </div>

                    <!-- Multiple shifts - show first one, add indicator -->
                    <div
                      v-else
                      :class="[
                        'h-full border-2 rounded-lg p-2 flex flex-col justify-between',
                        getScheduleCellClass(daySchedule[0].status, daySchedule[0].shift),
                        daySchedule[0].status === 'ongoing'
                          ? 'animate-pulse'
                          : '',
                      ]"
                    >
                      <span
                        class="text-xs font-semibold text-[#364153] truncate"
                        style="font-family: 'Inter', sans-serif"
                      >
                        {{ daySchedule[0].room }}
                      </span>
                      <span
                        v-if="daySchedule.length > 1"
                        class="text-[10px] font-bold text-[#155dfc] mt-0.5"
                        style="font-family: 'Inter', sans-serif"
                      >
                        +{{ daySchedule.length - 1 }} ca khác
                      </span>
                    </div>
                  </template>
                  <div v-else class="bg-gray-50 h-full rounded-lg" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Month View -->
        <div v-else-if="viewMode === 'month'">
          <!-- Month navigation -->
          <div class="flex items-center justify-between mb-4">
            <button @click="goToPreviousMonth" class="bg-white border !border-gray-300 rounded-lg h-9 px-4 text-sm font-medium hover:bg-gray-50 transition-colors">← Tháng trước</button>
            <span class="text-base font-semibold text-black">
              Tháng {{ monthViewDate.getMonth() + 1 }}/{{ monthViewDate.getFullYear() }}
            </span>
            <button @click="goToNextMonth" class="bg-white border !border-gray-300 rounded-lg h-9 px-4 text-sm font-medium hover:bg-gray-50 transition-colors">Tháng sau →</button>
          </div>
          <!-- Month grid -->
          <div class="grid grid-cols-7 gap-1">
            <div v-for="d in ['T2','T3','T4','T5','T6','T7','CN']" :key="d"
              class="text-center text-xs font-bold text-gray-500 py-2">{{ d }}</div>
            <!-- Empty cells before first day -->
            <div v-for="e in monthGridLeadingBlanks" :key="'blank-'+e" />
            <!-- Day cells -->
            <div
              v-for="day in monthGridDays"
              :key="day.date"
              class="aspect-square flex flex-col items-center justify-center rounded-lg text-sm border transition-colors"
              :class="day.shifts.length > 0
                ? (day.shifts.some(s => s === 'ca_sang' || s === 'ca_chieu') ? 'bg-yellow-50 border-yellow-400 font-semibold' : 'bg-purple-50 border-purple-400 font-semibold')
                : 'bg-white border-gray-100 text-gray-400'"
            >
              <span>{{ day.dayNum }}</span>
              <div class="flex gap-0.5 mt-0.5">
                <span v-if="day.shifts.includes('ca_sang') || day.shifts.includes('ca_chieu')" class="w-1.5 h-1.5 rounded-full bg-yellow-500" />
                <span v-if="day.shifts.includes('ca_toi')" class="w-1.5 h-1.5 rounded-full bg-purple-500" />
              </div>
            </div>
          </div>
          <!-- Legend -->
          <div class="flex items-center gap-6 mt-4 text-xs text-gray-500">
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-yellow-200 border border-yellow-400 inline-block"></span> Ca Sáng</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-purple-200 border border-purple-400 inline-block"></span> Ca Tối</span>
          </div>
        </div>
      </div>
    </div>



    <!-- Leave Request Tab Content -->
    <div v-else-if="activeTab === 'register-shift'" class="space-y-5">

      <!-- Alert: Lưu ý xin nghỉ trước -->
      <div class="flex gap-3 items-start bg-amber-50 border !border-amber-200 rounded-xl px-5 py-4">
        <div class="shrink-0 w-5 h-5 mt-0.5">
          <svg viewBox="0 0 20 20" fill="none" class="w-5 h-5 text-amber-500">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" fill="currentColor"/>
          </svg>
        </div>
        <div>
          <p class="text-sm font-semibold text-amber-800">Lưu ý khi xin nghỉ</p>
          <p class="text-sm text-amber-700 mt-0.5">
            Nên gửi đơn xin nghỉ trước <strong>3–7 ngày</strong> để Admin kịp sắp xếp nhân sự. Đơn đã duyệt không thể hủy.
          </p>
        </div>
      </div>

      <!-- Quota Card + Form -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Quota Card -->
        <div class="bg-white border !border-gray-200 rounded-xl p-5 shadow-sm flex flex-col gap-4">
          <div>
            <p class="text-sm font-semibold text-gray-500">Ngày phép tháng {{ leaveQuota.thang }}/{{ leaveQuota.nam }}</p>
            <div class="flex items-end gap-2 mt-1">
              <span class="text-4xl font-bold text-gray-900">{{ leaveQuota.con_lai }}</span>
              <span class="text-base text-gray-400 pb-1">/ {{ leaveQuota.tong_quota }} ngày</span>
            </div>
          </div>

          <!-- Progress bar -->
          <div class="w-full bg-gray-100 rounded-full h-2">
            <div
              class="h-2 rounded-full transition-all duration-500"
              :class="leaveQuota.da_su_dung >= 4 ? 'bg-red-400' : leaveQuota.da_su_dung >= 3 ? 'bg-amber-400' : 'bg-teal-400'"
              :style="{ width: Math.min((leaveQuota.da_su_dung / leaveQuota.tong_quota) * 100, 100) + '%' }"
            />
          </div>

          <div class="flex justify-between text-xs text-gray-500">
            <span>Đã dùng: <strong class="text-gray-700">{{ leaveQuota.da_su_dung }}</strong></span>
            <span>Tổng: <strong class="text-gray-700">{{ leaveQuota.tong_quota }}</strong></span>
          </div>

          <!-- Over quota warning -->
          <div v-if="leaveQuota.da_su_dung >= 4" class="bg-red-50 border !border-red-200 rounded-lg px-3 py-2 text-xs text-red-700">
            Bạn đã dùng hết ngày phép. Đơn tiếp theo sẽ được tính là <strong>nghỉ không lương</strong>.
          </div>
        </div>

        <!-- Submit Form -->
        <div class="lg:col-span-2 bg-white border !border-gray-200 rounded-xl p-5 shadow-sm">
          <h3 class="text-base font-semibold text-gray-800 mb-4">Gửi đơn xin nghỉ</h3>

          <form @submit.prevent="submitLeaveRequest" class="flex flex-col gap-4">
            <!-- Date range picker -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày <span class="text-red-500">*</span></label>
                <input
                  type="date"
                  v-model="leaveForm.start_date"
                  :min="minLeaveDate"
                  class="w-full border !border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-teal-400"
                  required
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày <span class="text-red-500">*</span></label>
                <input
                  type="date"
                  v-model="leaveForm.end_date"
                  :min="leaveForm.start_date || minLeaveDate"
                  class="w-full border !border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-teal-400"
                  required
                />
              </div>
            </div>
            <p v-if="leaveFormErrors.date" class="text-xs text-red-600 mt-1">{{ leaveFormErrors.date }}</p>

            <!-- Reason -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Lý do xin nghỉ</label>
              <textarea
                v-model="leaveForm.ly_do"
                rows="3"
                placeholder="Mô tả lý do (không bắt buộc)..."
                class="w-full border !border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-teal-400 resize-none"
              />
            </div>

            <!-- Paid/unpaid preview -->
            <div class="text-xs text-gray-500 flex items-center gap-1.5">
              <span
                class="inline-block w-2 h-2 rounded-full"
                :class="leaveQuota.da_su_dung < 4 ? 'bg-teal-400' : 'bg-red-400'"
              />
              <span v-if="leaveQuota.da_su_dung < 4">Đơn này sẽ tính là <strong class="text-teal-700">nghỉ có lương</strong> (còn {{ leaveQuota.con_lai }} ngày phép)</span>
              <span v-else>Đơn này sẽ tính là <strong class="text-red-600">nghỉ không lương</strong> (đã hết quota)</span>
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="leaveSubmitting"
                class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 disabled:opacity-50 text-white text-sm font-semibold rounded-lg transition-colors"
              >
                {{ leaveSubmitting ? 'Đang gửi...' : 'Gửi đơn xin nghỉ' }}
              </button>
            </div>

            <!-- Success / Error message -->
            <div v-if="leaveSubmitMessage" :class="['text-sm font-medium px-3 py-2 rounded-lg', leaveSubmitSuccess ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700']">
              {{ leaveSubmitMessage }}
            </div>
          </form>
        </div>
      </div>

      <!-- Leave History -->
      <div class="bg-white border !border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b !border-gray-200 flex items-center justify-between">
          <div>
            <h3 class="text-base font-semibold text-gray-800">Lịch sử đơn xin nghỉ</h3>
            <p class="text-xs text-gray-500 mt-0.5">Tất cả đơn bạn đã gửi</p>
          </div>
          <button @click="fetchLeaveRequests" class="text-xs text-teal-600 hover:underline font-medium">Làm mới</button>
        </div>

        <!-- Loading -->
        <div v-if="leaveLoading" class="py-10 text-center text-sm text-gray-400">Đang tải...</div>

        <!-- Empty -->
        <div v-else-if="leaveRequests.length === 0" class="py-10 text-center text-sm text-gray-400">
          Chưa có đơn xin nghỉ nào.
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-gray-50 border-b !border-gray-200">
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Ngày nghỉ</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Lý do</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Loại</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Trạng thái</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="req in leaveRequests"
                :key="req.id"
                class="border-b !border-gray-100 hover:bg-gray-50 transition-colors"
              >
                <td class="px-5 py-3 font-medium text-gray-800">{{ formatDateDisplay2(req.ngay_nghi) }}</td>
                <td class="px-5 py-3 text-gray-600 max-w-xs">
                  <span class="line-clamp-2">{{ req.ly_do || '—' }}</span>
                  <span v-if="req.ly_do_tu_choi" class="block text-xs text-red-500 mt-0.5">Lý do từ chối: {{ req.ly_do_tu_choi }}</span>
                </td>
                <td class="px-5 py-3">
                  <span
                    class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold"
                    :class="req.loai_nghi === 'co_luong' ? 'bg-teal-100 text-teal-700' : 'bg-orange-100 text-orange-700'"
                  >
                    {{ req.loai_nghi === 'co_luong' ? 'Có lương' : 'Không lương' }}
                  </span>
                </td>
                <td class="px-5 py-3">
                  <span
                    class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold"
                    :class="{
                      'bg-yellow-100 text-yellow-700': req.trang_thai === 'cho_duyet',
                      'bg-green-100 text-green-700': req.trang_thai === 'da_duyet',
                      'bg-red-100 text-red-700': req.trang_thai === 'tu_choi',
                    }"
                  >
                    {{ req.trang_thai === 'cho_duyet' ? 'Chờ duyệt' : req.trang_thai === 'da_duyet' ? 'Đã duyệt' : 'Từ chối' }}
                  </span>
                </td>
                <td class="px-5 py-3">
                  <button
                    v-if="req.trang_thai === 'cho_duyet'"
                    @click="cancelLeaveRequest(req.id)"
                    class="text-xs text-red-500 hover:text-red-700 font-medium hover:underline"
                  >
                    Hủy đơn
                  </button>
                  <span v-else class="text-xs text-gray-400">—</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>


</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { getUser } from "@/utils/auth";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
import {
  getMySchedule,
  getMyTodaySchedule,
} from "@/services/lichLamViecService";
import {
  getDanhSachLichDangKy,
} from "@/services/lichDangKyService";
import api from "@/utils/api";

const activeTab = ref("my-schedule");
const loading = ref(false);
const currentUser = ref(null);


const tabs = [
  {
    id: "my-schedule",
    label: "Lịch của tôi",
    icon: "calendarUser",
    left: "0px",
  },
  {
    id: "register-shift",
    label: "Xin nghỉ",
    icon: "clipboardCheck",
    left: "140px",
  },
];

// Date management
const currentDate = ref(new Date());
const selectedDate = ref(new Date());

const weekNumber = ref(49);
const weekRange = ref("01/12 - 07/12");
const minRequirement = ref(4);
const totalHours = ref(19);
const viewMode = ref("week");

// API Data
const scheduleData = ref(null);
const todayScheduleData = ref(null);
const confirmedShifts = ref([]); // Ca đã xác nhận
const registeredShifts = ref([]); // Ca đã đăng ký (tất cả trạng thái)
const weekShifts = ref(0); // Tổng số ca trong tuần

// Computed properties for date calculations
const startOfWeek = computed(() => {
  const date = new Date(currentDate.value);
  const day = date.getDay();
  const diff = date.getDate() - day + (day === 0 ? -6 : 1); // Monday as first day
  return new Date(date.setDate(diff));
});

const endOfWeek = computed(() => {
  const date = new Date(startOfWeek.value);
  date.setDate(date.getDate() + 6);
  return date;
});

const formatDate = (date) => {
  return date.toISOString().split("T")[0];
};

const formatDateDisplay = (date) => {
  const d = new Date(date);
  const day = String(d.getDate()).padStart(2, "0");
  const month = String(d.getMonth() + 1).padStart(2, "0");
  return `${day}/${month}`;
};

const formatDateTime = (dateTimeString) => {
  if (!dateTimeString) return "N/A";
  const d = new Date(dateTimeString);
  const day = String(d.getDate()).padStart(2, "0");
  const month = String(d.getMonth() + 1).padStart(2, "0");
  const year = d.getFullYear();
  const hours = String(d.getHours()).padStart(2, "0");
  const minutes = String(d.getMinutes()).padStart(2, "0");
  return `${day}/${month}/${year} ${hours}:${minutes}`;
};

// Update week range display
const updateWeekRange = () => {
  const start = formatDateDisplay(startOfWeek.value);
  const end = formatDateDisplay(endOfWeek.value);
  weekRange.value = `${start} - ${end}`;

  // Calculate week number
  const onejan = new Date(currentDate.value.getFullYear(), 0, 1);
  const week = Math.ceil(
    ((currentDate.value - onejan) / 86400000 + onejan.getDay() + 1) / 7
  );
  weekNumber.value = week;
};

// Navigation functions
const goToPreviousWeek = () => {
  const date = new Date(currentDate.value);
  date.setDate(date.getDate() - 7);
  currentDate.value = date;
  updateWeekRange();
  fetchScheduleData();
};

const goToNextWeek = () => {
  const date = new Date(currentDate.value);
  date.setDate(date.getDate() + 7);
  currentDate.value = date;
  updateWeekRange();
  fetchScheduleData();
};

// Fetch schedule data from API
const fetchScheduleData = async () => {
  loading.value = true;
  try {
    const weekStart = formatDate(startOfWeek.value);
    const weekEnd = formatDate(endOfWeek.value);

    console.log(`\n📅 Fetching schedule for week: ${weekStart} to ${weekEnd}`);
    console.log(`   Current date: ${formatDate(currentDate.value)}`);

    const data = await getMySchedule(weekStart, weekEnd);

    console.log("📦 fetchScheduleData response:", data);

    if (data && data.status) {
      scheduleData.value = data.data;
      console.log("✅ Schedule data loaded:", data.data);
      updateCalendarData(data.data);

      // Update employee info
      if (data.data.nhan_vien) {
        currentUser.value = {
          ...currentUser.value,
          ...data.data.nhan_vien,
        };
      }
    } else {
      console.warn("⚠️ No schedule data or status false:", data);
      // Vẫn cần update calendar với data rỗng
      updateCalendarData({ schedule: [], statistics: null });
    }

    // ✅ LUÔN fetch ca đăng ký đã duyệt (dù có hay không có lịch chính thức)
    try {
      console.log("🔄 Fetching approved shifts...");

      // Thử endpoint getDanhSachLichDangKy với filter trang_thai
      const approvedRes = await getDanhSachLichDangKy({
        trang_thai: "da_xac_nhan",
        per_page: 500,
      });

      console.log("📦 Raw API response:", approvedRes);

      // ✅ Handle response structure: data.data (pagination) or data (direct array)
      let approvedShifts = [];
      if (approvedRes && approvedRes.success && approvedRes.data) {
        if (Array.isArray(approvedRes.data)) {
          // Direct array
          approvedShifts = approvedRes.data;
        } else if (
          approvedRes.data.data &&
          Array.isArray(approvedRes.data.data)
        ) {
          // Paginated response
          approvedShifts = approvedRes.data.data;
        } else {
          console.warn("⚠️ Unknown response structure:", approvedRes.data);
        }
      } else {
        console.error("❌ API response invalid or failed:", approvedRes);
      }

      console.log(
        `✅ Extracted ${approvedShifts.length} approved shifts:`,
        approvedShifts
      );

      // Add approved shifts to calendar
      approvedShifts.forEach((item, index) => {
        console.log(
          `\n🔍 [${index + 1}/${approvedShifts.length}] Processing shift:`,
          {
            id: item.id,
            ngay_gio: item.ngay_gio,
            trang_thai: item.trang_thai,
            ghi_chu: item.ghi_chu,
            nhan_vien_id: item.nhan_vien_id,
          }
        );

        // ✅ Backend đã filter ra chỉ những ca "da_xac_nhan" rồi
        // Nhưng kiểm tra lại cho chắc
        const status = item.trang_thai || "";

        if (
          status !== "da_xac_nhan" &&
          status !== "confirmed" &&
          status !== "đã_xác_nhận"
        ) {
          console.warn(`❌ Skip shift ${item.id} - invalid status: ${status}`);
          return;
        }

        console.log(`✅ Status OK for shift ${item.id}`);
        const itemDate = item.ngay_gio;
        // Parse date string safely (handle timezone issues)
        let date;
        if (typeof itemDate === "string" && itemDate.includes("T")) {
          // ISO string format - parse just the date part
          const dateOnly = itemDate.split("T")[0];
          const [year, month, day] = dateOnly.split("-");
          date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
        } else if (typeof itemDate === "string" && itemDate.includes(" ")) {
          // "2025-12-15 14:30:00" format
          const dateOnly = itemDate.split(" ")[0];
          const [year, month, day] = dateOnly.split("-");
          date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
        } else {
          date = new Date(itemDate);
        }

        const dayOfWeek = date.getDay();
        const dayIndex = dayOfWeek === 0 ? 6 : dayOfWeek - 1;

        // Check if this date is within the current week
        const itemDateStr = formatDate(date);
        const startStr = formatDate(startOfWeek.value);
        const endStr = formatDate(endOfWeek.value);

        console.log(
          `📅 Checking shift ${item.id}: ${itemDateStr} (week: ${startStr} to ${endStr})`
        );

        if (itemDateStr >= startStr && itemDateStr <= endStr) {
          // Get time from datetime
          const time = new Date(itemDate).toLocaleTimeString("vi-VN", {
            hour: "2-digit",
            minute: "2-digit",
          });

          // Format room name with time and note
          const roomName = item.ghi_chu
            ? `Ca đăng ký ${time} - ${item.ghi_chu}`
            : `Ca đăng ký ${time}`;

          // Log and skip — registered shifts no longer displayed as separate slot
          console.log(`ℹ️ Registered shift ignored (slot removed): ${roomName}`);
        } else {
          console.log(`⚠️ Skip shift ${item.id} - outside current week`);
        }
      });

      // Update statistics with approved shifts
      updateStatisticsWithApprovedShifts(
        approvedShifts,
        startOfWeek.value,
        endOfWeek.value
      );

      console.log(
        `📊 Total approved shifts added to calendar: ${
          approvedShifts.filter(
            (s) =>
              formatDate(new Date(s.ngay_gio)) >=
                formatDate(startOfWeek.value) &&
              formatDate(new Date(s.ngay_gio)) <= formatDate(endOfWeek.value)
          ).length
        }`
      );

      // ⭐ FALLBACK: If main schedule is empty, also try to populate from approved shifts
      // This handles case where /lich-lam-viec returns empty
      if (
        !scheduleData.value?.schedule ||
        scheduleData.value.schedule.length === 0 ||
        (scheduleData.value.schedule.length > 0 &&
          scheduleData.value.schedule.every(
            (s) => !s.lich_lam_viec || s.lich_lam_viec.length === 0
          ))
      ) {
        console.warn(
          "Main schedule is empty, trying to populate from approved shifts"
        );

        // Group approved shifts by date
        const shiftsByDate = {};
        approvedShifts.forEach((item) => {
          const status = item.trang_thai || "";
          if (
            status === "da_xac_nhan" ||
            status === "confirmed" ||
            status === "đã_xác_nhận"
          ) {
            const dateOnly = (item.ngay_gio || "").split("T")[0];
            if (!shiftsByDate[dateOnly]) {
              shiftsByDate[dateOnly] = [];
            }
            shiftsByDate[dateOnly].push(item);
          }
        });

        console.log("Grouping approved shifts by date:", shiftsByDate);
      }
    } catch (err) {
      console.error("❌ Error fetching approved registered shifts:", err);
      console.error("Error status:", err.response?.status);
      console.error("Error message:", err.response?.data?.message);
      console.error("Error details:", err.response?.data || err.message);

      // Nếu lỗi 404 hoặc endpoint không tồn tại, thử endpoint khác
      if (err.response?.status === 404 || err.response?.status === 405) {
        console.warn("⚠️ Endpoint không tồn tại, thử dùng /lich-dang-ky");
        try {
          const fallbackRes = await getDanhSachLichDangKy({
            trang_thai: "da_xac_nhan",
            per_page: 500,
          });

          console.log("✅ Fallback response:", fallbackRes);

          if (fallbackRes && fallbackRes.success && fallbackRes.data) {
            let fallbackShifts = [];
            if (Array.isArray(fallbackRes.data)) {
              fallbackShifts = fallbackRes.data;
            } else if (
              fallbackRes.data.data &&
              Array.isArray(fallbackRes.data.data)
            ) {
              fallbackShifts = fallbackRes.data.data;
            }

            // Process fallback shifts (same logic as above)
            console.log(`✅ Got ${fallbackShifts.length} shifts from fallback`);
            // TODO: Process these shifts
          }
        } catch (fallbackErr) {
          console.error("❌ Fallback also failed:", fallbackErr);
        }
      }

      // Don't fail if we can't get registered shifts, just continue
    }
  } catch (error) {
    console.error("❌ Error fetching main schedule:", error);
    showErrorToast(
      "Lỗi",
      error.response?.data?.message || "Không thể tải lịch làm việc"
    );
  }

  // ✅ Đảm bảo loading = false dù thành công hay thất bại
  loading.value = false;
};

// Fetch today's schedule
const fetchTodaySchedule = async () => {
  loading.value = true;
  try {
    const data = await getMyTodaySchedule();

    if (data.status) {
      todayScheduleData.value = data.data;
      // You can display today's data in a special view if needed
    }
  } catch (error) {
    console.error("Error fetching today schedule:", error);
    showErrorToast("Lỗi", "Không thể tải lịch hôm nay");
  } finally {
    loading.value = false;
  }
};

// Fetch confirmed shifts (Ca đã xác nhận)
const fetchConfirmedShifts = async () => {
  loading.value = true;
  try {
    const response = await api.get("/lich-dang-ky/ca-da-xac-nhan-cua-toi");

    if (response.data.success) {
      confirmedShifts.value = response.data.data.data || [];
    } else {
      showErrorToast(
        "Lỗi",
        response.data.message || "Không thể tải ca đã xác nhận"
      );
    }
  } catch (error) {
    console.error("Error fetching confirmed shifts:", error);
    showErrorToast("Lỗi", "Không thể tải ca đã xác nhận");
  } finally {
    loading.value = false;
  }
};

// Fetch registered shifts (Ca đã đăng ký - tất cả trạng thái)
const fetchRegisteredShifts = async () => {
  loading.value = true;
  try {
    const response = await getDanhSachLichDangKy({ per_page: 500 });

    if (response.success) {
      // Handle paginated response
      let shifts = [];
      if (response.data && Array.isArray(response.data)) {
        // Direct array
        shifts = response.data;
      } else if (response.data && response.data.data) {
        // Paginated response with nested data
        shifts = response.data.data;
      }

      console.log("Fetched registered shifts:", shifts);
      registeredShifts.value = shifts;
    } else {
      registeredShifts.value = [];
    }
  } catch (error) {
    console.error("Error fetching registered shifts:", error);
    registeredShifts.value = [];
  } finally {
    loading.value = false;
  }
};

// Update calendar data from API response
const updateCalendarData = (data) => {
  if (!data || !data.schedule) {
    console.warn("updateCalendarData: No data or schedule", data);
    return;
  }

  console.log("updateCalendarData: Processing schedule", data.schedule);

  // Reset time slots
  timeSlots.value.forEach((slot) => {
    slot.schedule = [null, null, null, null, null, null, null];
  });

  console.log("📅 Starting to process schedule data...");

  // Map schedule data to calendar
  data.schedule.forEach((daySchedule) => {
    const date = new Date(daySchedule.date);
    const dayOfWeek = date.getDay();
    const dayIndex = dayOfWeek === 0 ? 6 : dayOfWeek - 1; // Monday = 0, Sunday = 6

    console.log(
      `Processing day ${daySchedule.date}, shifts:`,
      daySchedule.lich_lam_viec
    );

    // Process work shifts
    const shifts = daySchedule.lich_lam_viec || [];

    if (!Array.isArray(shifts)) {
      console.warn(
        `lich_lam_viec is not array for ${daySchedule.date}:`,
        daySchedule.lich_lam_viec
      );
      return;
    }

    // ✅ FIX: Kiểm tra lịch hẹn
    const appointments = daySchedule.lich_hen || [];
    const hasAppointments = appointments.length > 0;

    if (shifts.length === 0) {
      console.log(`⚠️ No shifts assigned for ${daySchedule.date}`);

      // ✅ Nếu không có ca làm việc nhưng CÓ lịch hẹn → Tạo ca "Lịch hẹn"
      if (hasAppointments) {
        console.log(
          `📅 Found ${appointments.length} appointment(s) without shift for ${daySchedule.date}`
        );

        // Tạo một "pseudo shift" để hiển thị lịch hẹn
        // Xác định ca dựa trên giờ hẹn đầu tiên
        const firstAppointment = appointments[0];
        const appointmentTime = new Date(firstAppointment.ngay_gio);
        const hour = appointmentTime.getHours();

        let slotIndex = 0; // Mặc định ca sáng
        let shiftType = "ca_sang";

        if (hour >= 7 && hour < 12) {
          slotIndex = 0; // Ca sáng
          shiftType = "ca_sang";
        } else if (hour >= 13 && hour < 18) {
          slotIndex = 1; // Ca chiều
          shiftType = "ca_chieu";
        } else if (hour >= 18 && hour < 22) {
          slotIndex = 2; // Ca tối
          shiftType = "ca_toi";
        }

        const shiftData = {
          id: `appointment-${daySchedule.date}`,
          room: `Lịch hẹn (${appointments.length} khách)`,
          patients: appointments.length,
          status: getShiftStatus(daySchedule.date, shiftType),
          appointments: appointments,
          date: daySchedule.date,
          shift: shiftType,
          isAppointmentOnly: true, // Đánh dấu đây là lịch hẹn không có ca làm việc
        };

        timeSlots.value[slotIndex].schedule[dayIndex] = shiftData;
      }
    } else {
      console.log(`✅ Found ${shifts.length} shift(s) for ${daySchedule.date}`);
    }

    shifts.forEach((shift) => {
      let slotIndex = getTimeSlotIndex(shift.thoi_gian_truc);

      // ✅ FIX: Nếu không thuộc 3 ca tiêu chuẩn (sáng/chiều/tối),
      // đưa vào slot 4 "Phân công" (index 3)
      if (slotIndex === -1) {
        slotIndex = 3; // Slot "Phân công"
        console.log(
          `Admin-assigned shift (non-standard time) for ${daySchedule.date}:`,
          shift.thoi_gian_truc
        );
      }

      if (timeSlots.value[slotIndex]) {
        const appointmentCount = daySchedule.lich_hen
          ? daySchedule.lich_hen.length
          : 0;

        const shiftData = {
          id: shift.id,
          room: formatRoomName(shift.thoi_gian_truc, shift.ghi_chu),
          patients: appointmentCount,
          status: getShiftStatus(daySchedule.date, shift.thoi_gian_truc),
          appointments: daySchedule.lich_hen || [],
          date: daySchedule.date,
          shift: shift.thoi_gian_truc,
        };

        // ✅ Xử lý nhiều ca trong cùng 1 slot (ví dụ: nhiều lịch phân công)
        if (timeSlots.value[slotIndex].schedule[dayIndex]) {
          const existing = timeSlots.value[slotIndex].schedule[dayIndex];
          if (Array.isArray(existing)) {
            existing.push(shiftData);
          } else {
            timeSlots.value[slotIndex].schedule[dayIndex] = [
              existing,
              shiftData,
            ];
          }
        } else {
          timeSlots.value[slotIndex].schedule[dayIndex] = shiftData;
        }
      }
    });
  });

  // ✅ Log tổng kết sau khi xử lý
  console.log("📊 Calendar update summary:");
  timeSlots.value.forEach((slot, idx) => {
    const count = slot.schedule.filter((s) => s !== null).length;
    const shifts = slot.schedule.filter((s) => s !== null);
    console.log(`  - ${slot.name} (${slot.time}): ${count} shift(s)`);
    if (count > 0 && idx === 3) {
      // Log chi tiết ca đăng ký
      console.log(`    ✅ Ca đăng ký đã duyệt:`, shifts);
    }
  });

  // Update statistics
  if (data.statistics) {
    weekShifts.value = data.statistics.total_work_days || 0;

    // Calculate total hours based on shift type
    let totalHoursCalc = 0;
    data.schedule.forEach((daySchedule) => {
      daySchedule.lich_lam_viec.forEach((shift) => {
        if (shift.thoi_gian_truc === 'ca_sang') totalHoursCalc += 9;
        else if (shift.thoi_gian_truc === 'ca_chieu') totalHoursCalc += 9;
        else if (shift.thoi_gian_truc === 'ca_toi') totalHoursCalc += 15;
      });
    });
    totalHours.value = totalHoursCalc;
  }
};

// Update statistics with approved registered shifts
const updateStatisticsWithApprovedShifts = (
  approvedShifts,
  weekStart,
  weekEnd
) => {
  let additionalShifts = 0;
  let additionalHours = 0;

  approvedShifts.forEach((item) => {
    const status = item.trang_thai || "";
    if (
      status === "da_xac_nhan" ||
      status === "confirmed" ||
      status === "đã_xác_nhận"
    ) {
      const itemDate = new Date(item.ngay_gio);
      const weekStartDate = new Date(weekStart);
      const weekEndDate = new Date(weekEnd);

      if (itemDate >= weekStartDate && itemDate <= weekEndDate) {
        additionalShifts += 1;
        additionalHours += 8; // Mỗi ca đăng ký tính 8 giờ
      }
    }
  });

  weekShifts.value = (weekShifts.value || 0) + additionalShifts;
  totalHours.value = (totalHours.value || 0) + additionalHours;
};

// Helper function to get time slot index
const getTimeSlotIndex = (thoi_gian_truc) => {
  if (thoi_gian_truc === 'ca_sang' || thoi_gian_truc === 'ca_chieu') return 0; // Ca Sáng
  if (thoi_gian_truc === 'ca_toi') return 1; // Ca Tối
  return -1;
};

// Helper function to get shift name
const getShiftName = (thoi_gian_truc) => {
  const names = {
    ca_sang: "Ca Sáng",
    ca_chieu: "Ca Chiều",
    ca_toi: "Ca Tối",
  };
  // ✅ FIX: Nếu không phải 3 ca tiêu chuẩn, trả về "Phân công"
  return names[thoi_gian_truc] || "Phân công";
};

// Helper function to format room/location name
const formatRoomName = (shift, ghi_chu) => {
  const shiftName = getShiftName(shift);
  return ghi_chu ? `${shiftName} - ${ghi_chu}` : shiftName;
};

// Helper function to determine shift status
const getShiftStatus = (date, shift) => {
  const now = new Date();
  const shiftDate = new Date(date);

  if (shiftDate < now) {
    return "past";
  }

  // Check if it's today and within shift time
  if (shiftDate.toDateString() === now.toDateString()) {
    const hour = now.getHours();
    if (shift === 'ca_sang' && hour >= 8 && hour < 17) return 'ongoing';
    if (shift === 'ca_toi' && (hour >= 17 || hour < 8)) return 'ongoing';
  }

  return "upcoming";
};

// Generate calendar days dynamically
const calendarDays = computed(() => {
  const days = [];
  const dayNames = ["Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7", "CN"];

  for (let i = 0; i < 7; i++) {
    const date = new Date(startOfWeek.value);
    date.setDate(date.getDate() + i);
    days.push({
      label: dayNames[i],
      date: formatDateDisplay(date),
      width: i < 4 ? "181.867px" : "78.82px",
    });
  }

  return days;
});

// Open registration modal
const openRegistrationModal = () => {
  showRegisterModal.value = true;
};

// Initialize on mount
onMounted(() => {
  currentUser.value = getUser();
  updateWeekRange();
  // Always fetch schedule data on mount
  fetchScheduleData();
});

// Watch for tab changes
watch(activeTab, (newTab) => {
  if (newTab === 'my-schedule') {
    fetchScheduleData();
  } else if (newTab === 'register-shift') {
    fetchLeaveRequests();
    fetchLeaveQuota();
  }
});

// Watch viewMode to load month data
watch(viewMode, (newMode) => {
  if (newMode === 'month') {
    monthViewDate.value = new Date(currentDate.value);
    fetchMonthData();
  }
});

const timeSlots = ref([
  {
    name: 'Ca Sáng',
    time: '08:00 – 17:00',
    schedule: [null, null, null, null, null, null, null],
  },
  {
    name: 'Ca Tối',
    time: '17:00 – 08:00',
    schedule: [null, null, null, null, null, null, null],
  },
]);

// Cell class: match admin's yellow (Ca Sáng) / purple (Ca Tối) palette
const getScheduleCellClass = (status, shift) => {
  if (shift === 'ca_sang' || shift === 'ca_chieu') return 'border-yellow-400 bg-yellow-50';
  if (shift === 'ca_toi') return 'border-purple-400 bg-purple-50';
  if (status === 'upcoming') return 'border-[#05df72] bg-green-50';
  if (status === 'ongoing')  return 'border-[#ff8904] bg-orange-50';
  return 'border-gray-300 bg-gray-50';
};

// Month view state
const monthViewDate = ref(new Date());
const allMonthShifts = ref([]);

const fetchMonthData = async () => {
  const year  = monthViewDate.value.getFullYear();
  const month = monthViewDate.value.getMonth();
  const first = new Date(year, month, 1);
  const last  = new Date(year, month + 1, 0);
  try {
    const data = await getMySchedule(formatDate(first), formatDate(last));
    if (data?.status && data.data?.schedule) {
      const shifts = [];
      data.data.schedule.forEach((day) => {
        (day.lich_lam_viec || []).forEach((s) => {
          shifts.push({ date: day.date, thoi_gian_truc: s.thoi_gian_truc });
        });
      });
      allMonthShifts.value = shifts;
    } else { allMonthShifts.value = []; }
  } catch { allMonthShifts.value = []; }
};

const goToPreviousMonth = () => {
  const d = new Date(monthViewDate.value);
  d.setMonth(d.getMonth() - 1);
  monthViewDate.value = d;
  fetchMonthData();
};
const goToNextMonth = () => {
  const d = new Date(monthViewDate.value);
  d.setMonth(d.getMonth() + 1);
  monthViewDate.value = d;
  fetchMonthData();
};

const monthGridLeadingBlanks = computed(() => {
  const first = new Date(monthViewDate.value.getFullYear(), monthViewDate.value.getMonth(), 1);
  const dow = first.getDay(); // 0=Sun
  return dow === 0 ? 6 : dow - 1; // Mon-based grid
});

const monthGridDays = computed(() => {
  const year  = monthViewDate.value.getFullYear();
  const month = monthViewDate.value.getMonth();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const days = [];
  for (let d = 1; d <= daysInMonth; d++) {
    const dateStr = `${year}-${String(month + 1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    const shifts = allMonthShifts.value
      .filter(s => s.date === dateStr)
      .map(s => s.thoi_gian_truc);
    days.push({ date: dateStr, dayNum: d, shifts });
  }
  return days;
});

const icons = {
  clipboardCheck:
    "https://www.figma.com/api/mcp/asset/afaa17a8-4dd1-4159-afba-ce6de41be981",
  calendarUser:
    "https://www.figma.com/api/mcp/asset/1d8695a1-d3ce-41f5-be9a-f01d493bd4ff",
  chevronLeft:
    "https://www.figma.com/api/mcp/asset/95893011-66af-46a2-89d0-8c37f38af121",
  calendar:
    "https://www.figma.com/api/mcp/asset/9bf5af36-6033-44b5-bc9f-c9d509e09bbd",
  chevronRight:
    "https://www.figma.com/api/mcp/asset/19f37aaa-5617-4f0f-a2f0-850b473694b0",
  alertCircle:
    "https://www.figma.com/api/mcp/asset/ac86bf5b-9f0d-484a-bc78-f45f1cfae5f1",
  calendarWhite:
    "https://www.figma.com/api/mcp/asset/0061a89d-0be4-4567-82c5-fa1ac2abfd69",
  listBlack:
    "https://www.figma.com/api/mcp/asset/ce93a96a-c48d-45d5-bfd4-9412ada63533",
  calendarBlack:
    "https://www.figma.com/api/mcp/asset/ce93a96a-c48d-45d5-bfd4-9412ada63533",
  listWhite:
    "https://www.figma.com/api/mcp/asset/0061a89d-0be4-4567-82c5-fa1ac2abfd69",
  mapPin:
    "https://www.figma.com/api/mcp/asset/80e0c896-dee8-4a04-98ce-137631eada1a",
  usersGreen:
    "https://www.figma.com/api/mcp/asset/525d3e04-34e1-478f-ab67-dcab0c194926",
  usersOrange:
    "https://www.figma.com/api/mcp/asset/23c7949b-a408-4b98-b4e4-624c65022e04",
  lightbulb:
    "https://www.figma.com/api/mcp/asset/307dc19e-d484-413b-977d-4ce4ececcd6b",
  checkCircleBlue:
    "https://www.figma.com/api/mcp/asset/71657569-34c5-4c7a-8449-a69598eac37c",
  plusCircle:
    "https://www.figma.com/api/mcp/asset/952ab492-d9f1-42fe-80eb-50dbf7b3c167",
  lockIcon:
    "https://www.figma.com/api/mcp/asset/be64e2a4-92fa-4ce0-8fa5-0ade98416436",
  checkCircleGreen:
    "https://www.figma.com/api/mcp/asset/8842afba-a48d-4d12-a65c-2b2a8b3760b5",
  plusCircleGray:
    "https://www.figma.com/api/mcp/asset/20fe5daa-db0a-4643-bb00-6350643d9ec5",
  save: "https://www.figma.com/api/mcp/asset/afd59c54-58a8-4849-bc1c-e61fde5b1006",
  send: "https://www.figma.com/api/mcp/asset/b8ce81d2-9e32-4792-8429-e45c725bc1c4",
  infoCircle:
    "https://www.figma.com/api/mcp/asset/74328a54-dc6a-450b-9316-ee606f99aace",
};

// ─── Leave Request Module ─────────────────────────────────────────────────
const leaveRequests   = ref([]);
const leaveLoading    = ref(false);
const leaveSubmitting = ref(false);
const leaveSubmitMessage = ref('');
const leaveSubmitSuccess = ref(false);
const leaveForm = ref({ start_date: '', end_date: '', ly_do: '' });
const leaveFormErrors = ref({});
const leaveQuota = ref({
  thang: new Date().getMonth() + 1,
  nam: new Date().getFullYear(),
  tong_quota: 4,
  da_su_dung: 0,
  con_lai: 4,
});

const minLeaveDate = computed(() => {
  const d = new Date();
  d.setDate(d.getDate() + 1);
  return d.toISOString().split('T')[0];
});

const fetchLeaveRequests = async () => {
  leaveLoading.value = true;
  try {
    const res = await api.get('/lich-nghi');
    leaveRequests.value = res.data?.data || [];
  } catch {
    leaveRequests.value = [];
  } finally {
    leaveLoading.value = false;
  }
};

const fetchLeaveQuota = async (thang, nam) => {
  const m = thang ?? (new Date().getMonth() + 1);
  const y = nam   ?? new Date().getFullYear();
  try {
    const res = await api.get('/lich-nghi/quota', { params: { thang: m, nam: y } });
    if (res.data?.status) leaveQuota.value = res.data.data;
  } catch { /* ignore */ }
};

const submitLeaveRequest = async () => {
  leaveFormErrors.value = {};
  leaveSubmitMessage.value = '';
  
  if (!leaveForm.value.start_date || !leaveForm.value.end_date) {
    leaveFormErrors.value.date = 'Vui lòng chọn khoảng thời gian xin nghỉ';
    return;
  }
  
  const start = new Date(leaveForm.value.start_date);
  const end = new Date(leaveForm.value.end_date);
  
  if (end < start) {
    leaveFormErrors.value.date = 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu';
    return;
  }

  leaveSubmitting.value = true;
  let successCount = 0;
  let errorMessages = [];

  // Loop through all dates in range and send requests
  let currentDate = new Date(start);
  while (currentDate <= end) {
    const dateString = currentDate.toISOString().split('T')[0];
    
    try {
      await api.post('/lich-nghi', {
        ngay_nghi: dateString,
        ly_do: leaveForm.value.ly_do,
      });
      successCount++;
    } catch (err) {
      const msg = err.response?.data?.errors?.ngay_nghi?.[0] || err.response?.data?.message || 'Lỗi';
      errorMessages.push(`${dateString}: ${msg}`);
    }
    
    currentDate.setDate(currentDate.getDate() + 1);
  }

  if (successCount > 0) {
    leaveSubmitSuccess.value = true;
    leaveSubmitMessage.value = `Đã gửi thành công ${successCount} đơn xin nghỉ.`;
    if (errorMessages.length > 0) {
      leaveSubmitMessage.value += ` Lỗi: ${errorMessages.join(' | ')}`;
    }
    leaveForm.value = { start_date: '', end_date: '', ly_do: '' };
    await fetchLeaveQuota(start.getMonth() + 1, start.getFullYear());
    await fetchLeaveRequests();
  } else {
    leaveSubmitSuccess.value = false;
    leaveSubmitMessage.value = `Không thể gửi đơn xin nghỉ. Lỗi: ${errorMessages.join(' | ')}`;
  }
  
  leaveSubmitting.value = false;
  setTimeout(() => { leaveSubmitMessage.value = ''; }, 5000);
};

const cancelLeaveRequest = async (id) => {
  if (!confirm('Hủy đơn xin nghỉ này?')) return;
  try {
    await api.delete('/lich-nghi/' + id);
    showSuccessToast('Thành công', 'Đã hủy đơn xin nghỉ');
    await fetchLeaveRequests();
    await fetchLeaveQuota();
  } catch {
    showErrorToast('Lỗi', 'Không thể hủy đơn này');
  }
};

const formatDateDisplay2 = (dateStr) => {
  if (!dateStr) return '—';
  const d = new Date(dateStr + 'T00:00:00');
  return d.toLocaleDateString('vi-VN', { weekday: 'short', day: '2-digit', month: '2-digit', year: 'numeric' });
};
// ─── End Leave Request Module ─────────────────────────────────────────────
</script>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f3f3f5;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: #d1d1d6;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a8a8b0;
}

/* Remove default button styling */
button:focus {
  outline: none;
}

/* Font imports */
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap");
</style>
