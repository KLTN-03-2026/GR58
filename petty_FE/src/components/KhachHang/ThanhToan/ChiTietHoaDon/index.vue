<template>
  <!-- Popup Thanh toán (pending) - 2 steps: invoice + payment-method -->
  <div
    v-if="displayType === 'pending' && currentStep === 'invoice'"
    class="bg-white rounded-[10px] shadow-lg p-6 flex flex-col gap-4 max-w-[512px] w-full"
  >
    <!-- Dialog Header -->
    <div class="flex flex-col gap-2">
      <div class="h-7 flex items-center justify-between">
        <h2 class="font-bold text-lg leading-7 text-black">
          Thanh toán hoá đơn
        </h2>
        <button 
          @click="closePopup"
          class="w-7 h-7 cursor-pointer"
        >
          <img :src="iconClose" alt="Close" class="w-full h-full" />
        </button>
      </div>
      <div>
        <p class="font-medium text-sm leading-5 text-gray-500">
          Hóa đơn {{ invoiceData.invoiceCode }}
        </p>
      </div>
    </div>

    <!-- Payments Section -->
    <div class="flex flex-col gap-4">
      <!-- Service List -->
      <div class="flex flex-col gap-3">
        <p class="font-medium text-sm leading-5 text-gray-600">
          Danh sách dịch vụ
        </p>
        <div class="flex flex-col gap-2">
          <div
            v-for="service in invoiceData.services"
            :key="service.id"
            class="bg-gray-100 rounded-[10px] px-3 py-0 h-16 flex items-center justify-between"
          >
            <div class="flex flex-col">
              <p class="font-medium text-sm leading-5 text-black">
                {{ service.name }}
              </p>
              <p class="font-medium text-sm leading-5 text-gray-500">
                {{ service.note }}
              </p>
            </div>
            <p class="font-medium text-sm leading-5 text-black">
              {{ formatCurrency(service.price) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Divider -->
      <div class="h-px bg-black/15 w-full"></div>

      <!-- Payment Summary -->
      <div class="bg-amber-50 rounded-[10px] px-4 pt-4 pb-0 flex flex-col gap-2">
        <div class="flex items-center justify-between">
          <p class="font-medium text-sm leading-5 text-gray-600">
            Tổng cộng:
          </p>
          <p class="font-medium text-sm leading-5 text-gray-600">
            {{ formatCurrency(invoiceData.totalAmount) }}
          </p>
        </div>
        <div class="flex items-center justify-between">
          <p class="font-medium text-sm leading-5 text-gray-600">
            Đã thanh toán:
          </p>
          <p class="font-medium text-sm leading-5 text-gray-600">
            {{ formatCurrency(-invoiceData.paidAmount) }}
          </p>
        </div>
        <div class="h-px bg-black/15 w-full"></div>
        <div class="flex items-center justify-between">
          <p class="font-medium text-sm leading-5 text-amber-600">
            Cần thanh toán thêm:
          </p>
          <p class="font-medium text-sm leading-5 text-amber-600">
            {{ formatCurrency(invoiceData.remainingAmount) }}
          </p>
        </div>
      </div>
    </div>

    <!-- Dialog Footer -->
    <div class="flex items-start justify-end h-9">
      <button
        @click="handleConfirmPayment"
        class="bg-amber-600 rounded-lg px-4 py-[6px] flex items-center gap-3"
      >
        <div class="w-4 h-4">
          <img :src="iconCreditCard" alt="" class="w-full h-full" />
        </div>
        <span class="font-semibold text-base leading-6 text-white">
          Xác nhận & Thanh toán
        </span>
      </button>
    </div>
  </div>

  <!-- Popup phương thức thanh toán -->
  <div
    v-else-if="displayType === 'pending' && currentStep === 'payment-method'"
    class="bg-white border border-black/15 rounded-[10px] px-6 py-6 flex flex-col gap-4 max-w-[540px] w-full"
  >
    <!-- Header -->
    <div class="flex flex-col gap-2 w-full">
      <div class="h-7">
        <h2 class="font-bold text-lg leading-7 text-black">
          Chọn phương thức thanh toán
        </h2>
      </div>
      <div>
        <p class="font-medium text-sm leading-5 text-gray-500">
          Vui lòng chọn phương thức thanh toán bạn muốn sử dụng
        </p>
      </div>
    </div>

    <!-- Payment Methods -->
    <div class="flex flex-col gap-4 w-full">
      <!-- VNPay -->
      <div class="bg-white border border-teal-300 rounded-[10px] h-[98px] px-[17px] py-[17px] flex items-start justify-between">
        <div class="flex items-start gap-3">
          <div class="rounded-[10px] shadow-md w-10 h-10 flex items-center justify-center">
            <img :src="imgVNPay" alt="VNPay" class="w-full h-full rounded-[10px] object-contain" />
          </div>
          <div class="flex flex-col">
            <p class="font-medium text-sm leading-5 text-black">
              VNPay
            </p>
            <p class="font-medium text-xs leading-5 text-gray-600">
              Thanh toán qua VNPay
            </p>
          </div>
        </div>
        <button
          @click="handlePayment('vnpay')"
          class="bg-[#5a9690] rounded-lg px-3 py-1 flex items-center gap-2"
        >
          <div class="w-[13.333px] h-[9.333px]">
            <img :src="iconPayment" alt="" class="w-full h-full" />
          </div>
          <span class="font-semibold text-base leading-6 text-white">
            Thanh toán
          </span>
        </button>
      </div>

      <!-- MoMo -->
      <div class="bg-white border border-teal-300 rounded-[10px] h-[98px] px-[17px] py-[17px] flex items-start justify-between">
        <div class="flex items-start gap-3">
          <div class="rounded-[10px] shadow-md w-10 h-10 flex items-center justify-center">
            <img :src="imgMoMo" alt="MoMo" class="w-full h-full rounded-[10px] object-contain" />
          </div>
          <div class="flex flex-col">
            <p class="font-medium text-sm leading-5 text-black">
              MoMo
            </p>
            <p class="font-medium text-xs leading-5 text-gray-600">
              Thanh toán qua MoMo
            </p>
          </div>
        </div>
        <button
          @click="handlePayment('momo')"
          class="bg-[#5a9690] rounded-lg px-3 py-1 flex items-center gap-2"
        >
          <div class="w-[13.333px] h-[9.333px]">
            <img :src="iconPayment" alt="" class="w-full h-full" />
          </div>
          <span class="font-semibold text-base leading-6 text-white">
            Thanh toán
          </span>
        </button>
      </div>

      <!-- Techcombank -->
      <div class="bg-white border border-teal-300 rounded-[10px] h-[98px] px-[17px] py-[17px] flex items-start justify-between">
        <div class="flex items-start gap-3">
          <div class="rounded-[10px] shadow-md w-10 h-10 flex items-center justify-center">
            <img :src="imgTechcombank" alt="Techcombank" class="w-full h-full rounded-[10px] object-contain" />
          </div>
          <div class="flex flex-col">
            <p class="font-medium text-sm leading-6 text-black">
              Ngân hàng Techcombank
            </p>
            <p class="font-medium text-xs leading-5 text-gray-600">
              Chuyển khoản qua ngân hàng
            </p>
          </div>
        </div>
        <button
          @click="handlePayment('techcombank')"
          class="bg-[#5a9690] rounded-lg px-3 py-1 flex items-center gap-2"
        >
          <div class="w-[13.333px] h-[9.333px]">
            <img :src="iconPayment" alt="" class="w-full h-full" />
          </div>
          <span class="font-semibold text-base leading-6 text-white">
            Thanh toán
          </span>
        </button>
      </div>
    </div>

    <!-- Back Button -->
    <div class="flex justify-end">
      <button
        @click="goBack"
        class="bg-white border border-black/40 rounded-lg px-[17px] py-[9px] flex items-center gap-2"
      >
        <div class="w-6 h-6">
          <img :src="iconArrowLeft" alt="" class="w-full h-full" />
        </div>
        <span class="font-semibold text-base leading-6 text-black">
          Quay lại
        </span>
      </button>
    </div>
  </div>

  <!-- Popup Đã trả trước (prepaid) -->
  <div
    v-else-if="displayType === 'prepaid'"
    class="bg-white border border-black/15 rounded-[10px] p-6 flex flex-col gap-4 max-w-[512px] shadow-lg"
  >
    <!-- Header -->
    <div class="flex flex-col gap-2">
      <div class="h-7 relative">
        <div class="flex items-center justify-between">
          <h2 class="font-bold text-lg leading-7 text-black">
            Chi tiết lịch hẹn
          </h2>
          <button @click="closePopup" class="w-7 h-7">
            <img :src="iconClose" alt="Close" class="w-full h-full" />
          </button>
        </div>
      </div>
      <p class="font-medium text-sm leading-5 text-gray-500">
        Mã lịch hẹn: {{ invoiceData.appointmentCode }}
      </p>
    </div>

    <!-- Content -->
    <div class="flex flex-col gap-4">
      <!-- Status -->
      <div class="bg-blue-50 rounded-[10px] h-16 flex items-center px-3 gap-4">
        <div class="w-5 h-5">
          <img :src="iconCheckCircle" alt="" class="w-full h-full" />
        </div>
        <div class="flex flex-col">
          <p class="font-medium text-sm leading-5 text-blue-900">
            Đã xác nhận (Chờ khám)
          </p>
          <p class="font-medium text-sm leading-5 text-blue-700">
            Vui lòng đến đúng giờ hẹn
          </p>
        </div>
      </div>

      <!-- Details -->
      <div class="flex flex-col gap-3">
        <!-- Time -->
        <div class="flex items-start gap-3">
          <div class="w-5 h-5 mt-0.5">
            <img :src="iconCalendar" alt="" class="w-full h-full" />
          </div>
          <div class="flex flex-col">
            <p class="font-medium text-sm leading-5 text-gray-600">
              Thời gian
            </p>
            <p class="font-medium text-sm leading-6 text-black">
              {{ invoiceData.time }}
            </p>
          </div>
        </div>

        <!-- Pet -->
        <div class="flex items-start gap-3">
          <div class="w-5 h-5 mt-0.5">
            <img :src="iconPaw" alt="" class="w-full h-full" />
          </div>
          <div class="flex flex-col">
            <p class="font-medium text-sm leading-5 text-gray-600">
              Thú cưng
            </p>
            <p class="font-medium text-sm leading-6 text-sky-500">
              {{ invoiceData.petName }}
            </p>
          </div>
        </div>

        <!-- Service -->
        <div class="flex items-start gap-3">
          <div class="w-5 h-5 mt-0.5">
            <img :src="iconClipboard" alt="" class="w-full h-full" />
          </div>
          <div class="flex flex-col">
            <p class="font-medium text-sm leading-5 text-gray-600">
              Dịch vụ đã đặt
            </p>
            <p class="font-medium text-sm leading-6 text-black">
              {{ invoiceData.service }}
            </p>
          </div>
        </div>

        <!-- Staff -->
        <div class="flex items-start gap-3">
          <div class="w-5 h-5 mt-0.5">
            <img :src="iconUser" alt="" class="w-full h-full" />
          </div>
          <div class="flex flex-col">
            <p class="font-medium text-sm leading-5 text-gray-600">
              Bác sĩ/Nhân viên
            </p>
            <p class="font-medium text-sm leading-6 text-black">
              {{ invoiceData.staff }}
            </p>
          </div>
        </div>
      </div>

      <!-- Divider -->
      <div class="bg-black/15 h-px w-full"></div>

      <!-- Payment Status -->
      <div class="bg-gray-100 rounded-[10px] p-3 flex flex-col gap-1">
        <p class="font-medium text-sm leading-5 text-gray-600">
          Thanh toán
        </p>
        <div class="flex items-center gap-2">
          <div class="w-4 h-4">
            <img :src="iconCheckGreen" alt="" class="w-full h-full" />
          </div>
          <p class="font-medium text-sm leading-5 text-teal-600">
            Đã thanh toán trước {{ formatCurrency(invoiceData.paidAmount) }}
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Popup Đang hoàn tiền (refunding) -->
  <div
    v-else-if="displayType === 'refunding'"
    class="bg-white border border-black/15 rounded-[10px] p-6 flex flex-col gap-4 max-w-[512px] shadow-lg"
  >
    <!-- Header -->
    <div class="flex flex-col gap-2">
      <div class="h-7 relative">
        <div class="flex items-center justify-between">
          <h2 class="font-bold text-lg leading-7 text-black">
            Trạng thái Hoàn tiền
          </h2>
          <button @click="closePopup" class="w-7 h-7">
            <img :src="iconClose" alt="Close" class="w-full h-full" />
          </button>
        </div>
      </div>
      <p class="font-medium text-sm leading-5 text-gray-500">
        Mã hóa đơn: {{ invoiceData.invoiceCode }}
      </p>
    </div>

    <!-- Content -->
    <div class="flex flex-col gap-4">
      <!-- Clock Icon -->
      <div class="flex justify-center">
        <div class="bg-amber-100 rounded-full w-16 h-16 flex items-center justify-center">
          <div class="w-10 h-10">
            <img :src="iconClock" alt="" class="w-full h-full" />
          </div>
        </div>
      </div>

      <!-- Refund Info -->
      <div class="bg-gray-100 rounded-[10px] p-4 flex flex-col gap-3">
        <div class="flex justify-between">
          <p class="font-medium text-sm leading-6 text-gray-600">
            Mã hóa đơn gốc:
          </p>
          <p class="font-medium text-sm leading-6 text-black">
            {{ invoiceData.originalInvoice }}
          </p>
        </div>
        <div class="flex justify-between">
          <p class="font-medium text-sm leading-6 text-gray-600">
            Dịch vụ đã hủy:
          </p>
          <p class="font-medium text-sm leading-6 text-black">
            {{ invoiceData.canceledService }}
          </p>
        </div>
        <div class="bg-black/15 h-px w-full"></div>
        <div class="flex justify-between">
          <p class="font-medium text-sm leading-6 text-black">
            Số tiền hoàn:
          </p>
          <p class="font-medium text-sm leading-6 text-teal-600">
            + {{ formatCurrency(invoiceData.refundAmount) }}
          </p>
        </div>
      </div>

      <!-- Process Timeline -->
      <div class="flex flex-col gap-0">
        <p class="font-medium text-sm leading-5 text-gray-600 mb-4">
          Tiến trình xử lý:
        </p>

        <!-- Step 1: Completed -->
        <div class="flex items-center gap-3 mb-6">
          <div class="bg-gray-400 rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0">
            <div class="w-5 h-5">
              <img :src="iconCheck" alt="" class="w-full h-full" />
            </div>
          </div>
          <p class="font-medium text-sm leading-6 text-gray-500">
            Yêu cầu đã được tiếp nhận
          </p>
        </div>

        <!-- Connector -->
        <div class="border-l-2 border-amber-200 h-6 ml-4"></div>

        <!-- Step 2: In Progress -->
        <div class="flex items-start gap-3 mb-6">
          <div class="bg-amber-400 rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0">
            <div class="w-5 h-5">
              <img :src="iconClockWhite" alt="" class="w-full h-full" />
            </div>
          </div>
          <div class="flex flex-col">
            <p class="font-medium text-sm leading-6 text-amber-700">
              Đang xử lý hoàn tiền
            </p>
            <p class="font-medium text-sm leading-5 text-amber-500">
              (Đang thực hiện)
            </p>
          </div>
        </div>

        <!-- Connector -->
        <div class="border-l-2 border-gray-300 h-6 ml-4"></div>

        <!-- Step 3: Pending -->
        <div class="flex items-center gap-3">
          <div class="bg-gray-300 rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0">
            <div class="w-5 h-5">
              <img :src="iconCheckCheck" alt="" class="w-full h-full" />
            </div>
          </div>
          <p class="font-medium text-sm leading-6 text-gray-400">
            Hoàn tất
          </p>
        </div>
      </div>

      <!-- Notice -->
      <div class="bg-amber-50 border border-amber-200 rounded-[10px] p-4">
        <p class="font-medium text-sm leading-5 text-gray-600">
          Yêu cầu hoàn tiền của bạn đang được Petty xử lý. Số tiền sẽ được hoàn về phương thức thanh toán ban đầu (VNPay/MoMo...) trong vòng <span class="text-amber-700">24 giờ làm việc</span>.
        </p>
      </div>
    </div>
  </div>

  <!-- Popup Đã hoàn tiền (refunded) -->
  <div
    v-else-if="displayType === 'refunded'"
    class="bg-white rounded-lg shadow-[0px_20px_25px_-5px_rgba(0,0,0,0.1),0px_8px_10px_-6px_rgba(0,0,0,0.1)] p-6 flex flex-col gap-4 items-center max-w-[464px]"
  >
    <!-- Success Icon -->
    <div class="bg-teal-100 rounded-full w-16 h-16 flex items-center justify-center">
      <div class="w-10 h-10">
        <img :src="iconCheckCircleGreen" alt="" class="w-full h-full" />
      </div>
    </div>

    <!-- Title -->
    <h2 class="font-semibold text-2xl leading-8 text-black text-center">
      Hoàn tiền thành công
    </h2>

    <!-- Subtitle -->
    <p class="font-medium text-base leading-5 text-gray-500 text-center">
      CREDIT NOTE
    </p>

    <!-- Refund Details -->
    <div class="border-t border-b border-gray-200 py-4 flex flex-col gap-4 w-full">
      <div class="flex justify-between">
        <p class="font-medium text-sm leading-5 text-gray-500">
          Mã hoàn tiền
        </p>
        <p class="font-medium text-sm leading-5 text-black">
          {{ invoiceData.refundCode }}
        </p>
      </div>
      <div class="flex justify-between">
        <p class="font-medium text-sm leading-5 text-gray-500">
          Mã hóa đơn gốc
        </p>
        <p class="font-medium text-sm leading-5 text-black">
          {{ invoiceData.originalInvoice }}
        </p>
      </div>
      <div class="flex justify-between">
        <p class="font-medium text-sm leading-5 text-gray-500">
          Ngày hoàn tiền
        </p>
        <p class="font-medium text-sm leading-5 text-black">
          {{ invoiceData.refundDate }}
        </p>
      </div>
      <div class="flex justify-between items-center">
        <p class="font-medium text-sm leading-5 text-gray-500">
          Phương thức
        </p>
        <div class="flex items-center gap-2">
          <div class="w-5 h-5 rounded-sm">
            <img :src="invoiceData.paymentMethodLogo" alt="" class="w-full h-full object-contain rounded-sm" />
          </div>
          <p class="font-medium text-sm leading-5 text-black">
            {{ invoiceData.paymentMethodName }}
          </p>
        </div>
      </div>
    </div>

    <!-- Service Details -->
    <div class="flex flex-col gap-3 w-full">
      <p class="font-medium text-sm leading-5 text-black">
        Chi tiết hoàn tiền
      </p>
      <div class="flex flex-col gap-3">
        <div
          v-for="item in invoiceData.items"
          :key="item.id"
          class="flex justify-between"
        >
          <p class="font-medium text-sm leading-5 text-gray-600">
            {{ item.name }}
          </p>
          <p class="font-medium text-sm leading-5 text-black">
            +{{ formatCurrency(item.amount) }}
          </p>
        </div>
        <div class="border-t border-dashed border-gray-100 h-px w-full"></div>
        <div class="flex justify-between">
          <p class="font-medium text-sm leading-5 text-black">
            Tổng tiền hoàn
          </p>
          <p class="font-semibold text-sm leading-5 text-purple-700">
            +{{ formatCurrency(invoiceData.totalRefund) }}
          </p>
        </div>
      </div>
    </div>

    <!-- Close Button -->
    <button
      @click="closePopup"
      class="bg-[#5a9690] rounded-lg px-4 py-2 w-full h-9 flex items-center justify-center"
    >
      <span class="font-semibold text-base leading-5 text-white">
        Đóng
      </span>
    </button>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  paymentStatus: {
    type: String,
    default: 'pending', // 'pending', 'prepaid', 'refunding', 'refunded'
    validator: (value) => ['pending', 'prepaid', 'refunding', 'refunded'].includes(value)
  },
  invoiceData: {
    type: Object,
    default: () => ({})
  }
});

// Emits
const emit = defineEmits(['close', 'payment-success']);

// Icon URLs
const iconClose = "https://www.figma.com/api/mcp/asset/ff6f3b80-2822-46c6-90c6-9d0dd49536aa";
const iconCreditCard = "https://www.figma.com/api/mcp/asset/657598ce-23a9-4493-ad54-d9e2ba05568d";
const iconPayment = "https://www.figma.com/api/mcp/asset/96b81acd-758d-48ba-91cf-8138f3483727";
const iconArrowLeft = "https://www.figma.com/api/mcp/asset/43afa04c-19b6-446d-acb7-4d8820adbbc4";
const iconCheckCircle = "https://www.figma.com/api/mcp/asset/4b19e1bc-3c3f-4445-bcdc-8b41d2263707";
const iconCalendar = "https://www.figma.com/api/mcp/asset/83b2216b-9633-4b4a-a23e-f41212d9f88e";
const iconPaw = "https://www.figma.com/api/mcp/asset/7894eae5-767c-49a0-be23-1daec3e151ef";
const iconClipboard = "https://www.figma.com/api/mcp/asset/5e7227af-99a9-47a2-a5c2-97c1a9196677";
const iconUser = "https://www.figma.com/api/mcp/asset/7894eae5-767c-49a0-be23-1daec3e151ef";
const iconCheckGreen = "https://www.figma.com/api/mcp/asset/9946d90b-c533-43ee-af69-f4dda8a6c1fc";
const iconClock = "https://www.figma.com/api/mcp/asset/00a0a401-ab05-40e8-9db6-1eb738a25e48";
const iconCheck = "https://www.figma.com/api/mcp/asset/8506c06d-ddb4-4be3-958b-14ff3514922a";
const iconClockWhite = "https://www.figma.com/api/mcp/asset/ad8a9885-677a-4850-b216-30197dcdc3f1";
const iconCheckCheck = "https://www.figma.com/api/mcp/asset/fbbf358f-147e-4f66-8e66-cf3f3c336f89";
const iconCheckCircleGreen = "https://www.figma.com/api/mcp/asset/9a12eb42-8255-4461-ad95-1d252a503542";

// Payment method images
const imgVNPay = "https://www.figma.com/api/mcp/asset/71cac2aa-e112-442f-9d92-fcb101db9d6f";
const imgMoMo = "https://www.figma.com/api/mcp/asset/60cb4d9d-1a12-4497-bcd2-efdeeaac0a08";
const imgTechcombank = "https://www.figma.com/api/mcp/asset/082e895d-1b45-4f48-98e0-07588a5db50d";

// State
const currentStep = ref('invoice'); // 'invoice' or 'payment-method' (only for pending status)
const displayType = computed(() => props.paymentStatus);

// Watch for popup open/close to reset step
watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    currentStep.value = 'invoice';
  }
});

// Methods
const formatCurrency = (amount) => {
  const absAmount = Math.abs(amount);
  const formatted = new Intl.NumberFormat('vi-VN', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(absAmount);
  
  return amount < 0 ? `-${formatted} ₫` : `${formatted} ₫`;
};

const closePopup = () => {
  currentStep.value = 'invoice';
  emit('close');
};

const handleConfirmPayment = () => {
  currentStep.value = 'payment-method';
};

const goBack = () => {
  currentStep.value = 'invoice';
};

const handlePayment = (method) => {
  console.log('Payment method selected:', method);
  
  // Simulate payment processing
  setTimeout(() => {
    emit('payment-success', {
      invoiceCode: props.invoiceData.invoiceCode,
      paymentMethod: method,
      amount: props.invoiceData.remainingAmount,
      timestamp: new Date().toISOString()
    });
    closePopup();
  }, 500);
};
</script>

<style scoped>
/* Custom styles if needed */
</style>
