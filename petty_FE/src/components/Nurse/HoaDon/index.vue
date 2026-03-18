<template>
  <div class="relative w-full h-full px-8 py-6">
    <!-- Main Content -->
    <div v-if="!selectedInvoice" class="flex flex-col gap-6">
      <!-- Header -->
      <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-semibold text-black">
          Tài chính & Hóa đơn (POS)
        </h1>
        <p class="text-base font-medium text-gray-500">
          Thu ngân - Thanh toán dịch vụ
        </p>
      </div>

      <!-- Search and Create Card -->
      <div
        class="bg-white border !border-gray-300 rounded-[14px] p-4 shadow-sm"
      >
        <div class="flex items-center justify-between gap-4">
          <!-- Search Input -->
          <div class="relative flex-1">
            <!-- <img
              :src="iconSearch"
              alt="Search"
              class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5"
            /> -->
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Nhập mã hóa đơn hoặc SĐT khách..."
              class="w-full h-11 bg-gray-50 border !border-gray-300 rounded-lg pl-4 pr-4 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
            />
          </div>

          <!-- Create Retail Invoice Button -->
          <button
            class="bg-[#00a63e] rounded-lg px-4 py-2.5 h-10 flex items-center gap-2 hover:bg-[#008833] transition-colors shrink-0"
            @click="createRetailInvoice"
          >
            <!-- <img :src="iconPlus" alt="Plus" class="w-4 h-4" /> -->
            <span class="text-sm font-medium text-white">
              Tạo hóa đơn bán lẻ
            </span>
          </button>
        </div>
      </div>

      <!-- Pending Invoices Card -->
      <div
        class="bg-white border !border-gray-300 rounded-[14px] p-6 shadow-sm"
      >
        <h2 class="text-lg font-semibold text-black mb-6">
          Hóa đơn chờ thanh toán
        </h2>

        <!-- Invoice List -->
        <div class="flex flex-col gap-3">
          <button
            v-for="invoice in filteredInvoices"
            :key="invoice.id"
            class="border-2 !border-gray-300 rounded-[14px] p-4 hover:border-[#009689] transition-colors text-left shadow-sm"
            @click="viewInvoice(invoice)"
          >
            <!-- Invoice Header -->
            <div class="flex items-center justify-between mb-2">
              <!-- Invoice ID and Customer -->
              <div class="flex flex-col gap-1">
                <p class="text-base font-bold text-black">
                  {{ invoice.invoiceId }}
                </p>
                <p class="text-sm text-gray-600">
                  {{ invoice.customerName }} - {{ invoice.petName }}
                </p>
              </div>

              <!-- Amount and Status -->
              <div class="flex flex-col items-end gap-1">
                <p class="text-lg font-bold text-[#155dfc]">
                  {{ formatCurrency(invoice.amount) }}
                </p>
                <span
                  class="bg-[#fef9c2] border !border-transparent rounded-lg px-2 py-0.5 text-xs font-medium text-[#a65f00]"
                >
                  Chờ thanh toán
                </span>
              </div>
            </div>

            <!-- Invoice Date -->
            <p class="text-xs text-gray-600">Ngày: {{ invoice.date }}</p>
          </button>
        </div>
      </div>
    </div>

    <!-- Detail View -->
    <div v-else class="flex flex-col gap-4">
      <!-- Back Button -->
      <button
        class="flex items-center gap-2 text-gray-600 hover:text-black transition-colors w-fit"
        @click="selectedInvoice = null"
      >
        <!-- <img :src="iconArrowLeft" alt="Back" class="w-5 h-5 rotate-180" /> -->
        <svg
          class="w-5 h-5"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 19l-7-7 7-7"
          />
        </svg>
        <span class="text-sm font-medium">Quay lại danh sách</span>
      </button>

      <ChiTietHoaDon
        :invoice-id="selectedInvoice.invoiceId"
        @changeInvoice="selectedInvoice = null"
        @complete="handlePaymentComplete"
      />
    </div>

    <!-- Create Retail Invoice Modal -->
    <TaoHoaDon
      :is-open="isTaoHoaDonOpen"
      @close="isTaoHoaDonOpen = false"
      @complete="handleRetailInvoiceComplete"
    />
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import ChiTietHoaDon from "./ChiTietHoaDon/index.vue";
import TaoHoaDon from "./TaoHoaDon/index.vue";

// Icons
const iconClock =
  "https://www.figma.com/api/mcp/asset/d5f9a429-69cb-4ed2-9ffa-7f8149547512";
const iconStatus =
  "https://www.figma.com/api/mcp/asset/f3445a93-7029-4dcb-8df2-a06d657787c8";
const iconCash =
  "https://www.figma.com/api/mcp/asset/1f8bf05e-8c7b-4a04-8794-6ed76fb885c3";
const iconRevenue =
  "https://www.figma.com/api/mcp/asset/ed1db92d-3827-41c8-af98-782817619120";
const iconLock =
  "https://www.figma.com/api/mcp/asset/cf0bcea0-b770-4e92-b6b1-a1a55389fb0c";
const iconSearch =
  "https://www.figma.com/api/mcp/asset/b901c038-cd1f-4ed5-8d13-6024afc9e670";
const iconPlus =
  "https://www.figma.com/api/mcp/asset/d918040c-7daa-4ec7-bb71-eeccf868527e";
const iconArrowLeft =
  "https://www.figma.com/api/mcp/asset/2bb7bd10-d9fa-46f4-8c76-259dd73e0fd2"; // Reusing chevron/arrow icon if available or generic

// State
const searchQuery = ref("");
const selectedInvoice = ref(null);
const isTaoHoaDonOpen = ref(false);

// Shift Information
const shiftInfo = ref({
  name: "Sáng (08:00 - 14:00)",
  status: "open",
  startingCash: 2000000,
  cashRevenue: 1500000,
});

// Pending Invoices
const pendingInvoices = ref([
  {
    id: 1,
    invoiceId: "HD001234",
    customerName: "Nguyễn Văn A",
    petName: "Milo",
    amount: 400000,
    status: "pending",
    date: "21/11/2024",
  },
  {
    id: 2,
    invoiceId: "HD001235",
    customerName: "Trần Thị B",
    petName: "Luna",
    amount: 242000,
    status: "pending",
    date: "21/11/2024",
  },
]);

// Computed
const filteredInvoices = computed(() => {
  if (!searchQuery.value) {
    return pendingInvoices.value;
  }

  const query = searchQuery.value.toLowerCase();
  return pendingInvoices.value.filter(
    (invoice) =>
      invoice.invoiceId.toLowerCase().includes(query) ||
      invoice.customerName.toLowerCase().includes(query) ||
      invoice.petName.toLowerCase().includes(query)
  );
});

// Methods
const formatCurrency = (amount) => {
  return (
    new Intl.NumberFormat("vi-VN", {
      style: "decimal",
    }).format(amount) + " ₫"
  );
};

const endShift = () => {
  console.log("End shift and handover");
  // TODO: Implement end shift modal with cash reconciliation
};

const createRetailInvoice = () => {
  isTaoHoaDonOpen.value = true;
};

const viewInvoice = (invoice) => {
  console.log("View invoice:", invoice.invoiceId);
  selectedInvoice.value = invoice;
};

const handlePaymentComplete = (data) => {
  console.log("Payment completed:", data);
  selectedInvoice.value = null;
  // Here you would typically refresh the list or remove the paid invoice
};

const handleRetailInvoiceComplete = (data) => {
  console.log("Retail invoice created:", data);
  isTaoHoaDonOpen.value = false;
  // Here you would typically refresh the list or add the new invoice
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}
</style>
