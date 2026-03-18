<template>
  <!-- Modal Overlay -->
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <!-- Modal Card -->
    <div
      class="bg-white border !border-gray-300 rounded-[10px] shadow-[0px_10px_15px_-3px_rgba(0,0,0,0.1),0px_4px_6px_-4px_rgba(0,0,0,0.1)] w-[510px] relative"
    >
      <!-- Close Button -->
      <button
        @click="$emit('close')"
        class="absolute right-6 top-4 w-4 h-4 opacity-70 hover:opacity-100 transition-opacity"
      >
        <CloseIcon />
      </button>

      <!-- Dialog Header -->
      <div class="px-6 pt-6 pb-0">
        <h2
          class="font-nunito font-semibold text-lg leading-[18px] text-neutral-950 tracking-tight mb-2"
        >
          Chi tiết lịch hẹn
        </h2>
        <p class="font-nunito text-sm leading-5 text-[#717182] tracking-tight">
          Mã lịch hẹn: {{ appointmentData.id }}
        </p>
      </div>

      <!-- Content -->
      <div class="px-6 pt-[22px] pb-0">
        <!-- Loading State -->
        <div v-if="isLoading" class="flex items-center justify-center py-8">
          <div
            class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#009689]"
          ></div>
        </div>

        <!-- Error State -->
        <div
          v-else-if="error"
          class="bg-red-50 border border-red-200 rounded-[10px] px-4 py-3 mb-4"
        >
          <p class="font-nunito text-sm text-red-700">{{ error }}</p>
        </div>

        <!-- Content -->
        <div v-else class="flex flex-col gap-4">
          <!-- Time -->
          <div class="relative h-11">
            <div class="absolute left-0 top-[2px] w-5 h-5">
              <CalendarIcon />
            </div>
            <div class="absolute left-8 top-0 flex flex-col">
              <p
                class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
              >
                Thời gian
              </p>
              <p
                class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
              >
                {{ appointmentData.time }} - {{ appointmentData.date }}
              </p>
            </div>
          </div>

          <!-- Customer -->
          <div class="relative h-11">
            <div class="absolute left-0 top-[2px] w-5 h-5">
              <UserIcon class="text-black" />
            </div>
            <div class="absolute left-8 top-0 flex flex-col">
              <p
                class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
              >
                Khách hàng
              </p>
              <p
                class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
              >
                {{ appointmentData.customer }}
              </p>
            </div>
          </div>

          <!-- Pet -->
          <div class="relative h-11">
            <div class="absolute left-0 top-[2px] w-5 h-5">
              <DogIcon />
            </div>
            <div class="absolute left-8 top-0 flex flex-col">
              <p
                class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
              >
                Thú cưng
              </p>
              <p
                class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
              >
                {{ appointmentData.pet }}
              </p>
            </div>
          </div>

          <!-- Service -->
          <div class="relative h-11">
            <div class="absolute left-0 top-[2px] w-5 h-5">
              <ServiceIcon />
            </div>
            <div class="absolute left-8 top-0 flex flex-col">
              <p
                class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
              >
                Dịch vụ
              </p>
              <p
                class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
              >
                {{ appointmentData.service }}
              </p>
            </div>
          </div>

          <!-- Assigned Staff -->
          <div class="relative h-[60px]">
            <div class="absolute left-0 top-[2px] w-5 h-5">
              <UserIcon />
            </div>
            <div class="absolute left-8 top-0 flex flex-col">
              <p
                class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
              >
                Phụ trách
              </p>
              <p
                class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
              >
                {{ appointmentData.assignedStaff }}
              </p>
              <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                ({{ appointmentData.department }})
              </p>
            </div>
          </div>

          <!-- Status and Payment Row -->
          <div
            class="border-t border-black/10 pt-[17px] grid grid-cols-2 gap-4"
          >
            <!-- Status -->
            <div class="flex flex-col">
              <p
                class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight mb-[6.5px]"
              >
                Trạng thái
              </p>
              <span
                :class="[
                  'inline-flex items-center px-2 py-[3px] rounded-lg font-nunito font-medium text-xs leading-4 w-fit',
                  getStatusClass(appointmentData.status),
                ]"
              >
                {{ getStatusLabel(appointmentData.status) }}
              </span>
            </div>

            <!-- Payment -->
            <div class="flex flex-col">
              <p
                class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight mb-[6.5px]"
              >
                Thanh toán
              </p>
              <span
                :class="[
                  'inline-flex items-center px-2 py-[3px] rounded-lg font-nunito font-medium text-xs leading-4 w-fit',
                  getPaymentClass(appointmentData.paymentStatus),
                ]"
              >
                {{ getPaymentLabel(appointmentData.paymentStatus) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Dialog Footer -->
      <div class="px-6 pt-4 pb-6 flex items-center justify-end">
        <button
          @click="$emit('close')"
          class="bg-[#030213] rounded-lg h-9 px-4 py-2 font-nunito font-medium text-sm leading-5 text-white tracking-tight hover:bg-[#1a1b2e] transition-colors"
        >
          Đóng
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from "vue";
import client from "../../../../../utils/api.js";
//Icon SVG
import CloseIcon from "@/assets/svg/close.svg";
import CalendarIcon from "@/assets/svg/calendar.svg";
import UserIcon from "@/assets/svg/user.svg";
import DogIcon from "@/assets/svg/dog.svg";
import ServiceIcon from "@/assets/svg/document.svg";

// Props
const props = defineProps({
  appointmentId: {
    type: String,
    default: "LH001237",
  },
});

// Emits
const emit = defineEmits(["close"]);

// State
const isLoading = ref(false);
const error = ref(null);
const appointment = ref(null);

// Helper function to fetch doctor info if not included in appointment response
const getDoctorInfo = async (doctorId) => {
  try {
    const response = await client.get(`/nhan-vien/${doctorId}`);
    if (response.data.status && response.data.data) {
      console.log("✅ Fetched doctor info:", response.data.data);
      return response.data.data;
    }
  } catch (err) {
    console.error(`Error fetching doctor ${doctorId}:`, err);
  }
  return null;
};

// Format date and time
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

// Transform appointment data
const transformAppointment = (data) => {
  const { date, time } = formatDateTime(data.ngay_gio);

  // Get customer name
  let customerName = "Chưa xác định";
  if (typeof data.khach_hang === "string" && data.khach_hang) {
    customerName = data.khach_hang;
  } else if (data.khach_hang?.ho_ten) {
    customerName = data.khach_hang.ho_ten;
  } else if (data.khach_hang?.full_name) {
    customerName = data.khach_hang.full_name;
  }

  // Get doctor info
  let assignedStaff = "Chưa gán";
  let department = "";
  console.log("🔍 Full data object:", JSON.stringify(data, null, 2));
  console.log("🔍 Checking nhan_vien:", data.nhan_vien);
  console.log("🔍 nhan_vien type:", typeof data.nhan_vien);
  console.log("🔍 nhan_vien_id:", data.nhan_vien_id);
  console.log("🔍 All data keys:", Object.keys(data));

  if (data.nhan_vien) {
    console.log(
      "🔍 nhan_vien full object:",
      JSON.stringify(data.nhan_vien, null, 2)
    );
    console.log("🔍 nhan_vien keys:", Object.keys(data.nhan_vien));
    console.log("🔍 Trying each field:");
    console.log("   - ho_ten:", data.nhan_vien.ho_ten);
    console.log("   - name:", data.nhan_vien.name);
    console.log("   - ten:", data.nhan_vien.ten);
    console.log("   - full_name:", data.nhan_vien.full_name);
    console.log("   - chuc_vu:", data.nhan_vien.chuc_vu);

    // Dynamic fallback: scan for any string field that might be a name
    let dynamicName = null;
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
        console.log(`   - ${key}: ${value}`);
        if (!dynamicName) {
          dynamicName = value;
        }
      }
    }

    assignedStaff =
      data.nhan_vien.ho_ten ||
      data.nhan_vien.name ||
      data.nhan_vien.ten ||
      data.nhan_vien.full_name ||
      dynamicName ||
      "Chưa xác định";
    department = data.nhan_vien.chuc_vu || "Bác Sĩ";
    console.log("✅ Doctor found:", assignedStaff, "| Department:", department);
  } else {
    console.log("❌ nhan_vien not in response or is null/undefined");
    console.log("❌ Entire appointment data keys:", Object.keys(data));
  }

  // Get pet name
  let petName = "Chưa xác định";
  if (data.thu_cung?.ten_thu_cung) {
    petName = data.thu_cung.ten_thu_cung;
  } else if (data.thu_cung?.name) {
    petName = data.thu_cung.name;
  }

  // Get service name
  let serviceName = "Dịch vụ không rõ";
  if (data.dich_vu?.ten) {
    serviceName = data.dich_vu.ten;
  } else if (data.dich_vu?.name) {
    serviceName = data.dich_vu.name;
  }

  return {
    id: data.id,
    time,
    date,
    customer: customerName,
    pet: petName,
    service: serviceName,
    assignedStaff,
    department,
    status: data.trang_thai || "pending",
    paymentStatus: data.thanh_toan?.trang_thai || "unpaid",
  };
};

// Fetch appointment details
const fetchAppointment = async () => {
  isLoading.value = true;
  error.value = null;
  try {
    const response = await client.get(`/lich-hen/${props.appointmentId}`);

    if (response.data.status) {
      let appointmentData = response.data.data;
      console.log("📋 Appointment detail:", appointmentData);

      // If backend didn't include nhan_vien but has nhan_vien_id, fetch it separately
      if (appointmentData.nhan_vien_id && !appointmentData.nhan_vien) {
        console.warn("⚠️ nhan_vien not included, fetching separately...");
        const doctorInfo = await getDoctorInfo(appointmentData.nhan_vien_id);
        if (doctorInfo) {
          appointmentData.nhan_vien = doctorInfo;
        }
      }

      appointment.value = transformAppointment(appointmentData);
    } else {
      error.value = "Không thể lấy thông tin lịch hẹn";
    }
  } catch (err) {
    error.value = err.message;
    console.error("Error fetching appointment:", err);
  } finally {
    isLoading.value = false;
  }
};

// Computed property for display data
const appointmentData = computed(() => {
  return (
    appointment.value || {
      id: props.appointmentId,
      time: "15:00",
      date: "19/11/2025",
      customer: "Chưa tải",
      pet: "-",
      service: "-",
      assignedStaff: "Chưa gán",
      department: "-",
      status: "pending",
      paymentStatus: "unpaid",
    }
  );
});

// Status helper methods
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

const getStatusClass = (status) => {
  const classes = {
    confirmed: "bg-green-100 text-[#008236]",
    "in-progress": "bg-purple-100 text-[#8200db]",
    pending: "bg-blue-100 text-[#1447e6]",
    completed: "bg-gray-100 text-[#364153]",
    cancelled: "bg-[#ffe2e2] text-[#c10007]",
  };
  return classes[status] || "bg-gray-100 text-gray-600";
};

const getPaymentLabel = (status) => {
  const labels = {
    paid: "Đã xong",
    unpaid: "Chưa thanh toán",
    refunded: "Đã hoàn tiền",
  };
  return labels[status] || status;
};

const getPaymentClass = (status) => {
  const classes = {
    paid: "bg-green-100 text-[#008236]",
    unpaid: "bg-gray-100 text-[#4a5565]",
    refunded: "bg-purple-100 text-[#7e22ce]",
  };
  return classes[status] || "bg-gray-100 text-gray-600";
};

// Load appointment on mount
onMounted(() => {
  if (props.appointmentId) {
    fetchAppointment();
  }
});
</script>

<style scoped>
/* No additional styles needed */
</style>
