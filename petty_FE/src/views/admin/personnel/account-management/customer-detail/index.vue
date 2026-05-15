```html
<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[1000] p-4"
  >
    <div
      class="bg-white border !border-gray-300 rounded-[10px] shadow-xl w-full max-w-[510px] max-h-[90vh] flex flex-col"
    >
      <!-- Fixed Header -->
      <div class="flex-shrink-0 p-6 pb-4 border-b border-gray-200">
        <div class="flex flex-col gap-2">
          <h2
            class="font-nunitoSans font-semibold text-lg leading-[18px] text-neutral-950 tracking-[-0.4395px]"
          >
            Thông tin Khách hàng
          </h2>
          <p
            class="font-nunitoSans text-sm leading-5 text-[#717182] tracking-[-0.1504px]"
          >
            Lịch sử khám và thông tin chi tiết
          </p>
        </div>
      </div>

      <!-- Scrollable Content -->
      <div class="flex-1 overflow-y-auto px-6 py-4">
        <div class="flex flex-col">
          <!-- Avatar -->
          <div class="flex justify-center mb-4">
            <div
              class="border-4 border-[#cbfbf1] rounded-full w-20 h-20 overflow-hidden"
            >
              <img
                :src="customer.avatar || 'https://www.gravatar.com/avatar?d=mp'"
                alt="Avatar"
                class="w-full h-full object-cover"
              />
            </div>
          </div>

          <!-- Customer Information Section -->
          <div
            class="bg-gray-50 rounded-[10px] p-4 mb-4 grid grid-cols-2 gap-y-[16px] gap-x-[16px]"
          >
            <!-- Name -->
            <div class="flex flex-col gap-1">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Họ tên</span
              >
              <span
                class="font-nunitoSans text-base leading-6 text-[#101828] tracking-[-0.3125px]"
                >{{ customer.name || '—' }}</span
              >
            </div>

            <!-- Phone -->
            <div class="flex flex-col gap-1">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Số điện thoại</span
              >
              <span
                class="font-nunitoSans text-base leading-6 text-[#101828] tracking-[-0.3125px]"
                >{{ customer.phone || '—' }}</span
              >
            </div>

            <!-- Email -->
            <div class="flex flex-col gap-1">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Email</span
              >
              <span
                class="font-nunitoSans text-base leading-6 text-[#101828] tracking-[-0.3125px]"
                >{{ customer.email || '—' }}</span
              >
            </div>

            <!-- Rank -->
            <div class="flex flex-col gap-1">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Xếp hạng</span
              >
              <span
                :class="[
                  'inline-flex items-center px-2 py-[3px] rounded-lg text-xs leading-4 font-nunitoSans font-medium w-fit',
                  customer.rank === 'Gold'
                    ? 'bg-[#fef3c6] text-[#bb4d00]'
                    : customer.rank === 'Silver'
                    ? 'bg-gray-100 text-gray-700'
                    : 'bg-orange-100 text-orange-700',
                ]"
              >
                {{ customer.rank || 'Silver' }}
              </span>
            </div>

            <!-- Address (full width) -->
            <div class="flex flex-col gap-1 col-span-2">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Địa chỉ</span
              >
              <span
                class="font-nunitoSans text-base leading-6 text-[#101828] tracking-[-0.3125px]"
                >{{ customer.address || '—' }}</span
              >
            </div>

            <!-- Total Spending -->
            <div class="flex flex-col gap-1">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Tổng chi tiêu</span
              >
              <span
                class="font-nunitoSans text-base leading-6 text-[#009689] tracking-[-0.3125px]"
                >{{ formatCurrency(customer.totalSpent || 0) }}</span
              >
            </div>

            <!-- Join Date -->
            <div class="flex flex-col gap-1">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Ngày tham gia</span
              >
              <span
                class="font-nunitoSans text-base leading-6 text-[#101828] tracking-[-0.3125px]"
                >{{ customer.joinDate || '—' }}</span
              >
            </div>

            <!-- Status -->
            <div class="flex flex-col gap-1 col-span-2">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Trạng thái</span
              >
              <span
                :class="[
                  'inline-flex items-center gap-2 px-2 py-[3px] rounded-lg text-xs leading-4 font-nunitoSans font-medium w-fit',
                  customer.status === 'active'
                    ? 'bg-green-100 text-[#008236]'
                    : 'bg-[#ffe2e2] text-[#c10007]',
                ]"
              >
                {{ customer.status === 'active' ? 'Hoạt động' : 'Bị chặn' }}
              </span>
            </div>
          </div>

          <!-- Pets Section -->
          <div class="flex flex-col gap-2 mb-4">
            <label
              class="font-nunitoSans font-medium text-sm leading-[14px] text-neutral-950 tracking-[-0.1504px]"
            >
              Thú cưng ({{ petList.length }} bé)
            </label>
            <div
              v-if="petList.length > 0"
              class="border !border-gray-300 rounded-[10px] divide-y divide-gray-200"
            >
              <div
                v-for="(pet, i) in petList"
                :key="i"
                class="px-3 py-2.5"
              >
                <p class="font-nunitoSans text-sm leading-5 text-[#101828] tracking-[-0.1504px]">
                  {{ pet }}
                </p>
              </div>
            </div>
            <div
              v-else
              class="border !border-gray-300 rounded-[10px] px-3 py-3 text-sm text-[#717182]"
            >
              Chưa có thú cưng
            </div>
          </div>
        </div>
      </div>

      <!-- Fixed Footer -->
      <div class="flex-shrink-0 p-6 pt-4 border-t border-gray-200">
        <div class="flex justify-end">
          <button
            @click="$emit('close')"
            class="bg-white border !border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 transition-colors"
          >
            <span
              class="font-nunitoSans font-medium text-sm leading-5 text-neutral-950 tracking-[-0.1504px]"
              >Đóng</span
            >
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

// Props
const props = defineProps({
  customer: {
    type: Object,
    required: true,
  },
});

// Emits
const emit = defineEmits(["close"]);

// Normalize pets — có thể là array of string hoặc array of object
const petList = computed(() => {
  const pets = props.customer?.pets;
  if (!Array.isArray(pets) || pets.length === 0) return [];
  return pets.map((p) => (typeof p === "string" ? p : p?.ten_thu_cung || p?.name || "—"));
});

// Methods
const formatCurrency = (amount) => {
  return Number(amount || 0).toLocaleString("vi-VN") + " ₫";
};
</script>

<style scoped>
/* Custom scrollbar for modal */
div::-webkit-scrollbar {
  width: 8px;
}

div::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

div::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

div::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
