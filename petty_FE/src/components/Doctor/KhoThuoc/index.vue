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
        <button
          class="h-10 px-4 bg-gray-50 border !border-gray-300 rounded-lg flex items-center justify-between gap-2 hover:bg-gray-100 transition-colors min-w-[120px]"
        >
          <span class="text-sm font-normal text-gray-900">Tất cả</span>
          <!-- <img :src="icons.chevronDown" alt="Dropdown" class="w-4 h-4" /> -->
          <span class="text-gray-500">▼</span>
        </button>
      </div>
    </div>

    <!-- Inventory Table -->
    <div
      class="bg-white border !border-gray-300 rounded-[14px] px-6 py-6 shadow-sm"
    >
      <!-- Table Title -->
      <div class="flex items-center gap-2 mb-6">
        <h2 class="text-base font-semibold text-black">
          Danh sách tồn kho ({{ inventoryItems.length }} sản phẩm)
        </h2>
      </div>

      <!-- Table -->
      <div class="overflow-auto custom-scrollbar" style="max-height: 467.5px">
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
              v-for="item in inventoryItems"
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
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const searchQuery = ref("");

const icons = {
  info: "https://www.figma.com/api/mcp/asset/af6dc119-7098-4ec1-87b8-9af190156b33",
  search:
    "https://www.figma.com/api/mcp/asset/3fc8bc03-d607-4d46-b3c0-1f894a7be67f",
  chevronDown:
    "https://www.figma.com/api/mcp/asset/e99ac39b-0d39-44de-be08-b2df27d5d961",
  alertTriangle:
    "https://www.figma.com/api/mcp/asset/23ab8af5-f3be-4bbf-95ca-35f771c1d7b2",
};

const inventoryItems = ref([
  {
    id: 1,
    name: "Vắc-xin Rabies (Nobivac)",
    category: "Vắc-xin",
    quantity: "25 Lọ",
    minQuantity: "10",
    stockStatus: "in-stock",
    stockLabel: "Còn hàng",
    expiryDate: "15/06/2025",
    expiryWarning: false,
    hasWarning: false,
  },
  {
    id: 2,
    name: "Kháng sinh Cephalexin 500mg",
    category: "Thuốc",
    quantity: "150 Viên",
    minQuantity: "50",
    stockStatus: "in-stock",
    stockLabel: "Còn hàng",
    expiryDate: "20/12/2025",
    expiryWarning: true,
    hasWarning: false,
  },
  {
    id: 3,
    name: "Thuốc tẩy giun Drontal",
    category: "Thuốc",
    quantity: "8 Viên",
    minQuantity: "10",
    stockStatus: "low-stock",
    stockLabel: "Sắp hết",
    expiryDate: "10/08/2025",
    expiryWarning: false,
    hasWarning: false,
  },
  {
    id: 4,
    name: "Vitamin tổng hợp ABC",
    category: "Thuốc",
    quantity: "3 Hộp",
    minQuantity: "5",
    stockStatus: "low-stock",
    stockLabel: "Sắp hết",
    expiryDate: "25/12/2024",
    expiryWarning: false,
    hasWarning: false,
  },
  {
    id: 5,
    name: "Vắc-xin 6 bệnh",
    category: "Vắc-xin",
    quantity: "18 Lọ",
    minQuantity: "10",
    stockStatus: "in-stock",
    stockLabel: "Còn hàng",
    expiryDate: "30/09/2025",
    expiryWarning: false,
    hasWarning: false,
  },
  {
    id: 6,
    name: "Thuốc chống viêm Meloxicam",
    category: "Thuốc",
    quantity: "0 Viên",
    minQuantity: "20",
    stockStatus: "out-of-stock",
    stockLabel: "Hết hàng",
    expiryDate: null,
    expiryWarning: false,
    hasWarning: true,
  },
  {
    id: 7,
    name: "Băng gạc vô trùng",
    category: "Vật tư",
    quantity: "50 Cuộn",
    minQuantity: "20",
    stockStatus: "in-stock",
    stockLabel: "Còn hàng",
    expiryDate: null,
    expiryWarning: false,
    hasWarning: false,
  },
  {
    id: 8,
    name: "Ống tiêm 5ml",
    category: "Vật tư",
    quantity: "200 Cái",
    minQuantity: "100",
    stockStatus: "in-stock",
    stockLabel: "Còn hàng",
    expiryDate: null,
    expiryWarning: false,
    hasWarning: false,
  },
]);
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
