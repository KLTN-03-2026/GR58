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
          Đang chờ thực hiện ({{ filteredPendingRecords.length }})
        </h2>
      </div>

      <div v-if="loading" class="py-10 text-center text-gray-500">
        Đang tải danh sách cận lâm sàng...
      </div>
      <div v-else-if="error" class="py-10 text-center text-red-600">
        {{ error }}
      </div>
      <div v-else-if="filteredPendingRecords.length" class="overflow-hidden">
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
              v-for="record in filteredPendingRecords"
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
      <div v-else class="py-10 text-center text-gray-500">
        Không có chỉ định cận lâm sàng đang chờ.
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
          Đã hoàn tất ({{ filteredCompletedRecords.length }})
        </h2>
      </div>

      <div v-if="filteredCompletedRecords.length" class="overflow-hidden">
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
              v-for="record in filteredCompletedRecords"
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
      <div v-else class="py-10 text-center text-gray-500">
        Chưa có kết quả cận lâm sàng hoàn tất.
      </div>
    </div>

    <!-- Ket Qua Sieu Am Modal -->
    <ClinicalTestResult
      v-if="isResultModalOpen"
      :mode="resultModalMode"
      :record-id="selectedRecord?.code"
      :record-data="selectedRecord"
      :result-data="viewResultData"
      @close="closeResultModal"
      @save="handleSaveResult"
    />
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import api from "@/utils/api";
import ClinicalTestResult from "./clinical-test-result/index.vue";

// Modal state
const isResultModalOpen = ref(false);
const resultModalMode = ref("upload");
const selectedRecord = ref(null);
const loading = ref(false);
const error = ref(null);
const pendingRecords = ref([]);
const completedRecords = ref([]);
const searchQuery = ref("");

// Computed property for view mode data
const viewResultData = computed(() => {
  if (!selectedRecord.value || resultModalMode.value !== "view") {
    return undefined;
  }

  return buildResultData(selectedRecord.value);
});

const stats = computed(() => ({
  pending: pendingRecords.value.length,
  completedToday: completedRecords.value.filter((record) => record.isToday).length,
  total: pendingRecords.value.length + completedRecords.value.length,
}));

const filteredPendingRecords = computed(() => filterRecords(pendingRecords.value));
const filteredCompletedRecords = computed(() => filterRecords(completedRecords.value));

const defaultPetImage =
  "https://www.figma.com/api/mcp/asset/e67eace5-4e3d-4730-901c-05b98d42a814";

const formatDateTime = (value) => {
  if (!value) return "--:--";
  const date = new Date(String(value).replace(" ", "T"));
  if (Number.isNaN(date.getTime())) return "--:--";
  return date.toLocaleString("vi-VN", {
    hour: "2-digit",
    minute: "2-digit",
    day: "2-digit",
    month: "2-digit",
  });
};

const isSameDay = (value) => {
  const date = new Date(String(value).replace(" ", "T"));
  if (Number.isNaN(date.getTime())) return false;
  const now = new Date();
  return date.toDateString() === now.toDateString();
};

const getPet = (record) => record?.lich_hen?.thu_cung || {};
const getOwner = (record) => record?.lich_hen?.khach_hang || {};

const mapClinicalRecord = (record) => {
  const pet = getPet(record);
  const owner = getOwner(record);
  const files = record.tep_dinh_kem_can_lam_sang || [];

  return {
    id: record.id,
    code: `PK-${String(record.id).padStart(4, "0")}`,
    patientName: pet.ten_thu_cung || pet.ten || "Chưa có tên",
    patientBreed:
      pet.giong_thu_cung || pet.loai_thu_cung || pet.giong_loai || "Chưa rõ giống",
    service: "Cận lâm sàng",
    doctor: record.nhan_vien?.full_name || "Chưa xác định",
    time: formatDateTime(record.created_at),
    isToday: isSameDay(record.thoi_gian_tra_ket_qua || record.created_at),
    conclusion: record.ket_qua_can_lam_sang || "",
    files,
    raw: record,
    patientInfo: {
      image: pet.anh_dai_dien || pet.anh || defaultPetImage,
      name: pet.ten_thu_cung || pet.ten || "Chưa có tên",
      species: pet.giong_thu_cung || pet.loai_thu_cung || "Thú cưng",
      age: pet.tuoi_thu_cung || "Chưa rõ tuổi",
      weight: record.can_nang ? `${record.can_nang}kg` : "Chưa cân",
      ownerName: owner.full_name || owner.ho_ten || "Chưa có chủ nuôi",
      ownerPhone: owner.phone || owner.so_dien_thoai || "Chưa có SĐT",
    },
    orderInfo: {
      service: "Cận lâm sàng",
      note: record.ghi_chu || record.chan_doan || "Không có lưu ý",
    },
  };
};

const buildResultData = (record) => {
  const files = record.files || [];
  return {
    petImage: record.patientInfo?.image || defaultPetImage,
    petName: record.patientName,
    petSpecies: record.patientBreed,
    petAge: record.patientInfo?.age || "Chưa rõ tuổi",
    petWeight: record.patientInfo?.weight || "Chưa cân",
    ownerName: record.patientInfo?.ownerName || "Chưa có chủ nuôi",
    ownerPhone: record.patientInfo?.ownerPhone || "Chưa có SĐT",
    images: files.length ? files : [{ url: "", name: "Không có file" }],
    imageCount: files.length,
    performedBy: record.doctor,
    time: formatDateTime(record.raw?.thoi_gian_tra_ket_qua || record.raw?.updated_at),
    conclusion: record.conclusion || "Chưa có kết luận",
  };
};

const filterRecords = (records) => {
  const keyword = searchQuery.value.trim().toLowerCase();
  if (!keyword) return records;

  return records.filter((record) => {
    return [record.code, record.patientName, record.patientBreed, record.service]
      .filter(Boolean)
      .some((value) => value.toLowerCase().includes(keyword));
  });
};

const loadClinicalRecords = async () => {
  loading.value = true;
  error.value = null;
  try {
    const { data: res } = await api.get("/phieu-kham", {
      params: {
        loai_chi_dinh: "chi_dinh_can_lam_sang",
        per_page: 100,
      },
    });

    const records = (res.data || []).map(mapClinicalRecord);
    pendingRecords.value = records.filter((record) => !record.raw?.ket_qua_can_lam_sang);
    completedRecords.value = records.filter((record) => record.raw?.ket_qua_can_lam_sang);
  } catch (err) {
    console.error("Clinical testing load error:", err);
    error.value =
      err.response?.data?.message || "Không thể tải dữ liệu cận lâm sàng.";
  } finally {
    loading.value = false;
  }
};

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
  saveResult(data);
};

const saveResult = async (data) => {
  if (!selectedRecord.value?.id) return;

  try {
    const files = data.files.map((file) => ({
      name: file.name,
      url: file.preview || "",
      type: file.file?.type || "",
    }));

    await api.patch(`/phieu-kham/${selectedRecord.value.id}`, {
      ket_qua_can_lam_sang: data.conclusion || "Đã có kết quả",
      tep_dinh_kem_can_lam_sang: files,
      thoi_gian_tra_ket_qua: new Date().toISOString(),
    });

    await loadClinicalRecords();
    closeResultModal();
  } catch (err) {
    console.error("Clinical testing save error:", err);
    alert(err.response?.data?.message || "Không thể lưu kết quả cận lâm sàng.");
  }
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

onMounted(loadClinicalRecords);
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
