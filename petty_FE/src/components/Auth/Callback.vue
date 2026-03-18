<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="text-center">
      <div class="mb-6">
        <div
          class="animate-spin rounded-full h-16 w-16 border-b-4 border-teal-600 mx-auto"
        ></div>
      </div>
      <h2 class="text-2xl font-bold text-gray-800 mb-2">
        Đang xử lý đăng nhập...
      </h2>
      <p class="text-gray-600">Vui lòng đợi trong giây lát</p>
    </div>
  </div>
</template>

<script setup>
// Import các thư viện cần thiết
import { onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useToast } from "vue-toastification";
import axios from "axios";

const router = useRouter();
const route = useRoute();
const toast = useToast();

onMounted(async () => {
  const token = route.query.token;
  const error = route.query.error;

  // Nếu có lỗi từ Backend
  if (error) {
    let errorMessage = "Đăng nhập thất bại. Vui lòng thử lại.";
    
    if (error === "google_login_failed") {
      errorMessage = "Đăng nhập Google thất bại. Vui lòng thử lại.";
    } else if (error === "facebook_login_failed") {
      errorMessage = "Đăng nhập Facebook thất bại. Vui lòng thử lại.";
    }
    
    toast.error(errorMessage);
    router.push("/khach-hang/dang-nhap");
    return;
  }

  // Nếu có token trong URL query
  if (token) {
    try {
      // Lưu token vào localStorage
      localStorage.setItem("auth_token", token);
      
      // Thiết lập Authorization header cho Axios
      axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

      // Gọi API để lấy thông tin người dùng
      const response = await axios.get("http://127.0.0.1:8000/api/user");
      
      if (response.data && response.data.status) {
        // Lưu thông tin người dùng
        localStorage.setItem("auth_user", JSON.stringify(response.data.data));
        
        toast.success("Đăng nhập thành công!");
        
        // Chuyển hướng về trang chủ sau 500ms
        setTimeout(() => {
          router.push("/");
        }, 500);
      } else {
        throw new Error("Không thể lấy thông tin người dùng");
      }
    } catch (err) {
      console.error("Lỗi xử lý callback xác thực:", err);
      toast.error("Có lỗi xảy ra khi xác thực. Vui lòng đăng nhập lại.");
      
      // Xóa token lỗi
      localStorage.removeItem("auth_token");
      localStorage.removeItem("auth_user");
      delete axios.defaults.headers.common["Authorization"];
      
      router.push("/khach-hang/dang-nhap");
    }
  } else {
    // Không có token trong URL -> Có thể Backend trả JSON trực tiếp
    // Kiểm tra xem có dữ liệu trong sessionStorage không (từ cửa sổ popup)
    const socialAuthData = sessionStorage.getItem('social_auth_response');
    
    if (socialAuthData) {
      try {
        const authData = JSON.parse(socialAuthData);
        
        if (authData.status && authData.token) {
          // Lưu token
          localStorage.setItem("auth_token", authData.token);
          
          // Lưu thông tin người dùng
          if (authData.data) {
            localStorage.setItem("auth_user", JSON.stringify(authData.data));
          }
          
          // Thiết lập Authorization header
          axios.defaults.headers.common["Authorization"] = `Bearer ${authData.token}`;
          
          // Xóa dữ liệu tạm thời
          sessionStorage.removeItem('social_auth_response');
          
          toast.success(authData.message || "Đăng nhập thành công!");
          
          setTimeout(() => {
            router.push("/");
          }, 500);
        } else {
          throw new Error("Dữ liệu xác thực không hợp lệ");
        }
      } catch (err) {
        console.error("Lỗi phân tích dữ liệu xác thực mạng xã hội:", err);
        sessionStorage.removeItem('social_auth_response');
        toast.error("Có lỗi xảy ra. Vui lòng thử lại.");
        router.push("/khach-hang/dang-nhap");
      }
    } else {
      // Không có gì cả
      toast.error("Không nhận được thông tin xác thực. Vui lòng thử lại.");
      router.push("/khach-hang/dang-nhap");
    }
  }
});
</script>

<style scoped>
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
