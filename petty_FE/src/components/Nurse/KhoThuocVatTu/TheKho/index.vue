<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white border !border-gray-300 rounded-[10px] shadow-lg w-[510px] relative"
    >
      <!-- Close Button -->
      <button
        @click="$emit('close')"
        class="absolute right-6 top-4 w-4 h-4 opacity-70 hover:opacity-100 transition-opacity"
      >
        <CloseIcon />
      </button>

      <!-- Header -->
      <div class="flex flex-col gap-2 px-6 pt-6 pb-4">
        <h2
          class="font-nunito font-semibold text-lg leading-[18px] text-neutral-950 tracking-tight"
        >
          Thẻ kho - {{ product?.name || "Zoletil 50" }}
        </h2>
        <p class="font-nunito text-sm leading-5 text-[#717182] tracking-tight">
          Lịch sử biến động kho hàng
        </p>
      </div>

      <!-- Content -->
      <div class="flex flex-col gap-4 px-6 py-4">
        <!-- Stock Info Card -->
        <div class="bg-gray-50 rounded-[10px] p-4 grid grid-cols-2 gap-4">
          <!-- Current Stock -->
          <div class="flex flex-col gap-0">
            <p
              class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
            >
              Tồn kho hiện tại
            </p>
            <p
              class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
            >
              {{ currentStock.quantity }} {{ currentStock.unit }}
            </p>
          </div>

          <!-- Lot Number -->
          <div class="flex flex-col gap-0">
            <p
              class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
            >
              Số lô
            </p>
            <p
              class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
            >
              {{ currentStock.lotNumber }}
            </p>
          </div>
        </div>

        <!-- History Section -->
        <div class="flex flex-col gap-2">
          <label
            class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
          >
            Lịch sử biến động
          </label>

          <!-- History List -->
          <div class="border !border-gray-300 rounded-[10px] overflow-hidden">
            <div
              v-for="(item, index) in history"
              :key="index"
              class="flex items-center justify-between px-3 py-3"
              :class="{
                'border-b !border-gray-300': index < history.length - 1,
              }"
            >
              <!-- Left: Type & Info -->
              <div class="flex flex-col gap-0">
                <p
                  class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                >
                  {{ item.type }}
                </p>
                <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                  {{ item.date }} - {{ item.user }}
                </p>
              </div>

              <!-- Right: Quantity Change -->
              <div
                class="font-nunito text-base leading-6 tracking-tight"
                :class="item.change > 0 ? 'text-[#00a63e]' : 'text-[#e7000b]'"
              >
                {{ item.change > 0 ? "+" : "" }}{{ item.change }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div
        class="flex items-center justify-end px-6 py-4 border-t !border-gray-300"
      >
        <button
          @click="$emit('close')"
          class="bg-white border !border-gray-400 rounded-lg h-9 px-[17px] py-[9px] hover:bg-gray-50 transition-colors"
        >
          <span
            class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
            >Đóng</span
          >
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
//Icon SVG
import CloseIcon from "@/assets/svg/close.svg";
// Props
const props = defineProps({
  product: {
    type: Object,
    default: () => ({
      name: "Zoletil 50",
      code: "MED001",
    }),
  },
  stockData: {
    type: Object,
    default: () => ({
      quantity: 5,
      unit: "Lọ",
      lotNumber: "LOT2024001",
    }),
  },
  historyData: {
    type: Array,
    default: () => [
      {
        type: "Nhập kho",
        date: "01/11/2024",
        user: "Nguyễn Văn A",
        change: 50,
      },
      {
        type: "Xuất kho (Bán hàng)",
        date: "05/11/2024",
        user: "Trần Thị B",
        change: -15,
      },
    ],
  },
});

// Emits
defineEmits(["close"]);

// Computed
const currentStock = computed(() => props.stockData);
const history = computed(() => props.historyData);
</script>

<style scoped>
/* No additional styles needed - all handled by Tailwind */
</style>
