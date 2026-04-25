<template>
  <div class="relative w-full h-full px-8 py-6">

    <!-- DANH SÁCH HÓA ĐƠN -->
    <div v-if="!selectedLichHen" class="flex flex-col gap-6">
      <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-semibold text-black">Tài chính & Hóa đơn (POS)</h1>
        <p class="text-base font-medium text-gray-500">Thu ngân - Thanh toán dịch vụ</p>
      </div>

      <!-- Tìm kiếm -->
      <div class="bg-white border !border-gray-300 rounded-[14px] p-4 shadow-sm">
        <div class="flex items-center gap-4">
          <div class="relative flex-1">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Nhập tên khách hàng, tên thú cưng..."
              class="w-full h-11 bg-gray-50 border !border-gray-300 rounded-lg pl-4 pr-4 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
            />
          </div>
          <button
            class="bg-[#00a63e] rounded-lg px-4 py-2.5 h-10 flex items-center gap-2 hover:bg-[#008833] transition-colors shrink-0"
            @click="isCreateInvoiceOpen = true"
          >
            <span class="text-sm font-medium text-white">Tạo hóa đơn bán lẻ</span>
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex justify-center items-center py-16">
        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#009689]"></div>
      </div>

      <!-- Danh sách chờ thanh toán -->
      <div v-else class="bg-white border !border-gray-300 rounded-[14px] p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-semibold text-black">Hóa đơn chờ thanh toán</h2>
          <span class="bg-yellow-100 text-yellow-700 text-xs font-medium px-3 py-1 rounded-full">
            {{ filteredList.length }} chờ xử lý
          </span>
        </div>

        <div v-if="filteredList.length === 0" class="text-center py-12 text-gray-400">
          Không có hóa đơn chờ thanh toán
        </div>

        <div v-else class="flex flex-col gap-3">
          <button
            v-for="item in filteredList"
            :key="item.id"
            class="border-2 !border-gray-200 rounded-[14px] p-4 hover:border-[#009689] transition-colors text-left shadow-sm"
            @click="selectLichHen(item)"
          >
            <div class="flex items-center justify-between mb-2">
              <div class="flex flex-col gap-1">
                <p class="text-base font-bold text-black">
                  {{ item.thu_cung?.ten_thu_cung || 'N/A' }}
                  <span class="text-sm font-normal text-gray-500 ml-1">
                    ({{ item.thu_cung?.loai_thu_cung || '' }})
                  </span>
                </p>
                <p class="text-sm text-gray-600">
                  Chủ: {{ item.khach_hang?.full_name || 'N/A' }}
                  <span v-if="item.khach_hang?.rank" class="ml-2 px-2 py-0.5 rounded text-xs font-medium"
                    :class="{
                      'bg-gray-100 text-gray-600': item.khach_hang.rank === 'Silver',
                      'bg-yellow-100 text-yellow-700': item.khach_hang.rank === 'Gold',
                      'bg-blue-100 text-blue-700': item.khach_hang.rank === 'Diamond',
                    }">
                    {{ item.khach_hang.rank }}
                  </span>
                </p>
              </div>
              <div class="flex flex-col items-end gap-1">
                <p class="text-lg font-bold text-[#155dfc]">
                  {{ formatCurrency(item.dich_vu?.gia_tien || 0) }}
                </p>
                <span class="bg-yellow-100 border-transparent rounded-lg px-2 py-0.5 text-xs font-medium text-yellow-700">
                  Chờ thanh toán
                </span>
              </div>
            </div>
            <div class="flex items-center gap-4 mt-1">
              <p class="text-xs text-gray-500">
                Dịch vụ: {{ item.dich_vu?.ten || 'N/A' }}
              </p>
              <p class="text-xs text-gray-500">
                Khám xong: {{ formatTime(item.thoi_gian_hoan_thanh) }}
              </p>
            </div>
          </button>
        </div>
      </div>
    </div>

    <!-- MÀN HÌNH THU TIỀN -->
    <div v-else class="flex flex-col gap-4">
      <button
        class="flex items-center gap-2 text-gray-600 hover:text-black transition-colors w-fit"
        @click="selectedLichHen = null"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        <span class="text-sm font-medium">Quay lại danh sách</span>
      </button>

      <ThanhToanForm
        :lich-hen="selectedLichHen"
        @complete="handlePaymentComplete"
      />
    </div>

    <!-- Modal tạo hóa đơn bán lẻ -->
    <CreateInvoice
      :is-open="isCreateInvoiceOpen"
      @close="isCreateInvoiceOpen = false"
      @complete="handleRetailInvoiceComplete"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import ThanhToanForm from './thanh_toan_form/index.vue'
import CreateInvoice from './create-invoice/index.vue'
import api from '@/utils/api'
import { useRoute } from 'vue-router'

const route = useRoute()

const loading = ref(false)
const searchQuery = ref('')
const selectedLichHen = ref(null)
const isCreateInvoiceOpen = ref(false)
const lichHenList = ref([])

// Fetch lịch hẹn đã khám xong, chưa thanh toán
const fetchPendingPayments = async () => {
  loading.value = true
  try {
    const res = await api.get('/lich-hen-all', {
      params: {
        trang_thai: 'completed',
        chua_thanh_toan: 1,
        per_page: 100,
      }
    })
    const data = res.data?.data
    lichHenList.value = Array.isArray(data)
      ? data
      : (data?.data || [])

    // Lọc thêm phía client: chỉ lấy những cái chưa có thanh_toan_id
    lichHenList.value = lichHenList.value.filter(item => !item.thanh_toan_id)
  } catch (err) {
    console.error('Lỗi tải danh sách:', err)
  } finally {
    loading.value = false
  }
}

const filteredList = computed(() => {
  if (!searchQuery.value) return lichHenList.value
  const q = searchQuery.value.toLowerCase()
  return lichHenList.value.filter(item =>
    item.khach_hang?.full_name?.toLowerCase().includes(q) ||
    item.thu_cung?.ten_thu_cung?.toLowerCase().includes(q) ||
    item.dich_vu?.ten?.toLowerCase().includes(q)
  )
})

const selectLichHen = (item) => {
  selectedLichHen.value = item
}

const handlePaymentComplete = () => {
  selectedLichHen.value = null
  fetchPendingPayments()
}

const handleRetailInvoiceComplete = () => {
  isCreateInvoiceOpen.value = false
  fetchPendingPayments()
}

const formatCurrency = (val) => {
  return new Intl.NumberFormat('vi-VN').format(val) + ' ₫'
}

const formatTime = (dt) => {
  if (!dt) return '--'
  return new Date(dt).toLocaleString('vi-VN', {
    day: '2-digit', month: '2-digit',
    hour: '2-digit', minute: '2-digit'
  })
}

onMounted(async () => {
  await fetchPendingPayments()
  
  // Tự động mở lịch hẹn nếu có lich_hen_id trên URL
  const lichHenId = route.query.lich_hen_id
  if (lichHenId) {
    const found = lichHenList.value.find(item => item.id == lichHenId)
    if (found) {
      selectedLichHen.value = found
    }
  }
})
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap");
* { font-family: "Nunito Sans", sans-serif; }
</style>