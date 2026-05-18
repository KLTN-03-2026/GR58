<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 z-[12000] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 sm:p-6"
    @click.self="closeModal"
  >
    <div
      :class="[
        'bg-white rounded-2xl shadow-2xl w-full flex flex-col overflow-hidden border border-gray-200 transition-all duration-200',
        isExpandedLayout
          ? 'max-w-[1040px] max-h-[92vh]'
          : 'max-w-[760px]'
      ]"
    >
      <!-- Header -->
      <div :class="[
        'border-b border-gray-200 bg-gradient-to-br from-teal-50 to-white',
        showQuickCreate ? 'px-6 pt-6 pb-4 sm:px-7' : 'px-8 pt-8 pb-6'
      ]">
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Tiếp Nhận Bệnh Nhân</h2>
        <p :class="['text-sm text-gray-600', showQuickCreate ? 'mt-1.5' : 'mt-2']">
          Tìm kiếm khách hàng hoặc tạo hồ sơ mới ngay trong luồng tiếp nhận
        </p>
      </div>

      <!-- Content -->
      <div :class="[
        'min-h-0 overflow-y-auto',
        showQuickCreate ? 'px-6 py-4 sm:px-7' : 'px-6 py-6 sm:px-8'
      ]">
        <div
          :class="[
            showQuickCreate ? 'space-y-4' : 'space-y-6',
            showServiceStep ? 'xl:grid xl:grid-cols-[minmax(0,1.15fr)_minmax(360px,0.85fr)] xl:gap-6 xl:space-y-0' : ''
          ]"
        >
          <!-- Step 1: Customer Search & Identity -->
          <div :class="[showQuickCreate ? 'space-y-3' : 'space-y-4', 'min-w-0']">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center text-sm font-bold">
                1
              </div>
              <h3 class="text-lg font-bold text-gray-900">
                {{ showQuickCreate ? "Tạo hồ sơ khách hàng" : "Định danh khách hàng" }}
              </h3>
            </div>

            <!-- Search Input -->
            <div v-if="!showQuickCreate">
              <input
                ref="searchInput"
                v-model="searchQuery"
                type="text"
                placeholder="Nhập số điện thoại hoặc tên khách hàng..."
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100 transition-all"
                @input="handleSearch"
              />

              <p class="mt-2 text-xs text-gray-500">
                Hệ thống sẽ tìm theo tên hoặc số điện thoại. Danh sách kết quả có cuộn riêng.
              </p>

              <!-- Search Results Panel -->
              <div
                v-if="showSearchResults && searchResults.length > 0"
                class="mt-3 overflow-hidden rounded-2xl border-2 border-gray-200 bg-white shadow-lg"
              >
                <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-4 py-3">
                  <div>
                    <p class="text-sm font-semibold text-gray-900">
                      {{ searchResults.length }} khách hàng phù hợp
                    </p>
                    <p class="text-xs text-gray-500">
                      Chọn đúng người theo số điện thoại và thú cưng
                    </p>
                  </div>
                  <span class="rounded-full bg-white px-2.5 py-1 text-[11px] font-semibold text-gray-500 border border-gray-200">
                    Cuộn riêng
                  </span>
                </div>
                <div class="max-h-[320px] overflow-y-auto">
                  <button
                    v-for="customer in searchResults"
                    :key="customer.id"
                    class="w-full border-b border-gray-100 px-4 py-3 text-left transition-colors hover:bg-teal-50 last:border-b-0"
                    @click="selectCustomer(customer)"
                  >
                    <div class="flex items-start justify-between gap-3">
                      <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ customer.full_name }}</p>
                        <p class="mt-1 text-xs font-medium text-gray-600">
                          {{ customer.so_dien_thoai || "Chưa có số điện thoại" }}
                        </p>
                        <p v-if="customer.email" class="mt-1 truncate text-xs text-gray-500">
                          {{ customer.email }}
                        </p>
                      </div>
                      <div class="shrink-0 text-right">
                        <span class="inline-flex rounded-full bg-teal-50 px-2.5 py-1 text-[11px] font-semibold text-teal-700">
                          {{ customer.thu_cung?.length || customer.thuCungs?.length || 0 }} thú cưng
                        </span>
                      </div>
                    </div>
                  </button>
                </div>
              </div>

              <!-- Searching State -->
              <div
                v-if="showSearchResults && searching && searchResults.length === 0"
                class="mt-3 rounded-2xl border border-dashed border-gray-300 bg-white px-4 py-4"
              >
                <p class="text-sm font-medium text-gray-700">Đang tìm khách hàng...</p>
                <p class="mt-1 text-xs text-gray-500">Kết quả sẽ hiện ngay trong danh sách bên dưới.</p>
              </div>

              <!-- No Results - Quick Create Prompt -->
              <div
                v-if="showSearchResults && searchResults.length === 0 && searchQuery.length >= 2 && !searching && !showQuickCreate && !selectedCustomer && !justCreatedCustomer"
                class="mt-3 bg-white border-2 border-amber-200 rounded-xl shadow-xl p-4"
              >
                <p class="text-sm font-semibold text-gray-800 mb-1">Không tìm thấy khách hàng phù hợp</p>
                <p class="text-xs text-gray-500 mb-3">
                  Có thể đây là khách vãng lai hoặc hồ sơ chưa được tạo trong hệ thống.
                </p>
                <button
                  class="w-full px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-semibold transition-colors"
                  @click="showQuickCreate = true"
                >
                  + Tạo hồ sơ mới ngay
                </button>
              </div>
            </div>

            <!-- Selected Customer Display -->
            <div v-if="selectedCustomer && !showQuickCreate" class="bg-teal-50 border-2 border-teal-200 rounded-xl p-4">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <p class="text-sm font-bold text-gray-900">{{ selectedCustomer.full_name }}</p>
                  <p class="text-xs text-gray-600 mt-1">{{ selectedCustomer.so_dien_thoai }}</p>
                  <p v-if="selectedCustomer.email" class="text-xs text-gray-500 mt-0.5">{{ selectedCustomer.email }}</p>
                </div>
                <button @click="clearCustomer" class="text-xs text-red-600 hover:text-red-700 font-medium">
                  Xóa
                </button>
              </div>

              <!-- Pet Selection -->
              <div v-if="customerPets.length > 0" class="mt-4 pt-4 border-t border-teal-200">
                <label class="text-xs font-bold text-gray-700 mb-2 block uppercase tracking-wide">Chọn thú cưng:</label>
                <select
                  v-model="selectedPetId"
                  class="w-full px-3 py-2.5 bg-white border-2 border-gray-300 rounded-lg text-sm focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100"
                >
                  <option value="">-- Chọn thú cưng --</option>
                  <option v-for="pet in customerPets" :key="pet.id" :value="pet.id">
                    {{ pet.ten_thu_cung }}
                  </option>
                </select>
              </div>
              <p v-else class="mt-4 pt-4 border-t border-teal-200 text-xs text-amber-600 font-medium">
                ⚠️ Khách hàng chưa có thú cưng
              </p>
            </div>

            <!-- Quick Create Form -->
            <div v-if="showQuickCreate" class="rounded-xl border border-amber-200 bg-amber-50/60 p-4 space-y-3">
              <div class="flex items-start justify-between gap-4">
                <div>
                  <h4 class="text-base font-bold text-gray-900">Tạo hồ sơ nhanh</h4>
                  <p class="mt-1 text-xs text-gray-600">
                    Chỉ giữ các thông tin tối thiểu để tiếp tục tiếp nhận.
                  </p>
                </div>
                <button @click="cancelQuickCreate" class="text-sm text-gray-500 hover:text-gray-700">
                  Hủy
                </button>
              </div>

              <!-- Customer Info -->
              <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                  <label class="text-xs font-semibold text-gray-700 mb-1.5 block">Họ tên <span class="text-red-600">*</span></label>
                  <input
                    v-model="quickCreateData.full_name"
                    type="text"
                    placeholder="Nguyễn Văn A"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"
                  />
                </div>
                <div>
                  <label class="text-xs font-semibold text-gray-700 mb-1.5 block">Số điện thoại <span class="text-red-600">*</span></label>
                  <input
                    v-model="quickCreateData.phone"
                    type="tel"
                    placeholder="0912345678"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"
                  />
                </div>
              </div>

              <div>
                <label class="text-xs font-semibold text-gray-700 mb-1.5 block">Email (tùy chọn)</label>
                <input
                  v-model="quickCreateData.email"
                  type="email"
                  placeholder="email@example.com"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"
                />
              </div>

              <!-- Pet Info -->
              <div class="space-y-3 border-t border-amber-200 pt-3">
                <h5 class="text-xs font-bold text-gray-700 uppercase tracking-wide">Thông tin thú cưng</h5>
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                  <div>
                    <label class="text-xs font-semibold text-gray-700 mb-1.5 block">Tên thú cưng <span class="text-red-600">*</span></label>
                    <input
                      v-model="quickCreateData.ten_thu_cung"
                      type="text"
                      placeholder="Milo"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"
                    />
                  </div>
                  <div>
                    <label class="text-xs font-semibold text-gray-700 mb-1.5 block">Loài <span class="text-red-600">*</span></label>
                    <div class="flex h-10 items-center gap-4 rounded-lg border border-gray-300 bg-white px-3">
                      <label
                        v-for="species in quickCreateSpeciesOptions"
                        :key="species.value"
                        class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer"
                      >
                        <div class="relative">
                          <input
                            v-model="quickCreateData.loai_thu_cung"
                            type="radio"
                            :value="species.value"
                            class="appearance-none h-4 w-4 rounded-full border border-gray-300 bg-white checked:border-amber-500"
                          />
                          <span
                            v-if="quickCreateData.loai_thu_cung === species.value"
                            class="pointer-events-none absolute left-1/2 top-1/2 h-2 w-2 -translate-x-1/2 -translate-y-1/2 rounded-full bg-amber-500"
                          />
                        </div>
                        <span>{{ species.label }}</span>
                      </label>
                    </div>
                  </div>
                </div>
                <div v-if="isOtherSpecies">
                  <label class="text-xs font-semibold text-gray-700 mb-1.5 block">Loài cụ thể <span class="text-red-600">*</span></label>
                  <select
                    v-model="quickCreateData.loai_thu_cung_khac"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"
                  >
                    <option value="">-- Chọn loài cụ thể --</option>
                    <option
                      v-for="species in quickCreateOtherSpeciesOptions"
                      :key="species.value"
                      :value="species.value"
                    >
                      {{ species.label }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="text-xs font-semibold text-gray-700 mb-1.5 block">Giống <span class="text-red-600">*</span></label>
                  <select
                    v-model="quickCreateData.giong_thu_cung"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"
                  >
                    <option value="">
                      {{ isOtherSpecies ? "-- Chọn giống hoặc nhóm giống --" : "-- Chọn giống --" }}
                    </option>
                    <option v-for="breed in quickCreateBreeds" :key="breed" :value="breed">
                      {{ breed }}
                    </option>
                  </select>
                </div>
              </div>

              <button
                :disabled="creatingProfile"
                :class="[
                  'w-full px-4 py-2.5 rounded-lg text-sm font-bold transition-colors',
                  creatingProfile
                    ? 'bg-gray-400 cursor-not-allowed text-white'
                    : 'bg-amber-600 hover:bg-amber-700 text-white'
                ]"
                @click="handleQuickCreate"
              >
                {{ creatingProfile ? 'Đang tạo...' : '✓ Tạo và tiếp tục' }}
              </button>
            </div>
          </div>

          <!-- Step 2: Service & Doctor (only show when customer is resolved) -->
          <div v-if="showServiceStep" class="space-y-4 min-w-0">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold">
                2
              </div>
              <h3 class="text-lg font-bold text-gray-900">Dịch vụ & Bác sĩ</h3>
            </div>

            <!-- Service Selection -->
            <div>
              <label class="text-xs font-bold text-gray-700 mb-2 block uppercase tracking-wide">
                Dịch vụ dự kiến <span class="text-red-600">*</span>
              </label>
              <select
                v-model="selectedServiceId"
                class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
              >
                <option value="">-- Chọn dịch vụ --</option>
                <option v-for="dv in services" :key="dv.id" :value="dv.id">
                  {{ dv.ten_dich_vu || dv.ten }}
                </option>
              </select>
              <p v-if="services.length === 0" class="text-xs text-gray-400 mt-2">Đang tải dịch vụ...</p>
            </div>

            <!-- Doctor Assignment -->
            <div>
              <label class="text-xs font-bold text-gray-700 mb-2 block uppercase tracking-wide">
                Bác sĩ phụ trách <span class="text-red-600">*</span>
              </label>
              <select
                v-model="selectedDoctor"
                class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
              >
                <option value="">-- Chọn bác sĩ --</option>
                <option v-for="bs in doctors" :key="bs.id" :value="bs.id">
                  {{ bs.full_name }}
                </option>
              </select>
              <p v-if="suggestedDoctor" class="text-xs text-blue-600 mt-2 font-medium">
                💡 Gợi ý: {{ suggestedDoctor.full_name }}
              </p>
            </div>
          </div>

          <!-- Error Messages -->
          <div v-if="errorMessage" class="bg-red-50 border-2 border-red-300 rounded-xl p-4">
            <p class="text-sm text-red-700 font-medium">{{ errorMessage }}</p>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div
        v-if="!showQuickCreate"
        class="px-6 py-5 sm:px-8 border-t border-gray-200 bg-gray-50 flex items-center justify-end gap-3"
      >
        <button
          class="px-5 py-2.5 bg-white border-2 border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors"
          @click="closeModal"
        >
          Hủy
        </button>
        <button
          v-if="!showQuickCreate"
          :disabled="submitting"
          :class="[
            'px-6 py-2.5 rounded-xl text-sm font-bold transition-all shadow-lg',
            submitting
              ? 'bg-gray-400 cursor-not-allowed text-white'
              : 'bg-teal-600 hover:bg-teal-700 text-white hover:shadow-xl'
          ]"
          @click="handleSubmit"
        >
          {{ submitting ? 'Đang tạo...' : '✓ Tiếp nhận' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick, computed } from "vue";
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

const showQuickCreate = ref(false);
const creatingProfile = ref(false);
const justCreatedCustomer = ref(null);
const quickCreateData = ref({
  full_name: "",
  phone: "",
  email: "",
  ten_thu_cung: "",
  loai_thu_cung: "cho",
  loai_thu_cung_khac: "",
  giong_thu_cung: "",
});

const services = ref([]);
const selectedServiceIds = ref([]);
const selectedDoctor = ref("");
const doctors = ref([]);
const suggestedDoctor = ref(null);

const errorMessage = ref("");
const submitting = ref(false);

const showServiceStep = computed(
  () => !!(selectedCustomer.value || justCreatedCustomer.value) && !showQuickCreate.value
);

const isExpandedLayout = computed(
  () => showQuickCreate.value || showServiceStep.value || !!errorMessage.value
);

const selectedServiceId = computed({
  get: () => selectedServiceIds.value[0] ?? "",
  set: (value) => {
    selectedServiceIds.value = value ? [Number(value)] : [];
  },
});

const quickCreateSpeciesOptions = [
  { value: "cho", label: "Chó" },
  { value: "meo", label: "Mèo" },
  { value: "khac", label: "Khác" },
];

const quickCreateOtherSpeciesOptions = [
  { value: "tho", label: "Thỏ" },
  { value: "hamster", label: "Hamster" },
  { value: "chim", label: "Chim" },
  { value: "rua", label: "Rùa" },
  { value: "khac", label: "Loài khác" },
];

const quickCreateBreedsMap = {
  cho: [
    "Poodle",
    "Golden Retriever",
    "Husky",
    "Chihuahua",
    "Corgi",
    "Bulldog",
    "Chó ta",
  ],
  meo: ["Mèo Anh lông ngắn", "Mèo Ba Tư", "Mèo Xiêm", "Mèo Bengal", "Mèo ta"],
  tho: ["Thỏ ta", "Holland Lop", "Lionhead", "Mini Rex", "Khác"],
  hamster: ["Hamster Bear", "Campbell", "Winter White", "Roborovski", "Khác"],
  chim: ["Yến phụng", "Chào mào", "Vẹt", "Chim cảnh khác", "Khác"],
  rua: ["Rùa tai đỏ", "Rùa cạn", "Rùa nước", "Rùa kiểng khác", "Khác"],
  khac: ["Không rõ giống", "Khác"],
};

const isOtherSpecies = computed(() => quickCreateData.value.loai_thu_cung === "khac");

const effectiveSpeciesKey = computed(() => {
  if (!isOtherSpecies.value) return quickCreateData.value.loai_thu_cung;
  return quickCreateData.value.loai_thu_cung_khac || "";
});

const quickCreateBreeds = computed(
  () => quickCreateBreedsMap[effectiveSpeciesKey.value] || []
);

const effectiveSpeciesLabel = computed(() => {
  if (quickCreateData.value.loai_thu_cung === "cho") return "Chó";
  if (quickCreateData.value.loai_thu_cung === "meo") return "Mèo";

  const matchedOther = quickCreateOtherSpeciesOptions.find(
    (species) => species.value === quickCreateData.value.loai_thu_cung_khac
  );
  return matchedOther?.label || "";
});

const formatLocalDateTime = (date = new Date()) => {
  const pad = (value) => String(value).padStart(2, "0");

  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(
    date.getDate()
  )} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(
    date.getSeconds()
  )}`;
};

const fetchDoctors = async () => {
  try {
    const res = await api.get("/bac-si/danh-sach", {
      params: {
        ngay_gio: formatLocalDateTime(),
      },
    });
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

const fetchDoctorSuggestion = async () => {
  try {
    const ngayGio = formatLocalDateTime();

    const res = await api.post("/bac-si/goi-y", { ngay_gio: ngayGio });
    if (res.data && res.data.status && res.data.data?.goi_y) {
      suggestedDoctor.value = res.data.data.goi_y;
      selectedDoctor.value = suggestedDoctor.value.id;
    }
  } catch (error) {
    console.error("Failed to fetch doctor suggestion:", error);
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

watch(
  () => quickCreateData.value.loai_thu_cung,
  () => {
    quickCreateData.value.loai_thu_cung_khac = "";
    quickCreateData.value.giong_thu_cung = "";
  }
);

watch(
  () => quickCreateData.value.loai_thu_cung_khac,
  () => {
    if (isOtherSpecies.value) {
      quickCreateData.value.giong_thu_cung = "";
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
  suggestedDoctor.value = null;
  showQuickCreate.value = false;
  justCreatedCustomer.value = null;
  quickCreateData.value = {
    full_name: "",
    phone: "",
    email: "",
    ten_thu_cung: "",
    loai_thu_cung: "cho",
    loai_thu_cung_khac: "",
    giong_thu_cung: "",
  };
  errorMessage.value = "";
  submitting.value = false;
  creatingProfile.value = false;
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
  showQuickCreate.value = false;

  customerPets.value = customer.thu_cung || customer.thuCungs || [];
  if (customerPets.value.length === 1) {
    selectedPetId.value = customerPets.value[0].id;
  } else {
    selectedPetId.value = "";
  }

  fetchDoctorSuggestion();
};

const clearCustomer = () => {
  selectedCustomer.value = null;
  justCreatedCustomer.value = null;
  customerPets.value = [];
  selectedPetId.value = "";
  searchQuery.value = "";
  selectedServiceIds.value = [];
  selectedDoctor.value = "";
  suggestedDoctor.value = null;
};

const cancelQuickCreate = () => {
  showQuickCreate.value = false;
  quickCreateData.value = {
    full_name: "",
    phone: "",
    email: "",
    ten_thu_cung: "",
    loai_thu_cung: "cho",
    loai_thu_cung_khac: "",
    giong_thu_cung: "",
  };
};

const handleQuickCreate = async () => {
  errorMessage.value = "";

  if (
    !quickCreateData.value.full_name ||
    !quickCreateData.value.phone ||
    !quickCreateData.value.ten_thu_cung ||
    (isOtherSpecies.value && !quickCreateData.value.loai_thu_cung_khac) ||
    !quickCreateData.value.giong_thu_cung
  ) {
    errorMessage.value = "Vui lòng điền đầy đủ thông tin bắt buộc (*)";
    return;
  }

  creatingProfile.value = true;
  try {
    const payload = {
      full_name: quickCreateData.value.full_name,
      phone: quickCreateData.value.phone,
      email: quickCreateData.value.email || null,
      ten_thu_cung: quickCreateData.value.ten_thu_cung,
      loai_thu_cung: effectiveSpeciesLabel.value,
      giong_thu_cung: quickCreateData.value.giong_thu_cung,
    };

    const res = await api.post("/khach-hang/staff-create", payload);
    if (res.data && res.data.status) {
      const newCustomer = res.data.data.customer;
      const newPet = res.data.data.pet;

      justCreatedCustomer.value = {
        id: newCustomer.id,
        full_name: newCustomer.full_name,
        so_dien_thoai: newCustomer.phone,
        email: newCustomer.email,
      };

      selectedCustomer.value = justCreatedCustomer.value;
      customerPets.value = [newPet];
      selectedPetId.value = newPet.id;
      searchQuery.value = newCustomer.full_name;
      searchResults.value = [];
      showSearchResults.value = false;
      errorMessage.value = "";

      showQuickCreate.value = false;
      fetchDoctorSuggestion();
    }
  } catch (error) {
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      errorMessage.value = Object.values(errors).flat().join(". ");
    } else {
      errorMessage.value = error.response?.data?.message || "Có lỗi xảy ra khi tạo hồ sơ";
    }
  } finally {
    creatingProfile.value = false;
  }
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

  const customer = selectedCustomer.value || justCreatedCustomer.value;
  if (!customer) {
    errorMessage.value = "Vui lòng tìm hoặc tạo hồ sơ khách hàng";
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
    const ngayGio = formatLocalDateTime();

    const payload = {
      khach_hang_id: customer.id,
      thu_cung_id: selectedPetId.value,
      dich_vu_ids: selectedServiceIds.value,
      nhan_vien_id: selectedDoctor.value,
      ngay_gio: ngayGio,
      nguon_goc: "walk-in",
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
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700;800&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}
</style>
