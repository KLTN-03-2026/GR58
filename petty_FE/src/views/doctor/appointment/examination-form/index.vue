<template>
  <div class="ef-root">
    <!-- Loading -->
    <div
      v-if="loading"
      class="flex flex-col items-center justify-center py-24 gap-4"
    >
      <div
        class="w-10 h-10 rounded-full border-2 border-slate-200 border-t-[#0a3161] animate-spin"
      ></div>
      <p
        class="text-sm text-slate-500 font-medium"
        style="font-family: 'Plus Jakarta Sans', sans-serif"
      >
        Đang tải thông tin bệnh nhân...
      </p>
    </div>

    <div v-else class="ef-layout">
      <!-- Header -->
      <header class="ef-header">
        <div class="flex items-center gap-5">
          <button class="ef-btn-back" @click="handleBack">
            <svg
              class="w-4 h-4"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15 19l-7-7 7-7"
              />
            </svg>
            Quay lại
          </button>
          <div>
            <p class="ef-eyebrow">Phiếu khám bệnh</p>
            <h1 class="ef-title">{{ pageTitle }}</h1>
          </div>
        </div>
        <button
          class="ef-btn-save"
          @click="handleSave"
          :disabled="saving || isViewMode"
          v-if="!isViewMode"
        >
          <span
            v-if="saving"
            class="w-3.5 h-3.5 rounded-full border-2 border-white/30 border-t-white animate-spin"
          ></span>
          <svg
            v-else
            class="w-4 h-4"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M5 13l4 4L19 7"
            />
          </svg>
          {{ saving ? "Đang lưu..." : "Lưu hồ sơ" }}
        </button>
      </header>

      <!-- Patient Banner -->
      <div class="ef-banner">
        <div class="ef-banner-glow"></div>
        <div class="ef-banner-inner">
          <!-- Pet Avatar -->
          <div class="ef-avatar-wrap">
            <img
              :src="patientInfo.petImage"
              @error="(e) => (e.target.src = DEFAULT_PET_IMAGE)"
              alt=""
              class="ef-avatar"
            />
          </div>
          <!-- Pet Info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-1.5">
              <h2 class="ef-pet-name">{{ patientInfo.petName }}</h2>
              <span class="ef-badge">{{ patientInfo.badge }}</span>
            </div>
            <p class="ef-pet-meta">
              {{ patientInfo.species }} · {{ patientInfo.breed }} ·
              {{ patientInfo.age }}
            </p>
          </div>
          <!-- Divider -->
          <div class="w-px h-14 bg-white/15 mx-2 flex-shrink-0"></div>
          <!-- Owner -->
          <div class="ef-tile">
            <p class="ef-tile-label">Chủ nuôi</p>
            <p class="ef-tile-value">{{ patientInfo.ownerName }}</p>
            <p class="ef-tile-sub">{{ patientInfo.ownerPhone }}</p>
          </div>
          <!-- Divider -->
          <div class="w-px h-14 bg-white/15 mx-2 flex-shrink-0"></div>
          <!-- Appointment -->
          <div class="ef-tile">
            <p class="ef-tile-label">Lịch khám</p>
            <p class="ef-tile-value">{{ patientInfo.service }}</p>
            <p class="ef-tile-sub">
              {{ patientInfo.appointmentDate }} ·
              {{ patientInfo.appointmentTime }}
            </p>
          </div>
        </div>
        <!-- Attachment uploader (after save) -->
        <Transition name="ef-fade">
          <ClinicalAttachmentUploader
            v-if="savedPhieuKhamId"
            :phieu-kham-id="savedPhieuKhamId"
            class="mt-5"
          />
        </Transition>
      </div>

      <!-- Body -->
      <div class="ef-body">
        <!-- Left Column -->
        <div class="ef-col-left">
          <!-- Dịch vụ đã thực hiện -->
          <section v-if="serviceChecklist.length > 0" class="ef-card">
            <div class="ef-card-header">
              <span class="ef-icon" style="background: #e0f2fe; color: #0369a1;">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
              </span>
              <h3 class="ef-card-title">Dịch vụ đã thực hiện</h3>
              <span class="ml-auto text-xs font-medium" :class="allServicesCompleted ? 'text-emerald-600' : 'text-amber-600'">
                {{ completedServicesCount }}/{{ serviceChecklist.length }}
              </span>
            </div>
            <div class="flex flex-col gap-2 mt-1">
              <label
                v-for="(svc, idx) in serviceChecklist"
                :key="idx"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors"
                :class="svc.done ? 'bg-emerald-50 !border-emerald-200' : '!border-slate-200 hover:bg-slate-50'"
              >
                <input
                  type="checkbox"
                  v-model="svc.done"
                  :disabled="isViewMode || saving"
                  @change="handleServiceChecklistChange"
                  class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                />
                <span class="text-sm font-medium" :class="svc.done ? 'text-emerald-700' : 'text-slate-700'">
                  {{ svc.name }}
                </span>
                <span v-if="svc.done" class="ml-auto text-xs text-emerald-500 font-medium">Xong</span>
              </label>
            </div>
            <p v-if="!allServicesCompleted" class="text-xs text-amber-600 mt-2">
              * Tick tất cả dịch vụ trước khi hoàn tất khám
            </p>
          </section>

          <!-- Vital Signs -->
          <section class="ef-card">
            <div class="ef-card-header">
              <span class="ef-icon ef-icon-amber"
                ><svg
                  class="w-3.5 h-3.5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" /></svg
              ></span>
              <h3 class="ef-card-title">Chỉ số sinh tồn</h3>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div class="ef-vital">
                <label class="ef-vital-label">Nhiệt độ</label>
                <div class="ef-vital-input-wrap">
                  <input
                    :value="vitalSigns.temperature"
                    @input="
                      onVitalInput('temperature', $event.target.value, 'decimal')
                    "
                    @keydown="preventInvalidVitalKeydown($event, 'decimal')"
                    @paste="handleVitalPaste($event, 'decimal')"
                    type="text"
                    inputmode="decimal"
                    placeholder="38.5"
                    class="ef-vital-input"
                    :class="{ 'ef-vital-input--error': fieldErrors.temperature }"
                    :disabled="isViewMode || saving"
                  />
                  <span class="ef-vital-unit">°C</span>
                </div>
                <p v-if="fieldErrors.temperature" class="ef-field-error">
                  {{ fieldErrors.temperature }}
                </p>
              </div>
              <div class="ef-vital">
                <label class="ef-vital-label">Cân nặng</label>
                <div class="ef-vital-input-wrap">
                  <input
                    :value="vitalSigns.weight"
                    @input="onVitalInput('weight', $event.target.value, 'decimal')"
                    @keydown="preventInvalidVitalKeydown($event, 'decimal')"
                    @paste="handleVitalPaste($event, 'decimal')"
                    type="text"
                    inputmode="decimal"
                    placeholder="4.2"
                    class="ef-vital-input"
                    :class="{ 'ef-vital-input--error': fieldErrors.weight }"
                    :disabled="isViewMode || saving"
                  />
                  <span class="ef-vital-unit">kg</span>
                </div>
                <p v-if="fieldErrors.weight" class="ef-field-error">
                  {{ fieldErrors.weight }}
                </p>
              </div>
              <div class="ef-vital">
                <label class="ef-vital-label">Nhịp tim</label>
                <div class="ef-vital-input-wrap">
                  <input
                    :value="vitalSigns.heartRate"
                    @input="
                      onVitalInput('heartRate', $event.target.value, 'integer')
                    "
                    @keydown="preventInvalidVitalKeydown($event, 'integer')"
                    @paste="handleVitalPaste($event, 'integer')"
                    type="text"
                    inputmode="numeric"
                    placeholder="80"
                    class="ef-vital-input"
                    :class="{ 'ef-vital-input--error': fieldErrors.heartRate }"
                    :disabled="isViewMode || saving"
                  />
                  <span class="ef-vital-unit">bpm</span>
                </div>
                <p v-if="fieldErrors.heartRate" class="ef-field-error">
                  {{ fieldErrors.heartRate }}
                </p>
              </div>
              <div class="ef-vital">
                <label class="ef-vital-label">Nhịp thở</label>
                <div class="ef-vital-input-wrap">
                  <input
                    :value="vitalSigns.respiratoryRate"
                    @input="
                      onVitalInput(
                        'respiratoryRate',
                        $event.target.value,
                        'integer'
                      )
                    "
                    @keydown="preventInvalidVitalKeydown($event, 'integer')"
                    @paste="handleVitalPaste($event, 'integer')"
                    type="text"
                    inputmode="numeric"
                    placeholder="20"
                    class="ef-vital-input"
                    :class="{
                      'ef-vital-input--error': fieldErrors.respiratoryRate,
                    }"
                    :disabled="isViewMode || saving"
                  />
                  <span class="ef-vital-unit">/phút</span>
                </div>
                <p v-if="fieldErrors.respiratoryRate" class="ef-field-error">
                  {{ fieldErrors.respiratoryRate }}
                </p>
              </div>
            </div>
          </section>

          <!-- Reason -->
          <section class="ef-card">
            <div class="ef-card-header">
              <span class="ef-icon ef-icon-violet"
                ><svg
                  class="w-3.5 h-3.5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                  /></svg
              ></span>
              <h3 class="ef-card-title">Lý do đến khám</h3>
            </div>
            <textarea
              v-model="reasonForVisit"
              rows="3"
              class="ef-textarea"
              placeholder="Mô tả lý do từ lời kể của chủ nuôi..."
              :disabled="isViewMode || saving"
            ></textarea>
          </section>

          <!-- Symptoms -->
          <section class="ef-card">
            <div class="ef-card-header">
              <span class="ef-icon ef-icon-rose"
                ><svg
                  class="w-3.5 h-3.5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                  /></svg
              ></span>
              <h3 class="ef-card-title">Triệu chứng</h3>
            </div>
            <textarea
              v-model="symptoms"
              rows="3"
              class="ef-textarea"
              placeholder="Quan sát và ghi nhận triệu chứng lâm sàng..."
              :disabled="isViewMode || saving"
            ></textarea>
          </section>

          <!-- Diagnosis -->
          <section class="ef-card ef-card-diagnosis">
            <div class="ef-card-header">
              <span class="ef-icon ef-icon-emerald"
                ><svg
                  class="w-3.5 h-3.5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  /></svg
              ></span>
              <h3 class="ef-card-title">
                Chẩn đoán
                <span v-if="requiresExamForAppointment" class="text-red-500 ml-0.5"
                  >*</span
                >
              </h3>
            </div>
            <textarea
              v-model="diagnosis"
              rows="3"
              class="ef-textarea ef-textarea-diag"
              placeholder="Nhập chẩn đoán bệnh..."
              :disabled="isViewMode || saving"
            ></textarea>
          </section>

          <!-- Notes -->
          <section class="ef-card">
            <div class="ef-card-header">
              <span class="ef-icon ef-icon-yellow"
                ><svg
                  class="w-3.5 h-3.5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                  /></svg
              ></span>
              <h3 class="ef-card-title">Ghi chú điều trị</h3>
            </div>
            <textarea
              v-model="notes"
              rows="3"
              class="ef-textarea"
              placeholder="Phác đồ, lưu ý đặc biệt sau khám..."
              :disabled="isViewMode || saving"
            ></textarea>
          </section>

        </div>

        <!-- Right: Action Panel -->
        <aside class="ef-col-right">
          <div class="ef-action-panel">
            <p class="ef-action-title">Thao tác</p>

            <button
              @click="isPrescriptionFormModalOpen = true"
              :class="[
                'ef-action-btn',
                selectedPrescriptionType === 'don_thuoc'
                  ? 'ef-action-btn--active-green'
                  : 'ef-action-btn--green',
              ]"
              :disabled="isViewMode || saving"
            >
              <span class="ef-action-icon">
                <svg
                  class="w-4 h-4"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  />
                </svg>
              </span>
              <div class="text-left text-current">
                <p class="text-sm font-semibold leading-5 text-current">
                  Kê đơn thuốc
                </p>
                <p class="text-xs opacity-70 leading-4 text-current">
                  {{
                    selectedPrescriptionType === "don_thuoc"
                      ? "✓ Đã thêm đơn thuốc"
                      : "Thêm toa thuốc cho bệnh nhân"
                  }}
                </p>
              </div>
            </button>

            <button
              disabled
              class="ef-action-btn ef-action-btn--disabled opacity-50 cursor-not-allowed"
              title="Tính năng đang phát triển"
            >
              <span class="ef-action-icon">
                <svg
                  class="w-4 h-4"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
              </span>
              <div class="text-left text-slate-400">
                <p class="text-sm font-semibold leading-5">
                  Hẹn tái khám
                </p>
                <p class="text-xs opacity-70 leading-4">
                  Đang phát triển
                </p>
              </div>
            </button>

            <div class="border-t border-slate-100 my-1"></div>

            <button
              :class="['ef-action-btn', allServicesCompleted ? 'ef-action-btn--complete' : 'ef-action-btn--disabled']"
              :disabled="!allServicesCompleted || isViewMode || saving"
              @click="hoanTatVaChuyenThuNgan"
              v-if="!isViewMode"
            >
              <span class="ef-action-icon">
                <svg
                  class="w-4 h-4"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                  />
                </svg>
              </span>
              <div class="text-left" :class="allServicesCompleted ? 'text-white' : 'text-slate-400'">
                <p class="text-sm font-semibold leading-5">
                  Hoàn tất & Chuyển thu ngân
                </p>
                <p class="text-xs opacity-70 leading-4">
                  {{ allServicesCompleted ? 'Kết thúc ca khám, tạo hóa đơn' : 'Tick tất cả dịch vụ trước' }}
                </p>
              </div>
            </button>
          </div>
        </aside>
      </div>
    </div>

    <!-- Don Thuoc Modal -->
    <div
      v-if="isPrescriptionFormModalOpen && !isViewMode"
      class="ef-modal"
      @click.self="isPrescriptionFormModalOpen = false"
    >
      <div class="w-full max-w-5xl mx-4">
        <DonThuoc
          :initial-data="donThuocData"
          @close="isPrescriptionFormModalOpen = false"
          @save="handlePrescriptionFormSave"
        />
      </div>
    </div>
    <!-- Hen Tai Kham Modal -->
    <div
      v-if="isFollowUpModalOpen"
      class="ef-modal"
      @click.self="isFollowUpModalOpen = false"
    >
      <div class="w-full max-w-2xl mx-4">
        <HenTaiKham
          @close="isFollowUpModalOpen = false"
          @save="handleFollowUpSave"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "@/utils/api";
import { resolveImageUrl } from "@/utils/image";
import { format } from "date-fns";
import { vi } from "date-fns/locale";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
import * as phieuKhamService from "@/services/phieuKhamService";
import DonThuoc from "./prescription-form/index.vue";
import HenTaiKham from "./follow-up-appointment/index.vue";
import ClinicalAttachmentUploader from "@/components/doctor/ClinicalAttachmentUploader.vue";

// Ảnh mặc định cho thú cưng khi chưa có ảnh
const DEFAULT_PET_IMAGE =
  "https://www.figma.com/api/mcp/asset/7dc3f4c9-30fd-4f46-b415-7a1aab552e01";

const router = useRouter();
const route = useRoute();

// Get appointment ID from route params
const appointmentId = computed(() => route.params.id);

// Loading state
const loading = ref(true);
const saving = ref(false);
const examMode = ref("create");
const pageTitle = computed(() =>
  examMode.value === "view" ? "Xem kết quả khám" : "Nhập thông tin khám"
);
const isViewMode = computed(() => examMode.value === "view");

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
const fieldErrors = ref({
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

// Service checklist
const serviceChecklist = ref([]);
const allServicesCompleted = computed(() => serviceChecklist.value.length > 0 && serviceChecklist.value.every(s => s.done));
const completedServicesCount = computed(() => serviceChecklist.value.filter(s => s.done).length);
const appointmentServiceNames = ref([]);

// Loại chỉ định được chọn
const selectedPrescriptionType = ref("don_thuoc"); // default to prescription

// Modal states
const isPrescriptionFormModalOpen = ref(false);
const isFollowUpModalOpen = ref(false);

// ID phiếu khám sau khi lưu lần đầu — dùng cho uploader đính kèm
const savedPhieuKhamId = ref(null);
const existingPhieuKhamId = ref(null);
const localChecklistKey = computed(() => `doctor_exam_checklist_${appointmentId.value || "unknown"}`);

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

const sanitizeDecimalInput = (value) => {
  const normalized = String(value ?? "")
    .replace(",", ".")
    .replace(/[^0-9.]/g, "");
  const firstDot = normalized.indexOf(".");
  if (firstDot === -1) return normalized;
  return (
    normalized.slice(0, firstDot + 1) +
    normalized.slice(firstDot + 1).replace(/\./g, "")
  );
};

const sanitizeIntegerInput = (value) => String(value ?? "").replace(/\D/g, "");

const onVitalInput = (field, rawValue, numericType) => {
  const invalidPattern = numericType === "decimal" ? /[^0-9.,]/ : /[^0-9]/;
  if (invalidPattern.test(String(rawValue ?? ""))) {
    fieldErrors.value[field] = "Chỉ được nhập số";
  } else {
    fieldErrors.value[field] = "";
  }

  vitalSigns.value[field] =
    numericType === "decimal"
      ? sanitizeDecimalInput(rawValue)
      : sanitizeIntegerInput(rawValue);
};

const preventInvalidVitalKeydown = (event, numericType) => {
  const allowedControlKeys = [
    "Backspace",
    "Delete",
    "ArrowLeft",
    "ArrowRight",
    "Tab",
    "Home",
    "End",
    "Enter",
  ];
  if (allowedControlKeys.includes(event.key) || event.ctrlKey || event.metaKey) {
    return;
  }

  const isDigit = /^[0-9]$/.test(event.key);
  const isDecimalSeparator = event.key === "." || event.key === ",";
  const allowDecimal = numericType === "decimal";

  if (isDigit || (allowDecimal && isDecimalSeparator)) {
    return;
  }

  event.preventDefault();
};

const handleVitalPaste = (event, numericType) => {
  const pasted = event.clipboardData?.getData("text") ?? "";
  const pattern = numericType === "decimal" ? /^[0-9.,]+$/ : /^[0-9]+$/;
  if (!pattern.test(pasted)) {
    event.preventDefault();
  }
};

const validateVitalSigns = () => {
  fieldErrors.value = {
    temperature: "",
    weight: "",
    heartRate: "",
    respiratoryRate: "",
  };

  const validators = [
    {
      field: "temperature",
      label: "Nhiệt độ",
      min: 30,
      max: 45,
      integer: false,
    },
    { field: "weight", label: "Cân nặng", min: 0, max: null, integer: false },
    { field: "heartRate", label: "Nhịp tim", min: 30, max: 200, integer: true },
    {
      field: "respiratoryRate",
      label: "Nhịp thở",
      min: 5,
      max: 50,
      integer: true,
    },
  ];

  let isValid = true;

  validators.forEach(({ field, label, min, max, integer }) => {
    const raw = vitalSigns.value[field];
    if (raw === "" || raw === null || raw === undefined) {
      return;
    }

    const parsed = integer ? parseInt(raw, 10) : parseFloat(raw);
    if (Number.isNaN(parsed)) {
      fieldErrors.value[field] = `${label} phải là số hợp lệ`;
      isValid = false;
      return;
    }

    if (parsed < min || (max !== null && parsed > max)) {
      const rangeText =
        max === null ? `lớn hơn hoặc bằng ${min}` : `trong khoảng ${min}-${max}`;
      fieldErrors.value[field] = `${label} phải ${rangeText}`;
      isValid = false;
      return;
    }

    if (integer && !Number.isInteger(parsed)) {
      fieldErrors.value[field] = `${label} phải là số nguyên`;
      isValid = false;
    }
  });

  return isValid;
};

const hasAnyMedicalInput = () =>
  Boolean(
    vitalSigns.value.temperature ||
      vitalSigns.value.weight ||
      vitalSigns.value.heartRate ||
      vitalSigns.value.respiratoryRate ||
      symptoms.value.trim() ||
      reasonForVisit.value.trim()
  );

const hasRequiredVitalSigns = () =>
  Boolean(
    vitalSigns.value.temperature &&
      vitalSigns.value.weight &&
      vitalSigns.value.heartRate &&
      vitalSigns.value.respiratoryRate
  );

const serviceRequiresExaminationByName = (serviceName) => {
  const normalized = String(serviceName || "").toLowerCase();
  if (!normalized) return false;

  const nonExamKeywords = [
    "cắt tỉa",
    "ve sinh",
    "vệ sinh",
    "groom",
    "spa",
    "tắm",
    "lam dep",
    "làm đẹp",
  ];
  if (nonExamKeywords.some((keyword) => normalized.includes(keyword))) {
    return false;
  }

  const examRequiredKeywords = [
    "khám",
    "xét nghiệm",
    "xet nghiem",
    "siêu âm",
    "sieu am",
    "điều trị",
    "dieu tri",
    "phẫu thuật",
    "phau thuat",
    "triệt sản",
    "triet san",
    "cấp cứu",
    "cap cuu",
  ];
  return examRequiredKeywords.some((keyword) => normalized.includes(keyword));
};

const requiresExamForAppointment = computed(() =>
  appointmentServiceNames.value.some((serviceName) =>
    serviceRequiresExaminationByName(serviceName)
  )
);

const loadServiceChecklistState = () => {
  try {
    const raw = localStorage.getItem(localChecklistKey.value);
    if (!raw) return;
    const map = JSON.parse(raw);
    serviceChecklist.value = serviceChecklist.value.map((svc) => ({
      ...svc,
      done: Boolean(map[svc.name]),
    }));
  } catch (error) {
    console.warn("Failed to load checklist state:", error);
  }
};

const persistServiceChecklistState = () => {
  try {
    const map = serviceChecklist.value.reduce((acc, svc) => {
      acc[svc.name] = Boolean(svc.done);
      return acc;
    }, {});
    localStorage.setItem(localChecklistKey.value, JSON.stringify(map));
  } catch (error) {
    console.warn("Failed to persist checklist state:", error);
  }
};

const handleServiceChecklistChange = () => {
  persistServiceChecklistState();
};

const ensureDraftPhieuKham = async () => {
  if (existingPhieuKhamId.value || isViewMode.value) {
    return;
  }
  try {
    const response = await api.post("/phieu-kham", {
      lich_hen_id: appointmentId.value,
      loai_chi_dinh: selectedPrescriptionType.value,
      ly_do_den_kham: null,
      trieu_chung: null,
      chan_doan: null,
      ghi_chu: null,
      nhiet_do: null,
      can_nang: null,
      nhip_tim: null,
      nhip_tho: null,
      don_thuoc: null,
    });
    const draftId = response.data?.data?.id;
    if (draftId) {
      existingPhieuKhamId.value = draftId;
      savedPhieuKhamId.value = draftId;
    }
  } catch (error) {
    console.error("Cannot create draft examination record:", error);
  }
};

const buildPhieuKhamPayload = () => ({
  lich_hen_id: appointmentId.value,
  nhiet_do: vitalSigns.value.temperature
    ? parseFloat(vitalSigns.value.temperature)
    : null,
  can_nang: vitalSigns.value.weight ? parseFloat(vitalSigns.value.weight) : null,
  nhip_tim: vitalSigns.value.heartRate
    ? parseInt(vitalSigns.value.heartRate, 10)
    : null,
  nhip_tho: vitalSigns.value.respiratoryRate
    ? parseInt(vitalSigns.value.respiratoryRate, 10)
    : null,
  ly_do_den_kham: reasonForVisit.value || null,
  trieu_chung: symptoms.value || null,
  chan_doan: diagnosis.value || null,
  ghi_chu: notes.value || null,
  loai_chi_dinh: selectedPrescriptionType.value,
  don_thuoc: donThuocData.value.length > 0 ? donThuocData.value : null,
});

const hydrateFromPhieuKham = (record) => {
  if (!record) return;
  existingPhieuKhamId.value = record.id;
  savedPhieuKhamId.value = record.id;
  vitalSigns.value = {
    temperature:
      record.nhiet_do !== null && record.nhiet_do !== undefined
        ? String(record.nhiet_do)
        : "",
    weight:
      record.can_nang !== null && record.can_nang !== undefined
        ? String(record.can_nang)
        : "",
    heartRate:
      record.nhip_tim !== null && record.nhip_tim !== undefined
        ? String(record.nhip_tim)
        : "",
    respiratoryRate:
      record.nhip_tho !== null && record.nhip_tho !== undefined
        ? String(record.nhip_tho)
        : "",
  };
  reasonForVisit.value = record.ly_do_den_kham || "";
  symptoms.value = record.trieu_chung || "";
  diagnosis.value = record.chan_doan || "";
  notes.value = record.ghi_chu || "";
  selectedPrescriptionType.value = record.loai_chi_dinh || "don_thuoc";
  donThuocData.value = Array.isArray(record.don_thuoc) ? record.don_thuoc : [];
};

const loadExistingPhieuKham = async (appointmentStatus) => {
  try {
    const result = await phieuKhamService.getAll({
      lich_hen_id: appointmentId.value,
      per_page: 1,
    });
    const record = Array.isArray(result?.data) ? result.data[0] : null;

    if (record) {
      hydrateFromPhieuKham(record);
      examMode.value = appointmentStatus === "completed" ? "view" : "edit";
      return;
    }

    examMode.value = appointmentStatus === "completed" ? "view" : "create";
    if (appointmentStatus !== "completed") {
      await ensureDraftPhieuKham();
    }
    if (appointmentStatus === "completed" && requiresExamForAppointment.value) {
      showErrorToast(
        "Lịch hẹn đã hoàn thành nhưng chưa tìm thấy phiếu khám đã lưu"
      );
    }
  } catch (error) {
    console.error("Error loading examination record:", error);
    if (appointmentStatus === "completed" && requiresExamForAppointment.value) {
      showErrorToast("Không thể tải kết quả khám đã lưu cho lịch hẹn này");
    }
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
        petImage: resolveImageUrl(
          petData?.anh_dai_dien_url || petData?.anh_dai_dien,
          DEFAULT_PET_IMAGE
        ),
        badge:
          data.la_khach_vang_lai ||
          data.nguon_goc === "walkin" ||
          data.nguon_goc === "walk-in"
            ? "Đến trực tiếp"
            : "Đặt trước",
        species:
          petData?.loai ||
          petData?.loai_thu_cung ||
          petData?.species ||
          "Chưa rõ loài",
        breed:
          petData?.giong ||
          petData?.giong_thu_cung ||
          petData?.giong_loai ||
          petData?.breed ||
          "Chưa rõ giống",
        age:
          petData?.ngay_sinh || petData?.tuoi_thu_cung
            ? calculateAge(petData.ngay_sinh || petData.tuoi_thu_cung)
            : "Chưa rõ tuổi",
        ownerName:
          (typeof data.khach_hang === "object"
            ? data.khach_hang?.full_name
            : data.khach_hang) ||
          data.khachHang?.full_name ||
          "Chưa có tên",
        ownerPhone:
          (typeof data.khach_hang === "object"
            ? data.khach_hang?.so_dien_thoai
            : null) ||
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
        service: data.dich_vus?.length ? data.dich_vus.map(d => d.ten).join(", ") : (data.dich_vu?.ten || data.dichVu?.ten || "Khám tổng quát"),
      };

      // Populate service checklist
      if (data.dich_vus?.length) {
        serviceChecklist.value = data.dich_vus.map(d => ({ name: d.ten, done: false }));
      } else if (data.dich_vu?.ten || data.dichVu?.ten) {
        serviceChecklist.value = [{ name: data.dich_vu?.ten || data.dichVu?.ten, done: false }];
      }
      appointmentServiceNames.value = serviceChecklist.value.map((svc) => svc.name);
      loadServiceChecklistState();

      // Load existing notes if any
      if (data.ghi_chu) {
        notes.value = data.ghi_chu;
      }

      await loadExistingPhieuKham(data.trang_thai);

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
  router.push("/doctor/appointments");
};

const handleSave = async () => {
  if (isViewMode.value) {
    return;
  }

  if (!validateVitalSigns()) {
    showErrorToast("Vui lòng kiểm tra lại các chỉ số sinh tồn");
    return;
  }

  const hasInput = hasAnyMedicalInput();
  if (requiresExamForAppointment.value && hasInput && !diagnosis.value.trim()) {
    showErrorToast("Đã nhập thông tin khám — vui lòng nhập chẩn đoán bệnh");
    return;
  }

  saving.value = true;
  try {
    const response = await api.post("/phieu-kham", buildPhieuKhamPayload());

    console.log("=== Save Response ===");
    console.log("Response:", response.data);

    if (response.status === 200 || response.status === 201 || response.data?.data) {
      const savedId = response.data?.data?.id;
      if (savedId) {
        savedPhieuKhamId.value = savedId;
        existingPhieuKhamId.value = savedId;
      }
      if (examMode.value === "create") {
        examMode.value = "edit";
      }

      showSuccessToast(
        response.data.message || "Lưu hồ sơ khám bệnh thành công!"
      );

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

// Lưu trữ đơn thuốc từ modal
const donThuocData = ref([]);

// Handle Don Thuoc modal save
const handlePrescriptionFormSave = (data) => {
  donThuocData.value = data;
  selectedPrescriptionType.value = "don_thuoc";
  isPrescriptionFormModalOpen.value = false;
  showSuccessToast(`Đã lưu đơn thuốc (${data.length} loại)`);
};

// Handle Hen Tai Kham modal save
const handleFollowUpSave = (data) => {
  console.log("Hen Tai Kham saved:", data);
  selectedPrescriptionType.value = "hen_tai_kham";
  isFollowUpModalOpen.value = false;
  showSuccessToast("Đã lưu lịch hẹn tái khám");
};
const hoanTatVaChuyenThuNgan = async () => {
  if (isViewMode.value) {
    return;
  }

  if (!validateVitalSigns()) {
    showErrorToast("Vui lòng kiểm tra lại các chỉ số sinh tồn trước khi hoàn tất");
    return;
  }

  const hasInput = hasAnyMedicalInput();
  if (requiresExamForAppointment.value && hasInput && !diagnosis.value.trim()) {
    showErrorToast("Đã nhập thông tin khám — vui lòng nhập chẩn đoán trước khi hoàn tất");
    return;
  }
  if (requiresExamForAppointment.value && !hasRequiredVitalSigns()) {
    showErrorToast(
      "Vui lòng nhập đầy đủ chỉ số sinh tồn trước khi hoàn tất khám"
    );
    return;
  }
  if (requiresExamForAppointment.value && !diagnosis.value.trim()) {
    showErrorToast("Vui lòng nhập chẩn đoán trước khi hoàn tất khám");
    return;
  }

  saving.value = true;
  try {
    const shouldPersistPhieuKham =
      requiresExamForAppointment.value ||
      hasAnyMedicalInput() ||
      Boolean(diagnosis.value.trim());

    if (shouldPersistPhieuKham) {
      const phieuKhamResponse = await api.post(
        "/phieu-kham",
        buildPhieuKhamPayload()
      );
      const phieuKhamId = phieuKhamResponse.data?.data?.id;
      if (phieuKhamId) {
        existingPhieuKhamId.value = phieuKhamId;
        savedPhieuKhamId.value = phieuKhamId;
      }
    }

    await api.post(`/lich-hen/${appointmentId.value}/hoan-thanh-kham`);

    showSuccessToast("Hoàn tất khám thành công!");
    persistServiceChecklistState();
    examMode.value = "view";
    setTimeout(() => {
      router.push("/doctor/appointments");
    }, 1000);
  } catch (error) {
    console.error("Lỗi hoàn tất khám:", error);
    showErrorToast(
      error.response?.data?.message || "Lỗi khi hoàn tất khám bệnh"
    );
  } finally {
    saving.value = false;
  }
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

<style scoped>
.ef-root {
  min-height: 100vh;
  font-family: "Nunito Sans", sans-serif;
}

/* Layout */
.ef-layout {
  max-width: 1200px;
  margin: 0 auto;
  padding: 28px 32px 64px;
  display: flex;
  flex-direction: column;
  gap: 22px;
  animation: ef-in 0.25s ease;
}
@keyframes ef-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Header */
.ef-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.ef-eyebrow {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #9ca3af;
  margin-bottom: 2px;
}
.ef-title {
  font-family: "Montserrat Alternates", sans-serif;
  font-size: 22px;
  font-weight: 600;
  color: #432323;
  margin: 0;
}
.ef-btn-back {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  border-radius: 8px;
  border: 1px solid #e0d9d9;
  background: #fff;
  color: #393e46;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  font-family: "Nunito Sans", sans-serif;
  transition: all 0.15s;
}
.ef-btn-back:hover {
  background: #f8fafc;
}
.ef-btn-save {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 22px;
  background: #009689;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  font-family: "Nunito Sans", sans-serif;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 150, 137, 0.3);
}
.ef-btn-save:hover:not(:disabled) {
  background: #008177;
  transform: translateY(-1px);
  box-shadow: 0 4px 14px rgba(0, 150, 137, 0.35);
}
.ef-btn-save:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Banner */
.ef-banner {
  background: linear-gradient(135deg, #5a9690 0%, #4a7f79 100%);
  border-radius: 8px;
  padding: 26px 30px;
  position: relative;
  overflow: hidden;
}
.ef-banner-glow {
  position: absolute;
  top: -60px;
  right: -60px;
  width: 220px;
  height: 220px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.05);
  pointer-events: none;
}
.ef-banner-inner {
  display: flex;
  align-items: center;
  gap: 22px;
  position: relative;
  z-index: 1;
}
.ef-avatar-wrap {
  flex-shrink: 0;
}
.ef-avatar {
  width: 82px;
  height: 82px;
  border-radius: 8px;
  object-fit: cover;
  border: 2.5px solid rgba(255, 255, 255, 0.25);
}
.ef-pet-name {
  font-family: "Montserrat Alternates", sans-serif;
  font-size: 26px;
  font-weight: 600;
  color: #fff;
  margin: 0;
}
.ef-badge {
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.25);
  color: rgba(255, 255, 255, 0.9);
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.05em;
  padding: 3px 10px;
  border-radius: 20px;
  white-space: nowrap;
}
.ef-pet-meta {
  color: rgba(255, 255, 255, 0.6);
  font-size: 13px;
  margin: 0;
}
.ef-tile {
  flex-shrink: 0;
}
.ef-tile-label {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.4);
  margin-bottom: 3px;
}
.ef-tile-value {
  font-size: 15px;
  font-weight: 600;
  color: #fff;
  margin-bottom: 2px;
}
.ef-tile-sub {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.5);
}

/* Body */
.ef-body {
  display: flex;
  gap: 20px;
  align-items: flex-start;
}
.ef-col-left {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.ef-col-right {
  width: 288px;
  flex-shrink: 0;
  position: sticky;
  top: 24px;
}

/* Cards */
.ef-card {
  background: #fff;
  border: 1px solid #e0d9d9;
  border-radius: 8px;
  padding: 20px 22px;
  transition: box-shadow 0.2s;
}
.ef-card:hover {
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}
.ef-card-diagnosis {
  border-color: #2dd4bf;
}
.ef-card-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 16px;
}
.ef-card-title {
  font-size: 14px;
  font-weight: 700;
  color: #393e46;
  margin: 0;
}

/* Icons */
.ef-icon {
  width: 28px;
  height: 28px;
  border-radius: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.ef-icon-amber {
  background: #fef3c7;
  color: #d97706;
}
.ef-icon-violet {
  background: #ede9fe;
  color: #7c3aed;
}
.ef-icon-rose {
  background: #fce7f3;
  color: #be185d;
}
.ef-icon-emerald {
  background: #dcfce7;
  color: #15803d;
}
.ef-icon-yellow {
  background: #fef9c3;
  color: #ca8a04;
}

/* Textarea */
.ef-textarea {
  width: 100%;
  background: #f8fafc;
  border: 1.5px solid #eeeeee;
  border-radius: 8px;
  padding: 11px 14px;
  font-size: 13.5px;
  color: #393e46;
  font-family: "Nunito Sans", sans-serif;
  resize: none;
  outline: none;
  transition: border-color 0.15s, background 0.15s;
  line-height: 1.6;
}
.ef-textarea:focus {
  background: #fff;
  border-color: #009689;
}
.ef-textarea::placeholder {
  color: #9ca3af;
}
.ef-textarea-diag {
  border-color: #2dd4bf;
}
.ef-textarea-diag:focus {
  border-color: #0d9488;
}

/* Vitals */
.ef-vital {
  background: #f8fafc;
  border: 1px solid #eeeeee;
  border-radius: 8px;
  padding: 12px 14px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.ef-vital-label {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  color: #9ca3af;
}
.ef-vital-input-wrap {
  display: flex;
  align-items: center;
  gap: 0;
}
.ef-vital-input {
  flex: 1;
  background: transparent;
  border: none;
  outline: none;
  font-family: "Nunito Sans", sans-serif;
  font-size: 18px;
  font-weight: 700;
  color: #432323;
  min-width: 0;
  padding: 0;
}
.ef-vital-input--error {
  border-color: #ef4444 !important;
  box-shadow: 0 0 0 1px rgba(239, 68, 68, 0.25);
}
.ef-field-error {
  margin-top: 6px;
  color: #dc2626;
  font-size: 12px;
  font-weight: 600;
  line-height: 1.3;
}
.ef-vital-input::placeholder {
  color: #cbd5e1;
  font-size: 16px;
}
.ef-vital-unit {
  font-family: "Nunito Sans", sans-serif;
  font-size: 12px;
  font-weight: 600;
  color: #9ca3af;
  flex-shrink: 0;
}

/* Action Panel */
/* Action Panel */
.ef-action-panel {
  background: #fff;
  border: 1px solid #e0d9d9;
  border-radius: 8px;
  padding: 18px 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.ef-action-title {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.09em;
  text-transform: uppercase;
  color: #9ca3af;
  margin-bottom: 2px;
}
.ef-action-btn {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
  padding: 12px 14px;
  border-radius: 8px;
  border: 1.5px solid transparent;
  cursor: pointer;
  font-family: "Nunito Sans", sans-serif;
  transition: all 0.18s;
}
.ef-action-icon {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.ef-action-btn--green {
  background: #f8fafc;
  border-color: #eeeeee;
  color: #393e46;
}
.ef-action-btn--green:hover {
  background: #f1f5f9;
}
.ef-action-btn--active-green {
  background: #009689;
  border-color: #009689;
  color: #fff;
}
.ef-action-btn--green .ef-action-icon {
  background: #e2e8f0;
}
.ef-action-btn--active-green .ef-action-icon {
  background: rgba(255, 255, 255, 0.2);
}
.ef-action-btn--cyan {
  background: #f8fafc;
  border-color: #eeeeee;
  color: #393e46;
}
.ef-action-btn--cyan:hover {
  background: #f1f5f9;
}
.ef-action-btn--active-cyan {
  background: #5a9690;
  border-color: #5a9690;
  color: #fff;
}
.ef-action-btn--cyan .ef-action-icon {
  background: #e2e8f0;
}
.ef-action-btn--active-cyan .ef-action-icon {
  background: rgba(255, 255, 255, 0.2);
}
.ef-action-btn--complete {
  background: #2f5755;
  border-color: #2f5755;
  color: #fff;
}
.ef-action-btn--complete:hover {
  background: #009689;
  transform: translateY(-1px);
  box-shadow: 0 4px 14px rgba(0, 150, 137, 0.3);
}
.ef-action-btn--complete .ef-action-icon {
  background: rgba(255, 255, 255, 0.15);
}
.ef-action-btn--disabled {
  background: #e2e8f0;
  border-color: #e2e8f0;
  color: #94a3b8;
  cursor: not-allowed;
}
.ef-action-btn--disabled .ef-action-icon {
  background: #cbd5e1;
}

/* Modal */
.ef-modal {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

/* Fade transition */
.ef-fade-enter-active,
.ef-fade-leave-active {
  transition: opacity 0.25s ease;
}
.ef-fade-enter-from,
.ef-fade-leave-to {
  opacity: 0;
}
</style>
