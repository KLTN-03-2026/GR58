<template>
  <div class="fixed inset-0 z-0 bg-[#eeeeee] overflow-y-auto top-[64px]">
    <div class="min-h-full flex items-center justify-center p-4 pb-20">
      <div class="responsive-modal max-w-md w-full bg-white rounded-3xl shadow-xl p-8 lg:p-12 text-center border-t-4 border-[#5A9690]">
        
        <!-- Loading UI -->
        <div v-if="status === 'loading'" class="py-8">
          <Loader2 class="w-16 h-16 text-[#5A9690] animate-spin mx-auto mb-6" />
          <h2 class="text-2xl font-bold text-gray-800 font-nunito mb-2">Đang xử lý</h2>
          <p class="text-gray-600 font-nunitoSans">Đang xác thực tài khoản của bạn...</p>
        </div>

        <!-- Success UI -->
        <div v-else-if="status === 'success'" class="py-4">
          <div class="mx-auto w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mb-6 shadow-sm border border-green-100">
            <CheckCircle2 class="w-12 h-12 text-green-500" />
          </div>
          <h2 class="text-2xl lg:text-3xl font-bold text-gray-800 font-nunito mb-4">Xác thực thành công!</h2>
          <p class="text-gray-600 font-nunitoSans mb-10 text-lg leading-relaxed">
            Tài khoản của bạn đã được kích hoạt. Hãy đăng nhập để bắt đầu trải nghiệm dịch vụ của PETTY.
          </p>
          <button
            @click="goToLogin"
            class="w-full bg-[#5A9690] text-white py-3.5 rounded-2xl font-semibold text-lg font-nunito hover:bg-opacity-90 transition-all duration-300 shadow-md hover:shadow-lg"
          >
            Đăng Nhập Ngay
          </button>
        </div>

        <!-- Error UI -->
        <div v-else-if="status === 'error'" class="py-4">
          <div class="mx-auto w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mb-6 shadow-sm border border-red-100">
            <XCircle class="w-12 h-12 text-red-500" />
          </div>
          <h2 class="text-2xl lg:text-3xl font-bold text-gray-800 font-nunito mb-4">Xác thực thất bại</h2>
          <p class="text-gray-600 font-nunitoSans mb-10 text-lg leading-relaxed">
            {{ errorMessage || 'Link xác thực không hợp lệ hoặc đã hết hạn.' }}
          </p>
          <div class="space-y-4">
            <button
              @click="goToLogin"
              class="w-full border-2 border-[#5A9690] text-[#5A9690] py-3.5 rounded-2xl font-semibold text-lg font-nunito hover:bg-[#5A9690] hover:text-white transition-all duration-300"
            >
              Về trang Đăng Nhập
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { Loader2, CheckCircle2, XCircle } from 'lucide-vue-next';
import axios from 'axios';
import { setAuth, clearAuth } from '@/utils/auth.js';

const route = useRoute();
const router = useRouter();

const status = ref('loading'); // 'loading', 'success', 'error'
const errorMessage = ref('');

const goToLogin = () => {
  router.push('/khach-hang/dang-nhap');
};

const verifyEmail = async () => {
  const token = route.query.token;
  
  if (!token) {
    status.value = 'error';
    errorMessage.value = 'Không tìm thấy mã xác thực trong đường dẫn.';
    return;
  }

  try {
    // Set token temporary for API call
    setAuth(token, null, true, 'customer');
    
    // Validate token with server
    const res = await axios.get('http://127.0.0.1:8000/api/user');
    
    // Server returns success response and user data
    if (res.status === 200) {
      // Save full user data
      setAuth(token, res.data, true, 'customer');
      status.value = 'success';
      
      // Chuyển hướng sau 2 giây
      setTimeout(() => {
        router.push('/');
      }, 2000);
    }
  } catch (err) {
    clearAuth('customer');
    status.value = 'error';
    if (err.response && err.response.data && err.response.data.message) {
      errorMessage.value = err.response.data.message;
    } else {
      errorMessage.value = 'Hệ thống đang gặp sự cố. Vui lòng thử lại sau.';
    }
  }
};

onMounted(() => {
  verifyEmail();
});
</script>

<style scoped>
@media (max-height: 900px) {
  .responsive-modal {
    transform: scale(0.95);
    transform-origin: center;
  }
}
@media (max-height: 700px) {
  .responsive-modal {
    transform: scale(0.9);
    transform-origin: center;
  }
}
</style>
