<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="visible"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
        @click.self="handleClose"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        <!-- Modal -->
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden animate-modal-in">

          <!-- Success State -->
          <div v-if="state === 'success'" class="flex flex-col items-center justify-center py-16 px-8">
            <div class="w-20 h-20 rounded-full bg-emerald-50 flex items-center justify-center mb-5 animate-success-pop">
              <svg class="w-10 h-10 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-1">Thanh toán thành công</h3>
            <p class="text-sm text-gray-500">Giao dịch đã được xác nhận</p>
          </div>

          <!-- Expired State -->
          <div v-else-if="state === 'expired'" class="flex flex-col items-center justify-center py-14 px-8">
            <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mb-4">
              <svg class="w-8 h-8 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Giao dịch hết hạn</h3>
            <p class="text-sm text-gray-500 mb-2">Thời gian thanh toán đã hết.</p>
            <p class="text-sm text-gray-500 mb-6">Lịch hẹn vẫn có hiệu lực — bạn có thể thanh toán tại phòng khám.</p>
            <button
              @click="retry"
              class="h-10 px-6 bg-[#009689] text-white rounded-xl text-sm font-semibold hover:bg-[#007d72] transition-colors"
            >
              Tạo lại giao dịch
            </button>
          </div>

          <!-- Loading State -->
          <div v-else-if="state === 'loading'" class="flex flex-col items-center justify-center py-20">
            <div class="w-10 h-10 border-3 border-gray-200 border-t-[#009689] rounded-full animate-spin"></div>
            <p class="mt-4 text-sm text-gray-500">Đang tạo giao dịch...</p>
          </div>

          <!-- Error State -->
          <div v-else-if="state === 'error'" class="flex flex-col items-center justify-center py-14 px-8">
            <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mb-4">
              <svg class="w-8 h-8 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Lỗi</h3>
            <p class="text-sm text-gray-500 text-center mb-6">{{ errorMessage }}</p>
            <div class="flex gap-3">
              <button @click="retry" class="h-10 px-5 bg-[#009689] text-white rounded-xl text-sm font-semibold hover:bg-[#007d72] transition-colors">
                Thử lại
              </button>
              <button @click="handleClose" class="h-10 px-5 border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors">
                Đóng
              </button>
            </div>
          </div>

          <!-- QR Payment State -->
          <div v-else class="flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 pt-5 pb-3">
              <h2 class="text-lg font-bold text-gray-900">Thanh toán chuyển khoản</h2>
              <button @click="handleClose" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors">
                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Timer -->
            <div class="flex justify-center mb-3">
              <div
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                :class="remainingSeconds < 120 ? 'bg-red-50 text-red-600' : 'bg-gray-100 text-gray-600'"
              >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ formattedTime }}
              </div>
            </div>

            <!-- QR Code -->
            <div class="flex justify-center px-6 mb-4">
              <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
                <img
                  v-if="!qrError"
                  :src="paymentInfo.qr_url"
                  alt="VietQR"
                  class="w-[250px] h-[250px] rounded-lg"
                  @error="qrError = true"
                />
                <div v-else class="w-[250px] h-[250px] bg-gray-50 rounded-lg flex flex-col items-center justify-center">
                  <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-xs text-gray-400">Không tải được QR</p>
                  <p class="text-xs text-gray-400">Dùng thông tin bên dưới</p>
                </div>
              </div>
            </div>

            <!-- Payment Info -->
            <div class="px-6 mb-4">
              <div class="bg-gray-50 rounded-xl p-4 space-y-2.5">
                <InfoRow label="Ngân hàng" :value="paymentInfo.bank_code" @copy="copyText(paymentInfo.bank_code)" />
                <InfoRow label="Số tài khoản" :value="paymentInfo.account_number" @copy="copyText(paymentInfo.account_number)" />
                <InfoRow label="Chủ tài khoản" :value="paymentInfo.account_name" @copy="copyText(paymentInfo.account_name)" />
                <InfoRow label="Số tiền" :value="formatCurrency(paymentInfo.amount)" @copy="copyText(String(Math.round(paymentInfo.amount)))" />
                <InfoRow label="Nội dung CK" :value="paymentInfo.content" highlight @copy="copyText(paymentInfo.content)" />
              </div>
            </div>

            <!-- Actions -->
            <div class="px-6 pb-5 flex flex-col gap-2.5">
              <button
                v-if="showManualConfirm"
                @click="confirmManualPayment"
                :disabled="confirming"
                class="w-full h-11 bg-[#009689] text-white rounded-xl text-sm font-semibold hover:bg-[#007d72] transition-colors disabled:opacity-50 flex items-center justify-center gap-2"
              >
                <div v-if="confirming" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                <span>{{ confirming ? 'Đang xác nhận...' : 'Xác nhận đã nhận tiền' }}</span>
              </button>
              <button
                @click="handleClose"
                class="w-full h-10 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors"
              >
                Đóng
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch, onUnmounted, computed } from 'vue'
import sepayService from '@/services/sepayService'
import { showSuccessToast, showErrorToast } from '@/utils/toast'

const props = defineProps({
  lichHenId: { type: Number, default: null },
  visible: { type: Boolean, default: false },
  initialPaymentInfo: { type: Object, default: null },
  initialThanhToanId: { type: Number, default: null },
  showManualConfirm: { type: Boolean, default: true },
})

const emit = defineEmits(['close', 'success'])

const state = ref('loading')
const paymentInfo = ref({})
const thanhToanId = ref(null)
const remainingSeconds = ref(900)
const qrError = ref(false)
const confirming = ref(false)
const errorMessage = ref('')
const copiedField = ref('')

let pollInterval = null
let countdownInterval = null

const formattedTime = computed(() => {
  const m = Math.floor(remainingSeconds.value / 60)
  const s = remainingSeconds.value % 60
  return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
})

watch(() => props.visible, async (val) => {
  if (val) {
    await initPayment()
  } else {
    cleanup()
  }
})

async function initPayment() {
  state.value = 'loading'
  qrError.value = false
  errorMessage.value = ''

  if (props.initialPaymentInfo && props.initialThanhToanId) {
    paymentInfo.value = props.initialPaymentInfo
    thanhToanId.value = props.initialThanhToanId

    const expiresAt = new Date(paymentInfo.value.expires_at)
    remainingSeconds.value = Math.max(0, Math.floor((expiresAt - Date.now()) / 1000))

    if (remainingSeconds.value <= 0) {
      state.value = 'expired'
      return
    }

    state.value = 'qr'
    startCountdown()
    startPolling()
    return
  }

  try {
    const res = await sepayService.createPayment(props.lichHenId)
    if (res.status) {
      paymentInfo.value = res.data.payment_info
      thanhToanId.value = res.data.thanh_toan.id

      const expiresAt = new Date(paymentInfo.value.expires_at)
      remainingSeconds.value = Math.max(0, Math.floor((expiresAt - Date.now()) / 1000))

      state.value = 'qr'
      startCountdown()
      startPolling()
    } else {
      errorMessage.value = res.message || 'Không thể tạo giao dịch'
      state.value = 'error'
    }
  } catch (e) {
    errorMessage.value = e.response?.data?.message || 'Không thể tạo giao dịch'
    state.value = 'error'
  }
}

function startCountdown() {
  countdownInterval = setInterval(() => {
    remainingSeconds.value--
    if (remainingSeconds.value <= 0) {
      state.value = 'expired'
      cleanup()
    }
  }, 1000)
}

function startPolling() {
  pollInterval = setInterval(async () => {
    if (!thanhToanId.value) return
    try {
      const res = await sepayService.checkStatus(thanhToanId.value)
      if (res.data?.trang_thai === 'da_thanh_toan') {
        onPaymentSuccess()
      }
    } catch (e) {
      // Silent fail on poll
    }
  }, 3000)
}

function onPaymentSuccess() {
  cleanup()
  state.value = 'success'
  showSuccessToast('Thanh toán thành công!')
  setTimeout(() => {
    emit('success')
    emit('close')
  }, 2000)
}

async function confirmManualPayment() {
  confirming.value = true
  try {
    const res = await sepayService.confirmManual(thanhToanId.value)
    if (res.status) {
      onPaymentSuccess()
    } else {
      showErrorToast(res.message || 'Không thể xác nhận')
    }
  } catch (e) {
    showErrorToast(e.response?.data?.message || 'Lỗi xác nhận thanh toán')
  } finally {
    confirming.value = false
  }
}

async function retry() {
  cleanup()
  await initPayment()
}

function handleClose() {
  cleanup()
  emit('close')
}

function cleanup() {
  if (pollInterval) { clearInterval(pollInterval); pollInterval = null }
  if (countdownInterval) { clearInterval(countdownInterval); countdownInterval = null }
}

async function copyText(text) {
  try {
    await navigator.clipboard.writeText(text)
    copiedField.value = text
    setTimeout(() => { copiedField.value = '' }, 1500)
  } catch {
    // Fallback
    const el = document.createElement('textarea')
    el.value = text
    document.body.appendChild(el)
    el.select()
    document.execCommand('copy')
    document.body.removeChild(el)
  }
}

function formatCurrency(val) {
  return new Intl.NumberFormat('vi-VN').format(Math.round(val || 0)) + ' ₫'
}

onUnmounted(cleanup)
</script>

<script>
const InfoRow = {
  props: ['label', 'value', 'highlight'],
  emits: ['copy'],
  template: `
    <div class="flex items-center justify-between">
      <div class="flex-1 min-w-0">
        <p class="text-[11px] text-gray-400 uppercase tracking-wide">{{ label }}</p>
        <p class="text-sm font-semibold truncate" :class="highlight ? 'text-[#009689]' : 'text-gray-800'">{{ value }}</p>
      </div>
      <button
        @click="$emit('copy')"
        class="ml-2 w-7 h-7 rounded-lg bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-90"
        title="Sao chép"
      >
        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
        </svg>
      </button>
    </div>
  `
}

export default { components: { InfoRow } }
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

@keyframes modal-in {
  from { opacity: 0; transform: scale(0.95) translateY(8px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes success-pop {
  0% { transform: scale(0); opacity: 0; }
  50% { transform: scale(1.15); }
  100% { transform: scale(1); opacity: 1; }
}

.animate-modal-in {
  animation: modal-in 0.25s ease-out;
}

.animate-success-pop {
  animation: success-pop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) 0.1s both;
}
</style>
