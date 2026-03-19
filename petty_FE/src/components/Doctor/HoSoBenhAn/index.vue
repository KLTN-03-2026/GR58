<template>
  <div class="flex flex-col gap-6 w-full h-full px-8 py-6">
    <!-- Header Section -->
    <div class="flex flex-col gap-1">
      <h1 class="text-2xl font-semibold text-black">Quản lý Hồ sơ bệnh án</h1>
      <p class="text-base font-medium text-gray-500">
        Tra cứu lịch sử khám bệnh và thông tin khách hàng
      </p>
    </div>

    <!-- Search & Filter Card -->
    <div
      class="bg-white border !border-gray-300 rounded-[14px] px-6 py-6 shadow-sm"
    >
      <div class="flex items-start justify-between gap-3">
        <!-- Search Input -->
        <div class="relative flex-1">
          <div class="absolute left-4 top-[14px] w-5 h-5">
            <SearchIcon />
          </div>
          <input
            v-model="searchQuery"
            @input="onSearchInput"
            type="text"
            placeholder="Nhập SĐT, Tên chủ hoặc Tên thú cưng để tra cứu..."
            class="w-full h-12 bg-[#f3f3f5] border-0 rounded-lg pl-12 pr-3 py-1 text-sm text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#155dfc]"
          />
        </div>

        <!-- Filter Buttons -->
        <div class="flex gap-2">
          <button
            @click="onFilterChange('all')"
            :class="[
              'h-9 px-3 rounded-lg flex items-center gap-2',
              selectedFilter === 'all'
                ? 'bg-[#155dfc] text-white'
                : 'bg-white border !border-gray-300 text-gray-900',
            ]"
          >
            <span class="text-sm font-medium">Tất cả</span>
          </button>
          <button
            @click="onFilterChange('member')"
            :class="[
              'h-9 px-3 rounded-lg flex items-center gap-2',
              selectedFilter === 'member'
                ? 'bg-[#155dfc] text-white'
                : 'bg-white border !border-gray-300 text-gray-900',
            ]"
          >
            <span class="text-sm font-medium">Member</span>
          </button>
          <button
            @click="onFilterChange('vanglai')"
            :class="[
              'h-9 px-3 rounded-lg flex items-center gap-2',
              selectedFilter === 'vanglai'
                ? 'bg-[#155dfc] text-white'
                : 'bg-white border !border-gray-300 text-gray-900',
            ]"
          >
            <span class="text-sm font-medium">Vãng lai</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="flex justify-center py-12">
      <p class="text-gray-400">Đang tải...</p>
    </div>

    <!-- Empty state -->
    <div v-else-if="filteredCustomers.length === 0" class="bg-white border !border-gray-300 rounded-[14px] shadow-sm p-12 text-center">
      <p class="text-gray-400">Chưa có hồ sơ bệnh án nào.</p>
    </div>

    <!-- Patient Records List -->
    <div v-else class="flex flex-col gap-4">
      <!-- Customer Card -->
      <div
        v-for="customer in filteredCustomers"
        :key="customer.id"
        class="bg-white border !border-gray-300 rounded-[14px] shadow-sm"
      >
        <!-- Customer Header -->
        <div class="border-b !border-gray-300 px-6 pt-6 pb-6">
          <div class="flex items-center justify-between">
            <!-- Customer Info -->
            <div class="flex items-center gap-3">
              <!-- <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <img :src="icons.user" alt="" class="w-5 h-5" />
              </div> -->
              <div class="flex flex-col">
                <p
                  class="text-base font-normal text-neutral-950 leading-6 tracking-[-0.3125px]"
                >
                  {{ customer.name }}
                </p>
                <div class="flex items-center gap-1">
                  <!-- <img :src="icons.phone" alt="" class="w-3 h-3" /> -->
                  <span class="text-sm font-normal text-gray-500">
                    {{ customer.phone }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Badge -->
            <div
              :class="[
                'px-3 py-1 rounded-lg border flex items-center gap-2',
                customer.type === 'member'
                  ? 'bg-blue-100 !border-[#bedbff]'
                  : 'bg-gray-100 !border-gray-200',
              ]"
            >
              <!-- <img
                :src="
                  customer.type === 'member'
                    ? icons.memberBadge
                    : icons.vanglaiBadge
                "
                alt=""
                class="w-3 h-3"
              /> -->
              <span
                :class="[
                  'text-xs font-medium leading-4',
                  customer.type === 'member'
                    ? 'text-[#1447e6]'
                    : 'text-[#364153]',
                ]"
              >
                {{ customer.type === "member" ? "Member" : "Vãng lai" }}
              </span>
            </div>
          </div>
        </div>

        <!-- Pets List -->
        <div class="px-6 py-10 flex flex-col gap-3">
          <div
            v-for="(pet, petIndex) in customer.pets"
            :key="`pet-${customer.id}-${petIndex}`"
            class="bg-gray-50 border !border-gray-300 rounded-lg p-4 flex items-center gap-4"
          >
            <!-- Pet Image -->
            <div
              class="w-20 h-20 rounded-[10px] border-2 border-white shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1),0px_1px_2px_-1px_rgba(0,0,0,0.1)] overflow-hidden flex-shrink-0"
            >
              <img :src="pet.image" alt="" class="w-full h-full object-cover" />
            </div>

            <!-- Pet Info -->
            <div class="flex-1 flex flex-col gap-2">
              <!-- Name & Species -->
              <div class="flex items-center gap-2 h-6">
                <span
                  class="text-base font-bold text-[#101828] leading-6 tracking-[-0.3125px]"
                >
                  {{ pet.name }}
                </span>
                <span
                  class="text-sm font-normal text-[#4a5565] leading-5 tracking-[-0.1504px]"
                >
                  ({{ pet.species }})
                </span>
              </div>

              <!-- Last Visit -->
              <div class="flex items-center gap-2">
                <span class="text-sm font-normal text-gray-500">
                  Lần khám cuối:
                </span>
                <span
                  class="text-sm font-bold text-[#4a5565] leading-5 tracking-[-0.1504px]"
                >
                  {{ pet.lastVisit }}
                </span>
                <span
                  class="text-sm font-normal text-[#4a5565] leading-5 tracking-[-0.1504px]"
                >
                  - Chẩn đoán: {{ pet.lastDiagnosis }}
                </span>
              </div>

              <!-- Pet Details -->
              <p class="text-xs font-normal text-[#6a7282] leading-4">
                {{ pet.age }} • {{ pet.gender }} • {{ pet.weight }}
              </p>
              <!-- Tổng số lần khám -->
              <p v-if="pet.totalExams" class="text-xs text-[#009689] font-medium">
                {{ pet.totalExams }} lần khám
              </p>
            </div>

            <!-- View Button -->
            <button
              @click="viewRecord(customer.id, pet.id || petIndex)"
              class="bg-[#155dfc] hover:bg-[#0d47c9] transition-colors rounded-lg px-3 py-[6px] flex items-center gap-3 flex-shrink-0"
            >
              <!-- <img :src="icons.eye" alt="" class="w-4 h-4" /> -->
              <span
                class="text-sm font-medium text-white leading-5 tracking-[-0.1504px]"
              >
                Xem Hồ sơ
              </span>
            </button>

            <!-- Expand Exam Records Button -->
            <!-- <button
              @click="togglePetDetail(`pet-${customer.id}-${petIndex}`)"
              :class="[
                'rounded-lg px-3 py-[6px] flex items-center gap-3 flex-shrink-0 transition-colors',
                expandedPets.has(`pet-${customer.id}-${petIndex}`)
                  ? 'bg-gray-200 text-gray-700'
                  : 'bg-gray-100 hover:bg-gray-200 text-gray-600',
              ]"
            >
              <span class="text-sm font-medium leading-5 tracking-[-0.1504px]">
                {{
                  expandedPets.has(`pet-${customer.id}-${petIndex}`)
                    ? "▼ Ẩn"
                    : "▶ Chi tiết"
                }}
              </span>
            </button> -->
          </div>

          <!-- Exam Records Section (Expandable) -->
          <div
            v-if="
              expandedPets.has(`pet-${customer.id}-${petIndex}`) &&
              getPhieuKhamForPet(pet.id || petIndex).length > 0
            "
            class="border-t border-gray-200 mt-4 pt-4 px-6"
          >
            <h4 class="text-sm font-bold text-gray-700 mb-3">
              Lịch sử khám bệnh
            </h4>
            <div class="space-y-3">
              <div
                v-if="getPhieuKhamForPet(pet.id || petIndex).length === 0"
                class="text-center py-4"
              >
                <p class="text-sm text-gray-500">Chưa có phiếu khám nào</p>
              </div>
              <div
                v-for="exam in getPhieuKhamForPet(pet.id || petIndex)"
                :key="exam.id"
                class="bg-white border border-gray-200 rounded-[8px] p-3"
              >
                <!-- Exam Header -->
                <div class="flex items-start justify-between mb-2">
                  <div class="flex-1">
                    <p class="text-xs font-bold text-gray-600 leading-4">
                      {{ formatDateTime(exam.created_at || exam.ngay_kham) }}
                    </p>
                    <p class="text-xs text-gray-500 leading-4">
                      Bác sĩ:
                      {{ exam.nhan_vien?.ten || exam.nhanVien?.ten || "N/A" }}
                    </p>
                  </div>
                  <div
                    :class="[
                      'px-2 py-1 rounded text-xs font-medium',
                      exam.loai_chi_dinh === 'chi_dinh_can_lam_sang'
                        ? 'bg-purple-100 text-purple-700'
                        : exam.loai_chi_dinh === 'don_thuoc'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-cyan-100 text-cyan-700',
                    ]"
                  >
                    {{
                      exam.loai_chi_dinh === "chi_dinh_can_lam_sang"
                        ? "Cận lâm sàng"
                        : exam.loai_chi_dinh === "don_thuoc"
                        ? "Đơn thuốc"
                        : "Tái khám"
                    }}
                  </div>
                </div>

                <!-- Vital Signs -->
                <div
                  v-if="
                    exam.nhiet_do ||
                    exam.can_nang ||
                    exam.nhip_tim ||
                    exam.nhip_tho
                  "
                  class="mb-2"
                >
                  <p class="text-xs font-semibold text-gray-600 mb-1">
                    Dấu hiệu sinh tồn:
                  </p>
                  <div class="flex gap-2 flex-wrap">
                    <span
                      v-if="exam.nhiet_do"
                      class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded"
                    >
                      Nhiệt độ: {{ exam.nhiet_do }}°C
                    </span>
                    <span
                      v-if="exam.can_nang"
                      class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded"
                    >
                      Cân nặng: {{ exam.can_nang }} kg
                    </span>
                    <span
                      v-if="exam.nhip_tim"
                      class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded"
                    >
                      Nhịp tim: {{ exam.nhip_tim }} bpm
                    </span>
                    <span
                      v-if="exam.nhip_tho"
                      class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded"
                    >
                      Nhịp thở: {{ exam.nhip_tho }}/phút
                    </span>
                  </div>
                </div>

                <!-- Medical Info -->
                <div v-if="exam.ly_do_den_kham" class="mb-2">
                  <p class="text-xs font-semibold text-gray-600">
                    Lý do đến khám:
                  </p>
                  <p class="text-xs text-gray-700">{{ exam.ly_do_den_kham }}</p>
                </div>

                <div v-if="exam.trieu_chung" class="mb-2">
                  <p class="text-xs font-semibold text-gray-600">
                    Triệu chứng:
                  </p>
                  <p class="text-xs text-gray-700">{{ exam.trieu_chung }}</p>
                </div>

                <div v-if="exam.chan_doan" class="mb-2">
                  <p class="text-xs font-semibold text-gray-600">Chẩn đoán:</p>
                  <p class="text-xs text-gray-700 font-medium text-blue-700">
                    {{ exam.chan_doan }}
                  </p>
                </div>

                <div v-if="exam.ghi_chu" class="mb-2">
                  <p class="text-xs font-semibold text-gray-600">Ghi chú:</p>
                  <p class="text-xs text-gray-700">{{ exam.ghi_chu }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "@/utils/api";
import { showErrorToast } from "@/utils/toast";
import { format } from "date-fns";
import { vi } from "date-fns/locale";
//Icon SVG
import SearchIcon from "@/assets/svg/search.svg";
const router = useRouter();

// Icons
const icons = {
  search:
    "https://www.figma.com/api/mcp/asset/18b3a6b9-ef14-4bd4-9891-6c33221bd520",
  filterWhite:
    "https://www.figma.com/api/mcp/asset/975757c0-9d30-427f-b8bc-cabfcc0a34b3",
  filterBlack:
    "https://www.figma.com/api/mcp/asset/975757c0-9d30-427f-b8bc-cabfcc0a34b3",
  memberWhite:
    "https://www.figma.com/api/mcp/asset/04a28a11-a2d5-44e8-9582-f0328ceb42a1",
  memberBlack:
    "https://www.figma.com/api/mcp/asset/04a28a11-a2d5-44e8-9582-f0328ceb42a1",
  vanglaiWhite:
    "https://www.figma.com/api/mcp/asset/f3462224-5618-4e0d-b3a0-80ce1c345e2d",
  vanglaiBlack:
    "https://www.figma.com/api/mcp/asset/f3462224-5618-4e0d-b3a0-80ce1c345e2d",
  user: "https://www.figma.com/api/mcp/asset/ffdf60ec-2493-42b6-bb4c-196a150deb61",
  phone:
    "https://www.figma.com/api/mcp/asset/0c829708-4e7b-4320-ac9f-ce323fdefadc",
  memberBadge:
    "https://www.figma.com/api/mcp/asset/1eb89c2f-dade-486f-988e-8893bfb9da1a",
  vanglaiBadge:
    "https://www.figma.com/api/mcp/asset/3ff3b015-4003-4406-aa33-bf59b5e29503",
  clock:
    "https://www.figma.com/api/mcp/asset/b18f33dc-1063-4480-9ca3-ee54c9faab38",
  eye: "https://www.figma.com/api/mcp/asset/019de0d1-2c82-4ecf-aa57-4f9b208c044d",
};

// State
const searchQuery = ref("");
const selectedFilter = ref("all");
const customers = ref([]);
const loading = ref(false);
const expandedPets = ref(new Set());

// Load danh sách hồ sơ bệnh án từ API
const loadHoSoBenhAn = async () => {
  loading.value = true;
  try {
    const response = await api.get("/ho-so-benh-an", {
      params: {
        search: searchQuery.value || undefined,
        type: selectedFilter.value !== "all" ? selectedFilter.value : undefined,
      },
    });
    if (response.data.success) {
      customers.value = response.data.data || [];
    }
  } catch (error) {
    console.error("Error loading hồ sơ bệnh án:", error);
    showErrorToast("Lỗi khi tải danh sách hồ sơ bệnh án");
  } finally {
    loading.value = false;
  }
};

/* FAKE DATA - commented out, now loading from API
const FAKE_customers = [
  {
    id: 1,
    name: "Nguyễn Văn A",
    phone: "0901234567",
    type: "member",
    pets: [
      {
        id: 1,
        name: "Milo",
        species: "Chó Golden Retriever",
        image:
          "https://www.figma.com/api/mcp/asset/800b99a2-25e2-4122-b7c9-7f659b22befc",
        lastVisit: "3 ngày trước",
        diagnosis: "Viêm da",
        age: "3 tuổi",
        gender: "Đực",
        weight: "28 kg",
      },
      {
        id: 2,
        name: "Kitty",
        species: "Mèo Mướp",
        image:
          "https://www.figma.com/api/mcp/asset/8b49f52a-230f-4db5-bd4a-9dafe0c324a8",
        lastVisit: "2 tháng trước",
        diagnosis: "Tiêm phòng 3 bệnh",
        age: "2 tuổi",
        gender: "Cái",
        weight: "3.5 kg",
      },
    ],
  },
  {
    id: 2,
    name: "Trần Thị B",
    phone: "0912345678",
    type: "vanglai",
    pets: [
      {
        id: 3,
        name: "Mimi",
        species: "Mèo Mướp",
        image:
          "https://www.figma.com/api/mcp/asset/0c9926b8-75af-485d-9a9e-03313d5f87ca",
        lastVisit: "Hôm nay",
        diagnosis: "Tiêm phòng",
        age: "1 tuổi",
        gender: "Cái",
        weight: "3 kg",
      },
    ],
  },
  {
    id: 3,
    name: "Lê Văn C",
    phone: "0923456789",
    type: "member",
    pets: [
      {
        id: 4,
        name: "Bông",
        species: "Mèo Anh lông dài",
        image:
          "https://www.figma.com/api/mcp/asset/de5b3d83-fdf1-425e-a2b1-cd82f1ce26c6",
        lastVisit: "5 ngày trước",
        diagnosis: "Tẩy giun định kỳ",
        age: "4 tuổi",
        gender: "Đực",
        weight: "5 kg",
      },
    ],
  },
  {
    id: 4,
    name: "Phạm Thị D",
    phone: "0934567890",
    type: "vanglai",
    pets: [
      {
        id: 5,
        name: "Max",
        species: "Chó Husky",
        image:
          "https://www.figma.com/api/mcp/asset/500ba864-bf69-491e-97c8-81316dcba68a",
        lastVisit: "1 tuần trước",
        diagnosis: "Khám tổng quát",
        age: "2 tuổi",
        gender: "Đực",
        weight: "25 kg",
      },
    ],
  },
]);
*/

// Computed: lấy từ API đã filter rồi
const filteredCustomers = computed(() => customers.value);

// Debounce search
let searchTimer = null;
const onSearchInput = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => loadHoSoBenhAn(), 400);
};

// Filter theo type
const onFilterChange = (type) => {
  selectedFilter.value = type;
  loadHoSoBenhAn();
};

// Phiếu khám history đã nhúng trong customer.pets từ API
const getPhieuKhamForPet = (_petId) => [];



// Toggle pet detail expansion
const togglePetDetail = (petId) => {
  if (expandedPets.value.has(petId)) {
    expandedPets.value.delete(petId);
  } else {
    expandedPets.value.add(petId);
  }
};

// Format date time
const formatDateTime = (dateString) => {
  if (!dateString) return "-";
  try {
    return format(new Date(dateString), "dd/MM/yyyy HH:mm", { locale: vi });
  } catch (e) {
    return dateString;
  }
};

// Xem chi tiết hồ sơ - truyền thu_cung_id và khach_hang_id
const viewRecord = (customerId, petId) => {
  router.push({
    path: "/doctor/benh-an/chi-tiet",
    query: { thu_cung_id: petId, khach_hang_id: customerId },
  });
};

// Load data on mount
onMounted(() => {
  loadHoSoBenhAn();
});
</script>
