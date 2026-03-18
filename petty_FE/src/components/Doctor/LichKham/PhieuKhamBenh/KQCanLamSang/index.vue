<template>
  <div
    class="bg-white flex flex-col pb-4 pt-6 px-6 rounded-[14px] shadow-lg w-full max-h-[90vh]"
  >
    <!-- Header -->
    <div
      class="flex items-center justify-between border-b !border-gray-300 pb-4 mb-4"
    >
      <div>
        <h2 class="font-semibold text-xl text-black">Chụp X-Quang Xương</h2>
      </div>
      <button
        @click="$emit('close')"
        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
      >
        <CloseIcon />
      </button>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col gap-4 flex-1 overflow-y-auto">
      <!-- Kết luận Card -->
      <div class="bg-blue-50 border !border-[#bedbff] rounded-lg p-4">
        <div class="mb-2">
          <p class="font-bold text-sm text-gray-700">Kết luận:</p>
        </div>
        <div class="mb-2">
          <p class="font-normal text-base text-black">
            {{ conclusion }}
          </p>
        </div>
        <div>
          <p class="font-normal text-xs text-gray-500">
            {{ completedTime }}
          </p>
        </div>
      </div>

      <!-- Hình ảnh Section -->
      <div class="flex flex-col gap-3">
        <div>
          <p class="font-bold text-sm text-gray-700">
            Hình ảnh ({{ images.length }}):
          </p>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div
            v-for="(image, index) in images"
            :key="index"
            class="border !border-gray-300 rounded-lg overflow-hidden aspect-square"
          >
            <img
              :src="image"
              :alt="`Image ${index + 1}`"
              class="w-full h-full object-cover"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
//Icon SVG
import CloseIcon from "@/assets/svg/close.svg";

// Icons from Figma
const iconClose =
  "https://www.figma.com/api/mcp/asset/1637b22c-53d7-4590-8645-5061c52e0276";

// Emits
defineEmits(["close"]);

// Props (optional - can be used to pass data from parent)
const props = defineProps({
  testName: {
    type: String,
    default: "Chụp X-Quang Xương",
  },
  conclusionText: {
    type: String,
    default: "Gãy xương đùi trái (kín)",
  },
  completedAt: {
    type: String,
    default: "Hoàn thành lúc 10:45",
  },
  resultImages: {
    type: Array,
    default: () => [
      "https://www.figma.com/api/mcp/asset/ace3b5a7-28d9-429d-afb1-9a9f4d3c56ab",
      "https://www.figma.com/api/mcp/asset/2ccd4e8d-3bb0-464d-bbcd-78e0816f07c6",
    ],
  },
});

// State
const conclusion = ref(props.conclusionText);
const completedTime = ref(props.completedAt);
const images = ref(props.resultImages);
</script>

<style scoped>
/* Ensure Nunito Sans font is loaded */
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}

.font-nunito {
  font-family: "Nunito Sans", sans-serif;
}
</style>
