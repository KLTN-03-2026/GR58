<template>
  <Transition name="slide">
    <div v-if="isOpen" class="fixed inset-0 z-50 flex justify-end">
      <!-- Backdrop -->
      <div
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        @click="closeModal"
      ></div>

      <!-- Panel -->
      <div
        class="relative bg-white w-full max-w-[700px] h-full shadow-xl flex flex-col overflow-hidden"
      >
        <!-- Header -->
        <div class="flex flex-col gap-1.5 px-6 py-4 border-b !border-gray-300">
          <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold text-black">
              Thông tin khách hàng
            </h2>
            <button
              @click="closeModal"
              class="w-6 h-6 flex items-center justify-center opacity-70 hover:opacity-100 transition-opacity"
            >
              <!-- <img :src="ICONS.close" alt="Đóng" class="w-4 h-4" /> -->
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
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
          <p class="text-sm text-gray-600">
            Chi tiết hồ sơ và lịch sử giao dịch
          </p>
        </div>

        <!-- Content (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-6">
          <div class="flex flex-col gap-6">
            <!-- Customer Info Card -->
            <div
              class="bg-blue-50 border-2 !border-[#bedbff] rounded-[14px] p-4"
            >
              <div class="flex items-center gap-3">
                <!-- Avatar -->
                <div
                  class="w-16 h-16 bg-[#bedbff] rounded-full flex items-center justify-center shrink-0"
                >
                  <!-- <img :src="ICONS.user" alt="User" class="w-8 h-8" /> -->
                  <svg
                    class="w-8 h-8 text-[#1447e6]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                    />
                  </svg>
                </div>

                <!-- Customer Details -->
                <div class="flex flex-col gap-2">
                  <h3 class="text-xl font-bold text-black">
                    {{ customer.name }}
                  </h3>
                  <div class="flex items-center gap-1">
                    <!-- <img :src="ICONS.phone" alt="Phone" class="w-3 h-3" /> -->
                    <p class="text-sm text-gray-600">
                      {{ customer.phone }}
                    </p>
                  </div>
                  <div
                    class="inline-flex items-center px-2 py-0.5 rounded-lg border border-transparent w-fit"
                    :class="getMembershipStyle(customer.membership)"
                  >
                    <span class="text-xs font-medium">{{
                      customer.membership
                    }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tabs -->
            <div class="flex flex-col gap-4">
              <div class="flex items-center gap-4 border-b !border-gray-300">
                <button
                  @click="activeTab = 'pets'"
                  class="px-0 py-2 text-sm transition-colors relative"
                  :class="
                    activeTab === 'pets'
                      ? 'text-[#1447e6] border-b-2 border-[#1447e6]'
                      : 'text-gray-600'
                  "
                >
                  Danh sách Thú cưng
                </button>
                <button
                  @click="activeTab = 'transactions'"
                  class="px-0 py-2 text-sm transition-colors relative"
                  :class="
                    activeTab === 'transactions'
                      ? 'text-[#1447e6] border-b-2 border-[#1447e6]'
                      : 'text-gray-600'
                  "
                >
                  Lịch sử Giao dịch
                </button>
              </div>

              <!-- Tab Content -->
              <div class="flex flex-col gap-4">
                <!-- Pets Tab -->
                <div v-if="activeTab === 'pets'" class="flex flex-col gap-4">
                  <div
                    v-for="pet in pets"
                    :key="pet.id"
                    class="bg-white border-2 !border-gray-300 rounded-[14px] p-4 shadow-sm"
                  >
                    <div class="flex gap-4">
                      <!-- Pet Image -->
                      <div
                        class="w-20 h-20 rounded-[10px] overflow-hidden shrink-0"
                      >
                        <img
                          :src="pet.image"
                          :alt="pet.name"
                          class="w-full h-full object-cover"
                        />
                      </div>

                      <!-- Pet Details -->
                      <div class="flex flex-col gap-2 flex-1">
                        <h4 class="text-lg font-bold text-black">
                          {{ pet.name }}
                        </h4>
                        <p class="text-sm text-gray-600">
                          {{ pet.species }} • {{ pet.age }} tuổi
                        </p>
                        <div
                          class="bg-gray-50 border !border-gray-300 rounded-lg p-2 flex flex-col gap-1"
                        >
                          <p class="text-sm text-gray-700">
                            Lần khám gần nhất: {{ pet.lastVisit }}
                          </p>
                          <p class="text-sm text-gray-700">
                            Dịch vụ: {{ pet.lastService }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Add Pet Button -->
                  <button
                    @click="addNewPet"
                    class="bg-white border !border-gray-300 rounded-lg px-4 py-3 h-12 flex items-center justify-center gap-2 hover:bg-gray-50 transition-colors"
                  >
                    <!-- <img :src="ICONS.plus" alt="Plus" class="w-4 h-4" /> -->
                    <span class="text-base font-semibold text-black">
                      Thêm thú cưng mới
                    </span>
                  </button>
                </div>

                <!-- Transactions Tab -->
                <div
                  v-else-if="activeTab === 'transactions'"
                  class="flex flex-col gap-4"
                >
                  <div
                    v-for="transaction in transactions"
                    :key="transaction.id"
                    class="bg-white border !border-gray-300 rounded-[14px] p-4 shadow-sm"
                  >
                    <div class="flex items-center justify-between">
                      <!-- Transaction Info -->
                      <div class="flex flex-col gap-2">
                        <h4 class="text-base font-bold text-black">
                          {{ transaction.service }}
                        </h4>
                        <p class="text-sm text-gray-600">
                          {{ transaction.date }}
                        </p>
                      </div>

                      <!-- Amount and Status -->
                      <div class="flex flex-col gap-2 items-end">
                        <p class="text-lg font-bold text-black">
                          {{ formatCurrency(transaction.amount) }}
                        </p>
                        <div
                          class="bg-green-100 border border-transparent rounded-lg px-2 py-0.5"
                        >
                          <span class="text-xs font-medium text-[#008236]">
                            {{ transaction.status }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Transition>

  <!-- Add Pet Modal -->
  <ThemThuCungMoi
    :is-open="isAddPetModalOpen"
    :customer-name="customer.name"
    @close="isAddPetModalOpen = false"
    @submit="handleAddPetSubmit"
  />
</template>

<script setup>
import { ref, computed } from "vue";
import ThemThuCungMoi from "./ThemThuCungMoi/index.vue";

// Icons from Figma (7-day expiry)
const ICONS = {
  close:
    "https://www.figma.com/api/mcp/asset/093dfe48-7373-4a7a-9024-0246fea90bb9",
  user: "https://www.figma.com/api/mcp/asset/cf96353e-47c6-439e-9925-62ad7e91b016",
  phone:
    "https://www.figma.com/api/mcp/asset/b9bf8c53-efc7-4005-ad6f-205478453065",
  plus: "https://www.figma.com/api/mcp/asset/3bd262e3-306f-475d-9429-8537b44c3979",
};

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  customerId: {
    type: [String, Number],
    default: null,
  },
});

// Emits
const emit = defineEmits(["close", "addPet"]);

// State
const activeTab = ref("pets");
const isAddPetModalOpen = ref(false);

// Sample customer data (replace with API call)
const customer = ref({
  name: "Trần Thị B",
  phone: "0912345678",
  membership: "Kim cương",
});

// Sample pets data (replace with API call)
const pets = ref([
  {
    id: 1,
    name: "Max",
    species: "Chó Husky",
    age: 3,
    image:
      "https://www.figma.com/api/mcp/asset/5801e277-8a6c-4489-8ccb-6151089e5870",
    lastVisit: "15/11/2024",
    lastService: "Spa & Tắm",
  },
]);

// Sample transactions data (replace with API call)
const transactions = ref([
  {
    id: 1,
    service: "Spa & Tắm",
    date: "15/11/2024",
    amount: 400000,
    status: "Đã thanh toán",
  },
]);

// Methods
const closeModal = () => {
  emit("close");
};

const addNewPet = () => {
  isAddPetModalOpen.value = true;
};

const handleAddPetSubmit = (petData) => {
  console.log("New pet data:", petData);
  // Mock adding to list
  pets.value.push({
    id: Date.now(),
    name: petData.name,
    species:
      petData.species === "dog"
        ? "Chó " + petData.breed
        : petData.species === "cat"
        ? "Mèo " + petData.breed
        : petData.breed,
    age: 0, // Calculate from birthdate
    image: "", // Placeholder
    lastVisit: "Chưa khám",
    lastService: "Chưa có",
  });
};

const getMembershipStyle = (membership) => {
  const styles = {
    "Kim cương": "bg-purple-100 text-[#8200db]",
    Vàng: "bg-[#fef9c2] text-[#a65f00]",
    Bạc: "bg-gray-100 text-[#364153]",
    Thường: "bg-gray-100 text-[#6a7282]",
  };
  return styles[membership] || "bg-gray-100 text-gray-600";
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(amount);
};

// TODO: Fetch customer data, pets, and transactions from API when component mounts
// TODO: Implement real-time updates for customer information
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}

/* Slide Animation */
.slide-enter-active,
.slide-leave-active {
  transition: opacity 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
}

.slide-enter-active .relative,
.slide-leave-active .relative {
  transition: transform 0.3s ease-in-out;
}

.slide-enter-from .relative,
.slide-leave-to .relative {
  transform: translateX(100%);
}
</style>
