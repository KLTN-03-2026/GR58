<template>
  <!-- Make inner container transparent so variant background (success/error/etc.) from the toast wrapper is visible -->
  <div
    class="toast-container bg-transparent rounded-lg w-full max-w-[520px] shadow-lg overflow-visible pointer-events-auto"
  >
    <div class="flex gap-3 items-start p-4 relative">
      <!-- Icon + Content -->
      <div class="flex gap-3 items-start flex-1 min-w-0">
        <!-- Icon -->
        <div class="flex-shrink-0 w-6 h-6 mt-0.5 flex justify-center items-center">
          <component :is="iconComponent" :size="24" :class="iconColorClass" />
        </div>

        <!-- Title + Description -->
        <div class="flex flex-col gap-1 flex-1 min-w-0">
          <p
            class="text-sm md:text-base font-semibold leading-5 font-nunitoSans"
            style="color: #ffffff"
          >
            {{ title }}
          </p>
          <p
            class="text-sm font-normal leading-5 whitespace-pre-wrap font-nunitoSans"
            v-html="message"
            style="color: rgba(255, 255, 255, 0.95)"
          ></p>
        </div>
      </div>

      <!-- Close Button -->
      <button
        @click.stop="$emit('close-toast')"
        aria-label="Close"
        class="flex-shrink-0 p-1.5 -m-1.5 rounded-md hover:bg-white/10 transition"
        style="color: rgba(255, 255, 255, 0.95)"
      >
        <X :size="16" />
      </button>
    </div>
    <!-- Progress Bar -->
    <div
      class="toast-progress absolute bottom-0 left-0 h-1.5 w-full"
      :style="{
        animation: `progress-bar ${duration}ms linear forwards`,
        background: 'rgba(255,255,255,0.18)',
      }"
    ></div>
  </div>
</template>

<style>
@keyframes progress-bar {
  from {
    width: 100%;
  }
  to {
    width: 0%;
  }
}

.toast-container:hover .toast-progress {
  animation-play-state: paused !important;
}
</style>

<script setup>
import { computed } from 'vue';
import { CheckCircle2, XCircle, Info, AlertTriangle, X } from 'lucide-vue-next';

const props = defineProps({
  type: {
    type: String,
    default: "success",
  },
  title: {
    type: String,
    default: "",
  },
  message: {
    type: String,
    default: "",
  },
  duration: {
    type: Number,
    default: 4000,
  },
});

defineEmits(["close-toast"]);

const iconComponent = computed(() => {
  switch (props.type) {
    case 'success': return CheckCircle2;
    case 'error': return XCircle;
    case 'info': return Info;
    case 'warning': return AlertTriangle;
    default: return Info;
  }
});

const iconColorClass = computed(() => {
  switch (props.type) {
    case 'success': return 'text-green-500';
    case 'error': return 'text-red-500';
    case 'info': return 'text-blue-500';
    case 'warning': return 'text-amber-500';
    default: return 'text-white';
  }
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Nunito+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,700&family=Nunito:wght@400&display=swap");

/* Ensure toast content appears above the progress bar and nothing covers the text */
.toast-container {
  position: relative;
  overflow: visible;
}
.toast-container > .flex {
  position: relative;
  z-index: 10;
}
.toast-progress {
  z-index: 0;
}
</style>
