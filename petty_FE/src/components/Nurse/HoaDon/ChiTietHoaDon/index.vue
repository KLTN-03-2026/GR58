<template>
  <div class="w-full flex items-start gap-6">
    <!-- Left Section: Invoice Details -->
    <div class="flex flex-col gap-4 flex-1">
      <!-- Invoice Detail Card -->
      <div
        class="bg-white border !border-gray-300 rounded-[14px] p-6 shadow-sm"
      >
        <!-- Card Header -->
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-base font-semibold text-black">Chi tiết Hóa đơn</h2>
        </div>

        <!-- Invoice Info (Gray Box) -->
        <div class="bg-gray-50 border !border-gray-300 rounded-[10px] p-3 mb-4">
          <div class="grid grid-cols-2 grid-rows-2 gap-2">
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">Mã HĐ:</span>
              <span class="text-sm font-bold text-black">
                {{ invoiceData.invoiceId }}
              </span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">Ngày:</span>
              <span class="text-sm text-black">
                {{ invoiceData.date }}
              </span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">Khách hàng:</span>
              <span class="text-sm text-black">
                {{ invoiceData.customerName }}
              </span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">Thú cưng:</span>
              <span class="text-sm text-black">
                {{ invoiceData.petName }}
              </span>
            </div>
          </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-hidden mb-4">
          <table class="w-full">
            <thead>
              <tr class="border-b !border-gray-300">
                <th
                  class="text-left px-2 py-2.5 text-sm font-medium text-black"
                >
                  Dịch vụ / Sản phẩm
                </th>
                <th
                  class="text-center px-2 py-2.5 text-sm font-medium text-black w-[47px]"
                >
                  SL
                </th>
                <th
                  class="text-right px-2 py-2.5 text-sm font-medium text-black w-[117px]"
                >
                  Đơn giá
                </th>
                <th
                  class="text-right px-2 py-2.5 text-sm font-medium text-black w-[125px]"
                >
                  Thành tiền
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(item, index) in invoiceData.items"
                :key="index"
                class="border-b !border-gray-300"
              >
                <td class="px-2 py-2.5 text-sm text-black">
                  {{ item.name }}
                </td>
                <td class="px-2 py-2.5 text-sm text-black text-center">
                  {{ item.quantity }}
                </td>
                <td class="px-2 py-2.5 text-sm text-black text-right">
                  {{ formatCurrency(item.unitPrice) }}
                </td>
                <td class="px-2 py-2.5">
                  <p class="text-sm font-bold text-black text-right">
                    {{ formatCurrency(item.totalPrice) }}
                  </p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Summary -->
        <div class="border-t !border-gray-300 pt-4 flex flex-col gap-2">
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600"> Tạm tính: </span>
            <span class="text-sm text-black">
              {{ formatCurrency(subtotal) }}
            </span>
          </div>

          <!-- Discount Row (if applied) -->
          <div v-if="discount > 0" class="flex items-center justify-between">
            <span class="text-sm text-[#00a63e]"> Giảm giá: </span>
            <span class="text-sm text-[#00a63e]">
              -{{ formatCurrency(discount) }}
            </span>
          </div>

          <!-- Total -->
          <div
            class="border-t !border-gray-300 pt-2 flex items-center justify-between"
          >
            <p class="text-lg font-bold text-black">Tổng phải thu:</p>
            <p class="text-2xl font-bold text-[#155dfc]">
              {{ formatCurrency(total) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Discount Code Card -->
      <div
        class="bg-white border !border-gray-300 rounded-[14px] p-6 shadow-sm"
      >
        <div class="flex items-center gap-2 mb-6">
          <!-- <img :src="iconTag" alt="Tag" class="w-4 h-4" /> -->
          <h3 class="text-sm font-medium text-black">Mã giảm giá</h3>
        </div>

        <div class="flex gap-2 mb-2">
          <input
            v-model="discountCode"
            type="text"
            placeholder="Nhập mã giảm giá..."
            class="flex-1 bg-gray-50 border !border-gray-300 rounded-lg px-3 py-2 h-10 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
          />
          <button
            class="bg-white border !border-gray-300 rounded-lg px-4 py-2 h-10 hover:bg-gray-50 transition-colors"
            @click="applyDiscount"
          >
            <span class="text-sm font-medium text-black"> Áp dụng </span>
          </button>
        </div>

        <p class="text-xs text-gray-600">VD: GIAMGIA10 (Giảm 10%)</p>
      </div>
    </div>

    <!-- Right Section: Payment Panel -->
    <div
      class="bg-white border-2 !border-[#7bf1a8] rounded-[14px] p-6 w-full max-w-[440px] flex flex-col gap-6 shadow-sm"
    >
      <!-- Header -->
      <div class="bg-green-50 rounded-lg flex items-center gap-2 py-2 px-3">
        <!-- <img :src="iconDollar" alt="Dollar" class="w-5 h-5" /> -->
        <h2 class="text-base font-semibold text-black">Thực hiện Thu tiền</h2>
      </div>

      <!-- Total Display -->
      <div class="bg-blue-50 border-2 !border-[#bedbff] rounded-[14px] p-4">
        <p class="text-sm text-gray-600 text-center mb-2">Tổng phải thu</p>
        <p class="text-3xl font-bold text-[#1447e6] text-center">
          {{ formatCurrency(total) }}
        </p>
      </div>

      <!-- Payment Method Selection -->
      <div class="flex flex-col gap-3">
        <h3 class="text-base font-medium text-black">Hình thức thanh toán:</h3>

        <div class="flex flex-col gap-2">
          <!-- Cash Option -->
          <button
            :class="[
              'border-2 rounded-[14px] p-4 h-16 flex items-center gap-3 transition-colors',
              paymentMethod === 'cash'
                ? 'border-[#00a63e] bg-green-50'
                : '!border-gray-300 bg-white hover:border-gray-400',
            ]"
            @click="paymentMethod = 'cash'"
          >
            <!-- <img :src="iconCash" alt="Cash" class="w-6 h-6" /> -->
            <span class="text-lg text-black"> Tiền mặt </span>
          </button>

          <!-- Transfer/QR Option -->
          <button
            :class="[
              'border-2 rounded-[14px] p-4 h-16 flex items-center gap-3 transition-colors',
              paymentMethod === 'transfer'
                ? 'border-[#155dfc] bg-blue-50'
                : '!border-gray-300 bg-white hover:border-gray-400',
            ]"
            @click="paymentMethod = 'transfer'"
          >
            <!-- <img :src="iconTransfer" alt="Transfer" class="w-6 h-6" /> -->
            <span class="text-lg text-black"> Chuyển khoản/QR </span>
          </button>
        </div>
      </div>

      <!-- Complete Button -->
      <button
        class="bg-[#00a63e] rounded-lg px-6 py-3 h-12 flex items-center justify-center gap-2 hover:bg-[#008833] transition-colors"
        @click="completePayment"
      >
        <!-- <img :src="iconCheck" alt="Check" class="w-4 h-4" /> -->
        <span class="text-lg font-medium text-white">
          Hoàn tất & In hóa đơn
        </span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";

// Props
const props = defineProps({
  invoiceId: {
    type: String,
    default: "HD001234",
  },
});

// Emits
const emit = defineEmits(["complete", "changeInvoice"]);

// Icons
const iconTag =
  "https://www.figma.com/api/mcp/asset/99f9f08c-4bea-4c5d-a237-67f68d19346a";
const iconDollar =
  "https://www.figma.com/api/mcp/asset/0c51f3cd-4c6a-4a32-bde0-240eb14a3413";
const iconCash =
  "https://www.figma.com/api/mcp/asset/1969b1d9-9c8e-4cbc-b783-f5b9823b4c70";
const iconTransfer =
  "https://www.figma.com/api/mcp/asset/66462652-3831-46d7-a563-49b4fa4eeddc";
const iconCheck =
  "https://www.figma.com/api/mcp/asset/b96c2bf7-7956-4d10-94ff-e7d27dabf6d5";

// State
const discountCode = ref("");
const paymentMethod = ref("cash");
const appliedDiscountCode = ref("");

// Invoice Data
const invoiceData = ref({
  invoiceId: "HD001234",
  date: "21/11/2024",
  customerName: "Nguyễn Văn A",
  petName: "Milo",
  items: [
    {
      name: "Khám tổng quát",
      quantity: 1,
      unitPrice: 150000,
      totalPrice: 150000,
    },
    {
      name: "Thuốc kháng sinh Amoxicillin",
      quantity: 1,
      unitPrice: 50000,
      totalPrice: 50000,
    },
    {
      name: "Dịch vụ Spa",
      quantity: 1,
      unitPrice: 200000,
      totalPrice: 200000,
    },
  ],
});

// Computed
const subtotal = computed(() => {
  return invoiceData.value.items.reduce(
    (sum, item) => sum + item.totalPrice,
    0
  );
});

const discount = computed(() => {
  if (appliedDiscountCode.value === "GIAMGIA10") {
    return subtotal.value * 0.1;
  }
  return 0;
});

const total = computed(() => {
  return subtotal.value - discount.value;
});

// Methods
const formatCurrency = (amount) => {
  return (
    new Intl.NumberFormat("vi-VN", {
      style: "decimal",
    }).format(amount) + " ₫"
  );
};

const applyDiscount = () => {
  const code = discountCode.value.trim().toUpperCase();

  if (code === "GIAMGIA10") {
    appliedDiscountCode.value = code;
    alert("Áp dụng mã giảm giá thành công! Giảm 10%");
  } else if (code === "") {
    alert("Vui lòng nhập mã giảm giá");
  } else {
    alert("Mã giảm giá không hợp lệ");
  }
};

const completePayment = () => {
  if (!paymentMethod.value) {
    alert("Vui lòng chọn hình thức thanh toán");
    return;
  }

  const paymentData = {
    invoiceId: invoiceData.value.invoiceId,
    subtotal: subtotal.value,
    discount: discount.value,
    total: total.value,
    paymentMethod: paymentMethod.value,
    discountCode: appliedDiscountCode.value,
  };

  console.log("Complete payment:", paymentData);
  emit("complete", paymentData);

  // TODO: Implement print invoice functionality
};

const changeInvoice = () => {
  emit("changeInvoice");
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}
</style>
