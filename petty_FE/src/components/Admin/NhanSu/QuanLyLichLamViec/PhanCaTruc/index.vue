<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white border !border-gray-300 rounded-[10px] shadow-lg w-[510px] relative"
    >
      <div class="p-6 flex flex-col gap-4">
        <!-- Close Button -->
        <button
          @click="$emit('close')"
          class="absolute right-6 top-4 w-4 h-4 opacity-70 hover:opacity-100 transition-opacity"
        >
          <CloseIcon />
        </button>

        <!-- Header -->
        <div class="flex flex-col gap-2">
          <h2
            class="font-nunito font-semibold text-lg leading-[18px] text-neutral-950 tracking-tight"
          >
            Phân ca trực
          </h2>
          <p
            class="font-nunito text-sm leading-5 text-[#717182] tracking-tight"
          >
            Thêm ca làm việc cho nhân viên
          </p>
        </div>

        <!-- Form Content -->
        <div class="flex flex-col gap-4">
          <!-- Select Staff -->
          <div class="flex flex-col gap-2">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
            >
              Chọn Nhân viên <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <button
                @click="toggleStaffDropdown"
                type="button"
                :class="[
                  'bg-[#f3f3f5] rounded-lg h-9 px-3 flex items-center justify-between hover:bg-gray-200 transition-colors min-w-[460px]',
                  props.serverErrors && props.serverErrors.nhan_vien_id
                    ? 'border-red-500'
                    : 'border-transparent',
                ]"
              >
                <span
                  class="font-nunito text-sm leading-5 text-[#717182] tracking-tight"
                >
                  {{
                    selectedStaff &&
                    (selectedStaff.full_name || selectedStaff.name)
                      ? selectedStaff.full_name || selectedStaff.name
                      : "Chọn nhân viên"
                  }}
                </span>
                <ChevronDownIcon />
              </button>

              <!-- Dropdown list -->
              <div
                v-if="showStaffDropdown"
                class="absolute left-0 mt-2 w-[260px] bg-white border !border-gray-300 rounded-lg shadow-lg z-50 max-h-56 overflow-auto"
              >
                <ul>
                  <li v-for="staff in staffList" :key="staff.id">
                    <button
                      @click.prevent="selectStaff(staff)"
                      class="w-full text-left px-3 py-2 hover:bg-gray-100"
                    >
                      <span class="font-nunito text-sm">{{ staff.name }}</span>
                    </button>
                  </li>
                  <li
                    v-if="staffList.length === 0"
                    class="px-3 py-2 text-sm text-gray-500"
                  >
                    Không có nhân viên
                  </li>
                </ul>
              </div>
              <p
                v-if="props.serverErrors && props.serverErrors.nhan_vien_id"
                class="text-xs text-red-600 mt-1"
              >
                {{ props.serverErrors.nhan_vien_id[0] }}
              </p>
            </div>
          </div>

          <!-- Date and Room -->
          <div class="grid grid-cols-2 gap-4">
            <!-- Select Date -->
            <div class="flex flex-col gap-2">
              <label
                class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
              >
                Chọn Ngày <span class="text-red-500">*</span>
              </label>
              <input
                v-model="selectedDate"
                type="date"
                :class="[
                  'bg-[#f3f3f5] rounded-lg h-9 px-3 font-nunito text-sm text-neutral-950 focus:outline-none focus:ring-2 focus:ring-[#009689]',
                  props.serverErrors && props.serverErrors.ngay_lam
                    ? 'border-red-500'
                    : 'border-transparent',
                ]"
              />
              <p
                v-if="props.serverErrors && props.serverErrors.ngay_lam"
                class="text-xs text-red-600 mt-1"
              >
                {{ props.serverErrors.ngay_lam[0] }}
              </p>
            </div>

            <!-- Room -->
            <div class="flex flex-col gap-2">
              <label
                class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
              >
                Phòng trực
              </label>
              <input
                v-model="room"
                type="text"
                placeholder="VD: P. Khám 01"
                :class="[
                  'bg-[#f3f3f5] rounded-lg h-9 px-3 font-nunito text-sm text-neutral-950 placeholder:text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689]',
                  props.serverErrors && props.serverErrors.phong_truc
                    ? 'border-red-500'
                    : 'border-transparent',
                ]"
              />
              <p
                v-if="props.serverErrors && props.serverErrors.phong_truc"
                class="text-xs text-red-600 mt-1"
              >
                {{ props.serverErrors.phong_truc[0] }}
              </p>
            </div>
          </div>

          <!-- Shift Time Selection -->
          <div class="flex flex-col gap-3">
            <label
              class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
            >
              Thời gian (Ca trực) <span class="text-red-500">*</span>
            </label>
            <div class="flex flex-col gap-3">
              <!-- Morning Shift -->
              <div class="flex items-center gap-2">
                <button
                  @click="selectedShift = 'morning'"
                  :class="[
                    'w-4 h-4 rounded-full border flex items-center justify-center flex-shrink-0',
                    selectedShift === 'morning'
                      ? 'border-[#030213]'
                      : 'border-gray-300',
                  ]"
                >
                  <div
                    v-if="selectedShift === 'morning'"
                    class="w-2 h-2 rounded-full bg-[#030213]"
                  ></div>
                </button>
                <div class="flex items-center gap-2">
                  <SunIcon />
                  <span
                    class="font-nunito text-base leading-6 text-neutral-950 tracking-tight"
                  >
                    Ca Sáng
                  </span>
                  <span
                    class="font-nunito text-sm leading-5 text-[#6a7282] tracking-tight"
                  >
                    (08:00 - 16:00)
                  </span>
                </div>
              </div>

              <!-- Afternoon Shift -->
              <div class="flex items-center gap-2">
                <button
                  @click="selectedShift = 'afternoon'"
                  :class="[
                    'w-4 h-4 rounded-full border flex items-center justify-center flex-shrink-0',
                    selectedShift === 'afternoon'
                      ? 'border-[#030213]'
                      : 'border-gray-300',
                  ]"
                >
                  <div
                    v-if="selectedShift === 'afternoon'"
                    class="w-2 h-2 rounded-full bg-[#030213]"
                  ></div>
                </button>
                <div class="flex items-center gap-2">
                  <SunsetIcon />
                  <span
                    class="font-nunito text-base leading-6 text-neutral-950 tracking-tight"
                  >
                    Ca Chiều
                  </span>
                  <span
                    class="font-nunito text-sm leading-5 text-[#6a7282] tracking-tight"
                  >
                    (13:00 - 21:00)
                  </span>
                </div>
              </div>

              <!-- Night Shift -->
              <div class="flex items-center gap-2">
                <button
                  @click="selectedShift = 'night'"
                  :class="[
                    'w-4 h-4 rounded-full border flex items-center justify-center flex-shrink-0',
                    selectedShift === 'night'
                      ? 'border-[#030213]'
                      : 'border-gray-300',
                  ]"
                >
                  <div
                    v-if="selectedShift === 'night'"
                    class="w-2 h-2 rounded-full bg-[#030213]"
                  ></div>
                </button>
                <div class="flex items-center gap-2">
                  <HalfMoonIcon />
                  <span
                    class="font-nunito text-base leading-6 text-neutral-950 tracking-tight"
                  >
                    Ca Tối / Trực đêm
                  </span>
                  <span
                    class="font-nunito text-sm leading-5 text-[#6a7282] tracking-tight"
                  >
                    (21:00 - 08:00)
                  </span>
                </div>
              </div>

              <!-- Custom Shift -->
              <div class="flex items-center gap-2">
                <!-- <button
                  @click="selectedShift = 'custom'"
                  :class="[
                    'w-4 h-4 rounded-full border flex items-center justify-center flex-shrink-0',
                    selectedShift === 'custom'
                      ? 'border-[#030213]'
                      : 'border-gray-300'
                  ]"
                >
                  <div
                    v-if="selectedShift === 'custom'"
                    class="w-2 h-2 rounded-full bg-[#030213]"
                  ></div>
                </button> -->
                <!-- <div class="flex items-center gap-2">
                  <img :src="iconClock" alt="Custom" class="w-4 h-4" />
                  <span class="font-nunito text-base leading-6 text-neutral-950 tracking-tight">
                    Tùy chỉnh
                  </span>
                </div> -->
              </div>
              <p
                v-if="props.serverErrors && props.serverErrors.thoi_gian_truc"
                class="text-xs text-red-600 mt-1"
              >
                {{ props.serverErrors.thoi_gian_truc[0] }}
              </p>
            </div>
          </div>

          <!-- Repeat Weekly Checkbox -->
          <div class="border-t border-gray-200/60 pt-4">
            <div class="flex items-center gap-2">
              <button
                @click="repeatWeekly = !repeatWeekly"
                :class="[
                  'w-4 h-4 rounded border flex items-center justify-center flex-shrink-0',
                  repeatWeekly
                    ? 'bg-[#030213] border-[#030213]'
                    : 'bg-[#f3f3f5] border-gray-200/60',
                ]"
              >
                <svg
                  v-if="repeatWeekly"
                  class="w-3 h-3 text-white"
                  fill="currentColor"
                  viewBox="0 0 12 12"
                >
                  <path
                    d="M10 3L4.5 8.5L2 6"
                    stroke="currentColor"
                    stroke-width="2"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
              </button>
              <label
                @click="repeatWeekly = !repeatWeekly"
                class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight cursor-pointer"
              >
                Lặp lại hàng tuần
              </label>
            </div>
          </div>
        </div>

        <!-- Footer Buttons -->
        <div class="flex items-center justify-end gap-2">
          <button
            @click="$emit('close')"
            :disabled="props.saving"
            class="bg-white border !border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 transition-colors disabled:opacity-50"
          >
            <span
              class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >Hủy</span
            >
          </button>
          <button
            @click="handleSave"
            :disabled="!isFormValid || props.saving"
            :class="[
              'rounded-lg px-4 py-2 transition-colors',
              isFormValid && !props.saving
                ? 'bg-[#5a9690] hover:bg-[#5a9690]/80'
                : 'bg-gray-300 cursor-not-allowed',
            ]"
          >
            <span
              class="font-nunito font-medium text-sm leading-5 text-white tracking-tight"
            >
              <span v-if="props.saving">Đang lưu...</span>
              <span v-else> Lưu</span>
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { listNhanVien } from "@/utils/nhanVien";

// Props
const props = defineProps({
  preselectedStaff: {
    type: Object,
    default: null,
  },
  preselectedDate: {
    type: String,
    default: "",
  },
  // server-side validation errors passed from parent
  serverErrors: {
    type: Object,
    default: () => ({}),
  },
  // parent saving state
  saving: {
    type: Boolean,
    default: false,
  },
});

// Emits
const emit = defineEmits(["close", "save"]);
//Icon SVG
import CloseIcon from "@/assets/svg/close.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import SunIcon from "@/assets/svg/sun.svg";
import SunsetIcon from "@/assets/svg/sunset.svg";
import HalfMoonIcon from "@/assets/svg/half-moon.svg";

// Icons (from Figma - expire in 7 days)
const iconChevronDown =
  "https://www.figma.com/api/mcp/asset/d674c637-771a-470c-9ee9-bd998cfdd093";
const iconSun =
  "https://www.figma.com/api/mcp/asset/c9e354c3-8fa1-4a4c-8b64-246eccd9b8f6";
const iconSunset =
  "https://www.figma.com/api/mcp/asset/e2ad62cc-2427-490d-957f-0b54a8b145f8";
const iconMoon =
  "https://www.figma.com/api/mcp/asset/035892ad-918a-4739-b1f7-206065cb2500";
const iconClock =
  "https://www.figma.com/api/mcp/asset/34a32003-d33b-4e5d-a611-bb8554b77415";
const iconClose =
  "https://www.figma.com/api/mcp/asset/c1aca6c3-953c-44dd-9939-0031d806bb2c";

// Reactive state
const selectedStaff = ref(props.preselectedStaff);
const staffList = ref([]);
const showStaffDropdown = ref(false);
const selectedDate = ref(props.preselectedDate);
const room = ref("");
const selectedShift = ref("morning");
const repeatWeekly = ref(false);

// Computed: Form validation
const isFormValid = computed(() => {
  return selectedStaff.value && selectedDate.value && selectedShift.value;
});

// Methods
const handleSave = () => {
  if (!isFormValid.value) return;

  const shiftTimes = {
    morning: { start: "08:00", end: "16:00", label: "Ca Sáng" },
    afternoon: { start: "13:00", end: "21:00", label: "Ca Chiều" },
    night: { start: "21:00", end: "08:00", label: "Ca Tối / Trực đêm" },
    custom: { start: "", end: "", label: "Tùy chỉnh" },
  };

  const shiftData = {
    staff: selectedStaff.value,
    date: selectedDate.value,
    room: room.value,
    shift: selectedShift.value,
    shiftTime: shiftTimes[selectedShift.value],
    repeatWeekly: repeatWeekly.value,
  };

  emit("save", shiftData);
};

// Load staff list for dropdown
onMounted(async () => {
  try {
    const items = await listNhanVien();
    // map to simple objects if necessary
    staffList.value = items.map((it) => ({
      id: it.id,
      name: it.full_name || it.name || it.ho_ten || "—",
      raw: it,
    }));
  } catch (e) {
    console.error("Failed to load staff list for shift dropdown", e);
  }
});

const toggleStaffDropdown = () => {
  showStaffDropdown.value = !showStaffDropdown.value;
};

const selectStaff = (staff) => {
  selectedStaff.value = staff.raw || staff;
  showStaffDropdown.value = false;
};
</script>

<style scoped>
/* No additional styles needed - using Tailwind CSS */
</style>
