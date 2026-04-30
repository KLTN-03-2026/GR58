<template>
  <div class="toast-card bg-white rounded-xl shadow-lg overflow-hidden w-full pointer-events-auto border !border-black/8">
    <div class="flex items-start gap-3 px-4 pt-4 pb-3">
      <!-- Icon -->
      <div class="flex-shrink-0 mt-0.5">
        <component :is="iconComponent" :size="20" :class="iconColorClass" />
      </div>

      <!-- Title + Description -->
      <div class="flex-1 min-w-0">
        <p class="text-sm font-bold text-gray-900 font-nunitoSans leading-5">{{ title }}</p>
        <p
          v-if="message"
          class="text-sm font-medium text-gray-500 font-nunitoSans leading-5 mt-0.5 whitespace-pre-wrap"
          v-html="message"
        ></p>
      </div>

      <!-- Close -->
      <button
        @click.stop="$emit('close-toast')"
        aria-label="Close"
        class="flex-shrink-0 p-1 -m-1 rounded-md hover:bg-gray-100 transition text-gray-400 hover:text-gray-600"
      >
        <X :size="15" />
      </button>
    </div>

    <!-- Accent bar -->
    <div class="relative h-1 w-full overflow-hidden" :class="barBgClass">
      <div
        class="absolute inset-y-0 left-0 h-full"
        :class="barFillClass"
        :style="{ animation: `progress-bar ${duration}ms linear forwards` }"
      ></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { CheckCircle2, XCircle, Info, AlertTriangle, X } from 'lucide-vue-next';

const props = defineProps({
  type: { type: String, default: 'success' },
  title: { type: String, default: '' },
  message: { type: String, default: '' },
  duration: { type: Number, default: 4000 },
});

defineEmits(['close-toast']);

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
    case 'success': return 'text-teal-500';
    case 'error': return 'text-red-500';
    case 'info': return 'text-blue-500';
    case 'warning': return 'text-amber-500';
    default: return 'text-gray-400';
  }
});

const barBgClass = computed(() => {
  switch (props.type) {
    case 'success': return 'bg-teal-100';
    case 'error': return 'bg-red-100';
    case 'info': return 'bg-blue-100';
    case 'warning': return 'bg-amber-100';
    default: return 'bg-gray-100';
  }
});

const barFillClass = computed(() => {
  switch (props.type) {
    case 'success': return 'bg-teal-500';
    case 'error': return 'bg-red-500';
    case 'info': return 'bg-blue-500';
    case 'warning': return 'bg-amber-400';
    default: return 'bg-gray-400';
  }
});
</script>

<style>
@keyframes progress-bar {
  from { width: 100%; }
  to   { width: 0%; }
}

/* Strip toastification's own background/padding so our white card takes full control */
.Vue-Toastification__toast {
  background: transparent !important;
  box-shadow: none !important;
  padding: 0 !important;
  min-height: unset !important;
  border-radius: 0 !important;
}
.Vue-Toastification__toast--success,
.Vue-Toastification__toast--error,
.Vue-Toastification__toast--info,
.Vue-Toastification__toast--warning {
  background: transparent !important;
}
.Vue-Toastification__toast-body {
  padding: 0 !important;
  margin: 0 !important;
}
</style>
