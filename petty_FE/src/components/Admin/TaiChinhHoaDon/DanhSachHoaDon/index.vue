<template>
  <div class="w-full min-h-screen px-8 py-6 flex flex-col gap-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-2">
      <h1 class="text-2xl font-semibold text-black">Danh sách Hóa đơn</h1>
      <p class="text-gray-500 font-medium text-base">
        Quản lý và theo dõi dòng tiền
      </p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-3 gap-6">
      <!-- Today's Revenue -->
      <div
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 flex items-center justify-between"
      >
        <div class="flex flex-col gap-1">
          <p class="text-sm font-medium text-black">Doanh thu hôm nay</p>
          <p
            class="font-nunito text-[30px] leading-9 text-[#00a63e] tracking-[0.3955px]"
          >
            1.550.000đ
          </p>
          <p class="font-nunito text-xs leading-4 text-[#6a7282]">
            Đã trừ hoàn tiền
          </p>
        </div>
        <div
          class="bg-green-100 rounded-full w-12 h-12 flex items-center justify-center"
        >
          <ArrowUpIcon class="w-6 h-6 text-green-500" />
        </div>
      </div>

      <!-- Unpaid -->
      <div
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 flex items-center justify-between"
      >
        <div class="flex flex-col gap-1">
          <p class="text-sm font-medium text-black">Chưa thanh toán</p>
          <p
            class="font-nunito text-[30px] leading-9 text-[#f54900] tracking-[0.3955px]"
          >
            800.000đ
          </p>
          <p class="font-nunito text-xs leading-4 text-[#6a7282]">
            Tổng giá trị HĐ chưa thanh toán
          </p>
        </div>
        <div
          class="bg-[#ffedd4] rounded-full w-12 h-12 flex items-center justify-center"
        >
          <InforIcon class="w-6 h-6 text-[#f54900]" />
        </div>
      </div>

      <!-- Refunded -->
      <div
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 flex items-center justify-between"
      >
        <div class="flex flex-col gap-1">
          <p class="text-sm font-medium text-black">Đã hoàn tiền</p>
          <p
            class="font-nunito text-[30px] leading-9 text-[#9810fa] tracking-[0.3955px]"
          >
            2.950.000đ
          </p>
          <p class="font-nunito text-xs leading-4 text-[#6a7282]">
            Tiền trả lại khách
          </p>
        </div>
        <div
          class="bg-purple-100 rounded-full w-12 h-12 flex items-center justify-center"
        >
          <AroundIcon class="w-6 h-6 text-[#9810fa]" />
        </div>
      </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div class="flex flex-col gap-4">
        <!-- Search Bar -->
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Tìm theo Mã hóa đơn, Tên khách hàng, SĐT..."
            class="bg-[#f3f3f5] border !border-transparent rounded-lg h-9 pl-10 pr-3 w-full font-nunito text-sm text-[#717182] tracking-tight focus:outline-none focus:ring-2 focus:ring-[#009689]"
          />
          <SearchIcon class="absolute left-3 top-[9px] w-5 h-5" />
        </div>

        <!-- Filter Buttons -->
        <div class="grid grid-cols-4 gap-4">
          <button
            class="bg-[#f3f3f5] border !border-gray-300 rounded-lg h-9 px-[13px] flex items-center justify-between hover:bg-gray-200 transition-colors"
          >
            <span
              class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight"
            >
              Hôm nay
            </span>
            <ChevronDownIcon />
          </button>

          <button
            class="bg-[#f3f3f5] border !border-gray-300 rounded-lg h-9 px-[13px] flex items-center justify-between hover:bg-gray-200 transition-colors"
          >
            <span
              class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight"
            >
              Tất cả
            </span>
            <ChevronDownIcon />
          </button>

          <button
            class="bg-[#f3f3f5] border !border-gray-300 rounded-lg h-9 px-[13px] flex items-center justify-between hover:bg-gray-200 transition-colors"
          >
            <span
              class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight"
            >
              Tất cả
            </span>
            <ChevronDownIcon />
          </button>

          <button
            class="bg-white border !border-gray-300 rounded-lg h-9 px-3 flex items-center justify-center gap-2 hover:bg-gray-50 transition-colors"
          >
            <DownloadIcon class="w-4 h-4" />
            <span
              class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
            >
              Xuất Excel
            </span>
          </button>
        </div>
      </div>
    </div>

    <!-- Invoice Table -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-black/10">
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Mã HĐ
              </th>
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Thời gian
              </th>
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Khách hàng
              </th>
              <th
                class="text-right px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Tổng giá trị
              </th>
              <th
                class="text-right px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Đã thanh toán
              </th>
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Hình thức
              </th>
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Trạng thái
              </th>
              <th
                class="text-left px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Người thu
              </th>
              <th
                class="text-right px-2 py-[10px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >
                Thao tác
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(invoice, index) in invoices"
              :key="index"
              class="border-b border-black/10 hover:bg-gray-50 transition-colors"
            >
              <!-- Invoice Code -->
              <td class="px-2 py-4">
                <p
                  class="font-nunito font-medium text-base leading-6 text-[#009689] tracking-tight"
                >
                  {{ invoice.code }}
                </p>
              </td>

              <!-- Time -->
              <td class="px-2 py-[8.5px]">
                <div class="flex flex-col gap-1">
                  <p
                    class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                  >
                    {{ invoice.time }}
                  </p>
                  <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                    {{ invoice.date }}
                  </p>
                </div>
              </td>

              <!-- Customer -->
              <td class="px-2 py-[8.5px]">
                <div class="flex flex-col gap-1">
                  <p
                    class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                  >
                    {{ invoice.customer }}
                  </p>
                  <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                    Pet: {{ invoice.petName }}
                  </p>
                </div>
              </td>

              <!-- Total Amount -->
              <td class="px-2 py-[19px]">
                <p
                  class="font-nunito text-sm leading-5 text-[#101828] text-right tracking-tight"
                >
                  {{ formatCurrency(invoice.totalAmount) }}
                </p>
              </td>

              <!-- Paid Amount -->
              <td class="px-2 py-5">
                <p
                  :class="[
                    'font-nunito text-sm leading-5 text-right tracking-tight',
                    invoice.paidAmount > 0
                      ? getPaidAmountColor(invoice.status)
                      : 'text-[#99a1af]',
                  ]"
                >
                  {{
                    invoice.paidAmount > 0
                      ? formatCurrency(invoice.paidAmount)
                      : "0 đ"
                  }}
                </p>
              </td>

              <!-- Payment Method -->
              <td class="px-2 py-[17.5px]">
                <span
                  :class="[
                    'inline-flex items-center px-2 py-[3px] rounded-lg font-nunito font-medium text-xs leading-4 border',
                    getPaymentMethodStyle(invoice.paymentMethod),
                  ]"
                >
                  {{ getPaymentMethodLabel(invoice.paymentMethod) }}
                </span>
              </td>

              <!-- Status -->
              <td class="px-2 py-[17.5px]">
                <span
                  :class="[
                    'inline-flex items-center px-2 py-[3px] rounded-lg font-nunito font-medium text-xs leading-4 border-0',
                    getStatusStyle(invoice.status),
                  ]"
                >
                  {{ getStatusLabel(invoice.status) }}
                </span>
              </td>

              <!-- Collector -->
              <td class="px-2 py-[8.5px]">
                <div class="flex flex-col gap-1">
                  <p
                    class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                  >
                    {{ invoice.collector }}
                  </p>
                  <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                    {{ invoice.department }}
                  </p>
                </div>
              </td>

              <!-- Actions -->
              <td class="px-2 py-[12.5px]">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="handleViewInvoice(invoice)"
                    class="w-9 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg transition-colors"
                  >
                    <EyeIcon class="w-4 h-4" />
                  </button>
                  <button
                    class="w-9 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg transition-colors"
                  >
                    <PrintIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modals -->
    <ChiTietHoaDon
      v-if="isInvoiceDetailModalOpen"
      :invoice="selectedInvoice"
      @close="isInvoiceDetailModalOpen = false"
    />
  </div>
</template>

<script setup>
import { ref } from "vue";
import ChiTietHoaDon from "./ChiTietHoaDon/index.vue";
//Icon SVG
import ArrowUpIcon from "@/assets/svg/arrow-up.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import PrintIcon from "@/assets/svg/print.svg";
import SearchIcon from "@/assets/svg/search.svg";
import EyeIcon from "@/assets/svg/eye.svg";
import AroundIcon from "@/assets/svg/around.svg";
import InforIcon from "@/assets/svg/infor.svg";
import DownloadIcon from "@/assets/svg/download.svg";

// Reactive state
const searchQuery = ref("");
const isInvoiceDetailModalOpen = ref(false);
const selectedInvoice = ref(null);

// Sample data
const invoices = ref([
  {
    code: "HD001234",
    time: "10:30",
    date: "20/11/2025",
    customer: "Nguyễn Văn A",
    phone: "0901234567",
    petName: "Milo",
    doctor: "BS. Nguyễn Văn B",
    department: "(Khoa Vận hành)",
    collectorDepartment: "Khoa Vận hành",
    totalAmount: 1000000,
    paidAmount: 1000000,
    paymentMethod: "cash",
    status: "paid",
    collector: "Thu ngân Mai",
    items: [
      { name: "Khám tổng quát", quantity: 1, price: 150000, total: 150000 },
      { name: "Siêu âm ổ bụng", quantity: 1, price: 300000, total: 300000 },
      {
        name: "Thuốc A (Omeprazole)",
        quantity: 2,
        price: 50000,
        total: 100000,
      },
      {
        name: "Thuốc B (Metronidazole)",
        quantity: 1,
        price: 450000,
        total: 450000,
      },
    ],
    transactions: [
      {
        time: "10:30",
        date: "20/11/2025",
        method: "Tiền mặt",
        methodIcon: "💵",
        amount: 900000,
        note: "Thanh toán tại quầy",
        status: "success",
      },
    ],
    promotion: {
      code: "NEWCUSTOMER2025",
      description: "Khuyến mãi khách hàng mới",
      type: "Giảm cố định",
      discount: -100000,
    },
  },
  {
    code: "HD001235",
    time: "11:15",
    date: "20/11/2025",
    customer: "Trần Thị B",
    phone: "0912345678",
    petName: "Luna",
    doctor: "BS. Trần Văn C",
    department: "(Khoa Vận hành)",
    collectorDepartment: "Khoa Vận hành",
    totalAmount: 800000,
    paidAmount: 0,
    paymentMethod: "cash",
    status: "unpaid",
    collector: "Thu ngân Mai",
    items: [
      { name: "Khám bệnh", quantity: 1, price: 200000, total: 200000 },
      { name: "Xét nghiệm máu", quantity: 1, price: 600000, total: 600000 },
    ],
    transactions: [],
    promotion: null,
  },
  {
    code: "HD001236",
    time: "14:20",
    date: "20/11/2025",
    customer: "Lê Văn C",
    phone: "0923456789",
    petName: "Max",
    doctor: "BS. Lê Thị D",
    department: "(Khoa Vận hành)",
    collectorDepartment: "Khoa Vận hành",
    totalAmount: 550000,
    paidAmount: 550000,
    paymentMethod: "vnpay",
    status: "paid",
    collector: "Thu ngân Lan",
    items: [
      { name: "Tiêm phòng", quantity: 1, price: 350000, total: 350000 },
      { name: "Vitamin tổng hợp", quantity: 1, price: 200000, total: 200000 },
    ],
    transactions: [
      {
        time: "14:20",
        date: "20/11/2025",
        method: "VNPay",
        methodIcon: "💳",
        amount: 550000,
        note: "Thanh toán qua VNPay",
        status: "success",
      },
    ],
    promotion: null,
  },
  {
    code: "HD001237",
    time: "15:45",
    date: "20/11/2025",
    customer: "Phạm Thị D",
    phone: "0934567890",
    petName: "Bella",
    doctor: "BS. Phạm Văn E",
    department: "(Khoa Vận hành)",
    collectorDepartment: "Khoa Vận hành",
    totalAmount: 2500000,
    paidAmount: 2500000,
    paymentMethod: "transfer",
    status: "refunded",
    collector: "Thu ngân Mai",
    items: [
      { name: "Phẫu thuật nhỏ", quantity: 1, price: 2000000, total: 2000000 },
      { name: "Thuốc kháng sinh", quantity: 1, price: 500000, total: 500000 },
    ],
    transactions: [
      {
        time: "15:45",
        date: "20/11/2025",
        method: "Chuyển khoản",
        methodIcon: "🏦",
        amount: 2500000,
        note: "Chuyển khoản ngân hàng",
        status: "success",
      },
    ],
    promotion: null,
  },
  {
    code: "HD001239",
    time: "16:30",
    date: "20/11/2025",
    customer: "Võ Thị F",
    phone: "0945678901",
    petName: "Kitty",
    doctor: "BS. Võ Văn G",
    department: "(Khoa Vận hành)",
    collectorDepartment: "Khoa Vận hành",
    totalAmount: 450000,
    paidAmount: 450000,
    paymentMethod: "cash",
    status: "refunding",
    collector: "Thu ngân Mai",
    items: [
      { name: "Tắm và vệ sinh", quantity: 1, price: 250000, total: 250000 },
      { name: "Cắt móng", quantity: 1, price: 200000, total: 200000 },
    ],
    transactions: [
      {
        time: "16:30",
        date: "20/11/2025",
        method: "Tiền mặt",
        methodIcon: "💵",
        amount: 450000,
        note: "Thanh toán tiền mặt",
        status: "success",
      },
    ],
    promotion: null,
  },
]);

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
    minimumFractionDigits: 0,
  })
    .format(amount)
    .replace("₫", "đ");
};

const getPaymentMethodLabel = (method) => {
  const labels = {
    cash: "Tiền mặt",
    vnpay: "VNPay",
    transfer: "Chuyển khoản",
  };
  return labels[method] || method;
};

const getPaymentMethodStyle = (method) => {
  const styles = {
    cash: "bg-gray-100 border-black/10 text-[#364153]",
    vnpay: "bg-purple-100 border-black/10 text-[#8200db]",
    transfer: "bg-blue-100 border-black/10 text-[#1447e6]",
  };
  return styles[method] || "bg-gray-100 border-black/10 text-[#364153]";
};

const getStatusLabel = (status) => {
  const labels = {
    paid: "Đã thanh toán",
    unpaid: "Chưa thanh toán",
    refunded: "Đã hoàn tiền",
    refunding: "Đang hoàn tiền",
  };
  return labels[status] || status;
};

const getStatusStyle = (status) => {
  const styles = {
    paid: "bg-green-100 text-[#008236]",
    unpaid: "bg-[#ffedd4] text-[#ca3500]",
    refunded: "bg-blue-100 text-[#1447e6]",
    refunding: "bg-[#fef9c2] text-[#a65f00]",
  };
  return styles[status] || "bg-gray-100 text-[#364153]";
};

const getPaidAmountColor = (status) => {
  const colors = {
    paid: "text-[#00a63e]",
    refunded: "text-[#155dfc]",
    refunding: "text-[#d08700]",
  };
  return colors[status] || "text-[#101828]";
};

const handleViewInvoice = (invoice) => {
  selectedInvoice.value = invoice;
  isInvoiceDetailModalOpen.value = true;
};
</script>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
