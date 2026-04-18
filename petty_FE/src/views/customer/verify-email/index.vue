<template>
  <div class="fixed inset-0 z-0 bg-[#eeeeee] overflow-y-auto top-[64px]">
    <div class="min-h-full flex items-center justify-center p-4 pb-20">
      <div class="responsive-modal max-w-lg w-full bg-white rounded-3xl shadow-xl p-8 lg:p-12 text-center border-t-4 border-[#5A9690]">
        <!-- Icon section -->
        <div class="mx-auto w-24 h-24 bg-teal-50 rounded-full flex items-center justify-center mb-6 shadow-sm">
          <Mail class="w-12 h-12 text-[#5A9690]" />
        </div>

        <h2 class="text-2xl lg:text-3xl font-bold text-gray-800 font-nunito mb-4">
          Xác thực địa chỉ email
        </h2>

        <p class="text-gray-600 font-nunitoSans mb-10 text-lg leading-relaxed" v-if="email">
          Chúng tôi đã gửi một email xác thực đến địa chỉ<br/>
          <strong class="text-gray-800 bg-gray-100 px-3 py-1 rounded-full inline-block mt-2 mb-2">{{ maskedEmail }}</strong>
          <br/>Vui lòng kiểm tra hộp thư đến (và thư mục rác) để hoàn tất đăng ký.
        </p>

        <p class="text-gray-600 font-nunitoSans mb-8 text-lg" v-else>
          Không tìm thấy địa chỉ email cần xác thực.
        </p>

        <!-- Resend button -->
        <div class="space-y-4">
          <button
            v-if="email && !isVerified"
            @click="resendEmail"
            :disabled="isLoading || countdown > 0"
            class="w-full border-2 border-[#5A9690] text-[#5A9690] py-3.5 rounded-2xl font-semibold text-lg font-nunito hover:bg-[#5A9690] hover:text-white transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <span v-if="isLoading">Đang xử lý...</span>
            <span v-else-if="countdown > 0">Gửi lại email ({{ countdown }}s)</span>
            <span v-else>Gửi lại email xác thực</span>
          </button>

          <button
            @click="goToLogin"
            class="w-full bg-[#5A9690] text-white py-3.5 rounded-2xl font-semibold text-lg font-nunito hover:bg-opacity-90 transition-all duration-300 shadow-md hover:shadow-lg"
          >
            Trở về trang Đăng Nhập
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { Mail } from 'lucide-vue-next';
import axios from 'axios';
import { showSuccessToast, showErrorToast } from '@/utils/toast.js';

const route = useRoute();
const router = useRouter();

const email = ref(route.query.email || '');
const isLoading = ref(false);
const isVerified = ref(false);
const countdown = ref(0);
let timer = null;

const maskedEmail = computed(() => {
  if (!email.value) return '';
  const parts = email.value.split('@');
  if (parts.length !== 2) return email.value;
  const name = parts[0];
  const domain = parts[1];
  
  if (name.length <= 2) return `${name[0]}***@${domain}`;
  return `${name.substring(0, 2)}***@${domain}`;
});

const startCountdown = () => {
  countdown.value = 60;
  timer = setInterval(() => {
    countdown.value--;
    if (countdown.value <= 0) {
      clearInterval(timer);
    }
  }, 1000);
};

const resendEmail = async () => {
  if (!email.value || isLoading.value || countdown.value > 0) return;
  
  isLoading.value = true;
  try {
    const res = await axios.post('http://127.0.0.1:8000/api/khach-hang/gui-lai-xac-nhan', {
      email: email.value
    });
    
    // Status 200: Successfully sent or not found (anti-enumeration)
    showSuccessToast('Email xác thực đã được gửi lại. Vui lòng kiểm tra hộp thư.');
    startCountdown();
  } catch (err) {
    if (err.response?.status === 400) {
      showErrorToast(err.response.data.message || 'Tài khoản đã được xác minh. Vui lòng đăng nhập.');
      isVerified.value = true;
      setTimeout(() => {
        router.push('/khach-hang/dang-nhap');
      }, 2000);
    } else {
      showErrorToast(err.response?.data?.message || 'Có lỗi xảy ra khi gửi lại email. Vui lòng thử lại sau.');
    }
  } finally {
    isLoading.value = false;
  }
};

const goToLogin = () => {
  router.push('/khach-hang/dang-nhap');
};

onMounted(() => {
  if (!email.value) {
    showErrorToast('Không tìm thấy thông tin email.');
    router.push('/khach-hang/dang-ki');
  }
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
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
