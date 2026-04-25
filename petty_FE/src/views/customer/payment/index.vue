<template>
  <div class="min-h-screen font-nunitoSans">
    <div class="container mx-auto max-w-6xl">
      <div class="flex flex-col gap-6 items-start w-full">
        <!-- Header -->
        <div class="flex flex-col h-[54px] items-start w-full">
          <div class="h-[30px] w-full">
            <p class="font-bold text-xl leading-7 text-black">Thanh toán</p>
          </div>
          <div class="h-6 w-full">
            <p class="font-medium text-lg leading-6 text-gray-700">
              Quản lý hóa đơn và lịch sử thanh toán
            </p>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-3 gap-6 w-full">
          <!-- Card 1: Tổng đã thanh toán -->
          <div
            class="bg-white border !border-teal-400 rounded-[14px] h-[164px] px-6 py-0 flex flex-col justify-between"
          >
            <div class="h-5 pt-[25px]">
              <p class="font-medium text-base leading-6 text-gray-700">
                Tổng đã thanh toán
              </p>
            </div>
            <div class="flex flex-col gap-1 pb-[25px]">
              <p class="font-semibold text-4xl leading-6 text-teal-500">
                {{ formatCurrency(totalPaid) }}
              </p>
              <p class="font-medium text-base leading-6 text-gray-700">
                {{ paidInvoiceCount }} hóa đơn
              </p>
            </div>
          </div>

          <!-- Card 2: Tổng dư nợ -->
          <div
            class="bg-white border !border-amber-300 rounded-[14px] h-[164px] px-6 py-0 flex flex-col justify-between"
          >
            <div class="h-5 pt-[25px]">
              <p class="font-medium text-base leading-6 text-gray-700">
                Tổng dư nợ
              </p>
            </div>
            <div class="flex flex-col gap-1 pb-[25px]">
              <p class="font-semibold text-4xl leading-6 text-amber-700">
                {{ formatCurrency(totalDebt) }}
              </p>
              <p class="font-medium text-base leading-6 text-gray-700">
                {{ debtInvoiceCount }} hóa đơn
              </p>
            </div>
          </div>

          <!-- Card 3: Tổng chi tiêu -->
          <div
            class="bg-white border !border-gray-400 rounded-[14px] h-[164px] px-6 py-0 flex flex-col justify-between"
          >
            <div class="h-5 pt-[25px]">
              <p class="font-medium text-base leading-6 text-gray-700">
                Tổng chi tiêu
              </p>
            </div>
            <div class="flex flex-col gap-1 pb-[25px]">
              <p class="font-semibold text-4xl leading-6 text-black">
                {{ formatCurrency(totalSpending) }}
              </p>
              <p class="font-medium text-base leading-6 text-gray-700">
                Trong năm {{ currentYear }}
              </p>
            </div>
          </div>
        </div>

        <!-- Payment History -->
        <div
          class="bg-white border !border-black/15 shadow-sm rounded-[14px] px-8 py-6 flex flex-col gap-6 w-full"
        >
          <!-- Card Header -->
          <div class="flex flex-col h-[70px] items-start w-full">
            <p class="font-bold text-lg leading-7 text-black">
              Lịch sử giao dịch
            </p>
            <p class="font-normal text-base leading-6 text-gray-500">
              Danh sách các hóa đơn và thanh toán
            </p>
          </div>

          <!-- Filter Payments -->
          <div
            class="bg-white rounded-[10px] shadow-md px-6 py-6 flex items-center gap-9"
          >
            <div class="flex items-center gap-2">
              <p class="font-medium text-base leading-6 text-black">
                Lọc theo:
              </p>
            </div>

            <div class="flex items-center gap-6">
              <!-- Filter: Dịch vụ -->
              <div class="relative">
                <button
                  @click="toggleServiceFilter"
                  class="bg-gray-200 border !border-black/15 rounded-lg px-[13px] py-[9px] flex items-center gap-16"
                >
                  <span class="text-sm font-medium text-gray-500 w-[111px]">
                    {{ selectedService || "Dịch vụ" }}
                  </span>
                  <div class="">
                    <ChevronDownIcon class="text-gray-500" />
                  </div>
                </button>
              </div>

              <!-- Filter: Tháng -->
              <div class="relative">
                <button
                  @click="toggleMonthFilter"
                  class="bg-gray-200 border !border-black/15 rounded-lg px-[13px] py-[9px] flex items-center gap-16"
                >
                  <span class="text-sm font-medium text-gray-500 w-[111px]">
                    {{ selectedMonth || "Tháng" }}
                  </span>
                  <div>
                    <ChevronDownIcon class="text-gray-500" />
                  </div>
                </button>
              </div>

              <!-- Filter: Năm -->
              <div class="relative">
                <button
                  @click="toggleYearFilter"
                  class="bg-gray-200 border !border-black/15 rounded-lg px-[13px] py-[9px] flex items-center gap-16"
                >
                  <span class="text-sm font-medium text-gray-500 w-[111px]">
                    {{ selectedYear || "Năm" }}
                  </span>
                  <div>
                    <ChevronDownIcon class="text-gray-500" />
                  </div>
                </button>
              </div>
            </div>

            <!-- Clear Filters -->
            <button
              @click="clearFilters"
              class="rounded-lg px-3 py-0 flex items-center justify-center"
            >
              <p class="font-semibold text-base leading-5 text-teal-700">
                Xóa bộ lọc
              </p>
            </button>
          </div>

          <!-- Payments Table -->
          <div class="flex flex-col gap-4">
            <!-- Empty State -->
            <div v-if="payments.length === 0 && !isLoading" class="flex flex-col items-center justify-center py-12 gap-4">
              <div class="text-gray-400 text-6xl">📋</div>
              <p class="font-medium text-lg text-gray-600">Chưa có lịch hẹn nào</p>
              <p class="font-normal text-sm text-gray-500">Các hóa đơn thanh toán sẽ hiển thị tại đây sau khi bạn đặt lịch khám</p>
            </div>

            <!-- Loading State -->
            <div v-else-if="isLoading" class="flex items-center justify-center py-12">
              <p class="font-medium text-lg text-gray-600">Đang tải dữ liệu...</p>
            </div>

            <!-- Table with Data -->
            <div v-else class="flex flex-col overflow-hidden w-full">
              <!-- Table Header -->
              <div class="border-b border-black/15 h-10 flex items-center">
                <div class="w-[126.688px] px-2">
                  <p class="font-semibold text-base leading-6 text-black">
                    Mã hóa đơn
                  </p>
                </div>
                <div class="w-[301.516px] px-2">
                  <p class="font-semibold text-base leading-6 text-black">
                    Dịch vụ
                  </p>
                </div>
                <div class="w-[136.531px] px-2">
                  <p class="font-semibold text-base leading-6 text-black">
                    Ngày khám
                  </p>
                </div>
                <div class="w-[250.727px] px-2">
                  <p class="font-semibold text-base leading-6 text-black">
                    Trạng thái & Số tiền
                  </p>
                </div>
                <div class="flex-1 px-2">
                  <p
                    class="font-semibold text-base leading-6 text-black text-right"
                  >
                    Thao tác
                  </p>
                </div>
              </div>

              <!-- Table Body -->
              <div class="flex flex-col">
                <div
                  v-for="payment in paginatedPayments"
                  :key="payment.id"
                  class="border-b border-black/15 h-[53px] flex items-center"
                >
                  <!-- Mã hóa đơn -->
                  <div class="w-[126.688px] px-2">
                    <p
                      class="font-bold text-sm leading-5"
                      :class="getInvoiceCodeColor(payment.status)"
                    >
                      {{ payment.invoiceCode }}
                    </p>
                  </div>

                  <!-- Dịch vụ -->
                  <div class="w-[301.516px] px-2">
                    <p class="font-medium text-sm leading-5 text-black">
                      {{ payment.service }}
                    </p>
                  </div>

                  <!-- Ngày khám -->
                  <div class="w-[136.531px] px-2">
                    <p class="font-medium text-sm leading-5 text-black">
                      {{ payment.date }}
                    </p>
                  </div>

                  <!-- Trạng thái & Số tiền -->
                  <div class="w-[250.727px] px-2">
                    <div class="flex flex-col gap-0">
                      <p
                        class="font-semibold text-sm leading-5"
                        :class="getStatusColor(payment.status)"
                      >
                        {{ payment.statusText }}
                      </p>
                      <p class="font-medium text-sm leading-5 text-gray-500">
                        {{ payment.amountText }}
                      </p>
                    </div>
                  </div>

                  <!-- Thao tác -->
                  <div class="flex-1 px-2 flex justify-end">
                    <button
                      v-if="payment.status === 'pending'"
                      @click="handlePayNow(payment)"
                      class="bg-amber-600 rounded-lg px-3 py-1 flex items-center gap-2"
                    >
                      <div class="w-4 h-4">
                        <WalletIcon />
                      </div>
                      <span
                        class="font-semibold text-base leading-6 text-white"
                      >
                        Thanh toán ngay
                      </span>
                    </button>

                    <button
                      v-else-if="
                        payment.status === 'prepaid' ||
                        payment.status === 'refunded'
                      "
                      @click="handleViewDetail(payment)"
                      class="bg-white border !border-black/15 rounded-lg px-3 py-1 flex items-center gap-2"
                    >
                      <div class="w-4 h-4">
                        <EyeIcon />
                      </div>
                      <span
                        class="font-semibold text-base leading-6 text-black"
                      >
                        Xem chi tiết
                      </span>
                    </button>

                    <button
                      v-else-if="payment.status === 'completed'"
                      @click="handleViewReceipt(payment)"
                      class="bg-white border !border-black/15 rounded-lg px-3 py-1 flex items-center gap-2"
                    >
                      <div class="w-4 h-4">
                        <DownloadIcon />
                      </div>
                      <span
                        class="font-semibold text-base leading-6 text-black"
                      >
                        Xem biên lai
                      </span>
                    </button>

                    <button
                      v-else-if="payment.status === 'refunding'"
                      @click="handleViewRefundStatus(payment)"
                      class="bg-white border !border-black/15 rounded-lg px-3 py-1 flex items-center gap-2"
                    >
                      <div class="!text-black">
                        <ClockIcon class="w-4 h-4" />
                      </div>
                      <span
                        class="font-semibold text-base leading-6 text-black"
                      >
                        Đang xử lý
                      </span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between w-full">
              <div class="flex items-center gap-1">
                <p class="font-medium text-sm leading-4 text-gray-600">
                  Hiển thị
                </p>
                <p class="font-medium text-base leading-4 text-[#101828]">
                  {{ startIndex }}-{{ endIndex }}
                </p>
                <p class="font-medium text-sm leading-4 text-gray-600">trong</p>
                <p class="font-medium text-base leading-4 text-[#101828]">
                  {{ totalPayments }}
                </p>
                <p class="font-medium text-sm leading-4 text-gray-600">
                  giao dịch
                </p>
              </div>

              <div class="flex items-center gap-2">
                <!-- Previous Button -->
                <button
                  @click="prevPage"
                  :disabled="currentPage === 1"
                  :class="{ 'opacity-50': currentPage === 1 }"
                  class="bg-white border border-black/20 rounded-lg px-[17px] py-px flex items-center justify-center"
                >
                  <div class="p-1">
                    <ChevronLeftIcon class="text-gray-500" />
                  </div>
                </button>

                <!-- Page Numbers -->
                <button
                  v-for="page in visiblePages"
                  :key="page"
                  @click="goToPage(page)"
                  :class="
                    page === currentPage
                      ? 'bg-teal-700 text-white'
                      : 'bg-white text-black'
                  "
                  class="border border-black/10 rounded-lg h-8 px-[13px] py-px flex items-center justify-center min-w-[32px]"
                >
                  <span class="font-semibold text-sm leading-5">
                    {{ page }}
                  </span>
                </button>

                <!-- Next Button -->
                <button
                  @click="nextPage"
                  :disabled="currentPage === totalPages"
                  class="bg-white border border-black/20 rounded-lg px-[17px] py-px flex items-center justify-center"
                >
                  <div class="p-1">
                    <ChevronRightIcon class="text-gray-500" />
                  </div>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Methods -->
        <div
          class="bg-teal-50/50 border !border-teal-500 rounded-[14px] px-8 py-6 flex flex-col gap-6 w-full"
        >
          <!-- Card Header -->
          <div class="flex flex-col h-[70px] items-start w-full">
            <p class="font-bold text-xl leading-6 text-teal-800">
              Phương thức thanh toán
            </p>
            <p class="font-normal text-lg leading-6 text-teal-600">
              Liên kết ví điện tử và tài khoản ngân hàng để thanh toán nhanh hơn
            </p>
          </div>

          <!-- Payment Method List -->
          <div class="flex flex-col gap-4">
            <!-- VNPay -->
            <div
              class="bg-white border !border-teal-300 rounded-[10px] h-[82px] px-[17px] py-px flex items-center justify-between"
            >
              <div class="flex items-center gap-3">
                <div class="rounded-[10px] shadow-md w-12 h-12">
                  <img
                    src="/src/assets/img_imports/public_img/vnpay.png"
                    alt="VNPay"
                    class="w-full h-full rounded-[10px] object-contain"
                  />
                </div>
                <div class="flex flex-col">
                  <p class="font-medium text-base leading-6 text-black">
                    VNPay
                  </p>
                  <p class="font-medium text-sm leading-6 text-gray-500">
                    Ví điện tử VNPay
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <div
                  class="bg-gray-100 border !border-black/15 rounded-lg px-[17px] py-[3px]"
                >
                  <p class="font-semibold text-sm leading-5 text-black/60">
                    Chưa liên kết
                  </p>
                </div>
                <button
                  @click="handleLinkPayment('vnpay')"
                  class="bg-white border !border-teal-500 rounded-lg px-[13px] py-[9px]"
                >
                  <p class="font-semibold text-sm leading-5 text-teal-500">
                    Liên kết
                  </p>
                </button>
              </div>
            </div>

            <!-- MoMo -->
            <div
              class="bg-white border !border-teal-300 rounded-[10px] h-[82px] px-[17px] py-px flex items-center justify-between"
            >
              <div class="flex items-center gap-3">
                <div class="rounded-[10px] shadow-md w-12 h-12">
                  <img
                    src="/src/assets/img_imports/public_img/momo.png"
                    alt="MoMo"
                    class="w-full h-full rounded-[10px] object-contain"
                  />
                </div>
                <div class="flex flex-col">
                  <p class="font-medium text-base leading-6 text-black">MoMo</p>
                  <p class="font-medium text-sm leading-6 text-gray-500">
                    Ví điện tử MoMo
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <div
                  class="bg-gray-100 border !border-black/15 rounded-lg px-[17px] py-[3px]"
                >
                  <p class="font-semibold text-sm leading-5 text-black/60">
                    Chưa liên kết
                  </p>
                </div>
                <button
                  @click="handleLinkPayment('momo')"
                  class="bg-white border !border-teal-500 rounded-lg px-[13px] py-[9px]"
                >
                  <p class="font-semibold text-sm leading-5 text-teal-500">
                    Liên kết
                  </p>
                </button>
              </div>
            </div>

            <!-- Techcombank (Linked)
            <div class="bg-white border border-green-400 rounded-[10px] h-[82px] px-[17px] py-px flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="rounded-[10px] shadow-md w-12 h-12">
                  <img :src="imgTechcombank" alt="Techcombank" class="w-full h-full rounded-[10px] object-contain" />
                </div>
                <div class="flex flex-col">
                  <p class="font-medium text-base leading-6 text-black">
                    Ngân hàng Techcombank
                  </p>
                  <p class="font-medium text-sm leading-6 text-gray-500">
                    **** **** **** 1234
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <div class="bg-green-100 border border-black/15 rounded-lg px-4 py-[2px] flex items-center gap-2">
                  <div class="w-3 h-3">
                    <img :src="iconCheck" alt="" class="w-full h-full" />
                  </div>
                  <p class="font-semibold text-sm leading-5 text-green-600">
                    Đã liên kết
                  </p>
                </div>
                <button
                  @click="handleUnlinkPayment('techcombank')"
                  class="bg-white border border-red-300 rounded-lg px-[13px] py-[9px]"
                >
                  <p class="font-semibold text-sm leading-5 text-red-600">
                    Hủy liên kết
                  </p>
                </button>
              </div>
            </div> -->

            <!-- Add Bank Account -->
            <!-- <div class="bg-white border border-gray-300 rounded-[10px] h-[82px] px-[17px] py-px flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="bg-gray-100 rounded-[10px] shadow-md w-12 h-12 flex items-center justify-center">
                  <div class="w-6 h-6">
                    <img :src="iconBank" alt="" class="w-full h-full" />
                  </div>
                </div>
                <div class="flex flex-col">
                  <p class="font-medium text-base leading-6 text-black">
                    Tài khoản ngân hàng
                  </p>
                  <p class="font-medium text-sm leading-6 text-gray-500">
                    Liên kết thêm tài khoản ngân hàng
                  </p>
                </div>
              </div>
              <button
                @click="handleAddBankAccount"
                class="bg-[#5a9690] rounded-lg px-3 py-2 flex items-center gap-2"
              >
                <div class="w-4 h-4">
                  <img :src="iconPlus" alt="" class="w-full h-full" />
                </div>
                <p class="font-semibold text-sm leading-5 text-white">
                  Thêm tài khoản
                </p>
              </button>
            </div> -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Payment Popup -->
  <Teleport to="body">
    <div
      v-if="showPaymentPopup"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closePaymentPopup"
    >
      <ChiTietHoaDon
        :is-open="showPaymentPopup"
        :payment-status="paymentStatus"
        :invoice-data="selectedPayment || {}"
        @close="closePaymentPopup"
        @payment-success="handlePaymentSuccess"
      />
    </div>
  </Teleport>

  <!-- Receipt Popup -->
  <Teleport to="body">
    <div
      v-if="showReceiptPopup"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeReceiptPopup"
    >
      <BienLaiThanhToan
        :is-open="showReceiptPopup"
        :receipt-data="selectedReceipt || {}"
        @close="closeReceiptPopup"
      />
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import ChiTietHoaDon from "./invoice-detail/index.vue";
import BienLaiThanhToan from "./payment-receipt/index.vue";
import { getPaymentInvoices } from "@/utils/payment.js";
// Icon
import Calendar from "@/assets/svg/calendar.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import ChevronRightIcon from "@/assets/svg/chevron-right.svg";
import ChevronLeftIcon from "@/assets/svg/chevron-left.svg";
import EyeIcon from "@/assets/svg/eye.svg";
import DownloadIcon from "@/assets/svg/download.svg";
import WalletIcon from "@/assets/svg/wallet.svg";
import ClockIcon from "@/assets/svg/clock.svg";

// Statistics data
const totalPaid = ref(0);
const paidInvoiceCount = ref(0);
const totalDebt = ref(0);
const debtInvoiceCount = ref(0);
const totalSpending = ref(0);
const currentYear = ref(new Date().getFullYear());
const isLoading = ref(false);

// Filter state
const selectedService = ref("");
const selectedMonth = ref("");
const selectedYear = ref("");

// Payment popup state
const showPaymentPopup = ref(false);
const selectedPayment = ref(null);
const paymentStatus = ref("pending");
const showReceiptPopup = ref(false);
const selectedReceipt = ref(null);

// Payments data
const payments = ref([]);

// Pagination
const currentPage = ref(1);
const itemsPerPage = ref(6);

const totalPayments = computed(() => payments.value.length);
const totalPages = computed(() =>
  Math.ceil(totalPayments.value / itemsPerPage.value)
);

const paginatedPayments = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return payments.value.slice(start, end);
});

const startIndex = computed(
  () => (currentPage.value - 1) * itemsPerPage.value + 1
);
const endIndex = computed(() =>
  Math.min(currentPage.value * itemsPerPage.value, totalPayments.value)
);

const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 3;

  for (let i = 1; i <= Math.min(maxVisible, totalPages.value); i++) {
    pages.push(i);
  }

  return pages;
});

// Methods
const formatCurrency = (amount) => {
  return (
    new Intl.NumberFormat("vi-VN", {
      style: "decimal",
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(amount) + " ₫"
  );
};

const getInvoiceCodeColor = (status) => {
  const colors = {
    pending: "text-amber-600",
    prepaid: "text-blue-500",
    completed: "text-green-600",
    refunding: "text-yellow-500",
    refunded: "text-purple-600",
  };
  return colors[status] || "text-black";
};

const getStatusColor = (status) => {
  const colors = {
    pending: "text-amber-600",
    prepaid: "text-blue-500",
    completed: "text-green-600",
    refunding: "text-yellow-500",
    refunded: "text-purple-600",
  };
  return colors[status] || "text-black";
};

const toggleServiceFilter = () => {
  // Implement dropdown toggle
  console.log("Toggle service filter");
};

const toggleMonthFilter = () => {
  // Implement dropdown toggle
  console.log("Toggle month filter");
};

const toggleYearFilter = () => {
  // Implement dropdown toggle
  console.log("Toggle year filter");
};

const clearFilters = () => {
  selectedService.value = "";
  selectedMonth.value = "";
  selectedYear.value = "";
};

const handlePayNow = (payment) => {
  console.log("Pay now:", payment);
  selectedPayment.value = {
    invoiceCode: payment.invoiceCode,
    services: [
      {
        id: 1,
        name: payment.service,
        note: "(Dịch vụ đã đặt)",
        price: payment.totalAmount,
      },
    ],
    totalAmount: payment.totalAmount,
    paidAmount: payment.paidAmount || 0,
    remainingAmount: payment.totalAmount - (payment.paidAmount || 0),
  };
  paymentStatus.value = "pending";
  showPaymentPopup.value = true;
};

const handleViewDetail = (payment) => {
  console.log("View detail:", payment);

  if (payment.status === "prepaid") {
    // Show prepaid appointment details
    paymentStatus.value = "prepaid";
    selectedPayment.value = {
      appointmentCode: payment.invoiceCode,
      time: `09:00 - ${payment.date}`,
      petName: "Luna", // Mock data - should come from payment
      service: payment.service,
      staff: "NV. Lê Thị C", // Mock data
      paidAmount: payment.paidAmount,
    };
  } else if (payment.status === "refunded") {
    // Show refunded details
    paymentStatus.value = "refunded";
    selectedPayment.value = {
      refundCode: payment.invoiceCode.replace("HD", "HT"),
      originalInvoice: payment.invoiceCode,
      refundDate: payment.date,
      paymentMethodLogo:
        "https://www.figma.com/api/mcp/asset/1d522772-1899-4c7a-ab6f-b5fd5ce56053",
      paymentMethodName: "Ví Momo",
      items: [{ id: 1, name: payment.service, amount: payment.totalAmount }],
      totalRefund: payment.totalAmount,
    };
  }

  showPaymentPopup.value = true;
};

const handleViewReceipt = (payment) => {
  console.log("View receipt:", payment);
  selectedReceipt.value = {
    invoiceCode: payment.invoiceCode,
    visitDate: payment.date,
    petName: "Luna", // Mock data - should come from payment
    doctor: "BS. Trần Văn D", // Mock data
    services: [{ id: 1, name: payment.service, price: payment.totalAmount }],
    totalAmount: payment.totalAmount,
    paidAmount: payment.paidAmount,
    prepaidAmount: payment.paidAmount,
  };
  showReceiptPopup.value = true;
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const goToPage = (page) => {
  currentPage.value = page;
};

const handleLinkPayment = (method) => {
  console.log("Link payment:", method);
  // Implement payment method linking
};

const handleUnlinkPayment = (method) => {
  console.log("Unlink payment:", method);
  // Implement payment method unlinking
};

const handleAddBankAccount = () => {
  console.log("Add bank account");
  // Implement add bank account
};

const handlePaymentSuccess = (paymentData) => {
  console.log("Payment successful:", paymentData);
  // TODO: Update payment status in the list
  // TODO: Show success notification
  alert(`Thanh toán thành công! Mã hóa đơn: ${paymentData.invoiceCode}`);
  showPaymentPopup.value = false;
  selectedPayment.value = null;
};

const closePaymentPopup = () => {
  showPaymentPopup.value = false;
  selectedPayment.value = null;
};

const closeReceiptPopup = () => {
  showReceiptPopup.value = false;
  selectedReceipt.value = null;
};

const handleViewRefundStatus = (payment) => {
  console.log("View refund status:", payment);

  // Show refunding status popup
  paymentStatus.value = "refunding";
  selectedPayment.value = {
    invoiceCode: payment.invoiceCode,
    originalInvoice: payment.invoiceCode,
    canceledService: payment.service,
    refundAmount: payment.totalAmount,
  };

  showPaymentPopup.value = true;
};

// Load payment data from backend
const loadPaymentData = async () => {
  try {
    isLoading.value = true;
    const response = await getPaymentInvoices();

    console.log('API Response:', response);

    if (response && response.data && response.data.length > 0) {
      // Map backend data to frontend format
      payments.value = response.data.map(lichHen => {
        // Ưu tiên lấy tiền từ thanh_toan nếu có
        const thanhToan = lichHen.thanh_toan;
        const tongTien = thanhToan
          ? parseFloat(thanhToan.tong_tien_sau_giam) || 0
          : parseFloat(lichHen.tong_tien) || parseFloat(lichHen.dich_vu?.gia_tien) || 0;
        const ngayGio = lichHen.ngay_gio;

        return {
          id: lichHen.id,
          invoiceCode: `HD${String(lichHen.id).padStart(6, '0')}`,
          service: lichHen.dich_vu?.ten || lichHen.dich_vu?.ten_dich_vu || 'Dịch vụ',
          date: ngayGio ? new Date(ngayGio).toLocaleDateString('vi-VN') : 'N/A',
          status: mapTrangThaiToStatus(lichHen.trang_thai, lichHen.da_thanh_toan),
          statusText: getStatusText(lichHen.trang_thai, tongTien, lichHen.da_thanh_toan),
          amountText: getAmountText(lichHen.trang_thai, tongTien, lichHen.da_thanh_toan),
          totalAmount: tongTien,
          paidAmount: lichHen.da_thanh_toan ? tongTien : 0,
        };
      });

      // Calculate statistics
      calculateStatistics();
      console.log('Loaded payments:', payments.value);
    } else {
      // Nếu không có dữ liệu từ API, xóa dữ liệu mẫu
      payments.value = [];
      calculateStatistics();
      console.log('No appointments found for this customer');
    }
  } catch (error) {
    console.error('Error loading payment data:', error);
    console.error('Error details:', error.response?.data);
    // Xóa dữ liệu nếu có lỗi
    payments.value = [];
    calculateStatistics();
  } finally {
    isLoading.value = false;
  }
};

// Map trạng thái from backend to frontend status
const mapTrangThaiToStatus = (trangThai, daThanhToan) => {
  if (trangThai === 'completed') return 'completed';
  if (trangThai === 'cancelled') return 'refunded';
  if (daThanhToan) return 'prepaid';
  return 'pending';
};

// Get status text based on trạng thái
const getStatusText = (trangThai, tongTien, daThanhToan) => {
  if (daThanhToan && trangThai !== 'completed' && trangThai !== 'cancelled') {
    return 'Đã thanh toán trước';
  }
  const statusTextMap = {
    'pending': `Cần thanh toán: ${formatCurrency(tongTien)}`,
    'cho_xac_nhan': `Cần thanh toán: ${formatCurrency(tongTien)}`,
    'confirmed': `Cần thanh toán: ${formatCurrency(tongTien)}`,
    'confirmed_by_staff': `Cần thanh toán: ${formatCurrency(tongTien)}`,
    'in-progress': `Cần thanh toán: ${formatCurrency(tongTien)}`,
    'completed': 'Đã hoàn thành',
    'cancelled': 'Đã hoàn tiền',
  };
  // Nếu không tìm thấy trạng thái, vẫn hiển thị số tiền
  return statusTextMap[trangThai] || `Cần thanh toán: ${formatCurrency(tongTien)}`;
};

// Get amount text based on trạng thái
const getAmountText = (trangThai, tongTien, daThanhToan) => {
  if (daThanhToan && trangThai !== 'completed' && trangThai !== 'cancelled') {
    return `(Đã trả: ${formatCurrency(tongTien)})`;
  }
  const amountTextMap = {
    'pending': `(Tổng: ${formatCurrency(tongTien)})`,
    'cho_xac_nhan': `(Tổng: ${formatCurrency(tongTien)})`,
    'confirmed': `(Tổng: ${formatCurrency(tongTien)})`,
    'confirmed_by_staff': `(Tổng: ${formatCurrency(tongTien)})`,
    'in-progress': `(Tổng: ${formatCurrency(tongTien)})`,
    'completed': `(Tổng: ${formatCurrency(tongTien)})`,
    'cancelled': `(+ ${formatCurrency(tongTien)})`,
  };
  // Nếu không tìm thấy trạng thái, vẫn hiển thị tổng tiền
  return amountTextMap[trangThai] || `(Tổng: ${formatCurrency(tongTien)})`;
};

// Calculate statistics from payments
const calculateStatistics = () => {
  totalPaid.value = 0;
  paidInvoiceCount.value = 0;
  totalDebt.value = 0;
  debtInvoiceCount.value = 0;
  totalSpending.value = 0;

  payments.value.forEach(payment => {
    if (payment.status === 'completed' || payment.status === 'prepaid') {
      totalPaid.value += payment.paidAmount;
      paidInvoiceCount.value++;
    }
    if (payment.status === 'pending') {
      totalDebt.value += (payment.totalAmount - payment.paidAmount);
      debtInvoiceCount.value++;
    }
    totalSpending.value += payment.totalAmount;
  });
};

// Load data on component mount
onMounted(() => {
  // Bỏ comment để kết nối backend
  loadPaymentData();
});

// Watch for route changes to reload data when coming back from payment
const route = useRoute();
watch(() => route.query.refresh, (newVal) => {
  if (newVal === 'true') {
    loadPaymentData();
  }
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700;800&family=Inter:wght@700&display=swap");
.font-nunitoSans {
  font-family: "Nunito Sans", sans-serif;
}
</style>