<template>
  <div class="w-full min-h-screen px-8 py-6 flex flex-col gap-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-2">
      <h1 class="text-2xl font-semibold text-black">Phiếu Chi</h1>
      <p class="text-gray-500 font-medium text-base">
        Quản lý chi phí và công nợ nhà cung cấp
      </p>
    </div>

    <!-- Summary Cards -->
    <div class="relative">
      <!-- Auto-refresh indicator -->
      <div
        v-if="isRefreshing"
        class="absolute top-0 right-0 z-10 bg-white/90 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg border !border-[#009689] flex items-center gap-2"
      >
        <svg
          class="animate-spin h-4 w-4 text-[#009689]"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        <span class="text-xs text-[#009689] font-medium">Đang cập nhật...</span>
      </div>

      <!-- Manual refresh button -->
      <button
        @click="handleManualRefresh"
        :disabled="isRefreshing"
        class="absolute top-0 right-0 z-10 bg-white hover:bg-gray-50 rounded-lg p-2 shadow-md border !border-[#009689] transition-all hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
        :class="{ 'opacity-0': isRefreshing }"
        title="Làm mới thống kê"
      >
        <svg
          class="w-5 h-5 text-[#009689]"
          :class="{ 'animate-spin': isRefreshing }"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
          ></path>
        </svg>
      </button>

      <div class="grid grid-cols-3 gap-6">
        <!-- Tổng chi tháng này -->
        <div
          class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 transition-all duration-300"
          :class="{ 'animate-pulse-soft': isRefreshing }"
        >
          <div class="flex items-center justify-between">
            <div class="flex flex-col gap-1">
              <div class="flex items-center gap-2">
                <p class="text-sm text-[#4a5565] leading-5">
                  Tổng chi tháng này
                </p>
                <span
                  v-if="statisticsTrend.totalThisMonth !== 0"
                  class="text-xs px-2 py-0.5 rounded-full"
                  :class="
                    statisticsTrend.totalThisMonth > 0
                      ? 'bg-red-100 text-red-600'
                      : 'bg-green-100 text-green-600'
                  "
                >
                  {{ statisticsTrend.totalThisMonth > 0 ? "↑" : "↓" }}
                  {{ Math.abs(statisticsTrend.totalThisMonth) }}%
                </span>
              </div>
              <p
                class="text-[30px] text-[#e7000b] leading-9 transition-all duration-300"
              >
                {{ formatCurrency(statistics.totalThisMonth) }}
              </p>
              <p class="text-xs text-[#6a7282] leading-4">
                Tiền đi ra khỏi quỹ
              </p>
            </div>
            <div
              class="w-12 h-12 bg-[#ffe2e2] rounded-full flex items-center justify-center"
            >
              <ArrowDownIcon class="w-6 h-6 text-[#e7000b]" />
            </div>
          </div>
        </div>

        <!-- Nợ Nhà cung cấp -->
        <div
          class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 transition-all duration-300"
          :class="{ 'animate-pulse-soft': isRefreshing }"
        >
          <div class="flex items-center justify-between">
            <div class="flex flex-col gap-1">
              <div class="flex items-center gap-2">
                <p class="text-sm text-[#4a5565] leading-5">Nợ Nhà cung cấp</p>
                <span
                  v-if="statisticsTrend.totalDebt !== 0"
                  class="text-xs px-2 py-0.5 rounded-full"
                  :class="
                    statisticsTrend.totalDebt > 0
                      ? 'bg-red-100 text-red-600'
                      : 'bg-green-100 text-green-600'
                  "
                >
                  {{ statisticsTrend.totalDebt > 0 ? "↑" : "↓" }}
                  {{ Math.abs(statisticsTrend.totalDebt) }}%
                </span>
              </div>
              <p
                class="text-[30px] text-[#f54900] leading-9 transition-all duration-300"
              >
                {{ formatCurrency(statistics.totalDebt) }}
              </p>
              <p class="text-xs text-[#6a7282] leading-4">Hàng nhập chưa trả</p>
            </div>
            <div
              class="w-12 h-12 bg-[#ffedd4] rounded-full flex items-center justify-center"
            >
              <WarningIcon class="w-6 h-6 text-[#f54900]" />
            </div>
          </div>
        </div>

        <!-- Chi phí Vận hành -->
        <div
          class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 transition-all duration-300"
          :class="{ 'animate-pulse-soft': isRefreshing }"
        >
          <div class="flex items-center justify-between">
            <div class="flex flex-col gap-1">
              <div class="flex items-center gap-2">
                <p class="text-sm text-[#4a5565] leading-5">Chi phí Vận hành</p>
                <span
                  v-if="statisticsTrend.totalOperating !== 0"
                  class="text-xs px-2 py-0.5 rounded-full"
                  :class="
                    statisticsTrend.totalOperating > 0
                      ? 'bg-red-100 text-red-600'
                      : 'bg-green-100 text-green-600'
                  "
                >
                  {{ statisticsTrend.totalOperating > 0 ? "↑" : "↓" }}
                  {{ Math.abs(statisticsTrend.totalOperating) }}%
                </span>
              </div>
              <p
                class="text-[30px] text-[#ff6900] leading-9 transition-all duration-300"
              >
                {{ formatCurrency(statistics.totalOperating) }}
              </p>
              <p class="text-xs text-[#6a7282] leading-4">
                Điện, nước, thuê nhà
              </p>
            </div>
            <div
              class="w-12 h-12 bg-orange-50 rounded-full flex items-center justify-center"
            >
              <IdeaIcon class="w-6 h-6 text-[#ff6900]" />
            </div>
          </div>
        </div>
      </div>

      <!-- Last updated timestamp -->
      <div class="mt-2 text-right">
        <p class="text-xs text-[#6a7282]">
          Cập nhật lần cuối: {{ lastUpdated }}
        </p>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div class="flex flex-col gap-4">
        <!-- Search Bar -->
        <div class="relative">
          <SearchIcon class="absolute left-3 top-2 w-5 h-5" />
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Tìm theo Mã phiếu, Tên Nhà cung cấp, Nội dung chi..."
            class="w-full h-9 pl-10 pr-3 py-1 bg-[#f3f3f5] border-0 rounded-lg text-sm text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689]"
          />
        </div>

        <!-- Filters and Create Button -->
        <div class="grid grid-cols-4 gap-4">
          <!-- Time Filter -->
          <button
            class="bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 flex items-center justify-between h-9 hover:bg-gray-200 transition-colors"
          >
            <span class="text-sm text-neutral-950">Tháng này</span>
            <ChevronDownIcon />
          </button>

          <!-- Category Filter -->
          <button
            class="bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 flex items-center justify-between h-9 hover:bg-gray-200 transition-colors"
          >
            <span class="text-sm text-neutral-950">Tất cả</span>
            <ChevronDownIcon />
          </button>

          <!-- Status Filter -->
          <button
            class="bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 flex items-center justify-between h-9 hover:bg-gray-200 transition-colors"
          >
            <span class="text-sm text-neutral-950">Tất cả</span>
            <ChevronDownIcon />
          </button>

          <!-- Create Button -->
          <button
            @click="isCreateExpenseModalOpen = true"
            class="bg-[#e7000b] text-white rounded-lg px-3 py-1 flex items-center justify-center gap-2 h-9 hover:bg-[#c4000a] transition-colors"
          >
            <AddIcon class="w-4 h-4" />
            <span class="text-sm font-medium">Tạo phiếu chi</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Expense Table -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="flex flex-col items-center gap-3">
          <svg
            class="animate-spin h-8 w-8 text-[#009689]"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
          <p class="text-sm text-[#717182]">Đang tải dữ liệu...</p>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-else-if="expenses.length === 0"
        class="flex items-center justify-center py-12"
      >
        <div class="flex flex-col items-center gap-3">
          <div
            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center"
          >
            <svg
              class="w-8 h-8 text-gray-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
              ></path>
            </svg>
          </div>
          <div class="text-center">
            <p class="text-base font-medium text-[#101828]">
              Chưa có phiếu chi nào
            </p>
            <p class="text-sm text-[#717182] mt-1">
              Tạo phiếu chi đầu tiên để bắt đầu
            </p>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-black/10">
              <th
                class="text-left text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Mã Phiếu
              </th>
              <th
                class="text-left text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Ngày tạo
              </th>
              <th
                class="text-left text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Nội dung chi
              </th>
              <th
                class="text-left text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Phân loại
              </th>
              <th
                class="text-right text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Tổng giá trị
              </th>
              <th
                class="text-right text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Đã trả
              </th>
              <th
                class="text-right text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Còn nợ
              </th>
              <th
                class="text-left text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Trạng thái
              </th>
              <th
                class="text-right text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Thao tác
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="expense in expenses"
              :key="expense.id"
              class="border-b border-black/10 hover:bg-gray-50 transition-colors"
            >
              <!-- Mã Phiếu -->
              <td class="py-4 px-2">
                <p class="text-base font-medium text-[#009689] text-center">
                  {{ expense.code }}
                </p>
              </td>

              <!-- Ngày tạo -->
              <td class="py-4 px-2">
                <div class="flex flex-col gap-1">
                  <p class="text-sm text-[#101828]">{{ expense.date }}</p>
                  <p class="text-xs text-[#6a7282]">{{ expense.time }}</p>
                </div>
              </td>

              <!-- Nội dung chi -->
              <td class="py-4 px-2">
                <div class="flex flex-col gap-1">
                  <p class="text-sm text-[#101828]">
                    {{ expense.description }}
                  </p>
                  <p class="text-xs text-[#6a7282] italic">
                    {{ expense.supplier }}
                  </p>
                </div>
              </td>

              <!-- Phân loại -->
              <td class="py-4 px-2">
                <span
                  class="inline-block px-3 py-1 rounded-lg border text-xs font-medium"
                  :class="getCategoryClass(expense.category)"
                >
                  {{ expense.categoryLabel }}
                </span>
              </td>

              <!-- Tổng giá trị -->
              <td class="py-4 px-2 text-right">
                <p class="text-sm text-[#101828]">
                  {{ formatCurrency(expense.totalAmount) }}
                </p>
              </td>

              <!-- Đã trả -->
              <td class="py-4 px-2 text-right">
                <p class="text-sm text-[#00a63e]">
                  {{ formatCurrency(expense.paidAmount) }}
                </p>
              </td>

              <!-- Còn nợ -->
              <td class="py-4 px-2 text-right">
                <p
                  class="text-sm"
                  :class="
                    expense.remainingAmount > 0
                      ? 'text-[#e7000b]'
                      : 'text-[#99a1af]'
                  "
                >
                  {{
                    expense.remainingAmount > 0
                      ? formatCurrency(expense.remainingAmount)
                      : "-"
                  }}
                </p>
              </td>

              <!-- Trạng thái -->
              <td class="py-4 px-2">
                <span
                  class="inline-block px-3 py-1 rounded-lg text-xs font-medium"
                  :style="getStatusStyle(expense.status)"
                >
                  {{ expense.statusLabel }}
                </span>
              </td>

              <!-- Thao tác -->
              <td class="py-4 px-2">
                <div class="flex items-center justify-end gap-2">
                  <button
                    v-if="expense.remainingAmount > 0"
                    @click="handleAddPayment(expense)"
                    class="w-9 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
                    title="Thanh toán"
                  >
                    <UpdateIcon class="w-4 h-4 text-[#e7000b]" />
                  </button>
                  <button
                    @click="handleViewExpense(expense)"
                    class="w-9 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
                    title="Xem chi tiết"
                  >
                    <EyeIcon class="w-4 h-4 text-[#009689]" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modals -->
    <TaoPhieuChi
      v-if="isCreateExpenseModalOpen"
      @close="isCreateExpenseModalOpen = false"
      @submit="handleCreateExpense"
    />

    <ChiTietPhieuChi
      v-if="isViewExpenseModalOpen"
      :expense="selectedExpense"
      @close="isViewExpenseModalOpen = false"
    />

    <ThanhToanThem
      v-if="isAddPaymentModalOpen"
      :expense-id="selectedExpenseForPayment?.id"
      :expense-code="selectedExpenseForPayment?.code"
      :supplier-name="selectedExpenseForPayment?.supplier"
      :total-amount="selectedExpenseForPayment?.totalAmount"
      :paid-amount="selectedExpenseForPayment?.paidAmount"
      :remaining-amount="selectedExpenseForPayment?.remainingAmount"
      @close="isAddPaymentModalOpen = false"
      @submit="handlePaymentSubmit"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { getPhieuChis, getPhieuChiById } from "@/utils/phieuChi";
import { showErrorToast, showSuccessToast } from "@/utils/toast";
import TaoPhieuChi from "./TaoPhieuChi/index.vue";
import ChiTietPhieuChi from "./ChiTietPhieuChi/index.vue";
import ThanhToanThem from "./ThanhToanThem/index.vue";
//Icon SVG
import IdeaIcon from "@/assets/svg/idea.svg";
import EyeIcon from "@/assets/svg/eye.svg";
import UpdateIcon from "@/assets/svg/update.svg";
import ArrowDownIcon from "@/assets/svg/arrow-down.svg";
import WarningIcon from "@/assets/svg/warning.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import AddIcon from "@/assets/svg/add.svg";
import SearchIcon from "@/assets/svg/search.svg";

// Icons
const iconExpense =
  "https://www.figma.com/api/mcp/asset/16735956-858e-46d4-bd3c-c2dd72763b52";
const iconDebt =
  "https://www.figma.com/api/mcp/asset/ebec506a-9322-4167-bbbb-46b34cc43d8a";
const iconOperating =
  "https://www.figma.com/api/mcp/asset/43c8fce2-6fb6-428d-974e-5dd1e67f313a";
const iconSearch =
  "https://www.figma.com/api/mcp/asset/18f6e5fc-aeeb-43d8-80ec-e1ac650323bc";
const iconChevronDown =
  "https://www.figma.com/api/mcp/asset/f86525a5-7485-445e-ad64-9a2d40a4b2f3";
const iconPlus =
  "https://www.figma.com/api/mcp/asset/09d2faf2-37da-4687-9b25-b6f21f2111e6";
const iconPayment =
  "https://www.figma.com/api/mcp/asset/c2d1d5da-d000-42b6-970e-025708bf6746";
const iconEye =
  "https://www.figma.com/api/mcp/asset/4277dd85-ff95-4e74-aaea-333b4cbd8189";

// Define emits
const emit = defineEmits(["create-expense", "view-detail", "payment"]);

// Search query
const searchQuery = ref("");
const isCreateExpenseModalOpen = ref(false);
const isViewExpenseModalOpen = ref(false);
const selectedExpense = ref(null);
const isAddPaymentModalOpen = ref(false);
const selectedExpenseForPayment = ref(null);

// Data từ API
const phieuChis = ref([]);
const loading = ref(false);
const pagination = ref({
  current_page: 1,
  per_page: 15,
  total: 0,
  last_page: 1,
});

// Statistics
const statistics = ref({
  totalThisMonth: 0,
  totalDebt: 0,
  totalOperating: 0,
});

// Previous statistics for trend calculation
const previousStatistics = ref({
  totalThisMonth: 0,
  totalDebt: 0,
  totalOperating: 0,
});

// Statistics trend (percentage change)
const statisticsTrend = ref({
  totalThisMonth: 0,
  totalDebt: 0,
  totalOperating: 0,
});

// Real-time update states
const isRefreshing = ref(false);
const lastUpdated = ref("--:--:--");
const refreshInterval = ref(null);
const AUTO_REFRESH_INTERVAL = 30000; // 30 seconds

// Filters
const filters = ref({
  loai_phieu_chi: null, // 'chi_nhap_hang' hoặc 'chi_van_hanh'
  trang_thai: null, // 'da_thanh_toan_du' hoặc 'chua_thanh_toan_du'
  tu_ngay: null,
  den_ngay: null,
  search: "",
});

// Load danh sách phiếu chi
const loadPhieuChis = async () => {
  try {
    loading.value = true;
    const params = {
      per_page: pagination.value.per_page,
      page: pagination.value.current_page,
      sort_by: "created_at",
      sort_order: "desc",
    };

    // Thêm filters nếu có
    if (filters.value.loai_phieu_chi) {
      params.loai_phieu_chi = filters.value.loai_phieu_chi;
    }
    if (filters.value.trang_thai) {
      params.trang_thai = filters.value.trang_thai;
    }
    if (filters.value.tu_ngay) {
      params.tu_ngay = filters.value.tu_ngay;
    }
    if (filters.value.den_ngay) {
      params.den_ngay = filters.value.den_ngay;
    }
    if (searchQuery.value) {
      params.search = searchQuery.value;
    }

    const response = await getPhieuChis(params);
    console.log("Phiếu chi response:", response);

    if (response && response.status) {
      phieuChis.value = response.data || [];
      if (response.pagination) {
        pagination.value = response.pagination;
      }

      // Tính toán thống kê
      calculateStatistics();
    }
  } catch (error) {
    console.error("Lỗi tải danh sách phiếu chi:", error);
    showErrorToast("Không thể tải danh sách phiếu chi");
  } finally {
    loading.value = false;
  }
};

// Tính toán thống kê
const calculateStatistics = () => {
  const currentMonth = new Date().getMonth();
  const currentYear = new Date().getFullYear();

  // Save previous statistics for trend calculation
  previousStatistics.value = { ...statistics.value };

  let totalThisMonth = 0;
  let totalDebt = 0;
  let totalOperating = 0;

  phieuChis.value.forEach((phieu) => {
    const phieuDate = new Date(phieu.ngay_chi);

    // Tổng chi tháng này
    if (
      phieuDate.getMonth() === currentMonth &&
      phieuDate.getFullYear() === currentYear
    ) {
      totalThisMonth += parseFloat(phieu.so_tien_thanh_toan_ngay) || 0;
    }

    // Nợ nhà cung cấp
    if (phieu.so_tien_con_no > 0) {
      totalDebt += parseFloat(phieu.so_tien_con_no) || 0;
    }

    // Chi phí vận hành
    if (phieu.loai_phieu_chi === "chi_van_hanh") {
      totalOperating += parseFloat(phieu.so_tien_thanh_toan_ngay) || 0;
    }
  });

  statistics.value = {
    totalThisMonth,
    totalDebt,
    totalOperating,
  };

  // Calculate trends (percentage change)
  calculateTrends();

  // Update last updated time
  updateLastUpdatedTime();
};

// Calculate trends
const calculateTrends = () => {
  const calculatePercentChange = (current, previous) => {
    // Ensure both values are numbers
    const curr = parseFloat(current) || 0;
    const prev = parseFloat(previous) || 0;

    if (prev === 0) return curr > 0 ? 100 : 0;
    const change = Math.round(((curr - prev) / prev) * 100);
    return isNaN(change) ? 0 : change;
  };

  statisticsTrend.value = {
    totalThisMonth: calculatePercentChange(
      statistics.value.totalThisMonth,
      previousStatistics.value.totalThisMonth
    ),
    totalDebt: calculatePercentChange(
      statistics.value.totalDebt,
      previousStatistics.value.totalDebt
    ),
    totalOperating: calculatePercentChange(
      statistics.value.totalOperating,
      previousStatistics.value.totalOperating
    ),
  };
};

// Update last updated time
const updateLastUpdatedTime = () => {
  const now = new Date();
  const hours = String(now.getHours()).padStart(2, "0");
  const minutes = String(now.getMinutes()).padStart(2, "0");
  const seconds = String(now.getSeconds()).padStart(2, "0");
  lastUpdated.value = `${hours}:${minutes}:${seconds}`;
};

// Start auto-refresh
const startAutoRefresh = () => {
  console.log("🔄 Auto-refresh started (every 30s)");
  refreshInterval.value = setInterval(() => {
    console.log("⏰ Auto-refresh triggered");
    refreshStatistics();
  }, AUTO_REFRESH_INTERVAL);
};

// Stop auto-refresh
const stopAutoRefresh = () => {
  if (refreshInterval.value) {
    clearInterval(refreshInterval.value);
    refreshInterval.value = null;
    console.log("⏹️ Auto-refresh stopped");
  }
};

// Refresh statistics only (without full page reload)
const refreshStatistics = async () => {
  if (isRefreshing.value) return;

  try {
    isRefreshing.value = true;
    const params = {
      per_page: 9999, // Get all for accurate statistics
      sort_by: "created_at",
      sort_order: "desc",
    };

    const response = await getPhieuChis(params);

    if (response && response.status) {
      const tempPhieuChis = response.data || [];

      // Recalculate statistics with new data
      const currentMonth = new Date().getMonth();
      const currentYear = new Date().getFullYear();

      // Save previous statistics
      previousStatistics.value = { ...statistics.value };

      let totalThisMonth = 0;
      let totalDebt = 0;
      let totalOperating = 0;

      tempPhieuChis.forEach((phieu) => {
        const phieuDate = new Date(phieu.ngay_chi);

        if (
          phieuDate.getMonth() === currentMonth &&
          phieuDate.getFullYear() === currentYear
        ) {
          totalThisMonth += parseFloat(phieu.so_tien_thanh_toan_ngay) || 0;
        }

        if (phieu.so_tien_con_no > 0) {
          totalDebt += parseFloat(phieu.so_tien_con_no) || 0;
        }

        if (phieu.loai_phieu_chi === "chi_van_hanh") {
          totalOperating += parseFloat(phieu.so_tien_thanh_toan_ngay) || 0;
        }
      });

      statistics.value = {
        totalThisMonth,
        totalDebt,
        totalOperating,
      };

      calculateTrends();
      updateLastUpdatedTime();

      console.log("✅ Statistics refreshed:", statistics.value);
    }
  } catch (error) {
    console.error("❌ Error refreshing statistics:", error);
  } finally {
    setTimeout(() => {
      isRefreshing.value = false;
    }, 500); // Show animation for at least 500ms
  }
};

// Manual refresh handler
const handleManualRefresh = async () => {
  console.log("🔄 Manual refresh triggered");
  await Promise.all([refreshStatistics(), loadPhieuChis()]);
  showSuccessToast("Đã cập nhật dữ liệu mới nhất");
};

// Transform data để hiển thị
const expenses = computed(() => {
  return phieuChis.value.map((phieu) => ({
    id: phieu.id,
    code: phieu.ma_phieu_chi,
    date: formatDate(phieu.ngay_chi),
    time: formatTime(phieu.created_at),
    description: phieu.ly_do_chi,
    supplier: phieu.nha_cung_cap
      ? `(NCC: ${phieu.nha_cung_cap.ten_nha_cung_cap})`
      : phieu.doi_tuong_nhan_tien
      ? `(${phieu.doi_tuong_nhan_tien})`
      : "",
    category: phieu.loai_phieu_chi,
    categoryLabel: phieu.loai_phieu_chi_label,
    totalAmount: phieu.tong_so_tien,
    paidAmount: phieu.so_tien_thanh_toan_ngay,
    remainingAmount: phieu.so_tien_con_no,
    status: phieu.trang_thai,
    statusLabel: phieu.trang_thai_label,
    rawData: phieu, // Giữ lại dữ liệu gốc
  }));
});

// Format date
const formatDate = (dateString) => {
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const year = date.getFullYear();
  return `${day}/${month}/${year}`;
};

// Format time
const formatTime = (dateString) => {
  const date = new Date(dateString);
  const hours = String(date.getHours()).padStart(2, "0");
  const minutes = String(date.getMinutes()).padStart(2, "0");
  return `${hours}:${minutes}`;
};

// Format currency
const formatCurrency = (amount) => {
  // Ensure amount is a valid number
  const numAmount = parseFloat(amount) || 0;

  return (
    new Intl.NumberFormat("vi-VN", {
      style: "decimal",
      minimumFractionDigits: 0,
    }).format(numAmount) + "đ"
  );
};

// Get category badge class
const getCategoryClass = (category) => {
  const classes = {
    chi_nhap_hang: "bg-blue-100 border-black/10 text-[#1447e6]",
    chi_van_hanh: "bg-[#fef9c2] border-black/10 text-[#a65f00]",
  };
  return classes[category] || "bg-gray-100 border-black/10 text-gray-700";
};

// Get status badge class
const getStatusClass = (status) => {
  const classes = {
    da_thanh_toan_du: "status-completed",
    chua_thanh_toan_du: "status-debt",
  };
  return classes[status] || "bg-gray-100 text-gray-700";
};

// Get status style (inline style để đảm bảo hiển thị màu)
const getStatusStyle = (status) => {
  const styles = {
    da_thanh_toan_du: {
      backgroundColor: "#d1fae5",
      color: "#059669",
    },
    hoan_thanh: {
      backgroundColor: "#d1fae5",
      color: "#059669",
    },
    chua_thanh_toan_du: {
      backgroundColor: "#fee2e2",
      color: "#dc2626",
    },
    con_no: {
      backgroundColor: "#fee2e2",
      color: "#dc2626",
    },
  };
  return styles[status] || { backgroundColor: "#f3f4f6", color: "#6b7280" };
};

const handleCreateExpense = async (data) => {
  console.log("New expense data:", data);
  showSuccessToast("Tạo phiếu chi thành công");
  isCreateExpenseModalOpen.value = false;

  // Reload danh sách và refresh statistics
  console.log("🔄 Refreshing after create...");
  await Promise.all([loadPhieuChis(), refreshStatistics()]);
};

const handleViewExpense = async (expense) => {
  try {
    // Lấy chi tiết phiếu chi từ API
    const response = await getPhieuChiById(expense.id);
    if (response && response.status) {
      selectedExpense.value = {
        ...response.data,
        // Transform để khớp với component ChiTietPhieuChi
        code: response.data.ma_phieu_chi,
        date: formatDate(response.data.ngay_chi),
        time: formatTime(response.data.created_at),
        description: response.data.ly_do_chi,
        supplier:
          response.data.nha_cung_cap?.ten_nha_cung_cap ||
          response.data.doi_tuong_nhan_tien,
        category: response.data.loai_phieu_chi,
        categoryLabel: response.data.loai_phieu_chi_label,
        totalAmount: response.data.tong_so_tien,
        paidAmount: response.data.so_tien_thanh_toan_ngay,
        remainingAmount: response.data.so_tien_con_no,
        status: response.data.trang_thai,
        statusLabel: response.data.trang_thai_label,
        recipient:
          response.data.doi_tuong_nhan_tien ||
          response.data.nha_cung_cap?.ten_nha_cung_cap,
        createdAt: `${formatTime(response.data.created_at)} - ${formatDate(
          response.data.created_at
        )}`,
        createdBy: response.data.nguoi_tao?.ten || "N/A",
        paymentHistory: [], // Có thể thêm sau nếu backend hỗ trợ
      };
      isViewExpenseModalOpen.value = true;
    }
  } catch (error) {
    console.error("Lỗi khi xem chi tiết:", error);
    showErrorToast("Không thể tải thông tin phiếu chi");
  }
};

const handleAddPayment = (expense) => {
  selectedExpenseForPayment.value = expense;
  isAddPaymentModalOpen.value = true;
};

const handlePaymentSubmit = async (data) => {
  console.log("💳 Payment submitted:", data);

  // Check if fully paid - kiểm tra nhiều điều kiện
  const isFullyPaid =
    data.so_tien_con_no === 0 ||
    data.so_tien_con_no <= 0 ||
    data.trang_thai === "da_thanh_toan_du" ||
    data.trang_thai === "hoan_thanh";

  if (isFullyPaid) {
    showSuccessToast(
      `✅ Đã thanh toán đủ! Phiếu chi ${data.ma_phieu_chi} đã hoàn thành.`
    );
  } else {
    showSuccessToast(
      `💰 Thanh toán thêm thành công. Còn nợ: ${formatCurrency(
        data.so_tien_con_no
      )}`
    );
  }

  // Close modal
  isAddPaymentModalOpen.value = false;
  selectedExpenseForPayment.value = null;

  // Reload danh sách và refresh statistics
  console.log("🔄 Refreshing after payment...");
  await Promise.all([loadPhieuChis(), refreshStatistics()]);
  console.log("✅ Refresh complete!");
};

// Load data khi component mount
onMounted(() => {
  loadPhieuChis();
  startAutoRefresh();
});

// Cleanup khi component unmount
onUnmounted(() => {
  stopAutoRefresh();
});
</script>

<style scoped>
/* Ensure Nunito Sans font is applied */
.font-nunito {
  font-family: "Nunito Sans", sans-serif;
}

/* Status badge colors */
.status-completed {
  background-color: #d1fae5 !important;
  color: #059669 !important;
}

.status-debt {
  background-color: #fee2e2 !important;
  color: #dc2626 !important;
}

/* Soft pulse animation for real-time updates */
@keyframes pulse-soft {
  0%,
  100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.95;
    transform: scale(0.998);
  }
}

.animate-pulse-soft {
  animation: pulse-soft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
