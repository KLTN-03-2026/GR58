<template>
  <div class="w-full min-h-screen px-8 py-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-2">
      <h1 class="font-semibold text-2xl text-black">Quản lý Lịch Hẹn</h1>
      <p class="font-medium text-base text-gray-500">
        Quản lý Lịch Hẹn của khách hàng và bác sĩ
      </p>
    </div>

    <!-- Main Card -->
    <div
      class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 mt-6"
    >
      <!-- Card Header -->
      <div class="flex items-center justify-between mb-6">
        <div class="font-medium text-base leading-4 text-black">
          Danh sách lịch hẹn
        </div>

        <!-- View Toggle and Actions -->
        <div class="flex items-center gap-3">
          <!-- View Toggle -->
          <!-- <div class="bg-[#ececf0] rounded-[14px] p-1 flex items-center">
            <button
              @click="viewMode = 'calendar'"
              :class="[
                'flex items-center gap-2 px-2 py-[5px] rounded-[14px] transition-colors',
                viewMode === 'calendar' ? '' : 'bg-white'
              ]"
            >
              <img :src="iconCalendar" alt="Calendar" class="w-4 h-4" />
              <span class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight">
                Lịch biểu
              </span>
            </button>
            <button
              @click="viewMode = 'list'"
              :class="[
                'flex items-center gap-2 px-2 py-[5px] rounded-[14px] transition-colors',
                viewMode === 'list' ? 'bg-white' : '',
              ]"
            >
              <img :src="iconList" alt="List" class="w-4 h-4" />
              <span
                class="font-medium text-sm leading-5 text-black tracking-tight"
              >
                Danh sách
              </span>
            </button>
          </div> -->

          <!-- Appointment Count Badge -->
          <div
            class="bg-blue-100 border !border-transparent rounded-lg px-2 py-2 flex items-center gap-2"
          >
            <span class="font-medium text-lg leading-4 text-[#1447e6]">
              {{ filteredAppointments.length }} lịch hẹn
            </span>
          </div>

          <!-- Create Appointment Button
          <button
            @click="isCreateAppointmentModalOpen = true"
            class="bg-[#00a63e] rounded-lg h-9 px-3 flex items-center gap-2 hover:bg-[#008c35] transition-colors"
          >
            <img :src="iconPlus" alt="Add" class="w-4 h-4" />
            <span class="font-nunito font-medium text-sm leading-5 text-white tracking-tight">
              Tạo lịch hẹn
            </span>
          </button> -->
        </div>
      </div>

      <!-- Filters -->
      <div class="flex items-center gap-3 mb-6">
        <!-- Search -->
        <div class="relative w-[204px]">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Tìm kiếm..."
            class="bg-[#f3f3f5] border !border-transparent rounded-lg h-9 pl-10 pr-3 w-full font-nunito text-sm text-neutral-950 placeholder:text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689]"
          />
          <SearchIcon
            class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4"
          />
        </div>

        <!-- Date Filter -->
        <button
          class="bg-[#f3f3f5] rounded-lg h-9 px-3 flex items-center justify-between gap-2 w-[204px] hover:bg-gray-200 transition-colors"
        >
          <span
            class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight"
          >
            Tất cả ngày
          </span>
          <ChevronDownIcon />
        </button>

        <!-- Doctor Filter -->
        <button
          class="bg-white border !border-gray-300 rounded-lg h-9 px-3 flex items-center justify-between gap-2 w-[204px] hover:bg-gray-50 transition-colors"
        >
          <span
            class="font-nunito font-medium text-sm leading-5 text-[#6a7282] tracking-tight"
          >
            Tất cả Bác sĩ
          </span>
          <ChevronDownIcon />
        </button>

        <!-- Status Filter -->
        <button
          class="bg-[#f3f3f5] rounded-lg h-9 px-3 flex items-center justify-between gap-2 w-[204px] hover:bg-gray-200 transition-colors"
        >
          <span
            class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight"
          >
            Tất cả trạng thái
          </span>
          <ChevronDownIcon />
        </button>
      </div>

      <!-- Appointments Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200/60">
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Mã lịch
              </th>
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Thời gian
              </th>
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Khách hàng
              </th>
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Dịch vụ
              </th>
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Phụ trách
              </th>
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Trạng thái
              </th>
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Trạng Thái Thanh Toán
              </th>
              <th
                class="text-right px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Thao tác
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(appointment, index) in filteredAppointments"
              :key="index"
              class="border-b border-gray-200/60 hover:bg-gray-50 transition-colors"
            >
              <!-- Appointment ID -->
              <td class="px-2 py-[14px]">
                <p
                  class="font-nunito font-medium text-base leading-6 text-[#009689] tracking-tight"
                >
                  {{ appointment.id }}
                </p>
              </td>

              <!-- Time & Date -->
              <td class="px-2 py-[8px]">
                <div class="flex flex-col">
                  <span
                    class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                  >
                    {{ appointment.time }}
                  </span>
                  <span class="font-nunito text-xs leading-4 text-[#6a7282]">
                    {{ appointment.date }}
                  </span>
                </div>
              </td>

              <!-- Customer -->
              <td class="px-2 py-[8px]">
                <div class="flex flex-col">
                  <span
                    class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                  >
                    {{ appointment.customer }}
                  </span>
                  <span class="font-nunito text-xs leading-4 text-[#6a7282]">
                    Pet: {{ appointment.pet }}
                  </span>
                </div>
              </td>

              <!-- Service -->
              <td class="px-2 py-[17px]">
                <p
                  class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                >
                  {{ appointment.service }}
                </p>
              </td>

              <!-- Assigned Staff -->
              <td class="px-2 py-[8px]">
                <div
                  v-if="appointment.assignedStaff"
                  class="flex items-center gap-3"
                >
                  <div
                    class="w-8 h-8 rounded-full bg-[#cbfbf1] flex items-center justify-center"
                  >
                    <span class="font-nunito text-xs leading-4 text-[#00786f]">
                      {{ appointment.assignedStaff.initial }}
                    </span>
                  </div>
                  <div class="flex flex-col">
                    <span
                      class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                    >
                      {{ appointment.assignedStaff.name }}
                    </span>
                    <span class="font-nunito text-xs leading-4 text-[#6a7282]">
                      {{ appointment.assignedStaff.department }}
                    </span>
                  </div>
                </div>
                <button
                  v-else
                  class="bg-orange-50 border !border-[#ffd6a7] rounded-lg h-8 px-3 flex items-center gap-2 hover:bg-orange-100 transition-colors"
                  @click="handleAssignDoctor(appointment.id)"
                >
                  <InforIcon class="w-4 h-4 text-[#f54900]" />
                  <span
                    class="font-nunito font-medium text-sm leading-5 text-[#f54900] tracking-tight"
                  >
                    Chưa gán
                  </span>
                </button>
              </td>

              <!-- Status -->
              <td class="px-2 py-[15px]">
                <span
                  :class="[
                    'inline-flex items-center px-2 py-[3px] rounded-lg font-nunito font-medium text-xs leading-4',
                    appointment.status === 'confirmed'
                      ? 'bg-green-100 text-[#008236]'
                      : appointment.status === 'in-progress'
                      ? 'bg-purple-100 text-[#8200db]'
                      : appointment.status === 'pending'
                      ? 'bg-blue-100 text-[#1447e6]'
                      : appointment.status === 'completed'
                      ? 'bg-gray-100 text-[#364153]'
                      : 'bg-[#ffe2e2] text-[#c10007]',
                  ]"
                >
                  {{ getStatusLabel(appointment.status) }}
                </span>
              </td>

              <!-- Payment Status -->
              <td class="px-2 py-[15px]">
                <span
                  :class="[
                    'inline-flex items-center px-2 py-[3px] rounded-lg font-nunito font-medium text-xs leading-4',
                    appointment.paymentStatus === 'paid'
                      ? 'bg-green-100 text-[#008236]'
                      : appointment.paymentStatus === 'refunded'
                      ? 'bg-purple-100 text-[#7e22ce]'
                      : 'bg-gray-100 text-[#4a5565]',
                  ]"
                >
                  {{ getPaymentStatusLabel(appointment.paymentStatus) }}
                </span>
              </td>

              <!-- Actions -->
              <td class="px-2 py-[10px]">
                <div class="flex items-center justify-end gap-2">
                  <!-- <button
                    v-if="appointment.status !== 'completed' && appointment.status !== 'cancelled'"
                    class="bg-white border border-gray-200/60 rounded-lg w-[38px] h-8 flex items-center justify-center hover:bg-gray-50 transition-colors"
                    title="Xác nhận"
                  >
                    <img :src="iconCheck" alt="Confirm" class="w-4 h-4" />
                  </button> -->
                  <button
                    class="bg-white border border-gray-200/60 rounded-lg w-[38px] h-8 flex items-center justify-center hover:bg-gray-50 transition-colors"
                    title="Xem chi tiết"
                    @click="handleViewAppointment(appointment.id)"
                  >
                    <EyeIcon class="w-4 h-4" />
                  </button>
                  <button
                    v-if="
                      appointment.status !== 'completed' &&
                      appointment.status !== 'cancelled'
                    "
                    class="bg-white border border-gray-200/60 rounded-lg w-[38px] h-8 flex items-center justify-center hover:bg-gray-50 transition-colors"
                    title="Hủy lịch"
                    @click="handleCancelAppointment(appointment.id)"
                  >
                    <CancelIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modals -->
    <TaoLichHen
      v-if="isCreateAppointmentModalOpen"
      @close="isCreateAppointmentModalOpen = false"
      @submit="handleCreateAppointment"
    />

    <ChiTietLichHen
      v-if="isViewAppointmentModalOpen"
      :appointment-id="selectedAppointmentId"
      @close="isViewAppointmentModalOpen = false"
    />

    <PhanCongBacSi
      v-if="isAssignDoctorModalOpen"
      :appointment-id="selectedAppointmentForAssign"
      :appointment="selectedAppointmentData"
      @close="isAssignDoctorModalOpen = false"
      @assign="handleAssignDoctorSubmit"
    />

    <XoaLichHen
      v-if="isCancelAppointmentModalOpen"
      :appointment-id="selectedAppointmentForCancel"
      @close="isCancelAppointmentModalOpen = false"
      @confirm="handleCancelAppointmentConfirm"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import client from "../../../../utils/api.js";
import TaoLichHen from "./TaoLichHen/index.vue";
import ChiTietLichHen from "./ChiTietLichHen/index.vue";
import PhanCongBacSi from "./PhanCongBacSi/index.vue";
import XoaLichHen from "./XoaLichHen/index.vue";
//Icon SVG
import SearchIcon from "@/assets/svg/search.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import InforIcon from "@/assets/svg/infor.svg";
import EyeIcon from "@/assets/svg/eye.svg";
import CancelIcon from "@/assets/svg/cancel.svg";
import WarningIcon from "@/assets/svg/warning.svg";

// Emits
const emit = defineEmits(["create-appointment"]);

// Reactive state
const viewMode = ref("list");
const searchQuery = ref("");
const isCreateAppointmentModalOpen = ref(false);
const isViewAppointmentModalOpen = ref(false);
const selectedAppointmentId = ref("");
const isAssignDoctorModalOpen = ref(false);
const selectedAppointmentForAssign = ref("");
const selectedAppointmentData = ref(null);
const isCancelAppointmentModalOpen = ref(false);
const selectedAppointmentForCancel = ref("");
const isLoading = ref(false);
const error = ref(null);

// Data from API
const appointments = ref([]);

// Cache for doctors to avoid repeated API calls
const doctorsCache = ref({});

// Helper function to get doctor info by ID from cache or API
const getDoctorInfo = async (doctorId) => {
  // Return from cache if available
  if (doctorsCache.value[doctorId]) {
    return doctorsCache.value[doctorId];
  }

  // If not in cache, try to fetch from API
  try {
    const response = await client.get(`/nhan-vien/${doctorId}`);
    if (response.data.status && response.data.data) {
      const doctorData = response.data.data;
      doctorsCache.value[doctorId] = doctorData;
      return doctorData;
    }
  } catch (err) {
    console.error(`Error fetching doctor ${doctorId}:`, err);
  }

  return null;
};

// Format date and time from ngay_gio (e.g., "2025-11-20 09:00:00")
const formatDateTime = (ngayGio) => {
  if (!ngayGio) return { date: "", time: "" };
  try {
    const date = new Date(ngayGio);
    const time = date.toLocaleTimeString("vi-VN", {
      hour: "2-digit",
      minute: "2-digit",
    });
    const dateStr = date.toLocaleDateString("vi-VN", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });
    return { date: dateStr, time };
  } catch (e) {
    return { date: "", time: "" };
  }
};

// Transform API data to UI format
const transformAppointment = (data) => {
  const { date, time } = formatDateTime(data.ngay_gio);

  // Debug: Log the raw appointment data
  console.log("📌 Raw appointment data:", data);
  console.log("📌 nhan_vien:", data.nhan_vien);
  console.log("📌 nhan_vien_id:", data.nhan_vien_id);
  console.log("📌 All keys in data:", Object.keys(data));

  let assignedStaff = null;
  // Check if doctor is assigned (either has nhan_vien object or nhan_vien_id)
  if (data.nhan_vien || data.nhan_vien_id) {
    console.log("🔍 Has nhan_vien_id or nhan_vien object");
    if (data.nhan_vien) {
      console.log("🔍 nhan_vien object keys:", Object.keys(data.nhan_vien));
    }

    // If nhan_vien_id exists but nhan_vien object is empty, log warning
    if (data.nhan_vien_id && !data.nhan_vien) {
      console.warn(
        "⚠️ Backend returned nhan_vien_id but not nhan_vien object. Relationships not loaded!"
      );
    }

    // Get doctor name with multiple fallbacks
    const doctorName =
      data.nhan_vien?.ho_ten ||
      data.nhan_vien?.name ||
      data.nhan_vien?.ten ||
      data.nhan_vien?.full_name ||
      "Bác Sĩ";

    // If still no name found, scan all fields dynamically
    let finalName = doctorName;
    if (finalName === "Bác Sĩ" && data.nhan_vien) {
      for (let key in data.nhan_vien) {
        const value = data.nhan_vien[key];
        if (
          typeof value === "string" &&
          value.length > 2 &&
          value.length < 100 &&
          (key.includes("name") ||
            key.includes("ten") ||
            key.includes("ho") ||
            key.includes("Name"))
        ) {
          finalName = value;
          console.log(`🔍 Found dynamic name field '${key}': ${value}`);
          break;
        }
      }
    }

    assignedStaff = {
      initial:
        finalName && finalName !== "Chưa xác định" ? finalName.charAt(0) : "B",
      name: finalName,
      department: data.nhan_vien?.chuc_vu || "Bác Sĩ",
    };
    console.log("✅ Assigned Staff created:", assignedStaff);
  } else {
    console.log(
      "❌ No doctor assigned - nhan_vien and nhan_vien_id are both empty"
    );
  }

  // Get customer name - Backend transforms khach_hang to string (full_name)
  // The backend transformData() converts khach_hang object to full_name string
  let customerName = "Chưa xác định";
  if (typeof data.khach_hang === "string" && data.khach_hang) {
    // Backend returns khach_hang as string (full_name)
    customerName = data.khach_hang;
  } else if (data.khach_hang?.ho_ten) {
    // Fallback: if still object with ho_ten
    customerName = data.khach_hang.ho_ten;
  } else if (data.khach_hang?.full_name) {
    // Fallback: if still object with full_name
    customerName = data.khach_hang.full_name;
  } else if (data.khach_hang?.name) {
    // Fallback: if still object with name
    customerName = data.khach_hang.name;
  }

  return {
    id: data.id,
    time,
    date,
    customer: customerName,
    pet: data.thu_cung?.ten_thu_cung || data.thu_cung?.name || "Chưa xác định",
    service: data.dich_vu?.ten || data.dich_vu?.name || "Dịch vụ không rõ",
    assignedStaff,
    status: data.trang_thai || "pending",
    paymentStatus: data.thanh_toan?.trang_thai || "unpaid",
    originalData: data, // Keep original data for reference
  };
};

// Fetch appointments from API
const fetchAppointments = async () => {
  isLoading.value = true;
  error.value = null;
  try {
    // Add query param to fetch all appointments (for admin)
    const response = await client.get("/lich-hen?all=1");

    if (response.data.status && Array.isArray(response.data.data)) {
      console.log("📋 Appointments from API:", response.data.data);
      appointments.value = response.data.data.map(transformAppointment);

      // Fetch missing doctor info for appointments without nhan_vien object
      const appointmentsNeedingDoctorInfo = response.data.data.filter(
        (apt) => apt.nhan_vien_id && !apt.nhan_vien
      );

      if (appointmentsNeedingDoctorInfo.length > 0) {
        console.log(
          `⚠️ Fetching ${appointmentsNeedingDoctorInfo.length} missing doctor infos...`
        );
        for (const apt of appointmentsNeedingDoctorInfo) {
          const doctorInfo = await getDoctorInfo(apt.nhan_vien_id);
          if (doctorInfo) {
            // Create nhan_vien object from fetched data
            apt.nhan_vien = doctorInfo;
            // Re-transform this appointment to get updated doctor name
            const transformedApt = transformAppointment(apt);
            // Find and update in appointments array
            const index = appointments.value.findIndex((a) => a.id === apt.id);
            if (index !== -1) {
              appointments.value[index] = transformedApt;
            }
          }
        }
      }

      console.log("✅ Transformed appointments:", appointments.value);
    } else if (response.data.data && response.data.data.data) {
      // Handle paginated response
      console.log("📋 Paginated appointments:", response.data.data.data);
      appointments.value = response.data.data.data.map(transformAppointment);
      console.log("✅ Transformed appointments:", appointments.value);
    }
  } catch (err) {
    error.value = err.message;
    console.error("Error fetching appointments:", err);
  } finally {
    isLoading.value = false;
  }
};

// Filtered appointments based on search
const filteredAppointments = computed(() => {
  if (!searchQuery.value) return appointments.value;

  const query = searchQuery.value.toLowerCase();
  return appointments.value.filter(
    (apt) =>
      apt.id.toLowerCase().includes(query) ||
      apt.customer.toLowerCase().includes(query) ||
      apt.pet.toLowerCase().includes(query) ||
      apt.service.toLowerCase().includes(query)
  );
});

// Load appointments on mount
onMounted(() => {
  fetchAppointments();
});

// Methods
const getStatusLabel = (status) => {
  const labels = {
    confirmed: "Đã xác nhận",
    "in-progress": "Đang khám",
    pending: "Chờ xác nhận",
    completed: "Hoàn thành",
    cancelled: "Đã hủy",
  };
  return labels[status] || status;
};

const getPaymentStatusLabel = (status) => {
  const labels = {
    paid: "Đã thanh toán",
    unpaid: "Chưa thanh toán",
    refunded: "Đã hoàn tiền",
  };
  return labels[status] || status;
};

const handleCreateAppointment = (data) => {
  console.log("New appointment data:", data);
  // Logic to create appointment goes here
  isCreateAppointmentModalOpen.value = false;
  // Refresh the list
  fetchAppointments();
};

const handleViewAppointment = (id) => {
  selectedAppointmentId.value = id;
  isViewAppointmentModalOpen.value = true;
};

const handleAssignDoctor = (id) => {
  // Tìm appointment data từ danh sách
  const appointmentData = appointments.value.find((apt) => apt.id === id);
  selectedAppointmentData.value = appointmentData || null;
  selectedAppointmentForAssign.value = id;
  isAssignDoctorModalOpen.value = true;
};

const handleAssignDoctorSubmit = (data) => {
  console.log("Assigned doctor:", data);

  // Find the appointment in the list and update it with assigned doctor info
  const appointmentIndex = appointments.value.findIndex(
    (apt) => apt.id === data.appointmentId
  );

  if (appointmentIndex !== -1) {
    // Update the appointment with doctor info and status
    appointments.value[appointmentIndex].assignedStaff = {
      initial: data.staffName ? data.staffName.charAt(0) : "?",
      name: data.staffName,
      department: "Bác sĩ",
    };

    // Update status to confirmed/da_xac_nhan
    if (data.status) {
      appointments.value[appointmentIndex].status = data.status;
    }
  }

  isAssignDoctorModalOpen.value = false;

  // Refresh the list to get latest data from server
  setTimeout(() => {
    fetchAppointments();
  }, 500);
};

const handleCancelAppointment = (id) => {
  selectedAppointmentForCancel.value = id;
  isCancelAppointmentModalOpen.value = true;
};

const handleCancelAppointmentConfirm = async (id) => {
  try {
    await client.delete(`/lich-hen/${id}`);
    console.log("Cancelled appointment:", id);
    isCancelAppointmentModalOpen.value = false;
    // Refresh the list
    fetchAppointments();
  } catch (err) {
    console.error("Error cancelling appointment:", err);
  }
};
</script>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
