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

          <button
            @click="isGenerateModalOpen = true"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg flex items-center gap-2 font-medium text-sm transition shadow-sm hover:shadow-md"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Phân lịch tự động
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
          Xác nhận đơn nghỉ
        </span>
      </button>
    </div>

    <!-- Schedule Tab Content -->
    <div v-if="activeTab === 'schedule'">
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
                      class="h-full space-y-2 rounded-lg border-2 border-dashed transition-all cursor-pointer p-2"
                      :class="getCellStyle(staff, day.dateStr)"
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
                            Ca trực
                          </div>
                          <span
                            :class="[
                              'inline-block mt-1 px-2 py-0.5 rounded text-white text-xs font-bold',
                              shift.thoi_gian_truc === 'ca_sang'
                                ? 'bg-yellow-500'
                                : shift.thoi_gian_truc === 'ca_chieu'
                                ? 'bg-amber-500'
                                : shift.thoi_gian_truc === 'ca_dang_ky'
                                ? 'bg-blue-500'
                                : 'bg-purple-500',
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
              <option value="cho_duyet">Chờ duyệt</option>
              <option value="da_duyet">Đã duyệt</option>
              <option value="tu_choi">Từ chối</option>
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
        <p class="text-gray-500 font-medium">Không có đơn xin nghỉ nào</p>
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
                  Ngày nghỉ
                </th>
                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">
                  Loại nghỉ
                </th>
                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">
                  Lý do
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

                <!-- Ngày nghỉ -->
                <td class="px-6 py-4">
                  <p class="text-sm font-semibold text-gray-900">
                    {{ confirmation.date }}
                  </p>
                  <p class="text-xs text-gray-500 mt-1">
                    Gửi lúc: {{ confirmation.submittedDateTime }}
                  </p>
                </td>

                <!-- Loại nghỉ -->
                <td class="px-6 py-4">
                  <span :class="['px-2 py-1 text-xs font-semibold rounded-full', confirmation.loaiNghi === 'co_luong' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800']">
                    {{ confirmation.loaiNghi === 'co_luong' ? 'Có lương' : 'Không lương' }}
                  </span>
                </td>

                <!-- Ghi chú/Lý do -->
                <td class="px-6 py-4">
                  <p class="text-sm text-gray-600 line-clamp-2" :title="confirmation.note">
                    {{ confirmation.note || "—" }}
                  </p>
                  <p v-if="confirmation.status === 'tu_choi' && confirmation.rejectReason" class="text-xs text-red-600 mt-1">
                    Lý do từ chối: {{ confirmation.rejectReason }}
                  </p>
                </td>

                <!-- Trạng thái -->
                <td class="px-6 py-4">
                  <button
                    @click="openConfirmationDetail(confirmation)"
                    :class="[
                      'inline-flex items-center px-3 py-1 rounded-full text-xs font-bold cursor-pointer transition-all hover:opacity-80',
                      confirmation.status === 'cho_duyet'
                        ? 'bg-amber-100 text-amber-800'
                        : confirmation.status === 'da_duyet'
                        ? 'bg-emerald-100 text-emerald-800'
                        : 'bg-red-100 text-red-800',
                    ]"
                  >
                    {{
                      confirmation.status === "cho_duyet"
                        ? "Chờ duyệt"
                        : confirmation.status === "da_duyet"
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

    <!-- Modal Phân Lịch Tự Động -->
    <div
      v-if="isGenerateModalOpen"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-[1050]"
      @click.self="isGenerateModalOpen = false"
    >
      <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 space-y-5">
        <div>
          <h3 class="text-lg font-semibold text-black">Phân lịch tự động</h3>
          <p class="text-sm text-gray-500 mt-1">
            Hệ thống sẽ tự sinh lịch theo vòng xoay 4-slot (ca sáng / ca tối / nghỉ bù) cho từng nhóm bác sĩ và y tá, thứ 2–thứ 7.
          </p>
        </div>

        <div class="flex gap-3">
          <div class="flex flex-col gap-1 flex-1">
            <label class="text-xs font-medium text-gray-600">Tháng</label>
            <select
              v-model="generateForm.month"
              class="h-9 border border-gray-300 rounded-lg px-2 text-sm focus:outline-none focus:border-teal-500"
            >
              <option v-for="m in 12" :key="m" :value="m">Tháng {{ m }}</option>
            </select>
          </div>
          <div class="flex flex-col gap-1 flex-1">
            <label class="text-xs font-medium text-gray-600">Năm</label>
            <input
              v-model.number="generateForm.year"
              type="number"
              class="h-9 border border-gray-300 rounded-lg px-3 text-sm focus:outline-none focus:border-teal-500"
            />
          </div>
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
          <input v-model="generateForm.force" type="checkbox" class="rounded" />
          <span class="text-sm text-gray-600">Tạo lại nếu đã tồn tại (xóa lịch cũ)</span>
        </label>

        <p v-if="generateMessage" class="text-sm" :class="generateError ? 'text-red-600' : 'text-green-600'">
          {{ generateMessage }}
        </p>

        <div class="flex gap-2 justify-end pt-1">
          <button
            @click="isGenerateModalOpen = false"
            class="h-9 px-4 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50"
          >
            Đóng
          </button>
          <button
            @click="runGenerateSchedule"
            :disabled="generating"
            class="h-9 px-5 bg-teal-700 hover:bg-teal-800 text-white text-sm font-semibold rounded-lg disabled:opacity-50 transition-colors"
          >
            {{ generating ? 'Đang sinh lịch...' : 'Xác nhận sinh lịch' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <PhanCaTruc
      v-if="isAddShiftModalOpen"
      :preselected-staff="selectedStaffForShift"
      :preselected-date="selectedDateForShift"
      :preselected-shift="selectedShiftForModal"
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
import PhanCaTruc from "./shift-assignment/index.vue";
import XacNhanDangKy from "./registration-confirmation/index.vue";
import api from "@/utils/api";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
import { generateSchedule } from "@/services/lichLamViecService";
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
  { value: "cho_duyet", label: "Chờ duyệt" },
  { value: "da_duyet", label: "Đã duyệt" },
  { value: "tu_choi", label: "Từ chối" },
];

// Generate schedule modal
const isGenerateModalOpen = ref(false);
const generating = ref(false);
const generateMessage = ref("");
const generateError = ref(false);
const _now = new Date();
const generateForm = ref({
  month: _now.getMonth() === 11 ? 1 : _now.getMonth() + 2,
  year: _now.getMonth() === 11 ? _now.getFullYear() + 1 : _now.getFullYear(),
  force: false,
});

const runGenerateSchedule = async () => {
  generating.value = true;
  generateMessage.value = "";
  generateError.value = false;
  try {
    const res = await generateSchedule(
      generateForm.value.year,
      generateForm.value.month,
      generateForm.value.force
    );
    generateMessage.value = res?.message || "Đã sinh lịch thành công!";
    showSuccessToast("Thành công", generateMessage.value);
    await fetchShiftsForWeek();
  } catch (err) {
    generateError.value = true;
    generateMessage.value =
      err?.response?.data?.message || "Lỗi khi sinh lịch";
    showErrorToast("Lỗi", generateMessage.value);
  } finally {
    generating.value = false;
  }
};

// Modal
const isAddShiftModalOpen = ref(false);
const selectedStaffForShift = ref(null);
const selectedDateForShift = ref("");
const selectedShiftForModal = ref("morning");
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

const getCellStyle = (staff, dateStr) => {
  const shifts = getShiftsForCell(staff, dateStr);
  const appointments = getAppointmentsForCell(staff, dateStr);

  if (shifts.length > 0) {
    const s = shifts[0];
    if (s.thoi_gian_truc === 'ca_sang') {
      return 'border-yellow-300 bg-yellow-50 hover:border-yellow-400 hover:bg-yellow-100/60';
    } else if (s.thoi_gian_truc === 'ca_chieu') {
      return 'border-amber-300 bg-amber-50 hover:border-amber-400 hover:bg-amber-100/60';
    } else if (s.thoi_gian_truc === 'ca_dang_ky') {
      return 'border-blue-300 bg-blue-50 hover:border-blue-400 hover:bg-blue-100/60';
    } else {
      return 'border-purple-300 bg-purple-50 hover:border-purple-400 hover:bg-purple-100/60';
    }
  } else if (appointments.length > 0) {
    return 'border-teal-300 bg-teal-50 hover:border-teal-400 hover:bg-teal-100/60';
  } else {
    return 'border-gray-200 hover:border-teal-300 hover:bg-teal-50/30';
  }
};

const shiftBorderClass = (shift) => {
  if (shift.thoi_gian_truc === "ca_sang") return "border-yellow-400";
  if (shift.thoi_gian_truc === "ca_chieu") return "border-amber-400";
  if (shift.thoi_gian_truc === "ca_dang_ky") return "border-blue-400";
  return "border-purple-400";
};

const formatRole = (role) => {
  if (!role) return "Nhân viên";
  const str = role.toLowerCase();
  if (str === "bac_si" || str === "bac si" || str === "bác sĩ") return "Bác Sĩ";
  if (str === "y_ta" || str === "y ta" || str === "y tá") return "Y Tá";
  if (str === "le_tan" || str === "le tan" || str === "lễ tân") return "Lễ Tân";
  return role;
};

// Fetch & Calculate
const fetchStaff = async () => {
  try {
    const res = await api.get("/nhan-vien", { params: { per_page: 500 } });
    const data = res.data?.data?.data || res.data?.data || [];
    staffList.value = data.map((s) => ({
      id: s.id,
      name: s.ho_ten || s.full_name || "Không tên",
      role: formatRole(s.chuc_vu || s.vai_tro),
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

  // Detect existing shift to pre-select the correct shift type
  const existingShifts = getShiftsForCell(staff, day.dateStr);
  if (existingShifts.length > 0) {
    const t = existingShifts[0].thoi_gian_truc;
    selectedShiftForModal.value = t === 'ca_toi' ? 'night' : 'morning';
  } else {
    selectedShiftForModal.value = 'morning';
  }

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
          : data.shift === "night"
          ? "ca_toi"
          : "ca_chieu",
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
    const res = await api.get("/admin/lich-nghi", {
      params: { per_page: 500 },
    });
    const items = res.data?.data?.data || res.data?.data || [];

    confirmations.value = items.map((item) => {
      return {
        id: item.id,
        staffName: item.nhan_vien?.full_name || "Không tên",
        avatar: item.nhan_vien?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(
          item.nhan_vien?.full_name || "N"
        )}&background=0D8ABC&color=fff`,
        role: formatRole(item.nhan_vien?.chuc_danh || item.nhan_vien?.vai_tro),
        date: new Date(item.ngay_nghi).toLocaleDateString("vi-VN"),
        loaiNghi: item.loai_nghi,
        note: item.ly_do || "",
        status: item.trang_thai,
        rejectReason: item.ly_do_tu_choi,
        submittedDateTime: new Date(item.created_at).toLocaleString("vi-VN", {
          year: "numeric",
          month: "2-digit",
          day: "2-digit",
          hour: "2-digit",
          minute: "2-digit",
        }),
        submittedDate: new Date(item.created_at).toLocaleDateString("vi-VN", {
          year: "numeric",
          month: "long",
          day: "numeric",
        }),
        confirmedDate: item.updated_at ? new Date(item.updated_at).toLocaleString("vi-VN") : null,
      };
    });
    
    // Sắp xếp: chờ duyệt lên đầu, rồi tới ngày tạo mới nhất
    confirmations.value.sort((a, b) => {
      if (a.status === 'cho_duyet' && b.status !== 'cho_duyet') return -1;
      if (a.status !== 'cho_duyet' && b.status === 'cho_duyet') return 1;
      return new Date(b.submittedDateTime) - new Date(a.submittedDateTime);
    });
  } catch (e) {
    console.error("Lỗi tải yêu cầu xác nhận", e);
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
    await api.patch(`/admin/lich-nghi/${selectedConfirmation.value.id}/duyet`);
    showSuccessToast("Thành công", "Đã duyệt đơn xin nghỉ");
    isConfirmationModalOpen.value = false;
    await fetchConfirmations();
  } catch (e) {
    showErrorToast("Lỗi", e.response?.data?.message || "Không thể duyệt yêu cầu");
  } finally {
    isProcessingConfirmation.value = false;
  }
};

const handleRejectFromModal = async (reason) => {
  if (!selectedConfirmation.value) return;
  isProcessingConfirmation.value = true;

  try {
    await api.patch(`/admin/lich-nghi/${selectedConfirmation.value.id}/tu-choi`, {
      ly_do: reason
    });
    showSuccessToast("Thành công", "Đã từ chối đơn xin nghỉ");
    isConfirmationModalOpen.value = false;
    await fetchConfirmations();
  } catch (e) {
    showErrorToast("Lỗi", e.response?.data?.message || "Không thể từ chối yêu cầu");
  } finally {
    isProcessingConfirmation.value = false;
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
