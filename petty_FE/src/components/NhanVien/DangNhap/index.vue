<template>
  <div class="fixed inset-0 z-0 overflow-y-auto top-[64px]">
    <div class="min-h-full flex items-center justify-center p-4 pb-20">
      <div
        class="responsive-modal bg-white border !border-gray-200 rounded-3xl w-full max-w-xl shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group cursor-pointer"
      >
        <div class="flex flex-col gap-4 p-1">
          <div
            class="px-6 pt-6 pb-4 flex flex-col items-center gap-7 text-center"
          >
            <div
              class="bg-teal-600 w-16 h-16 rounded-2xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110"
            >
              <SecurityIcon />
            </div>
            <div class="space-y-4 w-full">
              <h1
                class="text-xl font-bold text-gray-900 transition-colors duration-300 group-hover:text-teal-700"
              >
                Đăng Nhập Nhân Viên
              </h1>
              <p
                class="text-base text-gray-500 transition-colors duration-300 group-hover:text-gray-600 px-5"
              >
                Nhập thông tin đăng nhập của bạn để truy cập hệ thống
              </p>
            </div>
          </div>

          <div class="px-12 pb-8 space-y-8">
            <form @submit.prevent="handleLogin" class="space-y-6">
              <div class="space-y-3 group/input">
                <label
                  for="email"
                  class="text-sm font-semibold text-gray-900 transition-colors duration-200 group-hover/input:text-teal-700"
                >
                  Email
                </label>
                <div class="relative">
                  <EmailAddressIcon
                    class="w-5 h-5 text-gray-400 absolute left-4 top-3.5"
                  />
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    placeholder="nhanvien@pettyvcms.com"
                    class="w-full h-12 bg-gray-50 border !border-gray-300 rounded-lg pl-12 pr-4 text-sm font-medium text-gray-600 focus:outline-none focus:border-teal-600 focus:ring-4 focus:ring-teal-100 transition-all duration-200 group-hover/input:border-teal-400 group-hover/input:shadow-md"
                    required
                  />
                </div>
              </div>

              <div class="space-y-3 group/input">
                <label
                  for="password"
                  class="text-sm font-semibold text-gray-900 transition-colors duration-200 group-hover/input:text-teal-700"
                >
                  Mật khẩu
                </label>
                <div class="relative">
                  <PasswordIcon
                    class="w-5 h-5 text-gray-400 absolute left-4 top-3.5"
                  />
                  <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    placeholder="Nhập mật khẩu"
                    class="w-full h-12 bg-gray-50 border !border-gray-300 rounded-lg pl-12 pr-4 text-sm font-medium text-gray-600 focus:outline-none focus:border-teal-600 focus:ring-4 focus:ring-teal-100 transition-all duration-200 group-hover/input:border-teal-400 group-hover/input:shadow-md"
                    required
                  />
                </div>
              </div>

              <div class="flex items-center gap-3 group/checkbox">
                <input
                  id="remember"
                  v-model="rememberMe"
                  type="checkbox"
                  class="w-4 h-4 bg-gray-50 border border-gray-300 rounded cursor-pointer checked:bg-teal-600 checked:border-teal-600 focus:ring-0 focus:ring-offset-0 transition-all duration-300 appearance-none relative group-hover/checkbox:scale-110 group-hover/checkbox:shadow-md"
                />
                <label
                  for="remember"
                  class="text-sm font-semibold text-gray-900 cursor-pointer select-none transition-colors duration-200 group-hover/checkbox:text-teal-700"
                >
                  Ghi nhớ đăng nhập 30 ngày
                </label>
              </div>

              <button
                type="submit"
                :disabled="isSubmitting"
                class="w-full h-12 bg-teal-600 text-white font-semibold text-lg rounded-lg transition-all duration-300 shadow-sm hover:bg-teal-700 hover:shadow-lg hover:scale-[1.02] active:scale-100 active:shadow-md disabled:bg-gray-400 disabled:cursor-not-allowed disabled:transform-none"
              >
                {{ isSubmitting ? "Đang đăng nhập..." : "Đăng Nhập" }}
              </button>
            </form>

            <div class="text-center text-base group/link">
              <span
                class="text-gray-600 transition-colors duration-200 group-hover/link:text-gray-700"
              >
                Quên mật khẩu?
              </span>
              <a
                href="#"
                class="text-blue-600 font-medium ml-1 transition-all duration-300 hover:text-blue-700 hover:underline hover:underline-offset-4 hover:decoration-2"
              >
                Liên Hệ Quản Trị Viên
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter, useRoute } from "vue-router";
import { useToast } from "vue-toastification";
import { showInfoToast, showSuccessToast } from "@/utils/toast";
// Icon SVG
import SecurityIcon from "@/assets/svg/security.svg";
import EmailAddressIcon from "@/assets/svg/emailaddress.svg";
import PasswordIcon from "@/assets/svg/password.svg";

const router = useRouter();
const route = useRoute();
const toast = useToast();

const rememberMe = ref(false);
const isSubmitting = ref(false);
const errors = ref({});

const form = ref({
  email: "",
  password: "",
});

// If existing token is present, set axios header so components can use it
try {
  const existingToken =
    localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
  if (existingToken)
    axios.defaults.headers.common["Authorization"] = `Bearer ${existingToken}`;
} catch (e) {
  // ignore storage errors
}

// Show toasts on mount so plugin/UI are ready
onMounted(() => {
  if (route.query) {
    if (route.query.redirect || route.query.reason) {
      showInfoToast("Thông báo", "Vui lòng đăng nhập để tiếp tục");
    }
    if (route.query.logged_out) {
      // show success toast when redirected after logout
      showSuccessToast("Thông báo", "Đăng xuất thành công");
    }
  }
});

const handleLogin = async () => {
  errors.value = {};
  if (!form.value.email) {
    toast.error("Vui lòng nhập email");
    return;
  }
  if (!form.value.password) {
    toast.error("Vui lòng nhập mật khẩu");
    return;
  }

  isSubmitting.value = true;
  try {
    const payload = {
      email: form.value.email,
      password: form.value.password,
    };

    // API endpoint for employee login
    const res = await axios.post(
      "http://127.0.0.1:8000/api/nhan-vien/dang-nhap",
      payload
    );

    if (res.data && res.data.status) {
      toast.success(res.data.message || "Đăng nhập thành công");
      const token = res.data.token;
      if (token) {
        // Save token based on remember me option
        if (rememberMe.value) {
          localStorage.setItem("auth_token", token);
          sessionStorage.removeItem("auth_token");
        } else {
          sessionStorage.setItem("auth_token", token);
          localStorage.removeItem("auth_token");
        }

        // Set axios default header for subsequent requests
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

        // Save user data
        if (res.data.data) {
          const userStr = JSON.stringify(res.data.data);
          if (rememberMe.value) {
            localStorage.setItem("auth_user", userStr);
            sessionStorage.removeItem("auth_user");
          } else {
            sessionStorage.setItem("auth_user", userStr);
            localStorage.removeItem("auth_user");
          }

          // Save role for routing
          const role =
            res.data.data.phan_quyen?.ma_vai_tro ||
            res.data.data.vai_tro ||
            "nhan_vien";
          if (rememberMe.value) {
            localStorage.setItem("user_role", role);
          } else {
            sessionStorage.setItem("user_role", role);
          }
        }
      }

      // Redirect based on role or to default dashboard
      // Use redirect_url from backend if available
      let redirectPath = res.data.redirect_url || null;

      // Fallback to query parameter redirect
      if (!redirectPath && route.query && route.query.redirect) {
        redirectPath = String(route.query.redirect);
      }

      // Last fallback: determine based on role
      if (!redirectPath && res.data.data) {
        const role =
          res.data.data.vai_tro || res.data.data.phan_quyen?.ma_vai_tro;
        const roleRoutes = {
          bac_si: "/doctor/dashboard",
          y_ta: "/nurse/dashboard",
          le_tan: "/receptionist/dashboard",
          tro_ly: "/assistant/dashboard",
        };
        redirectPath = roleRoutes[role] || "/dashboard";
      }

      if (redirectPath && redirectPath.startsWith("/")) {
        router.push(redirectPath);
      } else {
        router.push("/dashboard");
      }
    } else {
      toast.error(res.data.message || "Đăng nhập thất bại");
    }
  } catch (err) {
    console.error("Login error:", err);

    if (err.response?.status === 422) {
      // Validation errors
      errors.value = err.response.data.errors || {};
      toast.error("Vui lòng kiểm tra lại thông tin đăng nhập");
    } else if (err.response?.status === 401) {
      // Unauthorized - wrong credentials
      toast.error(
        err.response.data.message || "Email hoặc mật khẩu không đúng"
      );
    } else if (err.response?.status === 403) {
      // Forbidden - account locked
      toast.error(err.response.data.message || "Tài khoản của bạn đã bị khóa");
    } else {
      // Other errors
      toast.error(
        err.response?.data?.message || "Đã có lỗi xảy ra khi đăng nhập"
      );
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
input[type="checkbox"]:checked::after {
  content: "";
  position: absolute;
  left: 5px;
  top: 1px;
  width: 4px;
  height: 8px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

input:focus {
  @apply ring-4 ring-teal-100;
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
