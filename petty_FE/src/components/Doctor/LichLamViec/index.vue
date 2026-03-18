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
              Tuần {{ weekNumber }} ({{ weekRange }})
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
              <span class="text-sm font-medium"> Lịch Tuần </span>
            </button>
            <!-- <button
              @click="viewMode = 'list'"
              :class="[
                'h-9 px-4 rounded-lg flex items-center gap-2 transition-colors',
                viewMode === 'list'
                  ? 'bg-[#030213] text-white'
                  : 'bg-white border !border-gray-300 text-black hover:bg-gray-50',
              ]"
            >
              <img
                :src="viewMode === 'list' ? icons.listWhite : icons.listBlack"
                alt=""
                class="w-4 h-4"
              />
              <span class="text-sm font-medium"> Danh sách </span>
            </button> -->
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

        <div v-else class="overflow-auto">
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
                        getScheduleCellClass(daySchedule.status),
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
                        getScheduleCellClass(daySchedule[0].status),
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

        <!-- Legend -->
        <div class="bg-gray-50 rounded-[10px] px-4 py-4">
          <p
            class="text-xs font-bold text-[#364153] leading-4 mb-3"
            style="font-family: 'Inter', sans-serif"
          >
            Chú thích:
          </p>
          <div class="flex items-center gap-8">
            <div
              v-for="calendarLegend in calendarLegends"
              :key="calendarLegend.label"
              class="flex items-center gap-2"
            >
              <div
                :class="['w-5 h-5 border-2 rounded', calendarLegend.class]"
              />
              <span
                class="text-xs font-normal text-[#4a5565]"
                style="font-family: 'Inter', sans-serif"
              >
                {{ calendarLegend.label }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Important Notes Card -->
    <div
      v-if="activeTab === 'my-schedule'"
      class="bg-teal-50 border !border-[#b9f8cf] rounded-[14px] px-6 py-4 mt-4"
    >
      <div class="flex gap-3">
        <!-- <div
          class="bg-green-100 rounded-[10px] w-8 h-8 flex items-center justify-center shrink-0 mt-0.5"
        >
           <img :src="icons.lightbulb" alt="" class="w-4 h-4" /> 
        </div> -->
        <div>
          <p
            class="text-sm font-bold text-[#364153] leading-5 tracking-[-0.3125px] mb-1.5"
            style="font-family: 'Inter', sans-serif"
          >
            Hướng dẫn sử dụng:
          </p>
          <ul class="flex flex-col gap-0.5 pl-2">
            <li
              class="text-xs font-normal text-[#364153] leading-4 tracking-[-0.1504px]"
              style="font-family: 'Inter', sans-serif"
            >
              <span class="font-bold">Lịch chính thức</span> đã được Admin
              duyệt.
            </li>
            <li
              class="text-xs font-normal text-[#364153] leading-4 tracking-[-0.1504px]"
              style="font-family: 'Inter', sans-serif"
            >
              Ca <span class="font-bold text-[#ca3500]">Đang diễn ra</span> sẽ
              nhấp nháy.
            </li>
            <li
              class="text-xs font-normal text-[#364153] leading-4 tracking-[-0.1504px]"
              style="font-family: 'Inter', sans-serif"
            >
              Nếu cần, vào tab <span class="font-bold">"Đăng ký ca"</span> để
              đăng ký thêm.
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Registration Tab Content -->
    <div v-else-if="activeTab === 'register-shift'" class="space-y-6">
      <!-- Registration Guide Card -->
      <div
        class="border !border-[#96f7e4] rounded-[14px] px-6 py-6 bg-gradient-to-r from-cyan-50 to-teal-50"
      >
        <div class="flex gap-4">
          <!-- <div
            class="bg-[#cbfbf1] rounded-[10px] w-10 h-10 flex items-center justify-center shrink-0"
          >
            <img :src="icons.infoCircle" alt="" class="w-5 h-5" />
          </div> -->
          <div>
            <p
              class="text-base font-bold text-[#364153] leading-6 tracking-[-0.3125px] mb-3"
              style="font-family: 'Inter', sans-serif"
            >
              Hướng dẫn đăng ký ca làm việc:
            </p>
            <ul class="flex flex-col gap-2 pl-2">
              <li
                class="text-sm font-normal text-[#364153] leading-5 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Chọn ca làm việc từ danh sách có sẵn
              </li>
              <li
                class="text-sm font-normal text-[#364153] leading-5 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Hạn đăng ký: <span class="font-bold">Thứ 6, 17:00</span>
              </li>
              <li
                class="text-sm font-normal text-[#364153] leading-5 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Admin sẽ phê duyệt trong
                <span class="font-bold">24 giờ</span>
              </li>
              <li
                class="text-sm font-normal text-[#364153] leading-5 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Một khi phê duyệt, không thể thay đổi
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Available Shifts Quick Preview -->
      <div
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] px-6 py-6"
      >
        <div class="flex items-center justify-between mb-4">
          <h3
            class="text-lg font-bold text-[#101828]"
            style="font-family: 'Inter', sans-serif"
          >
            Ca trống sắp tới
          </h3>
          <button
            type="button"
            @click="openRegistrationModal"
            class="z-10 cursor-pointer px-4 py-2 bg-[#5a9690] hover:bg-[#5a9690]/80 text-white rounded-lg text-sm font-bold hover:shadow-lg transition"
          >
            + Đăng ký ngay
          </button>
        </div>
        <p
          class="text-sm text-[#6a7282] mb-4"
          style="font-family: 'Inter', sans-serif"
        >
          Nhấn nút bên trên để mở giao diện đăng ký chi tiết
        </p>
      </div>

      <!-- Tips Card -->
      <div
        class="border !border-[#fef3c7] rounded-[14px] px-6 py-6 bg-amber-50"
      >
        <div class="flex gap-4">
          <div>
            <p
              class="text-base font-bold text-[#92400e] leading-6 tracking-[-0.3125px] mb-2"
              style="font-family: 'Inter', sans-serif"
            >
              Mẹo để có lịch tốt:
            </p>
            <ul class="flex flex-col gap-1.5 pl-2">
              <li
                class="text-sm font-normal text-[#92400e] leading-5 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Đăng ký sớm vào Thứ 2 để có lựa chọn nhiều
              </li>
              <li
                class="text-sm font-normal text-[#92400e] leading-5 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Kiểm tra thường xuyên những ca mới
              </li>
              <li
                class="text-sm font-normal text-[#92400e] leading-5 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Liên hệ Admin nếu có vấn đề
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Registered Shifts Table (Ca đã đăng ký) -->
      <div
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] overflow-hidden"
      >
        <div class="px-6 py-6 border-b !border-gray-300">
          <h3
            class="text-lg font-bold text-[#101828]"
            style="font-family: 'Inter', sans-serif"
          >
            Ca đã đăng ký
          </h3>
          <p
            class="text-sm text-[#6a7282] mt-1"
            style="font-family: 'Inter', sans-serif"
          >
            Danh sách tất cả các ca mà bạn đã đăng ký (chờ duyệt, đã duyệt, từ
            chối)
          </p>
        </div>

        <!-- Table Content -->
        <div v-if="registeredShifts.length > 0">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-[#e5e7eb] bg-[#f9fafb]">
                  <th
                    class="px-6 py-4 text-left text-sm font-bold text-[#101828]"
                    style="font-family: 'Inter', sans-serif"
                  >
                    📅 Ngày giờ
                  </th>
                  <th
                    class="px-6 py-4 text-left text-sm font-bold text-[#101828]"
                    style="font-family: 'Inter', sans-serif"
                  >
                    📝 Ghi chú
                  </th>
                  <th
                    class="px-6 py-4 text-left text-sm font-bold text-[#101828]"
                    style="font-family: 'Inter', sans-serif"
                  >
                    👤 Nhân viên
                  </th>
                  <th
                    class="px-6 py-4 text-left text-sm font-bold text-[#101828]"
                    style="font-family: 'Inter', sans-serif"
                  >
                    ⏳ Trạng thái
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="shift in registeredShifts"
                  :key="shift.id"
                  class="border-b border-[#e5e7eb] hover:bg-[#f0f9ff] transition"
                >
                  <td
                    class="px-6 py-4 text-base text-[#364153]"
                    style="font-family: 'Inter', sans-serif"
                  >
                    <span class="font-semibold">{{
                      formatDateTime(shift.ngay_gio)
                    }}</span>
                  </td>
                  <td
                    class="px-6 py-4 text-sm font-semibold text-[#364153] max-w-xs"
                    style="font-family: 'Inter', sans-serif"
                  >
                    <div class="break-words line-clamp-2">
                      {{ shift.ghi_chu || "—" }}
                    </div>
                  </td>
                  <td
                    class="px-6 py-4 text-base text-[#364153]"
                    style="font-family: 'Inter', sans-serif"
                  >
                    <span class="font-semibold">{{
                      shift.full_name ||
                      shift.ten_nhan_vien ||
                      shift.nhan_vien?.full_name ||
                      shift.nhan_vien?.ho_ten ||
                      "N/A"
                    }}</span>
                  </td>
                  <td
                    class="px-6 py-4 text-sm"
                    style="font-family: 'Inter', sans-serif"
                  >
                    <span
                      :class="[
                        'px-3 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap',
                        shift.trang_thai === 'pending' ||
                        shift.trang_thai === 'chưa_xác_nhận' ||
                        shift.trang_thai === 'chua_xac_nhan'
                          ? 'bg-yellow-100 text-yellow-800'
                          : shift.trang_thai === 'approved' ||
                            shift.trang_thai === 'confirmed' ||
                            shift.trang_thai === 'da_xac_nhan' ||
                            shift.trang_thai === 'đã_xác_nhận'
                          ? 'bg-green-100 text-green-800'
                          : 'bg-red-100 text-red-800',
                      ]"
                    >
                      {{
                        shift.trang_thai === "pending" ||
                        shift.trang_thai === "chưa_xác_nhận" ||
                        shift.trang_thai === "chua_xac_nhan"
                          ? "⏳ Chờ duyệt"
                          : shift.trang_thai === "approved" ||
                            shift.trang_thai === "confirmed" ||
                            shift.trang_thai === "da_xac_nhan" ||
                            shift.trang_thai === "đã_xác_nhận"
                          ? "✅ Đã duyệt"
                          : shift.trang_thai === "rejected" ||
                            shift.trang_thai === "tu_choi" ||
                            shift.trang_thai === "từ_chối"
                          ? "❌ Từ chối"
                          : shift.trang_thai
                      }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="px-6 py-12 text-center">
          <div class="flex justify-center mb-4">
            <div
              class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center"
            >
              <svg
                class="w-8 h-8 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
          </div>
          <p
            class="text-gray-600 font-semibold mb-2"
            style="font-family: 'Inter', sans-serif"
          >
            Chưa đăng ký ca nào
          </p>
          <p
            class="text-gray-500 text-sm"
            style="font-family: 'Inter', sans-serif"
          >
            Nhấn nút "Đăng ký ngay" ở trên để bắt đầu
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Registration Modal -->
  <DangKyCa
    :is-open="showRegisterModal"
    @close="showRegisterModal = false"
    @success="
      () => {
        showRegisterModal = false;
        fetchScheduleData();
        fetchRegisteredShifts();
        showSuccessToast('Thành công', 'Đăng ký ca làm việc thành công');
      }
    "
  />
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
  getCaDaXacNhanCuaToi,
} from "@/services/lichDangKyService";
import api from "@/utils/api";
import DangKyCa from "./DangKyCa/index.vue";

const activeTab = ref("my-schedule");
const loading = ref(false);
const currentUser = ref(null);

// Modal state
const showRegisterModal = ref(false);

const tabs = [
  {
    id: "my-schedule",
    label: "Lịch của tôi",
    icon: "calendarUser",
    left: "0px",
  },
  {
    id: "register-shift",
    label: "Đăng ký ca",
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

          console.log(
            `✅ Adding approved shift to day ${itemDateStr} (${dayIndex}):`,
            roomName
          );

          // ✅ Make sure we have 4 time slots
          if (!timeSlots.value[3]) {
            timeSlots.value[3] = {
              name: "Ca đăng ký",
              time: "Đã duyệt",
              schedule: [null, null, null, null, null, null, null],
            };
          }

          const shiftData = {
            id: item.id,
            room: roomName,
            patients: 0, // Ca đăng ký không có lịch hẹn
            status: getShiftStatus(itemDateStr, "ca_dang_ky"),
            appointments: [],
            date: itemDate,
            shift: "ca_dang_ky",
            ghi_chu: item.ghi_chu,
            isRegistered: true, // Đánh dấu đây là ca đăng ký
          };

          // Handle multiple shifts in same day
          if (timeSlots.value[3].schedule[dayIndex]) {
            const existing = timeSlots.value[3].schedule[dayIndex];
            if (Array.isArray(existing)) {
              existing.push(shiftData);
            } else {
              timeSlots.value[3].schedule[dayIndex] = [existing, shiftData];
            }
          } else {
            timeSlots.value[3].schedule[dayIndex] = shiftData;
          }
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
        // ca_sang: 4h (8-12), ca_chieu: 4h (13-17), ca_toi: 3h (18-21)
        if (shift.thoi_gian_truc === "ca_sang") totalHoursCalc += 4;
        else if (shift.thoi_gian_truc === "ca_chieu") totalHoursCalc += 4;
        else if (shift.thoi_gian_truc === "ca_toi") totalHoursCalc += 3;
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
  const mapping = {
    ca_sang: 0,
    ca_chieu: 1,
    ca_toi: 2,
  };
  return mapping[thoi_gian_truc] !== undefined ? mapping[thoi_gian_truc] : -1;
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
    if (shift === "ca_sang" && hour >= 8 && hour < 12) return "ongoing";
    if (shift === "ca_chieu" && hour >= 13 && hour < 17) return "ongoing";
    if (shift === "ca_toi" && hour >= 18 && hour < 21) return "ongoing";
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
  if (newTab === "my-schedule") {
    fetchScheduleData();
  } else if (newTab === "register-shift") {
    fetchRegisteredShifts();
  }
  // Không tự động mở modal, cho user bấm nút "Đăng ký ngay"
});

const timeSlots = ref([
  {
    name: "Sáng",
    time: "8h-12h",
    schedule: [null, null, null, null, null, null, null],
  },
  {
    name: "Chiều",
    time: "13h-17h",
    schedule: [null, null, null, null, null, null, null],
  },
  {
    name: "Tối",
    time: "18h-21h",
    schedule: [null, null, null, null, null, null, null],
  },
  {
    name: "Ca đăng ký",
    time: "Đã duyệt",
    schedule: [null, null, null, null, null, null, null],
  },
]);

const calendarLegends = [
  {
    label: "Sắp tới",
    class: "border-[#05df72] bg-green-50",
    status: "upcoming",
  },
  {
    label: "Đang diễn ra",
    class: "border-[#ff8904] bg-orange-50",
    status: "ongoing",
  },
  { label: "Đã qua", class: "border-gray-300 bg-gray-50", status: "past" },
];

// Helper function to get schedule cell class based on status
const getScheduleCellClass = (status) => {
  const legend = calendarLegends.find((l) => l.status === status);
  return legend ? legend.class : "";
};

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
