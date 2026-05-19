<template>
  <div class="w-full h-full px-8 py-6">
    <!-- Back Button -->
    <button
      @click="goBack"
      class="h-9 rounded-lg flex items-center gap-2 px-3 mb-6 hover:bg-gray-50"
    >
      <ArrowLeftIcon />
      <span class="text-sm font-medium text-gray-900">
        Quay lại danh sách
      </span>
    </button>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center h-64">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Đang tải dữ liệu bệnh án...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="flex items-center justify-center h-64">
      <div class="text-center">
        <p class="text-red-600 mb-4">{{ error }}</p>
        <button
          @click="loadPatientData"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
        >
          Thử lại
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="flex gap-6">
      <!-- Left Sidebar - Patient Info -->
      <div class="w-[357.664px] flex-shrink-0">
        <div
          class="bg-white border !border-gray-300 rounded-[14px] p-px shadow-sm"
        >
          <!-- Patient Header -->
          <div class="p-6 flex flex-col gap-2 items-center">
            <!-- Pet Image -->
            <div
              class="w-32 h-32 rounded-full border-4 border-[#bedbff] shadow-[0px_10px_15px_-3px_rgba(0,0,0,0.1),0px_4px_6px_-4px_rgba(0,0,0,0.1)] overflow-hidden p-1"
            >
              <img
                :src="patientData.image"
                alt=""
                class="w-full h-full object-cover rounded-full"
              />
            </div>

            <!-- Pet Name -->
            <h2
              class="text-2xl font-normal text-neutral-950 leading-8 tracking-[0.0703px]"
            >
              {{ patientData.name }}
            </h2>

            <!-- Species -->
            <p
              class="text-base text-[#4a5565] leading-6 tracking-[-0.3125px] text-center"
            >
              ({{ patientData.species }})
            </p>

            <!-- Badge -->
            <div
              :class="[
                'px-3 py-1 rounded-lg border flex items-center gap-2',
                patientData.type === 'member'
                  ? 'bg-blue-100 !border-[#bedbff]'
                  : 'bg-gray-100 border-gray-200',
              ]"
            >
              <!-- <img 
                :src="patientData.type === 'member' ? icons.memberBadge : icons.vanglaiBadge" 
                alt="" 
                class="w-3 h-3" 
              /> -->
              <span
                :class="[
                  'text-xs font-medium leading-4',
                  patientData.type === 'member'
                    ? 'text-[#1447e6]'
                    : 'text-[#364153]',
                ]"
              >
                {{ patientData.type === "member" ? "Member" : "Vãng lai" }}
              </span>
            </div>
          </div>

          <!-- Patient Details -->
          <div class="px-6 pb-6 flex flex-col gap-4">
            <!-- Warning Note -->
            <div
              class="bg-[#fff7ed] border-2 border-[#fdba74] rounded-[10px] p-4 flex flex-col gap-2"
            >
              <div class="flex items-center gap-2">
                <!-- <img :src="icons.warning" alt="" class="w-5 h-5" /> -->
                <span
                  class="text-base font-semibold text-[#7c2d12] leading-6 tracking-[-0.3125px]"
                >
                  Ghi Chú
                </span>
              </div>
              <p class="text-sm text-[#f97316] leading-5 tracking-[-0.1504px]">
                {{ patientData.note }}
              </p>
            </div>

            <div class="h-px bg-[rgba(0,0,0,0.1)]"></div>

            <!-- Biological Info -->
            <div class="flex flex-col gap-3">
              <h3
                class="text-base text-[#101828] leading-6 tracking-[-0.3125px]"
              >
                Thông tin Sinh học
              </h3>
              <div class="flex flex-col gap-2">
                <div class="flex justify-between items-center h-5">
                  <span
                    class="text-sm text-[#4a5565] leading-5 tracking-[-0.1504px]"
                    >Giống:</span
                  >
                  <span
                    class="text-sm font-bold text-neutral-950 leading-5 tracking-[-0.1504px]"
                    >{{ patientData.breed }}</span
                  >
                </div>
                <div class="flex justify-between items-center h-5">
                  <span
                    class="text-sm text-[#4a5565] leading-5 tracking-[-0.1504px]"
                    >Tuổi:</span
                  >
                  <span
                    class="text-sm font-bold text-neutral-950 leading-5 tracking-[-0.1504px]"
                    >{{ patientData.age }}</span
                  >
                </div>
                <div class="flex justify-between items-center h-5">
                  <span
                    class="text-sm text-[#4a5565] leading-5 tracking-[-0.1504px]"
                    >Giới tính:</span
                  >
                  <span
                    class="text-sm font-bold text-neutral-950 leading-5 tracking-[-0.1504px]"
                    >{{ patientData.gender }}</span
                  >
                </div>
                <div class="flex justify-between items-center h-5">
                  <span
                    class="text-sm text-[#4a5565] leading-5 tracking-[-0.1504px]"
                    >Cân nặng:</span
                  >
                  <span
                    class="text-sm font-bold text-[#00a63e] leading-5 tracking-[-0.1504px]"
                    >{{ patientData.weight }}</span
                  >
                </div>
              </div>
            </div>

            <div class="h-px bg-[rgba(0,0,0,0.1)]"></div>

            <!-- Owner Info -->
            <div class="flex flex-col gap-3">
              <h3
                class="text-base text-[#101828] leading-6 tracking-[-0.3125px]"
              >
                Chủ nuôi
              </h3>
              <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.user" alt="" class="w-4 h-4" /> -->
                  <span
                    class="text-sm text-neutral-950 leading-5 tracking-[-0.1504px]"
                    >{{ patientData.ownerName }}</span
                  >
                </div>
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.phone" alt="" class="w-4 h-4" /> -->
                  <a
                    :href="`tel:${patientData.ownerPhone}`"
                    class="text-sm text-[#155dfc] leading-5 tracking-[-0.1504px]"
                  >
                    {{ patientData.ownerPhone }}
                  </a>
                </div>
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.location" alt="" class="w-4 h-4" /> -->
                  <span
                    class="text-sm text-[#4a5565] leading-5 tracking-[-0.1504px]"
                    >{{ patientData.ownerAddress }}</span
                  >
                </div>
              </div>
            </div>

            <div class="h-px bg-[rgba(0,0,0,0.1)]"></div>
          </div>
        </div>
      </div>

      <!-- Right Content - Medical Records -->
      <div class="flex-1">
        <div
          class="bg-white border !border-gray-300 rounded-[14px] px-6 py-6 flex flex-col gap-6 shadow-sm"
        >
          <!-- Title -->
          <h2
            class="text-base font-normal text-neutral-950 tracking-[-0.3125px]"
          >
            Hồ sơ Bệnh án
          </h2>

          <!-- Tabs -->
          <div class="bg-gray-100 rounded-lg p-1 flex gap-1">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                'flex-1 h-9 rounded-lg flex items-center justify-center gap-2 transition-colors',
                activeTab === tab.id
                  ? 'bg-white shadow-sm'
                  : 'hover:bg-gray-50',
              ]"
            >
              <!-- <img :src="tab.icon" alt="" class="w-4 h-4" /> -->
              <span class="text-sm font-medium text-gray-900">
                {{ tab.label }}
              </span>
            </button>
          </div>

          <!-- Tab Content -->
          <div class="flex-1">
            <!-- Medical History Tab -->
            <div v-show="activeTab === 'history'" class="flex flex-col gap-8">
              <div
                v-if="medicalHistory.length === 0"
                class="border !border-gray-300 rounded-lg px-4 py-6 text-sm text-gray-500"
              >
                Chưa có lịch sử khám cho thú cưng này.
              </div>
              <div
                v-for="(record, index) in medicalHistory"
                :key="index"
                class="relative"
              >
                <!-- Timeline dot -->
                <div
                  class="absolute left-0 top-1 w-6 h-6 bg-[#2b7fff] border-4 border-white rounded-full shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1),0px_1px_2px_-1px_rgba(0,0,0,0.1)]"
                ></div>

                <!-- Timeline line -->
                <div
                  v-if="index < medicalHistory.length - 1"
                  class="absolute left-[13px] top-8 bottom-[-20px] w-0.5 bg-[#d1d5dc]"
                ></div>

                <!-- Record Card -->
                <div
                  class="ml-8 bg-white border !border-gray-300 border-l-4 border-l-[#2b7fff] rounded-[14px] p-4 shadow-sm"
                >
                  <!-- Header -->
                  <div class="flex justify-between items-start mb-4">
                    <div class="flex flex-col gap-1">
                      <div class="flex items-center gap-2 h-6">
                        <!-- <img :src="icons.calendar" alt="" class="w-4 h-4" /> -->
                        <span
                          class="text-base text-[#101828] leading-6 tracking-[-0.3125px]"
                          >{{ record.date }}</span
                        >
                        <span
                          class="text-sm text-[#6a7282] leading-5 tracking-[-0.1504px]"
                          >{{ record.time }}</span
                        >
                      </div>
                      <h3
                        class="text-lg font-normal text-neutral-950 leading-7 tracking-[-0.4395px]"
                      >
                        {{ record.title }}
                      </h3>
                    </div>
                    <div
                      class="bg-green-50 border !border-[#7bf1a8] rounded-lg px-2 py-[3px]"
                    >
                      <span class="text-xs font-medium text-[#008236] leading-4"
                        >Hoàn thành</span
                      >
                    </div>
                  </div>

                  <!-- Details -->
                  <div class="flex flex-col gap-2.5">
                    <div class="flex items-center gap-2 h-5">
                      <!-- <img :src="icons.doctor" alt="" class="w-4 h-4" /> -->
                      <span
                        class="text-sm text-[#4a5565] leading-5 tracking-[-0.1504px]"
                        >Bác sĩ:</span
                      >
                      <span
                        class="text-sm font-bold text-neutral-950 leading-5 tracking-[-0.1504px]"
                        >{{ record.doctor }}</span
                      >
                    </div>

                    <div class="flex gap-2">
                      <!-- <img
                        :src="icons.diagnosis"
                        alt=""
                        class="w-4 h-4 mt-0.5"
                      /> -->
                      <div class="flex-1">
                        <span
                          class="text-sm text-[#4a5565] leading-5 tracking-[-0.1504px]"
                          >Chẩn đoán:</span
                        >
                        <p
                          class="text-base text-[#101828] leading-6 tracking-[-0.3125px] mt-1"
                        >
                          {{ record.diagnosis }}
                        </p>
                      </div>
                    </div>

                    <div class="flex gap-2">
                      <!-- <img
                        :src="icons.treatment"
                        alt=""
                        class="w-4 h-4 mt-0.5"
                      /> -->
                      <div class="flex-1">
                        <span
                          class="text-sm text-[#4a5565] leading-5 tracking-[-0.1504px]"
                          >Điều trị:</span
                        >
                        <p
                          class="text-base text-[#101828] leading-6 tracking-[-0.3125px] mt-1"
                        >
                          {{ record.treatment }}
                        </p>
                      </div>
                    </div>

                    <button
                      @click="openDetailModal(record)"
                      class="bg-white border !border-gray-300 rounded-lg h-8 px-3 flex items-center gap-2 hover:bg-gray-50 self-start mt-2"
                    >
                      <!-- <img :src="icons.eye" alt="" class="w-4 h-4" /> -->
                      <span
                        class="text-sm font-medium text-neutral-950 tracking-[-0.1504px]"
                        >Xem chi tiết</span
                      >
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Vaccination Tab -->
            <div
              v-show="activeTab === 'vaccination'"
              class="border !border-gray-300 rounded-[10px] overflow-hidden"
            >
              <div v-if="vaccinations.length === 0" class="px-4 py-6 text-sm text-gray-500">
                Chưa có dữ liệu tiêm chủng trong hồ sơ hiện tại.
              </div>
              <table class="w-full">
                <thead class="bg-gray-100">
                  <tr class="h-16">
                    <th
                      class="text-left px-4 text-sm font-bold text-[#364153] tracking-[-0.1504px]"
                    >
                      Ngày tiêm
                    </th>
                    <th
                      class="text-left px-4 text-sm font-bold text-[#364153] tracking-[-0.1504px]"
                    >
                      Loại vắc-xin
                    </th>
                    <th
                      class="text-left px-4 text-sm font-bold text-[#364153] tracking-[-0.1504px]"
                    >
                      Số lô
                    </th>
                    <th
                      class="text-left px-4 text-sm font-bold text-[#364153] tracking-[-0.1504px]"
                    >
                      Bác sĩ
                    </th>
                    <th
                      class="text-left px-4 text-sm font-bold text-[#364153] tracking-[-0.1504px]"
                    >
                      Ngày tái chủng
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(vaccine, index) in vaccinations"
                    :key="index"
                    class="border-t border-gray-200 h-16"
                  >
                    <td
                      class="px-4 text-sm text-neutral-950 tracking-[-0.1504px]"
                    >
                      {{ vaccine.date }}
                    </td>
                    <td
                      class="px-4 text-sm font-semibold text-neutral-950 tracking-[-0.1504px]"
                    >
                      {{ vaccine.name }}
                    </td>
                    <td
                      class="px-4 text-sm text-[#4a5565] tracking-[-0.1504px]"
                    >
                      {{ vaccine.lot }}
                    </td>
                    <td
                      class="px-4 text-sm text-neutral-950 tracking-[-0.1504px]"
                    >
                      {{ vaccine.doctor }}
                    </td>
                    <td
                      class="px-4 text-sm text-[#00a63e] tracking-[-0.1504px]"
                    >
                      {{ vaccine.nextDate }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Images Tab -->
            <div v-show="activeTab === 'images'" class="grid grid-cols-3 gap-4">
              <div
                v-if="medicalImages.length === 0"
                class="col-span-3 border !border-gray-300 rounded-lg px-4 py-6 text-sm text-gray-500"
              >
                Chưa có hình ảnh cận lâm sàng hoặc đính kèm.
              </div>
              <div
                v-for="(image, index) in medicalImages"
                :key="index"
                class="flex flex-col gap-2"
              >
                <div
                  class="border !border-gray-300 rounded-[10px] shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1),0px_1px_2px_-1px_rgba(0,0,0,0.1)] overflow-hidden h-[222px]"
                >
                  <img
                    v-if="image.url"
                    :src="image.url"
                    alt=""
                    class="w-full h-full object-cover"
                  />
                  <div
                    v-else
                    class="w-full h-full bg-gray-100 flex items-center justify-center"
                  >
                    <div class="w-22 h-22 text-gray-400">No Image</div>
                  </div>
                </div>
                <div class="flex flex-col gap-1">
                  <div
                    class="border !border-gray-400 rounded-lg px-2 py-[3px] self-start"
                  >
                    <span
                      class="text-xs font-medium text-neutral-950 leading-4"
                      >{{ image.type }}</span
                    >
                  </div>
                  <p class="text-xs text-[#4a5565] leading-4">
                    {{ image.date }}
                  </p>
                  <p class="text-xs text-[#101828] leading-4">
                    {{ image.description }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Weight Tab -->
            <div v-show="activeTab === 'weight'" class="flex flex-col gap-4">
              <div
                v-if="weightChart.length === 0"
                class="border !border-gray-300 rounded-lg px-4 py-6 text-sm text-gray-500"
              >
                Chưa có dữ liệu cân nặng theo thời gian.
              </div>
              <!-- Weight Chart -->
              <div
                class="bg-gradient-to-r from-[#f0fdf4] to-[#eff6ff] border !border-[#b9f8cf] rounded-[10px] px-6 pt-6 pb-px"
                v-if="weightChart.length > 0"
              >
                <h3
                  class="text-base text-[#101828] leading-6 tracking-[-0.3125px] mb-4"
                >
                  Biểu đồ Cân nặng
                </h3>
                <div class="flex justify-between items-end h-64 pb-6">
                  <div
                    v-for="(point, index) in weightChart"
                    :key="index"
                    class="flex-1 flex flex-col items-center gap-2"
                  >
                    <span
                      class="text-sm text-[#101828] leading-5 tracking-[-0.1504px]"
                      >{{ point.weight }}</span
                    >
                    <div
                      class="w-full bg-gradient-to-t from-[#2b7fff] to-[#8ec5ff] rounded-t-[10px]"
                      :style="{ height: point.height }"
                    ></div>
                    <span class="text-xs text-[#4a5565] leading-4">{{
                      point.date
                    }}</span>
                  </div>
                </div>
              </div>

              <!-- Weight History Table -->
              <div
                class="border !border-gray-300 rounded-[10px] overflow-hidden"
                v-if="weightHistory.length > 0"
              >
                <table class="w-full">
                  <thead class="bg-gray-100">
                    <tr class="h-11">
                      <th
                        class="text-left px-4 text-sm font-bold text-[#364153] tracking-[-0.1504px]"
                      >
                        Ngày đo
                      </th>
                      <th
                        class="text-left px-4 text-sm font-bold text-[#364153] tracking-[-0.1504px]"
                      >
                        Cân nặng
                      </th>
                      <th
                        class="text-left px-4 text-sm font-bold text-[#364153] tracking-[-0.1504px]"
                      >
                        Thay đổi
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(record, index) in weightHistory"
                      :key="index"
                      class="border-t border-gray-200 h-11"
                    >
                      <td
                        class="px-4 text-sm text-neutral-950 tracking-[-0.1504px]"
                      >
                        {{ record.date }}
                      </td>
                      <td
                        class="px-4 text-sm font-bold text-neutral-950 tracking-[-0.1504px]"
                      >
                        {{ record.weight }}
                      </td>
                      <td
                        class="px-4 text-sm tracking-[-0.1504px]"
                        :class="record.change ? 'text-[#e7000b]' : ''"
                      >
                        {{ record.change || "" }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <div
      v-if="isDetailModalOpen"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-[2500] p-6"
    >
      <div class="w-full max-w-[1080px] h-full max-h-[92vh]">
        <XemChiTiet
          :record="selectedRecord?.raw || null"
          :patient="patientData"
          @close="isDetailModalOpen = false"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "@/utils/api";
import { resolveImageUrl } from "@/utils/image";
import XemChiTiet from "./view-detail/index.vue";
//Icon SVG
import ArrowLeftIcon from "@/assets/svg/arrow-left.svg";

const router = useRouter();
const route = useRoute();
const DEFAULT_PET_IMAGE = "https://www.figma.com/api/mcp/asset/7dc3f4c9-30fd-4f46-b415-7a1aab552e01";

// Loading state
const loading = ref(true);
const error = ref(null);

// Icons
const icons = {
  arrowLeft:
    "https://www.figma.com/api/mcp/asset/88a7f542-899f-4e9a-b859-50ef9c92edb7",
  memberBadge:
    "https://www.figma.com/api/mcp/asset/efeee3a6-470a-4f85-97c7-29ff36d08ef7",
  vanglaiBadge:
    "https://www.figma.com/api/mcp/asset/3ff3b015-4003-4406-aa33-bf59b5e29503",
  warning:
    "https://www.figma.com/api/mcp/asset/6e5976ea-c54a-4f07-bf61-8e5fcf7fa97e",
  user: "https://www.figma.com/api/mcp/asset/fd01f627-99e0-4a35-b9ad-295c945b166e",
  phone:
    "https://www.figma.com/api/mcp/asset/923d352e-0b30-448f-895b-67a61c909d12",
  location:
    "https://www.figma.com/api/mcp/asset/663dc7d9-184e-40e4-a885-43aea918487b",
  history:
    "https://www.figma.com/api/mcp/asset/856f8bac-fbac-409f-8f52-f85aa8d067e2",
  vaccination:
    "https://www.figma.com/api/mcp/asset/46f65f9f-65f1-4b7d-a220-a21cd420b1cd",
  images:
    "https://www.figma.com/api/mcp/asset/0d21df11-24c0-4370-a314-9d8df5538380",
  weight:
    "https://www.figma.com/api/mcp/asset/b4bee620-f957-4d59-81bc-232dff3e49f9",
  calendar:
    "https://www.figma.com/api/mcp/asset/b2d36ed2-541e-4054-b591-e20cf9d49671",
  doctor:
    "https://www.figma.com/api/mcp/asset/fa1bbe6d-a846-4f94-938f-2c5ef8b8a830",
  diagnosis:
    "https://www.figma.com/api/mcp/asset/cdcd4e33-439a-4e7b-a90a-2faff4f969b8",
  treatment:
    "https://www.figma.com/api/mcp/asset/f0cf62e4-802c-4c31-8a43-6a5d890cebb5",
  eye: "https://www.figma.com/api/mcp/asset/476d2828-1132-43c7-852a-13856f360b07",
};

// Active tab
const activeTab = ref("history");
const isDetailModalOpen = ref(false);
const selectedRecord = ref(null);

// Tabs configuration
const tabs = [
  {
    id: "history",
    label: "Lịch sử Khám",
    icon: icons.history,
    left: "3px",
    width: "173.328px",
  },
  {
    id: "vaccination",
    label: "Tiêm chủng",
    icon: icons.vaccination,
    left: "176.33px",
    width: "173.336px",
  },
  {
    id: "images",
    label: "Hình ảnh",
    icon: icons.images,
    left: "349.66px",
    width: "173.336px",
  },
  {
    id: "weight",
    label: "Cân nặng",
    icon: icons.weight,
    left: "523px",
    width: "173.336px",
  },
];

const patientData = ref({
  image: DEFAULT_PET_IMAGE,
  name: "Chưa có tên",
  species: "Chưa rõ",
  type: "vanglai",
  note: "Không có ghi chú",
  breed: "Chưa rõ",
  age: "Chưa rõ",
  gender: "Chưa rõ",
  weight: "Chưa rõ",
  ownerName: "Chưa có chủ nuôi",
  ownerPhone: "Chưa có SĐT",
  ownerAddress: "Chưa có địa chỉ",
});
const medicalHistory = ref([]);
const vaccinations = ref([]);
const medicalImages = ref([]);
const weightChart = ref([]);
const weightHistory = ref([]);

// Methods
const goBack = () => {
  router.push("/doctor/medical-records");
};

const openDetailModal = (record) => {
  selectedRecord.value = record;
  isDetailModalOpen.value = true;
};

const loadPatientData = async () => {
  const thuCungId = route.query.thu_cung_id;

  if (!thuCungId) {
    error.value = "Không tìm thấy ID thú cưng";
    loading.value = false;
    return;
  }

  loading.value = true;
  error.value = null;

  try {
    const { data: detailRes } = await api.get(`/ho-so-benh-an/thu-cung/${thuCungId}`);
    const payload = detailRes?.data || {};
    const pet = payload.thu_cung || {};
    const owner = payload.khach_hang || {};
    const history = payload.history || [];

    patientData.value = {
      image: resolveImageUrl(pet.image, DEFAULT_PET_IMAGE),
      name: pet.name || "Chưa có tên",
      species: pet.species || "Chưa rõ",
      type: owner.type || "vanglai",
      note: pet.note || "Không có ghi chú",
      breed: pet.breed || "Chưa rõ giống",
      age: pet.age || "Chưa rõ tuổi",
      gender: toGenderLabel(pet.gender),
      weight: pet.weight ? `${pet.weight} kg` : "Chưa cân",
      ownerName: owner.name || "Chưa có chủ nuôi",
      ownerPhone: owner.phone || "Chưa có SĐT",
      ownerAddress: owner.address || "Chưa có địa chỉ",
    };

    medicalHistory.value = history.map((record) => ({
      id: record.id,
      date: record.date || formatDate(record.created_at),
      time: record.time || formatTime(record.created_at),
      title: record.reason || "Khám bệnh",
      doctor: record.doctor?.name || "Chưa xác định",
      diagnosis: record.diagnosis || "Chưa có chẩn đoán",
      treatment: record.notes || "Không có ghi chú điều trị",
      raw: record,
    }));

    const weightRecords = (payload.weight_history || [])
      .map((record) => ({
        date: record.date,
        weight: parseFloat(record.value),
      }))
      .filter((record) => !Number.isNaN(record.weight));

    if (weightRecords.length > 0) {
      const minWeightValue = Math.min(...weightRecords.map((d) => d.weight));
      const maxWeightValue = Math.max(...weightRecords.map((d) => d.weight));

      weightChart.value = weightRecords.map((item) => ({
        weight: `${item.weight}kg`,
        height: `${((item.weight - minWeightValue) / (maxWeightValue - minWeightValue || 1)) * 256}px`,
        date: item.date,
      }));

      weightHistory.value = weightRecords.map((item, index) => {
        const change =
          index > 0 ? item.weight - weightRecords[index - 1].weight : 0;
        return {
          date: item.date,
          weight: `${item.weight} kg`,
          change:
            change !== 0
              ? `${change > 0 ? "+" : ""}${change.toFixed(1)} kg`
              : "",
        };
      }).reverse();
    } else {
      weightChart.value = [];
      weightHistory.value = [];
    }
  } catch (err) {
    console.error("Load patient data error:", err);
    error.value = err.response?.data?.message || "Không thể tải dữ liệu bệnh án";
  } finally {
    loading.value = false;
  }
};

// Helper functions
const toGenderLabel = (gender) => {
  if (gender === "male" || gender === "duc") return "Đực";
  if (gender === "female" || gender === "cai") return "Cái";
  return "Chưa rõ";
};

const formatDate = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleDateString("vi-VN");
};

const formatTime = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleTimeString("vi-VN", { hour: "2-digit", minute: "2-digit" });
};

// Load data on mount
onMounted(() => {
  loadPatientData();
});
</script>
