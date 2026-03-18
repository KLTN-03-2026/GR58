<template>
  <div class="relative w-full h-full px-8 py-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-1 mb-6">
      <div class="flex items-center gap-2">
        <!-- <img :src="icons.microscope" alt="" class="w-8 h-8" /> -->
        <h1 class="text-2xl font-semibold text-black">Cận lâm sàng</h1>
      </div>
      <p class="text-base font-medium text-gray-500">
        Danh sách chờ thực hiện chẩn đoán hình ảnh & xét nghiệm
      </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-3 gap-4 mb-6">
      <!-- Card 1: Chờ thực hiện -->
      <div
        class="bg-white border !border-gray-300 rounded-[14px] px-6 py-6 shadow-sm"
      >
        <div class="flex items-center justify-between">
          <div class="flex flex-col">
            <p class="text-sm font-normal text-gray-600 mb-1">Chờ thực hiện</p>
            <p class="text-3xl font-semibold text-[#d08700]">
              {{ stats.pending }}
            </p>
          </div>
          <!-- <div class="w-12 h-12 bg-[#fef9c2] rounded-full flex items-center justify-center">
            <img :src="icons.clock" alt="" class="w-6 h-6" />
          </div> -->
        </div>
      </div>

      <!-- Card 2: Đã hoàn tất hôm nay -->
      <div
        class="bg-white border !border-gray-300 rounded-[14px] px-6 py-6 shadow-sm"
      >
        <div class="flex items-center justify-between">
          <div class="flex flex-col">
            <p class="text-sm font-normal text-gray-600 mb-1">
              Đã hoàn tất hôm nay
            </p>
            <p class="text-3xl font-semibold text-[#00a63e]">
              {{ stats.completedToday }}
            </p>
          </div>
          <!-- <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
            <img :src="icons.checkCircle" alt="" class="w-6 h-6" />
          </div> -->
        </div>
      </div>

      <!-- Card 3: Tổng hôm nay -->
      <div
        class="bg-white border !border-gray-300 rounded-[14px] px-6 py-6 shadow-sm"
      >
        <div class="flex items-center justify-between">
          <div class="flex flex-col">
            <p class="text-sm font-normal text-gray-600 mb-1">Tổng hôm nay</p>
            <p class="text-3xl font-semibold text-[#155dfc]">
              {{ stats.total }}
            </p>
          </div>
          <!-- <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
            <img :src="icons.clipboard" alt="" class="w-6 h-6" />
          </div> -->
        </div>
      </div>
    </div>

    <!-- Search Bar -->
    <div
      class="bg-white border !border-gray-300 rounded-[14px] px-6 py-4 flex items-center mb-6 shadow-sm"
    >
      <div class="flex items-center justify-between w-full">
        <!-- Search Input -->
        <div class="relative flex-1 mr-3">
          <!-- <img :src="icons.search" alt="" class="absolute left-3 top-[10px] w-4 h-4" /> -->
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Tìm theo tên bệnh nhân hoặc mã HSBA..."
            class="w-full h-10 bg-gray-50 border !border-gray-300 rounded-lg pl-4 pr-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
          />
        </div>

        <!-- Service Filter Dropdown -->
        <button
          class="h-10 px-4 bg-gray-50 border !border-gray-300 rounded-lg flex items-center justify-between gap-2 hover:bg-gray-100 transition-colors min-w-[192px]"
        >
          <span class="text-sm font-normal text-gray-900">
            Tất cả dịch vụ
          </span>
          <span class="text-gray-500">▼</span>
        </button>
      </div>
    </div>

    <!-- Pending Table -->
    <div
      class="bg-white border !border-gray-300 rounded-[14px] px-6 py-6 mb-6 shadow-sm"
    >
      <div class="flex items-center gap-2 mb-6">
        <!-- <img :src="icons.clockOrange" alt="" class="w-5 h-5" /> -->
        <h2 class="text-base font-semibold text-black">
          Đang chờ thực hiện ({{ pendingRecords.length }})
        </h2>
      </div>

      <div class="overflow-hidden">
        <table class="w-full">
          <thead>
            <tr class="border-b border-[rgba(0,0,0,0.1)]">
              <th
                class="text-left py-[10.25px] px-2 text-[14px] font-medium text-neutral-950 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Mã HSBA
              </th>
              <th
                class="text-left py-[10.25px] px-2 text-[14px] font-medium text-neutral-950 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Bệnh nhân
              </th>
              <th
                class="text-left py-[10.25px] px-2 text-[14px] font-medium text-neutral-950 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Dịch vụ
              </th>
              <th
                class="text-left py-[10.25px] px-2 text-[14px] font-medium text-neutral-950 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                BS chỉ định
              </th>
              <th
                class="text-left py-[10.25px] px-2 text-[14px] font-medium text-neutral-950 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Thời gian
              </th>
              <th
                class="text-left py-[10.25px] px-2 text-[14px] font-medium text-neutral-950 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Trạng thái
              </th>
              <th
                class="text-right py-[10.25px] px-2 text-[14px] font-medium text-neutral-950 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Thao tác
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="record in pendingRecords"
              :key="record.id"
              class="border-b !border-gray-300"
            >
              <td class="py-4 px-2">
                <span class="text-sm font-normal text-blue-600">
                  {{ record.code }}
                </span>
              </td>
              <td class="py-4 px-2">
                <div class="flex flex-col">
                  <span class="text-sm font-normal text-black">
                    {{ record.patientName }}
                  </span>
                  <span class="text-sm font-normal text-gray-500">
                    {{ record.patientBreed }}
                  </span>
                </div>
              </td>
              <td class="py-4 px-2">
                <span class="text-sm font-normal text-black">
                  {{ record.service }}
                </span>
              </td>
              <td class="py-4 px-2">
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.doctor" alt="" class="w-4 h-4" /> -->
                  <span class="text-sm font-normal text-gray-700">
                    {{ record.doctor }}
                  </span>
                </div>
              </td>
              <td class="py-4 px-2">
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.clockSmall" alt="" class="w-4 h-4" /> -->
                  <span class="text-sm font-normal text-gray-700">
                    {{ record.time }}
                  </span>
                </div>
              </td>
              <td class="py-4 px-2">
                <div
                  class="bg-[#fef9c2] border !border-[#fef9c2] rounded-lg px-2 py-1 inline-flex"
                >
                  <span class="text-xs font-medium text-[#a65f00]">
                    Chờ thực hiện
                  </span>
                </div>
              </td>
              <td class="py-4 px-2 text-right">
                <button
                  @click="openUploadResultModal(record)"
                  class="h-9 px-4 bg-[#155dfc] text-white text-sm font-medium rounded-lg flex items-center gap-2 hover:bg-[#1447e6] transition-colors ml-auto"
                >
                  <!-- <img :src="icons.edit" alt="" class="w-4 h-4" /> -->
                  Nhập kết quả
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Completed Table -->
    <div
      class="bg-white border !border-gray-300 rounded-[14px] px-[25px] py-[25px]"
    >
      <div class="flex items-center gap-2 mb-[30px]">
        <!-- <img :src="icons.checkGreen" alt="" class="w-5 h-5" /> -->
        <h2
          class="text-[16px] font-normal leading-4 text-neutral-950 tracking-[-0.3125px]"
          style="font-family: 'Inter', sans-serif"
        >
          Đã hoàn tất ({{ completedRecords.length }})
        </h2>
      </div>

      <div class="overflow-hidden">
        <table class="w-full">
          <thead>
            <tr class="border-b border-[rgba(0,0,0,0.1)]">
              <th
                class="text-left py-[10.25px] px-2 text-[14px] font-medium text-neutral-950 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Mã HSBA
              </th>
              <th
                class="text-left py-[10.25px] px-2 text-[14px] font-medium text-neutral-950 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Bệnh nhân
              </th>
              <th
                class="text-left py-[10.25px] px-2 text-[14px] font-medium text-neutral-950 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Dịch vụ
              </th>
              <th
                class="text-left py-[10.25px] px-2 text-[14px] font-medium text-neutral-950 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                BS chỉ định
              </th>
              <th
                class="text-left py-[10.25px] px-2 text-[14px] font-medium text-neutral-950 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Thời gian
              </th>
              <th
                class="text-left py-[10.25px] px-2 text-[14px] font-medium text-neutral-950 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Trạng thái
              </th>
              <th
                class="text-right py-[10.25px] px-2 text-[14px] font-medium text-neutral-950 tracking-[-0.1504px]"
                style="font-family: 'Inter', sans-serif"
              >
                Thao tác
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="record in completedRecords"
              :key="record.id"
              class="border-b border-[rgba(0,0,0,0.1)] last:border-b-0"
            >
              <td class="py-5 px-2">
                <span
                  class="text-[14px] font-normal text-[#155dfc] tracking-[-0.1504px]"
                  style="font-family: 'Inter', sans-serif"
                >
                  {{ record.code }}
                </span>
              </td>
              <td class="py-2 px-2">
                <div class="flex flex-col">
                  <span
                    class="text-[14px] font-normal text-[#101828] tracking-[-0.1504px]"
                    style="font-family: 'Inter', sans-serif"
                  >
                    {{ record.patientName }}
                  </span>
                  <span
                    class="text-[14px] font-normal text-[#6a7282] tracking-[-0.1504px]"
                    style="font-family: 'Inter', sans-serif"
                  >
                    {{ record.patientBreed }}
                  </span>
                </div>
              </td>
              <td class="py-5 px-2">
                <span
                  class="text-[14px] font-normal text-[#101828] tracking-[-0.1504px]"
                  style="font-family: 'Inter', sans-serif"
                >
                  {{ record.service }}
                </span>
              </td>
              <td class="py-5 px-2">
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.doctor" alt="" class="w-4 h-4" /> -->
                  <span
                    class="text-[14px] font-normal text-[#364153] tracking-[-0.1504px]"
                    style="font-family: 'Inter', sans-serif"
                  >
                    {{ record.doctor }}
                  </span>
                </div>
              </td>
              <td class="py-5 px-2">
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.clockSmall" alt="" class="w-4 h-4" /> -->
                  <span
                    class="text-[14px] font-normal text-[#364153] tracking-[-0.1504px]"
                    style="font-family: 'Inter', sans-serif"
                  >
                    {{ record.time }}
                  </span>
                </div>
              </td>
              <td class="py-5 px-2">
                <div
                  class="bg-green-100 border !border-transparent rounded-lg px-2 py-[3px] inline-flex overflow-hidden"
                >
                  <span
                    class="text-[12px] font-medium leading-4 text-[#008236]"
                    style="font-family: 'Inter', sans-serif"
                  >
                    Đã hoàn tất
                  </span>
                </div>
              </td>
              <td class="py-3 px-2 text-right">
                <button
                  @click="openViewResultModal(record)"
                  class="h-8 px-3 bg-white border !border-gray-300 text-neutral-950 text-[14px] font-medium leading-5 tracking-[-0.1504px] rounded-lg hover:bg-gray-50 transition-colors ml-auto"
                  style="font-family: 'Inter', sans-serif"
                >
                  Xem kết quả
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Ket Qua Sieu Am Modal -->
    <KetQuaCanLamSang
      v-if="isResultModalOpen"
      :mode="resultModalMode"
      :record-id="selectedRecord?.code"
      :result-data="viewResultData"
      @close="closeResultModal"
      @save="handleSaveResult"
    />
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import KetQuaCanLamSang from "./KetQuaCanLamSang/index.vue";

// Modal state
const isResultModalOpen = ref(false);
const resultModalMode = ref("upload");
const selectedRecord = ref(null);

// Computed property for view mode data
const viewResultData = computed(() => {
  if (!selectedRecord.value || resultModalMode.value !== "view") {
    return undefined;
  }

  return {
    petImage:
      "https://www.figma.com/api/mcp/asset/e67eace5-4e3d-4730-901c-05b98d42a814",
    petName: selectedRecord.value.patientName,
    petSpecies: selectedRecord.value.patientBreed,
    petAge: "2 tuổi",
    petWeight: "28kg",
    ownerName: "Nguyễn Văn A",
    ownerPhone: "0901.234.567",
    images: [
      {
        url: "https://www.figma.com/api/mcp/asset/f01361b0-2d8c-43d7-bb5a-199a6e6fb476",
        name: "Ultrasound 1",
      },
      {
        url: "https://www.figma.com/api/mcp/asset/262a170f-695f-41f4-9fbd-ce1f709b4d6d",
        name: "Ultrasound 2",
      },
    ],
    imageCount: 2,
    performedBy: selectedRecord.value.doctor,
    time: selectedRecord.value.time,
    conclusion: "Thai kỳ ổn định, 2 bào thai",
  };
});

const openUploadResultModal = (record) => {
  selectedRecord.value = record;
  resultModalMode.value = "upload";
  isResultModalOpen.value = true;
};

const openViewResultModal = (record) => {
  selectedRecord.value = record;
  resultModalMode.value = "view";
  isResultModalOpen.value = true;
};

const closeResultModal = () => {
  isResultModalOpen.value = false;
  selectedRecord.value = null;
};

const handleSaveResult = (data) => {
  console.log("Saved result:", data);
  // TODO: Update record status and move to completed list
  closeResultModal();
};

// Icons from Figma
const icons = {
  microscope:
    "https://www.figma.com/api/mcp/asset/2a2d3dcb-02cf-4fac-8821-c09b433f5c45",
  clock:
    "https://www.figma.com/api/mcp/asset/4b00e762-5ad0-4e79-9a2a-b25f86fe42e0",
  checkCircle:
    "https://www.figma.com/api/mcp/asset/67329052-f00b-4597-ac09-de0949f4f65b",
  clipboard:
    "https://www.figma.com/api/mcp/asset/31bac90d-f408-46b5-bad3-defcedc04fb0",
  chevronDown:
    "https://www.figma.com/api/mcp/asset/121c0193-8761-4670-b537-383a48795e2c",
  search:
    "https://www.figma.com/api/mcp/asset/79de1f7f-f8e8-429f-a721-e2d29052a9cf",
  clockOrange:
    "https://www.figma.com/api/mcp/asset/7edd396b-a0b9-4007-a745-c207000f7d0e",
  doctor:
    "https://www.figma.com/api/mcp/asset/abcf1fb0-b8ec-4094-bb45-342878fe2dfb",
  clockSmall:
    "https://www.figma.com/api/mcp/asset/51465be5-bcee-4486-9eb7-52110e1053e6",
  edit: "https://www.figma.com/api/mcp/asset/eca51fa4-6e21-4f65-babd-a4fc374647be",
  checkGreen:
    "https://www.figma.com/api/mcp/asset/d2ca21dc-5376-47eb-944d-72fcb76def1e",
};

// Search query
const searchQuery = ref("");

// Statistics
const stats = ref({
  pending: 3,
  completedToday: 1,
  total: 4,
});

// Pending records
const pendingRecords = ref([
  {
    id: 1,
    code: "HSBA-001",
    patientName: "Milo",
    patientBreed: "Chó Golden Retriever",
    service: "Siêu âm",
    doctor: "BS. Nguyễn Văn A",
    time: "09:30 hôm nay",
  },
  {
    id: 2,
    code: "HSBA-002",
    patientName: "Lucy",
    patientBreed: "Mèo Ba Tư",
    service: "X-Quang",
    doctor: "BS. Trần Thu B",
    time: "09:45 hôm nay",
  },
  {
    id: 3,
    code: "HSBA-003",
    patientName: "Max",
    patientBreed: "Chó Poodle",
    service: "Xét nghiệm máu",
    doctor: "BS. Nguyễn Văn A",
    time: "10:00 hôm nay",
  },
]);

// Completed records
const completedRecords = ref([
  {
    id: 4,
    code: "HSBA-004",
    patientName: "Bella",
    patientBreed: "Mèo Anh lông ngắn",
    service: "Siêu âm",
    doctor: "BS. Lê Văn C",
    time: "08:30 hôm nay",
  },
  {
    id: 5,
    code: "HSBA-005",
    patientName: "Rocky",
    patientBreed: "Chó Husky",
    service: "X-Quang",
    doctor: "BS. Nguyễn Văn A",
    time: "Hôm qua",
  },
]);
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap");

/* Table styling */
table {
  border-collapse: collapse;
}

th {
  font-weight: 500;
}

td,
th {
  vertical-align: middle;
}
</style>
