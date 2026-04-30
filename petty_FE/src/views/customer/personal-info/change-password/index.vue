<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-8">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h2 class="text-xl font-bold text-gray-900">Đổi mật khẩu</h2>
          <p class="text-sm text-gray-500 mt-0.5">Nhập mật khẩu hiện tại và mật khẩu mới</p>
        </div>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Mật khẩu hiện tại -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Mật khẩu hiện tại *</label>
          <div class="relative">
            <input
              v-model="form.mat_khau_cu"
              :type="show.cu ? 'text' : 'password'"
              placeholder="Nhập mật khẩu hiện tại"
              class="w-full h-11 border rounded-lg px-3 pr-10 text-sm focus:outline-none focus:border-teal-500"
              :class="errors.mat_khau_cu ? 'border-red-400' : 'border-gray-300'"
            />
            <button type="button" @click="show.cu = !show.cu" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
              <EyeOff v-if="show.cu" :size="16" />
              <Eye v-else :size="16" />
            </button>
          </div>
          <p v-if="errors.mat_khau_cu" class="text-red-500 text-xs mt-1">{{ errors.mat_khau_cu }}</p>
        </div>

        <!-- Mật khẩu mới -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Mật khẩu mới *</label>
          <div class="relative">
            <input
              v-model="form.mat_khau_moi"
              :type="show.moi ? 'text' : 'password'"
              placeholder="Tối thiểu 8 ký tự"
              class="w-full h-11 border rounded-lg px-3 pr-10 text-sm focus:outline-none focus:border-teal-500"
              :class="errors.mat_khau_moi ? 'border-red-400' : 'border-gray-300'"
            />
            <button type="button" @click="show.moi = !show.moi" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
              <EyeOff v-if="show.moi" :size="16" />
              <Eye v-else :size="16" />
            </button>
          </div>
          <p v-if="errors.mat_khau_moi" class="text-red-500 text-xs mt-1">{{ errors.mat_khau_moi }}</p>
        </div>

        <!-- Xác nhận mật khẩu -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Xác nhận mật khẩu mới *</label>
          <div class="relative">
            <input
              v-model="form.xac_nhan_mat_khau"
              :type="show.xacNhan ? 'text' : 'password'"
              placeholder="Nhập lại mật khẩu mới"
              class="w-full h-11 border rounded-lg px-3 pr-10 text-sm focus:outline-none focus:border-teal-500"
              :class="errors.xac_nhan_mat_khau ? 'border-red-400' : 'border-gray-300'"
            />
            <button type="button" @click="show.xacNhan = !show.xacNhan" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
              <EyeOff v-if="show.xacNhan" :size="16" />
              <Eye v-else :size="16" />
            </button>
          </div>
          <p v-if="errors.xac_nhan_mat_khau" class="text-red-500 text-xs mt-1">{{ errors.xac_nhan_mat_khau }}</p>
        </div>

        <div class="flex gap-3 pt-2">
          <button
            type="button"
            @click="$emit('close')"
            class="flex-1 h-11 border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition"
          >
            Hủy
          </button>
          <button
            type="submit"
            :disabled="submitting"
            class="flex-1 h-11 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition disabled:opacity-50"
          >
            {{ submitting ? 'Đang lưu...' : 'Xác nhận' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { Eye, EyeOff } from "lucide-vue-next";
import client from "@/utils/api";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
import { logout } from "@/utils/auth";

const emit = defineEmits(["close"]);

const form = ref({ mat_khau_cu: "", mat_khau_moi: "", xac_nhan_mat_khau: "" });
const errors = ref({});
const show = ref({ cu: false, moi: false, xacNhan: false });
const submitting = ref(false);

const submit = async () => {
  errors.value = {};

  if (!form.value.mat_khau_cu) {
    errors.value.mat_khau_cu = "Vui lòng nhập mật khẩu hiện tại.";
    return;
  }
  if (!form.value.mat_khau_moi || form.value.mat_khau_moi.length < 8) {
    errors.value.mat_khau_moi = "Mật khẩu mới phải có ít nhất 8 ký tự.";
    return;
  }
  if (form.value.mat_khau_moi !== form.value.xac_nhan_mat_khau) {
    errors.value.xac_nhan_mat_khau = "Xác nhận mật khẩu không khớp.";
    return;
  }

  submitting.value = true;
  try {
    await client.post("/khach-hang/doi-mat-khau", form.value);
    showSuccessToast("Thành công", "Mật khẩu đã được cập nhật. Vui lòng đăng nhập lại.");
    emit("close");
    setTimeout(() => logout(), 1500);
  } catch (e) {
    const respErrors = e?.response?.data?.errors || {};
    if (Object.keys(respErrors).length) {
      errors.value = Object.fromEntries(
        Object.entries(respErrors).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
      );
    } else {
      showErrorToast("Lỗi", e?.response?.data?.message || "Đổi mật khẩu thất bại.");
    }
  } finally {
    submitting.value = false;
  }
};
</script>
