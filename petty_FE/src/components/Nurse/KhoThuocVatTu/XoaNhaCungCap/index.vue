<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white border border-gray-200/60 rounded-[10px] shadow-lg w-[512px] relative p-6"
    >
      <!-- Confirmation State -->
      <div v-if="!hasRelatedReceipts" class="flex flex-col gap-4">
        <!-- Header -->
        <div class="flex flex-col gap-2">
          <div class="flex items-start gap-2">
            <h2
              class="font-nunito font-semibold text-lg leading-7 text-neutral-950 tracking-tight"
            >
              Xác nhận xóa nhà cung cấp?
            </h2>
          </div>
          <div class="flex flex-col gap-2">
            <p
              class="font-nunito text-sm leading-5 text-[#717182] tracking-tight"
            >
              Bạn có chắc chắn muốn xóa nhà cung cấp
              <span class="font-semibold">{{ supplierName }}</span
              >?
            </p>
            <p
              class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
            >
              Hành động này không thể hoàn tác.
            </p>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-2 pt-4">
          <button
            @click="$emit('close')"
            class="bg-white border !border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 transition-colors"
          >
            <span class="font-medium text-sm text-black">Hủy</span>
          </button>
          <button
            @click="handleDelete"
            class="bg-[#e7000b] rounded-lg px-4 py-2 hover:bg-[#c0000a] transition-colors"
          >
            <span class="font-medium text-sm text-white">Xóa</span>
          </button>
        </div>
      </div>

      <!-- Error State (Cannot Delete) -->
      <div v-else class="flex flex-col gap-4">
        <!-- Header -->
        <div class="flex flex-col gap-2">
          <div class="flex items-start gap-2">
            <h2
              class="font-nunito font-semibold text-lg leading-7 text-neutral-950 tracking-tight"
            >
              Không thể xóa nhà cung cấp
            </h2>
          </div>
          <div class="flex flex-col gap-3">
            <p
              class="font-nunito text-sm leading-5 text-[#717182] tracking-tight"
            >
              Không thể xóa nhà cung cấp
              <span class="font-semibold">{{ supplierName }}</span>
              vì đã phát sinh dữ liệu nhập kho.
            </p>

            <!-- Related Receipts Box -->
            <div
              class="bg-red-50 border !border-[#ffc9c9] rounded-[10px] p-3 flex flex-col gap-2"
            >
              <p
                class="font-nunito text-sm leading-5 text-[#9f0712] tracking-tight"
              >
                📋 Các phiếu nhập liên quan:
              </p>
              <ul class="list-none space-y-1">
                <li
                  v-for="receipt in relatedReceipts"
                  :key="receipt.id"
                  class="font-nunito text-sm leading-5 text-[#c10007] tracking-tight"
                >
                  • {{ receipt.code }} - {{ receipt.date }} -
                  {{ formatCurrency(receipt.amount) }}
                </li>
              </ul>
            </div>

            <p
              class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
            >
              Bạn chỉ có thể
              <span class="font-semibold">Ngừng hợp tác</span>
              với nhà cung cấp này.
            </p>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-2 pt-4">
          <button
            @click="$emit('close')"
            class="bg-white border !border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 transition-colors"
          >
            <span class="font-medium text-sm text-black">Hủy</span>
          </button>
          <button
            @click="handleDeactivate"
            class="bg-[#e7000b] rounded-lg px-4 py-2 hover:bg-[#c0000a] transition-colors"
          >
            <span class="font-medium text-sm text-white">Ngừng hợp tác</span>
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
  supplierName: {
    type: String,
    default: "Công ty Vật tư Y tế Đông Nam",
  },
  relatedReceipts: {
    type: Array,
    default: () => [],
    // Example: [{ id: 'PN002', code: 'PN002', date: '2024-11-05', amount: 9600000 }]
  },
});

// Emits
const emit = defineEmits(["close", "delete", "deactivate"]);

// Computed
const hasRelatedReceipts = computed(() => {
  return props.relatedReceipts && props.relatedReceipts.length > 0;
});

// Methods
const formatCurrency = (amount) => {
  return amount.toLocaleString("vi-VN") + " ₫";
};

const handleDelete = () => {
  emit("delete");
};

const handleDeactivate = () => {
  emit("deactivate");
};
</script>

<style scoped>
/* Add any additional styles if needed */
</style>
