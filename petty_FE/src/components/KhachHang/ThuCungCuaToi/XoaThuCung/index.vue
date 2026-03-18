<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="close">
    <div class="bg-white border border-black/15 rounded-[10px] w-full max-w-[512px] m-4 p-6">
      <div class="flex flex-col gap-4 items-center justify-center">
        <!-- Warning Icon -->
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
          <img src="https://www.figma.com/api/mcp/asset/91d45e54-bd58-4ef6-a4ae-e082a1f9c2c2" alt="Warning"
            class="w-9 h-6" />
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
                <span class="text-base font-extrabold">{{ petData.name }}</span> 
                không?
              </p>
            </div>
            
            <!-- Warning details -->
            <div class="flex gap-2 items-center justify-center py-0.5 px-4">
              <p class="text-sm font-semibold text-gray-500 text-center leading-5">
                Hành động này không thể hoàn tác. Toàn bộ lịch sử khám bệnh và hồ sơ tiêm phòng liên quan đến {{ petData.name }} sẽ bị xóa vĩnh viễn
              </p>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-6 items-center">
          <button type="button" @click="handleDelete"
            class="bg-red-600 border border-red-300 px-8 py-2 rounded-lg text-sm font-semibold text-white hover:bg-red-700 transition">
            Xoá vĩnh viễn
          </button>
          <button type="button" @click="close"
            class="bg-white border border-black/15 px-8 py-2 rounded-lg text-sm font-semibold text-black hover:bg-gray-50 transition">
            Huỷ
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  petData: {
    type: Object,
    default: () => ({
      id: null,
      name: ''
    })
  }
});

const emit = defineEmits(['close', 'delete']);

const close = () => {
  emit('close');
};

const handleDelete = () => {
  emit('delete', props.petData);
  close();
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Nunito+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,700&family=Nunito:wght@400&display=swap");
</style>
