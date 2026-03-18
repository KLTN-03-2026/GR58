<template>
  <div class="w-full min-h-screen px-8 py-6">
    <!-- Page Header (only show in list view) -->
    <div
      v-if="!selectedPatient"
      class="flex items-center justify-between h-[60px] mb-6"
    >
      <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-black">Quản lý Hồ sơ Bệnh án</h1>
        <p class="text-gray-500 font-medium text-base">
          Tìm kiếm và quản lý thông tin bệnh nhân
        </p>
      </div>
    </div>

    <!-- Patient Detail View -->
    <ChiTietHoSoBA
      v-if="selectedPatient"
      :patient="selectedPatient"
      @back="selectedPatient = null"
    />

    <!-- Main Card (List View) -->
    <div
      v-else
      class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
    >
      <!-- Search and Filters -->
      <div class="flex flex-col gap-4 mt-6">
        <!-- Search Bar -->
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Tìm theo tên Pet, tên Chủ, SĐT hoặc Mã vi mạch..."
            class="bg-[#f3f3f5] border border-transparent rounded-lg h-12 pl-12 pr-3 w-full font-nunito text-sm text-[#717182] tracking-tight focus:outline-none focus:ring-2 focus:ring-[#009689]"
          />
          <SearchIcon class="absolute left-4 top-[14px] w-5 h-5" />
        </div>

        <!-- Filter Dropdowns -->
        <div class="grid grid-cols-2 gap-4">
          <!-- Species Filter -->
          <div class="relative">
            <button
              class="bg-[#f3f3f5] border !border-transparent rounded-lg h-9 px-[13px] py-px flex items-center justify-between w-full hover:bg-gray-200 transition-colors"
            >
              <span
                class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight"
              >
                🐾 Tất cả
              </span>
              <ChevronDownIcon />
            </button>
          </div>

          <!-- Breed Filter -->
          <div class="relative">
            <button
              class="bg-[#f3f3f5] border !border-transparent rounded-lg h-9 px-[13px] py-px flex items-center justify-between w-full opacity-50 hover:opacity-70 transition-opacity"
            >
              <span
                class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Tất cả giống
              </span>
              <ChevronDownIcon />
            </button>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-black/10">
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Bệnh nhân
              </th>
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Thông tin
              </th>
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Chủ nuôi
              </th>
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Lần khám cuối
              </th>
              <!-- <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Trạng thái
              </th> -->
              <th
                class="text-right px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Thao tác
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(patient, index) in patients"
              :key="index"
              class="border-b !border-gray-300 hover:bg-gray-50 transition-colors"
            >
              <!-- Patient -->
              <td class="px-2 py-2">
                <div class="flex items-center gap-3">
                  <!-- <img
                    :src="patient.image"
                    :alt="patient.name"
                    class="w-12 h-12 rounded-full object-cover"
                  /> -->
                  <p
                    class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                  >
                    {{ patient.name }}
                  </p>
                </div>
              </td>

              <!-- Info -->
              <td class="px-2 py-[12.5px]">
                <div class="flex flex-col gap-1">
                  <p
                    class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                  >
                    {{ patient.species }} {{ patient.breed }}
                  </p>
                  <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                    Tuổi: {{ patient.age }} tuổi
                  </p>
                </div>
              </td>

              <!-- Owner -->
              <td class="px-2 py-[10.5px]">
                <div class="flex flex-col">
                  <p
                    class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                  >
                    {{ patient.owner }}
                  </p>
                  <a
                    :href="'tel:' + patient.phone"
                    class="font-nunito text-xs leading-4 text-[#009689] hover:underline"
                  >
                    {{ patient.phone }}
                  </a>
                </div>
              </td>

              <!-- Last Visit -->
              <td class="px-2 py-[12.5px]">
                <div class="flex flex-col gap-1">
                  <div class="flex items-center gap-1">
                    <p
                      class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                    >
                      {{ patient.lastVisitDate }}
                    </p>
                    <p
                      class="font-nunito text-sm leading-5 text-[#6a7282] tracking-tight"
                    >
                      ({{ patient.lastVisitDaysAgo }})
                    </p>
                  </div>
                  <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                    Bởi: {{ patient.lastVisitDoctor }}
                  </p>
                </div>
              </td>

              <!-- Status -->
              <!-- <td class="px-2 py-[21.5px]">
                <span
                  :class="[
                    'inline-flex items-center px-2 py-[3px] rounded-lg font-nunito font-medium text-xs leading-4',
                    patient.status === 'normal'
                      ? 'bg-green-100 text-[#008236]'
                      : patient.status === 'treatment'
                      ? 'bg-[#ffe2e2] text-[#c10007]'
                      : 'bg-purple-100 text-[#8200db]',
                  ]"
                >
                  {{ getStatusLabel(patient.status) }}
                </span>
              </td> -->

              <!-- Actions -->
              <td class="px-2 py-[16.5px]">
                <div class="flex items-center justify-end">
                  <button
                    @click="openMedicalRecord(patient)"
                    class="bg-[#009689] rounded-lg h-8 px-[10px] flex items-center gap-1 hover:bg-[#007d72] transition-colors"
                  >
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-white tracking-tight"
                    >
                      Mở hồ sơ
                    </span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import ChiTietHoSoBA from "./ChiTietHoSoBA/index.vue";
//Icon SVG
import SearchIcon from "@/assets/svg/search.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";

// Emits
const emit = defineEmits(["open-record"]);

// Reactive state
const searchQuery = ref("");
const selectedPatient = ref(null);

// Sample Data
const patients = ref([
  {
    id: 1,
    name: "Milo",
    image:
      "https://www.figma.com/api/mcp/asset/338835fc-d9a8-4a14-8663-c7d6e83ddec3",
    breed: "Golden Retriever",
    age: 2,
    owner: "Nguyễn Văn A",
    phone: "0901234567",
    lastVisitDate: "19/11/2025",
    lastVisitDaysAgo: "Cách đây 1 ngày",
    lastVisitDoctor: "BS. Nguyễn Văn B",
    status: "normal",
  },
  {
    id: 2,
    name: "Luna",
    image:
      "https://www.figma.com/api/mcp/asset/6ef010eb-1724-4fb7-ba6e-e84132333e5e",
    breed: "Persian",
    age: 3,
    owner: "Trần Thị B",
    phone: "0909876543",
    lastVisitDate: "18/11/2025",
    lastVisitDaysAgo: "Cách đây 2 ngày",
    lastVisitDoctor: "BS. Trần Văn C",
    status: "treatment",
  },
  {
    id: 3,
    name: "Max",
    image:
      "https://www.figma.com/api/mcp/asset/f0c89c42-675f-42a2-b259-ad47d0644357",
    breed: "Husky",
    age: 4,
    owner: "Lê Văn C",
    phone: "0912345678",
    lastVisitDate: "15/11/2025",
    lastVisitDaysAgo: "Cách đây 5 ngày",
    lastVisitDoctor: "BS. Nguyễn Văn B",
    status: "normal",
  },
  {
    id: 4,
    name: "Bella",
    image:
      "https://www.figma.com/api/mcp/asset/71cf8008-bdd4-4b14-8d21-f744f5e24726",
    breed: "British Shorthair",
    age: 1,
    owner: "Phạm Thị D",
    phone: "0923456789",
    lastVisitDate: "20/11/2025",
    lastVisitDaysAgo: "Cách đây 0 ngày",
    lastVisitDoctor: "BS. Hoàng Văn D",
    status: "inpatient",
  },
  {
    id: 5,
    name: "Rocky",
    image:
      "https://www.figma.com/api/mcp/asset/83fed67f-e324-4350-835a-2b7e0d66a427",
    breed: "Bulldog",
    age: 5,
    owner: "Đỗ Văn E",
    phone: "0934567890",
    lastVisitDate: "17/11/2025",
    lastVisitDaysAgo: "Cách đây 3 ngày",
    lastVisitDoctor: "BS. Nguyễn Văn B",
    status: "treatment",
  },
]);

// Methods
const getStatusLabel = (status) => {
  const labels = {
    normal: "Bình thường",
    treatment: "Đang điều trị",
    inpatient: "Nội trú",
  };
  return labels[status] || status;
};

const openMedicalRecord = (patient) => {
  selectedPatient.value = patient;
  emit("open-record", patient);
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
