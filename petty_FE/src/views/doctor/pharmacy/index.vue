<template>
  <div class="relative w-full h-full px-8 py-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-1 mb-6">
      <h1 class="text-2xl font-semibold text-black">Kho thuốc & Vật tư</h1>
      <p class="text-base font-medium text-gray-500">
        Tra cứu tồn kho để kê đơn (Chỉ xem - Không chỉnh sửa)
      </p>
    </div>

    <!-- Info Card -->
    <div class="bg-blue-50 border-2 !border-[#bedbff] rounded-[14px] p-4 mb-6">
      <div class="flex gap-3">
        <!-- <img :src="icons.info" alt="Info" class="w-5 h-5 mt-0.5 shrink-0" /> -->
        <div class="flex flex-col gap-1">
          <p class="text-sm text-[#1c398e] leading-5 tracking-[-0.1504px]">
            <span class="font-bold">Lưu ý:</span>
            <span class="font-normal">
              Bạn chỉ có quyền xem thông tin tồn kho để kê đơn cho bệnh
              nhân.</span
            >
          </p>
          <p
            class="text-sm font-normal text-[#193cb8] leading-5 tracking-[-0.1504px]"
          >
            Nếu thuốc hết hàng hoặc sắp hết, vui lòng thông báo cho Quản lý kho
            để nhập thêm.
          </p>
        </div>
      </div>
    </div>

    <!-- Search & Filter Bar -->
    <div
      class="bg-white border !border-gray-300 rounded-[14px] px-6 py-4 mb-6 shadow-sm"
    >
      <div class="flex items-center justify-between gap-4">
        <!-- Search Input -->
        <div class="relative flex-1">
          <!-- <img :src="icons.search" alt="Search" class="absolute left-3 top-2.5 w-4 h-4" /> -->
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Tìm kiếm thuốc, vắc-xin..."
            class="w-full h-10 bg-gray-50 border !border-gray-300 rounded-lg pl-4 pr-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
          />
        </div>

        <!-- Category Filter -->
        <select
          v-model="selectedCategory"
          class="h-10 px-4 bg-gray-50 border !border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors min-w-[160px]"
        >
          <option value="">Tất cả</option>
          <option
            v-for="category in categories"
            :key="category"
            :value="category"
          >
            {{ category }}
          </option>
        </select>
      </div>
    </div>

    <!-- Inventory Table -->
    <div
      class="bg-white border !border-gray-300 rounded-[14px] px-6 py-6 shadow-sm"
    >
      <!-- Table Title -->
      <div class="flex items-center gap-2 mb-6">
        <h2 class="text-base font-semibold text-black">
          Danh sách tồn kho ({{ filteredInventoryItems.length }} sản phẩm)
        </h2>
      </div>

      <div v-if="loading" class="py-10 text-center text-gray-500">
        Đang tải dữ liệu tồn kho...
      </div>
      <div v-else-if="error" class="py-10 text-center text-red-600">
        {{ error }}
      </div>

      <!-- Table -->
      <div
        v-else-if="filteredInventoryItems.length"
        class="overflow-auto custom-scrollbar"
        style="max-height: 467.5px"
      >
        <table class="w-full">
          <thead>
            <tr class="border-b border-[rgba(0,0,0,0.1)]">
              <th
                class="text-left px-2 py-[10.25px] text-sm font-medium text-black tracking-[-0.1504px]"
              >
                Tên hàng
              </th>
              <th
                class="text-left px-2 py-[10.25px] text-sm font-medium text-black tracking-[-0.1504px]"
              >
                Danh mục
              </th>
              <th
                class="text-right px-2 py-[10.25px] text-sm font-medium text-black tracking-[-0.1504px]"
              >
                Tồn kho
              </th>
              <th
                class="text-center px-2 py-[10.25px] text-sm font-medium text-black tracking-[-0.1504px]"
              >
                Trạng thái
              </th>
              <th
                class="text-left px-2 py-[10.25px] text-sm font-medium text-black tracking-[-0.1504px]"
              >
                Hạn sử dụng
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in filteredInventoryItems"
              :key="item.id"
              :class="[
                'border-b !border-gray-300',
                item.stockStatus === 'out-of-stock' ? 'bg-red-50' : '',
                item.stockStatus === 'low-stock' ? 'bg-orange-50' : '',
              ]"
            >
              <!-- Product Name -->
              <td class="px-2 py-4">
                <div class="flex items-center gap-2">
                  <p
                    class="text-sm font-normal text-black leading-5 tracking-[-0.1504px]"
                  >
                    {{ item.name }}
                  </p>
                  <!-- <img v-if="item.hasWarning" :src="icons.alertTriangle" alt="Warning" class="w-4 h-4" /> -->
                </div>
              </td>

              <!-- Category -->
              <td class="px-2 py-4">
                <span
                  class="inline-block px-2 py-0.5 border !border-gray-300 rounded-lg text-xs font-medium text-black leading-4"
                >
                  {{ item.category }}
                </span>
              </td>

              <!-- Stock -->
              <td class="px-2 py-4 text-right">
                <p
                  class="text-sm font-normal leading-5 tracking-[-0.1504px]"
                  :class="[
                    item.stockStatus === 'out-of-stock' ? 'text-[#e7000b]' : '',
                    item.stockStatus === 'low-stock'
                      ? 'text-[#f54900]'
                      : 'text-black',
                  ]"
                >
                  {{ item.quantity }}
                </p>
                <p class="text-xs font-normal text-gray-500 leading-4 mt-0.5">
                  Tối thiểu: {{ item.minQuantity }}
                </p>
              </td>

              <!-- Status Badge -->
              <td class="px-2 py-4 text-center">
                <span
                  class="inline-block px-2 py-0.5 rounded-lg text-xs font-medium leading-4"
                  :class="[
                    item.stockStatus === 'in-stock'
                      ? 'bg-green-100 text-[#008236] border-0'
                      : '',
                    item.stockStatus === 'low-stock'
                      ? 'bg-[#ffedd4] text-[#ca3500] border-0'
                      : '',
                    item.stockStatus === 'out-of-stock'
                      ? 'bg-[#ffe2e2] text-[#c10007] border-0'
                      : '',
                  ]"
                >
                  {{ item.stockLabel }}
                </span>
              </td>

              <!-- Expiry Date -->
              <td class="px-2 py-4">
                <div v-if="item.expiryDate">
                  <p
                    class="text-sm font-normal leading-5 tracking-[-0.1504px]"
                    :class="
                      item.expiryWarning ? 'text-[#f54900]' : 'text-black'
                    "
                  >
                    {{ item.expiryDate }}
                  </p>
                  <p
                    v-if="item.expiryWarning"
                    class="text-xs font-normal text-[#f54900] leading-4 mt-1"
                  >
                    Sắp hết hạn
                  </p>
                </div>
                <p
                  v-else
                  class="text-sm font-normal text-gray-500 leading-5 tracking-[-0.1504px]"
                >
                  -
                </p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="py-10 text-center text-gray-500">
        Không tìm thấy thuốc hoặc vật tư phù hợp.
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { listHangHoa } from "@/utils/hangHoa";

const searchQuery = ref("");
const selectedCategory = ref("");
const loading = ref(false);
const error = ref(null);
const inventoryItems = ref([]);

const icons = {
  info: "https://www.figma.com/api/mcp/asset/af6dc119-7098-4ec1-87b8-9af190156b33",
  search:
    "https://www.figma.com/api/mcp/asset/3fc8bc03-d607-4d46-b3c0-1f894a7be67f",
  chevronDown:
    "https://www.figma.com/api/mcp/asset/e99ac39b-0d39-44de-be08-b2df27d5d961",
  alertTriangle:
    "https://www.figma.com/api/mcp/asset/23ab8af5-f3be-4bbf-95ca-35f771c1d7b2",
};

const formatDate = (value) => {
  if (!value) return null;
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return null;
  return date.toLocaleDateString("vi-VN");
};

const isExpiringSoon = (value) => {
  if (!value) return false;
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return false;
  const daysLeft = Math.ceil((date.getTime() - Date.now()) / 86400000);
  return daysLeft >= 0 && daysLeft <= 60;
};

const resolveStockStatus = (quantity, minQuantity) => {
  if (quantity <= 0) {
    return { stockStatus: "out-of-stock", stockLabel: "Hết hàng" };
  }
  if (quantity <= minQuantity) {
    return { stockStatus: "low-stock", stockLabel: "Sắp hết" };
  }
  return { stockStatus: "in-stock", stockLabel: "Còn hàng" };
};

const mapInventoryItem = (item) => {
  const quantity = Number(item.tong_so_luong_nhap ?? item.so_luong ?? 0);
  const minQuantity = Number(item.dinh_muc_toi_thieu ?? 0);
  const latestBatch = item.chi_tiet_nhap_kho_gan_nhat?.[0] || null;
  const stock = resolveStockStatus(quantity, minQuantity);

  return {
    id: item.id,
    name: item.ten_mat_hang || item.ten || item.ma_hang_hoa || "Chưa có tên",
    category: item.ten_danh_muc_hang_hoa || item.danh_muc?.ten_danh_muc_hang_hoa || "Chưa phân loại",
    quantity: `${quantity} ${item.don_vi_tinh || ""}`.trim(),
    minQuantity: `${minQuantity} ${item.don_vi_tinh || ""}`.trim(),
    expiryDate: formatDate(latestBatch?.han_su_dung),
    expiryWarning: isExpiringSoon(latestBatch?.han_su_dung),
    hasWarning: stock.stockStatus !== "in-stock" || isExpiringSoon(latestBatch?.han_su_dung),
    ...stock,
  };
};

const categories = computed(() => {
  return [...new Set(inventoryItems.value.map((item) => item.category))].sort();
});

const filteredInventoryItems = computed(() => {
  const keyword = searchQuery.value.trim().toLowerCase();
  return inventoryItems.value.filter((item) => {
    const matchesKeyword =
      !keyword ||
      item.name.toLowerCase().includes(keyword) ||
      item.category.toLowerCase().includes(keyword);
    const matchesCategory =
      !selectedCategory.value || item.category === selectedCategory.value;
    return matchesKeyword && matchesCategory;
  });
});

const loadInventory = async () => {
  loading.value = true;
  error.value = null;
  try {
    const data = await listHangHoa();
    inventoryItems.value = data.map(mapInventoryItem);
  } catch (err) {
    console.error("Doctor pharmacy load error:", err);
    error.value =
      err.response?.data?.message || "Không thể tải dữ liệu kho thuốc.";
  } finally {
    loading.value = false;
  }
};

onMounted(loadInventory);
</script>

<style scoped>
/* Custom scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f3f3f5;
  border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #d1d1d6;
  border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #a8a8b0;
}

/* Remove default input styling */
input:focus {
  outline: none;
}

/* Font imports */
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap");
</style>
