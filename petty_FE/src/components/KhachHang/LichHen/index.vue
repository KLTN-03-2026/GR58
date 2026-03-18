<template>
  <div class="min-h-screen font-nunitoSans">
    <div class="container mx-auto px-6 max-w-6xl">
      <!-- Header -->
      <div
        class="flex justify-between items-center mb-6 flex-col md:flex-row gap-4"
      >
        <div>
          <h1 class="text-xl font-bold text-black">Lịch hẹn khám</h1>
          <p class="text-lg font-medium text-gray-600">
            Quản lý các buổi khám đã đặt
          </p>
        </div>
        <button
          @click="handleNewAppointment"
          class="flex items-center gap-3 px-6 py-3 bg-[#5A9690] text-white rounded-lg font-medium text-lg hover:bg-teal-700 transition"
        >
          <AddIcon />
          Đặt lịch mới
        </button>
      </div>

      <!-- Tabs -->
      <div class="flex bg-zinc-100 rounded-2xl p-1 gap-1 mb-8 w-fit">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="
            activeTab === tab.id
              ? 'bg-white text-black shadow-sm'
              : 'text-black hover:opacity-70'
          "
          class="px-6 py-2.5 rounded-2xl font-semibold text-base transition"
        >
          {{ tab.label }} ({{ tab.count }})
        </button>
      </div>

      <!-- Filter -->
      <div class="bg-gray-200 border !border-black/20 rounded-2xl p-6 mb-8">
        <div class="flex flex-wrap items-center gap-4">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none">
              <path
                d="M3 5h14M7 10h6M5 15h10"
                stroke="#374151"
                stroke-width="2"
                stroke-linecap="round"
              />
            </svg>
            <span class="font-medium text-gray-700">Lọc theo:</span>
          </div>
          <div class="flex flex-1 flex-wrap gap-4">
            <button
              @click="openFilter('pet')"
              class="flex-1 min-w-48 flex justify-between items-center px-4 py-2 bg-white rounded-lg hover:opacity-80 transition"
            >
              <span class="text-gray-500 font-medium">{{
                selectedPetLabel
              }}</span>
              <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none">
                <path
                  d="M2 5l6 6 6-6"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                />
              </svg>
            </button>
            <button
              @click="openFilter('service')"
              class="flex-1 min-w-48 flex justify-between items-center px-4 py-2 bg-white rounded-lg hover:opacity-80 transition"
            >
              <span class="text-gray-500 font-medium">{{
                selectedServiceLabel
              }}</span>
              <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none">
                <path
                  d="M2 5l6 6 6-6"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                />
              </svg>
            </button>
            <button
              @click="openFilter('date')"
              class="flex-1 min-w-48 flex justify-between items-center px-4 py-2 bg-white rounded-lg hover:opacity-80 transition"
            >
              <span class="text-gray-500 font-medium">{{
                dateRangeLabel
              }}</span>
              <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none">
                <path
                  d="M2 5l6 6 6-6"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                />
              </svg>
            </button>
          </div>
          <button
            @click="clearFilters"
            class="px-3 py-1 text-teal-700 font-medium hover:opacity-70 transition"
          >
            Xóa bộ lọc
          </button>
        </div>
      </div>

      <!-- Filter panel -->
      <div v-if="showFilterPanel" class="mb-8">
        <div class="bg-white border rounded-lg p-4 max-w-3xl">
          <div v-if="activeFilterType === 'pet'">
            <label class="block text-sm font-medium text-gray-700 mb-2"
              >Chọn thú cưng</label
            >
            <select v-model="selectedPet" class="w-full border rounded-lg p-2">
              <option value="all">Tất cả thú cưng</option>
              <option v-for="p in pets" :key="p.id" :value="p.id">
                {{ p.name || p.ten || p.ten_thu_cung }}
              </option>
            </select>
          </div>
          <div v-if="activeFilterType === 'service'">
            <label class="block text-sm font-medium text-gray-700 mb-2"
              >Chọn dịch vụ</label
            >
            <select
              v-model="selectedService"
              class="w-full border rounded-lg p-2"
            >
              <option value="all">Tất cả dịch vụ</option>
              <option v-for="s in services" :key="s.id" :value="s.id">
                {{ s.name || s.ten || s.ten_dich_vu }}
              </option>
            </select>
          </div>
          <div v-if="activeFilterType === 'date'">
            <label class="block text-sm font-medium text-gray-700 mb-2"
              >Chọn khoảng thời gian</label
            >
            <div class="flex gap-2">
              <input
                type="date"
                v-model="dateFrom"
                class="border rounded-lg p-2"
              />
              <input
                type="date"
                v-model="dateTo"
                class="border rounded-lg p-2"
              />
            </div>
          </div>
          <div class="mt-4 flex justify-end gap-2">
            <button
              @click="applyFilters"
              class="px-4 py-2 bg-teal-600 text-white rounded-lg"
            >
              Áp dụng
            </button>
            <button @click="closeFilter" class="px-4 py-2 border rounded-lg">
              Đóng
            </button>
          </div>
        </div>
      </div>

      <!-- TAB SẮP TỚI -->
      <div v-if="activeTab === 'upcoming'" class="space-y-6">
        <div
          v-for="appt in upcomingAppointments"
          :key="appt.id"
          class="bg-white rounded-2xl overflow-hidden border !border-black/5 shadow-sm"
        >
          <div class="bg-teal-50 border-b border-teal-100 p-6">
            <div class="flex justify-between flex-col md:flex-row gap-4">
              <div>
                <div class="flex items-center gap-3 mb-2">
                  <h3 class="text-lg font-semibold">{{ appt.service }}</h3>
                  <span
                    :class="[
                      'px-3 py-1 rounded-lg font-medium text-base',
                      statusClass(appt.status),
                    ]"
                  >
                    {{ statusLabel(appt.status) }}
                  </span>
                </div>
                <p class="text-gray-600 font-medium">
                  Mã lịch hẹn: {{ appt.id }}
                </p>
              </div>
              <div class="text-right md:text-left">
                <div class="font-medium">{{ appt.date }}</div>
                <div class="text-teal-600 font-medium text-lg">
                  {{ appt.time }}
                </div>
              </div>
            </div>
          </div>

          <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6">
              <div class="space-y-6">
                <div class="flex gap-4">
                  <svg
                    class="w-6 h-6 mt-1 flex-shrink-0 text-gray-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <circle cx="12" cy="8" r="3.5" stroke-width="2" />
                    <path d="M4 20c0-4 4-7 8-7s8 3 8 7" stroke-width="2" />
                  </svg>
                  <div>
                    <p class="text-gray-600 font-medium mb-1">Thú cưng</p>
                    <p
                      :class="
                        appt.pet === 'Milo' ? 'text-amber-700' : 'text-sky-500'
                      "
                      class="text-lg font-semibold"
                    >
                      {{ appt.pet }}
                    </p>
                    <div
                      class="flex flex-wrap gap-4 mt-2 text-gray-600 font-medium"
                    >
                      <span>{{ appt.breed }}</span>
                    </div>
                  </div>
                </div>
                <div class="flex gap-4">
                  <svg
                    class="w-6 h-6 mt-1 flex-shrink-0 text-gray-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M12 12a5 5 0 100-10 5 5 0 000 10zM4 20c0-5 4-8 8-8s8 3 8 8"
                      stroke-width="2"
                    />
                  </svg>
                  <div>
                    <p class="text-gray-600 font-medium mb-1">Bác sĩ</p>
                    <p class="font-semibold">{{ appt.doctor }}</p>
                  </div>
                </div>
              </div>
              <div class="space-y-6">
                <div class="flex gap-4">
                  <svg
                    class="w-6 h-6 mt-1 flex-shrink-0 text-gray-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M12 2a8 8 0 00-8 8c0 6 8 12 8 12s8-6 8-12a8 8 0 00-8-8z"
                      stroke-width="2"
                    />
                    <circle cx="12" cy="10" r="3" fill="currentColor" />
                  </svg>
                  <div>
                    <p class="text-gray-600 font-medium mb-1">Địa điểm</p>
                    <p class="font-semibold">{{ appt.clinic }}</p>
                    <p class="text-gray-600 font-medium">{{ appt.address }}</p>
                  </div>
                </div>
                <div
                  v-if="appt.note"
                  class="bg-yellow-50 border !border-yellow-200 rounded-xl p-4"
                >
                  <p class="font-bold text-amber-800 text-sm mb-1">Lưu ý:</p>
                  <p class="font-medium text-amber-800">{{ appt.note }}</p>
                </div>
              </div>
            </div>

            <div
              class="pt-6 border-t border-zinc-200 flex flex-col md:flex-row gap-4"
            >
              <button
                @click="handleReschedule(appt.id)"
                class="flex-1 flex items-center justify-center gap-2 py-3 border border-black/10 rounded-lg hover:bg-gray-50 transition"
              >
                <svg class="w-5 h-5" viewBox="0 0 16 16" fill="none">
                  <path
                    d="M8 2v12M2 8h12"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                  />
                </svg>
                Đổi lịch
              </button>
              <button
                @click="handleCancel(appt.id)"
                class="flex-1 flex items-center justify-center gap-2 py-3 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition"
              >
                <svg class="w-5 h-5" viewBox="0 0 16 16" fill="none">
                  <path
                    d="M2 8h12"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                  />
                </svg>
                Hủy hẹn
              </button>
              <button
                @click="openDetail(appt)"
                class="flex-1 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition font-medium"
              >
                Xem chi tiết
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB ĐÃ QUA -->
      <div v-if="activeTab === 'past'" class="space-y-6">
        <div
          v-for="appt in pastAppointments"
          :key="appt.id"
          class="bg-white rounded-2xl border border-black/5 overflow-hidden shadow-sm"
        >
          <div class="p-6">
            <div class="flex justify-between flex-col md:flex-row gap-4 mb-4">
              <div>
                <div class="flex items-center gap-3 flex-wrap">
                  <h3 class="text-lg font-semibold">{{ appt.service }}</h3>
                  <span
                    class="px-3 py-1 rounded-lg text-sm font-medium"
                    :class="
                      appt.status === 'completed'
                        ? 'bg-emerald-100 text-green-700'
                        : 'bg-gray-200 text-gray-700'
                    "
                  >
                    {{ appt.badge1 }}
                  </span>
                  <span
                    v-if="appt.badge2"
                    class="px-3 py-1 bg-emerald-50 border border-emerald-200 text-green-700 rounded-lg text-sm font-medium"
                  >
                    {{ appt.badge2 }}
                  </span>
                </div>
                <p class="text-gray-600 font-medium mt-1">
                  Mã lịch hẹn: {{ appt.id }}
                </p>
              </div>
              <div class="text-right">
                <div class="font-medium">{{ appt.date }}</div>
                <div class="text-gray-600">{{ appt.time }}</div>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4 max-w-md ml-auto pt-4">
              <div>
                <button
                  v-if="appt.status === 'completed'"
                  @click="viewResult(appt.id)"
                  class="w-full px-6 py-3.5 bg-white border border-black/10 rounded-xl hover:bg-gray-50 transition font-semibold text-center"
                >
                  Xem kết quả
                </button>
              </div>
              <div>
                <button
                  v-if="appt.status !== 'cancelled'"
                  @click="rebook(appt.id)"
                  class="w-full px-6 py-3.5 bg-white border border-black/10 rounded-xl hover:bg-gray-50 transition font-semibold text-center"
                >
                  Đặt lại
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- POPUP CHI TIẾT LỊCH HẸN -->
    <teleport to="body">
      <ChiTietLichHen
        v-if="selectedAppt"
        :is-open="showDetail"
        :selected-appt="selectedAppt"
        @close="showDetail = false"
      />
    </teleport>

    <!-- POPUP ĐẶT LỊCH KHÁM -->
    <teleport to="body">
      <DatLichKham
        :is-open="showBookingPopup"
        :initial-data="rebookData"
        @close="showBookingPopup = false"
        @confirm="handleBookingConfirm"
        @openAddPet="() => {}"
      />
    </teleport>

    <!-- POPUP ĐỔI LỊCH HẸN -->
    <teleport to="body">
      <DoiLich
        v-if="rescheduleAppt"
        :is-open="showReschedulePopup"
        :old-appointment="rescheduleAppt"
        @close="showReschedulePopup = false"
        @save="handleRescheduleConfirm"
      />
    </teleport>

    <!-- POPUP HỦY LỊCH HẸN -->
    <teleport to="body">
      <HuyHen
        v-if="cancelAppt"
        :is-open="showCancelPopup"
        :appointment="cancelAppt"
        :cancel-status="cancelStatus"
        @close="showCancelPopup = false"
        @keep="showCancelPopup = false"
        @cancel="handleCancelConfirm"
      />
    </teleport>

    <!-- POPUP KẾT QUẢ KHÁM BỆNH -->
    <teleport to="body">
      <KetQuaKhambenh
        v-if="resultData"
        :is-open="showResultPopup"
        :result="resultData"
        @close="showResultPopup = false"
        @download="(file) => console.log('Downloading', file)"
      />
    </teleport>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
import AddIcon from "@/assets/svg/add.svg";

import DatLichKham from "./DatLichKham/index.vue";
import ChiTietLichHen from "./ChiTietLichHen/index.vue";
import DoiLich from "./DoiLich/index.vue";
import HuyHen from "./HuyHen/index.vue";
import KetQuaKhambenh from "./KetQuaKhambenh/index.vue";

const activeTab = ref("upcoming");
// tabs counts are computed based on filtered lists (defined later)

const upcomingAppointments = ref([]);
const pastAppointments = ref([]);
const loading = ref(false);

const API_BASE = import.meta.env.VITE_API_BASE || "http://127.0.0.1:8000/api";

const mapAppointment = (item) => {
  // item: LichHen with relations thuCung, dichVu, nhanVien, thanhToan
  const ngayGio = item.ngay_gio ? new Date(item.ngay_gio) : null;
  const day = ngayGio ? String(ngayGio.getDate()).padStart(2, "0") : "";
  const month = ngayGio ? String(ngayGio.getMonth() + 1).padStart(2, "0") : "";
  const year = ngayGio ? ngayGio.getFullYear() : "";
  const hours = ngayGio ? String(ngayGio.getHours()).padStart(2, "0") : "";
  const minutes = ngayGio ? String(ngayGio.getMinutes()).padStart(2, "0") : "";

  // Xử lý tên bác sĩ - ưu tiên nhiều trường hợp
  let doctorName = "Chưa phân công";

  if (item.nhanVien) {
    // Backend trả về relation nhanVien (camelCase)
    doctorName =
      item.nhanVien.full_name ||
      item.nhanVien.ho_ten ||
      item.nhanVien.name ||
      item.nhanVien.ten ||
      `BS #${item.nhanVien.id}`;
  } else if (item.nhan_vien) {
    // Fallback: snake_case format
    doctorName =
      item.nhan_vien.full_name ||
      item.nhan_vien.ho_ten ||
      item.nhan_vien.name ||
      item.nhan_vien.ten ||
      `BS #${item.nhan_vien.id}`;
  } else if (item.bacSi) {
    // Fallback: có thể có trường bacSi
    doctorName =
      item.bacSi.full_name ||
      item.bacSi.ho_ten ||
      item.bacSi.name ||
      `BS #${item.bacSi.id}`;
  } else if (item.bac_si) {
    // Fallback: snake_case bacSi
    doctorName =
      item.bac_si.full_name ||
      item.bac_si.ho_ten ||
      item.bac_si.name ||
      `BS #${item.bac_si.id}`;
  } else if (item.doctor_name || item.doctorName) {
    // Fallback: có sẵn tên bác sĩ
    doctorName = item.doctor_name || item.doctorName;
  } else if (item.nhan_vien_id) {
    // Có ID nhưng không có relation
    doctorName = `Bác sĩ #${item.nhan_vien_id}`;
  }

  return {
    id: item.id || item.ma || `LH${item.id}`,
    raw: item,
    service:
      item.dichVu?.name ||
      item.dichVu?.ten_dich_vu ||
      item.dich_vu?.ten ||
      item.dich_vu?.name ||
      "Dịch vụ",
    date: ngayGio ? `${day}/${month}/${year}` : "",
    time: ngayGio ? `${hours}:${minutes}` : "",
    pet:
      item.thuCung?.ten_thu_cung ||
      item.thuCung?.ten ||
      item.thuCung?.name ||
      item.thu_cung?.ten_thu_cung ||
      item.thu_cung?.ten ||
      item.thu_cung?.name ||
      (typeof item.thuCung === "string" ? item.thuCung : "") ||
      "Thú cưng",
    breed:
      item.thuCung?.giong_thu_cung ||
      item.thuCung?.giong ||
      item.thuCung?.breed ||
      item.thu_cung?.giong_thu_cung ||
      "-",
    age:
      item.thuCung?.tuoi_thu_cung ||
      item.thuCung?.age ||
      item.thu_cung?.tuoi_thu_cung ||
      "-",
    weight: item.thuCung?.can_nang
      ? `${item.thuCung.can_nang} kg`
      : item.thuCung?.weight
      ? `${item.thuCung.weight} kg`
      : "-",
    doctor: doctorName,
    clinic:
      item.dia_diem || item.clinic || "Phòng khám Petty - Chi nhánh Quận 1",
    address: item.dia_chi || "123 Nguyễn Huệ, Q.1, TP.HCM",
    note: item.ghi_chu || "",
    status: item.trang_thai || (item.thanhToan ? "paid" : "pending"),
  };
};

// helpers to render status badge label and classes
const statusLabel = (s) => {
  if (!s) return "Đang xử lý";
  const st = String(s).toLowerCase();
  switch (st) {
    case "pending":
      return "Chờ xác nhận";
    case "confirmed":
    case "confirmed_by_staff":
      return "Đã xác nhận";
    case "paid":
      return "Đã thanh toán";
    case "completed":
      return "Hoàn thành";
    case "cancelled":
    case "canceled":
      return "Đã hủy";
    default:
      // fallback: if backend already returns a human label, use it
      return String(s);
  }
};

const statusClass = (s) => {
  const st = s ? String(s).toLowerCase() : "";
  if (st === "confirmed" || st === "confirmed_by_staff")
    return "bg-teal-100 text-teal-700";
  if (st === "pending") return "bg-yellow-100 text-yellow-800";
  if (st === "paid" || st === "completed")
    return "bg-emerald-100 text-green-700";
  if (st === "cancelled" || st === "canceled") return "bg-red-100 text-red-700";
  return "bg-gray-200 text-gray-700";
};

const fetchAppointments = async (filters = {}) => {
  loading.value = true;
  try {
    const res = await axios.get(`${API_BASE}/lich-hen`, { params: filters });
    let list = [];
    if (res.data && res.data.data) {
      // handle paginated or plain array
      if (Array.isArray(res.data.data)) list = res.data.data;
      else if (Array.isArray(res.data.data.data)) list = res.data.data.data;
      else if (Array.isArray(res.data)) list = res.data;
    }

    const today = new Date();
    const upcoming = [];
    const past = [];
    list.forEach((it) => {
      const mapped = mapAppointment(it);
      const dt = it.ngay_gio ? new Date(it.ngay_gio) : null;
      if (
        dt &&
        dt >= new Date(today.getFullYear(), today.getMonth(), today.getDate())
      ) {
        upcoming.push(mapped);
      } else {
        past.push(mapped);
      }
    });

    upcomingAppointments.value = upcoming;
    pastAppointments.value = past;
  } catch (err) {
    console.error("Lỗi khi lấy lịch hẹn:", err);
    showErrorToast("Lỗi", "Không thể tải lịch hẹn. Vui lòng thử lại.");
  } finally {
    loading.value = false;
  }
};

// --- Filters state & helpers ---
import { computed } from "vue";

const pets = ref([]);
const services = ref([]);
const selectedPet = ref("all");
const selectedService = ref("all");
const dateFrom = ref("");
const dateTo = ref("");
const showFilterPanel = ref(false);
const activeFilterType = ref(null);

const selectedPetLabel = computed(() => {
  if (selectedPet.value === "all") return "Tất cả thú cưng";
  const p = pets.value.find((x) => String(x.id) === String(selectedPet.value));
  return p
    ? p.name || p.ten || p.ten_thu_cung || "Thú cưng"
    : "Tất cả thú cưng";
});

const selectedServiceLabel = computed(() => {
  if (selectedService.value === "all") return "Tất cả dịch vụ";
  const s = services.value.find(
    (x) => String(x.id) === String(selectedService.value)
  );
  return s ? s.name || s.ten || s.ten_dich_vu || "Dịch vụ" : "Tất cả dịch vụ";
});

const dateRangeLabel = computed(() => {
  if (!dateFrom.value && !dateTo.value) return "Khoảng thời gian";
  if (dateFrom.value && !dateTo.value) return `Từ ${dateFrom.value}`;
  if (!dateFrom.value && dateTo.value) return `Đến ${dateTo.value}`;
  return `${dateFrom.value} → ${dateTo.value}`;
});

const fetchFilterOptions = async () => {
  try {
    const [pRes, sRes] = await Promise.all([
      axios.get(`${API_BASE}/thu-cung?all=1`).catch(() => ({ data: [] })),
      axios.get(`${API_BASE}/dich-vu`).catch(() => ({ data: [] })),
    ]);
    pets.value = Array.isArray(pRes.data) ? pRes.data : pRes.data?.data || [];
    services.value = Array.isArray(sRes.data)
      ? sRes.data
      : sRes.data?.data || [];
  } catch (e) {
    console.warn("Không thể nạp tùy chọn lọc:", e);
  }
};

onMounted(() => {
  fetchAppointments();
  fetchFilterOptions();
});

const openFilter = (type) => {
  activeFilterType.value = type;
  showFilterPanel.value = true;
};

const closeFilter = () => {
  showFilterPanel.value = false;
  activeFilterType.value = null;
};

const applyFilters = () => {
  // call server with filter params
  const params = {};
  if (selectedPet.value !== "all") {
    const p = pets.value.find(
      (x) => String(x.id) === String(selectedPet.value)
    );
    if (p)
      params.pet_name =
        p.ten_thu_cung || p.name || p.ten || p.display_name || "";
  }
  if (selectedService.value !== "all") {
    params.dich_vu_id = selectedService.value;
  }
  if (dateFrom.value) params.from_date = dateFrom.value;
  if (dateTo.value) params.to_date = dateTo.value;

  fetchAppointments(params);
  closeFilter();
};

const clearFilters = () => {
  selectedPet.value = "all";
  selectedService.value = "all";
  dateFrom.value = "";
  dateTo.value = "";
};

const withinDateRange = (isoDate) => {
  if (!isoDate) return false;
  const t = new Date(isoDate).setHours(0, 0, 0, 0);
  if (dateFrom.value) {
    const from = new Date(dateFrom.value).setHours(0, 0, 0, 0);
    if (t < from) return false;
  }
  if (dateTo.value) {
    const to = new Date(dateTo.value).setHours(0, 0, 0, 0);
    if (t > to) return false;
  }
  return true;
};

const tabs = computed(() => [
  {
    id: "upcoming",
    label: "Sắp tới",
    count: upcomingAppointments.value.length,
  },
  { id: "past", label: "Đã qua", count: pastAppointments.value.length },
]);

const showDetail = ref(false);
const selectedAppt = ref(null);
const showBookingPopup = ref(false);
const rebookData = ref(null);
const showReschedulePopup = ref(false);
const rescheduleAppt = ref(null);
const showCancelPopup = ref(false);
const cancelAppt = ref(null);
const cancelStatus = ref("unpaid"); // 'late' | 'refundable' | 'unpaid'
const showResultPopup = ref(false);
const resultData = ref(null);

const openDetail = (appt) => {
  selectedAppt.value = { ...appt };
  showDetail.value = true;
};

const handleNewAppointment = () => {
  rebookData.value = null;
  showBookingPopup.value = true;
};

const handleBookingConfirm = async (bookingData) => {
  // bookingData may be server response or payload
  console.log("Booking confirmed:", bookingData);
  showSuccessToast("Đặt lịch", "Đặt lịch thành công");
  // refresh appointments list
  await fetchAppointments();
  showBookingPopup.value = false;
};

// openFilter/clearFilters implemented above with filter panel logic

const handleReschedule = (id) => {
  const appt = upcomingAppointments.value.find((a) => a.id === id);
  if (appt) {
    rescheduleAppt.value = {
      id: appt.id,
      petName: appt.pet,
      serviceName: appt.service,
      dateTime: `${appt.time} - ${appt.date}`,
      date: appt.date,
      time: appt.time,
    };
    showReschedulePopup.value = true;
  }
};

const handleRescheduleConfirm = async (data) => {
  // data: { oldDateTime, newDate, newTime, newDateTime }
  try {
    const apptId = rescheduleAppt.value?.id || data.id;
    if (!apptId) throw new Error("Không xác định được lịch hẹn để đổi.");

    // newDate is DD/MM/YYYY -> convert to YYYY-MM-DD
    const parts = data.newDate.split("/");
    if (parts.length !== 3) throw new Error("Ngày mới không hợp lệ.");
    const day = parts[0].padStart(2, "0");
    const month = parts[1].padStart(2, "0");
    const year = parts[2];
    const ngayGio = `${year}-${month}-${day} ${data.newTime}:00`;

    // send PATCH to update ngay_gio
    const res = await axios.patch(`${API_BASE}/lich-hen/${apptId}/ngay-gio`, {
      ngay_gio: ngayGio,
    });

    if (res.data && res.data.status) {
      showSuccessToast("Đổi lịch", "Đã cập nhật ngày giờ lịch hẹn");
      // refresh appointments
      await fetchAppointments();
      showReschedulePopup.value = false;
      rescheduleAppt.value = null;
    } else {
      console.error("Unexpected response", res);
      showErrorToast("Đổi lịch", "Không thể đổi lịch. Vui lòng thử lại.");
    }
  } catch (err) {
    console.error("Lỗi khi đổi lịch:", err);
    const msg =
      err?.response?.data?.message || err.message || "Lỗi khi đổi lịch hẹn";
    showErrorToast("Đổi lịch", msg);
  }
};

const handleCancel = (id) => {
  const appt = upcomingAppointments.value.find((a) => a.id === id);
  if (appt) {
    cancelAppt.value = {
      id: appt.id,
      petName: appt.pet,
      serviceName: appt.service,
      paidAmount: "250.000", // Mock amount
    };
    // Mock logic to determine cancel status
    // In real app, check time diff and payment status
    const randomStatus = Math.random();
    if (randomStatus < 0.33) cancelStatus.value = "late";
    else if (randomStatus < 0.66) cancelStatus.value = "refundable";
    else cancelStatus.value = "unpaid";

    showCancelPopup.value = true;
  }
};

const handleCancelConfirm = async (data) => {
  // data: { appointmentId, reason, cancelStatus } emitted from HuyHen
  try {
    const id = data?.appointmentId || data?.id;
    if (!id) throw new Error("Không xác định được lịch hẹn để hủy.");

    const res = await axios.delete(`${API_BASE}/lich-hen/${id}`);
    if (res.data && res.data.status) {
      showSuccessToast("Hủy hẹn", "Lịch hẹn đã được hủy");
      // refresh list
      await fetchAppointments();
      showCancelPopup.value = false;
      cancelAppt.value = null;
    } else {
      console.error("Unexpected delete response", res);
      showErrorToast("Hủy hẹn", "Không thể hủy lịch hẹn. Vui lòng thử lại.");
    }
  } catch (err) {
    console.error("Lỗi khi hủy lịch hẹn:", err);
    const msg =
      err?.response?.data?.message || err.message || "Lỗi khi hủy lịch hẹn";
    showErrorToast("Hủy hẹn", msg);
  }
};

const viewResult = (id) => {
  const appt = pastAppointments.value.find((a) => a.id === id);
  if (appt) {
    // Mock result data
    resultData.value = {
      date: appt.date,
      time: appt.time,
      serviceName: appt.service,
      doctorName: appt.doctor,
      doctorSpecialty: "Nội khoa thú y",
      diagnosis:
        "Viêm da dị ứng do bọ chét. Thú cưng có biểu hiện ngứa nhiều vùng lưng và đuôi, da mẩn đỏ.",
      medicines: [
        {
          name: "Apoquel 5.4mg",
          dosage: "1 viên/ngày",
          instruction: "Uống sau khi ăn sáng",
        },
        {
          name: "Bravecto",
          dosage: "1 viên duy nhất",
          instruction: "Nhai trực tiếp hoặc trộn với thức ăn",
        },
      ],
      postCareGuideline:
        "Vệ sinh môi trường sống sạch sẽ, giặt giũ đệm nằm của thú cưng. Tái khám sau 2 tuần nếu triệu chứng không giảm.",
      attachments: [
        {
          name: "Ket_qua_xet_nghiem_mau.pdf",
          label: "Kết quả xét nghiệm",
          type: "pdf",
          url: "#",
        },
        {
          name: "Hinh_anh_vung_da.jpg",
          label: "Hình ảnh lâm sàng",
          type: "image",
          url: "#",
        },
      ],
    };
    showResultPopup.value = true;
  }
};

const rebook = (id) => {
  const appt =
    pastAppointments.value.find((a) => a.id === id) ||
    upcomingAppointments.value.find((a) => a.id === id);
  if (appt) {
    rebookData.value = {
      petName: appt.pet,
      serviceName: appt.service,
    };
    showBookingPopup.value = true;
  }
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700;800&family=Inter:wght@700&display=swap");

.font-nunito {
  font-family: "Nunito Sans", sans-serif;
}
</style>
