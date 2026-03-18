<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-6"
    @click.self="closeModal"
  >
    <div
      class="bg-white rounded-[10px] shadow-[0px_20px_25px_-5px_rgba(0,0,0,0.1),0px_8px_10px_-6px_rgba(0,0,0,0.1)] p-6 flex flex-col gap-4 w-full max-w-[448px]"
      @click.stop
    >
      <!-- Dialog Header -->
      <div class="flex flex-col gap-0 h-12">
        <div class="h-7 relative w-full">
          <img
            :src="iconCheckCircle"
            alt="Check"
            class="absolute left-0 w-5 h-5 top-1"
          />
          <h2
            class="absolute left-7 top-0 font-nunito text-lg leading-7 text-[#101828] tracking-[-0.4395px]"
          >
            Xác nhận Check-in
          </h2>
        </div>
        <div class="h-5 relative w-full">
          <p
            class="absolute left-0 top-[0.5px] font-nunito text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
          >
            Xác nhận thông tin bệnh nhân: {{ patientInfo.petName }}
          </p>
        </div>
      </div>

      <!-- Patient Information Card -->
      <div
        class="border-2 border-[#bedbff] rounded-[14px] p-5 flex flex-col gap-4"
      >
        <!-- Card Title -->
        <div class="h-5 relative flex items-center">
          <img
            :src="iconInfo"
            alt="Info"
            class="w-4 h-4 mr-2"
          />
          <p
            class="font-nunito text-sm leading-5 text-[#1c398e] tracking-[0.1996px] uppercase"
          >
            Thông tin đặt khám
          </p>
        </div>

        <!-- Information Grid -->
        <div class="grid grid-cols-2 gap-x-8 gap-y-3">
          <!-- Left Column -->
          <!-- Owner Name -->
          <div class="flex items-start gap-1.5">
            <img :src="iconUser" alt="User" class="w-4 h-4 mt-0.5" />
            <div class="flex flex-col gap-[1.5px]">
              <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                Chủ nhân
              </p>
              <p
                class="font-nunito font-bold text-sm leading-5 text-[#101828] tracking-[-0.1504px]"
              >
                {{ patientInfo.ownerName }}
              </p>
            </div>
          </div>

          <!-- Pet Name -->
          <div class="flex items-start gap-1.5">
            <img :src="iconPaw" alt="Pet" class="w-4 h-4 mt-0.5" />
            <div class="flex flex-col gap-[1.5px]">
              <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                Tên thú cưng
              </p>
              <p
                class="font-nunito font-bold text-sm leading-5 text-[#101828] tracking-[-0.1504px]"
              >
                {{ patientInfo.petName }}
              </p>
            </div>
          </div>

          <!-- Breed -->
          <div class="flex items-start gap-1.5">
            <img :src="iconPaw2" alt="Breed" class="w-4 h-4 mt-0.5" />
            <div class="flex flex-col gap-[1.5px]">
              <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                Giống
              </p>
              <p
                class="font-nunito font-bold text-sm leading-5 text-[#101828] tracking-[-0.1504px]"
              >
                {{ patientInfo.breed }}
              </p>
            </div>
          </div>

          <!-- Service -->
          <div class="flex items-start gap-1.5">
            <img :src="iconService" alt="Service" class="w-4 h-4 mt-0.5" />
            <div class="flex flex-col gap-[1.5px]">
              <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                Dịch vụ
              </p>
              <p
                class="font-nunito font-bold text-sm leading-5 text-[#101828] tracking-[-0.1504px]"
              >
                {{ patientInfo.service }}
              </p>
            </div>
          </div>

          <!-- Room -->
          <div class="flex items-start gap-1.5">
            <img :src="iconRoom" alt="Room" class="w-4 h-4 mt-0.5" />
            <div class="flex flex-col gap-[1.5px]">
              <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                Phòng khám
              </p>
              <p
                class="font-nunito font-bold text-sm leading-5 text-[#101828] tracking-[-0.1504px]"
              >
                {{ patientInfo.room }}
              </p>
            </div>
          </div>

          <!-- Right Column -->
          <!-- Phone Number -->
          <div class="flex items-start gap-1.5 col-start-2 row-start-1">
            <img :src="iconPhone" alt="Phone" class="w-4 h-4 mt-0.5" />
            <div class="flex flex-col gap-[1.5px]">
              <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                Số điện thoại
              </p>
              <p
                class="font-nunito font-bold text-sm leading-5 text-[#101828] tracking-[-0.1504px]"
              >
                {{ patientInfo.phone }}
              </p>
            </div>
          </div>

          <!-- Species -->
          <div class="flex items-start gap-1.5 col-start-2 row-start-2">
            <img :src="iconPaw" alt="Species" class="w-4 h-4 mt-0.5" />
            <div class="flex flex-col gap-[1.5px]">
              <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                Loài
              </p>
              <p
                class="font-nunito font-bold text-sm leading-5 text-[#101828] tracking-[-0.1504px]"
              >
                {{ patientInfo.species }}
              </p>
            </div>
          </div>

          <!-- Birth Date -->
          <div class="flex items-start gap-1.5 col-start-2 row-start-3">
            <img :src="iconCalendar" alt="Birth Date" class="w-4 h-4 mt-0.5" />
            <div class="flex flex-col gap-[1.5px]">
              <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                Ngày sinh
              </p>
              <p
                class="font-nunito font-bold text-sm leading-5 text-[#101828] tracking-[-0.1504px]"
              >
                {{ patientInfo.birthDate }}
              </p>
            </div>
          </div>

          <!-- Doctor -->
          <div class="flex items-start gap-1.5 col-start-2 row-start-4">
            <img :src="iconDoctor" alt="Doctor" class="w-4 h-4 mt-0.5" />
            <div class="flex flex-col gap-[1.5px]">
              <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                Bác sĩ
              </p>
              <p
                class="font-nunito font-bold text-sm leading-5 text-[#101828] tracking-[-0.1504px]"
              >
                {{ patientInfo.doctor }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Dialog Footer -->
      <div class="flex items-center justify-end gap-2 h-9">
        <button
          class="bg-white border border-black/10 rounded-lg px-[17px] py-[9px] h-9 hover:bg-gray-50 transition-colors"
          @click="closeModal"
        >
          <span
            class="font-nunito font-medium text-sm leading-5 text-[#101828] tracking-[-0.1504px]"
          >
            Hủy
          </span>
        </button>
        <button
          class="bg-[#155dfc] rounded-lg px-3 py-2 flex items-center gap-2.5 hover:bg-[#1447e6] transition-colors"
          @click="confirmCheckIn"
        >
          <span
            class="font-nunito font-medium text-sm leading-5 text-white tracking-[-0.1504px]"
          >
            Xác nhận
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue'

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  patientInfo: {
    type: Object,
    default: () => ({
      ownerName: 'Nguyễn Văn A',
      phone: '0901234567',
      petName: 'Milo',
      species: 'Chó',
      breed: 'Poodle',
      birthDate: '15/03/2021',
      service: 'Khám tổng quát',
      doctor: 'BS. Hùng',
      room: 'P.101'
    })
  }
})

// Emits
const emit = defineEmits(['close', 'confirm'])

// Icons from Figma
const iconCheckCircle = "https://www.figma.com/api/mcp/asset/c4b3f467-29c6-4ff9-b43e-d3c3cef236c6"
const iconInfo = "https://www.figma.com/api/mcp/asset/7f60e5fc-6580-4d1e-9237-a0af4d6eaf39"
const iconUser = "https://www.figma.com/api/mcp/asset/1412af31-2c7a-4991-ab99-7e32515330bd"
const iconPaw = "https://www.figma.com/api/mcp/asset/e86ef917-f44a-47d2-a2e6-b213512f6fac"
const iconPaw2 = "https://www.figma.com/api/mcp/asset/4c9c1bee-4e85-46ba-95ee-23e61d04804c"
const iconService = "https://www.figma.com/api/mcp/asset/bd47d21d-d2c6-4903-8575-929420b34f24"
const iconRoom = "https://www.figma.com/api/mcp/asset/f2f9538c-8a04-4790-8fb3-a12134201835"
const iconPhone = "https://www.figma.com/api/mcp/asset/cff25dfd-3ad4-4080-b0e1-78bd20ac9d16"
const iconCalendar = "https://www.figma.com/api/mcp/asset/39a10a8d-371c-42f1-84cf-3f939847112f"
const iconDoctor = "https://www.figma.com/api/mcp/asset/6990e9b2-7dfa-4dd7-81a5-fe3898c7346d"

// Methods
const closeModal = () => {
  emit('close')
}

const confirmCheckIn = () => {
  emit('confirm', props.patientInfo)
  // TODO: Implement check-in API call
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap');

* {
  font-family: 'Nunito Sans', sans-serif;
}
</style>
