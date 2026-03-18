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
          Phân công Bác sĩ
        </h2>
        <p class="font-nunito text-sm leading-5 text-[#717182] tracking-tight">
          Chọn bác sĩ phù hợp cho lịch hẹn này
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
          <!-- Appointment Info Alert -->
          <div
            class="bg-teal-50 border !border-teal-200 rounded-[10px] px-[17px] py-[13px] relative"
          >
            <div class="absolute left-[17px] top-[15px] w-4 h-4">
              <CalendarIcon />
            </div>
            <div class="ml-7 flex flex-col gap-1">
              <div class="flex items-start">
                <p
                  class="font-nunito font-bold text-sm text-[#717182] tracking-tight"
                >
                  Lịch hẹn:
                </p>
                <p
                  class="font-nunito text-sm text-[#717182] tracking-tight ml-1"
                >
                  {{ appointmentInfo.id }} - {{ appointmentInfo.service }}
                </p>
              </div>
              <div class="flex items-center gap-1">
                <ClockIcon class="w-3 h-3" />
                <p class="font-nunito text-sm text-[#717182] tracking-tight">
                  {{ appointmentInfo.time }} - {{ appointmentInfo.date }} | 🐾
                  {{ appointmentInfo.pet }}
                </p>
              </div>
            </div>
          </div>

          <!-- Staff List Container -->
          <div class="pr-2 flex flex-col gap-4 max-h-[316px] overflow-y-auto">
            <!-- Available Doctors Section -->
            <div class="flex flex-col gap-3">
              <div class="flex items-center gap-2 h-5">
                <div class="w-2 h-2 rounded-full bg-[#00c950]"></div>
                <h4
                  class="font-nunito font-medium text-sm leading-5 text-[#364153] tracking-tight"
                >
                  Bác sĩ khả dụng ({{ availableStaff.length }})
                </h4>
              </div>

              <div class="flex flex-col gap-2">
                <div
                  v-for="staff in availableStaff"
                  :key="staff.id"
                  class="bg-green-50 border !border-green-200 rounded-[10px] px-[13px] py-[13px] flex items-center justify-between"
                >
                  <div class="flex items-center gap-3">
                    <!-- Avatar -->
                    <div
                      class="w-10 h-10 rounded-full bg-[#009689] flex items-center justify-center"
                    >
                      <span
                        class="font-nunito text-base leading-6 text-white tracking-tight"
                      >
                        {{ staff.initial }}
                      </span>
                    </div>

                    <!-- Info -->
                    <div class="flex flex-col gap-1">
                      <p
                        class="font-nunito font-medium text-base leading-6 text-[#101828] tracking-tight"
                      >
                        {{ staff.name }}
                      </p>
                      <div class="flex items-center gap-2">
                        <!-- <span class="bg-white border border-black/10 rounded-lg px-[9px] py-[3px] font-nunito font-medium text-xs leading-4 text-neutral-950">
                          {{ staff.department }}
                        </span> -->
                        <div class="flex items-center gap-1">
                          <div
                            class="w-[6px] h-[6px] rounded-full bg-[#00c950]"
                          ></div>
                          <span
                            class="font-nunito text-xs leading-4 text-[#00a63e]"
                          >
                            Đang trực
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Select Button -->
                  <button
                    @click="handleSelectStaff(staff)"
                    :disabled="isAssigning"
                    class="bg-[#00a63e] rounded-lg h-8 px-[10px] flex items-center gap-1 hover:bg-[#008c35] disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  >
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-white tracking-tight"
                    >
                      {{ isAssigning ? "Đang..." : "Chọn" }}
                    </span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Unavailable Doctors Section -->
            <div class="flex flex-col gap-3">
              <div class="flex items-center gap-2 h-5">
                <div class="w-2 h-2 rounded-full bg-[#99a1af]"></div>
                <h4
                  class="font-nunito font-medium text-sm leading-5 text-[#99a1af] tracking-tight"
                >
                  Không trực ({{ unavailableStaff.length }})
                </h4>
              </div>

              <div class="flex flex-col gap-2">
                <div
                  v-for="staff in unavailableStaff"
                  :key="staff.id"
                  class="bg-gray-50 border border-gray-200 rounded-[10px] px-[13px] py-[13px] flex items-center opacity-50"
                >
                  <div class="flex items-center gap-3">
                    <!-- Avatar -->
                    <div
                      class="w-10 h-10 rounded-full bg-[#d1d5dc] flex items-center justify-center"
                    >
                      <span
                        class="font-nunito text-base leading-6 text-[#4a5565] tracking-tight"
                      >
                        {{ staff.initial }}
                      </span>
                    </div>

                    <!-- Info -->
                    <div class="flex flex-col gap-1">
                      <p
                        class="font-nunito font-medium text-base leading-6 text-[#4a5565] tracking-tight"
                      >
                        {{ staff.name }}
                      </p>
                      <div class="flex items-center gap-2">
                        <!-- <span class="bg-white border border-black/10 rounded-lg px-[9px] py-[3px] font-nunito font-medium text-xs leading-4 text-neutral-950">
                          {{ staff.department }}
                        </span> -->
                        <span
                          class="font-nunito text-xs leading-4 text-[#6a7282]"
                        >
                          Không có ca trực
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Dialog Footer -->
      <div class="px-6 pt-4 pb-6 flex items-center justify-end">
        <button
          @click="$emit('close')"
          class="bg-white border !border-gray-300 rounded-lg px-4 py-2 font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight hover:bg-gray-50 transition-colors"
        >
          Đóng
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import client from "../../../../../utils/api.js";
//Icon SVG
import CloseIcon from "@/assets/svg/close.svg";
import CalendarIcon from "@/assets/svg/calendar.svg";
import ClockIcon from "@/assets/svg/clock.svg";

// Props
const props = defineProps({
  appointmentId: {
    type: String,
    default: "LH001236",
  },
  appointment: {
    type: Object,
    default: null,
  },
});

// Emits
const emit = defineEmits(["close", "assign"]);

// Reactive state
const isLoading = ref(false);
const error = ref(null);
const appointmentInfo = computed(() => {
  if (props.appointment) {
    return {
      id: props.appointment.id || "LH001236",
      service: props.appointment.service || "Phẫu thuật xương",
      duration: props.appointment.duration || 120,
      time: props.appointment.time || "14:00",
      date: props.appointment.date || "20/11/2025",
      pet: props.appointment.pet || "Bella",
    };
  }
  // Fallback data nếu appointment không được truyền
  return {
    id: "LH001236",
    service: "Phẫu thuật xương",
    duration: 120,
    time: "14:00",
    date: "20/11/2025",
    pet: "Bella",
  };
});

const allDoctors = ref([]);
const isAssigning = ref(false);

// Get available and unavailable doctors
const availableStaff = computed(() => {
  // Tất cả bác sĩ đã được lọc là hoạt động trong fetchDoctors()
  return allDoctors.value.filter((doctor) => doctor.trang_thai === "hoat_dong");
});

const unavailableStaff = computed(() => {
  // Không có bác sĩ không hoạt động vì đã lọc trong fetchDoctors()
  return allDoctors.value.filter((doctor) => doctor.trang_thai !== "hoat_dong");
});

// Fetch doctors from API
const fetchDoctors = async () => {
  isLoading.value = true;
  error.value = null;
  try {
    const response = await client.get("/nhan-vien");

    if (response.data.status && Array.isArray(response.data.data)) {
      // Filter only doctors (vai_tro = 'bac_si' or similar)
      console.log("All employees from API:", response.data.data);
      console.log("First employee structure:", response.data.data[0]); // Debug: check field names

      const doctors = response.data.data
        .filter((nhanvien) => {
          // Kiểm tra vai trò là bác sĩ
          const isBacSi =
            nhanvien.vai_tro === "bac_si" ||
            nhanvien.vai_tro === "doctor" ||
            nhanvien.vai_tro === "bacsi" ||
            nhanvien.chuc_vu === "Bác sĩ" ||
            nhanvien.chuc_vu === "bac_si";

          // Kiểm tra trạng thái là hoạt động
          const isActive = nhanvien.trang_thai === "hoat_dong";

          if (isBacSi && isActive) {
            console.log(
              `✓ Found active doctor: ${
                nhanvien.ho_ten ||
                nhanvien.name ||
                nhanvien.ten ||
                nhanvien.full_name
              }`
            );
          }

          // Chỉ trả về những bác sĩ có trạng thái hoạt động
          return isBacSi && isActive;
        })
        .map((doctor) => {
          const doctorName =
            doctor.ho_ten ||
            doctor.name ||
            doctor.ten ||
            doctor.full_name ||
            "Không xác định";
          console.log(
            `Mapped doctor: ID=${doctor.id}, Name=${doctorName}, Fields:`,
            Object.keys(doctor)
          );
          return {
            id: doctor.id,
            name: doctorName,
            initial:
              doctorName && doctorName !== "Không xác định"
                ? doctorName.charAt(0)
                : "?",
            trang_thai: doctor.trang_thai || "hoat_dong",
            department: doctor.chuc_vu || "Bác sĩ",
          };
        });

      console.log("Filtered doctors:", doctors);
      allDoctors.value = doctors;

      if (doctors.length === 0) {
        error.value = `Không tìm thấy bác sĩ hoạt động. Tổng nhân viên: ${response.data.data.length}. Vui lòng kiểm tra cơ sở dữ liệu.`;
      }
    }
  } catch (err) {
    error.value = err.message;
    console.error("Error fetching doctors:", err);
  } finally {
    isLoading.value = false;
  }
};

// Assign doctor to appointment
const handleSelectStaff = async (staff) => {
  isAssigning.value = true;
  try {
    // Update appointment with nhan_vien_id using PUT instead of PATCH
    const response = await client.put(`/lich-hen/${props.appointmentId}`, {
      nhan_vien_id: staff.id,
      trang_thai: "confirmed", // Use backend-accepted value
    });

    if (response.data.status) {
      emit("assign", {
        appointmentId: props.appointmentId,
        staffId: staff.id,
        staffName: staff.name,
        status: "confirmed", // Emit confirmed status
      });

      // Close modal after successful assignment
      setTimeout(() => {
        emit("close");
      }, 500);
    }
  } catch (err) {
    console.error("Error assigning doctor:", err);
    error.value = err.response?.data?.message || "Lỗi khi phân công bác sĩ";
  } finally {
    isAssigning.value = false;
  }
};

// Load doctors on mount
onMounted(() => {
  fetchDoctors();
});
</script>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
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
