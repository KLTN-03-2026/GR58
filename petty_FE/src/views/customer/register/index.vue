<template>
  <div class="fixed inset-0 z-0 bg-[#eeeeee] overflow-y-auto top-[64px]">
    <div class="min-h-full flex items-center justify-center p-4 pb-20">
      <div
        class="responsive-modal max-w-6xl w-full bg-white rounded-3xl shadow-xl overflow-hidden"
      >
        <div class="grid grid-cols-1 lg:grid-cols-2 min-h-full">
          <!-- Left Side - Info (hidden on mobile for better UX or stacked) -->
          <!-- We keep it visible but adjust padding and order -->
          <div
            class="bg-gradient-to-br from-[#5a9690] to-[#3d7671] p-6 lg:p-12 text-white relative"
          >
            <div class="mb-8 lg:mb-16">
              <h1
                class="text-white text-2xl lg:text-3xl font-bold font-nunito mb-2"
              >
                Chào mừng bạn đến với PETTY
              </h1>
              <p class="text-blue-100 font-nunitoSans text-sm lg:text-base">
                Tham gia cùng hàng nghìn chủ nuôi đang sử dụng để đặt lịch
              </p>
            </div>

            <div class="mb-8 lg:mb-28 rounded-xl overflow-hidden">
              <img
                src="/src/assets/img_imports/sign_in_up_img/sign-img2.png"
                alt="Veterinarian with pet"
                class="w-full h-48 lg:h-80 object-cover rounded-lg"
              />
            </div>

            <div class="space-y-4 hidden lg:block">
              <div class="flex items-start gap-3">
                <Tick class="w-5 h-5 mt-1 flex-shrink-0 text-white" />
                <div>
                  <h3 class="text-white text-lg font-semibold font-nunitoSans">
                    Bảo mật & An toàn
                  </h3>
                  <p class="text-sm text-blue-100 font-nunitoSans">
                    Mọi thông tin của bạn và thú cưng được lưu trữ bảo mật
                  </p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <Tick class="w-5 h-5 mt-1 flex-shrink-0 text-white" />
                <div>
                  <h3 class="text-white text-lg font-semibold font-nunitoSans">
                    24/7 Hỗ Trợ
                  </h3>
                  <p class="text-sm text-blue-100 font-nunitoSans">
                    Đội ngũ PETTY luôn sẵn sàng giúp bạn khi cần
                  </p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <Tick class="w-5 h-5 mt-1 flex-shrink-0 text-white" />
                <div>
                  <h3 class="text-white text-lg font-semibold font-nunitoSans">
                    Sử dụng dễ dàng
                  </h3>
                  <p class="text-sm text-blue-100 font-nunitoSans">
                    Bắt đầu chỉ với vài thao tác đơn giản
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Side - Form -->
          <div class="p-6 lg:p-12 bg-white">
            <div class="mb-6 lg:mb-8">
              <h2
                class="text-xl lg:text-2xl font-bold text-textColor font-nunito mb-1"
              >
                Tạo tài khoản
              </h2>
              <p class="text-lg text-gray-400 font-semibold font-nunitoSans">
                Bắt đầu để khám phá ngay nhé
              </p>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-5">
              <!-- Họ và Tên -->
              <div>
                <label
                  class="block text-sm font-semibold text-textColor mb-2 font-nunitoSans"
                  >Họ và Tên</label
                >
                <div class="relative">
                  <div
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                  >
                    <UserIcon class="w-6 h-6" />
                  </div>
                  <input
                    v-model="formData.full_name"
                    type="text"
                    placeholder="Nguyễn Văn A"
                    class="w-full pl-14 pr-3 py-3 bg-gray-100 border-0 rounded-lg text-base font-nunitoSans text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary"
                    :class="{ 'border-red-500': errors.full_name }"
                    ref="fullNameInput"
                  />
                </div>
                <p v-if="errors.full_name" class="mt-1 text-sm text-red-600">
                  {{ errors.full_name[0] }}
                </p>
              </div>

              <!-- Email -->
              <div>
                <label
                  class="block text-sm font-semibold text-textColor mb-2 font-nunitoSans"
                  >Email</label
                >
                <div class="relative">
                  <div
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                  >
                    <EmailAddressIcon class="w-6 h-6" />
                  </div>
                  <input
                    v-model="formData.email"
                    type="email"
                    placeholder="nguyenvana@example.com"
                    class="w-full pl-14 pr-3 py-3 bg-gray-100 border-0 rounded-lg text-base font-nunitoSans text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary"
                    :class="{ 'border-red-500': errors.email }"
                    ref="emailInput"
                  />
                </div>
                <p v-if="errors.email" class="mt-1 text-sm text-red-600">
                  {{ errors.email[0] }}
                </p>
              </div>

              <!-- Mật khẩu -->
              <div>
                <label
                  class="block text-sm font-semibold text-textColor mb-2 font-nunitoSans"
                  >Mật Khẩu</label
                >
                <div class="relative">
                  <div
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                  >
                    <PasswordIcon class="w-5 h-5" />
                  </div>
                  <input
                    v-model="formData.password"
                    type="password"
                    placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)"
                    class="w-full pl-14 pr-3 py-3 bg-gray-100 border-0 rounded-lg text-base font-nunitoSans text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary"
                    :class="{ 'border-red-500': errors.password }"
                    ref="passwordInput"
                  />
                </div>
                <p v-if="errors.password" class="mt-1 text-sm text-red-600">
                  {{ errors.password[0] }}
                </p>
              </div>

              <!-- Xác nhận mật khẩu -->
              <div>
                <label
                  class="block text-sm font-semibold text-textColor mb-2 font-nunitoSans"
                  >Xác nhận mật khẩu</label
                >
                <div class="relative">
                  <div
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                  >
                    <PasswordIcon class="w-5 h-5" />
                  </div>
                  <input
                    v-model="formData.confirmPassword"
                    type="password"
                    placeholder="Nhập lại mật khẩu"
                    class="w-full pl-14 pr-3 py-3 bg-gray-100 border-0 rounded-lg text-base font-nunitoSans text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary"
                    ref="confirmInput"
                  />
                </div>
                <p v-if="passwordMismatch" class="mt-1 text-sm text-red-600">
                  Mật khẩu xác nhận không khớp
                </p>
              </div>

              <!-- Điều khoản -->
              <div class="flex items-start gap-2 pt-2">
                <input
                  v-model="formData.agreeTerms"
                  type="checkbox"
                  id="terms"
                  class="w-4 h-4 mt-0.5 bg-gray-100 border-gray-300 rounded focus:ring-2 focus:ring-primary"
                  ref="agreeRef"
                />
                <label
                  for="terms"
                  class="text-sm font-semibold text-gray-900 font-nunitoSans"
                >
                  Tôi đồng ý với các
                  <a href="#" class="text-blue-600 font-bold hover:underline"
                    >Điều Khoản</a
                  >
                  và
                  <a href="#" class="text-blue-600 font-bold hover:underline"
                    >Chính Sách Bảo Mật</a
                  >
                </label>
              </div>
              <p v-if="errors.agreeTerms" class="text-sm text-red-600 -mt-3">
                Bạn cần đồng ý với điều khoản
              </p>

              <!-- Nút Đăng ký -->
              <button
                type="submit"
                :disabled="isSubmitting"
                class="w-full bg-[#5A9690] text-white py-3 rounded-2xl font-semibold text-lg font-nunito hover:bg-opacity-90 transition-all duration-300 disabled:opacity-70"
              >
                <span v-if="!isSubmitting">Đăng Ký</span>
                <span v-else>Đang xử lý...</span>
              </button>

              <!-- Divider -->
              <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                  <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                  <span class="px-4 bg-white text-gray-600"
                    >Hoặc tiếp tục với</span
                  >
                </div>
              </div>

              <!-- Social Buttons -->
              <div class="grid grid-cols-2 gap-3">
                <button
                  type="button"
                  @click="handleGoogleLogin"
                  class="flex items-center justify-center gap-2 px-4 py-2 shadow-md rounded-lg text-sm font-semibold text-gray-900 hover:bg-gray-100 transition"
                >
                  <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path
                      fill="#4285F4"
                      d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                    />
                    <path
                      fill="#34A853"
                      d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                    />
                    <path
                      fill="#FBBC05"
                      d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                    />
                    <path
                      fill="#EA4335"
                      d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                    />
                  </svg>
                  Google
                </button>
                <button
                  type="button"
                  @click="handleFacebookLogin"
                  class="flex items-center justify-center gap-2 px-4 py-2 shadow-md rounded-lg text-sm font-semibold text-gray-900 hover:bg-gray-100 transition"
                >
                  <svg class="w-5 h-5" fill="#1877F2" viewBox="0 0 24 24">
                    <path
                      d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                    />
                  </svg>
                  Facebook
                </button>
              </div>
            </form>

            <div class="mt-6 text-center">
              <p class="text-sm text-gray-600 font-nunitoSans">
                Đã có tài khoản?
                <router-link
                  to="/khach-hang/dang-nhap"
                  class="text-blue-600 font-medium hover:underline ml-1"
                >
                  Đăng Nhập
                </router-link>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Import các thư viện cần thiết
import { ref, nextTick } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { showSuccessToast, showErrorToast } from "@/utils/toast.js";
import Tick from "@/assets/svg/tick.svg";
import UserIcon from "@/assets/svg/User.svg";
import EmailAddressIcon from "@/assets/svg/emailaddress.svg";
import PasswordIcon from "@/assets/svg/password.svg";

const router = useRouter();

const formData = ref({
  full_name: "",
  email: "",
  password: "",
  confirmPassword: "",
  agreeTerms: false,
});

const errors = ref({});
const isSubmitting = ref(false);
const passwordMismatch = ref(false);

const fullNameInput = ref(null);
const emailInput = ref(null);
const passwordInput = ref(null);
const confirmInput = ref(null);
const agreeRef = ref(null);

const inputRefs = {
  full_name: fullNameInput,
  email: emailInput,
  password: passwordInput,
  confirmPassword: confirmInput,
  agreeTerms: agreeRef,
};

const focusByKey = (key) => {
  const r = inputRefs[key];
  if (r && r.value && typeof r.value.focus === "function") {
    try {
      r.value.scrollIntoView?.({ behavior: "smooth", block: "center" });
    } catch (e) {}
    r.value.focus();
  }
};

const focusFirstError = (errs) => {
  const order = ["full_name", "email", "password", "agreeTerms"];
  for (const k of order) {
    if (errs && errs[k]) {
      focusByKey(k);
      break;
    }
  }
};

const handleSubmit = async () => {
  errors.value = {};
  passwordMismatch.value = false;
  isSubmitting.value = true;

  const required = ["full_name", "email", "password", "confirmPassword"];
  const missingKey = required.find((k) => !formData.value[k]);
  if (missingKey) {
    showErrorToast("Vui lòng điền đầy đủ các trường bắt buộc");
    isSubmitting.value = false;
    await nextTick();
    focusByKey(missingKey);
    return;
  }

  if (formData.value.password !== formData.value.confirmPassword) {
    passwordMismatch.value = true;
    isSubmitting.value = false;
    await nextTick();
    focusByKey("password");
    return;
  }

  if (!formData.value.agreeTerms) {
    errors.value.agreeTerms = ["Bạn cần đồng ý với điều khoản"];
    showErrorToast("Bạn cần đồng ý với Điều khoản và Chính sách bảo mật");
    isSubmitting.value = false;
    await nextTick();
    focusByKey("agreeTerms");
    return;
  }

  const payload = {
    full_name: formData.value.full_name,
    email: formData.value.email,
    password: formData.value.password,
  };

  try {
    const res = await axios.post(
      "http://127.0.0.1:8000/api/khach-hang/dang-ki",
      payload
    );

    if (res.data.status) {
      router.push('/verify-email?email=' + encodeURIComponent(formData.value.email));
    }
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors;
      showErrorToast("Vui lòng kiểm tra lại thông tin đăng ký");
      await nextTick();
      focusFirstError(errors.value);
    } else {
      showErrorToast(
        err.response?.data?.message || "Đã có lỗi xảy ra, vui lòng thử lại"
      );
    }
  } finally {
    isSubmitting.value = false;
  }
};

// Xử lý đăng nhập Google
const handleGoogleLogin = () => {
  const width = 500;
  const height = 600;
  const left = window.screen.width / 2 - width / 2;
  const top = window.screen.height / 2 - height / 2;

  // Mở popup đăng nhập Google
  const popup = window.open(
    "http://127.0.0.1:8000/api/auth/google",
    "Google Login",
    `width=${width},height=${height},left=${left},top=${top}`
  );

  // Lắng nghe thông điệp từ cửa sổ popup
  const messageHandler = (event) => {
    // Kiểm tra nguồn gốc để đảm bảo bảo mật
    if (event.origin !== "http://127.0.0.1:8000") return;

    const authData = event.data;

    if (authData && authData.status && authData.token) {
      // Lưu token và user data
      localStorage.setItem("auth_token", authData.token);
      if (authData.data) {
        localStorage.setItem("auth_user", JSON.stringify(authData.data));
      }

      // Đóng popup
      if (popup) popup.close();

      // Hiển thị thông báo
      showSuccessToast("Đăng nhập Google thành công!");

      // Redirect về trang chủ
      setTimeout(() => {
        router.push("/");
        window.location.reload(); // Reload để cập nhật header
      }, 500);

      // Xóa listener
      window.removeEventListener("message", messageHandler);
    }
  };

  window.addEventListener("message", messageHandler);
};

// Xử lý đăng nhập Facebook
const handleFacebookLogin = () => {
  const width = 500;
  const height = 600;
  const left = window.screen.width / 2 - width / 2;
  const top = window.screen.height / 2 - height / 2;

  // Mở popup đăng nhập Facebook
  const popup = window.open(
    "http://127.0.0.1:8000/api/auth/facebook",
    "Facebook Login",
    `width=${width},height=${height},left=${left},top=${top}`
  );

  // Lắng nghe message từ popup
  const messageHandler = (event) => {
    if (event.origin !== "http://127.0.0.1:8000") return;

    const authData = event.data;

    if (authData && authData.status && authData.token) {
      localStorage.setItem("auth_token", authData.token);
      if (authData.data) {
        localStorage.setItem("auth_user", JSON.stringify(authData.data));
      }

      if (popup) popup.close();

      showSuccessToast("Đăng nhập Facebook thành công!");

      setTimeout(() => {
        router.push("/");
        window.location.reload();
      }, 500);

      window.removeEventListener("message", messageHandler);
    }
  };

  window.addEventListener("message", messageHandler);
};
</script>

<style scoped>
.border-red-500 {
  border: 1px solid #ef4444 !important;
}

@media (max-height: 900px) {
  .responsive-modal {
    transform: scale(0.9);
    transform-origin: center;
  }
}

@media (max-height: 800px) {
  .responsive-modal {
    transform: scale(0.8);
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
