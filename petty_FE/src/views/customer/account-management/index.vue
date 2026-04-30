<template>
  <div>
    <div class="min-h-screen max-w-5xl mx-auto md:px-6 sm:px-4">
      <div
        class="bg-teal-50 border text-teal-300 rounded-xl p-8 flex gap-3 mb-6 sm:p-4"
      >
        <HeartIcon class="w-7 h-7 text-teal-500 flex-shrink-0 mt-1" />
        <div>
          <p class="font-semibold text-base text-teal-900 mb-2">
            Chào mừng bạn quay trở lại! Bạn có
          </p>
          <div class="text-3xl font-bold text-teal-900 mb-2 sm:text-2xl">
            {{ upcomingAppointments.length }} lịch hẹn sắp tới
          </div>
          <p class="inline font-semibold text-base text-teal-900">và</p>
          <div
            class="text-3xl font-bold text-orange-500 inline mx-1 sm:text-2xl"
          >
            {{ pendingPayments }} thanh toán chờ xử lý
          </div>
        </div>
      </div>

      <div class="flex gap-16 mb-11">
        <button
          @click="openBookingPopup"
          class="flex-1 flex items-center justify-center gap-4 py-6 rounded-lg bg-teal-700 text-white font-bold text-xl hover:bg-teal-700/80 transition"
        >
          <CalendarIcon class="w-6 h-6" />
          <span>Đặt lịch khám</span>
        </button>
        <button
          @click="openAddPetPopup"
          class="flex-1 flex items-center justify-center gap-4 py-6 rounded-lg bg-white border border-teal-200 text-teal-700 font-bold text-xl hover:!bg-teal-700 hover:!text-white hover:border-teal-700 transition duration-300"
        >
          <AddIcon class="w-4 h-4" />
          <span>Thêm thú cưng</span>
        </button>
      </div>

      <div class="grid grid-cols-3 gap-11 mb-6">
        <div
          class="bg-white border text-sky-400 rounded-2xl p-6 flex flex-col justify-between"
        >
          <div class="flex justify-between items-center mb-10">
            <span class="text-gray-600 text-lg font-normal"
              >Lịch hẹn sắp tới</span
            >
            <CalendarIcon class="w-7 h-7" />
          </div>
          <div>
            <div class="text-5xl font-normal text-gray-900 font-['Nunito']">
              {{ upcomingAppointments.length }}
            </div>
            <div class="text-sky-400 text-lg font-bold italic mt-1">
              Trong tháng này
            </div>
          </div>
        </div>

        <div
          class="bg-white border text-orange-400 rounded-2xl p-6 flex flex-col justify-between"
        >
          <div class="flex justify-between items-center mb-10">
            <span class="text-gray-600 text-lg font-normal"
              >Nhắc tiêm phòng</span
            >
            <VaccineIcon class="w-7 h-7" />
          </div>
          <div>
            <div class="text-5xl font-normal text-gray-900 font-['Nunito']">
              {{ vaccinationReminders.length }}
            </div>
            <div class="text-orange-400 text-lg font-bold italic mt-1">
              Cần tiêm trong tháng này
            </div>
          </div>
        </div>

        <div
          class="bg-white border text-purple-400 rounded-2xl p-6 flex flex-col justify-between"
        >
          <div class="flex justify-between items-center mb-10">
            <span class="text-gray-600 text-lg font-normal"
              >Thanh toán chờ xử lý</span
            >
            <DollarIcon class="w-5 h-5" />
          </div>
          <div>
            <div
              class="text-3xl font-bold text-gray-900 font-['Nunito'] leading-tight"
            >
              {{ formatPrice(totalPendingAmount) }}đ
            </div>
            <div class="text-purple-400 text-lg font-bold italic mt-1">
              {{ pendingPayments }} hóa đơn chưa thanh toán
            </div>
          </div>
        </div>
      </div>

      <!-- Lịch hẹn sắp tới -->
      <div
        class="bg-white border !border-gray-200 shadow-sm rounded-2xl p-6 mb-6"
      >
        <div
          class="flex flex-row justify-between items-start mb-8 sm:flex-col sm:gap-4"
        >
          <div>
            <h3 class="text-xl font-medium text-black mb-1">
              Lịch hẹn sắp tới
            </h3>
            <p class="text-sm text-gray-500 font-medium">
              Các buổi khám được xếp lịch
            </p>
          </div>
          <button
            @click="$router.push('/customer/appointments')"
            class="flex items-center gap-1 text-teal-500 font-semibold text-base hover:text-teal-600 transition"
          >
            <span>Xem tất cả</span>
            <ArrowRightIcon class="w-4 h-4" />
          </button>
        </div>

        <div class="space-y-4">
          <div
            v-for="appt in upcomingAppointments.slice(0, 3)"
            :key="appt.id"
            class="border !border-gray-300 rounded-lg p-4"
            style="
              display: flex !important;
              align-items: center !important;
              gap: 1rem !important;
              flex-direction: row !important;
            "
          >
            <!-- Date Badge -->
            <div
              class="bg-teal-100 rounded-lg w-14 h-14 flex flex-col items-center justify-center"
              style="
                flex-shrink: 0 !important;
                width: 3.5rem !important;
                height: 3.5rem !important;
              "
            >
              <div class="text-teal-800 font-bold text-base">
                {{ appt.date.split("/")[0] }}
              </div>
              <div class="text-teal-900 text-xs font-normal font-['Inter']">
                Th {{ appt.date.split("/")[1] }}
              </div>
            </div>

            <!-- Service Info - Takes up available space -->
            <div
              class="flex-1 min-w-0"
              style="flex: 1 !important; min-width: 0 !important"
            >
              <h4 class="font-semibold text-base text-black mb-1">
                {{ appt.service }}
              </h4>
              <p class="text-gray-500 text-sm font-semibold mb-2">
                Thú cưng:
                <span :class="getPetColorClass(appt.pet)" class="font-bold">
                  {{ appt.pet }}
                </span>
              </p>
              <div
                class="flex gap-4 items-center text-sm font-semibold text-gray-500"
              >
                <div class="flex items-center gap-1">
                  <ClockIcon class="w-4 h-4 text-gray-500" />
                  <span>{{ appt.time }}</span>
                </div>
              </div>
            </div>

            <!-- Status Badge and Button - Aligned to right -->
            <div
              class="flex items-center gap-3"
              style="
                display: flex !important;
                align-items: center !important;
                gap: 0.75rem !important;
                flex-shrink: 0 !important;
              "
            >
              <span
                :class="getStatusColorClass(appt.status)"
                class="font-bold text-sm px-3 py-1.5 rounded-lg whitespace-nowrap"
              >
                {{ statusLabel(appt.status) }}
              </span>
              <button
                @click="openAppointmentDetail(appt)"
                class="border !border-gray-300 text-black font-bold text-sm px-4 py-1.5 rounded-lg hover:bg-gray-50 transition whitespace-nowrap"
              >
                Chi tiết
              </button>
            </div>
          </div>

          <!-- Nếu không có lịch hẹn -->
          <div
            v-if="upcomingAppointments.length === 0"
            class="text-center py-8 text-gray-500"
          >
            Chưa có lịch hẹn sắp tới
          </div>
        </div>
      </div>

      <!-- Nhắc tiêm phòng -->
      <div
        class="bg-yellow-50/30 border !border-yellow-300 shadow-sm rounded-2xl p-8"
      >
        <div class="flex gap-2 items-start mb-6 text-orange-700">
          <VaccineIcon class="w-7 h-7" />
          <div>
            <h3 class="text-xl font-bold text-orange-900">
              Nhắc nhở tiêm phòng
            </h3>
            <p class="text-base text-orange-700 mt-2">
              Đừng quên lịch tiêm phòng cho bé cưng
            </p>
          </div>
        </div>

        <div class="space-y-3">
          <div
            v-for="pet in vaccinationReminders"
            :key="pet.id"
            class="bg-white border !border-yellow-300 rounded-lg p-3.5 flex justify-between items-center"
          >
            <div>
              <h4 class="font-bold text-base text-black mb-1">
                {{ pet.ten_thu_cung }}
              </h4>
              <p class="text-gray-500 text-base font-normal font-['Inter']">
                {{ pet.loai_thu_cung }} —
                {{ pet.giong_thu_cung || "Chưa rõ giống" }}
              </p>
            </div>
            <div class="text-right">
              <button
                @click="
                  openVaccinationBooking(
                    pet.ten_thu_cung,
                    'Kiểm tra & tiêm phòng định kỳ',
                    ''
                  )
                "
                class="text-teal-500 font-semibold text-sm hover:text-teal-600 transition"
              >
                Đặt lịch ngay
              </button>
            </div>
          </div>
          <div
            v-if="vaccinationReminders.length === 0"
            class="text-center py-6 text-gray-400 text-sm"
          >
            Không có lịch nhắc tiêm phòng
          </div>
        </div>
      </div>
    </div>

    <!-- Các Popup -->
    <ThemThuCung
      ref="themThuCungRef"
      :isOpen="isAddPetOpen"
      @close="closeAddPetPopup"
      @submit="handleAddPetSubmit"
    />

    <!-- Local success popup (replaces missing ThemThuCungThanhCong component) -->
    <div
      v-if="isSuccessPopupOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
      @click.self="closeSuccessPopup"
    >
      <div
        class="bg-white border border-black/15 rounded-[10px] w-full max-w-[512px] m-4 p-6"
      >
        <div class="flex flex-col gap-4 items-center justify-center">
          <div
            class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center"
          >
            <img
              src="https://www.figma.com/api/mcp/asset/49ad787d-519e-4d49-84a2-20bd7851ef0c"
              alt="Success"
              class="w-10 h-10"
            />
          </div>
          <div class="flex flex-col gap-2 text-center">
            <h2 class="text-2xl font-semibold text-neutral-950">
              Thêm thú cưng thành công
            </h2>
            <p class="text-sm text-gray-600">
              Bạn có muốn đặt lịch khám cho bé
              {{ newPetData.name || newPetData.ten || newPetData.ten_thu_cung }}
              ngay không?
            </p>
          </div>

          <div class="bg-teal-50 rounded-[10px] w-full py-4 px-4">
            <div class="flex flex-col gap-1 items-center">
              <div class="flex gap-2 items-center justify-center py-0.5">
                <p class="text-sm font-semibold text-gray-500">Loài:</p>
                <p class="text-sm font-medium text-black">
                  {{ newPetData.species || "-" }}
                </p>
              </div>
              <div class="flex gap-2 items-center justify-center py-0.5">
                <p class="text-sm font-semibold text-gray-500">Tên thú cưng:</p>
                <p class="text-sm font-medium text-black">
                  {{
                    newPetData.name ||
                    newPetData.ten ||
                    newPetData.ten_thu_cung ||
                    "-"
                  }}
                </p>
              </div>
            </div>
          </div>

          <div class="flex gap-6 items-center">
            <button
              type="button"
              @click="handleBookAppointment(newPetData)"
              class="bg-[#5a9690] px-8 py-2 rounded-lg text-sm font-semibold text-white"
            >
              Đặt lịch khám
            </button>
            <button
              type="button"
              @click="closeSuccessPopup"
              class="bg-white border border-black/15 px-8 py-2 rounded-lg text-sm font-semibold text-black"
            >
              Đóng
            </button>
          </div>
        </div>
      </div>
    </div>

    <DatLichKham
      :isOpen="isBookingPopupOpen"
      :initialData="rebookData"
      @close="closeBookingPopup"
      @confirm="handleBookingConfirm"
      @openAddPet="openAddPetPopup"
    />

    <ChiTietLichHen
      :isOpen="isAppointmentDetailOpen"
      :selectedAppt="selectedAppointment"
      @close="closeAppointmentDetail"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import client from "@/utils/api";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
//Icon SVG
import HeartIcon from "@/assets/svg/Heart.svg";
import CalendarIcon from "@/assets/svg/calendar.svg";
import AddIcon from "@/assets/svg/add.svg";
import DollarIcon from "@/assets/svg/dollar.svg";
import VaccineIcon from "@/assets/svg/vaccine.svg";
import ArrowRightIcon from "@/assets/svg/arrow-right.svg";
import ClockIcon from "@/assets/svg/clock.svg";

import ThemThuCung from "../my-pets/add-pet/index.vue";
import DatLichKham from "../appointment/book-appointment/index.vue";
import ChiTietLichHen from "../appointment/appointment-detail/index.vue";

const upcomingAppointments = ref([]);
const pastAppointments = ref([]);
const loading = ref(true);

// Use the same mapAppointment and fetchAppointments logic as the main LichHen page
const mapAppointment = (item) => {
  const ngayGio = item.ngay_gio ? new Date(item.ngay_gio) : null;
  const day = ngayGio ? String(ngayGio.getDate()).padStart(2, "0") : "";
  const month = ngayGio ? String(ngayGio.getMonth() + 1).padStart(2, "0") : "";
  const year = ngayGio ? ngayGio.getFullYear() : "";
  const hours = ngayGio ? String(ngayGio.getHours()).padStart(2, "0") : "";
  const minutes = ngayGio ? String(ngayGio.getMinutes()).padStart(2, "0") : "";

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
    clinic:
      item.dia_diem || item.clinic || "Phòng khám Petty - Chi nhánh Quận 1",
    address: item.dia_chi || "123 Nguyễn Huệ, Q.1, TP.HCM",
    note: item.ghi_chu || "",
    status: item.trang_thai || (item.thanhToan ? "paid" : "pending"),
  };
};

const fetchAppointments = async (filters = {}) => {
  loading.value = true;
  try {
    const res = await client.get(`/lich-hen`, { params: filters });
    let list = [];
    if (res.data && res.data.data) {
      if (Array.isArray(res.data.data)) list = res.data.data;
      else if (Array.isArray(res.data.data.data)) list = res.data.data.data;
      else if (Array.isArray(res.data)) list = res.data;
    }

    const today = new Date();
    const todayStart = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    const upcoming = [];
    const past = [];
    list.forEach((it) => {
      const mapped = mapAppointment(it);
      const dt = it.ngay_gio ? new Date(it.ngay_gio) : null;
      const status = String(it.trang_thai || "").toLowerCase();
      const isCancelled = status === "cancelled" || status === "canceled";
      if (!isCancelled && dt && dt >= todayStart) {
        upcoming.push(mapped);
      } else if (!isCancelled) {
        past.push(mapped);
      }
    });

    upcomingAppointments.value = upcoming.sort(
      (a, b) => new Date(a.raw.ngay_gio) - new Date(b.raw.ngay_gio)
    );
    pastAppointments.value = past;
  } catch (err) {
    console.error("Lỗi tải lịch hẹn:", err);
    showErrorToast("Lỗi", "Không thể tải lịch hẹn. Vui lòng thử lại.");
  } finally {
    loading.value = false;
  }
};

// Tính toán thanh toán chờ xử lý
const isPendingPayment = (a) =>
  !a.raw.da_thanh_toan && a.raw.trang_thai !== "cancelled";

const pendingPayments = computed(
  () => upcomingAppointments.value.filter(isPendingPayment).length
);

const totalPendingAmount = computed(() =>
  upcomingAppointments.value
    .filter(isPendingPayment)
    .reduce(
      (sum, a) =>
        sum +
        (parseFloat(a.raw.tong_tien) ||
          parseFloat(a.raw.dich_vu?.gia_tien) ||
          0),
      0
    )
);

const formatPrice = (price) => {
  return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

// Màu tên thú cưng
const getPetColorClass = (name) => {
  const colors = {
    Milo: "text-amber-700",
    Luna: "text-sky-500",
    Cookie: "text-purple-600",
    Bella: "text-pink-600",
    Max: "text-emerald-600",
  };
  return colors[name] || "text-sky-500";
};

const statusLabel = (s) => {
  const st = String(s || "").toLowerCase();
  if (st.includes("confirmed")) return "Đã xác nhận";
  if (st === "pending") return "Chờ xác nhận";
  if (st === "paid") return "Đã thanh toán";
  return "Đã xác nhận";
};

const getStatusColorClass = (s) => {
  const st = String(s || "").toLowerCase();
  if (st === "pending") return "bg-amber-100 text-amber-800";
  if (st.includes("confirmed")) return "bg-teal-100 text-teal-800";
  if (st === "paid") return "bg-green-100 text-green-800";
  return "bg-teal-100 text-teal-800"; // default
};

// Nhắc tiêm phòng — lấy từ danh sách thú cưng của khách hàng
const vaccinationReminders = ref([]);

const fetchPets = async () => {
  try {
    const res = await client.get("/thu-cung");
    let list = [];
    if (Array.isArray(res.data)) list = res.data;
    else if (Array.isArray(res.data?.data)) list = res.data.data;
    vaccinationReminders.value = list;
  } catch {
    vaccinationReminders.value = [];
  }
};

// Popup refs
const themThuCungRef = ref(null);
const isAddPetOpen = ref(false);
const isSuccessPopupOpen = ref(false);
const newPetData = ref({});
const isBookingPopupOpen = ref(false);
const rebookData = ref(null);
const isAppointmentDetailOpen = ref(false);
const selectedAppointment = ref({});

const openAddPetPopup = () => {
  isAddPetOpen.value = true;
};
const closeAddPetPopup = () => {
  isAddPetOpen.value = false;
};
const openBookingPopup = () => {
  rebookData.value = null;
  isBookingPopupOpen.value = true;
};
const closeBookingPopup = () => {
  isBookingPopupOpen.value = false;
};
const openAppointmentDetail = (appt) => {
  selectedAppointment.value = appt;
  isAppointmentDetailOpen.value = true;
};
const closeAppointmentDetail = () => {
  isAppointmentDetailOpen.value = false;
};

const openVaccinationBooking = (petName, serviceName, dueDate) => {
  rebookData.value = { petName, serviceName, dueDate };
  isBookingPopupOpen.value = true;
};

// Handlers for add-pet flow (ThemThuCung emits submit with pet data)
const handleAddPetSubmit = (petData) => {
  // store and show success popup
  newPetData.value = petData || {};
  isAddPetOpen.value = false;
  isSuccessPopupOpen.value = true;
};

const closeSuccessPopup = () => {
  isSuccessPopupOpen.value = false;
  newPetData.value = {};
};

const handleBookAppointment = (pet) => {
  // open booking with pet prefilled
  const petName =
    pet?.name || pet?.ten || pet?.ten_thu_cung || newPetData.value?.name;
  rebookData.value = { petName };
  isBookingPopupOpen.value = true;
  // close the success popup if open
  isSuccessPopupOpen.value = false;
};

const handleBookingConfirm = () => {
  showSuccessToast(
    "Đặt lịch thành công!",
    "Chúng tôi sẽ liên hệ xác nhận sớm nhất"
  );
  fetchAppointments();
};

onMounted(() => {
  fetchAppointments();
  fetchPets();
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Nunito+Sans:ital,wght@0,400;0,500;0,600;0,700;1,700&family=Nunito:wght@400&display=swap");

body {
  font-family: "Nunito Sans", sans-serif;
}
</style>
