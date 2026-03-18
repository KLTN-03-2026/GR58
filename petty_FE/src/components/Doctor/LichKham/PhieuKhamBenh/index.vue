<template>
  <div class="w-full min-h-screen px-8 py-6 flex flex-col">
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-20">
      <div class="text-center">
        <div
          class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#155dfc] mx-auto mb-4"
        ></div>
        <p class="text-gray-600">Đang tải thông tin bệnh nhân...</p>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else>
      <div class="flex items-center justify-between mb-6">
        <!-- Left: Back button & Title -->
        <div class="flex items-center gap-4">
          <button
            class="h-9 px-3 rounded-lg flex items-center gap-2 hover:bg-gray-50 transition-colors"
            @click="handleBack"
          >
            <ArrowLeftIcon />
            <span
              class="font-medium text-sm leading-5 text-gray-900 tracking-[-0.1504px]"
              >Quay lại</span
            >
          </button>
          <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-semibold text-black">Khám bệnh</h1>
            <p class="text-gray-500 font-medium text-base">
              Nhập chẩn đoán và kê đơn thuốc
            </p>
          </div>
        </div>

        <!-- Right: Save button -->
        <button
          class="h-9 px-3 bg-[#155dfc] text-white rounded-lg flex items-center gap-2 hover:bg-[#1447e6] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          @click="handleSave"
          :disabled="saving"
        >
          <div
            v-if="saving"
            class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
          ></div>
          <!-- <img v-else :src="icons.save" alt="" class="w-4 h-4" /> -->
          <span class="font-medium text-sm leading-5 tracking-[-0.1504px]">
            {{ saving ? "Đang lưu..." : "Lưu hồ sơ" }}
          </span>
        </button>
      </div>

      <!-- Patient Info Card (Blue highlight) -->
      <div
        class="bg-blue-50 border-2 !border-[#bedbff] rounded-[14px] p-[26px] mb-6"
      >
        <div class="flex items-start gap-6">
          <!-- Pet Image -->
          <div
            class="w-32 h-32 border-4 border-white rounded-[14px] shadow-lg overflow-hidden flex-shrink-0"
          >
            <img
              :src="patientInfo.petImage"
              alt=""
              class="w-full h-full object-cover"
            />
          </div>

          <!-- Patient Details -->
          <div class="flex-1 flex flex-col gap-3">
            <!-- Pet Name & Badge -->
            <div class="flex items-center gap-3">
              <h2
                class="font-bold text-2xl leading-8 text-[#101828] tracking-[0.0703px]"
              >
                {{ patientInfo.petName }}
              </h2>
              <div
                class="bg-purple-50 border !border-[#e9d4ff] rounded-lg px-2 py-1 flex items-center gap-2"
              >
                <!-- <img :src="icons.userPurple" alt="" class="w-4 h-4" /> -->
                <span class="font-medium text-xs leading-4 text-[#8200db]">{{
                  patientInfo.badge
                }}</span>
              </div>
            </div>

            <!-- Species, Breed, Age -->
            <div
              class="flex items-center gap-3 text-sm leading-5 text-[#364153] tracking-[-0.1504px]"
            >
              <div class="flex gap-1">
                <span class="font-bold">Loài:</span>
                <span class="font-normal">{{ patientInfo.species }}</span>
              </div>
              <span class="text-[#99a1af]">•</span>
              <div class="flex gap-1">
                <span class="font-bold">Giống:</span>
                <span class="font-normal">{{ patientInfo.breed }}</span>
              </div>
              <span class="text-[#99a1af]">•</span>
              <div class="flex gap-1">
                <span class="font-bold">Tuổi:</span>
                <span class="font-normal">{{ patientInfo.age }}</span>
              </div>
            </div>

            <!-- Owner & Appointment Info -->
            <div class="bg-gray-50 rounded-[10px] p-4 grid grid-cols-2 gap-3">
              <!-- Owner Info -->
              <div class="flex flex-col gap-2">
                <p
                  class="font-normal text-xs leading-4 text-[#6a7282] uppercase"
                >
                  Chủ nuôi
                </p>
                <p
                  class="font-bold text-base leading-6 text-[#101828] tracking-[-0.3125px]"
                >
                  {{ patientInfo.ownerName }}
                </p>
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.phone" alt="" class="w-4 h-4" /> -->
                  <span
                    class="font-normal text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                    >{{ patientInfo.ownerPhone }}</span
                  >
                </div>
              </div>

              <!-- Appointment Info -->
              <div class="flex flex-col gap-2">
                <p
                  class="font-normal text-xs leading-4 text-[#6a7282] uppercase"
                >
                  Thông tin lịch khám
                </p>
                <div class="flex items-center gap-4">
                  <div class="flex items-center gap-1.5">
                    <!-- <img :src="icons.calendar" alt="" class="w-4 h-4" /> -->
                    <span
                      class="font-normal text-sm leading-5 text-[#364153] tracking-[-0.1504px]"
                      >{{ patientInfo.appointmentDate }}</span
                    >
                  </div>
                  <div class="flex items-center gap-1.5">
                    <!-- <img :src="icons.clock" alt="" class="w-4 h-4" /> -->
                    <span
                      class="font-normal text-sm leading-5 text-[#364153] tracking-[-0.1504px]"
                      >{{ patientInfo.appointmentTime }}</span
                    >
                  </div>
                </div>
                <div
                  class="bg-white border !border-gray-300 rounded-lg px-2 py-1 inline-flex items-center gap-2 w-fit"
                >
                  <!-- <img :src="icons.stethoscope" alt="" class="w-4 h-4" /> -->
                  <span class="font-medium text-xs leading-4 text-gray-900">{{
                    patientInfo.service
                  }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content: Two Columns -->
      <div class="flex items-start justify-between gap-6">
        <!-- Left Column: Forms -->
        <div class="flex flex-col gap-6 flex-1">
          <!-- Vital Signs Card -->
          <div
            class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-[25px]"
          >
            <div class="flex items-center gap-2 mb-[30px]">
              <!-- <img :src="icons.activity" alt="" class="w-4 h-4" /> -->
              <h3
                class="font-normal text-base leading-4 text-gray-900 tracking-[-0.3125px]"
              >
                Chỉ số sinh tồn
              </h3>
            </div>
            <div class="grid grid-cols-2 gap-x-4 gap-y-4">
              <!-- Temperature -->
              <div class="flex flex-col gap-1">
                <label
                  class="flex items-center gap-1 text-sm leading-5 text-[#364153] tracking-[-0.1504px]"
                >
                  <!-- <img :src="icons.thermometer" alt="" class="w-4 h-4" /> -->
                  Nhiệt độ (°C)
                </label>
                <input
                  v-model="vitalSigns.temperature"
                  type="text"
                  class="h-9 bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 text-sm text-[#717182] tracking-[-0.1504px] focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Weight -->
              <div class="flex flex-col gap-1">
                <label
                  class="flex items-center gap-1 text-sm leading-5 text-[#364153] tracking-[-0.1504px]"
                >
                  <!-- <img :src="icons.weight" alt="" class="w-4 h-4" /> -->
                  Cân nặng (kg)
                </label>
                <input
                  v-model="vitalSigns.weight"
                  type="text"
                  class="h-9 bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 text-sm text-[#717182] tracking-[-0.1504px] focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Heart Rate -->
              <div class="flex flex-col gap-1">
                <label
                  class="flex items-center gap-1 text-sm leading-5 text-[#364153] tracking-[-0.1504px]"
                >
                  <!-- <img :src="icons.heartbeat" alt="" class="w-4 h-4" /> -->
                  Nhịp tim (bpm)
                </label>
                <input
                  v-model="vitalSigns.heartRate"
                  type="text"
                  class="h-9 bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 text-sm text-[#717182] tracking-[-0.1504px] focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Respiratory Rate -->
              <div class="flex flex-col gap-1">
                <label
                  class="flex items-center gap-1 text-sm leading-5 text-[#364153] tracking-[-0.1504px]"
                >
                  <!-- <img :src="icons.lungs" alt="" class="w-4 h-4" /> -->
                  Nhịp thở (/phút)
                </label>
                <input
                  v-model="vitalSigns.respiratoryRate"
                  type="text"
                  class="h-9 bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 text-sm text-[#717182] tracking-[-0.1504px] focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>
          </div>

          <!-- Reason for Visit Card -->
          <div
            class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-[25px]"
          >
            <div class="flex items-center gap-2 mb-[30px]">
              <!-- <img :src="icons.reason" alt="" class="w-4 h-4" /> -->
              <h3
                class="font-normal text-base leading-4 text-gray-900 tracking-[-0.3125px]"
              >
                Lý do đến khám
              </h3>
            </div>
            <textarea
              v-model="reasonForVisit"
              rows="3"
              class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 text-sm text-[#717182] leading-5 tracking-[-0.1504px] resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Mô tả triệu chứng của thú cưng (từ lời kể của chủ hoặc quan sát)..."
            ></textarea>
          </div>

          <!-- Symptoms Card -->
          <div
            class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-[25px]"
          >
            <div class="flex items-center gap-2 mb-[30px]">
              <!-- <img :src="icons.symptoms" alt="" class="w-4 h-4" /> -->
              <h3
                class="font-normal text-base leading-4 text-gray-900 tracking-[-0.3125px]"
              >
                Triệu chứng
              </h3>
            </div>
            <textarea
              v-model="symptoms"
              rows="3"
              class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 text-sm text-[#717182] leading-5 tracking-[-0.1504px] resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Mô tả triệu chứng của thú cưng (từ lời kể của chủ hoặc quan sát)..."
            ></textarea>
          </div>

          <!-- Lab Results Card (Blue highlight) -->
          <div
            class="bg-blue-50 border-2 border-[#bedbff] rounded-[14px] p-[26px]"
          >
            <div class="flex items-center gap-2 mb-[30px]">
              <!-- <img :src="icons.activity" alt="" class="w-4 h-4" /> -->
              <h3
                class="font-normal text-base leading-4 text-gray-900 tracking-[-0.3125px]"
              >
                KẾT QUẢ CẬN LÂM SÀNG
              </h3>
            </div>
            <div class="bg-white border !border-[#bedbff] rounded-[10px] p-3">
              <div class="flex flex-col gap-1.5 mb-3">
                <p
                  class="font-bold text-sm leading-5 text-[#101828] tracking-[-0.1504px]"
                >
                  1. Chụp X-Quang Xương:
                </p>
                <p
                  class="font-normal text-sm leading-5 text-[#364153] tracking-[-0.1504px]"
                >
                  Gãy xương đùi trái (kín)
                </p>
                <p class="font-normal text-xs leading-4 text-[#6a7282]">
                  Hoàn thành lúc 10:45
                </p>
              </div>
              <button
                @click="isKQCanLamSangModalOpen = true"
                class="bg-white border !border-[#8ec5ff] rounded-lg px-4 py-1.5 text-sm font-medium text-[#155dfc] tracking-[-0.1504px] hover:bg-blue-50 transition-colors"
              >
                Xem ảnh (2)
              </button>
            </div>
          </div>

          <!-- Diagnosis Card (Required) -->
          <div
            class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-[25px]"
          >
            <div class="flex items-center gap-2 mb-[30px]">
              <!-- <img :src="icons.diagnosis" alt="" class="w-4 h-4" /> -->
              <h3
                class="font-normal text-base leading-4 text-gray-900 tracking-[-0.3125px]"
              >
                Chẩn đoán
              </h3>
              <span class="text-base text-[#e7000b] tracking-[-0.3125px]"
                >*</span
              >
            </div>
            <textarea
              v-model="diagnosis"
              rows="3"
              class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 text-sm text-[#717182] leading-5 tracking-[-0.1504px] resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Nhập chẩn đoán bệnh..."
            ></textarea>
          </div>

          <!-- Notes Card -->
          <div
            class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-[25px]"
          >
            <div class="flex items-center gap-2 mb-[30px]">
              <h3
                class="font-normal text-base leading-4 text-gray-900 tracking-[-0.3125px]"
              >
                Ghi Chú
              </h3>
            </div>
            <textarea
              v-model="notes"
              rows="3"
              class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 text-sm text-[#717182] leading-5 tracking-[-0.1504px] resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Mô tả phương pháp điều trị (nếu có)..."
            ></textarea>
          </div>
        </div>

        <!-- Right Column: Action Buttons -->
        <div class="flex flex-col gap-6 w-[358px]">
          <!-- Lab Test Order Button -->
          <button
            @click="isChiDinhModalOpen = true"
            :class="[
              'h-9 rounded-lg flex items-center justify-center gap-2 text-sm font-medium tracking-[-0.1504px] transition-colors',
              selectedPrescriptionType === 'chi_dinh_can_lam_sang'
                ? 'bg-[#dab2ff] border-2 border-[#8200db] text-[#8200db]'
                : 'bg-white border-2 !border-[#dab2ff] text-[#8200db] hover:bg-purple-50',
            ]"
          >
            <!-- <img :src="icons.labTest" alt="" class="w-4 h-4" /> -->
            Chỉ định cận lâm sàng
          </button>

          <!-- Prescription Button -->
          <button
            @click="isDonThuocModalOpen = true"
            :class="[
              'h-9 rounded-lg flex items-center justify-center gap-2 text-sm font-medium tracking-[-0.1504px] transition-colors',
              selectedPrescriptionType === 'don_thuoc'
                ? 'bg-[#7bf1a8] border-2 border-[#008236] text-[#008236]'
                : 'bg-white border-2 !border-[#7bf1a8] text-[#008236] hover:bg-green-50',
            ]"
          >
            <!-- <img :src="icons.prescription" alt="" class="w-4 h-4" /> -->
            Đơn thuốc
          </button>

          <!-- Follow-up Appointment Button -->
          <button
            @click="isHenTaiKhamModalOpen = true"
            :class="[
              'h-9 rounded-lg flex items-center justify-center gap-2 text-sm font-medium tracking-[-0.1504px] transition-colors',
              selectedPrescriptionType === 'hen_tai_kham'
                ? 'bg-[#53eafd] border-2 border-[#007595] text-[#007595]'
                : 'bg-white border-2 !border-[#53eafd] text-[#007595] hover:bg-cyan-50',
            ]"
          >
            <!-- <img :src="icons.followUp" alt="" class="w-4 h-4" /> -->
            Hẹn Tái Khám
          </button>

          <!-- Complete & Transfer Button -->
          <button
            class="bg-[#5a9690] text-white rounded-lg px-8 py-3 text-sm font-medium text-center tracking-[-0.1504px] hover:bg-[#4a857f] transition-colors"
          >
            Hoàn tất & Chuyển thu ngân
          </button>
        </div>
      </div>
    </div>
    <!-- End of v-else -->

    <!-- Chi Dinh Modal -->
    <div
      v-if="isChiDinhModalOpen"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
      @click.self="isChiDinhModalOpen = false"
    >
      <div class="w-full max-w-2xl mx-4">
        <ChiDinh
          @close="isChiDinhModalOpen = false"
          @save="handleChiDinhSave"
        />
      </div>
    </div>

    <!-- Don Thuoc Modal -->
    <div
      v-if="isDonThuocModalOpen"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
      @click.self="isDonThuocModalOpen = false"
    >
      <div class="w-full max-w-2xl mx-4">
        <DonThuoc
          @close="isDonThuocModalOpen = false"
          @save="handleDonThuocSave"
        />
      </div>
    </div>

    <!-- Hen Tai Kham Modal -->
    <div
      v-if="isHenTaiKhamModalOpen"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
      @click.self="isHenTaiKhamModalOpen = false"
    >
      <div class="w-full max-w-2xl mx-4">
        <HenTaiKham
          @close="isHenTaiKhamModalOpen = false"
          @save="handleHenTaiKhamSave"
        />
      </div>
    </div>

    <!-- KQ Can Lam Sang Modal -->
    <div
      v-if="isKQCanLamSangModalOpen"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
      @click.self="isKQCanLamSangModalOpen = false"
    >
      <div class="w-full max-w-2xl mx-4">
        <KQCanLamSang @close="isKQCanLamSangModalOpen = false" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "@/utils/api";
import { format } from "date-fns";
import { vi } from "date-fns/locale";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
import ChiDinh from "./ChiDinh/index.vue";
import DonThuoc from "./DonThuoc/index.vue";
import HenTaiKham from "./HenTaiKham/index.vue";
import KQCanLamSang from "./KQCanLamSang/index.vue";
//Icon SVG
import ArrowLeftIcon from "@/assets/svg/arrow-left.svg";

const router = useRouter();
const route = useRoute();

// Get appointment ID from route params
const appointmentId = computed(() => route.params.id);

// Loading state
const loading = ref(true);
const saving = ref(false);

// Icons from Figma
const icons = {
  arrowLeft:
    "https://www.figma.com/api/mcp/asset/76b11aab-6817-4105-94c7-1180dbbb1fb3",
  save: "https://www.figma.com/api/mcp/asset/f76bae8e-6f66-4f28-acaa-19150935c4f6",
  userPurple:
    "https://www.figma.com/api/mcp/asset/af4ede92-6228-4dd1-99db-eff9ed2b1d20",
  phone:
    "https://www.figma.com/api/mcp/asset/5a79a9a9-0485-4d94-a78b-4e4b35306ff5",
  calendar:
    "https://www.figma.com/api/mcp/asset/2801329b-9e9f-418d-95bb-7c96e481db4a",
  clock:
    "https://www.figma.com/api/mcp/asset/68ca25c2-7947-465e-af9d-0418216a9c45",
  stethoscope:
    "https://www.figma.com/api/mcp/asset/42eb7cf4-83ba-45b6-8e8f-0a2e139d2738",
  activity:
    "https://www.figma.com/api/mcp/asset/868e59e4-5a5c-47b8-a0e0-4a286cbbf945",
  thermometer:
    "https://www.figma.com/api/mcp/asset/0ff425ce-e518-46b7-8a11-cf3cae0a436c",
  weight:
    "https://www.figma.com/api/mcp/asset/65ebcc68-bd19-45bb-ad10-865dac201c71",
  heartbeat:
    "https://www.figma.com/api/mcp/asset/be36d78b-ae2e-4792-bd5b-d92799ae00c0",
  lungs:
    "https://www.figma.com/api/mcp/asset/2749b6bc-762e-4b59-913b-377fb8b0e5ec",
  reason:
    "https://www.figma.com/api/mcp/asset/c0305605-27e4-4f30-a91b-af51a1287fcc",
  symptoms:
    "https://www.figma.com/api/mcp/asset/618349e3-61b4-4047-bfd0-76286998bf8d",
  diagnosis:
    "https://www.figma.com/api/mcp/asset/4179807a-a764-4d16-b5e9-d873e8edd7c4",
  labTest:
    "https://www.figma.com/api/mcp/asset/53df81c1-f80d-4947-b736-a5349c435d8d",
  prescription:
    "https://www.figma.com/api/mcp/asset/6ddb03bc-f4a6-4e07-aae5-e62d607a9fa1",
  followUp:
    "https://www.figma.com/api/mcp/asset/2e0d530a-e641-49ef-b197-02399437f53d",
};

// Patient Information
const patientInfo = ref({
  petName: "",
  petImage: "",
  badge: "Đặt trước",
  species: "",
  breed: "",
  age: "",
  ownerName: "",
  ownerPhone: "",
  appointmentDate: "",
  appointmentTime: "",
  service: "",
});

// Vital Signs
const vitalSigns = ref({
  temperature: "",
  weight: "",
  heartRate: "",
  respiratoryRate: "",
});

// Form Data
const reasonForVisit = ref("");
const symptoms = ref("");
const diagnosis = ref("");
const notes = ref("");

// Loại chỉ định được chọn
const selectedPrescriptionType = ref("don_thuoc"); // default to prescription

// Modal states
const isChiDinhModalOpen = ref(false);
const isDonThuocModalOpen = ref(false);
const isHenTaiKhamModalOpen = ref(false);
const isKQCanLamSangModalOpen = ref(false);

// Helper function to parse datetime
const parseDateTime = (dateString) => {
  if (!dateString) return null;
  try {
    if (typeof dateString === "string") {
      return new Date(dateString.replace(" ", "T"));
    }
    return new Date(dateString);
  } catch (error) {
    console.error("Error parsing datetime:", dateString, error);
    return null;
  }
};

// Calculate age from birth date
const calculateAge = (birthDate) => {
  if (!birthDate) return "Chưa rõ";
  const years = new Date().getFullYear() - new Date(birthDate).getFullYear();
  return `${years} tuổi`;
};

// Load appointment data
const loadAppointmentData = async () => {
  loading.value = true;
  try {
    const response = await api.get(`/lich-hen/${appointmentId.value}`);

    console.log("=== Appointment API Response ===");
    console.log("Full response:", response.data);

    if (response.data.status && response.data.data) {
      const data = response.data.data;

      console.log("Appointment data:", data);
      console.log("Thu cung data:", data.thu_cung);
      console.log("Khach hang:", data.khach_hang);
      console.log("Dich vu:", data.dich_vu);

      // Parse datetime
      const appointmentDateTime = parseDateTime(data.ngay_gio);
      const checkInDateTime = parseDateTime(data.thoi_gian_checkin);

      // Nếu thu_cung null hoặc không có tên, mới cần fetch riêng
      let petData = data.thu_cung;
      if (data.thu_cung_id && (!data.thu_cung || !data.thu_cung.ten)) {
        try {
          const petResponse = await api.get(`/thu-cung/${data.thu_cung_id}`);
          if (petResponse.data.status && petResponse.data.data) {
            petData = petResponse.data.data;
            console.log("Pet data fetched separately:", petData);
          }
        } catch (petError) {
          console.error("Error fetching pet data:", petError);
        }
      }

      // Log thu_cung details (ưu tiên các trường đã được BE chuẩn hóa)
      if (petData) {
        console.log("Pet details:", {
          ten: petData.ten || petData.ten_thu_cung,
          loai: petData.loai || petData.species,
          giong: petData.giong || petData.giong_loai || petData.breed,
          ngay_sinh: petData.ngay_sinh,
          anh: petData.anh_dai_dien,
        });
      }

      // Update patient info: lấy trực tiếp các trường đã được BE chuẩn hóa
      patientInfo.value = {
        petName: petData?.ten || petData?.ten_thu_cung || "Chưa có tên",
        petImage: petData?.anh_dai_dien || "https://via.placeholder.com/150",
        badge: data.la_khach_vang_lai ? "Vãng lai" : "Đặt trước",
        species: petData?.loai || petData?.species || "Chưa rõ loài",
        breed:
          petData?.giong ||
          petData?.giong_loai ||
          petData?.breed ||
          "Chưa rõ giống",
        age: petData?.ngay_sinh
          ? calculateAge(petData.ngay_sinh)
          : "Chưa rõ tuổi",
        ownerName:
          data.khach_hang || data.khachHang?.full_name || "Chưa có tên",
        ownerPhone:
          data.khach_hang_info?.so_dien_thoai ||
          data.khachHang?.so_dien_thoai ||
          "Chưa có SĐT",
        appointmentDate: appointmentDateTime
          ? format(appointmentDateTime, "dd/MM/yyyy", { locale: vi })
          : "",
        appointmentTime: checkInDateTime
          ? format(checkInDateTime, "HH:mm", { locale: vi })
          : appointmentDateTime
          ? format(appointmentDateTime, "HH:mm", { locale: vi })
          : "",
        service: data.dich_vu?.ten || data.dichVu?.ten || "Khám tổng quát",
      };

      // Load existing notes if any
      if (data.ghi_chu) {
        notes.value = data.ghi_chu;
      }

      console.log("=== Patient Info Loaded ===");
      console.log("Final patient info:", patientInfo.value);
    } else {
      console.error("Invalid response structure:", response.data);
      showErrorToast("Dữ liệu không hợp lệ");
    }
  } catch (error) {
    console.error("=== Error Loading Appointment ===");
    console.error("Error:", error);
    console.error("Response:", error.response?.data);
    showErrorToast(
      error.response?.data?.message || "Lỗi khi tải thông tin lịch hẹn"
    );
  } finally {
    loading.value = false;
  }
};

// Methods
const handleBack = () => {
  router.push("/doctor/lich-kham");
};

const handleSave = async () => {
  // Validate required fields
  if (!diagnosis.value.trim()) {
    showErrorToast("Vui lòng nhập chẩn đoán bệnh");
    return;
  }

  saving.value = true;
  try {
    // Prepare data according to backend API spec
    const phieuKhamData = {
      lich_hen_id: appointmentId.value,
      // Vital signs (nullable)
      nhiet_do: vitalSigns.value.temperature
        ? parseFloat(vitalSigns.value.temperature)
        : null,
      can_nang: vitalSigns.value.weight
        ? parseFloat(vitalSigns.value.weight)
        : null,
      nhip_tim: vitalSigns.value.heartRate
        ? parseInt(vitalSigns.value.heartRate)
        : null,
      nhip_tho: vitalSigns.value.respiratoryRate
        ? parseInt(vitalSigns.value.respiratoryRate)
        : null,
      // Medical information (nullable)
      ly_do_den_kham: reasonForVisit.value || null,
      trieu_chung: symptoms.value || null,
      chan_doan: diagnosis.value, // Required
      ghi_chu: notes.value || null,
      // Loại chỉ định (required) - use selected type
      loai_chi_dinh: selectedPrescriptionType.value,
    };

    console.log("=== Saving Phiếu Khám ===");
    console.log("Data:", phieuKhamData);

    // Call backend API to save examination record
    const response = await api.post("/phieu-kham", phieuKhamData);

    console.log("=== Save Response ===");
    console.log("Response:", response.data);

    if (response.data.status || response.status === 201) {
      showSuccessToast(
        response.data.message || "Lưu hồ sơ khám bệnh thành công!"
      );

      // Optionally redirect after saving
      setTimeout(() => {
        router.push("/doctor/lich-kham");
      }, 1500);
    } else {
      showErrorToast(response.data.message || "Lỗi khi lưu hồ sơ khám bệnh");
    }
  } catch (error) {
    console.error("=== Error Saving Examination ===");
    console.error("Error:", error);
    console.error("Response:", error.response?.data);

    const errorMessage =
      error.response?.data?.message ||
      error.response?.data?.errors?.lich_hen_id?.[0] ||
      "Lỗi khi lưu hồ sơ khám bệnh";

    showErrorToast(errorMessage);
  } finally {
    saving.value = false;
  }
};

// Handle Chi Dinh modal save
const handleChiDinhSave = (data) => {
  console.log("Chi Dinh saved:", data);
  selectedPrescriptionType.value = "chi_dinh_can_lam_sang";
  isChiDinhModalOpen.value = false;
  showSuccessToast("Đã lưu chỉ định cận lâm sàng");
};

// Handle Don Thuoc modal save
const handleDonThuocSave = (data) => {
  console.log("Don Thuoc saved:", data);
  selectedPrescriptionType.value = "don_thuoc";
  isDonThuocModalOpen.value = false;
  showSuccessToast("Đã lưu đơn thuốc");
};

// Handle Hen Tai Kham modal save
const handleHenTaiKhamSave = (data) => {
  console.log("Hen Tai Kham saved:", data);
  selectedPrescriptionType.value = "hen_tai_kham";
  isHenTaiKhamModalOpen.value = false;
  showSuccessToast("Đã lưu lịch hẹn tái khám");
};

// Load data on mount
onMounted(() => {
  if (appointmentId.value) {
    loadAppointmentData();
  } else {
    showErrorToast("Không tìm thấy ID lịch hẹn");
    router.push("/doctor/lich-kham");
  }
});
</script>
