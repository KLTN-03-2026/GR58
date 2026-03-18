<template>
  <div
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-[9999] p-4 font-nunito"
    @click.self="$emit('close')"
  >
    <div
      class="bg-white w-full max-w-[510px] max-h-[90vh] flex flex-col rounded-[16px] shadow-2xl overflow-hidden animate-scale-up"
    >
      <!-- Header -->
      <div class="px-4 py-4 border-b !border-gray-300 flex-shrink-0">
        <h2 class="text-2xl font-semibold text-gray-900 leading-8">
          Chi tiết hóa đơn - {{ invoice.code }}
        </h2>
        <p class="text-sm text-[#717182] mt-1">
          Thông tin chi tiết và lịch sử thanh toán
        </p>
      </div>

      <!-- Scrollable Content -->
      <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
        <!-- Info Card -->
        <div
          class="bg-gray-200 rounded-[10px] p-4 grid grid-cols-2 gap-x-4 gap-y-4"
        >
          <!-- Customer -->
          <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-[#4a5565]">Khách hàng</label>
            <div class="flex flex-col">
              <p class="text-base text-[#101828] leading-6">
                {{ invoice.customer }}
              </p>
              <p class="text-base text-[#101828] leading-6">
                ({{ invoice.phone }})
              </p>
              <p class="text-sm text-[#4a5565] mt-0.5">
                Pet: {{ invoice.petName }}
              </p>
            </div>
          </div>

          <!-- Doctor -->
          <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-[#4a5565]"
              >Bác sĩ chỉ định</label
            >
            <div class="flex flex-col">
              <p class="text-base text-[#101828] leading-6">
                {{ invoice.doctor }}
              </p>
              <p class="text-sm text-[#4a5565]">{{ invoice.department }}</p>
            </div>
          </div>

          <!-- Date -->
          <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-[#4a5565]">Ngày tạo</label>
            <p class="text-base text-[#101828] leading-6">
              {{ invoice.time }} - <br />{{ invoice.date }}
            </p>
          </div>

          <!-- Cashier -->
          <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-[#4a5565]">Người thu</label>
            <div class="flex flex-col">
              <p class="text-base text-[#101828] leading-6">
                {{ invoice.collector }}
              </p>
              <p class="text-sm text-[#4a5565]">
                {{ invoice.collectorDepartment }}
              </p>
            </div>
          </div>
        </div>

        <!-- Services & Medicines List -->
        <div class="flex flex-col gap-3">
          <h3 class="text-base text-[#101828] font-normal leading-6">
            Danh sách Dịch vụ & Thuốc
          </h3>
          <div class="border !border-gray-400 rounded-[10px] overflow-hidden">
            <table class="w-full text-sm text-left">
              <thead class="bg-white border-b !border-gray-300">
                <tr>
                  <th
                    class="px-3 py-2.5 font-medium text-gray-900 border-r border-transparent"
                  >
                    Tên dịch vụ/Thuốc
                  </th>
                  <th
                    class="px-3 py-2.5 font-medium text-gray-900 text-center w-12"
                  >
                    SL
                  </th>
                  <th
                    class="px-3 py-2.5 font-medium text-gray-900 text-right w-24"
                  >
                    Đơn giá
                  </th>
                  <th
                    class="px-3 py-2.5 font-medium text-gray-900 text-right w-24"
                  >
                    Thành tiền
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-black/10">
                <tr
                  v-for="(item, index) in invoice.items"
                  :key="index"
                  class="bg-white"
                >
                  <td class="px-3 py-2.5 text-gray-900">{{ item.name }}</td>
                  <td class="px-3 py-2.5 text-center text-gray-900">
                    {{ item.quantity }}
                  </td>
                  <td class="px-3 py-2.5 text-right text-gray-900">
                    {{ formatCurrency(item.price) }}
                  </td>
                  <td class="px-3 py-2.5 text-right font-medium text-gray-900">
                    {{ formatCurrency(item.total) }}
                  </td>
                </tr>
                <!-- Total Row -->
                <tr class="bg-gray-50 border-t border-black/10">
                  <td
                    colspan="3"
                    class="px-3 py-3 text-right font-bold text-gray-900"
                  >
                    Tổng cộng:
                  </td>
                  <td
                    class="px-3 py-3 text-right font-bold text-lg text-gray-900"
                  >
                    {{ formatCurrency(invoice.totalAmount) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Payment History -->
        <div class="flex flex-col gap-3">
          <h3 class="text-base text-[#101828] font-normal leading-6">
            Lịch sử Giao dịch (Payment History)
          </h3>

          <div class="flex flex-col gap-3">
            <!-- Transaction Item -->
            <div
              v-for="(transaction, index) in invoice.transactions"
              :key="index"
              class="bg-white border !border-gray-300 rounded-[10px] p-[17px] flex justify-between items-start"
            >
              <div class="flex flex-col gap-2">
                <p class="text-sm text-[#4a5565]">
                  {{ transaction.time }} - {{ transaction.date }}
                </p>
                <div class="flex flex-col gap-0.5">
                  <p class="text-sm text-[#364153] flex items-center gap-1">
                    <!-- {{ transaction.methodIcon }} -->
                    {{ transaction.method }}
                  </p>
                  <div class="flex items-baseline gap-1">
                    <span class="text-lg text-[#101828]">Số tiền:</span>
                    <span class="text-lg font-bold text-[#009689]">{{
                      formatCurrency(transaction.amount)
                    }}</span>
                  </div>
                  <p class="text-sm text-[#4a5565] italic mt-1">
                    Ghi chú: {{ transaction.note }}
                  </p>
                </div>
              </div>

              <span
                class="inline-flex items-center gap-1.5 px-[9px] py-[3px] rounded-lg bg-[#dcfce7] text-[#008236] text-xs font-medium w-fit h-fit"
              >
                Thành công
              </span>
            </div>

            <!-- Final Status Summary -->
            <div
              class="bg-[#f0fdfa] border !border-teal-400 rounded-[10px] p-[17px]"
            >
              <div class="flex flex-col gap-1">
                <p class="text-sm text-[#364153]">Trạng thái cuối:</p>
                <div class="flex items-center gap-3">
                  <span
                    class="inline-flex items-center gap-1 px-[9px] py-[3px] rounded-lg bg-[#dcfce7] text-[#008236] text-xs font-medium"
                  >
                    Đã thanh toán
                  </span>
                  <div class="flex items-baseline gap-1">
                    <span class="text-sm text-[#364153]">Đã thanh toán:</span>
                    <span class="text-sm font-bold text-[#00a63e]">{{
                      formatCurrency(invoice.paidAmount)
                    }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Promotion -->
        <div v-if="invoice.promotion" class="flex flex-col gap-3">
          <h3 class="text-base text-[#101828] font-normal leading-6">
            Mã khuyến mãi
          </h3>
          <div
            class="bg-[#f0fdfa] border-2 !border-teal-400 rounded-[10px] p-[18px] flex justify-between items-start"
          >
            <div class="flex flex-col gap-3">
              <div class="flex items-center gap-3">
                <span
                  class="bg-[#009689] text-white px-[9px] py-[3px] rounded-lg text-xs font-medium"
                >
                  {{ invoice.promotion.code }}
                </span>
                <span class="text-sm text-[#364153]">{{
                  invoice.promotion.description
                }}</span>
              </div>

              <div class="flex items-center gap-4">
                <div class="text-sm text-[#4a5565]">
                  Loại:
                  <span class="font-bold">{{ invoice.promotion.type }}</span>
                </div>
                <div class="flex items-baseline gap-1">
                  <span class="text-lg text-[#00786f]">Giảm giá:</span>
                  <span class="text-lg font-bold text-[#009689]">{{
                    formatCurrency(Math.abs(invoice.promotion.discount))
                  }}</span>
                </div>
              </div>
            </div>

            <div class="hidden sm:block">
              <span
                class="inline-flex items-center px-[9px] py-[3px] rounded-lg bg-[#dcfce7] text-[#008236] text-xs font-medium whitespace-nowrap"
              >
                Đã áp dụng
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div
        class="py-4 px-6 border-t border-black/10 bg-white flex justify-end gap-3 flex-shrink-0 z-10"
      >
        <button
          @click="$emit('close')"
          class="px-4 py-2 border !border-gray-300 rounded-lg text-sm font-medium text-neutral-950 hover:bg-gray-50 transition-colors"
        >
          Đóng
        </button>
        <button
          @click="printInvoice"
          class="px-4 border !border-gray-300 rounded-lg text-sm font-medium text-neutral-950 hover:bg-gray-50 transition-colors flex items-center gap-2"
        >
          In hóa đơn
        </button>
        <button
          @click="refundInvoice"
          class="px-4 bg-[#d4183d] text-white rounded-lg text-sm font-medium hover:bg-[#b71535] transition-colors flex items-center gap-2"
        >
          Hoàn tiền
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
// Props
const props = defineProps({
  invoice: {
    type: Object,
    default: () => ({
      code: "HD001234",
      customer: "Nguyễn Văn A",
      phone: "0901234567",
      petName: "Milo",
      doctor: "BS. Nguyễn Văn B",
      department: "Khoa Lâm Sàng",
      time: "10:30",
      date: "20/11/2025",
      collector: "Thu ngân Mai",
      collectorDepartment: "Khoa Vận hành",
      items: [
        {
          name: "Khám tổng quát",
          quantity: 1,
          price: 150000,
          total: 150000,
        },
        {
          name: "Siêu âm ổ bụng",
          quantity: 1,
          price: 300000,
          total: 300000,
        },
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
      totalAmount: 1000000,
      paidAmount: 1000000,
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
    }),
  },
});

// Emits
const emit = defineEmits(["close", "print", "refund"]);

// Icons
const iconPrint =
  "https://www.figma.com/api/mcp/asset/951df8cf-41b3-429b-a733-64acd07f888d";
const iconRefund =
  "https://www.figma.com/api/mcp/asset/b06c2e12-bea4-406b-960f-580f9b22d480";

// Methods
const formatCurrency = (amount) => {
  return (
    new Intl.NumberFormat("vi-VN", {
      style: "decimal",
      minimumFractionDigits: 0,
    }).format(amount) + "đ"
  );
};

const printInvoice = () => {
  emit("print", props.invoice);
};

const refundInvoice = () => {
  emit("refund", props.invoice);
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
  background-color: #c1c1c1;
  border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #a8a8a8;
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