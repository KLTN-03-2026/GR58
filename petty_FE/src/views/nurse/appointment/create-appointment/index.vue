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
              <h3 class="text-base font-bold text-black">
                Khối 1: Định danh (Khách & Pet)
              </h3>
            </div>

            <!-- Search Input -->
            <div class="relative">
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

              <!-- Search Results Dropdown -->
              <div
                v-if="showSearchResults && searchResults.length > 0"
                class="absolute z-10 w-full mt-1 bg-white border !border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto"
              >
                <button
                  v-for="customer in searchResults"
                  :key="customer.id"
                  class="w-full px-3 py-2 text-left hover:bg-gray-50 border-b !border-gray-100 last:border-b-0"
                  @click="selectCustomer(customer)"
                >
                  <p class="text-sm font-medium text-black">{{ customer.full_name }}</p>
                  <p class="text-xs text-gray-500">{{ customer.so_dien_thoai }}</p>
                </button>
              </div>

              <!-- No Results -->
              <div
                v-if="showSearchResults && searchResults.length === 0 && searchQuery.length >= 2 && !searching"
                class="absolute z-10 w-full mt-1 bg-white border !border-gray-300 rounded-lg shadow-lg p-3"
              >
                <p class="text-sm text-gray-500">Không tìm thấy khách hàng</p>
              </div>
            </div>

            <!-- Selected Customer Display -->
            <div v-if="selectedCustomer" class="mt-3 bg-white border !border-green-300 rounded-lg p-3">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-semibold text-black">{{ selectedCustomer.full_name }}</p>
                  <p class="text-xs text-gray-500">{{ selectedCustomer.so_dien_thoai }}</p>
                </div>
                <button @click="clearCustomer" class="text-xs text-red-500 hover:text-red-700">Xoá</button>
              </div>

              <!-- Pet Selection -->
              <div v-if="customerPets.length > 0" class="mt-2 pt-2 border-t !border-gray-200">
                <label class="text-xs font-medium text-gray-600 mb-1 block">Chọn thú cưng:</label>
                <select
                  v-model="selectedPetId"
                  class="w-full bg-gray-50 border !border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#009689]"
                >
                  <option value="">-- Chọn thú cưng --</option>
                  <option v-for="pet in customerPets" :key="pet.id" :value="pet.id">
                    {{ pet.ten_thu_cung }}
                  </option>
                </select>
              </div>
              <p v-else class="mt-2 text-xs text-orange-500">Khách hàng chưa có thú cưng nào</p>
            </div>
          </div>

          <!-- Section 2: Service & Doctor Assignment -->
          <div class="bg-blue-50 border-2 !border-[#bedbff] rounded-[14px] p-4">
            <div class="flex items-center gap-2 mb-4">
              <h3 class="text-base font-bold text-black">
                Khối 2: Phân luồng (Dịch vụ & Bác sĩ)
              </h3>
            </div>

            <div class="flex flex-col gap-4">
              <!-- Service Selection (multi-select) -->
              <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-black">
                  Loại dịch vụ <span class="text-red-600">*</span>
                </label>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="dv in services"
                    :key="dv.id"
                    :class="[
                      'px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors',
                      selectedServiceIds.includes(dv.id)
                        ? 'bg-[#009689] text-white !border-[#009689]'
                        : 'bg-white text-gray-700 !border-gray-300 hover:border-[#009689]'
                    ]"
                    @click="toggleService(dv.id)"
                  >
                    {{ dv.ten_dich_vu || dv.ten }}
                  </button>
                </div>
                <p v-if="services.length === 0" class="text-xs text-gray-400">Đang tải...</p>
              </div>

              <!-- Doctor/Room Assignment -->
              <div class="flex flex-col gap-2">
                <div class="flex items-baseline gap-0.5">
                  <label class="text-sm font-medium text-black">
                    Phân công Bác sĩ / Phòng khám
                  </label>
                  <span class="text-sm font-medium text-red-600"> * </span>
                </div>
                <select
                  v-model="selectedDoctor"
                  class="bg-white border !border-gray-300 rounded-lg px-3 py-2.5 h-11 focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent text-sm w-full"
                >
                  <option value="">-- Vui lòng chọn Bác sĩ --</option>
                  <option v-for="bs in doctors" :key="bs.id" :value="bs.id">
                    {{ bs.full_name }}
                  </option>
                </select>
                <p class="text-xs text-gray-600">
                  Mặc định chọn bác sĩ rảnh nhất
                </p>
              </div>
            </div>
          </div>

          <!-- Error Messages -->
          <div v-if="errorMessage" class="bg-red-50 border !border-red-300 rounded-lg p-3">
            <p class="text-sm text-red-600">{{ errorMessage }}</p>
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
          :disabled="submitting"
          :class="[
            'rounded-lg px-4 py-2.5 h-10 flex items-center gap-2 transition-colors',
            submitting ? 'bg-gray-400 cursor-not-allowed' : 'bg-[#009689] hover:bg-[#007d72]'
          ]"
          @click="handleSubmit"
        >
          <span class="text-sm font-medium text-white">
            {{ submitting ? 'Đang tạo...' : 'Tiếp nhận' }}
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick } from "vue";
import api from "@/utils/api";
import { createAppointment } from "@/services/lichHenService";
import { dichVuService } from "@/services/dichVuService";

const props = defineProps({
  isOpen: { type: Boolean, default: false },
  prefilledCustomer: { type: Object, default: null },
});

const emit = defineEmits(["close", "submit", "success"]);

// State
const searchInput = ref(null);
const searchQuery = ref("");
const searchResults = ref([]);
const showSearchResults = ref(false);
const searching = ref(false);
let searchTimeout = null;

const selectedCustomer = ref(null);
const customerPets = ref([]);
const selectedPetId = ref("");

const services = ref([]);
const selectedServiceIds = ref([]);
const selectedDoctor = ref("");
const doctors = ref([]);

const errorMessage = ref("");
const submitting = ref(false);

const fetchDoctors = async () => {
  try {
    const res = await api.get("/nhan-vien?vai_tro=bac_si");
    if (res.data && res.data.status) {
      doctors.value = res.data.data;
    }
  } catch (error) {
    console.error("Failed to fetch doctors:", error);
  }
};

const fetchServices = async () => {
  try {
    const res = await dichVuService.getAll({ per_page: 50 });
    if (res.status && res.data) {
      services.value = Array.isArray(res.data) ? res.data : (res.data.data || []);
    }
  } catch (error) {
    console.error("Failed to fetch services:", error);
  }
};

watch(
  () => props.isOpen,
  (newVal) => {
    if (newVal) {
      if (doctors.value.length === 0) fetchDoctors();
      if (services.value.length === 0) fetchServices();
      if (props.prefilledCustomer) {
        selectCustomer(props.prefilledCustomer);
      }
      nextTick(() => {
        searchInput.value?.focus();
      });
    } else {
      resetForm();
    }
  }
);

const resetForm = () => {
  searchQuery.value = "";
  searchResults.value = [];
  showSearchResults.value = false;
  selectedCustomer.value = null;
  customerPets.value = [];
  selectedPetId.value = "";
  selectedServiceIds.value = [];
  selectedDoctor.value = "";
  errorMessage.value = "";
  submitting.value = false;
};

const handleSearch = () => {
  clearTimeout(searchTimeout);
  errorMessage.value = "";

  if (searchQuery.value.length < 2) {
    searchResults.value = [];
    showSearchResults.value = false;
    return;
  }

  searching.value = true;
  showSearchResults.value = true;

  searchTimeout = setTimeout(async () => {
    try {
      const res = await api.get("/khach-hang", {
        params: { search: searchQuery.value, per_page: 10 },
      });
      if (res.data && res.data.status) {
        searchResults.value = res.data.data?.data || res.data.data || [];
      }
    } catch (error) {
      console.error("Search error:", error);
      searchResults.value = [];
    } finally {
      searching.value = false;
    }
  }, 300);
};

const selectCustomer = (customer) => {
  selectedCustomer.value = customer;
  searchQuery.value = customer.full_name;
  showSearchResults.value = false;

  customerPets.value = customer.thu_cung || customer.thuCungs || [];
  if (customerPets.value.length === 1) {
    selectedPetId.value = customerPets.value[0].id;
  } else {
    selectedPetId.value = "";
  }
};

const clearCustomer = () => {
  selectedCustomer.value = null;
  customerPets.value = [];
  selectedPetId.value = "";
  searchQuery.value = "";
};

const toggleService = (id) => {
  const idx = selectedServiceIds.value.indexOf(id);
  if (idx === -1) {
    selectedServiceIds.value.push(id);
  } else {
    selectedServiceIds.value.splice(idx, 1);
  }
};

const closeModal = () => {
  emit("close");
};

const handleSubmit = async () => {
  errorMessage.value = "";

  if (!selectedCustomer.value) {
    errorMessage.value = "Vui lòng tìm và chọn khách hàng";
    return;
  }
  if (!selectedPetId.value) {
    errorMessage.value = "Vui lòng chọn thú cưng";
    return;
  }
  if (selectedServiceIds.value.length === 0) {
    errorMessage.value = "Vui lòng chọn ít nhất 1 dịch vụ";
    return;
  }
  if (!selectedDoctor.value) {
    errorMessage.value = "Vui lòng chọn Bác sĩ";
    return;
  }

  submitting.value = true;
  try {
    const now = new Date();
    now.setMinutes(now.getMinutes() + 5);
    const ngayGio = now.toISOString().slice(0, 19).replace("T", " ");

    const payload = {
      thu_cung_id: selectedPetId.value,
      dich_vu_ids: selectedServiceIds.value,
      nhan_vien_id: selectedDoctor.value,
      ngay_gio: ngayGio,
      trang_thai: "confirmed",
    };

    const result = await createAppointment(payload);
    emit("success", result);
    emit("submit", result);
    closeModal();
  } catch (error) {
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      errorMessage.value = Object.values(errors).flat().join(". ");
    } else {
      errorMessage.value = error.response?.data?.message || "Có lỗi xảy ra, vui lòng thử lại";
    }
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}
</style>
