<template>
  <div class="bg-white border border-[rgba(0,0,0,0.1)] rounded-[14px] p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2
          class="text-xl font-bold text-[#101828] mb-1"
          style="font-family: 'Nunito Sans', sans-serif"
        >
          📅 Lịch làm việc hôm nay
        </h2>
        <p
          class="text-sm text-[#6a7282]"
          style="font-family: 'Inter', sans-serif"
        >
          {{ formatDateDisplay(new Date()) }}
        </p>
      </div>
      <button
        @click="refreshData"
        :disabled="loading"
        class="bg-[#155dfc] hover:bg-[#1347c9] disabled:bg-gray-300 rounded-lg h-10 px-4 flex items-center gap-2 transition-colors"
      >
        <svg
          v-if="!loading"
          class="w-4 h-4 text-white"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
          />
        </svg>
        <div
          v-else
          class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
        ></div>
        <span
          class="text-sm font-medium text-white"
          style="font-family: 'Inter', sans-serif"
        >
          Làm mới
        </span>
      </button>
    </div>

    <!-- Loading State -->
    <div
      v-if="loading && !scheduleData"
      class="flex items-center justify-center h-64"
    >
      <div
        class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#155dfc]"
      ></div>
    </div>

    <!-- Content -->
    <div v-else-if="scheduleData">
      <!-- Statistics Cards -->
      <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4">
          <div class="flex items-center justify-between">
            <div>
              <p
                class="text-xs text-[#6a7282] mb-1"
                style="font-family: 'Inter', sans-serif"
              >
                Ca làm việc
              </p>
              <p
                class="text-2xl font-bold text-[#155dfc]"
                style="font-family: 'Inter', sans-serif"
              >
                {{ scheduleData.statistics?.work_shifts || 0 }}
              </p>
            </div>
            <div
              class="w-12 h-12 bg-[#155dfc] rounded-full flex items-center justify-center"
            >
              <span class="text-2xl">🕐</span>
            </div>
          </div>
        </div>

        <div
          class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4"
        >
          <div class="flex items-center justify-between">
            <div>
              <p
                class="text-xs text-[#6a7282] mb-1"
                style="font-family: 'Inter', sans-serif"
              >
                Tổng lịch hẹn
              </p>
              <p
                class="text-2xl font-bold text-[#008236]"
                style="font-family: 'Inter', sans-serif"
              >
                {{ scheduleData.statistics?.appointments || 0 }}
              </p>
            </div>
            <div
              class="w-12 h-12 bg-[#008236] rounded-full flex items-center justify-center"
            >
              <span class="text-2xl">👥</span>
            </div>
          </div>
        </div>

        <div
          class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4"
        >
          <div class="flex items-center justify-between">
            <div>
              <p
                class="text-xs text-[#6a7282] mb-1"
                style="font-family: 'Inter', sans-serif"
              >
                Chờ xác nhận
              </p>
              <p
                class="text-2xl font-bold text-[#ff8904]"
                style="font-family: 'Inter', sans-serif"
              >
                {{ scheduleData.statistics?.pending_appointments || 0 }}
              </p>
            </div>
            <div
              class="w-12 h-12 bg-[#ff8904] rounded-full flex items-center justify-center"
            >
              <span class="text-2xl">⏳</span>
            </div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg p-4">
          <div class="flex items-center justify-between">
            <div>
              <p
                class="text-xs text-[#6a7282] mb-1"
                style="font-family: 'Inter', sans-serif"
              >
                Đã xác nhận
              </p>
              <p
                class="text-2xl font-bold text-[#009689]"
                style="font-family: 'Inter', sans-serif"
              >
                {{ scheduleData.statistics?.confirmed_appointments || 0 }}
              </p>
            </div>
            <div
              class="w-12 h-12 bg-[#009689] rounded-full flex items-center justify-center"
            >
              <span class="text-2xl">✅</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Work Shifts -->
      <div class="mb-6">
        <h3
          class="text-base font-bold text-[#364153] mb-3"
          style="font-family: 'Inter', sans-serif"
        >
          Ca làm việc
        </h3>
        <div
          v-if="
            scheduleData.lich_lam_viec && scheduleData.lich_lam_viec.length > 0
          "
          class="grid grid-cols-3 gap-3"
        >
          <div
            v-for="shift in scheduleData.lich_lam_viec"
            :key="shift.id"
            class="bg-gradient-to-r from-[#f0fdfa] to-[#f0fdf4] border border-[#b9f8cf] rounded-lg p-4"
          >
            <div class="flex items-center gap-2 mb-2">
              <span class="text-2xl">
                {{ getShiftIcon(shift.thoi_gian_truc) }}
              </span>
              <div>
                <p
                  class="text-sm font-semibold text-[#101828]"
                  style="font-family: 'Inter', sans-serif"
                >
                  {{ getShiftName(shift.thoi_gian_truc) }}
                </p>
                <p
                  class="text-xs text-[#6a7282]"
                  style="font-family: 'Inter', sans-serif"
                >
                  {{ getShiftTime(shift.thoi_gian_truc) }}
                </p>
              </div>
            </div>
            <p
              v-if="shift.ghi_chu"
              class="text-xs text-[#4a5565] mt-2"
              style="font-family: 'Inter', sans-serif"
            >
              📍 {{ shift.ghi_chu }}
            </p>
          </div>
        </div>
        <div v-else class="text-center py-8 bg-gray-50 rounded-lg">
          <span class="text-4xl">😴</span>
          <p
            class="text-sm text-[#6a7282] mt-2"
            style="font-family: 'Inter', sans-serif"
          >
            Bạn không có ca làm việc nào hôm nay
          </p>
        </div>
      </div>

      <!-- Appointments -->
      <div>
        <h3
          class="text-base font-bold text-[#364153] mb-3"
          style="font-family: 'Inter', sans-serif"
        >
          Lịch hẹn hôm nay
        </h3>
        <div
          v-if="scheduleData.lich_hen && scheduleData.lich_hen.length > 0"
          class="space-y-3"
        >
          <div
            v-for="appointment in scheduleData.lich_hen"
            :key="appointment.id"
            class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <span
                    :class="[
                      'inline-block px-2 py-1 rounded-full text-xs font-medium',
                      appointment.trang_thai === 'cho_xac_nhan'
                        ? 'bg-orange-100 text-[#ca3500]'
                        : '',
                      appointment.trang_thai === 'da_xac_nhan'
                        ? 'bg-green-100 text-[#008236]'
                        : '',
                      appointment.trang_thai === 'hoan_thanh'
                        ? 'bg-blue-100 text-[#155dfc]'
                        : '',
                      appointment.trang_thai === 'huy'
                        ? 'bg-gray-100 text-[#6a7282]'
                        : '',
                    ]"
                    style="font-family: 'Inter', sans-serif"
                  >
                    {{ getStatusText(appointment.trang_thai) }}
                  </span>
                  <span
                    class="text-xs text-[#6a7282]"
                    style="font-family: 'Inter', sans-serif"
                  >
                    {{ formatTime(appointment.ngay_gio) }}
                  </span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <p
                      class="text-xs text-[#6a7282] mb-1"
                      style="font-family: 'Inter', sans-serif"
                    >
                      Khách hàng
                    </p>
                    <p
                      class="text-sm font-semibold text-[#101828]"
                      style="font-family: 'Inter', sans-serif"
                    >
                      {{ appointment.khach_hang || "N/A" }}
                    </p>
                    <p
                      v-if="appointment.khach_hang_phone"
                      class="text-xs text-[#6a7282]"
                      style="font-family: 'Inter', sans-serif"
                    >
                      📞 {{ appointment.khach_hang_phone }}
                    </p>
                  </div>

                  <div>
                    <p
                      class="text-xs text-[#6a7282] mb-1"
                      style="font-family: 'Inter', sans-serif"
                    >
                      Thú cưng
                    </p>
                    <p
                      class="text-sm font-semibold text-[#101828]"
                      style="font-family: 'Inter', sans-serif"
                    >
                      {{ appointment.thu_cung || "N/A" }}
                    </p>
                  </div>
                </div>

                <div class="mt-2">
                  <p
                    class="text-xs text-[#6a7282] mb-1"
                    style="font-family: 'Inter', sans-serif"
                  >
                    Dịch vụ
                  </p>
                  <p
                    class="text-sm font-medium text-[#155dfc]"
                    style="font-family: 'Inter', sans-serif"
                  >
                    {{ appointment.dich_vu || "N/A" }}
                  </p>
                </div>

                <p
                  v-if="appointment.ghi_chu"
                  class="text-xs text-[#4a5565] mt-2 italic"
                  style="font-family: 'Inter', sans-serif"
                >
                  💬 {{ appointment.ghi_chu }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8 bg-gray-50 rounded-lg">
          <span class="text-4xl">📅</span>
          <p
            class="text-sm text-[#6a7282] mt-2"
            style="font-family: 'Inter', sans-serif"
          >
            Chưa có lịch hẹn nào hôm nay
          </p>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="text-center py-12">
      <span class="text-4xl">⚠️</span>
      <p
        class="text-sm text-[#6a7282] mt-2"
        style="font-family: 'Inter', sans-serif"
      >
        Không thể tải dữ liệu lịch làm việc
      </p>
      <button
        @click="refreshData"
        class="mt-4 bg-[#155dfc] hover:bg-[#1347c9] rounded-lg h-10 px-4 text-sm font-medium text-white"
      >
        Thử lại
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { getMyTodaySchedule } from "@/services/lichLamViecService";
import { showErrorToast } from "@/utils/toast";

const loading = ref(false);
const scheduleData = ref(null);

const fetchData = async () => {
  loading.value = true;
  try {
    const data = await getMyTodaySchedule();
    if (data.status) {
      scheduleData.value = data.data;
    }
  } catch (error) {
    console.error("Error fetching today schedule:", error);
    showErrorToast(
      "Lỗi",
      error.response?.data?.message || "Không thể tải lịch hôm nay"
    );
  } finally {
    loading.value = false;
  }
};

const refreshData = () => {
  fetchData();
};

const formatDateDisplay = (date) => {
  const d = new Date(date);
  const days = [
    "Chủ nhật",
    "Thứ 2",
    "Thứ 3",
    "Thứ 4",
    "Thứ 5",
    "Thứ 6",
    "Thứ 7",
  ];
  const dayName = days[d.getDay()];
  const day = String(d.getDate()).padStart(2, "0");
  const month = String(d.getMonth() + 1).padStart(2, "0");
  const year = d.getFullYear();
  return `${dayName}, ${day}/${month}/${year}`;
};

const formatTime = (datetime) => {
  const d = new Date(datetime);
  const hours = String(d.getHours()).padStart(2, "0");
  const minutes = String(d.getMinutes()).padStart(2, "0");
  return `${hours}:${minutes}`;
};

const getShiftName = (shift) => {
  const names = {
    ca_sang: "Ca Sáng",
    ca_chieu: "Ca Chiều",
    ca_toi: "Ca Tối",
  };
  return names[shift] || shift;
};

const getShiftTime = (shift) => {
  const times = {
    ca_sang: "08:00 - 12:00",
    ca_chieu: "13:00 - 17:00",
    ca_toi: "18:00 - 21:00",
  };
  return times[shift] || "";
};

const getShiftIcon = (shift) => {
  const icons = {
    ca_sang: "🌅",
    ca_chieu: "☀️",
    ca_toi: "🌙",
  };
  return icons[shift] || "🕐";
};

const getStatusText = (status) => {
  const texts = {
    cho_xac_nhan: "⏳ Chờ xác nhận",
    da_xac_nhan: "✅ Đã xác nhận",
    hoan_thanh: "✓ Hoàn thành",
    huy: "❌ Đã hủy",
  };
  return texts[status] || status;
};

onMounted(() => {
  fetchData();
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap");
</style>
