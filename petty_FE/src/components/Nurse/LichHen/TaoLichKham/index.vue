<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-6"
    @click.self="closeModal"
  >
    <div
      class="bg-white rounded-[14px] shadow-lg w-full max-w-md max-h-[90vh] flex flex-col overflow-hidden"
    >
      <!-- Dialog Header -->
      <div class="px-6 pt-6 pb-4 border-b !border-gray-300">
        <h2 class="text-lg font-semibold text-black">Tiếp Nhận Bệnh Nhân</h2>
        <p class="text-sm text-gray-600 mt-1">
          Tạo lịch khám mới ngay tại phòng khám
        </p>
      </div>

      <!-- Dialog Content (Scrollable) -->
      <div class="flex-1 overflow-y-auto px-6 py-6">
        <div class="flex flex-col gap-6">
          <!-- Section 1: Customer & Pet Identity -->
          <div class="bg-teal-50 border-2 !border-[#96f7e4] rounded-[14px] p-4">
            <div class="flex items-center gap-2 mb-4">
              <!-- <img :src="iconUser" alt="User" class="w-5 h-5" /> -->
              <h3 class="text-base font-bold text-black">
                Khối 1: Định danh (Khách & Pet)
              </h3>
            </div>

            <div
              class="bg-white border !border-gray-300 rounded-lg px-3 py-2.5 h-11 flex items-center"
            >
              <input
                ref="searchInput"
                v-model="searchQuery"
                type="text"
                placeholder="Nhập SĐT hoặc Tên (Auto-focus)..."
                class="w-full bg-transparent text-sm text-gray-900 outline-none placeholder:text-gray-400"
                @input="handleSearch"
              />
            </div>
          </div>

          <!-- Section 2: Service & Doctor Assignment -->
          <div class="bg-blue-50 border-2 !border-[#bedbff] rounded-[14px] p-4">
            <div class="flex items-center gap-2 mb-4">
              <!-- <img :src="iconService" alt="Service" class="w-5 h-5" /> -->
              <h3 class="text-base font-bold text-black">
                Khối 2: Phân luồng (Dịch vụ & Bác sĩ)
              </h3>
            </div>

            <div class="flex flex-col gap-4">
              <!-- Service Type Select -->
              <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-black">
                  Loại dịch vụ
                </label>
                <button
                  class="bg-white border !border-gray-300 rounded-lg px-3 py-2.5 h-11 flex items-center justify-between hover:border-[#009689] transition-colors"
                  @click="toggleServiceDropdown"
                >
                  <span class="text-sm text-black">
                    {{ selectedService || "Khám tổng quát" }}
                  </span>
                  <!-- <img :src="iconChevronDown" alt="Dropdown" class="w-4 h-4" /> -->
                </button>
              </div>

              <!-- Doctor/Room Assignment -->
              <div class="flex flex-col gap-2">
                <div class="flex items-baseline gap-0.5">
                  <label class="text-sm font-medium text-black">
                    Phân công Bác sĩ / Phòng khám
                  </label>
                  <span class="text-sm font-medium text-red-600"> * </span>
                </div>
                <button
                  class="bg-white border !border-gray-300 rounded-lg px-3 py-2.5 h-11 flex items-center justify-between hover:border-[#009689] transition-colors"
                  @click="toggleDoctorDropdown"
                >
                  <span class="text-sm text-black">
                    {{ selectedDoctor || "BS. Hùng - P.102" }}
                  </span>
                  <!-- <img :src="iconChevronDown" alt="Dropdown" class="w-4 h-4" /> -->
                </button>
                <div class="flex items-center gap-1">
                  <!-- <img :src="iconInfo" alt="Info" class="w-3 h-3" /> -->
                  <p class="text-xs text-gray-600">
                    Mặc định chọn bác sĩ rảnh nhất
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Dialog Footer -->
      <div
        class="px-6 py-4 border-t !border-gray-300 flex items-center justify-end gap-3"
      >
        <button
          class="bg-white border !border-gray-300 rounded-lg px-4 py-2.5 h-10 hover:bg-gray-50 transition-colors"
          @click="closeModal"
        >
          <span class="text-sm font-medium text-black"> Hủy </span>
        </button>
        <button
          class="bg-[#009689] rounded-lg px-4 py-2.5 h-10 flex items-center gap-2 hover:bg-[#007d72] transition-colors"
          @click="handleSubmit"
        >
          <!-- <img :src="iconCheck" alt="Submit" class="w-4 h-4" /> -->
          <span class="text-sm font-medium text-white"> Tiếp nhận </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick } from "vue";

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
});

// Emits
const emit = defineEmits(["close", "submit"]);

// Icons
const iconUser =
  "https://www.figma.com/api/mcp/asset/73ac094a-c79d-4bd4-affd-5380b088b2af";
const iconService =
  "https://www.figma.com/api/mcp/asset/57520e3f-9f22-4c25-8a31-3be4371178cd";
const iconChevronDown =
  "https://www.figma.com/api/mcp/asset/182e137a-e0da-4077-8d26-98371db706d5";
const iconInfo =
  "https://www.figma.com/api/mcp/asset/cf2d49ea-3232-44e3-9cab-858396917d5a";
const iconCheck =
  "https://www.figma.com/api/mcp/asset/41d97c29-d6c8-4fed-8ed1-c7e7daa2d717";

// State
const searchInput = ref(null);
const searchQuery = ref("");
const selectedService = ref("Khám tổng quát");
const selectedDoctor = ref("BS. Hùng - P.102");

// Watch for modal open to auto-focus
watch(
  () => props.isOpen,
  (newVal) => {
    if (newVal) {
      nextTick(() => {
        searchInput.value?.focus();
      });
    } else {
      // Reset form when modal closes
      searchQuery.value = "";
      selectedService.value = "Khám tổng quát";
      selectedDoctor.value = "BS. Hùng - P.102";
    }
  }
);

// Methods
const closeModal = () => {
  emit("close");
};

const handleSearch = () => {
  console.log("Search query:", searchQuery.value);
  // TODO: Implement search logic for customer/pet by phone or name
};

const toggleServiceDropdown = () => {
  console.log("Toggle service dropdown");
  // TODO: Implement service selection dropdown
};

const toggleDoctorDropdown = () => {
  console.log("Toggle doctor dropdown");
  // TODO: Implement doctor/room selection dropdown
};

const handleSubmit = () => {
  if (!selectedDoctor.value) {
    alert("Vui lòng chọn Bác sĩ / Phòng khám");
    return;
  }

  const appointmentData = {
    searchQuery: searchQuery.value,
    service: selectedService.value,
    doctor: selectedDoctor.value,
  };

  console.log("Submit appointment:", appointmentData);
  emit("submit", appointmentData);
  // TODO: Implement appointment creation logic
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}
</style>
