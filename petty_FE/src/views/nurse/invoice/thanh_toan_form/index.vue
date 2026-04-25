<template>
  <div class="flex flex-col gap-6">
    <!-- Thông tin bệnh nhân -->
    <div class="bg-blue-50 border-2 !border-[#bedbff] rounded-[14px] p-6">
      <div class="flex items-start gap-4">
        <div class="w-16 h-16 rounded-[10px] bg-white border !border-gray-200 flex items-center justify-center overflow-hidden">
          <img v-if="lichHen.thu_cung?.anh_dai_dien" :src="lichHen.thu_cung.anh_dai_dien" class="w-full h-full object-cover" />
          <span v-else class="text-2xl">🐾</span>
        </div>
        <div class="flex-1">
          <div class="flex items-center gap-3 mb-2">
            <h2 class="text-xl font-bold text-black">{{ lichHen.thu_cung?.ten_thu_cung || 'N/A' }}</h2>
            <span class="bg-white border !border-gray-300 rounded-lg px-2 py-0.5 text-xs text-gray-600">
              {{ lichHen.dich_vu?.ten || 'N/A' }}
            </span>
          </div>
          <div class="grid grid-cols-2 gap-x-6 gap-y-1 text-sm">
            <p class="text-gray-500">Chủ nuôi: <span class="font-medium text-gray-800">{{ lichHen.khach_hang?.full_name }}</span></p>
            <p class="text-gray-500">SĐT: <span class="font-medium text-gray-800">{{ lichHen.khach_hang?.phone || 'Chưa có' }}</span></p>
            <p class="text-gray-500">Hạng: <span class="font-medium" :class="rankColor">{{ lichHen.khach_hang?.rank }}</span></p>
            <p class="text-gray-500">Bác sĩ: <span class="font-medium text-gray-800">{{ lichHen.nhan_vien?.full_name || 'N/A' }}</span></p>
          </div>
        </div>
      </div>
    </div>

    <div class="flex gap-6">
      <!-- CỘT TRÁI: Dịch vụ + Khuyến mãi -->
      <div class="flex flex-col gap-4 flex-1">

        <!-- Dịch vụ đã dùng -->
        <div class="bg-white border !border-gray-300 rounded-[14px] p-5 shadow-sm">
          <h3 class="font-semibold text-gray-900 mb-4">Dịch vụ đã sử dụng</h3>

          <div class="flex flex-col gap-2">
            <div
              v-for="dv in dichVuList"
              :key="dv.id"
              class="flex items-center justify-between py-2 border-b !border-gray-100 last:border-0"
            >
              <div>
                <p class="text-sm font-medium text-gray-800">{{ dv.ten }}</p>
                <p class="text-xs text-gray-500">{{ dv.danh_muc?.ten_nhom || '' }}</p>
              </div>
              <p class="text-sm font-bold text-gray-900">{{ formatCurrency(dv.gia_tien) }}</p>
            </div>
          </div>

          <!-- Thêm dịch vụ -->
          <div class="mt-3 flex gap-2">
            <select
              v-model="selectedDichVuId"
              class="flex-1 h-9 bg-gray-50 border !border-gray-300 rounded-lg px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#009689]"
            >
              <option value="">-- Thêm dịch vụ --</option>
              <option v-for="dv in allDichVus" :key="dv.id" :value="dv.id">
                {{ dv.ten }} - {{ formatCurrency(dv.gia_tien) }}
              </option>
            </select>
            <button
              @click="themDichVu"
              :disabled="!selectedDichVuId"
              class="h-9 px-4 bg-[#009689] text-white rounded-lg text-sm font-medium disabled:opacity-40 hover:bg-[#007d72] transition-colors"
            >
              Thêm
            </button>
          </div>
        </div>

        <!-- Khuyến mãi -->
        <div class="bg-white border !border-gray-300 rounded-[14px] p-5 shadow-sm">
          <h3 class="font-semibold text-gray-900 mb-4">Khuyến mãi</h3>

          <!-- Tự động theo rank -->
          <div v-if="autoPromo" class="bg-green-50 border !border-green-200 rounded-lg p-3 mb-3 flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-green-800">{{ autoPromo.ten_khuyen_mai }}</p>
              <p class="text-xs text-green-600">Tự động theo hạng {{ lichHen.khach_hang?.rank }}</p>
            </div>
            <p class="text-sm font-bold text-green-700">-{{ formatCurrency(soTienGiam) }}</p>
          </div>

          <!-- Nhập mã thủ công -->
          <div class="flex gap-2">
            <input
              v-model="maGiamGia"
              type="text"
              placeholder="Nhập mã giảm giá..."
              :disabled="!!autoPromo"
              class="flex-1 h-9 bg-gray-50 border !border-gray-300 rounded-lg px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#009689] disabled:opacity-50"
            />
            <button
              @click="applyMaGiamGia"
              :disabled="!maGiamGia || loadingPreview"
              class="h-9 px-4 bg-[#155dfc] text-white rounded-lg text-sm font-medium disabled:opacity-40 hover:bg-[#1447e6] transition-colors"
            >
              {{ loadingPreview ? '...' : 'Áp dụng' }}
            </button>
            <button
              v-if="maGiamGia && promoApplied"
              @click="clearPromo"
              class="h-9 px-3 border !border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50"
            >
              Xóa
            </button>
          </div>
          <p v-if="promoError" class="text-xs text-red-500 mt-1">{{ promoError }}</p>
          <p v-if="promoApplied && !promoError" class="text-xs text-green-600 mt-1">
            Áp dụng thành công! Giảm {{ formatCurrency(soTienGiam) }}
          </p>
        </div>

      </div>

      <!-- CỘT PHẢI: Tổng tiền + Thanh toán -->
      <div class="flex flex-col gap-4 w-[340px]">

        <!-- Tổng tiền -->
        <div class="bg-white border !border-gray-300 rounded-[14px] p-5 shadow-sm">
          <h3 class="font-semibold text-gray-900 mb-4">Tổng thanh toán</h3>
          <div class="flex flex-col gap-2">
            <div class="flex justify-between text-sm">
              <span class="text-gray-500">Tạm tính</span>
              <span class="font-medium">{{ formatCurrency(tongTienGoc) }}</span>
            </div>
            <div v-if="soTienGiam > 0" class="flex justify-between text-sm">
              <span class="text-gray-500">Giảm giá</span>
              <span class="font-medium text-green-600">-{{ formatCurrency(soTienGiam) }}</span>
            </div>
            <div class="border-t !border-gray-200 pt-2 flex justify-between">
              <span class="font-bold text-gray-900">Thành tiền</span>
              <span class="font-bold text-xl text-[#155dfc]">{{ formatCurrency(tongTienSauGiam) }}</span>
            </div>
          </div>
        </div>

        <!-- Hình thức thanh toán -->
        <div class="bg-white border !border-gray-300 rounded-[14px] p-5 shadow-sm">
          <h3 class="font-semibold text-gray-900 mb-4">Hình thức thanh toán</h3>

          <div class="flex flex-col gap-2">
            <label
              v-for="opt in paymentOptions"
              :key="opt.value"
              class="flex items-center gap-3 p-3 border-2 rounded-lg cursor-pointer transition-colors"
              :class="hinhThuc === opt.value
                ? 'border-[#009689] bg-green-50'
                : 'border-gray-200 hover:border-gray-300'"
            >
              <input type="radio" v-model="hinhThuc" :value="opt.value" class="accent-[#009689]" />
              <span class="text-sm font-medium text-gray-800">{{ opt.label }}</span>
            </label>
          </div>

          <!-- Kết hợp: nhập từng phần -->
          <div v-if="hinhThuc === 'ket_hop'" class="mt-3 flex flex-col gap-2">
            <div class="flex items-center gap-2">
              <label class="text-xs text-gray-500 w-24">Tiền mặt</label>
              <input
                v-model.number="tienMat"
                type="number"
                min="0"
                class="flex-1 h-9 bg-gray-50 border !border-gray-300 rounded-lg px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#009689]"
                @input="calcTienOnline"
              />
            </div>
            <div class="flex items-center gap-2">
              <label class="text-xs text-gray-500 w-24">Online</label>
              <input
                v-model.number="tienOnline"
                type="number"
                min="0"
                class="flex-1 h-9 bg-gray-50 border !border-gray-300 rounded-lg px-3 text-sm focus:outline-none"
                readonly
              />
            </div>
          </div>
        </div>

        <!-- Nút xác nhận -->
        <button
          @click="confirmPayment"
          :disabled="saving || tongTienSauGiam <= 0"
          class="w-full h-12 bg-[#009689] text-white rounded-[12px] font-semibold text-base hover:bg-[#007d72] transition-colors disabled:opacity-50 flex items-center justify-center gap-2"
        >
          <div v-if="saving" class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
          <span>{{ saving ? 'Đang xử lý...' : 'Xác nhận thanh toán' }}</span>
        </button>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '@/utils/api'
import { showSuccessToast, showErrorToast } from '@/utils/toast'


const props = defineProps({
  lichHen: { type: Object, required: true }
})

const emit = defineEmits(['complete'])

// State
const saving = ref(false)
const loadingPreview = ref(false)
const dichVuList = ref([])
const allDichVus = ref([])
const selectedDichVuId = ref('')
const maGiamGia = ref('')
const promoApplied = ref(false)
const promoError = ref('')
const autoPromo = ref(null)
const hinhThuc = ref('tien_mat')
const tienMat = ref(0)
const tienOnline = ref(0)
const soTienGiam = ref(0)
const khuyenMaiId = ref(null)
const loaiGiam = ref('khong_giam')

const paymentOptions = [
  { value: 'tien_mat', label: 'Tiền mặt' },
  { value: 'vnpay',    label: 'VNPay' },
  { value: 'momo',     label: 'Momo' },
  { value: 'ket_hop',  label: 'Kết hợp (Tiền mặt + Online)' },
]

// Tổng tiền
const tongTienGoc = computed(() =>
  dichVuList.value.reduce((sum, dv) => sum + (dv.gia_tien || 0), 0)
)
const tongTienSauGiam = computed(() =>
  Math.max(0, tongTienGoc.value - soTienGiam.value)
)

const rankColor = computed(() => {
  const rank = props.lichHen.khach_hang?.rank
  if (rank === 'Gold') return 'text-yellow-600'
  if (rank === 'Diamond') return 'text-blue-600'
  return 'text-gray-600'
})

// Load dữ liệu ban đầu
onMounted(async () => {
  // Dịch vụ gốc từ lịch hẹn
  if (props.lichHen.dich_vu) {
  dichVuList.value = [{
    ...props.lichHen.dich_vu,
    gia_tien: parseFloat(props.lichHen.dich_vu.gia_tien) || 0
  }]
} else if (props.lichHen.dich_vu_id) {
  // Fetch dịch vụ nếu chưa load
  try {
    const res = await api.get(`/dich-vu/${props.lichHen.dich_vu_id}`)
    if (res.data?.data) {
      dichVuList.value = [{
        ...res.data.data,
        gia_tien: parseFloat(res.data.data.gia_tien) || 0
      }]
    }
  } catch (e) {
    console.error('Lỗi load dịch vụ:', e)
  }
}

  // Load tất cả dịch vụ để thêm
  try {
    const res = await api.get('/dich-vu', { params: { trang_thai: 'kinh_doanh', per_page: 100 } })
    const raw = res.data?.data
    allDichVus.value = Array.isArray(raw) ? raw : (raw?.data || [])
  } catch (e) {
    console.error(e)
  }

  // Preview để lấy khuyến mãi tự động theo rank
  await previewThanhToan()
})

// Preview API
const previewThanhToan = async () => {
  try {
    const res = await api.post('/thanh-toan/preview', {
      lich_hen_id: props.lichHen.id,
      ma_giam_gia: maGiamGia.value || undefined,
    })
    const d = res.data?.data
    if (d) {
      soTienGiam.value = d.so_tien_giam || 0
      if (d.khuyen_mai?.loai_giam === 'rank') {
        autoPromo.value = { ten_khuyen_mai: d.khuyen_mai.ten }
        loaiGiam.value = 'rank'
      }
      if (d.error) {
        promoError.value = d.error
        promoApplied.value = false
        soTienGiam.value = 0
      }
    }
  } catch (e) {
    console.error(e)
  }
}

// Thêm dịch vụ
const themDichVu = () => {
  const dv = allDichVus.value.find(d => d.id == selectedDichVuId.value)
  if (dv && !dichVuList.value.find(d => d.id === dv.id)) {
    dichVuList.value.push(dv)
    selectedDichVuId.value = ''
  }
}

// Áp dụng mã giảm giá
const applyMaGiamGia = async () => {
  loadingPreview.value = true
  promoError.value = ''
  try {
    const res = await api.post('/thanh-toan/preview', {
      lich_hen_id: props.lichHen.id,
      ma_giam_gia: maGiamGia.value,
    })
    const d = res.data?.data
    if (d?.error) {
      promoError.value = d.error
      promoApplied.value = false
      soTienGiam.value = 0
    } else {
      soTienGiam.value = d.so_tien_giam || 0
      promoApplied.value = true
      loaiGiam.value = 'ma_code'
    }
  } catch (e) {
    promoError.value = 'Không thể kiểm tra mã giảm giá'
  } finally {
    loadingPreview.value = false
  }
}

const clearPromo = () => {
  maGiamGia.value = ''
  promoApplied.value = false
  promoError.value = ''
  soTienGiam.value = autoPromo.value ? soTienGiam.value : 0
  loaiGiam.value = autoPromo.value ? 'rank' : 'khong_giam'
  previewThanhToan()
}

const calcTienOnline = () => {
  tienOnline.value = Math.max(0, tongTienSauGiam.value - tienMat.value)
}

watch(hinhThuc, (val) => {
  if (val === 'tien_mat') {
    tienMat.value = tongTienSauGiam.value
    tienOnline.value = 0
  } else if (val !== 'ket_hop') {
    tienMat.value = 0
    tienOnline.value = tongTienSauGiam.value
  } else {
    tienMat.value = 0
    tienOnline.value = tongTienSauGiam.value
  }
})

// Xác nhận thanh toán
const confirmPayment = async () => {
  saving.value = true
  try {
    await api.post('/thanh-toan', {
      lich_hen_id: props.lichHen.id,
      hinh_thuc_thanh_toan: hinhThuc.value,
      tien_mat: hinhThuc.value === 'tien_mat' ? tongTienSauGiam.value
              : hinhThuc.value === 'ket_hop' ? tienMat.value : 0,
      tien_online: hinhThuc.value !== 'tien_mat' ? tongTienSauGiam.value
                 : hinhThuc.value === 'ket_hop' ? tienOnline.value : 0,
      ma_giam_gia: promoApplied.value ? maGiamGia.value : undefined,
    })
    showSuccessToast('Thanh toán thành công!')
    emit('complete')
  } catch (e) {
    showErrorToast(e.response?.data?.message || 'Lỗi thanh toán, vui lòng thử lại')
  } finally {
    saving.value = false
  }
}

const formatCurrency = (val) =>
  new Intl.NumberFormat('vi-VN').format(Math.round(val || 0)) + ' ₫'
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap");
* { font-family: "Nunito Sans", sans-serif; }
</style>