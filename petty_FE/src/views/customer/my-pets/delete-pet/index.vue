<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="close">
    <div class="bg-white border border-black/15 rounded-[10px] w-full max-w-[512px] m-4 p-6">
      <div class="flex flex-col gap-4 items-center justify-center">
        <!-- Warning Icon -->
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
          <AlertTriangle class="w-8 h-8 text-red-600" />
        </div>

        <!-- Title -->
        <div>
          <h2 class="text-2xl font-semibold text-neutral-950 text-center leading-8">
            Xoá thú cưng ?
          </h2>
        </div>

        <!-- Warning Message Box -->
        <div class="bg-red-50 rounded-[10px] w-full py-6 px-4">
          <div class="flex flex-col gap-2">
            <!-- Main confirmation text -->
            <div class="flex gap-2 items-center justify-center py-0.5 px-4">
              <p class="text-sm font-semibold text-gray-500 text-center">
                Bạn có chắc chắn muốn xóa 
                <span class="text-base font-extrabold">{{ petData.ten_thu_cung || petData.name }}</span> 
                không?
              </p>
            </div>
            
            <!-- Warning details -->
            <div class="flex gap-2 items-center justify-center py-0.5 px-4">
              <p class="text-sm font-semibold text-gray-500 text-center leading-5">
                Hành động này không thể hoàn tác. Nếu thú cưng này chưa phát sinh lịch hẹn, hồ sơ của bé sẽ bị xóa vĩnh viễn.
              </p>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-6 items-center">
          <button
            type="button"
            @click="handleDelete"
            :disabled="isDeleting"
            class="bg-red-600 border border-red-300 px-8 py-2 rounded-lg text-sm font-semibold text-white hover:bg-red-700 transition disabled:opacity-60 disabled:cursor-not-allowed"
          >
            {{ isDeleting ? "Đang xóa..." : "Xoá vĩnh viễn" }}
          </button>
          <button
            type="button"
            @click="close"
            :disabled="isDeleting"
            class="bg-white border border-black/15 px-8 py-2 rounded-lg text-sm font-semibold text-black hover:bg-gray-50 transition disabled:opacity-60 disabled:cursor-not-allowed"
          >
            Huỷ
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { AlertTriangle } from "lucide-vue-next";

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  petData: {
    type: Object,
    default: () => ({
      id: null,
      ten_thu_cung: '',
      name: '' // fallback
    })
  },
  isDeleting: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['close', 'delete']);

const close = () => {
  emit('close');
};

const handleDelete = () => {
  emit('delete', props.petData);
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Nunito+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,700&family=Nunito:wght@400&display=swap");
</style>
