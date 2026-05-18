<template>
  <div class="relative w-full h-full px-8 py-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-semibold text-black">Dashboard - Sảnh chờ</h1>
        <p class="text-base font-medium text-gray-500">
          Quản lý hàng chờ & điều phối khách hàng
        </p>
      </div>
      <div class="flex items-center gap-3">
        <button
          class="bg-[#009689] rounded-lg px-4 py-2.5 h-10 flex items-center gap-2 hover:bg-[#007d72] transition-colors"
          @click="openIntakeModal"
        >
          <!-- <img :src="iconCalendarPlus" alt="Create" class="w-4 h-4" /> -->
          <span class="text-sm font-medium text-white"> Tiếp nhận bệnh nhân </span>
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-4 gap-4 mb-6">
      <div
        class="bg-white border !border-gray-300 rounded-[14px] p-4 shadow-sm"
      >
        <div class="flex items-center justify-between">
          <div class="flex flex-col gap-1">
            <p class="text-sm text-gray-600">Sắp đến</p>
            <p class="text-2xl font-bold text-[#155dfc]">
              {{ stats.upcoming }}
            </p>
          </div>
          <!-- <img :src="iconClock" alt="Upcoming" class="w-8 h-8" /> -->
        </div>
      </div>

      <div
        class="bg-white border !border-gray-300 rounded-[14px] p-4 shadow-sm"
      >
        <div class="flex items-center justify-between">
          <div class="flex flex-col gap-1">
            <p class="text-sm text-gray-600">Đang chờ</p>
            <p class="text-2xl font-bold text-[#f54900]">
              {{ stats.waiting }}
            </p>
          </div>
          <!-- <img :src="iconHourglass" alt="Waiting" class="w-8 h-8" /> -->
        </div>
      </div>

      <div
        class="bg-white border !border-gray-300 rounded-[14px] p-4 shadow-sm"
      >
        <div class="flex items-center justify-between">
          <div class="flex flex-col gap-1">
            <p class="text-sm text-gray-600">Chờ thanh toán</p>
            <p class="text-2xl font-bold text-[#00a63e]">
              {{ stats.payment }}
            </p>
          </div>
          <!-- <img :src="iconDollar" alt="Payment" class="w-8 h-8" /> -->
        </div>
      </div>

      <div
        class="bg-white border !border-gray-300 rounded-[14px] p-4 shadow-sm"
      >
        <div class="flex items-center justify-between">
          <div class="flex flex-col gap-1">
            <p class="text-sm text-gray-600">Tổng hôm nay</p>
            <p class="text-2xl font-bold text-[#9810fa]">
              {{ stats.total }}
            </p>
          </div>
          <!-- <img :src="iconUsers" alt="Total" class="w-8 h-8" /> -->
        </div>
      </div>
    </div>

    <!-- Queue Card -->
    <div
      class="bg-white border-2 !border-[#bedbff] rounded-[14px] p-6 shadow-sm"
    >
      <!-- Queue Header -->
      <div class="bg-blue-50 rounded-lg p-3 flex items-center gap-2 mb-4">
        <!-- <img :src="iconQueue" alt="Queue" class="w-5 h-5" /> -->
        <p class="text-base font-semibold text-black">Hàng chờ hôm nay</p>
      </div>

      <!-- Appointments List -->
      <div v-if="loading" class="flex justify-center items-center py-8">
        <span class="text-gray-500">Đang tải dữ liệu...</span>
      </div>
      <div v-else class="flex flex-col">
        <div
          v-for="(appointment, index) in appointments"
          :key="index"
          class="border-b !border-gray-300 last:border-0 py-4 flex flex-col gap-3"
        >
          <!-- Badges Row -->
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <!-- Type Badge -->
              <span
                v-if="appointment.type === 'scheduled'"
                class="bg-blue-50 border !border-[#bedbff] rounded-lg px-2 py-1 flex items-center gap-1.5"
              >
                <!-- <img :src="iconCalendar" alt="Scheduled" class="w-3 h-3" /> -->
                <span class="text-xs font-medium text-[#1447e6]">
                  Đặt trước
                </span>
              </span>
              <span
                v-else-if="appointment.type === 'walkin'"
                class="bg-purple-50 border !border-[#e9d4ff] rounded-lg px-2 py-1 flex items-center gap-1.5"
              >
                <!-- <img :src="iconWalkIn" alt="Walk-in" class="w-3 h-3" /> -->
                <span class="text-xs font-medium text-[#8200db]">
                  Đến trực tiếp
                </span>
              </span>
              <span
                v-else-if="appointment.type === 'member'"
                class="bg-blue-50 border !border-blue-300 rounded-lg px-2 py-1 flex items-center gap-1.5"
              >
                <!-- <img :src="iconMember" alt="Member" class="w-3 h-3" /> -->
                <span class="text-xs font-medium text-blue-700">
                  Thành Viên
                </span>
              </span>

              <!-- Status Badge -->
              <span
                v-if="appointment.status === 'upcoming'"
                class="bg-blue-100 border !border-blue-300 rounded-lg px-3 py-1"
              >
                <span class="text-xs font-medium text-blue-700"> Sắp đến </span>
              </span>
              <span
                v-else-if="appointment.status === 'arrived'"
                class="bg-green-100 border !border-[#7bf1a8] rounded-lg px-3 py-1"
              >
                <span class="text-xs font-medium text-[#008236]"> Đã đến </span>
              </span>
              <span
                v-else-if="appointment.status === 'examining'"
                class="bg-orange-100 border !border-orange-300 rounded-lg px-3 py-1"
              >
                <span class="text-xs font-medium text-orange-700"> Đang khám </span>
              </span>
              <span
                v-else-if="appointment.status === 'payment'"
                class="bg-emerald-100 border !border-emerald-300 rounded-lg px-2 py-1"
              >
                <span class="text-xs font-medium text-emerald-700">
                  Chờ thanh toán
                </span>
              </span>
            </div>

            <!-- Time Badge -->
            <span
              v-if="appointment.delay"
              :class="[
                'rounded-lg px-2 py-1 flex items-center gap-1.5',
                appointment.delay.type === 'late'
                  ? 'bg-red-50 border !border-red-300'
                  : appointment.delay.type === 'waiting'
                  ? 'bg-[#ffedd4]'
                  : appointment.delay.type === 'short'
                  ? 'bg-gray-100'
                  : 'bg-cyan-50 border !border-cyan-200',
              ]"
            >
              <!-- <img :src="appointment.delay.icon" alt="Time" class="w-3 h-3" /> -->
              <span
                :class="[
                  'text-xs font-medium',
                  appointment.delay.type === 'late'
                    ? 'text-red-600'
                    : appointment.delay.type === 'waiting'
                    ? 'text-[#ca3500]'
                    : appointment.delay.type === 'short'
                    ? 'text-gray-700'
                    : 'text-cyan-600',
                ]"
              >
                {{ appointment.delay.text }}
              </span>
            </span>
          </div>

          <!-- Pet & Owner Info -->
          <div class="flex items-center gap-3">
            <img
              :src="appointment.petImage"
              :alt="appointment.petName"
              class="w-12 h-12 rounded-[10px] object-cover"
              @error="(e) => e.target.src = defaultPetImage"
            />
            <div class="flex flex-col gap-1.5">
              <div class="flex items-center gap-3">
                <p class="text-base font-bold text-black">
                  {{ appointment.petName }}
                </p>
                <p v-if="appointment.petSpecies || appointment.petType" class="text-sm text-gray-600">
                  {{ [appointment.petSpecies, appointment.petType].filter(Boolean).join(' - ') }}
                </p>
                <p class="text-sm text-gray-700">
                  Chủ: {{ appointment.ownerName }}
                </p>
                <span
                  v-if="appointment.daTTTruoc"
                  class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700"
                >
                  Đã TT trước
                </span>
              </div>
              <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                  <p class="text-sm text-gray-600">
                    {{ appointment.type === 'walkin' ? 'Tiếp nhận:' : 'Giờ hẹn:' }}
                  </p>
                  <p class="text-sm font-bold text-[#1447e6]">
                    {{ appointment.appointmentTime }}
                  </p>
                </div>
                <div class="flex items-center gap-1.5">
                  <!-- <img
                    :src="appointment.checkedIn ? iconCheckPurple : iconCheckGray"
                    alt="Check-in"
                    class="w-3 h-3"
                  /> -->
                  <p
                    :class="[
                      'text-sm',
                      appointment.checkedIn
                        ? 'text-purple-700'
                        : 'text-gray-500',
                    ]"
                  >
                    Check-in:
                    <span v-if="appointment.checkedIn" class="font-bold">
                      {{ appointment.checkInTime }}
                    </span>
                    <span v-else>--:--</span>
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Room Assignment (if checked in) -->
          <div
            v-if="appointment.room"
            class="bg-blue-50 border-2 !border-[#8ec5ff] rounded-[10px] px-3 py-2 flex items-center justify-between"
          >
            <p class="text-xs text-gray-600">Mời vào:</p>
            <p class="text-base font-bold text-[#1c398e]">
              {{ appointment.room }}
            </p>
          </div>

          <!-- Service & Doctor Info -->
          <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between">
              <p class="text-sm text-gray-600">Dịch vụ:</p>
              <p class="text-base font-medium text-black">
                {{ appointment.service }}
              </p>
            </div>
            <div class="flex items-center justify-between">
              <p class="text-sm text-gray-600">Bác sĩ:</p>
              <p class="text-sm font-medium text-gray-700">
                {{ appointment.doctor }}
              </p>
            </div>
          </div>

          <!-- Action Button -->
          <button
            v-if="appointment.status === 'upcoming'"
            class="bg-blue-600 rounded-lg h-10 w-full flex items-center justify-center gap-2 hover:bg-blue-700 transition-colors"
            @click="checkIn(appointment)"
          >
            <!-- <img :src="iconCheckIn" alt="Check-in" class="w-4 h-4" /> -->
            <span class="text-sm font-medium text-white"> Check-In </span>
          </button>
          <div
            v-else-if="appointment.status === 'examining'"
            class="bg-orange-50 border !border-orange-200 rounded-lg h-10 w-full flex items-center justify-center"
          >
            <span class="text-sm font-medium text-orange-600">⏳ Bác sĩ đang khám...</span>
          </div>
          <button
            v-else-if="appointment.status === 'payment'"
            class="bg-green-600 rounded-lg h-10 w-full flex items-center justify-center gap-2 hover:bg-green-700 transition-colors"
            @click="collectPayment(appointment)"
          >
            <span class="text-sm font-medium text-white"> Thu tiền </span>
          </button>
        </div>
      </div>
    </div>

    <!-- Check-in Modal -->
    <CheckInModal
      :is-open="isCheckInModalOpen"
      :appointment="selectedAppointment"
      @close="isCheckInModalOpen = false"
      @success="handleCheckInConfirm"
    />

    <!-- Unified Intake Modal -->
    <UnifiedIntakeModal
      :is-open="isIntakeModalOpen"
      :prefilled-customer="prefilledCustomer"
      @close="isIntakeModalOpen = false"
      @success="handleIntakeSuccess"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import CheckInModal from "../appointment/check-in-modal.vue";
import UnifiedIntakeModal from "../appointment/unified-intake-modal.vue";
import { getAllAppointments } from "../../../services/lichHenService";
import { resolveImageUrl } from "../../../utils/image";

const router = useRouter();

// Icons from Figma
const iconCalendarPlus =
  "https://www.figma.com/api/mcp/asset/eb7ba029-87ac-483f-a065-c7a95b006ca8";
const iconUserPlus =
  "https://www.figma.com/api/mcp/asset/4ee8bff8-974f-463d-9e56-5d8ebb28c911";
const iconClock =
  "https://www.figma.com/api/mcp/asset/0af944c2-9662-4e84-90c9-e1e0603dfe40";
const iconHourglass =
  "https://www.figma.com/api/mcp/asset/51760b53-8078-456b-839e-b2740b85f9b5";
const iconDollar =
  "https://www.figma.com/api/mcp/asset/2d39fef4-1bb4-49bf-a962-ea912dadd540";
const iconUsers =
  "https://www.figma.com/api/mcp/asset/27d4694f-74ed-4d7c-a4ac-565abd0a5b67";
const iconQueue =
  "https://www.figma.com/api/mcp/asset/41c800ea-01f1-421f-beb6-91673805bcf6";
const iconCalendar =
  "https://www.figma.com/api/mcp/asset/b806e6b2-5eb0-4316-967e-e20db0fb53fb";
const iconWalkIn =
  "https://www.figma.com/api/mcp/asset/b1d7a0e0-a91e-44ac-8e6d-0a7194b71d4a";
const iconMember =
  "https://www.figma.com/api/mcp/asset/43a32373-ad71-4294-b528-ef85402b0bda";
const iconCheckGray =
  "https://www.figma.com/api/mcp/asset/ac3540cb-5ef7-4bdb-9121-71953068bffd";
const iconCheckPurple =
  "https://www.figma.com/api/mcp/asset/d802f853-3fdf-461c-8836-1d9526531d0d";
const iconCheckIn =
  "https://www.figma.com/api/mcp/asset/a382a642-b1f1-4f96-8d19-8faf985a760d";
const iconMoney =
  "https://www.figma.com/api/mcp/asset/0b5f2b69-d162-4cfa-9d82-946bf1295c71";
const iconLate =
  "https://www.figma.com/api/mcp/asset/a3ca31b5-42c5-4af6-aaaf-04e195c5d752";
const iconWaiting =
  "https://www.figma.com/api/mcp/asset/3a61ff7d-568a-4854-9b64-adedb69ebe1c";
const iconShort =
  "https://www.figma.com/api/mcp/asset/e641182a-026e-40bd-99a0-5b3ef2d1e51c";
const iconRemaining =
  "https://www.figma.com/api/mcp/asset/e525afb8-5c7d-455b-a40a-a1f4d0c79573";

const defaultPetImage = "https://placehold.co/100x100/e2e8f0/64748b?text=Pet";

// Loading State
const loading = ref(true);

// Stats
const stats = ref({
  upcoming: 0,
  waiting: 0,
  payment: 0,
  total: 0,
});

// Appointments data
const appointments = ref([]);

const isWalkInSource = (item) =>
  item?.nguon_goc === "walk-in" ||
  item?.nguon_goc === "walkin" ||
  (item?.ghi_chu && item.ghi_chu.toLowerCase().includes("walk-in")) ||
  (item?.ghi_chu && item.ghi_chu.toLowerCase().includes("vãng lai"));

const loadDashboardData = async () => {
  loading.value = true;
  try {
    const today = new Date(Date.now() + 7 * 60 * 60 * 1000)
      .toISOString().split('T')[0];

    // Fetch song song: lịch hẹn hôm nay + tất cả chưa thanh toán
    const [res, unpaidRes] = await Promise.all([
      getAllAppointments({
        per_page: 100,
        from_date: today + ' 00:00:00',
        to_date: today + ' 23:59:59',
      }),
      getAllAppointments({
        per_page: 100,
        trang_thai: 'completed',
        chua_thanh_toan: true,
      }),
    ]);

    const data = res.data || [];
    const unpaidData = unpaidRes.data || [];

    let upcoming = 0;
    let waiting = 0;

    appointments.value = data
      .map((item) => {
        const trangThai = item.trang_thai;
        const isWalkIn = isWalkInSource(item);
        let statusGroup;
        if (trangThai === 'pending' || trangThai === 'confirmed') {
          statusGroup = 'upcoming';
        } else if (trangThai === 'checked_in') {
          statusGroup = 'arrived';
        } else if (trangThai === 'in-progress' || trangThai === 'dang_kham') {
          statusGroup = item.thoi_gian_bat_dau_kham ? 'examining' : 'arrived';
        } else if (trangThai === 'completed' || trangThai === 'cho_thanh_toan') {
          statusGroup = (item.da_thanh_toan || item.thanh_toan_id) ? 'done' : 'payment';
        } else {
          statusGroup = 'done';
        }

        if (statusGroup === 'upcoming') upcoming++;
        if (statusGroup === 'arrived') waiting++;

      return {
        raw: item,
        id: item.id,
        type: isWalkIn ? "walkin" : "scheduled",
        status: statusGroup,
        petName: item.thu_cung?.ten_thu_cung || 'N/A',
        petType: item.thu_cung?.giong_thu_cung || item.thu_cung?.giong || '',
        petSpecies: item.thu_cung?.loai_thu_cung || '',
        petImage: resolveImageUrl(item.thu_cung?.anh_dai_dien_url || item.thu_cung?.anh_dai_dien) || defaultPetImage,
        ownerName: item.khach_hang?.full_name || 'N/A',
        phone: item.khach_hang?.phone || item.khach_hang?.so_dien_thoai || null,
        appointmentTime: (item.thoi_gian_checkin || item.ngay_gio)
          ? new Date(item.thoi_gian_checkin || item.ngay_gio).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })
          : '--:--',
        ngay_gio: item.ngay_gio,
        checkedIn: !!item.thoi_gian_checkin,
        checkInTime: item.thoi_gian_checkin
          ? new Date(item.thoi_gian_checkin).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })
          : null,
        service: item.dich_vus?.length ? item.dich_vus.map(d => d.ten).join(", ") : (item.dich_vu?.ten_dich_vu || item.dich_vu?.ten || 'N/A'),
        doctor: item.nhan_vien?.full_name || 'N/A',
        room: item.nhan_vien?.phong_kham || null,
        daTTTruoc: !!item.da_thanh_toan,
        delay: null,
      };
    });

    // Chỉ hiển thị trong hàng chờ: bỏ qua trạng thái "done" (đã thanh toán / hủy)
    appointments.value = appointments.value.filter(a => a.status !== 'done');

    // "Chờ thanh toán" = tất cả lịch hẹn completed chưa thanh toán (không lọc ngày)
    const payment = unpaidData.length;

    stats.value = {
      upcoming,
      waiting,
      payment,
      total: data.length,
    };
  } catch (error) {
    console.error("Dashboard data error:", error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadDashboardData();
});

// Modal State
const isCheckInModalOpen = ref(false);
const isIntakeModalOpen = ref(false);
const selectedAppointment = ref(null);
const prefilledCustomer = ref(null);

// Methods
const openIntakeModal = () => {
  prefilledCustomer.value = null;
  isIntakeModalOpen.value = true;
};

const handleIntakeSuccess = () => {
  isIntakeModalOpen.value = false;
  loadDashboardData();
};

const checkIn = (appointment) => {
  // Truyền raw data để modal hiển thị đúng các trường nested
  selectedAppointment.value = appointment.raw;
  isCheckInModalOpen.value = true;
};

const handleCheckInConfirm = (updatedData) => {
  const id = updatedData?.id || selectedAppointment.value?.id;
  const index = appointments.value.findIndex((a) => a.id === id);
  if (index !== -1) {
    appointments.value[index].status = "arrived";
    appointments.value[index].checkedIn = true;
    appointments.value[index].checkInTime = new Date().toLocaleTimeString(
      "vi-VN",
      { hour: "2-digit", minute: "2-digit" }
    );
    stats.value.upcoming = Math.max(0, stats.value.upcoming - 1);
    stats.value.waiting++;
  }
  isCheckInModalOpen.value = false;
};

const collectPayment = (appointment) => {
  router.push({
    path: '/nurse/invoices',
    query: { lich_hen_id: appointment.id }
  });
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}
</style>
