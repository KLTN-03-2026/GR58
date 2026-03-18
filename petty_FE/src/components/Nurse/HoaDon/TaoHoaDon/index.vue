<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-6"
    @click.self="closeModal"
  >
    <div
      class="bg-white rounded-[14px] shadow-lg w-full max-w-[1100px] max-h-[90vh] overflow-y-auto p-6 flex flex-col gap-6"
    >
      <!-- Header -->
      <div class="flex flex-col gap-1">
        <div class="flex items-center gap-2">
          <!-- <img :src="ICONS.receipt" alt="Receipt" class="w-5 h-5" /> -->
          <h2 class="text-lg font-semibold text-black">
            Tạo Hóa Đơn Mới / POS
          </h2>
        </div>
        <p class="text-sm text-gray-600">
          Bán hàng đa năng - Spa, Grooming, Thức ăn, Phụ kiện
        </p>
      </div>

      <!-- Main Content -->
      <div class="flex gap-6">
        <!-- Left Section: Product Selection -->
        <div class="flex flex-col gap-4 flex-1">
          <!-- Search Bar -->
          <div class="relative">
            <!-- <img :src="ICONS.search" alt="Search" class="absolute left-3 top-3.5 w-5 h-5" /> -->
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Tìm tên hàng, mã thuốc hoặc quét mã vạch..."
              class="w-full h-11 bg-gray-50 border !border-gray-300 rounded-lg pl-4 pr-12 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
            />
            <button class="absolute right-3 top-3 w-5 h-5">
              <!-- <img :src="ICONS.barcode" alt="Barcode" class="w-full h-full" /> -->
            </button>
          </div>

          <!-- Category Tabs -->
          <div class="bg-gray-100 rounded-[14px] p-1 flex gap-0.5">
            <button
              v-for="category in categories"
              :key="category.key"
              @click="activeCategory = category.key"
              class="flex-1 h-9 rounded-[14px] flex items-center justify-center gap-2 px-2 text-sm font-medium transition-colors"
              :class="
                activeCategory === category.key
                  ? 'bg-white text-black shadow-sm'
                  : 'text-gray-700'
              "
            >
              <!-- <img v-if="category.icon" :src="category.icon" alt="" class="w-4 h-4" /> -->
              <span>{{ category.label }}</span>
            </button>
          </div>

          <!-- Product Grid -->
          <div class="grid grid-cols-2 gap-3">
            <button
              v-for="product in filteredProducts"
              :key="product.id"
              @click="addToCart(product)"
              :disabled="product.stock === 0"
              class="border-2 !border-gray-300 rounded-[14px] h-[100px] p-4 flex flex-col justify-between text-left transition-colors hover:border-[#009689] disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
            >
              <h3 class="text-base font-bold text-black line-clamp-1">
                {{ product.name }}
              </h3>
              <div class="flex items-end justify-between">
                <p class="text-lg font-bold text-[#00a63e]">
                  {{ formatCurrency(product.price) }}
                </p>
                <div
                  class="px-2 py-0.5 rounded-lg text-xs font-medium flex items-center gap-1"
                  :class="getStockBadgeStyle(product)"
                >
                  <!-- <img :src="getStockIcon(product)" alt="" class="w-3 h-3" /> -->
                  <span>{{ getStockText(product) }}</span>
                </div>
              </div>
            </button>
          </div>
        </div>

        <!-- Right Section: Cart & Payment -->
        <div class="w-[427px] flex flex-col gap-4">
          <!-- Customer Input -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-black"> Khách hàng: </label>
            <input
              v-model="customerSearch"
              type="text"
              placeholder="Tìm khách hàng hoặc 'Khách lẻ'"
              class="h-10 bg-gray-50 border !border-gray-300 rounded-lg px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
            />
          </div>

          <!-- Payment Summary Card -->
          <div
            class="bg-white border !border-gray-300 rounded-[14px] p-4 flex flex-col gap-3 shadow-sm"
          >
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-600">Tạm tính:</span>
              <span class="text-black">{{ formatCurrency(subtotal) }}</span>
            </div>

            <!-- Discount Section -->
            <div class="border-t !border-gray-300 pt-3 flex flex-col gap-2">
              <div class="flex items-center justify-between">
                <label class="text-sm font-medium text-black">
                  Giảm giá / Chiết khấu:
                </label>
                <button class="w-4 h-4">
                  <!-- <img :src="ICONS.edit" alt="Edit" class="w-full h-full" /> -->
                </button>
              </div>
              <div class="flex gap-2">
                <input
                  v-model="discountValue"
                  type="number"
                  min="0"
                  placeholder="0"
                  class="flex-1 h-10 bg-gray-50 border !border-gray-300 rounded-lg px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
                />
                <button
                  @click="toggleDiscountType"
                  class="w-20 h-10 bg-gray-50 border !border-gray-300 rounded-lg flex items-center justify-between px-3"
                >
                  <!-- <img
                    v-if="discountType === 'percent'"
                    :src="ICONS.percent"
                    alt="Percent"
                    class="w-4 h-4"
                  /> -->
                  <span
                    v-if="discountType === 'percent'"
                    class="text-sm text-black"
                    >%</span
                  >
                  <span v-else class="text-sm text-black">đ</span>
                  <!-- <img :src="ICONS.chevronDown" alt="Toggle" class="w-4 h-4" /> -->
                </button>
              </div>
              <div
                v-if="discount > 0"
                class="flex items-center justify-between text-sm"
              >
                <span class="text-[#00a63e]">Giảm:</span>
                <span class="text-[#00a63e]"
                  >-{{ formatCurrency(discount) }}</span
                >
              </div>
            </div>

            <!-- Total -->
            <div
              class="border-t !border-gray-300 pt-3 flex items-center justify-between"
            >
              <h3 class="text-base font-bold text-black">KHÁCH PHẢI TRẢ:</h3>
              <p class="text-xl font-bold text-[#155dfc]">
                {{ formatCurrency(total) }}
              </p>
            </div>
          </div>

          <!-- Payment Method -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-black">
              Hình thức thanh toán:
            </label>
            <div class="flex flex-col gap-2">
              <button
                @click="paymentMethod = 'cash'"
                class="h-12 rounded-[14px] border-2 px-4 flex items-center gap-2 transition-colors"
                :class="
                  paymentMethod === 'cash'
                    ? 'bg-green-50 !border-[#00c950]'
                    : '!border-gray-300'
                "
              >
                <!-- <img :src="ICONS.cash" alt="Cash" class="w-5 h-5" /> -->
                <span class="text-base text-black">Tiền mặt</span>
              </button>
              <button
                @click="paymentMethod = 'transfer'"
                class="h-12 rounded-[14px] border-2 px-4 flex items-center gap-2 transition-colors"
                :class="
                  paymentMethod === 'transfer'
                    ? 'bg-blue-50 !border-[#155dfc]'
                    : '!border-gray-300'
                "
              >
                <!-- <img :src="ICONS.transfer" alt="Transfer" class="w-5 h-5" /> -->
                <span class="text-base text-black">Chuyển khoản/QR</span>
              </button>
            </div>
          </div>

          <!-- Payment Button -->
          <button
            @click="processPayment"
            :disabled="cart.length === 0 || !paymentMethod"
            class="h-12 bg-[#00a63e] rounded-lg flex items-center justify-center gap-2 text-sm font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <!-- <img :src="ICONS.check" alt="Check" class="w-4 h-4" /> -->
            <span>Thanh toán & In</span>
          </button>
        </div>
      </div>

      <!-- Cart Section -->
      <div
        class="bg-white border !border-gray-300 rounded-[14px] p-6 min-h-[152px] shadow-sm"
      >
        <h3 class="text-base font-medium text-black mb-4">Giỏ hàng</h3>
        <div
          v-if="cart.length === 0"
          class="flex items-center justify-center py-4"
        >
          <p class="text-sm text-gray-600">Chưa có sản phẩm nào</p>
        </div>
        <div v-else class="flex flex-wrap gap-3">
          <div
            v-for="item in cart"
            :key="item.id"
            class="bg-gray-50 border !border-gray-300 rounded-[14px] p-3 flex items-start gap-3 max-w-[400px]"
          >
            <div class="flex flex-col gap-0.5 flex-1">
              <p class="text-sm font-bold text-black">
                {{ item.quantity }}x {{ item.name }}
              </p>
              <p class="text-xs text-gray-600">
                {{ formatCurrency(item.price) }}
              </p>
            </div>
            <button @click="removeFromCart(item.id)" class="w-4 h-4 shrink-0">
              <!-- <img :src="ICONS.remove" alt="Remove" class="w-full h-full" /> -->
              <svg
                class="w-4 h-4 text-gray-600 hover:text-black"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";

// Icons from Figma (7-day expiry)
const ICONS = {
  receipt:
    "https://www.figma.com/api/mcp/asset/f46e0588-2a17-487d-b4aa-1022b27298fd",
  search:
    "https://www.figma.com/api/mcp/asset/e47f5665-8a3b-4878-abb5-a6aafca6733c",
  barcode:
    "https://www.figma.com/api/mcp/asset/15243d47-82bf-447b-8106-4e9b21fc1716",
  medicine:
    "https://www.figma.com/api/mcp/asset/6e53644e-43cf-4c12-a9a5-e51ac0017711",
  spa: "https://www.figma.com/api/mcp/asset/0dfea349-656f-499c-8cf3-afaf6b961f16",
  food: "https://www.figma.com/api/mcp/asset/7613dd80-3661-4f46-b4b9-c0e4177b702d",
  stockGreen:
    "https://www.figma.com/api/mcp/asset/00827afe-47a5-4ffc-b6db-b6636b604df1",
  stockRed:
    "https://www.figma.com/api/mcp/asset/024e8e88-0518-434b-b989-52977203bbac",
  stockAlert:
    "https://www.figma.com/api/mcp/asset/103ab930-d027-4f04-91cd-1cd291651273",
  service:
    "https://www.figma.com/api/mcp/asset/1e6adca5-beac-4f8c-889a-c64b074ffbbd",
  edit: "https://www.figma.com/api/mcp/asset/71c10355-5e88-4666-859b-511e4f840b44",
  percent:
    "https://www.figma.com/api/mcp/asset/14f967c7-7c0d-405a-b968-c422fe097bf9",
  chevronDown:
    "https://www.figma.com/api/mcp/asset/fc3c4c08-7a62-42c9-a491-d8632fb6ba24",
  cash: "https://www.figma.com/api/mcp/asset/a68998ff-8e2e-45c5-8f19-9b26b6875369",
  transfer:
    "https://www.figma.com/api/mcp/asset/77bcb1d6-b2de-4fa9-b452-84e32eb0bb72",
  check:
    "https://www.figma.com/api/mcp/asset/1393d7eb-2590-4e49-a301-384efe23dc2f",
  remove:
    "https://www.figma.com/api/mcp/asset/adc6c9fc-3341-40a4-8cf0-977df21fe328",
};

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
});

// Emits
const emit = defineEmits(["close", "complete"]);

// State
const searchQuery = ref("");
const customerSearch = ref("");
const activeCategory = ref("all");
const cart = ref([]);
const discountValue = ref(0);
const discountType = ref("percent"); // 'percent' or 'amount'
const paymentMethod = ref("cash");

// Categories
const categories = [
  { key: "all", label: "Tất cả", icon: null },
  { key: "medicine", label: "Thuốc", icon: ICONS.medicine },
  { key: "spa", label: "Spa/Grooming", icon: ICONS.spa },
  { key: "food", label: "Thức ăn", icon: ICONS.food },
];

// Sample products (replace with API call)
const products = ref([
  {
    id: 1,
    name: "Thuốc kháng sinh Amoxicillin",
    price: 50000,
    category: "medicine",
    stock: 12,
    type: "product",
  },
  {
    id: 2,
    name: "Vitamin tổng hợp",
    price: 80000,
    category: "medicine",
    stock: 3,
    type: "product",
  },
  {
    id: 3,
    name: "Thuốc tẩy giun",
    price: 120000,
    category: "medicine",
    stock: 0,
    type: "product",
  },
  {
    id: 4,
    name: "Cắt tỉa lông",
    price: 300000,
    category: "spa",
    stock: null,
    type: "service",
  },
  {
    id: 5,
    name: "Tắm vệ sinh cơ bản",
    price: 150000,
    category: "spa",
    stock: null,
    type: "service",
  },
  {
    id: 6,
    name: "Spa cao cấp (tắm + cắt + nhuộm)",
    price: 500000,
    category: "spa",
    stock: null,
    type: "service",
  },
  {
    id: 7,
    name: "Pate Royal Canin",
    price: 40000,
    category: "food",
    stock: 15,
    type: "product",
  },
  {
    id: 8,
    name: "Hạt Smartheart 1kg",
    price: 85000,
    category: "food",
    stock: 8,
    type: "product",
  },
  {
    id: 9,
    name: "Xương gặm cho chó",
    price: 35000,
    category: "food",
    stock: 20,
    type: "product",
  },
]);

// Computed
const filteredProducts = computed(() => {
  let filtered = products.value;

  // Filter by category
  if (activeCategory.value !== "all") {
    filtered = filtered.filter((p) => p.category === activeCategory.value);
  }

  // Filter by search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter((p) => p.name.toLowerCase().includes(query));
  }

  return filtered;
});

const subtotal = computed(() => {
  return cart.value.reduce((sum, item) => sum + item.price * item.quantity, 0);
});

const discount = computed(() => {
  if (!discountValue.value) return 0;

  if (discountType.value === "percent") {
    return Math.round((subtotal.value * discountValue.value) / 100);
  } else {
    return Math.min(discountValue.value, subtotal.value);
  }
});

const total = computed(() => {
  return Math.max(0, subtotal.value - discount.value);
});

// Methods
const closeModal = () => {
  emit("close");
};

const addToCart = (product) => {
  if (product.stock === 0) return;

  const existingItem = cart.value.find((item) => item.id === product.id);

  if (existingItem) {
    existingItem.quantity++;
  } else {
    cart.value.push({
      id: product.id,
      name: product.name,
      price: product.price,
      quantity: 1,
      type: product.type,
    });
  }
};

const removeFromCart = (productId) => {
  const index = cart.value.findIndex((item) => item.id === productId);
  if (index !== -1) {
    cart.value.splice(index, 1);
  }
};

const toggleDiscountType = () => {
  discountType.value = discountType.value === "percent" ? "amount" : "percent";
  discountValue.value = 0;
};

const getStockBadgeStyle = (product) => {
  if (product.type === "service") {
    return "bg-purple-100 text-[#8200db]";
  }
  if (product.stock === 0) {
    return "bg-[#ffe2e2] text-[#c10007]";
  }
  if (product.stock <= 5) {
    return "bg-[#ffe2e2] text-[#c10007]";
  }
  return "bg-green-100 text-[#008236]";
};

const getStockIcon = (product) => {
  if (product.type === "service") return ICONS.service;
  if (product.stock === 0) return ICONS.stockAlert;
  if (product.stock <= 5) return ICONS.stockRed;
  return ICONS.stockGreen;
};

const getStockText = (product) => {
  if (product.type === "service") return "Dịch vụ";
  if (product.stock === 0) return "Hết hàng";
  if (product.stock <= 5) return `Còn: ${product.stock}`;
  return `Tồn: ${product.stock}`;
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(amount);
};

const processPayment = () => {
  if (cart.value.length === 0 || !paymentMethod.value) return;

  const invoiceData = {
    customer: customerSearch.value || "Khách lẻ",
    items: cart.value,
    subtotal: subtotal.value,
    discount: discount.value,
    total: total.value,
    paymentMethod: paymentMethod.value,
    timestamp: new Date().toISOString(),
  };

  emit("complete", invoiceData);

  // Reset form
  cart.value = [];
  customerSearch.value = "";
  discountValue.value = 0;
  searchQuery.value = "";
};

// TODO: Integrate with API to fetch products and stock levels
// TODO: Implement barcode scanner functionality
// TODO: Add customer search with autocomplete
// TODO: Print invoice after payment
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700;800&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}

/* Hide number input arrows */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
  appearance: textfield;
}
</style>
