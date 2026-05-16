<template>
  <div class="grid grid-cols-3 gap-4 h-[calc(100vh-180px)]">

    <!-- CỘT TRÁI: Thông tin + Dịch vụ + Đơn thuốc -->
    <div class="col-span-2 flex flex-col gap-4 overflow-y-auto pr-2">

      <!-- Patient Info (compact) -->
      <div class="bg-white border !border-gray-200 rounded-xl p-4 flex items-center gap-3">
        <div class="w-11 h-11 rounded-lg bg-gray-100 border !border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
          <img
            v-if="lichHen.thu_cung?.anh_dai_dien || lichHen.thu_cung?.anh_dai_dien_url"
            :src="resolveImageUrl(lichHen.thu_cung.anh_dai_dien_url || lichHen.thu_cung.anh_dai_dien)"
            class="w-full h-full object-cover"
            @error="(e) => e.target.style.display = 'none'"
          />
          <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2">
            <p class="text-sm font-bold text-gray-900 truncate">{{ lichHen.thu_cung?.ten_thu_cung || 'N/A' }}</p>
            <span
              v-if="lichHen.khach_hang?.rank"
              :class="[
                'px-1.5 py-0.5 rounded text-[10px] font-bold uppercase',
                lichHen.khach_hang.rank === 'Gold' ? 'bg-amber-100 text-amber-700' :
                lichHen.khach_hang.rank === 'Diamond' ? 'bg-blue-100 text-blue-700' :
                'bg-gray-100 text-gray-600'
              ]"
            >{{ lichHen.khach_hang.rank }}</span>
          </div>
          <p class="text-xs text-gray-500 truncate">
            {{ lichHen.khach_hang?.full_name }}
            <span v-if="lichHen.khach_hang?.phone" class="ml-1">| {{ lichHen.khach_hang.phone }}</span>
            <span v-if="lichHen.nhan_vien?.full_name" class="ml-1">| BS: {{ lichHen.nhan_vien.full_name }}</span>
          </p>
        </div>
      </div>

      <!-- Services -->
      <div class="bg-white border !border-gray-200 rounded-xl p-4 flex-1">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Dịch vụ đã sử dụng</h3>
          <span v-if="lichHen.da_thanh_toan" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-semibold">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            Đã TT trước
          </span>
        </div>

        <div class="divide-y divide-gray-100">
          <div
            v-for="dv in dichVuList"
            :key="dv.id"
            class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0"
          >
            <div>
              <p class="text-sm font-medium text-gray-800">{{ dv.ten }}</p>
              <p v-if="dv.danh_muc?.ten_nhom" class="text-[11px] text-gray-400">{{ dv.danh_muc.ten_nhom }}</p>
            </div>
            <p class="text-sm font-semibold text-gray-900 tabular-nums">{{ formatCurrency(dv.gia_tien) }}</p>
          </div>
        </div>

        <!-- Add Service (only if not pre-paid) -->
        <div v-if="!lichHen.da_thanh_toan" class="mt-3 pt-3 border-t !border-gray-100 flex gap-2">
          <select
            v-model="selectedDichVuId"
            class="flex-1 h-8 bg-gray-50 border !border-gray-200 rounded-lg px-2 text-xs text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#009689]"
          >
            <option value="">Thêm dịch vụ...</option>
            <option v-for="dv in allDichVus" :key="dv.id" :value="dv.id">
              {{ dv.ten }} — {{ formatCurrency(dv.gia_tien) }}
            </option>
          </select>
          <button
            @click="themDichVu"
            :disabled="!selectedDichVuId"
            class="h-8 px-3 bg-[#009689] text-white rounded-lg text-xs font-medium disabled:opacity-30 hover:bg-[#007d72] transition-colors"
          >
            Thêm
          </button>
        </div>
      </div>

      <!-- Đơn thuốc (prescription) — only show if don_thuoc has items -->
      <div v-if="donThuocItems.length > 0" class="bg-white border !border-gray-200 rounded-xl p-4">
        <div class="flex items-center gap-2 mb-3">
          <div class="w-6 h-6 rounded-md bg-amber-50 flex items-center justify-center">
            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
            </svg>
          </div>
          <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Đơn thuốc</h3>
          <span class="ml-auto text-[10px] font-medium text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded">
            {{ donThuocItems.length }} loại
          </span>
        </div>

        <div class="divide-y divide-gray-100">
          <div
            v-for="(thuoc, idx) in donThuocItems"
            :key="idx"
            class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0"
          >
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-800 truncate">{{ thuoc.ten }}</p>
              <p class="text-[11px] text-gray-400">
                {{ thuoc.so_luong }} {{ thuoc.don_vi || 'đơn vị' }} × {{ formatCurrency(thuoc.don_gia) }}
              </p>
            </div>
            <p class="text-sm font-semibold text-amber-700 tabular-nums ml-3">
              {{ formatCurrency(thuoc.so_luong * thuoc.don_gia) }}
            </p>
          </div>
        </div>

        <!-- Subtotal thuốc -->
        <div class="mt-3 pt-3 border-t !border-amber-100 flex items-center justify-between">
          <span class="text-xs font-medium text-gray-500">Tổng đơn thuốc</span>
          <span class="text-sm font-bold text-amber-700 tabular-nums">{{ formatCurrency(tongTienThuoc) }}</span>
        </div>
      </div>

      <!-- Promotion (compact) — hide if pre-paid (only collecting prescription fee) -->
      <div v-if="!lichHen.da_thanh_toan" class="bg-white border !border-gray-200 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
          <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Khuyến mãi</h3>
          <p v-if="autoPromo" class="text-xs font-medium text-emerald-600">
            Rank {{ lichHen.khach_hang?.rank }}: -{{ formatCurrency(soTienGiam) }}
          </p>
        </div>

        <div class="flex gap-2">
          <input
            v-model="maGiamGia"
            type="text"
            placeholder="Nhập mã giảm giá..."
            :disabled="!!autoPromo"
            class="flex-1 h-8 bg-gray-50 border !border-gray-200 rounded-lg px-3 text-xs focus:outline-none focus:ring-1 focus:ring-[#009689] disabled:opacity-40"
          />
          <button
            @click="applyMaGiamGia"
            :disabled="!maGiamGia || loadingPreview"
            class="h-8 px-3 bg-[#009689] text-white rounded-lg text-xs font-medium disabled:opacity-30 hover:bg-[#007d72] transition-colors"
          >
            {{ loadingPreview ? '...' : 'Áp dụng' }}
          </button>
          <button
            v-if="maGiamGia && promoApplied"
            @click="clearPromo"
            class="h-8 px-2 border !border-gray-200 rounded-lg text-xs text-gray-500 hover:bg-gray-50"
          >
            Xoá
          </button>
        </div>
        <p v-if="promoError" class="text-[11px] text-red-500 mt-1">{{ promoError }}</p>
        <p v-if="promoApplied && !promoError" class="text-[11px] text-emerald-600 mt-1">Giảm {{ formatCurrency(soTienGiam) }}</p>
      </div>
    </div>

    <!-- CỘT PHẢI: Tổng + Thanh toán + Nút (sticky, không cần scroll) -->
    <div class="flex flex-col gap-4">

      <!-- Pre-paid indicator -->
      <div v-if="lichHen.da_thanh_toan && donThuocItems.length > 0" class="bg-emerald-50 border !border-emerald-200 rounded-xl p-3">
        <div class="flex items-center gap-2 mb-1">
          <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span class="text-xs font-semibold text-emerald-700">Đã thanh toán trước</span>
        </div>
        <p class="text-sm font-bold text-emerald-800 tabular-nums ml-6">{{ formatCurrency(tongTienDichVu) }}</p>
        <p class="text-[11px] text-emerald-600 ml-6 mt-0.5">Phí dịch vụ — đã thu khi đặt lịch</p>
      </div>

      <!-- Payment Summary -->
      <div class="bg-[#f8fffe] border-2 !border-[#009689]/20 rounded-xl p-4">
        <div class="flex flex-col gap-1.5">
          <!-- Service fee line -->
          <div class="flex justify-between text-sm">
            <span class="text-gray-500">Phí dịch vụ</span>
            <span :class="['font-medium tabular-nums', lichHen.da_thanh_toan ? 'text-gray-400 line-through' : 'text-gray-700']">
              {{ formatCurrency(tongTienDichVu) }}
            </span>
          </div>

          <!-- Prescription fee line -->
          <div v-if="donThuocItems.length > 0" class="flex justify-between text-sm">
            <span class="text-gray-500">Đơn thuốc</span>
            <span class="font-medium text-amber-700 tabular-nums">{{ formatCurrency(tongTienThuoc) }}</span>
          </div>

          <!-- Discount line -->
          <div v-if="soTienGiam > 0 && !lichHen.da_thanh_toan" class="flex justify-between text-sm">
            <span class="text-gray-500">Giảm</span>
            <span class="font-medium text-emerald-600 tabular-nums">-{{ formatCurrency(soTienGiam) }}</span>
          </div>

          <!-- Separator + Total -->
          <div class="border-t !border-[#009689]/20 pt-2 mt-1 flex justify-between items-baseline">
            <span class="text-sm font-bold text-gray-900">
              {{ lichHen.da_thanh_toan ? 'Cần thu thêm' : 'Thành tiền' }}
            </span>
            <span class="text-xl font-extrabold text-[#009689] tabular-nums">{{ formatCurrency(soTienCanThu) }}</span>
          </div>
        </div>
      </div>

      <!-- Payment Method -->
      <div>
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Hình thức</h3>
        <div class="grid grid-cols-2 gap-2">
          <button
            @click="hinhThuc = 'tien_mat'"
            :class="[
              'flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl border-2 transition-all',
              hinhThuc === 'tien_mat'
                ? '!border-[#009689] bg-[#f0fdfa]'
                : '!border-gray-200 bg-white hover:border-gray-300'
            ]"
          >
            <svg :class="['w-5 h-5', hinhThuc === 'tien_mat' ? 'text-[#009689]' : 'text-gray-400']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span :class="['text-xs font-semibold', hinhThuc === 'tien_mat' ? 'text-[#009689]' : 'text-gray-600']">Tiền mặt</span>
          </button>

          <button
            @click="hinhThuc = 'chuyen_khoan'"
            :class="[
              'flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl border-2 transition-all',
              hinhThuc === 'chuyen_khoan'
                ? '!border-[#009689] bg-[#f0fdfa]'
                : '!border-gray-200 bg-white hover:border-gray-300'
            ]"
          >
            <svg :class="['w-5 h-5', hinhThuc === 'chuyen_khoan' ? 'text-[#009689]' : 'text-gray-400']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
            <span :class="['text-xs font-semibold', hinhThuc === 'chuyen_khoan' ? 'text-[#009689]' : 'text-gray-600']">QR</span>
          </button>
        </div>
      </div>

      <!-- Confirm Button -->
      <button
        @click="confirmPayment"
        :disabled="saving || soTienCanThu <= 0"
        class="w-full h-12 bg-[#009689] text-white rounded-xl font-bold text-sm hover:bg-[#007d72] transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2 mt-auto"
      >
        <div v-if="saving" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
        <span>{{ saving ? 'Đang xử lý...' : `Xác nhận thu ${formatCurrency(soTienCanThu)}` }}</span>
      </button>
    </div>

    <!-- QR Payment Modal -->
    <PaymentQrModal
      :lich-hen-id="lichHen.id"
      :visible="showQrModal"
      :amount="soTienCanThu"
      :is-bo-sung="isBoSung"
      :bo-sung-items="donThuocItems"
      @close="showQrModal = false"
      @success="onQrPaymentSuccess"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/utils/api'
import { resolveImageUrl } from '@/utils/image'
import { showSuccessToast, showErrorToast } from '@/utils/toast'
import PaymentQrModal from '@/components/payment/PaymentQrModal.vue'

const props = defineProps({
  lichHen: { type: Object, required: true }
})

const emit = defineEmits(['complete'])

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
const soTienGiam = ref(0)
const showQrModal = ref(false)
const loaiGiam = ref('khong_giam')
const donThuocItems = ref([])

const isBoSung = computed(() => !!props.lichHen.da_thanh_toan && donThuocItems.value.length > 0)

const tongTienDichVu = computed(() =>
  dichVuList.value.reduce((sum, dv) => sum + (dv.gia_tien || 0), 0)
)

const tongTienThuoc = computed(() =>
  donThuocItems.value.reduce((sum, t) => sum + (t.so_luong * t.don_gia), 0)
)

const tongTienGoc = computed(() => tongTienDichVu.value + tongTienThuoc.value)

const tongTienSauGiam = computed(() =>
  Math.max(0, tongTienGoc.value - soTienGiam.value)
)

const soTienCanThu = computed(() => {
  if (props.lichHen.da_thanh_toan) {
    return tongTienThuoc.value
  }
  return tongTienSauGiam.value
})

onMounted(async () => {
  if (props.lichHen.dich_vus?.length) {
    dichVuList.value = props.lichHen.dich_vus.map(dv => ({
      ...dv,
      gia_tien: parseFloat(dv.don_gia || dv.gia_tien) || 0
    }))
  } else if (props.lichHen.dich_vu) {
    dichVuList.value = [{
      ...props.lichHen.dich_vu,
      gia_tien: parseFloat(props.lichHen.dich_vu.gia_tien) || 0
    }]
  } else if (props.lichHen.dich_vu_id) {
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

  try {
    const res = await api.get('/dich-vu', { params: { trang_thai: 'kinh_doanh', per_page: 100 } })
    const raw = res.data?.data
    allDichVus.value = Array.isArray(raw) ? raw : (raw?.data || [])
  } catch (e) {
    console.error(e)
  }

  // Fetch phiếu khám để lấy đơn thuốc
  try {
    const res = await api.get('/phieu-kham', { params: { lich_hen_id: props.lichHen.id, per_page: 1 } })
    const phieuKhams = res.data?.data
    if (Array.isArray(phieuKhams) && phieuKhams.length > 0) {
      const pk = phieuKhams[0]
      if (pk.don_thuoc && Array.isArray(pk.don_thuoc) && pk.don_thuoc.length > 0) {
        donThuocItems.value = pk.don_thuoc.map(item => ({
          ten: item.ten || item.name || '',
          so_luong: parseFloat(item.so_luong || item.quantity || 0),
          don_gia: parseFloat(item.don_gia || item.unit_price || 0),
          don_vi: item.don_vi || item.unit || '',
        }))
      }
    }
  } catch (e) {
    console.error('Lỗi load phiếu khám:', e)
  }

  if (!props.lichHen.da_thanh_toan) {
    await previewThanhToan()
  }
})

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

const themDichVu = () => {
  const dv = allDichVus.value.find(d => d.id == selectedDichVuId.value)
  if (dv && !dichVuList.value.find(d => d.id === dv.id)) {
    dichVuList.value.push(dv)
    selectedDichVuId.value = ''
  }
}

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

const confirmPayment = async () => {
  if (hinhThuc.value === 'chuyen_khoan') {
    showQrModal.value = true
    return
  }

  saving.value = true
  try {
    if (isBoSung.value) {
      await api.post('/thanh-toan/bo-sung', {
        lich_hen_id: props.lichHen.id,
        hinh_thuc_thanh_toan: 'tien_mat',
        items: donThuocItems.value.map(t => ({
          ten: t.ten,
          so_luong: t.so_luong,
          don_gia: t.don_gia,
        })),
        ghi_chu: 'Thu bổ sung đơn thuốc',
      })
    } else {
      await api.post('/thanh-toan', {
        lich_hen_id: props.lichHen.id,
        hinh_thuc_thanh_toan: hinhThuc.value,
        tien_mat: soTienCanThu.value,
        tien_online: 0,
        ma_giam_gia: promoApplied.value ? maGiamGia.value : undefined,
      })
    }
    showSuccessToast('Thanh toán thành công!')
    emit('complete')
  } catch (e) {
    showErrorToast(e.response?.data?.message || 'Lỗi thanh toán, vui lòng thử lại')
  } finally {
    saving.value = false
  }
}

const onQrPaymentSuccess = () => {
  showQrModal.value = false
  emit('complete')
}

const formatCurrency = (val) =>
  new Intl.NumberFormat('vi-VN').format(Math.round(val || 0)) + ' ₫'
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,400;0,6..12,500;0,6..12,600;0,6..12,700;0,6..12,800&display=swap");
* { font-family: "Nunito Sans", sans-serif; }
</style>
