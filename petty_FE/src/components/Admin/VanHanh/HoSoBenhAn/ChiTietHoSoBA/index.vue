<template>
  <div class="flex flex-col gap-6">
    <!-- Back Button -->
    <button
      @click="$emit('back')"
      class="rounded-lg h-9 px-[13px] mb-0 w-fit flex items-center gap-2 hover:bg-gray-50 transition-colors"
    >
      <ArrowLeftIcon class="text-black" />
      <span
        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
      >
        Quay lại danh sách
      </span>
    </button>

    <!-- Patient Info Card -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div class="flex items-start justify-between mb-4">
        <!-- Left: Patient Photo and Info -->
        <div class="flex gap-4">
          <img
            :src="patient.image"
            :alt="patient.name"
            class="w-24 h-24 rounded-full object-cover"
          />
          <div class="flex flex-col gap-2">
            <h2
              class="font-nunito text-2xl leading-8 text-[#101828] tracking-[0.0703px]"
            >
              {{ patient.name }}
            </h2>
            <div class="flex flex-col gap-1">
              <p
                class="font-nunito text-sm leading-5 text-[#364153] tracking-tight"
              >
                {{ patient.species }} {{ patient.breed }}
              </p>
              <p
                class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
              >
                Tuổi: {{ patient.age }} tuổi
              </p>
              <!-- <div class="flex items-center gap-1">
                <img :src="iconWeight" alt="Weight" class="w-4 h-4" />
                <p
                  class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                >
                  Cân nặng: 28.5 kg
                </p>
              </div> -->
              <!-- <p class="font-nunito text-xs leading-4 text-[#4a5565]">
                Mã vi mạch: 982000123456789
              </p> -->
            </div>
          </div>
        </div>

        <!-- Right: Owner Info -->
        <div class="flex flex-col items-end gap-2">
          <div class="flex items-center gap-2">
            <UserIcon class="w-4 h-4" />
            <p
              class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
            >
              {{ patient.owner }}
            </p>
          </div>
          <div class="flex items-center gap-2">
            <PhoneCallIcon class="w-4 h-4 text-[#009689]" />
            <a
              :href="'tel:' + patient.phone"
              class="font-nunito text-sm leading-5 text-[#009689] hover:underline"
            >
              {{ patient.phone }}
            </a>
          </div>
          <div class="flex items-center gap-2">
            <MapIcon class="w-4 h-4 text-[#4a5565]" />
            <p
              class="font-nunito text-base leading-6 text-[#4a5565] tracking-tight text-right"
            >
              123 Nguyễn Huệ, Q.1, TP.HCM
            </p>
          </div>
        </div>
      </div>

      <!-- Warnings and Allergies -->
      <div class="border-t !border-gray-300 pt-4 flex flex-col gap-2">
        <div
          class="bg-orange-50 border !border-[#ffd6a7] rounded-[10px] px-[13px] py-[13px]"
        >
          <div class="flex items-center gap-2">
            <WarningIcon class="w-4 h-4 text-[#9f2d00]" />
            <p
              class="font-nunito font-bold text-sm leading-5 text-[#9f2d00] tracking-tight"
            >
              Ghi chú:
            </p>
            <p
              class="font-nunito text-sm leading-5 text-[#9f2d00] tracking-tight"
            >
              Dễ cắn khi sợ hãi và dị ứng với Penicillin
            </p>
          </div>
        </div>
        <!-- <div class="bg-red-50 border border-[#ffc9c9] rounded-[10px] px-[13px] py-[13px]">
          <div class="flex items-center gap-2">
            <p class="font-nunito font-bold text-sm leading-5 text-[#9f0712] tracking-tight">
              Dị ứng:
            </p>
            <p class="font-nunito text-sm leading-5 text-[#9f0712] tracking-tight">
              Penicillin
            </p>
          </div>
        </div> -->
      </div>
    </div>

    <!-- Medical History and Details Row -->
    <div class="grid grid-cols-[358px_1fr] gap-6">
      <!-- Left: Medical History -->
      <div
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
      >
        <h3
          class="font-nunito text-base leading-4 text-neutral-950 tracking-tight mb-6"
        >
          Lịch sử khám
        </h3>
        <div class="flex flex-col gap-3">
          <button
            v-for="(visit, index) in medicalHistory"
            :key="index"
            @click="selectedVisit = visit"
            :class="[
              'rounded-[10px] p-[18px] text-left transition-colors',
              selectedVisit.date === visit.date
                ? 'bg-teal-50 border-2 border-[#00bba7]'
                : 'border-2 border-gray-200 hover:border-gray-300',
            ]"
          >
            <div class="flex gap-3">
              <CalendarIcon class="w-5 h-5" />
              <div class="flex flex-col gap-1">
                <p
                  class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                >
                  {{ visit.date }}
                </p>
                <p class="font-nunito text-xs leading-4 text-[#4a5565]">
                  {{ visit.reason }}
                </p>
                <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                  Bởi: {{ visit.doctor }}
                </p>
              </div>
            </div>
          </button>
        </div>
      </div>

      <!-- Right: Visit Details -->
      <div
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 flex flex-col gap-[30px]"
      >
        <!-- Header with tabs -->
        <div class="flex items-center justify-between">
          <h3
            class="font-nunito text-base leading-4 text-neutral-950 tracking-tight"
          >
            Chi tiết phiếu khám - {{ selectedVisit.date }}
          </h3>
        </div>

        <!-- Tabs -->
        <div
          class="bg-[#ececf0] rounded-[14px] p-[3.5px] flex items-center gap-0"
        >
          <button
            v-for="(tab, index) in tabs"
            :key="index"
            @click="activeTab = tab.id"
            :class="[
              'flex-1 h-[29px] rounded-[14px] flex items-center justify-center gap-2 transition-colors',
              activeTab === tab.id ? 'bg-white' : '',
            ]"
          >
            <span
              class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
            >
              {{ tab.label }}
            </span>
          </button>
        </div>

        <!-- Tab Content -->
        <div v-if="activeTab === 'info'" class="flex flex-col gap-4">
          <!-- Doctor -->
          <div class="flex flex-col gap-1">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-[#364153] tracking-tight"
            >
              Bác sĩ phụ trách
            </label>
            <p
              class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
            >
              {{ selectedVisit.doctor }}
            </p>
          </div>

          <!-- Reason -->
          <div class="flex flex-col gap-1">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-[#364153] tracking-tight"
            >
              Lý do đến khám
            </label>
            <p
              class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
            >
              {{ selectedVisit.reason }}
            </p>
          </div>

          <!-- Vital Signs -->
          <div class="flex flex-col gap-2">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-[#364153] tracking-tight"
            >
              Chỉ số sinh tồn
            </label>
            <div class="grid grid-cols-2 gap-4">
              <div
                class="bg-gray-100 rounded-[10px] p-3 flex items-center gap-2"
              >
                <TemperatureIcon class="w-5 h-5 text-[#FB2C36]" />
                <div class="flex flex-col">
                  <p class="font-nunito text-xs leading-4 text-[#4a5565]">
                    Nhiệt độ
                  </p>
                  <p
                    class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                  >
                    38.5°C
                  </p>
                </div>
              </div>
              <div
                class="bg-gray-100 rounded-[10px] p-3 flex items-center gap-2"
              >
                <HeartIcon class="w-5 h-5 text-[#F6339A]" />
                <div class="flex flex-col">
                  <p class="font-nunito text-xs leading-4 text-[#4a5565]">
                    Nhịp tim
                  </p>
                  <p
                    class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                  >
                    90 bpm
                  </p>
                </div>
              </div>
              <div
                class="bg-gray-100 rounded-[10px] p-3 flex items-center gap-2"
              >
                <BreathIcon class="w-5 h-5 text-[#2B7FFF]" />
                <div class="flex flex-col">
                  <p class="font-nunito text-xs leading-4 text-[#4a5565]">
                    Hô hấp
                  </p>
                  <p
                    class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                  >
                    24 lần/phút
                  </p>
                </div>
              </div>
              <div
                class="bg-gray-100 rounded-[10px] p-3 flex items-center gap-2"
              >
                <WeightIcon class="w-5 h-5 text-[#00C950]" />
                <div class="flex flex-col">
                  <p class="font-nunito text-xs leading-4 text-[#4a5565]">
                    Cân nặng
                  </p>
                  <p
                    class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                  >
                    28.5 kg
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Diagnosis -->
          <div class="flex flex-col gap-1">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-[#364153] tracking-tight"
            >
              Chẩn đoán (Diagnosis)
            </label>
            <div
              class="bg-blue-50 border !border-blue-100 rounded-[10px] p-[13px]"
            >
              <p
                class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
              >
                Sức khỏe tốt, tiêm vắc-xin phòng dại và bệnh Care
              </p>
            </div>
          </div>

          <!-- Notes -->
          <div class="flex flex-col gap-1">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-[#364153] tracking-tight"
            >
              Ghi chú nội bộ
            </label>
            <div class="bg-gray-200 rounded-[10px] p-3">
              <p
                class="font-nunito text-base leading-6 text-[#364153] tracking-tight"
              >
                Theo dõi sau tiêm 15 phút, không có phản ứng phụ
              </p>
            </div>
          </div>
        </div>

        <div v-else-if="activeTab === 'services'" class="flex flex-col gap-6">
          <!-- Performed Services Section -->
          <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between h-8">
              <label
                class="font-nunito font-medium text-sm leading-[14px] text-[#364153] tracking-tight"
              >
                Dịch vụ đã thực hiện
              </label>
            </div>
            <div class="flex flex-col gap-2">
              <div
                class="bg-gray-100 border !border-gray-300 rounded-[10px] px-[13px] py-[13px] flex flex-col gap-1"
              >
                <p
                  class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                >
                  Tiêm phòng dại
                </p>
                <p class="font-nunito text-xs leading-4 text-[#4a5565]">
                  Vắc-xin phòng dại hàng năm
                </p>
              </div>
              <div
                class="bg-gray-100 border !border-gray-300 rounded-[10px] px-[13px] py-[13px] flex flex-col gap-1"
              >
                <p
                  class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                >
                  Tiêm phòng Care
                </p>
                <p class="font-nunito text-xs leading-4 text-[#4a5565]">
                  Vắc-xin 7 trong 1
                </p>
              </div>
            </div>
          </div>

          <!-- Prescription Section -->
          <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between h-8">
              <label
                class="font-nunito font-medium text-sm leading-[14px] text-[#364153] tracking-tight"
              >
                Đơn thuốc
              </label>
            </div>
            <div class="h-[52px] flex items-center justify-center">
              <p
                class="font-nunito text-sm leading-5 text-[#6a7282] tracking-tight"
              >
                Chưa có đơn thuốc
              </p>
            </div>
          </div>
        </div>

        <div v-else-if="activeTab === 'images'" class="flex flex-col gap-4">
          <!-- Section Header -->
          <div class="flex items-center justify-between h-8">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-[#364153] tracking-tight"
            >
              Hình ảnh & Kết quả xét nghiệm
            </label>
          </div>

          <!-- Upload Area - Empty State -->
          <div
            class="bg-gray-50 border-2 !border-[#d1d5dc] border-dashed rounded-[10px] h-56 flex flex-col items-center justify-center gap-4"
          >
            <ImageIcon class="w-12 h-12 text-[#d1d5dc]" />
            <p
              class="font-nunito text-sm leading-5 text-[#6a7282] tracking-tight"
            >
              Chưa có hình ảnh hoặc tài liệu nào
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Re-examination Schedule Card -->
    <div
      class="bg-gradient-to-r from-[#eff6ff] to-[#faf5ff] border-2 border-[#8ec5ff] rounded-[12px] p-6 flex flex-col gap-6"
    >
      <!-- Card Title -->
      <div class="flex items-center gap-2">
        <p
          class="font-nunito text-[16px] leading-[16px] text-[#1c398e] tracking-[-0.3125px]"
        >
          KẾ HOẠCH TÁI KHÁM & NHẮC LỊCH
        </p>
      </div>

      <!-- Schedule Content -->
      <div
        class="bg-white border-2 border-[#bedbff] rounded-[10px] p-4 flex flex-col gap-3"
      >
        <!-- Date and Badge -->
        <div class="flex items-center justify-between">
          <div class="flex flex-col gap-1">
            <div class="flex items-baseline gap-2">
              <p
                class="font-nunito font-bold text-[18px] leading-[28px] text-[#101828] tracking-[-0.4395px]"
              >
                04/12/2025
              </p>
              <p
                class="font-nunito text-[16px] leading-[24px] text-[#4a5565] tracking-[-0.3125px]"
              >
                (Thứ Năm)
              </p>
            </div>
            <p
              class="font-nunito text-[14px] leading-[20px] text-[#6a7282] tracking-[-0.1504px]"
            >
              Còn 3 ngày nữa
            </p>
          </div>
          <span
            class="inline-flex items-center px-[9px] py-[3px] rounded-lg font-nunito font-medium text-xs leading-4 bg-purple-100 border border-[#dab2ff] text-[#8200db]"
          >
            Tiêm phòng
          </span>
        </div>

        <!-- Note Box -->
        <div
          class="bg-blue-50 border !border-blue-100 rounded-[10px] p-[13px] flex gap-3"
        >
          <QaMessIcon class="w-5 h-5" />
          <div class="flex flex-col gap-1">
            <p
              class="font-nunito text-[14px] leading-[20px] text-[#101828] tracking-[-0.1504px]"
            >
              Hẹn tiêm mũi nhắc lại vắc-xin 7 bệnh (Mũi 3). Chủ nuôi nhớ mang
              theo sổ tiêm chủng.
            </p>
            <p class="font-nunito text-xs leading-4 text-[#6a7282]">
              Ghi chú từ BS. Nguyễn Văn B
            </p>
          </div>
        </div>

        <!-- Status and Actions -->
        <!-- <div class="border-t border-gray-200 pt-[1px] flex items-center justify-between">
          <div class="flex items-center gap-2">
            <p class="font-nunito text-[14px] leading-[20px] text-[#4a5565] tracking-[-0.1504px]">
              Trạng thái:
            </p>
            <span class="inline-flex items-center px-[9px] py-[3px] rounded-lg font-nunito font-medium text-xs leading-4 bg-[#fef9c2] border border-[#ffdf20] text-[#a65f00]">
              🟡 Chờ xử lý
            </span>
          </div>
          <div class="flex items-center gap-2">
            <button class="bg-white border border-[#7bf1a8] rounded-lg h-8 px-[11px] flex items-center gap-1 hover:bg-green-50 transition-colors">
              <img :src="iconPhoneCheck" alt="Call" class="w-4 h-4" />
              <span class="font-nunito font-medium text-[14px] leading-[20px] text-[#00a63e] tracking-[-0.1504px]">
                📞 Xác nhận đã gọi
              </span>
            </button>
            <button class="bg-[#155dfc] rounded-lg h-8 px-[10px] flex items-center gap-1 hover:bg-[#1350e0] transition-colors">
              <img :src="iconCalendarPlus" alt="Book" class="w-4 h-4" />
              <span class="font-nunito font-medium text-[14px] leading-[20px] text-white tracking-[-0.1504px]">
                📅 Đặt lịch ngay
              </span>
            </button>
            <button class="bg-white border border-[#ffa2a2] rounded-lg w-[38px] h-8 flex items-center justify-center hover:bg-red-50 transition-colors">
              <img :src="iconTrash" alt="Delete" class="w-4 h-4" />
            </button>
          </div>
        </div> -->
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
//Icon SVG
import ArrowLeftIcon from "@/assets/svg/arrow-left.svg";
import WarningIcon from "@/assets/svg/warning.svg";
import CalendarIcon from "@/assets/svg/calendar.svg";
import HeartIcon from "@/assets/svg/heart.svg";
import UserIcon from "@/assets/svg/user.svg";
import PhoneCallIcon from "@/assets/svg/phonecall.svg";
import MapIcon from "@/assets/svg/map.svg";
import TemperatureIcon from "@/assets/svg/temperature.svg";
import BreathIcon from "@/assets/svg/breath.svg";
import WeightIcon from "@/assets/svg/weight.svg";
import ImageIcon from "@/assets/svg/image.svg";
import QaMessIcon from "@/assets/svg/qa-mess.svg";
const props = defineProps({
  patient: {
    type: Object,
    required: true,
  },
});

defineEmits(["back"]);

// Icons
const iconBack =
  "https://www.figma.com/api/mcp/asset/4ab4b914-6785-49c7-b04d-ff8a0192483b";
const iconWeight =
  "https://www.figma.com/api/mcp/asset/ca58f6e1-61e9-424c-add3-405ee13711c9";
const iconUser =
  "https://www.figma.com/api/mcp/asset/f4d78c69-f836-4b10-a671-e3f62937b86d";
const iconPhone =
  "https://www.figma.com/api/mcp/asset/d80f605e-ba76-472b-a4a5-288f20d3b629";
const iconLocation =
  "https://www.figma.com/api/mcp/asset/c09eef86-b430-491e-9e7a-d9ee707928b9";
const iconAlert =
  "https://www.figma.com/api/mcp/asset/ea36ae7b-a672-4357-9383-04d9f2dde2fa";
const iconCalendar =
  "https://www.figma.com/api/mcp/asset/dd634253-fea8-47da-949f-0912c6ec1f68";
const iconInfo =
  "https://www.figma.com/api/mcp/asset/be0020cc-20c7-4c90-bb69-4d5a39ac36e9";
const iconServices =
  "https://www.figma.com/api/mcp/asset/0ed0317d-2f15-4ee0-8a93-aa506c0ce0cd";
const iconImages =
  "https://www.figma.com/api/mcp/asset/984dd6a3-bbb2-4877-882c-57e01794ed69";
const iconTemp =
  "https://www.figma.com/api/mcp/asset/c2388239-23e1-4152-b258-7f201196c69f";
const iconHeart =
  "https://www.figma.com/api/mcp/asset/4f05fa02-1ee5-4be0-a73a-7d5fc3b5320d";
const iconLungs =
  "https://www.figma.com/api/mcp/asset/2253a098-eb59-4ee8-927c-02f41871caa7";
const iconScale =
  "https://www.figma.com/api/mcp/asset/abea2973-bdc1-4361-85ea-7e950004cdd3";
const iconImagePlaceholder =
  "https://www.figma.com/api/mcp/asset/b34534d4-96ca-4bb4-aaf9-85801b20c55f";
const iconClipboard =
  "https://www.figma.com/api/mcp/asset/bc9e4feb-a504-49e2-b9a8-ecaa8e247dd9";
const iconPhoneCheck =
  "https://www.figma.com/api/mcp/asset/a2d6229c-5bfc-4581-8d8d-1835060c47b5";
const iconCalendarPlus =
  "https://www.figma.com/api/mcp/asset/ec9c9a09-6781-485d-9c4e-9596d60ea7f3";
const iconTrash =
  "https://www.figma.com/api/mcp/asset/4394fffa-b9e9-4861-9796-7c6f6cac9986";

// Reactive state
const activeTab = ref("info");

// Tabs configuration
const tabs = ref([
  { id: "info", label: "Thông tin khám", icon: iconInfo },
  { id: "services", label: "Dịch vụ & Thuốc", icon: iconServices },
  { id: "images", label: "Hình ảnh & XN", icon: iconImages },
]);

// Medical history
const medicalHistory = ref([
  {
    date: "19/11/2025",
    reason: "Tiêm phòng định kỳ",
    doctor: "BS. Nguyễn Văn B",
  },
  {
    date: "15/10/2025",
    reason: "Khám tiêu hóa - Bỏ ăn, nôn mửa",
    doctor: "BS. Trần Văn C",
  },
]);

const selectedVisit = ref(medicalHistory.value[0]);
</script>
