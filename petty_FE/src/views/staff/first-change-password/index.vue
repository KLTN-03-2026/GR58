<template>
  <div class="fixed inset-0 z-0 overflow-y-auto top-[64px]">
    <div class="min-h-full flex items-center justify-center p-4 pb-20">
      <div class="bg-white border !border-gray-200 rounded-3xl w-full max-w-xl shadow-sm">
        <div class="flex flex-col gap-4 p-1">
          <!-- Header -->
          <div class="px-6 pt-8 pb-4 flex flex-col items-center gap-5 text-center">
            <div class="bg-teal-600 w-16 h-16 rounded-2xl flex items-center justify-center">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
              </svg>
            </div>
            <div class="space-y-2">
              <h1 class="text-xl font-bold text-gray-900">Đặt mật khẩu mới</h1>
              <p class="text-sm text-gray-500 px-4">
                Đây là lần đầu bạn đăng nhập. Vui lòng đặt mật khẩu mới để bảo mật tài khoản.
              </p>
            </div>
          </div>

          <!-- Info banner -->
          <div class="mx-6 bg-teal-50 border !border-teal-200 rounded-xl px-4 py-3 flex items-start gap-3">
            <svg class="w-5 h-5 text-teal-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm text-teal-700">
              Mật khẩu mới phải có ít nhất <strong>8 ký tự</strong>. Không được dùng lại mật khẩu từ email.
            </p>
          </div>

          <!-- Form -->
          <form @submit.prevent="handleSubmit" class="px-12 pb-8 space-y-5 mt-2">
            <!-- New password -->
            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-900">Mật khẩu mới *</label>
              <div class="relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Nhập mật khẩu mới"
                  class="w-full h-12 bg-gray-50 border !border-gray-300 rounded-lg pl-12 pr-12 text-sm font-medium text-gray-600 focus:outline-none focus:border-teal-600 focus:ring-4 focus:ring-teal-100 transition-all"
                  required
                />
                <button type="button" @click="showPassword = !showPassword"
                  class="absolute right-4 top-3.5 text-gray-400 hover:text-gray-600">
                  <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                  </svg>
                </button>
              </div>
              <!-- Strength indicator -->
              <div v-if="form.password" class="flex gap-1 mt-1">
                <div v-for="i in 4" :key="i"
                  class="h-1 flex-1 rounded-full transition-all"
                  :class="i <= passwordStrength ? strengthColor : 'bg-gray-200'"
                />
              </div>
              <p v-if="form.password" class="text-xs" :class="strengthTextColor">{{ strengthLabel }}</p>
            </div>

            <!-- Confirm password -->
            <div class="space-y-2">
              <label class="text-sm font-semibold text-gray-900">Xác nhận mật khẩu *</label>
              <div class="relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <input
                  v-model="form.password_confirmation"
                  :type="showConfirm ? 'text' : 'password'"
                  placeholder="Nhập lại mật khẩu mới"
                  class="w-full h-12 bg-gray-50 border !border-gray-300 rounded-lg pl-12 pr-12 text-sm font-medium text-gray-600 focus:outline-none focus:border-teal-600 focus:ring-4 focus:ring-teal-100 transition-all"
                  :class="form.password_confirmation && form.password !== form.password_confirmation ? '!border-red-400' : ''"
                  required
                />
                <button type="button" @click="showConfirm = !showConfirm"
                  class="absolute right-4 top-3.5 text-gray-400 hover:text-gray-600">
                  <svg v-if="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                  </svg>
                </button>
              </div>
              <p v-if="form.password_confirmation && form.password !== form.password_confirmation"
                class="text-xs text-red-500">Mật khẩu xác nhận không khớp.</p>
            </div>

            <!-- Submit -->
            <button
              type="submit"
              :disabled="isSubmitting || !isValid"
              class="w-full h-12 bg-teal-600 text-white font-semibold text-base rounded-lg transition-all hover:bg-teal-700 hover:shadow-lg disabled:bg-gray-300 disabled:cursor-not-allowed mt-2"
            >
              {{ isSubmitting ? 'Đang lưu...' : '🔐 Xác nhận mật khẩu mới' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { getToken, getUser } from '@/utils/auth'

const router = useRouter()

const form = ref({ password: '', password_confirmation: '' })
const isSubmitting = ref(false)
const showPassword = ref(false)
const showConfirm  = ref(false)

// Lấy vai trò thực từ user đã lưu (ưu tiên hơn user_role string)
function getCurrentVaiTro() {
  // Thử lấy user từ từng slot theo thứ tự
  for (const role of ['y_ta', 'bac_si', 'le_tan', 'tro_ly']) {
    const user = getUser(role)
    if (user && user.vai_tro) return user.vai_tro
  }
  // Fallback về user_role đã lưu
  return localStorage.getItem('user_role') || sessionStorage.getItem('user_role') || ''
}

// Lấy token đúng theo vai trò thực
function getCurrentToken() {
  const vaiTro = getCurrentVaiTro()
  if (vaiTro) {
    const t = getToken(vaiTro)
    if (t) return t
  }
  // Fallback: thử từng slot
  for (const role of ['y_ta', 'bac_si', 'le_tan', 'tro_ly', 'staff']) {
    const t = getToken(role)
    if (t) return t
  }
  return null
}

// Password strength
const passwordStrength = computed(() => {
  const p = form.value.password
  if (!p) return 0
  let score = 0
  if (p.length >= 8)  score++
  if (p.length >= 12) score++
  if (/[A-Z]/.test(p) && /[a-z]/.test(p)) score++
  if (/[0-9]/.test(p) || /[^A-Za-z0-9]/.test(p)) score++
  return Math.max(1, score)
})

const strengthColor = computed(() => {
  const colors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500']
  return colors[passwordStrength.value - 1] || 'bg-gray-200'
})

const strengthLabel = computed(() => {
  const labels = ['Yếu', 'Trung bình', 'Khá', 'Mạnh']
  return labels[passwordStrength.value - 1] || ''
})

const strengthTextColor = computed(() => {
  const colors = ['text-red-500', 'text-orange-500', 'text-yellow-600', 'text-green-600']
  return colors[passwordStrength.value - 1] || ''
})

const isValid = computed(() =>
  form.value.password.length >= 8 &&
  form.value.password === form.value.password_confirmation
)

const handleSubmit = async () => {
  if (!isValid.value || isSubmitting.value) return
  isSubmitting.value = true
  try {
    const token = getCurrentToken()
    const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000/api'
    await axios.post(`${API_BASE}/nhan-vien/doi-mat-khau-lan-dau`, form.value, {
      headers: { Authorization: `Bearer ${token}` },
    })

    // Redirect về dashboard theo vai trò thực của user
    const vaiTro = getCurrentVaiTro()
    const roleRoutes = {
      bac_si:   '/doctor/dashboard',
      y_ta:     '/nurse/dashboard',
      le_tan:   '/receptionist/dashboard',
      tro_ly:   '/assistant/dashboard',
    }
    router.push(roleRoutes[vaiTro] || '/dashboard')
  } catch (err) {
    alert(err.response?.data?.message || 'Có lỗi xảy ra. Vui lòng thử lại.')
  } finally {
    isSubmitting.value = false
  }
}
</script>
