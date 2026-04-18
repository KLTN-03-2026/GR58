<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100] p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] flex flex-col relative overflow-hidden">
        <!-- Nút Đóng X -->
        <button
          @click="closeModal"
          class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-full transition-colors z-10"
        >
          <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>

        <div class="p-6 overflow-y-auto">
          <!-- State 1: Unauthenticated -->
          <div v-if="modalState === 'unauthenticated'" class="flex flex-col items-center text-center gap-6 py-4">
            <div class="w-16 h-16 bg-[#5a9690]/20 rounded-full flex items-center justify-center">
              <svg class="w-8 h-8 text-[#2f5755]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </div>
            <div class="flex flex-col gap-2">
              <h3 class="text-2xl font-bold text-[#432323]">Bắt đầu đặt lịch</h3>
              <p class="text-gray-600 font-medium text-sm">Vui lòng đăng nhập để tiếp tục quá trình đặt lịch khám cho thú cưng của bạn.</p>
            </div>
            <div class="flex flex-col gap-3 w-full mt-2">
              <button @click="router.push('/customer/login')" class="w-full bg-[#5a9690] hover:bg-[#3a6b68] text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md">
                Đăng nhập
              </button>
              <button @click="router.push('/customer/register')" class="w-full border-2 border-[#5a9690] text-[#2f5755] hover:bg-[#5a9690]/10 font-semibold py-3 px-4 rounded-xl transition-all">
                Tạo tài khoản mới
              </button>
            </div>
          </div>

          <!-- State 'loading' -->
          <div v-else-if="modalState === 'loading'" class="flex flex-col items-center justify-center gap-4 py-12">
            <svg class="animate-spin h-10 w-10 text-[#5a9690]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <p class="text-gray-500 font-medium">Đang tải thông tin...</p>
          </div>

          <!-- State 'error' -->
          <div v-else-if="modalState === 'error'" class="flex flex-col items-center text-center gap-4 py-8">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
              <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-[#432323]">Đã xảy ra lỗi</h3>
            <p class="text-gray-500 text-sm">Không thể tải thông tin thú cưng. Vui lòng thử lại sau.</p>
            <button @click="fetchPets" :disabled="isRetrying" class="mt-2 bg-[#5a9690] hover:bg-[#3a6b68] text-white font-semibold py-2 px-6 rounded-xl transition-all">
              {{ isRetrying ? 'Đang thử lại...' : 'Thử lại' }}
            </button>
          </div>

          <!-- State 2: No pets -->
          <div v-else-if="modalState === 'no-pets'" class="flex flex-col items-center text-center gap-6 py-6">
            <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center">
              <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.514"/>
              </svg>
            </div>
            <div class="flex flex-col gap-2">
              <h3 class="text-xl font-bold text-[#432323]">Bạn chưa có thú cưng</h3>
              <p class="text-gray-600 font-medium text-sm px-4">Hãy thêm thông tin thú cưng của bạn trước khi bắt đầu đặt lịch khám nhé.</p>
            </div>
            <button @click="router.push('/customer/my-pets?action=add-pet')" class="w-full bg-[#5a9690] hover:bg-[#3a6b68] text-white font-semibold py-3 px-4 rounded-xl transition-all mt-2">
              Thêm thú cưng ngay
            </button>
          </div>

          <!-- State 3: Has pets -->
          <div v-else-if="modalState === 'has-pets'" class="flex flex-col gap-4 py-2">
            <h3 class="text-2xl font-bold text-[#432323] text-center mb-2">Chọn thú cưng cần khám</h3>
            <div class="flex flex-col gap-3">
              <div
                v-for="pet in pets"
                :key="pet.id"
                @click="redirectToBooking(pet.id)"
                class="flex items-center gap-4 p-4 border-2 border-gray-100 bg-gray-50 hover:border-[#5a9690] hover:bg-teal-50 rounded-2xl cursor-pointer transition-all group"
              >
                <!-- Avatar placeholder -->
                <div class="w-14 h-14 bg-[#5a9690]/20 rounded-full flex items-center justify-center shrink-0 border-2 border-white shadow-sm overflow-hidden">
                  <img v-if="pet.image" :src="pet.image" alt="Avatar" class="w-full h-full object-cover" />
                  <svg v-else class="w-7 h-7 text-[#2f5755]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <!-- Pet info -->
                <div class="flex-1 flex flex-col min-w-0">
                  <span class="text-lg font-bold text-[#222831] truncate group-hover:text-[#2f5755] transition-colors">{{ pet.ten_thu_cung || pet.name }}</span>
                  <span class="text-sm font-medium text-gray-500 truncate">{{ pet.giong_thu_cung || pet.breed || 'Không rõ giống' }}</span>
                </div>
                <!-- Arrow Icon -->
                <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm group-hover:bg-[#5a9690] transition-colors group-hover:text-white text-gray-400">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </div>
              </div>
            </div>
            
            <button @click="router.push('/customer/my-pets?action=add-pet')" class="mt-4 text-[#5a9690] font-semibold text-sm hover:underline text-center w-full">
              Thêm thú cưng khác
            </button>
          </div>

        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { getToken } from '@/utils/auth';
import axios from 'axios';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['close']);

const router = useRouter();
const modalState = ref('loading'); // 'loading' | 'unauthenticated' | 'no-pets' | 'has-pets' | 'error'
const pets = ref([]);
const isRetrying = ref(false);

const closeModal = () => {
  emit('close');
};

const redirectToBooking = (petId) => {
  closeModal();
  router.push(`/customer/appointments/book?pet_id=${petId}`);
};

const API_BASE = import.meta.env.VITE_API_BASE || "http://127.0.0.1:8000/api";

const fetchPets = async () => {
  isRetrying.value = true;
  modalState.value = 'loading';
  try {
    const token = getToken('customer');
    if (!token) {
      modalState.value = 'unauthenticated';
      return;
    }

    const res = await axios.get(`${API_BASE}/thu-cung?all=1`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    let list = [];
    if (res.data && res.data.data) {
      if (Array.isArray(res.data.data)) list = res.data.data;
      else if (Array.isArray(res.data.data.data)) list = res.data.data.data;
    }

    pets.value = list;

    if (pets.value.length === 0) {
      modalState.value = 'no-pets';
    } else {
      modalState.value = 'has-pets';
    }
  } catch (err) {
    if (err.response && err.response.status === 401) {
      modalState.value = 'unauthenticated';
    } else {
      console.warn("Lỗi fetch thú cưng Start Modal:", err);
      modalState.value = 'error';
    }
  } finally {
    isRetrying.value = false;
  }
};

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    fetchPets();
  }
});
</script>
