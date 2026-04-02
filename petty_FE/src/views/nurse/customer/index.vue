<template>
  <div class="relative w-full h-full px-8 py-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-1 mb-6">
      <h1 class="text-2xl font-semibold text-black">Quản lý Khách hàng</h1>
      <p class="text-base font-medium text-gray-500">
        Tra cứu thông tin, lịch sử và quản lý quan hệ khách hàng
      </p>
    </div>

    <!-- Search and Filters Card -->
    <div
      class="bg-white border !border-gray-300 rounded-[14px] p-4 shadow-sm mb-6"
    >
      <div class="flex flex-col gap-4">
        <!-- Search Input -->
        <div class="relative">
          <!-- <img
            :src="iconSearch"
            alt="Search"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 z-10"
          /> -->
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Tìm theo Tên, SĐT, hoặc Tên Thú cưng..."
            class="w-full h-11 bg-gray-50 border !border-gray-300 rounded-lg px-4 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
          />
        </div>

        <!-- Filters and Create Button -->
        <div class="flex items-center justify-between">
          <!-- Filter Buttons -->
          <div class="flex items-center gap-3">
            <span class="text-sm text-gray-600"> Lọc nhanh: </span>
            <button
              v-for="filter in filters"
              :key="filter.key"
              :class="[
                'h-9 rounded-[10px] px-4 transition-colors',
                activeFilter === filter.key
                  ? 'bg-[#155dfc] text-white'
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
              ]"
              @click="activeFilter = filter.key"
            >
              <span class="text-sm">
                {{ filter.label }}
              </span>
            </button>
          </div>

          <!-- Create Customer Button -->
          <button
            class="bg-[#9810fa] rounded-lg px-4 py-2.5 h-10 flex items-center gap-2 hover:bg-[#7a0cc9] transition-colors"
            @click="createCustomer"
          >
            <!-- <img :src="iconPlus" alt="Create" class="w-4 h-4" /> -->
            <span class="text-sm font-medium text-white"> Tạo khách mới </span>
          </button>
        </div>
      </div>
    </div>

    <!-- Customer List Card -->
    <div class="bg-white border !border-gray-300 rounded-[14px] p-6 shadow-sm">
      <!-- Card Title -->
      <h2 class="text-base font-semibold text-black mb-6">
        Danh sách khách hàng - {{ filteredCustomers.length }} khách
      </h2>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <!-- Table Header -->
          <thead>
            <tr class="border-b !border-gray-300">
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Khách hàng
              </th>
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Liên hệ
              </th>
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Hạng thành viên
              </th>
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Thú cưng
              </th>
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Lần cuối đến
              </th>
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Thao tác
              </th>
            </tr>
          </thead>

          <!-- Table Body -->
          <tbody>
            <tr
              v-for="(customer, index) in filteredCustomers"
              :key="index"
              class="border-b !border-gray-300"
            >
              <!-- Customer Name -->
              <td class="px-2 py-4">
                <p class="text-base font-bold text-black">
                  {{ customer.name }}
                </p>
              </td>

              <!-- Contact -->
              <td class="px-2 py-2">
                <div class="flex flex-col gap-1">
                  <div class="flex items-center gap-2">
                    <!-- <img :src="iconPhone" alt="Phone" class="w-4 h-4" /> -->
                    <span class="text-sm text-black">
                      {{ customer.phone }}
                    </span>
                  </div>
                  <p v-if="customer.hasZalo" class="text-xs text-[#00a63e]">
                    ✓ Zalo đã kết nối
                  </p>
                </div>
              </td>

              <!-- Membership Tier -->
              <td class="px-2 py-4">
                <span
                  :class="[
                    'inline-block rounded-lg px-2 py-1 border',
                    customer.tier === 'gold'
                      ? 'bg-[#fef9c2] text-[#a65f00] border-transparent'
                      : customer.tier === 'diamond'
                      ? 'bg-purple-100 text-[#8200db] border-transparent'
                      : customer.tier === 'silver'
                      ? 'bg-gray-100 text-gray-700 border-transparent'
                      : 'bg-gray-100 text-gray-600 border-transparent',
                  ]"
                >
                  <span class="text-xs font-medium">
                    {{
                      customer.tier === "gold"
                        ? "Vàng"
                        : customer.tier === "diamond"
                        ? "Kim cương"
                        : customer.tier === "silver"
                        ? "Bạc"
                        : "Thường"
                    }}
                  </span>
                </span>
              </td>

              <!-- Pets -->
              <td class="px-2 py-3">
                <div class="flex items-center gap-2">
                  <div
                    v-for="(pet, petIndex) in customer.pets"
                    :key="petIndex"
                    class="flex items-center gap-1"
                  >
                    <img
                      v-if="pet.image"
                      :src="pet.image"
                      :alt="pet.name"
                      class="w-8 h-8 rounded-full object-cover"
                    />
                    <span v-else class="text-lg">{{ pet.emoji }}</span>
                    <span class="text-sm text-gray-700">
                      {{ pet.name }}
                    </span>
                  </div>
                </div>
              </td>

              <!-- Last Visit -->
              <td class="px-2 py-2.5">
                <div class="flex flex-col">
                  <p class="text-sm text-black">
                    {{ customer.lastVisit }}
                  </p>
                  <p class="text-xs text-gray-600">
                    {{ customer.lastVisitRelative }}
                  </p>
                </div>
              </td>

              <!-- Actions -->
              <td class="px-2 py-3">
                <button
                  class="bg-white border !border-gray-300 rounded-lg h-10 px-3 flex items-center gap-2 hover:bg-gray-50 transition-colors"
                  @click="editCustomer(customer)"
                >
                  <!-- <img :src="iconEdit" alt="Edit" class="w-4 h-4" /> -->
                  <span class="text-sm font-medium text-black"> Sửa </span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- Create Customer Modal -->
    <CreateCustomerModal
      :is-open="isCreateCustomerModalOpen"
      @close="isCreateCustomerModalOpen = false"
      @submit="handleCreateCustomerSubmit"
    />

    <!-- Edit Customer Slide-over -->
    <EditCustomerInfo
      :is-open="isEditCustomerPanelOpen"
      :customer-id="selectedCustomer?.id"
      @close="isEditCustomerPanelOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import CreateCustomerModal from "./create-customer/index.vue";
import EditCustomerInfo from "./edit-customer-info/index.vue";
import { getKhachHangList } from "../../../utils/khachHang";

// Icons
const iconSearch =
  "https://www.figma.com/api/mcp/asset/c1002f19-5383-4004-a035-6dc60c0e3e44";
const iconPlus =
  "https://www.figma.com/api/mcp/asset/47deee17-108b-45ad-8194-f79617746c52";
const iconPhone =
  "https://www.figma.com/api/mcp/asset/58211529-ce1d-4e9e-bfc6-5809535e4b45";
const iconEdit =
  "https://www.figma.com/api/mcp/asset/c3315624-e13b-45e0-bb20-e94cab91a41d";

// Pet images
const petMilo =
  "https://www.figma.com/api/mcp/asset/174f969e-2834-4a74-9dbe-b72236bce422";
const petLuna =
  "https://www.figma.com/api/mcp/asset/124d8241-ae4e-4751-b756-0abfc4e46455";
const petMax =
  "https://www.figma.com/api/mcp/asset/932faee2-5103-4183-8628-4e83f7b63e53";
const petRocky =
  "https://www.figma.com/api/mcp/asset/39dfd855-2b60-4c2b-9f9d-d0391b4739d9";

// State
const searchQuery = ref("");
const activeFilter = ref("all");
const isCreateCustomerModalOpen = ref(false);
const isEditCustomerPanelOpen = ref(false);
const selectedCustomer = ref(null);

// Filters
const filters = computed(() => [
  { key: "all", label: `Tất cả (${customers.value.length})` },
  { key: "member", label: "Thành Viên" },
  { key: "walkin", label: "Vãng Lai" },
]);

// Customer data
const customers = ref([]);

const fetchCustomers = async () => {
  try {
    const res = await getKhachHangList();
    if (res && res.data) {
      customers.value = res.data.map((c) => ({
        id: c.id,
        name: c.full_name,
        phone: c.so_dien_thoai || "N/A",
        hasZalo: false,
        tier: "regular",
        pets: Array.isArray(c.thu_cung) ? c.thu_cung.map(pet => ({ name: pet.ten_thu_cung, emoji: "🐾" })) : [],
        lastVisit: c.updated_at ? new Date(c.updated_at).toLocaleDateString("vi-VN") : "N/A",
        lastVisitRelative: "",
        isMember: false,
      }));
    }
  } catch (error) {
    console.error("Error fetching customers:", error);
  }
};

onMounted(() => {
  fetchCustomers();
});

// Computed
const filteredCustomers = computed(() => {
  let filtered = customers.value;

  // Filter by membership type
  if (activeFilter.value === "member") {
    filtered = filtered.filter((c) => c.isMember);
  } else if (activeFilter.value === "walkin") {
    filtered = filtered.filter((c) => !c.isMember);
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(
      (c) =>
        c.name.toLowerCase().includes(query) ||
        c.phone.includes(query) ||
        c.pets.some((pet) => pet.name.toLowerCase().includes(query))
    );
  }

  return filtered;
});

// Methods
const createCustomer = () => {
  isCreateCustomerModalOpen.value = true;
};

const handleCreateCustomerSubmit = (data) => {
  console.log("Create customer:", data);
  // TODO: Implement API call
  isCreateCustomerModalOpen.value = false;
};

const editCustomer = (customer) => {
  selectedCustomer.value = customer;
  isEditCustomerPanelOpen.value = true;
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}
</style>
