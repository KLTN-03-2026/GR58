<template>
  <div class="min-h-screen font-nunitoSans" @click="closeAllDropdowns">
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
          <div class="bg-white border !border-teal-400 rounded-[14px] h-[164px] px-6 py-0 flex flex-col justify-between">
            <div class="h-5 pt-[25px]">
              <p class="font-medium text-base leading-6 text-gray-700">Tổng đã thanh toán</p>
            </div>
            <div class="flex flex-col gap-1 pb-[25px]">
              <p class="font-semibold text-4xl leading-6 text-teal-500">{{ formatCurrency(totalPaid) }}</p>
              <p class="font-medium text-base leading-6 text-gray-700">{{ paidInvoiceCount }} hóa đơn — Tháng này</p>
            </div>
          </div>

          <div class="bg-white border !border-amber-300 rounded-[14px] h-[164px] px-6 py-0 flex flex-col justify-between">
            <div class="h-5 pt-[25px]">
              <p class="font-medium text-base leading-6 text-gray-700">Tổng dư nợ</p>
            </div>
            <div class="flex flex-col gap-1 pb-[25px]">
              <p class="font-semibold text-4xl leading-6 text-amber-700">{{ formatCurrency(totalDebt) }}</p>
              <p class="font-medium text-base leading-6 text-gray-700">{{ debtInvoiceCount }} hóa đơn</p>
            </div>
          </div>

          <div class="bg-white border !border-gray-400 rounded-[14px] h-[164px] px-6 py-0 flex flex-col justify-between">
            <div class="h-5 pt-[25px]">
              <p class="font-medium text-base leading-6 text-gray-700">Tổng chi tiêu</p>
            </div>
            <div class="flex flex-col gap-1 pb-[25px]">
              <p class="font-semibold text-4xl leading-6 text-black">{{ formatCurrency(totalSpending) }}</p>
              <p class="font-medium text-base leading-6 text-gray-700">Trong năm {{ currentYear }}</p>
            </div>
          </div>
        </div>

        <!-- Payment History -->
        <div class="bg-white border !border-black/15 shadow-sm rounded-[14px] px-8 py-6 flex flex-col gap-6 w-full">
          <!-- Card Header -->
          <div class="flex flex-col h-[70px] items-start w-full">
            <p class="font-bold text-lg leading-7 text-black">Lịch sử giao dịch</p>
            <p class="font-normal text-base leading-6 text-gray-500">Danh sách các hóa đơn và thanh toán</p>
          </div>

          <!-- Filter Row -->
          <div class="bg-white rounded-[10px] shadow-md px-6 py-6 flex items-center gap-9" @click.stop>
            <div class="flex items-center gap-2">
              <p class="font-medium text-base leading-6 text-black">Lọc theo:</p>
            </div>

            <div class="flex items-center gap-6">
              <!-- Filter: Dịch vụ -->
              <div class="relative">
                <button
                  @click.stop="toggleDropdown('service')"
                  class="bg-gray-200 border !border-black/15 rounded-lg px-[13px] py-[9px] flex items-center gap-4 min-w-[160px]"
                >
                  <span class="text-sm font-medium text-gray-600 flex-1 text-left truncate">
                    {{ selectedService || 'Dịch vụ' }}
                  </span>
                  <ChevronDownIcon class="text-gray-500 flex-shrink-0 transition-transform" :class="{ 'rotate-180': openDropdown === 'service' }" />
                </button>
                <div v-if="openDropdown === 'service'" class="absolute top-full left-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl z-50 min-w-[200px] max-h-56 overflow-y-auto">
                  <div
                    @click="selectService(null)"
                    class="px-4 py-2.5 text-sm cursor-pointer hover:bg-teal-50 hover:text-teal-700 font-medium"
                    :class="{ 'bg-teal-50 text-teal-700': !selectedService }"
                  >
                    Tất cả dịch vụ
                  </div>
                  <div
                    v-for="svc in uniqueServices"
                    :key="svc"
                    @click="selectService(svc)"
                    class="px-4 py-2.5 text-sm cursor-pointer hover:bg-teal-50 hover:text-teal-700 font-medium border-t border-gray-50"
                    :class="{ 'bg-teal-50 text-teal-700': selectedService === svc }"
                  >
                    {{ svc }}
                  </div>
                </div>
              </div>

              <!-- Filter: Tháng -->
              <div class="relative">
                <button
                  @click.stop="toggleDropdown('month')"
                  class="bg-gray-200 border !border-black/15 rounded-lg px-[13px] py-[9px] flex items-center gap-4 min-w-[140px]"
                >
                  <span class="text-sm font-medium text-gray-600 flex-1 text-left">
                    {{ selectedMonth ? `Tháng ${selectedMonth}` : 'Tháng' }}
                  </span>
                  <ChevronDownIcon class="text-gray-500 flex-shrink-0 transition-transform" :class="{ 'rotate-180': openDropdown === 'month' }" />
                </button>
                <div v-if="openDropdown === 'month'" class="absolute top-full left-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl z-50 w-40 max-h-56 overflow-y-auto">
                  <div
                    @click="selectMonth(null)"
                    class="px-4 py-2.5 text-sm cursor-pointer hover:bg-teal-50 hover:text-teal-700 font-medium"
                    :class="{ 'bg-teal-50 text-teal-700': !selectedMonth }"
                  >
                    Tất cả
                  </div>
                  <div
                    v-for="m in 12"
                    :key="m"
                    @click="selectMonth(m)"
                    class="px-4 py-2.5 text-sm cursor-pointer hover:bg-teal-50 hover:text-teal-700 font-medium border-t border-gray-50"
                    :class="{ 'bg-teal-50 text-teal-700': selectedMonth === m }"
                  >
                    Tháng {{ m }}
                  </div>
                </div>
              </div>

              <!-- Filter: Năm -->
              <div class="relative">
                <button
                  @click.stop="toggleDropdown('year')"
                  class="bg-gray-200 border !border-black/15 rounded-lg px-[13px] py-[9px] flex items-center gap-4 min-w-[120px]"
                >
                  <span class="text-sm font-medium text-gray-600 flex-1 text-left">
                    {{ selectedYear || 'Năm' }}
                  </span>
                  <ChevronDownIcon class="text-gray-500 flex-shrink-0 transition-transform" :class="{ 'rotate-180': openDropdown === 'year' }" />
                </button>
                <div v-if="openDropdown === 'year'" class="absolute top-full left-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl z-50 w-32">
                  <div
                    @click="selectYear(null)"
                    class="px-4 py-2.5 text-sm cursor-pointer hover:bg-teal-50 hover:text-teal-700 font-medium"
                    :class="{ 'bg-teal-50 text-teal-700': !selectedYear }"
                  >
                    Tất cả
                  </div>
                  <div
                    v-for="y in availableYears"
                    :key="y"
                    @click="selectYear(y)"
                    class="px-4 py-2.5 text-sm cursor-pointer hover:bg-teal-50 hover:text-teal-700 font-medium border-t border-gray-50"
                    :class="{ 'bg-teal-50 text-teal-700': selectedYear === y }"
                  >
                    {{ y }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Filter status + Clear -->
            <div class="flex items-center gap-4 ml-auto">
              <div v-if="hasActiveFilters" class="flex items-center gap-2">
                <span v-if="selectedService" class="inline-flex items-center gap-1.5 bg-teal-100 text-teal-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                  {{ selectedService }}
                  <button @click="selectService(null)" class="hover:text-teal-900">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                  </button>
                </span>
                <span v-if="selectedMonth" class="inline-flex items-center gap-1.5 bg-teal-100 text-teal-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                  Tháng {{ selectedMonth }}
                  <button @click="selectMonth(null)" class="hover:text-teal-900">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                  </button>
                </span>
                <span v-if="selectedYear" class="inline-flex items-center gap-1.5 bg-teal-100 text-teal-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                  {{ selectedYear }}
                  <button @click="selectYear(null)" class="hover:text-teal-900">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                  </button>
                </span>
              </div>
              <button @click="clearFilters" class="rounded-lg px-3 py-0 flex items-center justify-center">
                <p class="font-semibold text-base leading-5 text-teal-700">Xóa bộ lọc</p>
              </button>
            </div>
          </div>

          <!-- Payments Table -->
          <div class="flex flex-col gap-4">
            <!-- Empty State -->
            <div v-if="filteredPayments.length === 0 && !isLoading" class="flex flex-col items-center justify-center py-12 gap-4">
              <div class="text-gray-400 text-6xl">📋</div>
              <p class="font-medium text-lg text-gray-600">
                {{ hasActiveFilters ? 'Không có hóa đơn phù hợp với bộ lọc' : 'Chưa có lịch hẹn nào' }}
              </p>
              <p class="font-normal text-sm text-gray-500">
                {{ hasActiveFilters ? 'Hãy thử thay đổi bộ lọc khác' : 'Các hóa đơn thanh toán sẽ hiển thị tại đây sau khi bạn đặt lịch khám' }}
              </p>
              <button v-if="hasActiveFilters" @click="clearFilters" class="text-teal-700 font-semibold hover:underline text-sm">
                Xóa bộ lọc
              </button>
            </div>

            <!-- Loading State -->
            <div v-else-if="isLoading" class="flex items-center justify-center py-12">
              <svg class="animate-spin h-8 w-8 text-teal-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>

            <!-- Table with Data -->
            <div v-else class="flex flex-col overflow-hidden w-full">
              <!-- Table Header -->
              <div class="border-b border-black/15 h-10 flex items-center">
                <div class="w-[126.688px] px-2">
                  <p class="font-semibold text-base leading-6 text-black">Mã hóa đơn</p>
                </div>
                <div class="w-[301.516px] px-2">
                  <p class="font-semibold text-base leading-6 text-black">Dịch vụ</p>
                </div>
                <div class="w-[136.531px] px-2">
                  <p class="font-semibold text-base leading-6 text-black">Ngày khám</p>
                </div>
                <div class="w-[220px] px-2">
                  <p class="font-semibold text-base leading-6 text-black">Trạng thái & Số tiền</p>
                </div>
                <div class="flex-1 px-2">
                  <p class="font-semibold text-base leading-6 text-black text-right">Thao tác</p>
                </div>
              </div>

              <!-- Table Body -->
              <div class="flex flex-col">
                <div
                  v-for="payment in paginatedPayments"
                  :key="payment.id"
                  class="border-b border-black/15 h-[53px] flex items-center"
                >
                  <div class="w-[126.688px] px-2">
                    <p class="font-bold text-sm leading-5" :class="getInvoiceCodeColor(payment.status)">
                      {{ payment.invoiceCode }}
                    </p>
                  </div>
                  <div class="w-[301.516px] px-2">
                    <p class="font-medium text-sm leading-5 text-black truncate">{{ payment.service }}</p>
                  </div>
                  <div class="w-[136.531px] px-2">
                    <p class="font-medium text-sm leading-5 text-black">{{ payment.date }}</p>
                  </div>
                  <div class="w-[220px] px-2">
                    <div class="flex flex-col gap-0">
                      <p class="font-semibold text-sm leading-5" :class="getStatusColor(payment.status)">
                        {{ payment.statusText }}
                      </p>
                      <p class="font-medium text-xs leading-5 text-gray-500">{{ payment.amountText }}</p>
                    </div>
                  </div>
                  <div class="flex-1 px-2 flex justify-end">
                    <button
                      v-if="payment.status === 'pending'"
                      @click="handlePayNow(payment)"
                      class="bg-amber-600 rounded-lg px-3 py-1 flex items-center gap-2"
                    >
                      <div class="w-4 h-4"><WalletIcon /></div>
                      <span class="font-semibold text-sm leading-6 text-white">Thanh toán ngay</span>
                    </button>

                    <button
                      v-else-if="payment.status === 'prepaid' || payment.status === 'refunded'"
                      @click="handleViewDetail(payment)"
                      class="bg-white border !border-black/15 rounded-lg px-3 py-1 flex items-center gap-2"
                    >
                      <div class="w-4 h-4"><EyeIcon /></div>
                      <span class="font-semibold text-sm leading-6 text-black">Xem chi tiết</span>
                    </button>

                    <button
                      v-else-if="payment.status === 'completed'"
                      @click="handleViewReceipt(payment)"
                      class="bg-white border !border-black/15 rounded-lg px-3 py-1 flex items-center gap-2"
                    >
                      <div class="w-4 h-4"><DownloadIcon /></div>
                      <span class="font-semibold text-sm leading-6 text-black">Xem biên lai</span>
                    </button>

                    <button
                      v-else-if="payment.status === 'refunding'"
                      @click="handleViewRefundStatus(payment)"
                      class="bg-white border !border-black/15 rounded-lg px-3 py-1 flex items-center gap-2"
                    >
                      <div class="!text-black"><ClockIcon class="w-4 h-4" /></div>
                      <span class="font-semibold text-sm leading-6 text-black">Đang xử lý</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="totalFilteredPayments > 0" class="flex items-center justify-between w-full">
              <div class="flex items-center gap-1">
                <p class="font-medium text-sm leading-4 text-gray-600">Hiển thị</p>
                <p class="font-medium text-base leading-4 text-[#101828]">{{ paginationStart }}-{{ paginationEnd }}</p>
                <p class="font-medium text-sm leading-4 text-gray-600">trong</p>
                <p class="font-medium text-base leading-4 text-[#101828]">{{ totalFilteredPayments }}</p>
                <p class="font-medium text-sm leading-4 text-gray-600">giao dịch</p>
              </div>

              <div class="flex items-center gap-2">
                <button
                  @click="prevPage"
                  :disabled="currentPage === 1"
                  :class="{ 'opacity-50': currentPage === 1 }"
                  class="bg-white border border-black/20 rounded-lg px-[17px] py-px flex items-center justify-center"
                >
                  <div class="p-1"><ChevronLeftIcon class="text-gray-500" /></div>
                </button>

                <button
                  v-for="page in visiblePages"
                  :key="page"
                  @click="goToPage(page)"
                  :class="page === currentPage ? 'bg-teal-700 text-white' : 'bg-white text-black'"
                  class="border border-black/10 rounded-lg h-8 px-[13px] py-px flex items-center justify-center min-w-[32px]"
                >
                  <span class="font-semibold text-sm leading-5">{{ page }}</span>
                </button>

                <button
                  @click="nextPage"
                  :disabled="currentPage === totalPages"
                  :class="{ 'opacity-50': currentPage === totalPages }"
                  class="bg-white border border-black/20 rounded-lg px-[17px] py-px flex items-center justify-center"
                >
                  <div class="p-1"><ChevronRightIcon class="text-gray-500" /></div>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-teal-50/50 border !border-teal-500 rounded-[14px] px-8 py-6 flex flex-col gap-6 w-full">
          <div class="flex flex-col h-[70px] items-start w-full">
            <p class="font-bold text-xl leading-6 text-teal-800">Phương thức thanh toán</p>
            <p class="font-normal text-lg leading-6 text-teal-600">
              Các phương thức thanh toán được hỗ trợ tại Petty
            </p>
          </div>

          <div class="flex flex-col gap-4">
            <!-- Chuyển khoản ngân hàng (SePay) -->
            <div class="bg-white border !border-teal-300 rounded-[10px] h-[82px] px-[17px] py-px flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="rounded-[10px] shadow-md w-12 h-12">
                  <img src="/src/assets/img_imports/public_img/sepay.png" alt="SePay" class="w-full h-full rounded-[10px] object-contain" />
                </div>
                <div class="flex flex-col">
                  <p class="font-medium text-base leading-6 text-black">Chuyển khoản ngân hàng</p>
                  <p class="font-medium text-sm leading-6 text-gray-500">Quét mã QR bằng app ngân hàng bất kỳ</p>
                </div>
              </div>
              <div class="bg-green-100 border border-green-300 rounded-lg px-[17px] py-[3px]">
                <p class="font-semibold text-sm leading-5 text-green-700">Khả dụng</p>
              </div>
            </div>

            <!-- MoMo -->
            <div class="bg-white border !border-teal-300 rounded-[10px] h-[82px] px-[17px] py-px flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="rounded-[10px] shadow-md w-12 h-12">
                  <img src="/src/assets/img_imports/public_img/momo.png" alt="MoMo" class="w-full h-full rounded-[10px] object-contain" />
                </div>
                <div class="flex flex-col">
                  <p class="font-medium text-base leading-6 text-black">Ví MoMo</p>
                  <p class="font-medium text-sm leading-6 text-gray-500">Thanh toán qua ứng dụng MoMo</p>
                </div>
              </div>
              <div class="bg-green-100 border border-green-300 rounded-lg px-[17px] py-[3px]">
                <p class="font-semibold text-sm leading-5 text-green-700">Khả dụng</p>
              </div>
            </div>

            <!-- Tiền mặt -->
            <div class="bg-white border !border-teal-300 rounded-[10px] h-[82px] px-[17px] py-px flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="rounded-[10px] shadow-md w-12 h-12 bg-teal-50 flex items-center justify-center">
                  <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div class="flex flex-col">
                  <p class="font-medium text-base leading-6 text-black">Tiền mặt tại phòng khám</p>
                  <p class="font-medium text-sm leading-6 text-gray-500">Thanh toán trực tiếp khi đến khám</p>
                </div>
              </div>
              <div class="bg-green-100 border border-green-300 rounded-lg px-[17px] py-[3px]">
                <p class="font-semibold text-sm leading-5 text-green-700">Khả dụng</p>
              </div>
            </div>
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
import { paymentService } from "@/services/paymentService";

import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import ChevronRightIcon from "@/assets/svg/chevron-right.svg";
import ChevronLeftIcon from "@/assets/svg/chevron-left.svg";
import EyeIcon from "@/assets/svg/eye.svg";
import DownloadIcon from "@/assets/svg/download.svg";
import WalletIcon from "@/assets/svg/wallet.svg";
import ClockIcon from "@/assets/svg/clock.svg";

// Statistics
const totalPaid = ref(0);
const paidInvoiceCount = ref(0);
const totalDebt = ref(0);
const debtInvoiceCount = ref(0);
const totalSpending = ref(0);
const currentYear = ref(new Date().getFullYear());
const isLoading = ref(false);

// Filter state
const selectedService = ref('');
const selectedMonth = ref(null);
const selectedYear = ref(null);
const openDropdown = ref(null);

// Payment popup state
const showPaymentPopup = ref(false);
const selectedPayment = ref(null);
const paymentStatus = ref("pending");
const showReceiptPopup = ref(false);
const selectedReceipt = ref(null);


// Payments data (raw from API)
const payments = ref([]);

// Pagination
const currentPage = ref(1);
const itemsPerPage = 6;

// --- Computed filter options ---
const uniqueServices = computed(() => {
  const seen = new Set();
  payments.value.forEach(p => { if (p.service) seen.add(p.service); });
  return Array.from(seen).sort();
});

const availableYears = computed(() => {
  const seen = new Set();
  payments.value.forEach(p => {
    if (p.rawDate) {
      const y = new Date(p.rawDate).getFullYear();
      if (!isNaN(y)) seen.add(y);
    }
  });
  const years = Array.from(seen).sort((a, b) => b - a);
  if (!years.includes(currentYear.value)) years.unshift(currentYear.value);
  return years;
});

const hasActiveFilters = computed(() => !!selectedService.value || !!selectedMonth.value || !!selectedYear.value);

// --- Filtered payments ---
const filteredPayments = computed(() => {
  return payments.value.filter(p => {
    const matchService = !selectedService.value || p.service === selectedService.value;

    let matchMonth = true;
    let matchYear = true;
    if ((selectedMonth.value || selectedYear.value) && p.rawDate) {
      const d = new Date(p.rawDate);
      if (!isNaN(d.getTime())) {
        if (selectedMonth.value) matchMonth = d.getMonth() + 1 === selectedMonth.value;
        if (selectedYear.value) matchYear = d.getFullYear() === selectedYear.value;
      }
    }

    return matchService && matchMonth && matchYear;
  });
});

const totalFilteredPayments = computed(() => filteredPayments.value.length);
const totalPages = computed(() => Math.ceil(totalFilteredPayments.value / itemsPerPage));

const paginatedPayments = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredPayments.value.slice(start, start + itemsPerPage);
});

const paginationStart = computed(() => (currentPage.value - 1) * itemsPerPage + 1);
const paginationEnd = computed(() => Math.min(currentPage.value * itemsPerPage, totalFilteredPayments.value));

const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 5;
  let start = Math.max(1, currentPage.value - 2);
  let end = Math.min(totalPages.value, start + maxVisible - 1);
  if (end - start < maxVisible - 1) start = Math.max(1, end - maxVisible + 1);
  for (let i = start; i <= end; i++) pages.push(i);
  return pages;
});

// --- Dropdown methods ---
const toggleDropdown = (name) => {
  openDropdown.value = openDropdown.value === name ? null : name;
};

const closeAllDropdowns = () => {
  openDropdown.value = null;
};

const selectService = (val) => {
  selectedService.value = val || '';
  openDropdown.value = null;
  currentPage.value = 1;
};

const selectMonth = (val) => {
  selectedMonth.value = val;
  openDropdown.value = null;
  currentPage.value = 1;
};

const selectYear = (val) => {
  selectedYear.value = val;
  openDropdown.value = null;
  currentPage.value = 1;
};

const clearFilters = () => {
  selectedService.value = '';
  selectedMonth.value = null;
  selectedYear.value = null;
  currentPage.value = 1;
};

// --- Formatting helpers ---
const formatCurrency = (amount) => {
  return new Intl.NumberFormat("vi-VN", { style: "decimal", minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(amount || 0) + " ₫";
};

const getInvoiceCodeColor = (status) => {
  const colors = { pending: "text-amber-600", prepaid: "text-blue-500", completed: "text-green-600", refunding: "text-yellow-500", refunded: "text-purple-600" };
  return colors[status] || "text-black";
};

const getStatusColor = (status) => {
  const colors = { pending: "text-amber-600", prepaid: "text-blue-500", completed: "text-green-600", refunding: "text-yellow-500", refunded: "text-purple-600" };
  return colors[status] || "text-black";
};

// --- Map backend trạng thái to frontend status ---
const mapTrangThaiToStatus = (trangThai, daThanhToan, thanhToan) => {
  if (trangThai === 'cancelled') return 'refunded';
  if (daThanhToan || (thanhToan && thanhToan.trang_thai === 'da_thanh_toan')) return 'completed';
  return 'pending';
};

const getStatusText = (trangThai, tongTien, daThanhToan, thanhToan) => {
  if (trangThai === 'cancelled') return 'Đã hủy';
  if (daThanhToan || (thanhToan && thanhToan.trang_thai === 'da_thanh_toan') || trangThai === 'completed') return 'Đã thanh toán';
  return `Cần thanh toán: ${formatCurrency(tongTien)}`;
};

const getAmountText = (trangThai, tongTien, daThanhToan, thanhToan) => {
  return `(Tổng: ${formatCurrency(tongTien)})`;
};

// Map payment method from backend to display label
const mapPaymentMethod = (phuongThuc) => {
  const methodMap = {
    'momo': 'Ví MoMo',
    'vnpay': 'VNPay',
    'tien_mat': 'Tiền mặt',
    'cash': 'Tiền mặt',
    'bank_transfer': 'Chuyển khoản',
    'chuyen_khoan': 'Chuyển khoản QR',
    'online': 'Chuyển khoản QR',
  };
  return methodMap[phuongThuc] || phuongThuc || 'Không rõ';
};

// --- Load data ---
const loadPaymentData = async () => {
  isLoading.value = true;
  try {
    const response = await paymentService.getInvoices();

    if (response && response.data && response.data.length > 0) {
      const rows = [];

      response.data.forEach(lichHen => {
        const thanhToan = lichHen.thanh_toan;
        const tongTien = thanhToan
          ? parseFloat(thanhToan.tong_tien_sau_giam) || 0
          : parseFloat(lichHen.tong_tien) || parseFloat(lichHen.dich_vu?.gia_tien) || 0;
        const ngayGio = lichHen.ngay_gio;
        const phuongThucThanhToan = thanhToan?.hinh_thuc_thanh_toan || lichHen.phuong_thuc_thanh_toan || null;

        const isPaid = lichHen.da_thanh_toan || (thanhToan && thanhToan.trang_thai === 'da_thanh_toan');
        const paidAmount = thanhToan && (lichHen.trang_thai === 'completed' || isPaid)
          ? parseFloat(thanhToan.tong_tien_sau_giam) || 0
          : 0;

        rows.push({
          id: lichHen.id,
          invoiceCode: `HD${String(lichHen.id).padStart(6, '0')}`,
          service: lichHen.dich_vus?.length ? lichHen.dich_vus.map(d => d.ten).join(", ") : (lichHen.dich_vu?.ten || lichHen.dich_vu?.ten_dich_vu || 'Dịch vụ'),
          date: ngayGio ? new Date(ngayGio).toLocaleDateString('vi-VN') : 'N/A',
          rawDate: ngayGio || null,
          status: mapTrangThaiToStatus(lichHen.trang_thai, lichHen.da_thanh_toan, thanhToan),
          statusText: getStatusText(lichHen.trang_thai, tongTien, lichHen.da_thanh_toan, thanhToan),
          amountText: getAmountText(lichHen.trang_thai, tongTien, lichHen.da_thanh_toan, thanhToan),
          totalAmount: tongTien,
          paidAmount: paidAmount,
          phuongThuc: mapPaymentMethod(phuongThucThanhToan),
          petName: lichHen.thu_cung?.ten_thu_cung || lichHen.thu_cung?.ten || null,
          doctor: lichHen.nhan_vien?.full_name || lichHen.nhan_vien?.ho_ten || null,
          dich_vus: lichHen.dich_vus || [],
        });

        // Thêm dòng riêng cho thanh toán bổ sung (thuốc)
        if (lichHen.thanh_toans && lichHen.thanh_toans.length > 0) {
          lichHen.thanh_toans
            .filter(tt => tt.ma_thanh_toan && tt.ma_thanh_toan.startsWith('BS'))
            .forEach(ttBS => {
              rows.push({
                id: `${lichHen.id}-bs-${ttBS.id}`,
                invoiceCode: `HD${String(lichHen.id).padStart(6, '0')}-T`,
                service: `Thuốc bổ sung — ${lichHen.dich_vus?.length ? lichHen.dich_vus.map(d => d.ten).join(", ") : (lichHen.dich_vu?.ten || 'Dịch vụ')}`,
                date: ttBS.ngay_thanh_toan ? new Date(ttBS.ngay_thanh_toan).toLocaleDateString('vi-VN') : (ngayGio ? new Date(ngayGio).toLocaleDateString('vi-VN') : 'N/A'),
                rawDate: ttBS.ngay_thanh_toan || ngayGio || null,
                status: ttBS.trang_thai === 'da_thanh_toan' ? 'completed' : 'pending',
                statusText: ttBS.trang_thai === 'da_thanh_toan' ? `Đã thanh toán` : `Cần thanh toán: ${Number(ttBS.tong_tien_sau_giam).toLocaleString('vi-VN')} đ`,
                amountText: `(Tổng: ${Number(ttBS.tong_tien_sau_giam).toLocaleString('vi-VN')} đ)`,
                totalAmount: parseFloat(ttBS.tong_tien_sau_giam) || 0,
                paidAmount: ttBS.trang_thai === 'da_thanh_toan' ? parseFloat(ttBS.tong_tien_sau_giam) || 0 : 0,
                phuongThuc: mapPaymentMethod(ttBS.hinh_thuc_thanh_toan),
                petName: lichHen.thu_cung?.ten_thu_cung || lichHen.thu_cung?.ten || null,
                doctor: lichHen.nhan_vien?.full_name || lichHen.nhan_vien?.ho_ten || null,
                dich_vus: lichHen.dich_vus || [],
              });
            });
        }
      });

      payments.value = rows.sort((a, b) => String(a.id).localeCompare(String(b.id), undefined, { numeric: true }));
      calculateStatistics();
    } else {
      payments.value = [];
      calculateStatistics();
    }
  } catch (error) {
    console.error('Error loading payment data:', error);
    payments.value = [];
    calculateStatistics();
  } finally {
    isLoading.value = false;
  }
};

const calculateStatistics = () => {
  totalPaid.value = 0;
  paidInvoiceCount.value = 0;
  totalDebt.value = 0;
  debtInvoiceCount.value = 0;
  totalSpending.value = 0;

  const now = new Date();
  const thisMonth = now.getMonth();
  const thisYear = now.getFullYear();

  payments.value.forEach(payment => {
    const isPaid = payment.status === 'completed';

    // Tổng đã thanh toán: chỉ tháng hiện tại
    if (isPaid && payment.rawDate) {
      const d = new Date(payment.rawDate);
      if (d.getMonth() === thisMonth && d.getFullYear() === thisYear) {
        totalPaid.value += payment.paidAmount;
        paidInvoiceCount.value++;
      }
    }

    // Tổng dư nợ: tất cả hóa đơn chưa trả (không lọc thời gian)
    if (payment.status === 'pending') {
      totalDebt.value += (payment.totalAmount - payment.paidAmount);
      debtInvoiceCount.value++;
    }

    // Tổng chi tiêu: chỉ hóa đơn đã thanh toán xong trong năm
    if (isPaid && payment.rawDate) {
      const y = new Date(payment.rawDate).getFullYear();
      if (y === thisYear) totalSpending.value += payment.paidAmount;
    }
  });
};

// Reset page when filters change
watch([selectedService, selectedMonth, selectedYear], () => {
  currentPage.value = 1;
});

// --- Actions ---
const prevPage = () => { if (currentPage.value > 1) currentPage.value--; };
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++; };
const goToPage = (page) => { currentPage.value = page; };

const handlePayNow = (payment) => {
  selectedPayment.value = {
    invoiceCode: payment.invoiceCode,
    services: payment.dich_vus?.length
      ? payment.dich_vus.map(d => ({ id: d.id, name: d.ten, note: '(Dịch vụ đã đặt)', price: parseFloat(d.don_gia || d.gia_tien || 0) }))
      : [{ id: 1, name: payment.service, note: '(Dịch vụ đã đặt)', price: payment.totalAmount }],
    totalAmount: payment.totalAmount,
    paidAmount: payment.paidAmount || 0,
    remainingAmount: payment.totalAmount - (payment.paidAmount || 0),
  };
  paymentStatus.value = "pending";
  showPaymentPopup.value = true;
};

const handleViewDetail = (payment) => {
  if (payment.status === 'prepaid') {
    paymentStatus.value = "prepaid";
    selectedPayment.value = {
      appointmentCode: payment.invoiceCode,
      time: `09:00 - ${payment.date}`,
      petName: payment.petName || 'N/A',
      service: payment.service,
      staff: payment.doctor || 'N/A',
      paidAmount: payment.paidAmount,
      phuongThuc: payment.phuongThuc,
    };
  } else if (payment.status === 'refunded') {
    paymentStatus.value = "refunded";
    selectedPayment.value = {
      refundCode: payment.invoiceCode.replace("HD", "HT"),
      originalInvoice: payment.invoiceCode,
      refundDate: payment.date,
      paymentMethodName: payment.phuongThuc,
      items: [{ id: 1, name: payment.service, amount: payment.totalAmount }],
      totalRefund: payment.totalAmount,
    };
  }
  showPaymentPopup.value = true;
};

const handleViewReceipt = (payment) => {
  selectedReceipt.value = {
    invoiceCode: payment.invoiceCode,
    visitDate: payment.date,
    petName: payment.petName || 'N/A',
    doctor: payment.doctor || 'N/A',
    services: [{ id: 1, name: payment.service, price: payment.totalAmount }],
    totalAmount: payment.totalAmount,
    paidAmount: payment.paidAmount,
    prepaidAmount: payment.paidAmount,
    phuongThuc: payment.phuongThuc,
  };
  showReceiptPopup.value = true;
};

const handleViewRefundStatus = (payment) => {
  paymentStatus.value = "refunding";
  selectedPayment.value = {
    invoiceCode: payment.invoiceCode,
    originalInvoice: payment.invoiceCode,
    canceledService: payment.service,
    refundAmount: payment.totalAmount,
    phuongThuc: payment.phuongThuc,
  };
  showPaymentPopup.value = true;
};

const handlePaymentSuccess = (paymentData) => {
  showPaymentPopup.value = false;
  selectedPayment.value = null;
  loadPaymentData();
};

const closePaymentPopup = () => {
  showPaymentPopup.value = false;
  selectedPayment.value = null;
};

const closeReceiptPopup = () => {
  showReceiptPopup.value = false;
  selectedReceipt.value = null;
};

onMounted(() => {
  loadPaymentData();
});

const route = useRoute();
watch(() => route.query.refresh, (newVal) => {
  if (newVal === 'true') loadPaymentData();
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700;800&family=Inter:wght@700&display=swap");
.font-nunitoSans {
  font-family: "Nunito Sans", sans-serif;
}
</style>
