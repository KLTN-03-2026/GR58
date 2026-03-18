<template>
  <!-- Modal Overlay -->
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <!-- Modal Card -->
    <div class="bg-white border border-black/10 rounded-[10px] shadow-[0px_10px_15px_-3px_rgba(0,0,0,0.1),0px_4px_6px_-4px_rgba(0,0,0,0.1)] w-[510px] relative">
      <!-- Close Button -->
      <button
        @click="$emit('close')"
        class="absolute right-4 top-4 w-4 h-4 opacity-70 hover:opacity-100 transition-opacity"
      >
        <img :src="iconClose" alt="Close" class="w-full h-full" />
      </button>

      <!-- Dialog Header -->
      <div class="px-6 pt-6 pb-0">
        <h2 class="font-nunito font-semibold text-lg leading-[18px] text-neutral-950 tracking-tight mb-2">
          Tạo lịch hẹn mới
        </h2>
        <p class="font-nunito text-sm leading-5 text-[#717182] tracking-tight">
          Điền thông tin để tạo lịch hẹn cho khách hàng
        </p>
      </div>

      <!-- Form Content -->
      <div class="px-6 pt-[22px] pb-0">
        <div class="flex flex-col gap-4">
          <!-- Row 1: Customer Name + Pet Name -->
          <div class="grid grid-cols-2 gap-4">
            <!-- Customer Name -->
            <div class="flex flex-col">
              <label class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight mb-[6px]">
                Tên khách hàng <span class="text-red-500">*</span>
              </label>
              <input
                v-model="formData.customerName"
                type="text"
                placeholder="Nguyễn Văn A"
                class="bg-[#f3f3f5] border border-transparent rounded-lg h-9 px-3 py-1 font-nunito text-sm text-[#717182] tracking-tight focus:outline-none focus:ring-2 focus:ring-[#009689]"
              />
            </div>

            <!-- Pet Name -->
            <div class="flex flex-col">
              <label class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight mb-[6px]">
                Tên thú cưng <span class="text-red-500">*</span>
              </label>
              <input
                v-model="formData.petName"
                type="text"
                placeholder="Milo"
                class="bg-[#f3f3f5] border border-transparent rounded-lg h-9 px-3 py-1 font-nunito text-sm text-[#717182] tracking-tight focus:outline-none focus:ring-2 focus:ring-[#009689]"
              />
            </div>
          </div>

          <!-- Row 2: Date + Time -->
          <div class="grid grid-cols-2 gap-4">
            <!-- Appointment Date -->
            <div class="flex flex-col">
              <label class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight mb-[6px]">
                Ngày hẹn <span class="text-red-500">*</span>
              </label>
              <input
                v-model="formData.appointmentDate"
                type="date"
                class="bg-[#f3f3f5] border border-transparent rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight focus:outline-none focus:ring-2 focus:ring-[#009689]"
              />
            </div>

            <!-- Appointment Time -->
            <div class="flex flex-col">
              <label class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight mb-[6px]">
                Giờ hẹn <span class="text-red-500">*</span>
              </label>
              <input
                v-model="formData.appointmentTime"
                type="time"
                class="bg-[#f3f3f5] border border-transparent rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight focus:outline-none focus:ring-2 focus:ring-[#009689]"
              />
            </div>
          </div>

          <!-- Service -->
          <div class="flex flex-col">
            <label class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight mb-[6px]">
              Dịch vụ <span class="text-red-500">*</span>
            </label>
            <input
              v-model="formData.service"
              type="text"
              placeholder="Khám tổng quát, Tiêm phòng, v.v."
              class="bg-[#f3f3f5] border border-transparent rounded-lg h-9 px-3 py-1 font-nunito text-sm text-[#717182] tracking-tight focus:outline-none focus:ring-2 focus:ring-[#009689]"
            />
          </div>

        </div>
      </div>

      <!-- Dialog Footer -->
      <div class="px-6 pt-4 pb-6 flex items-center justify-end gap-2">
        <button
          @click="$emit('close')"
          class="bg-white border border-black/10 rounded-lg h-9 px-[17px] py-[9px] font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight hover:bg-gray-50 transition-colors"
        >
          Hủy
        </button>
        <button
          @click="handleSubmit"
          :disabled="!isFormValid"
          :class="[
            'rounded-lg h-9 px-4 py-2 font-nunito font-medium text-sm leading-5 text-white tracking-tight transition-colors',
            isFormValid ? 'bg-[#009689] hover:bg-[#007d72]' : 'bg-gray-300 cursor-not-allowed'
          ]"
        >
          Tạo lịch hẹn
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Icons (from Figma - expire in 7 days)
const iconChevronDown = "https://www.figma.com/api/mcp/asset/09325100-aebd-446a-8f3e-b34e1cf7fcd2"
const iconClose = "https://www.figma.com/api/mcp/asset/fd4a7605-cacc-497a-9b9e-c02330d38dc8"

// Emits
const emit = defineEmits(['close', 'submit'])

// Form Data
const formData = ref({
  customerName: '',
  petName: '',
  appointmentDate: '',
  appointmentTime: '',
  service: '',
  department: '',
  assignedStaff: ''
})

// Form Validation
const isFormValid = computed(() => {
  return formData.value.customerName &&
         formData.value.petName &&
         formData.value.appointmentDate &&
         formData.value.appointmentTime &&
         formData.value.service &&
         formData.value.department &&
         formData.value.assignedStaff
})

// Submit Handler
const handleSubmit = () => {
  if (isFormValid.value) {
    emit('submit', { ...formData.value })
  }
}
</script>

<style scoped>
/* Custom date/time input styling */
input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator {
  cursor: pointer;
  filter: invert(0.5);
}

input[type="date"]:hover::-webkit-calendar-picker-indicator,
input[type="time"]:hover::-webkit-calendar-picker-indicator {
  filter: invert(0.3);
}
</style>
