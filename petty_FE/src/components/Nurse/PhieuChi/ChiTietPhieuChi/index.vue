<template>
  <div
    class="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center font-nunito p-4"
    @click.self="$emit('close')"
  >
    <div
      class="bg-white w-full max-w-[550px] max-h-[90vh] flex flex-col rounded-[16px] shadow-2xl overflow-hidden animate-scale-up"
    >
      <!-- Header -->
      <div class="px-4 py-4 border-b border-gray-100 flex-shrink-0">
        <h2 class="text-xl font-bold text-gray-900 leading-7">
          Chi tiết phiếu chi - {{ expense.code }}
        </h2>
        <p class="text-sm text-gray-500 mt-1">
          Thông tin chi tiết và lịch sử thanh toán
        </p>
      </div>

      <!-- Scrollable Content -->
      <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
        <!-- Info Info Block -->
        <div
          class="bg-gray-200 rounded-xl p-5 grid grid-cols-2 gap-x-8 gap-y-5"
        >
          <!-- Col 1 -->
          <div class="flex flex-col gap-1">
            <span class="text-sm font-medium text-gray-500">Loại chi</span>
            <div
              :class="getCategoryClass(expense.category)"
              class="px-2.5 py-1 rounded-md text-xs font-semibold w-fit border"
            >
              {{ expense.categoryLabel }}
            </div>
          </div>

          <div class="flex flex-col gap-1">
            <span class="text-sm font-medium text-gray-500">Người tạo</span>
            <span class="text-sm font-semibold text-gray-900">{{
              expense.createdBy
            }}</span>
          </div>

          <!-- Col 2 / Full Width -->
          <div class="col-span-2 flex flex-col gap-1">
            <span class="text-sm font-medium text-gray-500"
              >Đối tượng nhận tiền</span
            >
            <span class="text-base text-gray-900">{{ expense.recipient }}</span>
          </div>

          <div class="flex flex-col gap-1">
            <span class="text-sm font-medium text-gray-500">Ngày tạo</span>
            <span class="text-sm font-medium text-gray-900">{{
              expense.createdAt
            }}</span>
          </div>

          <div class="flex flex-col gap-1">
            <span class="text-sm font-medium text-gray-500">Trạng thái</span>
            <div
              :class="getStatusClass(expense.status)"
              class="px-2.5 py-1 rounded-md text-xs font-semibold w-fit flex items-center gap-1.5"
            >
              <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
              {{ expense.statusLabel }}
            </div>
          </div>
        </div>

        <!-- Description & Values -->
        <div>
          <h3
            class="text-sm font-semibold text-gray-900 mb-3 uppercase tracking-wide"
          >
            Chi tiết nội dung
          </h3>
          <div class="border !border-gray-300 rounded-xl overflow-hidden">
            <!-- Content -->
            <div class="p-4 bg-white border-b border-gray-100">
              <p class="text-sm text-gray-700 leading-relaxed">
                {{ expense.description }}
              </p>
            </div>
            <!-- Totals -->
            <div class="bg-gray-50/50 p-4 space-y-3">
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">Tổng giá trị</span>
                <span class="text-lg font-bold text-gray-900">{{
                  formatCurrency(expense.totalAmount)
                }}</span>
              </div>
              <div class="h-px bg-gray-200 w-full"></div>
              <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col">
                  <span class="text-xs text-gray-500">Đã thanh toán</span>
                  <span class="text-sm font-semibold text-green-600">{{
                    formatCurrency(expense.paidAmount)
                  }}</span>
                </div>
                <div class="flex flex-col text-right">
                  <span class="text-xs text-gray-500">Còn nợ</span>
                  <span class="text-sm font-semibold text-red-500">{{
                    formatCurrency(expense.remainingAmount)
                  }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment History -->
        <div>
          <h3
            class="text-sm font-semibold text-gray-900 mb-3 uppercase tracking-wide flex justify-between items-center"
          >
            Lịch sử giao dịch
            <span class="text-xs font-normal text-gray-500 normal-case"
              >(Payment History)</span
            >
          </h3>

          <!-- Loading -->
          <div v-if="loading" class="flex justify-center py-6">
            <svg
              class="animate-spin h-6 w-6 text-orange-500"
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
          </div>

          <!-- Empty -->
          <div
            v-else-if="displayPaymentHistory.length === 0"
            class="rounded-xl border border-dashed border-gray-300 p-8 text-center bg-gray-50"
          >
            <p class="text-sm text-gray-500">Chưa có lịch sử thanh toán</p>
          </div>

          <!-- List -->
          <div v-else class="space-y-3">
            <div
              v-for="(payment, index) in displayPaymentHistory"
              :key="index"
              class="border border-gray-200 rounded-xl p-4 bg-white hover:border-orange-200 transition-colors shadow-sm"
            >
              <div class="flex justify-between items-start mb-3">
                <span
                  class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded"
                  >{{ payment.date }}</span
                >
                <div
                  class="flex items-center gap-1 bg-green-50 text-green-700 px-2 py-1 rounded-md"
                >
                  <svg
                    class="w-3.5 h-3.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    ></path>
                  </svg>
                  <span class="text-xs font-semibold">Thành công</span>
                </div>
              </div>

              <div class="flex items-center gap-3 mb-3">
                <div
                  class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center text-lg shadow-sm border border-orange-100"
                >
                  {{
                    payment.method.toLowerCase().includes("tiền mặt")
                      ? "💵"
                      : "🏦"
                  }}
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    {{ payment.method }}
                  </p>
                  <p
                    class="text-xs text-gray-500 italic max-w-[200px] truncate"
                  >
                    {{ payment.note }}
                  </p>
                </div>
              </div>

              <div
                class="flex justify-between items-end pt-3 border-t border-gray-100 border-dashed"
              >
                <div class="flex flex-col gap-0.5">
                  <div
                    v-if="payment.cashAmount > 0"
                    class="text-[11px] text-gray-500"
                  >
                    Tiền mặt:
                    <span class="font-medium text-gray-700">{{
                      formatCurrency(payment.cashAmount)
                    }}</span>
                  </div>
                  <div
                    v-if="payment.transferAmount > 0"
                    class="text-[11px] text-gray-500"
                  >
                    CK:
                    <span class="font-medium text-gray-700">{{
                      formatCurrency(payment.transferAmount)
                    }}</span>
                  </div>
                </div>
                <div class="text-lg font-bold text-green-600">
                  {{ formatCurrency(payment.amount) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div
        class="p-4 border-t border-gray-100 bg-white flex justify-end gap-3 flex-shrink-0 z-10"
      >
        <button
          @click="$emit('close')"
          class="px-5 py-2.5 border !border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all active:scale-95"
        >
          Đóng
        </button>
        <button
          v-if="displayPaymentHistory.length > 0"
          @click="exportToPDF"
          class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all flex items-center gap-2 active:scale-95"
        >
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
            ></path>
          </svg>
          In phiếu chi
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { getPhieuChiPaymentHistory } from "@/utils/phieuChi";
import { showErrorToast } from "@/utils/toast";

// Props
const props = defineProps({
  expense: {
    type: Object,
    required: true,
    default: () => ({
      code: "PC0056",
      category: "purchase", // purchase, operating
      categoryLabel: "Nhập hàng",
      recipient: "Công ty CP Dược Thú Y Việt Nam",
      description: "Nhập thuốc lô A1 - Tháng 11",
      createdAt: "09:30 - 20/11/2025",
      createdBy: "Kế toán Hoa",
      status: "debt",
      statusLabel: "Còn nợ NCC",
      totalAmount: 15000000,
      paidAmount: 5000000,
      remainingAmount: 10000000,
      paymentHistory: [],
    }),
  },
});

// Define emits
const emit = defineEmits(["close", "add-payment"]);

// State
const paymentHistory = ref([]);
const loading = ref(false);

// Load payment history
const loadPaymentHistory = async () => {
  if (!props.expense?.id) {
    return;
  }

  try {
    loading.value = true;
    const response = await getPhieuChiPaymentHistory(props.expense.id);

    if (response && response.status === true && response.data) {
      if (Array.isArray(response.data)) {
        paymentHistory.value = response.data.map((item, index) => {
          return {
            date: formatDateTime(item.ngay_thanh_toan || item.created_at),
            method: getPaymentMethodLabel(item.hinh_thuc_thanh_toan),
            amount: parseFloat(item.so_tien_thanh_toan) || 0,
            note: item.ghi_chu || "Không có ghi chú",
            status: "success",
            cashAmount: parseFloat(item.tien_mat) || 0,
            transferAmount: parseFloat(item.tien_chuyen_khoan) || 0,
            rawData: item,
          };
        });
      } else {
        paymentHistory.value = [];
      }
    } else {
      paymentHistory.value = props.expense.paymentHistory || [];
    }
  } catch (error) {
    console.error("Lỗi khi tải lịch sử thanh toán:", error);
    paymentHistory.value = props.expense.paymentHistory || [];
    if (error.response?.status !== 404) {
      // Slient fail or usage toast if needed
    }
  } finally {
    loading.value = false;
  }
};

// Format date time
const formatDateTime = (dateString) => {
  if (!dateString) return "N/A";
  const date = new Date(dateString);
  const hours = String(date.getHours()).padStart(2, "0");
  const minutes = String(date.getMinutes()).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const year = date.getFullYear();
  return `${hours}:${minutes} - ${day}/${month}/${year}`;
};

// Get payment method label
const getPaymentMethodLabel = (method) => {
  const labels = {
    tien_mat: "Tiền mặt",
    chuyen_khoan: "Chuyển khoản",
    ca_hai: "Tiền mặt + Chuyển khoản",
    cash: "Tiền mặt",
    transfer: "Chuyển khoản",
    both: "Tiền mặt + Chuyển khoản",
  };
  return labels[method] || method || "Không xác định";
};

// Computed: Merge payment history from props and loaded data
const displayPaymentHistory = computed(() => {
  return paymentHistory.value.length > 0
    ? paymentHistory.value
    : props.expense.paymentHistory || [];
});

// Load when component mounts
onMounted(() => {
  loadPaymentHistory();
});

// Export to PDF handler
const exportToPDF = async () => {
  if (!props.expense?.id) {
    showErrorToast("Không tìm thấy ID phiếu chi");
    return;
  }

  try {
    const API_BASE =
      import.meta.env.VITE_API_BASE || "http://127.0.0.1:8000/api";
    const token = localStorage.getItem("token");
    const url = `${API_BASE}/phieu-chi/${props.expense.id}/pdf`;

    const response = await fetch(url, {
      method: "GET",
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      throw new Error("Không thể tải file PDF");
    }

    const blob = await response.blob();
    const blobUrl = window.URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = blobUrl;
    link.download = `PhieuChi_${
      props.expense.code
    }_${new Date().getTime()}.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(blobUrl);
  } catch (error) {
    console.error("Lỗi khi hủy PDF:", error);
    showErrorToast(
      error.message || "Không thể xuất PDF. Vui lòng thử lại sau."
    );
  }
};

// Format currency
const formatCurrency = (amount) => {
  return (
    new Intl.NumberFormat("vi-VN", {
      style: "decimal",
      minimumFractionDigits: 0,
    }).format(amount) + "đ"
  );
};

// Get category badge class
const getCategoryClass = (category) => {
  // Styles based on Tailwind classes
  const classes = {
    purchase: "bg-purple-50 text-purple-700 border-purple-100",
    chi_nhap_hang: "bg-purple-50 text-purple-700 border-purple-100",
    operating: "bg-amber-50 text-amber-700 border-amber-100",
    chi_van_hanh: "bg-amber-50 text-amber-700 border-amber-100",
    salary: "bg-blue-50 text-blue-700 border-blue-100",
    luong: "bg-blue-50 text-blue-700 border-blue-100",
    other: "bg-gray-50 text-gray-700 border-gray-100",
  };
  return classes[category] || classes.other;
};

// Get status badge class
const getStatusClass = (status) => {
  const classes = {
    completed: "bg-green-50 text-green-700",
    da_thanh_toan_du: "bg-green-50 text-green-700",
    hoan_thanh: "bg-green-50 text-green-700",
    debt: "bg-red-50 text-red-700",
    chua_thanh_toan_du: "bg-red-50 text-red-700",
    partial: "bg-orange-50 text-orange-700",
  };
  return classes[status] || "bg-gray-100 text-gray-700";
};
</script>

<style scoped>
.font-nunito {
  font-family: "Nunito Sans", sans-serif;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #e5e7eb;
  border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #d1d5db;
}

@keyframes scale-up {
  0% {
    transform: scale(0.95);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.animate-scale-up {
  animation: scale-up 0.2s ease-out forwards;
}
</style>
