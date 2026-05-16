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
          <div class="flex flex-col gap-2 relative">
            <label class="text-sm font-medium text-black"> Khách hàng: </label>
            <input
              v-model="customerSearch"
              type="text"
              placeholder="Tìm khách hàng hoặc 'Khách lẻ'"
              class="h-10 bg-gray-50 border !border-gray-300 rounded-lg px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
              @input="handleCustomerSearch"
            />
            <div
              v-if="customerResults.length > 0"
              class="absolute top-[68px] z-10 w-full bg-white border !border-gray-300 rounded-lg shadow-lg max-h-40 overflow-y-auto"
            >
              <button
                v-for="c in customerResults"
                :key="c.id"
                class="w-full px-3 py-2 text-left hover:bg-gray-50 border-b !border-gray-100 last:border-b-0"
                @click="selectCustomer(c)"
              >
                <p class="text-sm font-medium text-black">{{ c.full_name }}</p>
                <p class="text-xs text-gray-500">{{ c.so_dien_thoai || c.phone }}</p>
              </button>
            </div>
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

          <!-- Error Message -->
          <div v-if="errorMessage" class="bg-red-50 border !border-red-300 rounded-lg p-3">
            <p class="text-sm text-red-600">{{ errorMessage }}</p>
          </div>

          <!-- Payment Button -->
          <button
            @click="processPayment"
            :disabled="cart.length === 0 || !paymentMethod || submitting"
            class="h-12 bg-[#00a63e] rounded-lg flex items-center justify-center gap-2 text-sm font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span>{{ submitting ? 'Đang xử lý...' : 'Thanh toán & In' }}</span>
          </button>

          <!-- QR Modal -->
          <div v-if="qrModal && qrData" class="bg-blue-50 border !border-blue-300 rounded-lg p-4 text-center">
            <p class="text-sm font-medium text-black mb-2">Quét mã QR để thanh toán</p>
            <img v-if="qrData.qr_url" :src="qrData.qr_url" alt="QR Code" class="w-48 h-48 mx-auto border rounded" />
            <p class="text-xs text-gray-600 mt-2">Đang chờ xác nhận thanh toán...</p>
            <button @click="qrModal = false; stopPolling()" class="mt-2 text-xs text-red-500 hover:text-red-700">Huỷ</button>
          </div>
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
import { ref, computed, watch } from "vue";
import api from "@/utils/api";
import { dichVuService } from "@/services/dichVuService";
import { sepayService } from "@/services/sepayService";

// Props
const props = defineProps({
  isOpen: { type: Boolean, default: false },
  lichHenId: { type: [Number, String], default: null },
});

// Emits
const emit = defineEmits(["close", "complete"]);

// State
const searchQuery = ref("");
const customerSearch = ref("");
const customerResults = ref([]);
const selectedCustomer = ref(null);
const activeCategory = ref("all");
const cart = ref([]);
const discountValue = ref(0);
const discountType = ref("percent");
const paymentMethod = ref("cash");
const submitting = ref(false);
const errorMessage = ref("");
const qrModal = ref(false);
const qrData = ref(null);
const pollInterval = ref(null);

// Categories
const categories = [
  { key: "all", label: "Tất cả", icon: null },
  { key: "medicine", label: "Thuốc", icon: null },
  { key: "spa", label: "Spa/Grooming", icon: null },
  { key: "food", label: "Thức ăn", icon: null },
];

const products = ref([]);

const loadProducts = async () => {
  try {
    const [goodsRes, servicesRes] = await Promise.all([
      api.get("/hang-hoa", { params: { per_page: 100 } }),
      dichVuService.getAll({ per_page: 100 }),
    ]);

    const goods = (goodsRes.data?.data?.data || goodsRes.data?.data || []).map((item) => ({
      id: `goods-${item.id}`,
      realId: item.id,
      name: item.ten_hang_hoa || item.ten,
      price: item.gia_ban || item.gia_tien || 0,
      category: mapGoodsCategory(item.danh_muc_hang_hoa_id || item.danh_muc?.id),
      stock: item.ton_kho ?? item.so_luong ?? 0,
      type: "product",
    }));

    const services = (servicesRes.data || servicesRes.data?.data || []).map((item) => {
      const svc = Array.isArray(item) ? item : item;
      return {
        id: `service-${svc.id}`,
        realId: svc.id,
        name: svc.ten_dich_vu || svc.ten,
        price: svc.gia_tien || svc.gia || 0,
        category: "spa",
        stock: null,
        type: "service",
      };
    });

    products.value = [...goods, ...services];
  } catch (error) {
    console.error("Failed to load products:", error);
  }
};

const mapGoodsCategory = (categoryId) => {
  // Map category IDs to POS categories — basic heuristic
  return "medicine";
};

watch(() => props.isOpen, (val) => {
  if (val) {
    if (products.value.length === 0) loadProducts();
    errorMessage.value = "";
  } else {
    stopPolling();
  }
});

let searchTimeout = null;
const handleCustomerSearch = () => {
  clearTimeout(searchTimeout);
  if (customerSearch.value.length < 2) {
    customerResults.value = [];
    return;
  }
  searchTimeout = setTimeout(async () => {
    try {
      const res = await api.get("/khach-hang", {
        params: { search: customerSearch.value, per_page: 5 },
      });
      customerResults.value = res.data?.data?.data || res.data?.data || [];
    } catch {
      customerResults.value = [];
    }
  }, 300);
};

const selectCustomer = (c) => {
  selectedCustomer.value = c;
  customerSearch.value = c.full_name;
  customerResults.value = [];
};

// Computed
const filteredProducts = computed(() => {
  let filtered = products.value;

  if (activeCategory.value !== "all") {
    filtered = filtered.filter((p) => p.category === activeCategory.value);
  }

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
  }
  return Math.min(discountValue.value, subtotal.value);
});

const total = computed(() => {
  return Math.max(0, subtotal.value - discount.value);
});

// Methods
const closeModal = () => {
  stopPolling();
  emit("close");
};

const addToCart = (product) => {
  if (product.stock === 0) return;
  const existing = cart.value.find((item) => item.id === product.id);
  if (existing) {
    existing.quantity++;
  } else {
    cart.value.push({ id: product.id, name: product.name, price: product.price, quantity: 1, type: product.type });
  }
};

const removeFromCart = (productId) => {
  const idx = cart.value.findIndex((item) => item.id === productId);
  if (idx !== -1) cart.value.splice(idx, 1);
};

const toggleDiscountType = () => {
  discountType.value = discountType.value === "percent" ? "amount" : "percent";
  discountValue.value = 0;
};

const getStockBadgeStyle = (product) => {
  if (product.type === "service") return "bg-purple-100 text-[#8200db]";
  if (product.stock === 0) return "bg-[#ffe2e2] text-[#c10007]";
  if (product.stock <= 5) return "bg-[#ffe2e2] text-[#c10007]";
  return "bg-green-100 text-[#008236]";
};

const getStockText = (product) => {
  if (product.type === "service") return "Dịch vụ";
  if (product.stock === 0) return "Hết hàng";
  if (product.stock <= 5) return `Còn: ${product.stock}`;
  return `Tồn: ${product.stock}`;
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(amount);
};

const processPayment = async () => {
  if (cart.value.length === 0 || !paymentMethod.value) return;
  errorMessage.value = "";

  if (!props.lichHenId) {
    errorMessage.value = "POS hiện chỉ hỗ trợ thanh toán cho lịch hẹn đã hoàn thành. Vui lòng chọn từ danh sách chờ thanh toán.";
    return;
  }

  submitting.value = true;
  try {
    if (paymentMethod.value === "cash") {
      const res = await api.post("/thanh-toan", {
        lich_hen_id: props.lichHenId,
        hinh_thuc_thanh_toan: "tien_mat",
        tien_mat: total.value,
        ma_giam_gia: null,
        ghi_chu: `POS - ${cart.value.map(i => i.name).join(", ")}`,
      });
      if (res.data?.status) {
        emit("complete", res.data);
        resetForm();
      } else {
        errorMessage.value = res.data?.message || "Thanh toán thất bại";
      }
    } else {
      // QR/Bank transfer
      const res = await sepayService.createPayment(props.lichHenId, `POS - ${cart.value.map(i => i.name).join(", ")}`);
      if (res.status && res.data) {
        qrData.value = res.data;
        qrModal.value = true;
        startPolling(res.data.id || res.data.thanh_toan_id);
      } else {
        errorMessage.value = res.message || "Không thể tạo mã QR";
      }
    }
  } catch (error) {
    errorMessage.value = error.response?.data?.message || "Có lỗi xảy ra";
  } finally {
    submitting.value = false;
  }
};

const startPolling = (thanhToanId) => {
  pollInterval.value = setInterval(async () => {
    try {
      const res = await sepayService.checkStatus(thanhToanId);
      if (res.status && res.data?.trang_thai === "da_thanh_toan") {
        stopPolling();
        qrModal.value = false;
        emit("complete", res.data);
        resetForm();
      }
    } catch {
      // Ignore polling errors
    }
  }, 3000);
};

const stopPolling = () => {
  if (pollInterval.value) {
    clearInterval(pollInterval.value);
    pollInterval.value = null;
  }
};

const resetForm = () => {
  cart.value = [];
  customerSearch.value = "";
  selectedCustomer.value = null;
  discountValue.value = 0;
  searchQuery.value = "";
  errorMessage.value = "";
  qrModal.value = false;
  qrData.value = null;
};
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
