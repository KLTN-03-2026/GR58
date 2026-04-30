<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    @click.self="closePopup"
  >
    <!-- Modal đặt lịch chính -->
    <div
      class="bg-white rounded-lg border !border-black/15 w-full max-w-[512px] max-h-[90vh] shadow-xl flex flex-col"
    >
      <!-- Fixed Header: Tiêu đề + Thanh tiến độ -->
      <div
        class="flex flex-col p-6 pb-4 gap-4 flex-shrink-0 border-b border-gray-200"
      >
        <!-- Tiêu đề -->
        <div class="flex flex-col gap-2">
          <div class="h-7 relative">
            <h2 class="text-lg font-bold text-black">Đặt lịch khám</h2>
            <button
              @click="closePopup"
              class="absolute right-0 top-0 w-7 h-7 flex items-center justify-center hover:opacity-70 transition-opacity"
            >
              <IconClose />
            </button>
          </div>
          <p class="text-sm font-medium text-gray-500">
            {{ stepDescriptions[currentStep] }}
          </p>
        </div>

        <!-- Thanh tiến độ -->
        <div class="flex flex-col gap-2 h-9">
          <div class="flex items-center justify-between h-5">
            <span
              v-for="(step, index) in steps"
              :key="index"
              class="text-sm font-medium"
              :class="index <= currentStep ? 'text-[#5A9690]' : 'text-gray-500'"
            >
              {{ step }}
            </span>
          </div>
          <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
            <div
              class="h-full bg-[#5A9690] rounded-full transition-all duration-300"
              :style="{ width: progressWidth }"
            ></div>
          </div>
        </div>
      </div>

      <!-- Scrollable Content Area -->
      <div class="flex-1 overflow-y-auto px-6 py-4">
        <!-- Bước 1: Chọn thú cưng -->
        <div v-if="currentStep === 0" class="flex flex-col gap-4">
          <div class="grid grid-cols-2 gap-4">
            <div
              v-for="pet in pets"
              :key="pet.id"
              @click="selectPet(pet)"
              :class="[
                'border-2 rounded-lg p-[18px] cursor-pointer transition-all',
                selectedPet?.id === pet.id
                  ? 'border-teal-500 bg-teal-50'
                  : 'border-gray-200 hover:border-gray-300',
              ]"
            >
              <div class="flex items-center gap-3">
                <div
                  class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0"
                >
                  <Heart1Icon />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-black truncate">
                    {{ pet.name }}
                  </p>
                  <p class="text-sm font-medium text-gray-500 truncate">
                    {{ pet.breed }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Bước 2: Chọn dịch vụ -->
        <div v-if="currentStep === 1" class="flex flex-col gap-3">
          <div
            v-for="service in services"
            :key="service.id"
            @click="selectService(service)"
            :class="[
              'border-2 rounded-lg p-[18px] cursor-pointer transition-all',
              selectedService?.id === service.id
                ? 'border-teal-500 bg-teal-50'
                : 'border-gray-300 hover:border-gray-400',
            ]"
          >
            <div class="flex flex-col gap-1">
              <div class="flex items-center gap-2">
                <p class="text-sm font-medium text-black">
                  {{ service.name }}
                </p>
                <SuccessIcon
                  v-if="selectedService?.id === service.id"
                  alt="Selected"
                  class="w-5 h-5"
                />
              </div>
              <p class="text-sm font-medium text-gray-600">
                {{ service.description }}
              </p>
              <div class="flex items-center gap-4 mt-1">
                <div class="flex items-center gap-2">
                  <ClockIcon class="w-4 h-4 text-gray-500" />
                  <span class="text-sm font-medium text-gray-500">
                    {{ service.duration }} phút
                  </span>
                </div>
                <span class="text-sm font-medium text-teal-600">
                  {{ formatPrice(service.price) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Bước 3: Chọn ngày giờ -->
        <div v-if="currentStep === 2" class="flex flex-col gap-4">
          <!-- Date Picker -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-black">
              Chọn ngày khám
            </label>
            <div class="border !border-gray-300 rounded-lg p-3">
              <div class="flex flex-col items-center">
                <!-- Calendar Header -->
                <div class="flex items-center justify-between w-full mb-4">
                  <button
                    @click="previousMonth"
                    class="w-7 h-7 border border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50 transition-colors"
                    :disabled="!canGoPrevious"
                    :class="{ 'opacity-50 cursor-not-allowed': !canGoPrevious }"
                  >
                    <ChevronLeftIcon />
                  </button>
                  <span class="text-sm font-semibold text-black">
                    {{ currentMonthYear }}
                  </span>
                  <button
                    @click="nextMonth"
                    class="w-7 h-7 border border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50 transition-colors"
                    :disabled="!canGoNext"
                    :class="{ 'opacity-50 cursor-not-allowed': !canGoNext }"
                  >
                    <ChevronRightIcon />
                  </button>
                </div>

                <!-- Calendar Grid -->
                <div class="w-full">
                  <div class="grid grid-cols-7 gap-0 mb-2">
                    <div
                      v-for="day in weekDays"
                      :key="day"
                      class="h-5 flex items-center justify-center"
                    >
                      <span class="text-sm font-medium text-gray-500">
                        {{ day }}
                      </span>
                    </div>
                  </div>
                  <div class="grid grid-cols-7 gap-0">
                    <button
                      v-for="(date, index) in calendarDates"
                      :key="index"
                      @click="selectDate(date)"
                      :disabled="!date.isCurrentMonth || date.isPast"
                      :class="[
                        'w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors',
                        date.isSelected
                          ? 'bg-black text-white'
                          : !date.isCurrentMonth || date.isPast
                          ? 'opacity-50 cursor-not-allowed text-gray-500'
                          : 'text-black hover:bg-gray-100 cursor-pointer',
                      ]"
                    >
                      {{ date.day }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Time Picker -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-black">Chọn giờ khám</label>

            <div v-if="!selectedDate" class="text-sm text-gray-500 py-2">
              Vui lòng chọn ngày để xem các khung giờ khả dụng.
            </div>

            <div v-else-if="loadingSlots" class="text-sm text-gray-400 py-2">Đang tải lịch...</div>

            <div v-else-if="slotsMessage" class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">
              {{ slotsMessage }}
            </div>

            <template v-else>
              <div class="grid grid-cols-4 gap-2">
                <button
                  v-for="time in timeSlots"
                  :key="time.value"
                  @click="selectTime(time)"
                  :disabled="time.isBooked"
                  :class="[
                    'h-9 border rounded-lg text-sm font-semibold transition-colors',
                    selectedTime === time.value
                      ? 'bg-[#487874] text-white border-[#5A9690]/80'
                      : '',
                    time.isBooked
                      ? 'opacity-50 cursor-not-allowed border-gray-300 bg-white text-black'
                      : '',
                    !time.isBooked && selectedTime !== time.value
                      ? '!border-gray-300 bg-white text-black hover:border-gray-400'
                      : '',
                  ]"
                >
                  {{ time.label }}
                </button>
              </div>
              <p class="text-sm font-medium text-gray-500 mt-1">* Các khung giờ bị mờ đã kín lịch</p>
            </template>
          </div>
        </div>

        <!-- Bước 4: Xác nhận -->
        <div v-if="currentStep === 3" class="flex flex-col gap-4">
          <!-- Thông tin đặt lịch -->
          <div class="bg-teal-50 rounded-lg p-4 flex flex-col gap-3">
            <h3 class="text-sm font-semibold text-teal-900">
              Thông tin đặt lịch
            </h3>
            <div class="w-full h-px bg-gray-300"></div>
            <div class="flex flex-col gap-2">
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-600"
                  >Khách hàng:</span
                >
                <span class="text-sm font-medium text-black">{{
                  customerNameLocal
                }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-600">Thú cưng:</span>
                <span class="text-sm font-medium text-black">{{
                  selectedPet?.name
                }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-600">Dịch vụ:</span>
                <span class="text-sm font-medium text-black">{{
                  selectedService?.name
                }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-600"
                  >Thời gian:</span
                >
                <span class="text-sm font-medium text-black">{{
                  formattedDateTime
                }}</span>
              </div>
              <div class="w-full h-px bg-gray-300"></div>
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-black">Tạm tính:</span>
                <span class="text-sm font-medium text-teal-600">{{
                  formatPrice(selectedService?.price)
                }}</span>
              </div>
            </div>
          </div>

          <!-- Phương thức thanh toán -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-black">
              Phương thức thanh toán
            </label>
            <div class="flex flex-col gap-2">
              <!-- Thanh toán trước -->
              <div
                @click="selectPaymentMethod('online')"
                :class="[
                  'border rounded-lg p-3 flex items-start gap-2 cursor-pointer transition-all',
                  paymentMethod === 'online'
                    ? '!border-teal-500 bg-teal-50'
                    : '!border-gray-300 hover:border-gray-400',
                ]"
              >
                <div
                  class="w-4 h-4 rounded-full border-2 mt-0.5 flex items-center justify-center flex-shrink-0"
                  :class="
                    paymentMethod === 'online'
                      ? 'border-black'
                      : 'border-gray-400'
                  "
                >
                  <div
                    v-if="paymentMethod === 'online'"
                    class="w-2 h-2 bg-black rounded-full"
                  ></div>
                </div>
                <div class="flex-1 flex items-center justify-between gap-2">
                  <span class="text-sm font-semibold text-black">
                    Thanh toán trước (VNPay/Momo)
                  </span>
                </div>
              </div>

              <!-- Thanh toán tại phòng khám -->
              <div
                @click="selectPaymentMethod('offline')"
                :class="[
                  'border rounded-lg p-3 flex items-start gap-2 cursor-pointer transition-all',
                  paymentMethod === 'offline'
                    ? '!border-teal-500 bg-teal-50'
                    : '!border-gray-300 hover:border-gray-400',
                ]"
              >
                <div
                  class="w-4 h-4 rounded-full border-2 mt-0.5 flex items-center justify-center flex-shrink-0"
                  :class="
                    paymentMethod === 'offline'
                      ? 'border-black'
                      : 'border-gray-400'
                  "
                >
                  <div
                    v-if="paymentMethod === 'offline'"
                    class="w-2 h-2 bg-black rounded-full"
                  ></div>
                </div>
                <div class="flex-1 flex items-center justify-between gap-2">
                  <span class="text-sm font-semibold text-black">
                    Thanh toán tại phòng khám
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Fixed Footer: Nút điều hướng -->
      <div
        class="flex items-center justify-end gap-6 p-6 pt-4 flex-shrink-0 border-t border-gray-200"
      >
        <button
          v-if="currentStep > 0"
          @click="previousStep"
          class="h-9 px-4 border !border-gray-300 rounded-lg bg-white hover:bg-gray-50 transition-colors"
        >
          <span class="text-sm font-semibold text-black"> Quay lại </span>
        </button>
        <button
          v-if="currentStep < 3"
          @click="nextStep"
          :disabled="!canProceed"
          :class="[
            'h-9 px-4 rounded-lg transition-colors',
            canProceed
              ? 'bg-[#5A9690] hover:bg-[#5A9690] text-white'
              : 'bg-gray-300 text-gray-500 cursor-not-allowed',
          ]"
        >
          <span class="text-sm font-semibold"> Tiếp tục </span>
        </button>
        <button
          v-if="currentStep === 3"
          @click="confirmBooking"
          :disabled="!canConfirm || isSubmitting"
          :class="[
            'h-9 px-3 rounded-lg flex items-center gap-2 transition-colors',
            canConfirm && !isSubmitting
              ? 'bg-[#5A9690] hover:bg-[#5A9690] text-white'
              : 'bg-gray-300 text-gray-500 cursor-not-allowed',
          ]"
        >
          <span class="text-sm font-semibold"> Xác nhận đặt lịch </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();
import { showSuccessToast, showErrorToast } from "@/utils/toast";
import { getUser } from "@/utils/auth";
//Icon SVG
import IconClose from "@/assets/svg/close.svg";
import Heart1Icon from "@/assets/svg/heart1.svg";
import AddIcon from "@/assets/svg/add.svg";
import ClockIcon from "@/assets/svg/clock.svg";
import SuccessIcon from "@/assets/svg/success.svg";
import ChevronLeftIcon from "@/assets/svg/chevron-left.svg";
import ChevronRightIcon from "@/assets/svg/chevron-right.svg";
import TickIcon from "@/assets/svg/tick.svg";
// Thuộc tính (props)
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: true,
  },
  customerName: {
    type: String,
    default: "Phương Linh",
  },
  initialData: {
    type: Object,
    default: null,
  },
});

// Sự kiện phát (emits)
const emit = defineEmits(["close", "confirm", "openAddPet"]);

// Quản lý bước
const currentStep = ref(0);
const steps = ["Chọn thú cưng", "Chọn dịch vụ", "Chọn ngày giờ", "Xác nhận"];
const stepDescriptions = [
  "Chọn thú cưng cần khám",
  "Chọn dịch vụ khám",
  "Chọn ngày và giờ khám",
  "Xác nhận thông tin",
];

const progressWidth = computed(() => {
  return `${((currentStep.value + 1) / steps.length) * 100}%`;
});

// Dữ liệu
const pets = ref([]);
const API_BASE = import.meta.env.VITE_API_BASE || "http://127.0.0.1:8000/api";

const mapBackendPetSimple = (item) => ({
  id: item.id,
  name: item.ten_thu_cung || item.name || "Không rõ",
  breed: item.giong_thu_cung || item.breed || "-",
});

const fetchPets = async () => {
  try {
    // lấy thú cưng của người dùng; sử dụng ?all=1 như ThuCungCuaToi để nhận toàn bộ danh sách
    const res = await axios.get(`${API_BASE}/thu-cung?all=1`);

    let list = [];
    if (res.data && res.data.data) {
      if (Array.isArray(res.data.data)) list = res.data.data;
      else if (Array.isArray(res.data.data.data)) list = res.data.data.data;
    }

    pets.value = list.map(mapBackendPetSimple);

    if (route.query.pet_id) {
      const petIdToSelect = Number(route.query.pet_id);
      const foundPet = pets.value.find(p => p.id === petIdToSelect);
      if (foundPet) {
        selectedPet.value = foundPet;
      }
    }
  } catch (err) {
    // giữ danh sách rỗng/mặc định nếu có lỗi
    console.warn("Lỗi khi lấy thú cưng của khách hàng:", err);
  }
};

const services = ref([]);

const mapBackendServiceSimple = (item) => ({
  id: item.id,
  name: item.ten_dich_vu || item.name || item.title || item.ten || "Dịch vụ",
  description: item.mo_ta || item.description || item.desc || "",
  duration:
    item.thoi_luong ||
    item.duration ||
    item.duration_min ||
    item.duration_minutes ||
    30,
  price: item.gia || item.price || item.cost || 0,
});

const fetchServices = async () => {
  try {
    const res = await axios.get(`${API_BASE}/dich-vu`);
    let list = [];
    if (res.data && res.data.data) {
      if (Array.isArray(res.data.data)) list = res.data.data;
      else if (Array.isArray(res.data.data.data)) list = res.data.data.data;
    }

    services.value = list.map(mapBackendServiceSimple);
  } catch (err) {
    console.warn("Lỗi khi lấy danh sách dịch vụ:", err);
    // keep services empty so UI indicates no services; optionally fallback could be added
  }
};

const weekDays = ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"];

const timeSlots    = ref([]);
const loadingSlots = ref(false);
const slotsMessage = ref('');

// Trạng thái lựa chọn
const selectedPet = ref(null);
const selectedService = ref(null);
const selectedDate = ref(null);
const selectedTime = ref(null);
const paymentMethod = ref("online");

// Trạng thái thành công
const isSubmitting = ref(false);

// Tên khách hàng (ưu tiên tên người dùng đã đăng nhập)
const customerNameLocal = ref(props.customerName || "");

const refreshCustomerName = () => {
  try {
    const u = getUser();
    if (u) {
      customerNameLocal.value =
        u.full_name ||
        u.fullName ||
        u.name ||
        u.email ||
        props.customerName ||
        "";
    } else {
      customerNameLocal.value = props.customerName || "";
    }
  } catch (e) {
    customerNameLocal.value = props.customerName || "";
  }
};

// Trạng thái lịch
const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());

const currentMonthYear = computed(() => {
  const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];
  return `${months[currentMonth.value]} ${currentYear.value}`;
});

const calendarDates = computed(() => {
  const dates = [];
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  const firstDay = new Date(currentYear.value, currentMonth.value, 1);
  const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0);
  const startingDayOfWeek = firstDay.getDay();

  // Previous month dates
  const prevMonthLastDay = new Date(currentYear.value, currentMonth.value, 0);
  for (let i = startingDayOfWeek - 1; i >= 0; i--) {
    const day = prevMonthLastDay.getDate() - i;
    dates.push({
      day,
      isCurrentMonth: false,
      isPast: true,
      isSelected: false,
      date: new Date(currentYear.value, currentMonth.value - 1, day),
    });
  }

  // Current month dates
  for (let day = 1; day <= lastDay.getDate(); day++) {
    const date = new Date(currentYear.value, currentMonth.value, day);
    date.setHours(0, 0, 0, 0);
    const isPast = date < today;
    const isSelected =
      selectedDate.value &&
      date.getDate() === selectedDate.value.getDate() &&
      date.getMonth() === selectedDate.value.getMonth() &&
      date.getFullYear() === selectedDate.value.getFullYear();

    dates.push({
      day,
      isCurrentMonth: true,
      isPast,
      isSelected,
      date,
    });
  }

  // Next month dates to fill the grid
  const remainingDays = 42 - dates.length;
  for (let day = 1; day <= remainingDays; day++) {
    dates.push({
      day,
      isCurrentMonth: false,
      isPast: false,
      isSelected: false,
      date: new Date(currentYear.value, currentMonth.value + 1, day),
    });
  }

  return dates;
});

const canGoPrevious = computed(() => {
  const today = new Date();
  return (
    currentYear.value > today.getFullYear() ||
    (currentYear.value === today.getFullYear() &&
      currentMonth.value > today.getMonth())
  );
});

const canGoNext = computed(() => {
  // Allow navigation up to 6 months in the future
  const today = new Date();
  const maxDate = new Date(today.getFullYear(), today.getMonth() + 6);
  const currentDate = new Date(currentYear.value, currentMonth.value);
  return currentDate < maxDate;
});

// Thuộc tính tính toán (computed)
const canProceed = computed(() => {
  switch (currentStep.value) {
    case 0:
      return selectedPet.value !== null;
    case 1:
      return selectedService.value !== null;
    case 2:
      return selectedDate.value !== null && selectedTime.value !== null;
    default:
      return false;
  }
});

const canConfirm = computed(() => {
  return (
    selectedPet.value &&
    selectedService.value &&
    selectedDate.value &&
    selectedTime.value &&
    paymentMethod.value
  );
});

const formattedDateTime = computed(() => {
  if (!selectedDate.value || !selectedTime.value) return "";
  const day = selectedDate.value.getDate().toString().padStart(2, "0");
  const month = (selectedDate.value.getMonth() + 1).toString().padStart(2, "0");
  const year = selectedDate.value.getFullYear();
  return `${selectedTime.value} - ${day}/${month}/${year}`;
});

// Các phương thức
const selectPet = (pet) => {
  selectedPet.value = pet;
};

const selectService = (service) => {
  selectedService.value = service;
};

const fetchAvailableSlots = async (date) => {
  if (!date) return;
  const pad = (n) => String(n).padStart(2, '0');
  const dateStr = `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;

  loadingSlots.value = true;
  slotsMessage.value = '';
  timeSlots.value    = [];
  selectedTime.value = null;

  try {
    const res   = await axios.get(`${API_BASE}/lich-hen/available-slots`, { params: { date: dateStr } });
    const slots = res.data?.slots || [];

    if (slots.length === 0) {
      slotsMessage.value = res.data?.message || 'Phòng khám không có lịch làm việc cho ngày này, vui lòng chọn ngày khác';
    } else {
      timeSlots.value = slots.map((s) => ({
        value:    s.time,
        label:    s.time,
        isBooked: !s.available,
      }));
    }
  } catch (err) {
    slotsMessage.value = err.response?.data?.message || 'Không thể tải lịch khám cho ngày này';
  } finally {
    loadingSlots.value = false;
  }
};

const selectDate = (date) => {
  if (!date.isCurrentMonth || date.isPast) return;
  selectedDate.value = date.date;
  fetchAvailableSlots(date.date);
};

const selectTime = (time) => {
  if (time.isBooked) return;
  selectedTime.value = time.value;
};

const selectPaymentMethod = (method) => {
  paymentMethod.value = method;
};

const previousMonth = () => {
  if (!canGoPrevious.value) return;
  if (currentMonth.value === 0) {
    currentMonth.value = 11;
    currentYear.value--;
  } else {
    currentMonth.value--;
  }
};

const nextMonth = () => {
  if (!canGoNext.value) return;
  if (currentMonth.value === 11) {
    currentMonth.value = 0;
    currentYear.value++;
  } else {
    currentMonth.value++;
  }
};

const nextStep = () => {
  if (canProceed.value && currentStep.value < 3) {
    currentStep.value++;
  }
};

const previousStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--;
  }
};

const closePopup = () => {
  emit("close");
  if (route.path.includes("/appointments/book")) {
    router.push("/customer/appointments");
  }
  // Đặt lại sau khi animation kết thúc
  setTimeout(() => {
    currentStep.value = 0;
    selectedPet.value = null;
    selectedService.value = null;
    selectedDate.value = null;
    selectedTime.value = null;
    paymentMethod.value = "online";
  }, 300);
};

const confirmBooking = async () => {
  if (!canConfirm.value) return;

  // chuẩn bị payload mà backend mong đợi
  // backend expects: thu_cung_id, dich_vu_id, ngay_gio (Y-m-d H:i:s), optional ghi_chu
  const pad = (n) => String(n).padStart(2, "0");
  const day = pad(selectedDate.value.getDate());
  const month = pad(selectedDate.value.getMonth() + 1);
  const year = selectedDate.value.getFullYear();

  // selectedTime is like '08:30'
  const ngay_gio = `${year}-${month}-${day} ${selectedTime.value}:00`;

  const payload = {
    thu_cung_id: selectedPet.value.id,
    dich_vu_id: selectedService.value.id,
    ngay_gio,
    ghi_chu: `Phương thức: ${paymentMethod.value}`,
  };

  isSubmitting.value = true;
  try {
    const res = await axios.post(`${API_BASE}/lich-hen`, payload);

    const data = res.data && res.data.data ? res.data.data : null;

    showSuccessToast("Đặt lịch thành công", "Lịch hẹn của bạn đã được tạo.");
    closePopup();

    // phát sự kiện kèm phản hồi server khi có
    emit("confirm", data || payload);
  } catch (err) {
    let message = "Không thể tạo lịch hẹn. Vui lòng thử lại.";
    const errData = err.response?.data;

    if (err.response?.status === 422) {
      const errMsg = errData?.message || '';
      if (errMsg.includes('đã đầy')) {
        message = errMsg;
        // Refresh slots và đưa về bước chọn giờ
        await fetchAvailableSlots(selectedDate.value);
        selectedTime.value = null;
        currentStep.value  = 2;
      } else if (errData?.errors) {
        const firstKey = Object.keys(errData.errors)[0];
        if (firstKey) message = errData.errors[firstKey][0];
      } else {
        message = errMsg || message;
      }
    } else if (errData?.message) {
      message = errData.message;
    }

    showErrorToast("Lỗi khi đặt lịch", message);
  } finally {
    isSubmitting.value = false;
  }
};


const formatPrice = (price) => {
  if (!price) return "";
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  })
    .format(price)
    .replace("₫", "₫");
};

// Quan sát thay đổi tháng của lịch để xử lý (nếu cần) khi ngày được chọn nằm ngoài phạm vi
watch([currentMonth, currentYear], () => {
  if (selectedDate.value) {
    const selectedMonth = selectedDate.value.getMonth();
    const selectedYear = selectedDate.value.getFullYear();
    if (
      selectedMonth !== currentMonth.value ||
      selectedYear !== currentYear.value
    ) {
      // Keep the selection but just ensure it's visible when user navigates back
    }
  }
});

// Quan sát props.isOpen để xử lý dữ liệu ban đầu (hỗ trợ đặt lại / rebook)
watch(
  () => props.isOpen,
  (newVal) => {
    if (newVal) {
      // refresh pets each time modal opens so newly added pets are available
      fetchPets();
      // refresh services when modal opens too
      fetchServices();
      // refresh customer display name
      refreshCustomerName();
    }

    if (newVal && props.initialData) {
      const pet = pets.value.find((p) => p.name === props.initialData.petName);
      const service = services.value.find(
        (s) => s.name === props.initialData.serviceName
      );

      if (pet) selectedPet.value = pet;
      if (service) selectedService.value = service;

      // Handle pre-selected date if provided (for vaccination reminders)
      if (props.initialData.dueDate) {
        const [day, month, year] = props.initialData.dueDate.split("/");
        const preSelectedDate = new Date(
          parseInt(year),
          parseInt(month) - 1,
          parseInt(day)
        );
        selectedDate.value = preSelectedDate;

        // Navigate to the correct month
        currentMonth.value = preSelectedDate.getMonth();
        currentYear.value = preSelectedDate.getFullYear();
      }

      if (pet && service) {
        currentStep.value = 2; // Jump to Date/Time selection
      }
    }
  }
);

onMounted(() => {
  // initial fetch so component has user's pets
  fetchPets();
  // initial fetch services and customer name
  fetchServices();
  refreshCustomerName();
});
</script>

<style scoped>
/* Thanh cuộn tùy chỉnh cho modal */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
