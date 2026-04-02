<template>
  <div class="fixed inset-0 z-0 bg-[#eeeeee] overflow-y-auto top-[64px]">
    <div class="min-h-full flex items-center justify-center p-4 pb-20">
      <div
        class="responsive-modal bg-white rounded-3xl shadow-2xl p-6 lg:p-14 w-full max-w-7xl flex flex-col lg:flex-row items-center justify-center gap-8 lg:gap-16 overflow-hidden"
      >
        <div
          class="relative w-full max-w-lg hidden md:block md:h-64 lg:h-auto flex-shrink-0 rounded-2xl overflow-hidden group cursor-pointer"
        >
          <img
            src="../../../assets/img_imports/sign_in_up_img/sign-img1.png"
            alt="PETTY - Chăm sóc thú cưng"
            class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
          />

          <div
            class="absolute inset-x-4 bottom-6 lg:inset-x-6 lg:bottom-8 max-w-[400px] mx-auto text-white"
          >
            <div
              class="bg-white/30 backdrop-blur-sm border rounded-lg p-3 shadow-md transition-all duration-300 ease-out group-hover:bg-white/80 group-hover:backdrop-blur-md group-hover:shadow-xl group-hover:scale-105 group-hover:border-white/40 group-hover:-translate-y-1"
            >
              <div class="space-y-3">
                <p
                  class="text-gray-900 text-xs font-medium leading-tight italic group-hover:text-gray-900 transition-colors duration-300"
                >
                  "Trước đây mỗi lần đưa Bún đi khám là phải chờ khá lâu, nhưng
                  từ khi có PETTY, tôi chỉ cần đặt lịch trước vài phút là có
                  lịch ngay..."
                </p>
                <div
                  class="border-t border-gray-300/40 pt-2 group-hover:border-gray-400/60 transition-colors duration-300"
                >
                  <p
                    class="text-gray-900 font-semibold text-xs group-hover:text-gray-950 transition-colors duration-300"
                  >
                    Ngọc Trâm
                  </p>
                  <p
                    class="text-gray-600 text-xs italic group-hover:text-gray-700 transition-colors duration-300"
                  >
                    chủ của chú chó Bún
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="w-full max-w-md space-y-6 lg:space-y-8">
          <div class="text-center space-y-2 lg:space-y-4">
            <h1
              class="text-3xl lg:text-5xl font-bold text-teal-800 font-['Montserrat_Alternates']"
            >
              PETTY Xin Chào
            </h1>
            <p class="text-sm lg:text-base text-gray-700 font-medium">
              Bắt đầu trải nghiệm các dịch vụ tại PETTY ngay nào
            </p>
          </div>

          <form @submit.prevent="handleLogin" class="space-y-6">
            <div>
              <label class="block text-base font-medium text-gray-800">
                Email <span class="text-red-600">*</span>
              </label>
              <div class="relative mt-2">
                <div
                  class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"
                >
                  <MailIcon class="h-6 w-6 text-gray-500" aria-hidden="true" />
                </div>
                <input
                  v-model="form.email"
                  ref="emailInput"
                  type="email"
                  placeholder="Nhập email"
                  autocomplete="email"
                  class="w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-teal-600"
                  :class="{ 'border-red-500': errors.email }"
                  aria-invalid="errors.email ? 'true' : 'false'"
                  aria-describedby="email-error"
                  required
                />
                <!-- suggestion when stored user email matches typed email -->
                <div
                  v-if="showStoredSuggestion"
                  class="mt-2 text-sm text-gray-600 flex items-center gap-2"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-teal-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 11c0 1.657-1.343 3-3 3s-3-1.343-3-3 1.343-3 3-3 3 1.343 3 3z"
                    />
                  </svg>
                  <span>Phát hiện tài khoản đã ghi nhớ. </span>
                  <button
                    @click="useRememberedAccount"
                    type="button"
                    class="ml-2 text-teal-700 font-medium hover:underline"
                  >
                    Sử dụng mật khẩu đã lưu
                  </button>
                </div>
                <p
                  v-if="errors.email"
                  id="email-error"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ errors.email[0] }}
                </p>
              </div>
            </div>

            <div>
              <label class="block text-base font-medium text-gray-800">
                Mật Khẩu <span class="text-red-600">*</span>
              </label>
              <div class="relative mt-2 text-gray-300">
                <div
                  class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"
                >
                  <LockIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                </div>
                <input
                  v-model="form.password"
                  ref="passwordInput"
                  type="password"
                  placeholder="Nhập mật khẩu"
                  autocomplete="current-password"
                  class="w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-teal-600"
                  :class="{ 'border-red-500': errors.password }"
                  aria-invalid="errors.password ? 'true' : 'false'"
                  aria-describedby="password-error"
                  required
                />
                <p
                  v-if="errors.password"
                  id="password-error"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ errors.password[0] }}
                </p>
              </div>
            </div>

            <div class="flex justify-between items-center text-sm">
              <a href="#" class="text-gray-500 hover:text-teal-700 font-medium"
                >Quên Mật Khẩu?</a
              >
              <div class="flex items-center gap-2">
                <span class="text-gray-500 font-medium">Ghi nhớ đăng nhập</span>
                <button
                  type="button"
                  @click="toggleRemember"
                  :class="[
                    'w-14 h-7 rounded-full p-1 transition flex items-center',
                    rememberMe
                      ? 'bg-gradient-to-r from-teal-500 to-teal-700 justify-end'
                      : 'bg-gray-300 justify-start',
                  ]"
                >
                  <div class="w-5 h-5 bg-white rounded-full shadow"></div>
                </button>
              </div>
            </div>

            <button
              type="submit"
              class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3.5 rounded-2xl transition shadow-md text-lg"
            >
              Đăng Nhập
            </button>
          </form>

          <div class="relative text-center text-sm text-gray-500 my-6">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <span class="relative bg-white px-3 font-medium">Hoặc</span>
          </div>

          <div class="flex justify-center gap-5">
            <button
              @click="handleGoogleLogin"
              class="p-3.5 bg-gray-200 rounded-xl hover:scale-110 transition shadow-sm"
            >
              <GoogleIcon class="w-8 h-8" />
            </button>
            <button
              @click="handleFacebookLogin"
              class="p-3.5 bg-gray-200 rounded-xl hover:scale-110 transition shadow-sm"
            >
              <FacebookIcon class="w-8 h-8" />
            </button>
          </div>

          <p class="text-center text-base mt-6">
            <span class="text-gray-600">Chưa có tài khoản?</span>
            <router-link to="/khach-hang/dang-ky">
              <a
                href="/khach-hang/dang-ky"
                class="text-blue-600 font-bold hover:underline ml-1"
                >Đăng Ký</a
              >
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Import các thư viện cần thiết
import { ref, nextTick, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import { useToast } from "vue-toastification";
import { showInfoToast } from "@/utils/toast";
import { setAuth, getToken, getUser } from "@/utils/auth";
import MailIcon from "@/assets/svg/mail.svg";
import LockIcon from "@/assets/svg/password.svg";
import GoogleIcon from "@/assets/svg/google.svg";
import FacebookIcon from "@/assets/svg/facebook.svg";

const router = useRouter();
const route = useRoute();
const toast = useToast();

const form = ref({
  email: "",
  password: "",
});

const errors = ref({});
const isSubmitting = ref(false);
const rememberMe = ref(true);

const emailInput = ref(null);
const passwordInput = ref(null);
const agreeRef = ref(null);

// Lấy thông tin user đã lưu (nếu có)
let storedUser = getUser("customer");

if (storedUser?.email) {
  form.value.email = storedUser.email;
}

const showStoredSuggestion = ref(false);

const focusByKey = async (key) => {
  await nextTick();
  if (key === "email") emailInput.value?.focus();
  else if (key === "password") passwordInput.value?.focus();
};

const checkShowSuggestion = () => {
  const e = (form.value.email || "").toLowerCase();
  showStoredSuggestion.value =
    storedUser && storedUser.email && e === storedUser.email.toLowerCase();
};

// Kiểm tra xem đã đăng nhập chưa
const existingToken = getToken("customer");
if (existingToken) {
  router.push("/");
}

// If user was redirected here because they tried to access a protected page,
// show an informational toast asking them to login first using our styled toast.
if (route.query && route.query.redirect) {
  showInfoToast("Thông báo", "Vui lòng đăng nhập để tiếp tục");
}

// Xử lý đăng nhập
const handleLogin = async () => {
  errors.value = {};
  isSubmitting.value = true;

  if (!form.value.email) {
    toast.error("Vui lòng nhập email");
    await nextTick();
    emailInput.value?.focus();
    isSubmitting.value = false;
    return;
  }
  if (!form.value.password) {
    toast.error("Vui lòng nhập mật khẩu");
    await nextTick();
    passwordInput.value?.focus();
    isSubmitting.value = false;
    return;
  }

  try {
    const payload = {
      email: form.value.email,
      password: form.value.password,
    };
    const res = await axios.post(
      "http://127.0.0.1:8000/api/khach-hang/dang-nhap",
      payload
    );
    if (res.data && res.data.status) {
      toast.success(res.data.message || "Đăng nhập thành công");
      const token = res.data.token;
      if (token) {
        setAuth(token, res.data.data || null, rememberMe.value, "customer");
      }

      // After login, redirect to original page if provided
      const redirectPath =
        route.query && route.query.redirect
          ? String(route.query.redirect)
          : null;
      if (redirectPath && redirectPath.startsWith("/")) {
        router.push(redirectPath);
      } else {
        router.push("/");
      }
    } else {
      toast.error(res.data.message || "Đăng nhập thất bại");
    }
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {};
      toast.error("Vui lòng kiểm tra lại thông tin đăng nhập");
      if (errors.value.email) focusByKey("email");
      else if (errors.value.password) focusByKey("password");
    } else {
      toast.error(err.response?.data?.message || "Đã có lỗi xảy ra");
    }
  } finally {
    isSubmitting.value = false;
  }
};

const toggleRemember = () => {
  rememberMe.value = !rememberMe.value;
};

const useRememberedAccount = async () => {
  await nextTick();
  passwordInput.value?.focus();

  const token = getToken("customer");
  if (token) {
    router.push("/");
  }
};

watch(
  () => form.value.email,
  () => {
    checkShowSuggestion();
  }
);

// Xử lý đăng nhập Google
const handleGoogleLogin = () => {
  window.location.href = "http://127.0.0.1:8000/api/auth/google";
};

// Xử lý đăng nhập Facebook
const handleFacebookLogin = () => {
  window.location.href = "http://127.0.0.1:8000/api/auth/facebook";
};
</script>

<style scoped>
@font-face {
  font-family: "Montserrat Alternates";
  src: url("https://fonts.googleapis.com/css2?family=Montserrat+Alternates:wght@700&display=swap")
    format("woff2");
  font-weight: 700;
  font-display: swap;
}

@media (max-height: 900px) {
  .responsive-modal {
    transform: scale(0.9);
    transform-origin: center;
  }
}

@media (max-height: 800px) {
  .responsive-modal {
    transform: scale(0.85);
    transform-origin: center;
  }
}

@media (max-height: 700px) {
  .responsive-modal {
    transform: scale(0.8);
    transform-origin: center;
  }
}

@media (max-height: 600px) {
  .responsive-modal {
    transform: none;
    margin-top: 1rem;
    margin-bottom: 1rem;
  }
}
</style>
