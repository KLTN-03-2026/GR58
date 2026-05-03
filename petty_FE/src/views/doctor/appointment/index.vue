<template>
  <div class="w-full min-h-screen px-8 py-6 flex flex-col gap-6">
    <!-- Header -->
    <div class="flex flex-col gap-2">
      <h1 class="text-2xl font-semibold text-black">Bệnh nhân chờ khám</h1>
      <p class="text-gray-500 font-medium text-base">
        Danh sách bệnh nhân đã check-in và chờ được khám bệnh
      </p>
    </div>

    <!-- Toolbar -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-4">
      <div class="flex items-center gap-3 mb-4">
        <button
          @click="previousDay"
          :disabled="loading"
          class="w-10 h-10 rounded-lg border !border-gray-300 hover:bg-gray-50 flex items-center justify-center"
        >
          <ChevronLeftIcon />
        </button>

        <div class="flex-1 bg-gray-50 rounded-xl px-8 py-3 text-center">
          <p class="font-medium text-[#101828]">{{ currentDateText }}</p>
        </div>

        <button
          @click="nextDay"
          :disabled="loading"
          class="w-10 h-10 rounded-lg border !border-gray-300 hover:bg-gray-50 flex items-center justify-center"
        >
          <ChevronRightIcon />
        </button>

        <button
          @click="goToToday"
          class="h-10 px-6 bg-[#5a9690] text-white rounded-lg hover:bg-[#4a857f] font-medium"
        >
          Hôm nay
        </button>
      </div>

      <!-- Tabs -->
      <div class="flex gap-3">
        <button
          v-for="tab in statusTabs"
          :key="tab.value"
          @click="activeTab = tab.value"
          :class="[
            'h-10 px-5 rounded-xl text-sm font-medium transition-colors',
            activeTab === tab.value
              ? 'bg-[#155dfc] text-white'
              : 'bg-gray-100 text-[#364153] hover:bg-gray-200',
          ]"
        >
          {{ tab.label }} <span class="opacity-75 ml-1">({{ tab.count }})</span>
        </button>
      </div>
    </div>

    <!-- Loading / Empty -->
    <div v-if="loading" class="text-center py-12 text-gray-500">
      Đang tải lịch khám...
    </div>
    <div v-else-if="error" class="text-center py-12 text-red-600">
      {{ error }}
    </div>
    <div
      v-else-if="displayAppointments.length === 0"
      class="text-center py-12 text-gray-500"
    >
      Không có lịch khám nào trong ngày {{ formatDate(currentDate) }}.
    </div>

    <!-- Appointment List -->
    <div v-else class="flex flex-col gap-4">
      <div
        v-for="appt in displayAppointments"
        :key="appt.id"
        class="bg-white rounded-[14px] border overflow-hidden"
        :class="{
          'border-l-4 border-l-[#60a5fa] !border-t !border-r !border-b !border-gray-300':
            appt.displayStatus.type === 'late',
          'border-l-4 border-l-[#fb923c] !border-t !border-r !border-b !border-gray-300':
            appt.displayStatus.type === 'examining',
          '!border-gray-300':
            appt.displayStatus.type !== 'late' &&
            appt.displayStatus.type !== 'examining',
        }"
      >
        <div class="flex items-stretch min-h-[200px]">
          <!-- Time Column -->
          <div
            class="w-44 border-r !border-gray-300 p-6 flex flex-col justify-between"
          >
            <div>
              <!-- Giờ hẹn -->
              <p class="text-3xl font-medium text-[#101828]">
                {{ formatTime(appt.ngay_gio) }}
              </p>

              <!-- Giờ check-in -->
              <div class="mt-3 flex items-center gap-2 text-sm">
                <!-- <img
                  src="@/assets/svg/clock.svg"
                  class="w-4 h-4 brightness-75 hue-rotate-[270deg]"
                /> -->
                <span class="text-[#9810fa]">
                  Check-in: {{ formatTime(appt.thoi_gian_checkin) }}
                </span>
              </div>

              <!-- Thời gian chờ -->
              <div
                v-if="appt.displayStatus.waitMinutes"
                class="mt-2 text-xs text-gray-500"
              >
                Đã chờ: {{ appt.displayStatus.waitMinutes }} phút
              </div>

              <!-- Thời gian khám -->
              <div
                v-if="appt.displayStatus.examMinutes"
                class="mt-2 text-xs text-orange-600"
              >
                Đang khám: {{ appt.displayStatus.examMinutes }} phút
              </div>
            </div>

            <div class="flex flex-col gap-2">
              <!-- Status Badge -->
              <div
                class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-medium"
                :class="{
                  'bg-[#dbe7ff] text-[#1447e6]':
                    appt.displayStatus.type === 'waiting',
                  'bg-orange-100 text-[#ea580c]':
                    appt.displayStatus.type === 'examining',
                  'bg-green-100 text-[#00a63e]':
                    appt.displayStatus.type === 'completed',
                }"
              >
                <!-- <img
                  v-if="appt.displayStatus.type === 'completed'"
                  src="@/assets/svg/tick.svg"
                  class="w-4 h-4"
                /> -->
                {{ appt.displayStatus.label }}
              </div>

              <!-- Thời gian chờ -->
              <div
                v-if="appt.displayStatus.waitMinutes > 0"
                class="px-3 py-1 rounded-lg text-xs bg-gray-100 text-gray-600"
              >
                Chờ {{ appt.displayStatus.waitMinutes }} phút
              </div>

              <!-- Thời gian khám -->
              <div
                v-if="appt.displayStatus.examMinutes > 0"
                class="px-3 py-1 rounded-lg text-xs bg-orange-100 text-orange-700"
              >
                Khám {{ appt.displayStatus.examMinutes }} phút
              </div>
            </div>
          </div>

          <!-- Pet & Customer Info -->
          <div class="flex-1 p-6 flex gap-6">
            <div
              class="w-28 h-28 border-2 !border-gray-300 rounded-2xl overflow-hidden flex-shrink-0"
            >
              <img
                v-if="appt.thu_cung?.anh_dai_dien"
                :src="appt.thu_cung.anh_dai_dien"
                class="w-full h-full object-cover"
              />
              <div
                v-else
                class="w-full h-full bg-gray-50 flex items-center justify-center text-6xl"
              >
                <!-- Emoji removed -->
              </div>
            </div>

            <div class="flex-1 flex flex-col gap-3">
              <div class="flex items-center gap-3 flex-wrap">
                <h3 class="text-xl font-bold text-[#101828]">
                  {{ appt.thu_cung?.ten_thu_cung || "Chưa có tên" }}
                </h3>
                <span class="text-[#101828]"
                  >({{
                    appt.thu_cung?.giong_thu_cung ||
                    appt.thu_cung?.loai_thu_cung ||
                    "?"
                  }})</span
                >
                <span class="text-sm text-[#4a5565]"
                  >- {{ calculateAge(appt.thu_cung?.tuoi_thu_cung) }}</span
                >

                <span
                  class="px-3 py-1 rounded-lg text-xs font-medium flex items-center gap-1"
                  :class="
                    appt.la_khach_vang_lai
                      ? 'bg-[#f2e6ff] text-[#8200db]'
                      : 'bg-[#dbe7ff] text-[#1447e6]'
                  "
                >
                  <!-- <img
                    v-if="!appt.la_khach_vang_lai"
                    src="@/assets/svg/calendar.svg"
                    class="w-4 h-4"
                  />
                  <img
                    v-else
                    src="@/assets/svg/user-round.svg"
                    class="w-4 h-4"
                  /> -->
                  {{ appt.la_khach_vang_lai ? "Vãng lai" : "Đặt trước" }}
                </span>
              </div>

              <div class="flex items-center gap-3 text-sm">
                <span class="text-[#364153]">Chủ:</span>
                <span class="font-bold">{{
                  appt.khach_hang?.full_name || "Chưa có chủ nuôi"
                }}</span>
                <span class="text-[#99a1af]">•</span>
                <div class="flex items-center gap-1">
                  <!-- <img src="@/assets/svg/phonecall.svg" class="w-4 h-4" /> -->
                  <span class="text-[#4a5565]">{{
                    appt.khach_hang?.phone ||
                    appt.khach_hang?.so_dien_thoai ||
                    "Chưa có"
                  }}</span>
                </div>
              </div>

              <div
                class="inline-flex px-3 py-1 bg-blue-50 border !border-[#bedbff] rounded-lg text-xs text-[#1447e6] w-fit"
              >
                {{ appt.dich_vu?.ten_dich_vu || appt.dich_vu?.ten || "Khám tổng quát" }}
              </div>

              <!-- Ghi chú -->
              <div
                v-if="appt.ghi_chu"
                class="p-3 bg-amber-50 border !border-[#fee685] rounded-xl"
              >
                <div class="flex gap-3 text-sm text-[#7b3306]">
                  <!-- <img
                    src="@/assets/svg/info-circle.svg"
                    class="w-5 h-5 flex-shrink-0"
                  /> -->
                  <p>
                    <span class="font-bold">Ghi chú:</span> {{ appt.ghi_chu }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Button -->
          <div class="w-64 p-6 flex items-center justify-end">
            <!-- Đang khám → TIẾP TỤC -->
            <button
              v-if="appt.displayStatus.type === 'examining'"
              @click="startExam(appt.id)"
              class="w-full h-11 bg-[#f54900] text-white rounded-xl font-medium hover:bg-[#ca3500]"
            >
              TIẾP TỤC KHÁM
            </button>

            <!-- Hoàn thành → Xem lại -->
            <button
              v-else-if="appt.displayStatus.type === 'completed'"
              @click="viewExamDetail(appt.id)"
              class="w-full h-11 border !border-gray-300 rounded-xl flex items-center justify-center gap-2 text-gray-700 hover:bg-gray-50"
            >
              <!-- <img src="@/assets/svg/eye.svg" class="w-5 h-5" /> -->
              Xem kết quả
            </button>

            <!-- Chờ khám → BẮT ĐẦU KHÁM -->
            <button
              v-else-if="appt.displayStatus.type === 'waiting'"
              @click="confirmStartExam(appt)"
              class="w-full h-11 bg-[#155dfc] text-white rounded-xl flex items-center justify-center gap-2 hover:bg-[#1447e6]"
            >
              <svg
                class="w-5 h-5"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <circle
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="2"
                />
                <path d="M10 8L16 12L10 16V8Z" fill="currentColor" />
              </svg>
              BẮT ĐẦU KHÁM
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "@/utils/api";
import { getUser } from "@/utils/auth";
import { format, differenceInMinutes, parseISO } from "date-fns";
import { vi } from "date-fns/locale";
//Icon SVG
import ChevronRightIcon from "@/assets/svg/chevron-right.svg";
import ChevronLeftIcon from "@/assets/svg/chevron-left.svg";

// State
const router = useRouter();
const currentDate = ref(new Date());
const appointments = ref([]); // Bệnh nhân chờ khám
const examiningAppointments = ref([]); // Bệnh nhân đang khám
const completedAppointments = ref([]); // Bệnh nhân đã hoàn thành
const loading = ref(false);
const error = ref(null);
const activeTab = ref("waiting"); // Mặc định tab "Chờ khám"

// Format helpers
const formatDate = (date) => format(date, "dd/MM/yyyy");
const formatTime = (dateString) => {
  if (!dateString) return "--:--";
  try {
    // Xử lý nhiều format datetime
    let date;
    if (typeof dateString === "string") {
      // Nếu là string có dạng "YYYY-MM-DD HH:mm:ss" (MySQL format)
      date = new Date(dateString.replace(" ", "T"));
    } else {
      date = new Date(dateString);
    }

    if (isNaN(date.getTime())) {
      // Fallback: thử parseISO
      date = parseISO(dateString);
    }

    return format(date, "HH:mm");
  } catch (error) {
    console.error("Error formatting time:", dateString, error);
    return "--:--";
  }
};
const currentDateText = computed(() => {
  return format(currentDate.value, "EEEE, dd/MM/yyyy", { locale: vi }).replace(
    /^\w/,
    (c) => c.toUpperCase()
  );
});

// Tính tuổi thú cưng
const calculateAge = (birthDate) => {
  if (!birthDate) return "Chưa rõ tuổi";
  const years = new Date().getFullYear() - new Date(birthDate).getFullYear();
  return `${years} tuổi`;
};

// Xác định trạng thái hiển thị của bệnh nhân (sau khi đã check-in)
const getStatus = (appt) => {
  const now = new Date();

  // Helper function to parse datetime
  const parseDateTime = (dateString) => {
    if (!dateString) return null;
    try {
      if (typeof dateString === "string") {
        // MySQL datetime format: "YYYY-MM-DD HH:mm:ss"
        return new Date(dateString.replace(" ", "T"));
      }
      return new Date(dateString);
    } catch (error) {
      console.error("Error parsing datetime:", dateString, error);
      return null;
    }
  };

  // Hoàn thành khám
  if (appt.trang_thai === "completed" && appt.thoi_gian_hoan_thanh) {
    return { type: "completed", label: "Hoàn thành", color: "green" };
  }

  // Đang khám: có thoi_gian_bat_dau_kham nhưng chưa hoàn thành
  if (appt.thoi_gian_bat_dau_kham && !appt.thoi_gian_hoan_thanh) {
    const startTime = parseDateTime(appt.thoi_gian_bat_dau_kham);
    const examMinutes = startTime ? differenceInMinutes(now, startTime) : 0;
    return {
      type: "examining",
      label: "Đang khám",
      color: "orange",
      examMinutes: examMinutes,
    };
  }

  // Chờ khám: đã check-in nhưng chưa bắt đầu khám
  if (appt.thoi_gian_checkin && !appt.thoi_gian_bat_dau_kham) {
    const checkInTime = parseDateTime(appt.thoi_gian_checkin);
    const waitMinutes = checkInTime ? differenceInMinutes(now, checkInTime) : 0;
    return {
      type: "waiting",
      label: "Chờ khám",
      waitMinutes: waitMinutes,
      color: "blue",
    };
  }

  // Fallback
  return { type: "arrived", label: "Đã đến", color: "green" };
};

// Tabs count - Dựa trên bệnh nhân đã check-in
const statusTabs = computed(() => {
  // Chờ khám: đã check-in, chưa bắt đầu khám
  const waiting = appointments.value.length;

  // Đang khám: có thoi_gian_bat_dau_kham nhưng chưa hoàn thành
  const examining = examiningAppointments.value.length;

  // Tổng số
  const completed = completedAppointments.value.length;
  const all = waiting + examining + completed;

  return [
    { label: "Chờ khám", value: "waiting", count: waiting },
    { label: "Đang khám", value: "examining", count: examining },
    { label: "Hoàn thành", value: "completed", count: completed },
    { label: "Tất cả", value: "all", count: all },
  ];
});

const displayAppointments = computed(() => {
  let list = [];

  if (activeTab.value === "waiting") {
    // Tab chờ khám - lấy từ appointments
    list = [...appointments.value];
  } else if (activeTab.value === "examining") {
    // Tab đang khám - lấy từ examiningAppointments
    list = [...examiningAppointments.value];
  } else if (activeTab.value === "completed") {
    list = [...completedAppointments.value];
  } else {
    // Tab tất cả - gộp toàn bộ workflow trong ngày
    list = [
      ...appointments.value,
      ...examiningAppointments.value,
      ...completedAppointments.value,
    ];
  }

  // Sắp xếp theo thời gian check-in (ai check-in trước khám trước)
  return list.sort((a, b) => {
    const aTime = a.thoi_gian_checkin || a.ngay_gio;
    const bTime = b.thoi_gian_checkin || b.ngay_gio;
    return new Date(aTime) - new Date(bTime);
  });
});

// Lấy dữ liệu từ API - CHỈ BỆNH NHÂN ĐÃ CHECK-IN (chờ khám)
const fetchAppointments = async () => {
  error.value = null;
  try {
    const dateStr = format(currentDate.value, "yyyy-MM-dd");

    // Sử dụng API /benh-nhan-cho-kham - danh sách bệnh nhân đã check-in, chưa bắt đầu khám
    const res = await api.get("/benh-nhan-cho-kham", {
      params: {
        ngay: dateStr,
        per_page: 100,
      },
    });

    // Handle both paginated and non-paginated responses
    let data = [];
    if (res.data.status && res.data.data) {
      if (Array.isArray(res.data.data)) {
        data = res.data.data;
      } else if (res.data.data.data && Array.isArray(res.data.data.data)) {
        data = res.data.data.data;
      }
    }
    appointments.value = data.map((appt) => ({
      ...appt,
      displayStatus: getStatus(appt),
    }));
  } catch (err) {
    console.error("Error fetching appointments:", err);
    error.value =
      err.response?.data?.message || "Không thể tải danh sách chờ khám.";
  }
};

// Lấy danh sách bệnh nhân đang khám
const fetchExaminingAppointments = async () => {
  try {
    const dateStr = format(currentDate.value, "yyyy-MM-dd");

    const res = await api.get("/benh-nhan-dang-kham", {
      params: {
        ngay: dateStr,
        per_page: 100,
      },
    });

    let data = [];
    if (res.data.status && res.data.data) {
      if (Array.isArray(res.data.data)) {
        data = res.data.data;
      } else if (res.data.data.data && Array.isArray(res.data.data.data)) {
        data = res.data.data.data;
      }
    }

    examiningAppointments.value = data.map((appt) => ({
      ...appt,
      displayStatus: getStatus(appt),
    }));
  } catch (err) {
    console.error("Error fetching examining appointments:", err);
    error.value =
      err.response?.data?.message || "Không thể tải danh sách đang khám.";
  }
};

// Lấy danh sách bệnh nhân đã hoàn thành trong ngày
const fetchCompletedAppointments = async () => {
  try {
    const dateStr = format(currentDate.value, "yyyy-MM-dd");
    const currentDoctor = getUser("bac_si");
    const res = await api.get("/lich-hen-all", {
      params: {
        from_date: `${dateStr} 00:00:00`,
        to_date: `${dateStr} 23:59:59`,
        trang_thai: "completed",
        per_page: 100,
        ...(currentDoctor?.id ? { nhan_vien_id: currentDoctor.id } : {}),
      },
    });

    let data = [];
    if (res.data.status && res.data.data) {
      if (Array.isArray(res.data.data)) {
        data = res.data.data;
      } else if (res.data.data.data && Array.isArray(res.data.data.data)) {
        data = res.data.data.data;
      }
    }

    completedAppointments.value = data.map((appt) => ({
      ...appt,
      displayStatus: {
        type: "completed",
        label: "Hoàn thành",
        color: "green",
      },
    }));
  } catch (err) {
    console.error("Error fetching completed appointments:", err);
    error.value =
      err.response?.data?.message || "Không thể tải danh sách hoàn thành.";
  }
};

// Fetch tất cả dữ liệu
const fetchAllData = async () => {
  loading.value = true;
  error.value = null;
  await Promise.all([
    fetchAppointments(),
    fetchExaminingAppointments(),
    fetchCompletedAppointments(),
  ]);
  loading.value = false;
};

// Navigation
const previousDay = () =>
  (currentDate.value = new Date(
    currentDate.value.setDate(currentDate.value.getDate() - 1)
  ));
const nextDay = () =>
  (currentDate.value = new Date(
    currentDate.value.setDate(currentDate.value.getDate() + 1)
  ));
const goToToday = () => (currentDate.value = new Date());

watch(currentDate, fetchAllData);
onMounted(fetchAllData);

// Xác nhận bắt đầu khám
const confirmStartExam = async (appt) => {
  if (!confirm(`Bắt đầu khám bệnh cho ${appt.thu_cung?.ten_thu_cung}?`)) {
    return;
  }

  try {
    // Gọi API bắt đầu khám
    const res = await api.post(`/lich-hen/${appt.id}/bat-dau-kham`);

    if (res.data.status) {
      alert("Đã bắt đầu khám bệnh!");

      // Refresh danh sách
      await fetchAllData();

      // Chuyển sang tab "Đang khám"
      activeTab.value = "examining";

      // Chuyển sang trang phiếu khám
      router.push(`/doctor/lich-kham/phieu-kham/${appt.id}`);
    }
  } catch (err) {
    console.error("Error starting exam:", err);
    alert(err.response?.data?.message || "Lỗi khi bắt đầu khám bệnh");
  }
};

// Bắt đầu khám (nếu đã bắt đầu rồi)
const startExam = (id) => {
  router.push(`/doctor/lich-kham/phieu-kham/${id}`);
};

// Xem chi tiết kết quả khám
const viewExamDetail = (id) => {
  router.push(`/doctor/lich-kham/phieu-kham/${id}`);
};
</script>
<style scoped></style>
