<template>
  <div class="w-full min-h-screen px-8 py-6">
    <!-- Header Section -->
    <div class="flex flex-col gap-2">
      <h1 class="text-2xl font-semibold text-black">Phân ca làm việc</h1>
      <p class="text-gray-500 font-medium text-base">
        Quản lý lịch làm việc và lịch hẹn cho nhân viên
      </p>
    </div>

    <!-- Control Panel -->
    <div
      class="bg-white rounded-xl border !border-gray-300 p-6 mb-6 mt-6 shadow-sm hover:shadow-md transition-shadow"
    >
      <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
        <!-- Left: Navigation -->
        <div class="flex items-center gap-3">
          <button
            @click="previousWeek"
            class="p-2 rounded-lg border !border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition"
            title="Tuần trước"
          >
            <ChevronLeftIcon />
          </button>

          <div
            class="bg-gradient-to-r from-teal-50 to-cyan-50 px-6 py-2 rounded-lg font-medium text-sm text-gray-800 border !border-teal-200 min-w-max"
          >
            {{ currentWeekRange }}
          </div>

          <button
            @click="nextWeek"
            class="p-2 rounded-lg border !border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition"
            title="Tuần sau"
          >
            <ChevronRightIcon />
          </button>

          <button
            @click="goToToday"
            class="flex items-center gap-2 px-4 py-2 rounded-lg border border-teal-200 bg-teal-50 hover:bg-teal-100 transition text-teal-700 font-medium text-sm"
          >
            Hôm nay
          </button>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-3 w-full lg:w-auto">
          <div class="relative flex-1 lg:flex-none">
            <input
              type="text"
              placeholder="Tìm nhân viên..."
              class="w-full lg:w-48 pl-10 pr-4 py-2 bg-gray-50 border !border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition"
            />
            <SearchIcon
              class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
            />
          </div>

          <button
            @click="handleAddShift"
            class="bg-[#5a9690] hover:bg-[#5a9690]/80 text-white px-5 py-2 rounded-lg flex items-center gap-2 font-medium text-sm transition shadow-sm hover:shadow-md"
          >
            <AddIcon />
            Thêm ca
          </button>
        </div>
      </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="mb-6 flex gap-2 border-b !border-gray-200">
      <button
        @click="activeTab = 'schedule'"
        :class="[
          'px-6 py-3 font-medium text-sm transition-all',
          activeTab === 'schedule'
            ? 'text-teal-500 border-b-2 border-teal-500'
            : 'text-gray-600 hover:text-gray-900',
        ]"
      >
        <span class="flex items-center gap-2">
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
            />
          </svg>
          Lịch làm việc
        </span>
      </button>
      <button
        @click="activeTab = 'confirmations'"
        :class="[
          'px-6 py-3 font-medium text-sm transition-all',
          activeTab === 'confirmations'
            ? 'text-teal-500 border-b-2 border-teal-500'
            : 'text-gray-600 hover:text-gray-900',
        ]"
      >
        <span class="flex items-center gap-2">
          <svg
            class="w-4 h-4"
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
          Xác nhận đăng ký
        </span>
      </button>
    </div>

    <!-- Schedule Tab Content -->
    <div v-if="activeTab === 'schedule'">
      <!-- Summary Section -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Overall Total -->
        <div
          class="bg-white border !border-gray-300 rounded-lg p-5 shadow-sm hover:shadow-md transition"
        >
          <p class="text-gray-600 text-sm font-medium mb-2">Tổng giờ tuần</p>
          <p class="text-3xl font-bold text-teal-600">
            {{ overallTotal }}<span class="text-lg text-gray-400">h</span>
          </p>
        </div>

        <!-- Day Totals -->
        <template v-for="day in weekDays" :key="day.dateStr">
          <div
            :class="[
              'bg-white border !border-gray-300 rounded-lg p-4 shadow-sm hover:shadow-md transition text-center',
              day.isToday
                ? 'border-teal-300 bg-gradient-to-br from-teal-50 to-cyan-50'
                : 'border-gray-200',
            ]"
          >
            <p
              :class="[
                'text-xs font-bold mb-1',
                day.isToday ? 'text-teal-700' : 'text-gray-600',
              ]"
            >
              {{ day.dayName }}
            </p>
            <p
              :class="[
                'text-lg font-bold mb-2',
                day.isToday ? 'text-teal-600' : 'text-gray-900',
              ]"
            >
              {{ day.date }}
            </p>
            <p class="text-sm font-semibold text-gray-600">
              {{ totalsPerDay[day.dateStr] || 0
              }}<span class="text-xs text-gray-400">h</span>
            </p>
          </div>
        </template>
      </div>

      <!-- Empty State -->
      <div v-if="!isLoading && !hasAnyShift" class="mb-6">
        <div
          class="bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-lg flex items-start gap-3"
        >
          <svg
            class="w-5 h-5 mt-0.5 flex-shrink-0"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 9v2m0 4v2m0 4v2M7.08 6.47A9.968 9.968 0 0112 1c4.97 0 9 4.03 9 9s-4.03 9-9 9S3 17 3 12c0-2.395.904-4.577 2.391-6.22"
            />
          </svg>
          <div>
            <p class="font-semibold">Không có dữ liệu</p>
            <p class="text-sm mt-1">
              Chưa có ca làm việc hoặc lịch hẹn trong tuần này
            </p>
          </div>
        </div>
      </div>

      <!-- Schedule Table -->
      <div
        class="bg-white rounded-lg border !border-gray-300 shadow-sm overflow-hidden"
      >
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr
                class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200"
              >
                <th
                  class="text-left px-6 py-4 sticky left-0 bg-gradient-to-r from-gray-50 to-gray-100 z-10 border-r border-gray-200 font-semibold text-sm text-gray-900"
                >
                  Nhân viên
                </th>
                <th
                  v-for="day in weekDays"
                  :key="day.dateStr"
                  :class="[
                    'px-5 py-4 text-center border-r border-gray-200 font-semibold text-sm',
                    day.isToday ? 'bg-teal-50 text-teal-900' : 'text-gray-900',
                  ]"
                >
                  <div class="flex flex-col items-center">
                    <span class="text-xs font-bold text-gray-600">{{
                      day.dayName
                    }}</span>
                    <span class="text-lg font-bold mt-1">{{ day.date }}</span>
                    <span
                      class="text-xs mt-2 font-semibold"
                      :class="day.isToday ? 'text-teal-600' : 'text-gray-500'"
                    >
                      {{ totalsPerDay[day.dateStr] || 0 }}h
                    </span>
                  </div>
                </th>
                <th
                  class="px-6 py-4 text-center font-semibold text-sm text-gray-900 bg-gradient-to-l from-gray-50 to-gray-100"
                >
                  Tổng giờ
                </th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="staff in staffList"
                :key="staff.id"
                class="border-b border-gray-100 hover:bg-gray-50 transition group"
              >
                <!-- Staff Info -->
                <td
                  class="px-6 py-5 border-r border-gray-100 sticky left-0 bg-white group-hover:bg-gray-50 z-10"
                >
                  <div class="flex items-center gap-3">
                    <img
                      :src="staff.avatar"
                      :alt="staff.name"
                      class="w-10 h-10 rounded-full object-cover border-2 border-gray-200"
                    />
                    <div>
                      <p class="font-semibold text-sm text-gray-900">
                        {{ staff.name }}
                      </p>
                      <p class="text-xs text-gray-500 mt-1">{{ staff.role }}</p>
                    </div>
                  </div>
                </td>

                <!-- Days -->
                <td
                  v-for="day in weekDays"
                  :key="day.dateStr"
                  :class="[
                    'border-r border-gray-100 px-3 py-4',
                    day.isToday ? 'bg-teal-50/50' : '',
                  ]"
                >
                  <div class="min-h-32">
                    <div
                      @click="addShiftForStaff(staff, day)"
                      class="h-full space-y-2 rounded-lg border-2 border-dashed border-gray-200 hover:border-teal-300 hover:bg-teal-50/30 transition-all cursor-pointer p-2"
                      :class="{
                        'border-teal-300 bg-teal-50':
                          hasShift(staff, day.dateStr) ||
                          getAppointmentsForCell(staff, day.dateStr).length > 0,
                      }"
                    >
                      <!-- Shifts -->
                      <template
                        v-if="getShiftsForCell(staff, day.dateStr).length"
                      >
                        <div
                          v-for="shift in getShiftsForCell(staff, day.dateStr)"
                          :key="shift.id"
                          class="bg-white rounded-md border-l-4 p-2 text-xs hover:shadow-sm transition"
                          :class="shiftBorderClass(shift)"
                        >
                          <div class="font-semibold text-gray-800 truncate">
                            {{ shift.phong_truc || "Phòng" }}
                          </div>
                          <span
                            :class="[
                              'inline-block mt-1 px-2 py-0.5 rounded text-white text-xs font-bold',
                              shift.thoi_gian_truc === 'ca_sang'
                                ? 'bg-emerald-500'
                                : shift.thoi_gian_truc === 'ca_chieu'
                                ? 'bg-amber-500'
                                : shift.thoi_gian_truc === 'ca_dang_ky'
                                ? 'bg-blue-500'
                                : 'bg-slate-600',
                            ]"
                          >
                            {{
                              shift.thoi_gian_truc === "ca_sang"
                                ? "Sáng"
                                : shift.thoi_gian_truc === "ca_chieu"
                                ? "Chiều"
                                : shift.thoi_gian_truc === "ca_dang_ky"
                                ? "Đăng ký"
                                : "Tối"
                            }}
                          </span>
                          <!-- Show notes for registered shifts -->
                          <div
                            v-if="shift.ghi_chu"
                            class="text-gray-600 mt-1 text-xs italic line-clamp-2"
                          >
                            {{ shift.ghi_chu }}
                          </div>
                        </div>
                      </template>

                      <!-- Appointments -->
                      <template
                        v-if="getAppointmentsForCell(staff, day.dateStr).length"
                      >
                        <div
                          v-for="appointment in getAppointmentsForCell(
                            staff,
                            day.dateStr
                          )"
                          :key="appointment.id"
                          class="bg-blue-50 rounded-md border-l-4 border-blue-400 p-2 text-xs hover:shadow-sm transition"
                        >
                          <div class="font-semibold text-blue-900 truncate">
                            {{ appointment.customer }}
                          </div>
                          <div class="text-blue-700 truncate mt-1 text-xs">
                            {{ appointment.service }}
                          </div>
                        </div>
                      </template>
                    </div>
                  </div>
                </td>

                <!-- Total Hours -->
                <td class="px-6 py-5 text-center">
                  <span
                    class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 rounded-lg text-lg font-bold text-gray-900"
                  >
                    {{ staff.totalHours
                    }}<span class="text-sm text-gray-500 ml-1">h</span>
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Confirmations Tab Content -->
    <div v-if="activeTab === 'confirmations'" class="space-y-6">
      <!-- Filter Section -->
      <div class="bg-white rounded-lg border !border-gray-300 p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row gap-4 items-end">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Trạng thái
            </label>
            <select
              v-model="confirmationFilter"
              class="w-full px-4 py-2 border !border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
            >
              <option value="">Tất cả</option>
              <option value="pending">Chờ xác nhận</option>
              <option value="confirmed">Đã xác nhận</option>
              <option value="rejected">Từ chối</option>
            </select>
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Tìm nhân viên
            </label>
            <input
              v-model="confirmationSearch"
              type="text"
              placeholder="Nhập tên nhân viên..."
              class="w-full px-4 py-2 border !border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
            />
          </div>
        </div>
      </div>

      <!-- Confirmations List -->
      <div
        v-if="filteredConfirmations.length === 0"
        class="text-center py-12 bg-white rounded-lg border !border-gray-300"
      >
        <svg
          class="w-16 h-16 mx-auto text-gray-300 mb-4"
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
        <p class="text-gray-500 font-medium">Không có yêu cầu xác nhận nào</p>
      </div>

      <!-- Table View -->
      <div
        v-else
        class="bg-white rounded-lg border !border-gray-300 overflow-hidden shadow-sm"
      >
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50 border-b !border-gray-300">
                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">
                  Nhân viên
                </th>
                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">
                  Ngày giờ
                </th>
                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">
                  Ghi chú
                </th>
                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">
                  Trạng thái
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="confirmation in filteredConfirmations"
                :key="confirmation.id"
                class="border-b border-gray-200 hover:bg-gray-50 transition"
              >
                <!-- Nhân viên -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img
                      :src="confirmation.avatar"
                      :alt="confirmation.staffName"
                      class="w-10 h-10 rounded-full object-cover border border-gray-200"
                    />
                    <div>
                      <p class="text-sm font-bold text-gray-900">
                        {{ confirmation.staffName }}
                      </p>
                      <p class="text-xs text-gray-500">
                        {{ confirmation.role }}
                      </p>
                    </div>
                  </div>
                </td>

                <!-- Ngày giờ -->
                <td class="px-6 py-4">
                  <p class="text-sm font-semibold text-gray-900">
                    {{ confirmation.dateTime }}
                  </p>
                </td>

                <!-- Ghi chú -->
                <td class="px-6 py-4">
                  <p class="text-sm text-gray-600">
                    {{ confirmation.note || "—" }}
                  </p>
                </td>

                <!-- Trạng thái -->
                <td class="px-6 py-4">
                  <div
                    v-if="editingStatusId === confirmation.id"
                    class="relative w-48"
                  >
                    <select
                      :value="
                        confirmation.status === 'pending'
                          ? 'chua_xac_nhan'
                          : confirmation.status === 'confirmed'
                          ? 'da_xac_nhan'
                          : 'tu_choi'
                      "
                      @change="
                        (e) =>
                          updateStatusForConfirmation(
                            confirmation.id,
                            e.target.value
                          )
                      "
                      :disabled="updatingStatusId === confirmation.id"
                      class="w-full px-3 py-2 border !border-gray-300 rounded-lg text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <option value="chua_xac_nhan">Chờ duyệt</option>
                      <option value="da_xac_nhan">Đã duyệt</option>
                      <option value="tu_choi">Từ chối</option>
                    </select>
                  </div>
                  <button
                    v-else
                    @click="editingStatusId = confirmation.id"
                    :class="[
                      'inline-flex items-center px-3 py-1 rounded-full text-xs font-bold cursor-pointer transition-all hover:opacity-80',
                      confirmation.status === 'pending'
                        ? 'bg-yellow-100 text-yellow-800'
                        : confirmation.status === 'confirmed' ||
                          confirmation.status === 'đã_xác_nhận'
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800',
                    ]"
                  >
                    {{
                      confirmation.status === "pending"
                        ? "Chờ duyệt"
                        : confirmation.status === "confirmed" ||
                          confirmation.status === "đã_xác_nhận"
                        ? "Đã duyệt"
                        : "Từ chối"
                    }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <PhanCaTruc
      v-if="isAddShiftModalOpen"
      :preselected-staff="selectedStaffForShift"
      :preselected-date="selectedDateForShift"
      :serverErrors="serverErrors"
      :saving="isSavingShift"
      @close="isAddShiftModalOpen = false"
      @save="handleSaveShift"
    />

    <!-- Confirmation Detail Modal -->
    <XacNhanDangKy
      v-if="isConfirmationModalOpen && selectedConfirmation"
      :confirmation="selectedConfirmation"
      :is-processing="isProcessingConfirmation"
      @close="isConfirmationModalOpen = false"
      @approve="handleApproveFromModal"
      @reject="handleRejectFromModal"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import PhanCaTruc from "./PhanCaTruc/index.vue";
import XacNhanDangKy from "./XacNhanDangKy/index.vue";
import api from "@/utils/api";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
//Icon SVG
import ChevronLeftIcon from "@/assets/svg/chevron-left.svg";
import ChevronRightIcon from "@/assets/svg/chevron-right.svg";
import CalendarIcon from "@/assets/svg/calendar.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import SearchIcon from "@/assets/svg/search.svg";
import AddIcon from "@/assets/svg/add.svg";
import CloseIcon from "@/assets/svg/close.svg";

// State
const staffList = ref([]);
const currentWeekStart = ref(new Date());
const shifts = ref({}); // lich_lam_viec (ca làm việc)
const appointments = ref({}); // lich_hen (lịch hẹn khách hàng)
const totalsPerDay = ref({});
const overallTotal = ref(0);
const isLoading = ref(false);

// Tabs
const activeTab = ref("schedule");

// Confirmations
const confirmations = ref([]);
const confirmationFilter = ref("");
const confirmationSearch = ref("");
const selectedConfirmation = ref(null);
const isConfirmationModalOpen = ref(false);
const isProcessingConfirmation = ref(false);
const editingStatusId = ref(null);
const updatingStatusId = ref(null);

// Status options
const statusOptions = [
  { value: "chua_xac_nhan", label: "Chờ duyệt" },
  { value: "da_xac_nhan", label: "Đã duyệt" },
  { value: "tu_choi", label: "Từ chối" },
];

// Modal
const isAddShiftModalOpen = ref(false);
const selectedStaffForShift = ref(null);
const selectedDateForShift = ref("");
const isSavingShift = ref(false);
const serverErrors = ref({});

// Helpers
const formatDate = (date) => {
  const d = new Date(date);
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(
    2,
    "0"
  )}-${String(d.getDate()).padStart(2, "0")}`;
};

// Normalize date key to YYYY-MM-DD (handles ISO datetimes returned from API)
// Ensure we use the correct date in Vietnam timezone
const normalizeDateKey = (d) => {
  if (!d && d !== 0) return d;
  if (typeof d === "string") {
    // If it's an ISO string with T (datetime), parse it properly
    if (d.includes("T")) {
      // Parse the datetime and get the date part, then format to YYYY-MM-DD
      const date = new Date(d);
      return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(
        2,
        "0"
      )}-${String(date.getDate()).padStart(2, "0")}`;
    }
    // If it's already in YYYY-MM-DD format, return as is
    return d;
  }
  return formatDate(d);
};

const currentWeekRange = computed(() => {
  const start = new Date(currentWeekStart.value);
  const end = new Date(start);
  end.setDate(start.getDate() + 6);
  const fmt = (d) =>
    `${String(d.getDate()).padStart(2, "0")}/${String(
      d.getMonth() + 1
    ).padStart(2, "0")}`;
  return `${fmt(start)} - ${fmt(end)}`;
});

const weekDays = computed(() => {
  const days = [];
  const names = ["T2", "T3", "T4", "T5", "T6", "T7", "CN"];
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  for (let i = 0; i < 7; i++) {
    const date = new Date(currentWeekStart.value);
    date.setDate(date.getDate() + i);
    const dateStr = formatDate(date);
    const isToday = formatDate(date) === formatDate(today);

    days.push({
      dayName: names[i],
      date: `${String(date.getDate()).padStart(2, "0")}/${String(
        date.getMonth() + 1
      ).padStart(2, "0")}`,
      dateStr,
      isToday,
    });
  }
  return days;
});

const hasAnyShift = computed(() => {
  // Check if any staff has totalHours > 0 OR if there are any appointments
  if (Object.keys(totalsPerDay.value).length) return true;
  if (Object.keys(appointments.value).length) return true; // Check appointments too
  return staffList.value.some((s) => (s.totalHours || 0) > 0);
});

const filteredConfirmations = computed(() => {
  return confirmations.value.filter((c) => {
    const matchStatus =
      !confirmationFilter.value || c.status === confirmationFilter.value;
    const matchSearch =
      !confirmationSearch.value ||
      c.staffName
        .toLowerCase()
        .includes(confirmationSearch.value.toLowerCase());
    return matchStatus && matchSearch;
  });
});

const hasShift = (staff, dateStr) =>
  getShiftsForCell(staff, dateStr).length > 0;
const getShiftsForCell = (staff, dateStr) =>
  (shifts.value[staff.id] || {})[dateStr] || [];

const getAppointmentsForCell = (staff, dateStr) =>
  (appointments.value[staff.id] || {})[dateStr] || [];

const shiftBorderClass = (shift) => {
  if (shift.thoi_gian_truc === "ca_sang") return "border-emerald-400";
  if (shift.thoi_gian_truc === "ca_chieu") return "border-amber-400";
  if (shift.thoi_gian_truc === "ca_dang_ky") return "border-blue-400";
  return "border-slate-700";
};

// Fetch & Calculate
const fetchStaff = async () => {
  try {
    const res = await api.get("/nhan-vien", { params: { per_page: 500 } });
    const data = res.data?.data?.data || res.data?.data || [];
    staffList.value = data.map((s) => ({
      id: s.id,
      name: s.ho_ten || s.full_name || "Không tên",
      role: s.chuc_vu || s.vai_tro || "Nhân viên",
      avatar:
        s.avatar ||
        `https://ui-avatars.com/api/?name=${encodeURIComponent(
          s.ho_ten || "N"
        )}&background=0D8ABC&color=fff`,
      totalHours: 0,
    }));
  } catch (e) {
    showErrorToast("Lỗi", "Không tải được nhân viên");
  }
};

const fetchShiftsForWeek = async () => {
  isLoading.value = true;
  try {
    const dates = weekDays.value.map((d) => d.dateStr);
    const requests = dates.map((d) =>
      api.get("/lich-lam-viec", { params: { ngay_lam: d, per_page: 200 } })
    );
    const responses = await Promise.all(requests);

    const newShifts = {};
    const dayTotals = {};
    let totalAll = 0;

    responses.forEach((res) => {
      const items = res.data?.data?.data || res.data?.data || [];
      items.forEach((item) => {
        const sid = item.nhan_vien_id || item.nhanvien_id;
        const rawDate = item.ngay_lam || item.date || "";
        const date = normalizeDateKey(rawDate);
        if (!sid || !date) return;

        if (!newShifts[sid]) newShifts[sid] = {};
        if (!newShifts[sid][date]) newShifts[sid][date] = [];
        newShifts[sid][date].push(item);

        const hours = item.thoi_gian_truc === "ca_toi" ? 12 : 8;
        dayTotals[date] = (dayTotals[date] || 0) + hours;
        totalAll += hours;
      });
    });

    // Fetch approved registered shifts (ca đã duyệt từ đăng ký)
    try {
      const approvedRes = await api.get("/lich-dang-ky", {
        params: { per_page: 500 },
      });
      const approvedShifts =
        approvedRes.data?.data?.data || approvedRes.data?.data || [];

      approvedShifts.forEach((item) => {
        // Only include shifts with approved status
        const status = item.trang_thai || "";
        if (
          status !== "da_xac_nhan" &&
          status !== "confirmed" &&
          status !== "đã_xác_nhận"
        ) {
          return;
        }

        const sid = item.nhan_vien_id;
        const rawDate = item.ngay_gio || "";
        const date = normalizeDateKey(rawDate);

        if (!sid || !date) return;

        if (!newShifts[sid]) newShifts[sid] = {};
        if (!newShifts[sid][date]) newShifts[sid][date] = [];

        // Mark as registered shift with all necessary info
        const shiftData = {
          ...item,
          id: item.id,
          phong_truc: `Đăng ký: ${new Date(item.ngay_gio).toLocaleTimeString(
            "vi-VN",
            { hour: "2-digit", minute: "2-digit" }
          )}`,
          thoi_gian_truc: "ca_dang_ky", // Mark as registered shift
          is_registered: true,
          ghi_chu: item.ghi_chu || "",
        };
        newShifts[sid][date].push(shiftData);

        // Add 8 hours for each registered shift
        dayTotals[date] = (dayTotals[date] || 0) + 8;
        totalAll += 8;
      });
    } catch (err) {
      console.error("Error fetching approved registered shifts:", err);
    }

    shifts.value = newShifts;
    totalsPerDay.value = dayTotals;
    overallTotal.value = totalAll;

    // Update total per staff
    staffList.value.forEach((staff) => {
      let h = 0;
      weekDays.value.forEach((day) => {
        const dayShifts = getShiftsForCell(staff, day.dateStr);
        dayShifts.forEach(
          (s) =>
            (h +=
              s.thoi_gian_truc === "ca_toi"
                ? 12
                : s.thoi_gian_truc === "ca_dang_ky"
                ? 8
                : 8)
        );
      });
      staff.totalHours = h;
    });

    // Also fetch appointments
    await fetchAppointmentsForWeek();

    // Calculate totals INCLUDING appointments (mỗi lịch hẹn = 1 giờ)
    let updatedDayTotals = { ...dayTotals };
    let updatedOverallTotal = totalAll;

    Object.keys(appointments.value).forEach((staffId) => {
      Object.keys(appointments.value[staffId]).forEach((dateStr) => {
        const appointmentCount = appointments.value[staffId][dateStr].length;
        updatedDayTotals[dateStr] =
          (updatedDayTotals[dateStr] || 0) + appointmentCount;
        updatedOverallTotal += appointmentCount;
      });
    });

    totalsPerDay.value = updatedDayTotals;
    overallTotal.value = updatedOverallTotal;
  } catch (e) {
    console.error("Lỗi tải lịch làm việc", e);
  } finally {
    isLoading.value = false;
  }
};

// Fetch appointments for the week
const fetchAppointmentsForWeek = async () => {
  try {
    const startDate = weekDays.value[0].dateStr;
    const endDate = weekDays.value[6].dateStr;

    const res = await api.get("/lich-hen", {
      params: { per_page: 500 },
    });

    const allAppointments = res.data?.data?.data || res.data?.data || [];
    const newAppointments = {};

    allAppointments.forEach((item) => {
      const sid = item.nhan_vien_id; // Bác sĩ được phân công
      const rawDate = item.ngay_gio || "";
      const date = normalizeDateKey(rawDate);

      if (!sid || !date) return;

      const itemDate = date.substring(0, 10); // Lấy phần ngày (YYYY-MM-DD)
      if (itemDate < startDate || itemDate > endDate) return;

      if (!newAppointments[sid]) newAppointments[sid] = {};
      if (!newAppointments[sid][itemDate]) newAppointments[sid][itemDate] = [];

      // Transform appointment data to get customer name and service name
      const transformedAppointment = {
        id: item.id,
        customer:
          typeof item.khach_hang === "string"
            ? item.khach_hang
            : item.khach_hang?.ho_ten ||
              item.khach_hang?.full_name ||
              item.khach_hang?.name ||
              "Khách hàng",
        service: item.dich_vu?.ten || item.dich_vu?.name || "Dịch vụ",
        pet: item.thu_cung?.ten_thu_cung || item.thu_cung?.name || "Thú cưng",
        time: item.ngay_gio
          ? new Date(item.ngay_gio).toLocaleTimeString("vi-VN", {
              hour: "2-digit",
              minute: "2-digit",
            })
          : "",
        status: item.trang_thai,
      };

      newAppointments[sid][itemDate].push(transformedAppointment);
    });

    appointments.value = newAppointments;
  } catch (e) {
    console.error("Lỗi tải lịch hẹn", e);
  }
};

// Navigation
const previousWeek = () =>
  (currentWeekStart.value = new Date(
    currentWeekStart.value.setDate(currentWeekStart.value.getDate() - 7)
  ));
const nextWeek = () =>
  (currentWeekStart.value = new Date(
    currentWeekStart.value.setDate(currentWeekStart.value.getDate() + 7)
  ));
const goToToday = () => {
  const today = new Date();
  const diff = today.getDay() === 0 ? -6 : 1 - today.getDay();
  currentWeekStart.value = new Date(today.setDate(today.getDate() + diff));
};

// Modal
const addShiftForStaff = (staff, day) => {
  selectedStaffForShift.value = staff;
  selectedDateForShift.value = day.dateStr;
  isAddShiftModalOpen.value = true;
};

const handleAddShift = () => {
  selectedStaffForShift.value = null;
  selectedDateForShift.value = "";
  isAddShiftModalOpen.value = true;
};

const handleSaveShift = async (data) => {
  isSavingShift.value = true;
  try {
    await api.post("/lich-lam-viec", {
      nhan_vien_id: data.staff.id,
      ngay_lam: data.date,
      thoi_gian_truc:
        data.shift === "morning"
          ? "ca_sang"
          : data.shift === "afternoon"
          ? "ca_chieu"
          : "ca_toi",
      phong_truc: data.room,
    });
    showSuccessToast("Thành công", "Đã thêm ca làm việc");
    isAddShiftModalOpen.value = false;
    await fetchShiftsForWeek();
  } catch (e) {
    if (e.response?.status === 422) {
      serverErrors.value = e.response.data.errors || {};
      showErrorToast("Lỗi", "Vui lòng kiểm tra lại thông tin");
    }
  } finally {
    isSavingShift.value = false;
  }
};

// Fetch confirmations
const fetchConfirmations = async () => {
  try {
    const res = await api.get("/lich-dang-ky/chua-xac-nhan", {
      params: { per_page: 500 },
    });
    const items = res.data?.data?.data || res.data?.data || [];

    confirmations.value = items.map((item) => {
      // Normalize status from BE
      let status = item.trang_thai || "pending";
      if (status === "chưa_xác_nhận" || status === "chua_xac_nhan") {
        status = "pending";
      } else if (status === "đã_xác_nhận" || status === "da_xac_nhan") {
        status = "confirmed";
      } else if (status === "từ_chối" || status === "tu_choi") {
        status = "rejected";
      }

      return {
        id: item.id,
        staffName: item.full_name || item.ten_nhan_vien || "Không tên",
        avatar: `https://ui-avatars.com/api/?name=${encodeURIComponent(
          item.full_name || item.ten_nhan_vien || "N"
        )}&background=0D8ABC&color=fff`,
        role: "Nhân viên",
        date: new Date(item.ngay_gio || item.ngay).toLocaleDateString("vi-VN"),
        dateTime: new Date(item.ngay_gio || item.ngay).toLocaleString("vi-VN", {
          year: "numeric",
          month: "2-digit",
          day: "2-digit",
          hour: "2-digit",
          minute: "2-digit",
        }),
        shift: "morning",
        room: "Phòng chung",
        hours: 8,
        note: item.ghi_chu || "",
        status: status,
        submittedDate: new Date(
          item.created_at || item.ngay_gio
        ).toLocaleDateString("vi-VN", {
          year: "numeric",
          month: "long",
          day: "numeric",
        }),
      };
    });
  } catch (e) {
    console.error("Lỗi tải yêu cầu xác nhận", e);
  }
};

// Actions for confirmations
const approveConfirmation = async (id) => {
  const confirmation = confirmations.value.find((c) => c.id === id);
  if (!confirmation) return;

  selectedConfirmation.value = confirmation;
  isConfirmationModalOpen.value = true;
  isProcessingConfirmation.value = true;

  try {
    await api.put(`/dang-ky-ca/${id}`, { trang_thai: "confirmed" });
    showSuccessToast("Thành công", "Đã xác nhận yêu cầu");
    isConfirmationModalOpen.value = false;
    await fetchConfirmations();
  } catch (e) {
    showErrorToast("Lỗi", "Không thể xác nhận yêu cầu");
  } finally {
    isProcessingConfirmation.value = false;
  }
};

const rejectConfirmation = async (id) => {
  const confirmation = confirmations.value.find((c) => c.id === id);
  if (!confirmation) return;

  selectedConfirmation.value = confirmation;
  isConfirmationModalOpen.value = true;
  isProcessingConfirmation.value = true;

  try {
    await api.put(`/dang-ky-ca/${id}`, { trang_thai: "rejected" });
    showSuccessToast("Thành công", "Đã từ chối yêu cầu");
    isConfirmationModalOpen.value = false;
    await fetchConfirmations();
  } catch (e) {
    showErrorToast("Lỗi", "Không thể từ chối yêu cầu");
  } finally {
    isProcessingConfirmation.value = false;
  }
};

const openConfirmationDetail = (confirmation) => {
  selectedConfirmation.value = confirmation;
  isConfirmationModalOpen.value = true;
};

const handleApproveFromModal = async () => {
  if (!selectedConfirmation.value) return;
  isProcessingConfirmation.value = true;

  try {
    await api.put(`/dang-ky-ca/${selectedConfirmation.value.id}`, {
      trang_thai: "confirmed",
    });
    showSuccessToast("Thành công", "Đã xác nhận yêu cầu");
    isConfirmationModalOpen.value = false;
    await fetchConfirmations();
  } catch (e) {
    showErrorToast("Lỗi", "Không thể xác nhận yêu cầu");
  } finally {
    isProcessingConfirmation.value = false;
  }
};

const handleRejectFromModal = async () => {
  if (!selectedConfirmation.value) return;
  isProcessingConfirmation.value = true;

  try {
    await api.put(`/dang-ky-ca/${selectedConfirmation.value.id}`, {
      trang_thai: "rejected",
    });
    showSuccessToast("Thành công", "Đã từ chối yêu cầu");
    isConfirmationModalOpen.value = false;
    await fetchConfirmations();
  } catch (e) {
    showErrorToast("Lỗi", "Không thể từ chối yêu cầu");
  } finally {
    isProcessingConfirmation.value = false;
  }
};

const updateStatusForConfirmation = async (confirmationId, newStatus) => {
  updatingStatusId.value = confirmationId;
  try {
    const response = await api.put(
      `/lich-dang-ky/${confirmationId}/doi-trang-thai`,
      {
        trang_thai: newStatus,
      }
    );

    if (response.data.success) {
      showSuccessToast("Thành công", `Đổi trạng thái thành công`);
      editingStatusId.value = null;
      // Refresh both confirmations list and schedule to show updated shifts
      await fetchConfirmations();
      await fetchShiftsForWeek();
    } else {
      showErrorToast(
        "Lỗi",
        response.data.message || "Không thể cập nhật trạng thái"
      );
    }
  } catch (error) {
    console.error("Error updating status:", error);
    showErrorToast(
      "Lỗi",
      error.response?.data?.message || "Không thể cập nhật trạng thái"
    );
  } finally {
    updatingStatusId.value = null;
  }
};

// Init
onMounted(async () => {
  goToToday();
  // Ensure staff list is loaded before calculating totals (fetchShifts uses staffList)
  await fetchStaff();
  await fetchShiftsForWeek();
  await fetchConfirmations();
});

watch(currentWeekStart, fetchShiftsForWeek);
</script>

<style scoped>
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f3f4f6;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, #14b8a6 0%, #0d9488 100%);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(180deg, #0d9488 0%, #0a7c66 100%);
}

/* Table responsiveness */
@media (max-width: 1024px) {
  table {
    font-size: 0.875rem;
  }
}

@media (max-width: 768px) {
  table {
    font-size: 0.75rem;
  }
}
</style>
