<template>
  <div
    class="bg-white flex flex-col pb-4 pt-6 px-6 rounded-[14px] shadow-lg w-full max-h-[90vh]"
  >
    <!-- Header -->
    <div
      class="flex items-center justify-between border-b !border-gray-300 pb-4 mb-4"
    >
      <div class="flex items-center gap-3">
        <h2 class="font-semibold text-xl text-black">Chỉ định cận lâm sàng</h2>
      </div>
      <button
        @click="$emit('close')"
        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
      >
        <CloseIcon />
      </button>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col gap-4 flex-1 overflow-y-auto">
      <!-- Search Bar with Dropdown -->
      <div class="relative">
        <div
          class="flex-1 bg-gray-50 border !border-gray-300 rounded-lg overflow-hidden"
        >
          <input
            v-model="searchQuery"
            @focus="searchQuery && (showSearchResults = true)"
            type="text"
            placeholder="Tìm tên xét nghiệm, dịch vụ..."
            class="w-full h-10 px-4 py-2 font-normal text-sm text-gray-700 bg-transparent border-none outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset"
          />
        </div>
        
        <!-- Search Results Dropdown -->
        <div
          v-if="showSearchResults && searchQuery"
          class="absolute top-full left-0 right-0 mt-1 bg-white border !border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto z-10"
        >
          <!-- Service Results -->
          <button
            v-for="service in availableServices"
            :key="service.id"
            @click="selectService(service)"
            class="w-full px-4 py-2 text-left hover:bg-gray-50 flex justify-between items-center border-b !border-gray-100 last:border-b-0"
          >
            <span class="text-sm text-gray-900">{{ service.ten_dich_vu }}</span>
            <span class="text-xs text-blue-600">{{ formatPrice(service.gia) }}</span>
          </button>
          
          <!-- No Results -->
          <div
            v-if="availableServices.length === 0"
            class="px-4 py-3 text-center"
          >
            <p class="text-sm text-gray-500 mb-2">Không tìm thấy dịch vụ</p>
            <button
              @click="addCustomService"
              class="w-full bg-blue-50 border !border-blue-300 rounded-lg px-4 py-2 hover:bg-blue-100 transition-colors"
            >
              <span class="text-sm font-medium text-blue-700">+ Thêm dịch vụ mới</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Service Item Card -->
      <div
        class="bg-purple-50 border !border-purple-200 rounded-lg p-4 flex flex-col gap-4"
      >
        <!-- Service Header -->
        <div class="flex items-start justify-between">
          <div class="flex flex-col gap-1">
            <p class="font-bold text-sm text-black">{{ clinicalTest.name }}</p>
            <p class="font-normal text-xs text-purple-600">{{ formatPrice(clinicalTest.price) }}</p>
          </div>
          <button
            @click="$emit('close')"
            class="w-6 h-6 flex items-center justify-center rounded hover:bg-purple-100 transition-colors"
          >
            <span class="text-gray-500 text-lg">×</span>
          </button>
        </div>

        <!-- Form Fields -->
        <div class="flex flex-col gap-2">
          <label class="font-medium text-xs text-gray-700">
            Ghi chú / Yêu cầu đặc biệt
          </label>
          <textarea
            v-model="clinicalTest.note"
            placeholder="VD: Xét nghiệm máu toàn phần, cần nhịn ăn trước khi xét nghiệm..."
            rows="4"
            class="w-full px-3 py-2 bg-white border !border-gray-300 rounded-lg font-normal text-sm text-gray-700 outline-none focus:ring-2 focus:ring-purple-400 transition-all resize-none"
          />
        </div>

        <!-- Priority -->
        <div class="flex flex-col gap-2">
          <label class="font-medium text-xs text-gray-700">
            Mức độ ưu tiên
          </label>
          <div class="flex gap-2">
            <button
              v-for="priority in priorities"
              :key="priority"
              @click="clinicalTest.priority = priority"
              :class="
                clinicalTest.priority === priority
                  ? 'bg-purple-600 text-white'
                  : 'bg-white text-gray-700 border !border-gray-300'
              "
              class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            >
              {{ priority }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Actions -->
    <div
      class="flex items-center justify-end gap-3 border-t !border-gray-300 pt-4 mt-4"
    >
      <button
        @click="$emit('close')"
        class="bg-white border !border-gray-300 rounded-lg px-4 py-2 h-10 hover:bg-gray-50 transition-colors"
      >
        <span class="font-medium text-sm text-gray-900"> Hủy </span>
      </button>
      <button
        @click="saveClinicalTest"
        class="bg-purple-600 rounded-lg px-4 py-2 h-10 flex items-center gap-2 hover:bg-purple-700 transition-colors"
      >
        <span class="font-medium text-sm text-white"> Lưu chỉ định </span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import api from "@/utils/api.js";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
import CloseIcon from "@/assets/svg/close.svg";

const emit = defineEmits(["close", "save"]);

const searchQuery = ref("");
const clinicalTest = ref({
  id: null,
  name: "Xét nghiệm máu",
  price: 150000,
  note: "",
  priority: "Bình thường",
});
const availableServices = ref([]);
const showSearchResults = ref(false);
const priorities = ["Khẩn cấp", "Ưu tiên", "Bình thường"];

const fetchServices = async () => {
  if (!searchQuery.value.trim()) {
    availableServices.value = [];
    return;
  }
  
  try {
    const response = await api.get("/dich-vu", {
      params: {
        search: searchQuery.value,
      },
    });
    availableServices.value = response.data.data || [];
    showSearchResults.value = true;
  } catch (error) {
    console.error("Error fetching services:", error);
  }
};

const selectService = (service) => {
  clinicalTest.value.id = service.id;
  clinicalTest.value.name = service.ten_dich_vu;
  clinicalTest.value.price = service.gia;
  searchQuery.value = "";
  showSearchResults.value = false;
};

const addCustomService = () => {
  if (!searchQuery.value.trim()) {
    showErrorToast("Lỗi", "Vui lòng nhập tên dịch vụ");
    return;
  }
  
  clinicalTest.value.id = null;
  clinicalTest.value.name = searchQuery.value;
  clinicalTest.value.price = 0;
  searchQuery.value = "";
  showSearchResults.value = false;
  showSuccessToast("Đã chọn", clinicalTest.value.name);
};

const formatPrice = (price) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(price);
};

const saveClinicalTest = () => {
  if (!clinicalTest.value.name) {
    showErrorToast("Lỗi", "Vui lòng chọn dịch vụ");
    return;
  }

  emit("save", clinicalTest.value);
  showSuccessToast("Thành công", "Đã lưu chỉ định cận lâm sàng");
};

watch(searchQuery, (newVal) => {
  if (newVal && newVal.trim()) {
    fetchServices();
  } else {
    availableServices.value = [];
    showSearchResults.value = false;
  }
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}
</style>
