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
              :placeholder="activeInvoiceTab === 'pending' ? 'Nhập tên khách hàng, tên thú cưng...' : 'Tìm mã hoá đơn, tên khách, SĐT...'"
              class="w-full h-11 bg-gray-50 border !border-gray-300 rounded-lg pl-4 pr-4 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
            />
          </div>
          <button
            disabled
            class="bg-gray-300 rounded-lg px-4 py-2.5 h-10 flex items-center gap-2 cursor-not-allowed shrink-0"
            title="Tính năng đang phát triển"
          >
            <span class="text-sm font-medium text-gray-500">Tạo hóa đơn bán lẻ</span>
          </button>
        </div>
      </div>

      <!-- Tabs: Chờ thanh toán / Lịch sử -->
      <div class="bg-white border !border-gray-300 rounded-[14px] shadow-sm overflow-hidden">
        <div class="flex border-b !border-gray-300">
          <button
            :class="['flex-1 h-12 flex items-center justify-center border-b-2 transition-colors',
              activeInvoiceTab === 'pending' ? 'bg-blue-50 border-[#1447e6]' : 'border-transparent hover:bg-gray-50']"
            @click="activeInvoiceTab = 'pending'"
          >
            <span :class="['text-sm font-medium', activeInvoiceTab === 'pending' ? 'text-[#1447e6]' : 'text-gray-600']">
              Chờ thanh toán
            </span>
            <span v-if="filteredList.length" class="ml-2 bg-yellow-100 text-yellow-700 text-xs font-medium px-2 py-0.5 rounded-full">
              {{ filteredList.length }}
            </span>
          </button>
          <button
            :class="['flex-1 h-12 flex items-center justify-center border-b-2 transition-colors',
              activeInvoiceTab === 'history' ? 'bg-blue-50 border-[#1447e6]' : 'border-transparent hover:bg-gray-50']"
            @click="activeInvoiceTab = 'history'; if (invoiceHistory.length === 0) fetchInvoiceHistory()"
          >
            <span :class="['text-sm font-medium', activeInvoiceTab === 'history' ? 'text-[#1447e6]' : 'text-gray-600']">
              Lịch sử thanh toán
            </span>
          </button>
        </div>

        <!-- Loading -->
        <div v-if="loading || historyLoading" class="flex justify-center items-center py-16">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#009689]"></div>
        </div>

        <!-- TAB: Chờ thanh toán -->
        <div v-else-if="activeInvoiceTab === 'pending'" class="p-6">
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
                    {{ formatCurrency(getDisplayAmount(item)) }}
                  </p>
                  <span
                    :class="[
                      'rounded-lg px-2 py-0.5 text-xs font-medium',
                      item._loai === 'thu_thuoc_bo_sung'
                        ? 'bg-amber-100 text-amber-700'
                        : 'bg-yellow-100 text-yellow-700'
                    ]"
                  >
                    {{ item._loai === 'thu_thuoc_bo_sung' ? 'Thu thuốc bổ sung' : 'Chờ thanh toán' }}
                  </span>
                </div>
              </div>
              <div class="flex items-center gap-4 mt-1">
                <p class="text-xs text-gray-500">
                  Dịch vụ: {{ item.dich_vus?.length ? item.dich_vus.map(d => d.ten).join(", ") : (item.dich_vu?.ten || 'N/A') }}
                </p>
                <p class="text-xs text-gray-500">
                  Khám xong: {{ formatTime(item.thoi_gian_hoan_thanh) }}
                </p>
              </div>
            </button>
          </div>
        </div>

        <!-- TAB: Lịch sử thanh toán -->
        <div v-else-if="activeInvoiceTab === 'history'" class="p-6">
          <!-- Filters -->
          <div class="flex flex-wrap items-center gap-3 mb-4">
            <select
              v-model="historyPeriod"
              @change="fetchInvoiceHistory"
              class="h-9 px-3 border !border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#009689]"
            >
              <option value="today">Hôm nay</option>
              <option value="7days">7 ngày</option>
              <option value="30days">30 ngày</option>
              <option value="this_month">Tháng này</option>
            </select>
            <select
              v-model="historyMethod"
              @change="fetchInvoiceHistory"
              class="h-9 px-3 border !border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#009689]"
            >
              <option value="all">Tất cả phương thức</option>
              <option value="tien_mat">Tiền mặt</option>
              <option value="chuyen_khoan">Chuyển khoản</option>
              <option value="momo">MoMo</option>
            </select>
            <select
              v-model="historyStatus"
              @change="fetchInvoiceHistory"
              class="h-9 px-3 border !border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#009689]"
            >
              <option value="all">Tất cả trạng thái</option>
              <option value="da_thanh_toan">Đã thanh toán</option>
              <option value="cho_thanh_toan">Chờ thanh toán</option>
              <option value="hoan_tien">Hoàn tiền</option>
            </select>
          </div>

          <!-- History Table -->
          <div v-if="filteredHistory.length === 0" class="text-center py-12 text-gray-400">
            Không có hoá đơn nào
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b !border-gray-300">
                  <th class="text-left px-3 py-2 text-sm font-medium text-gray-600">Mã</th>
                  <th class="text-left px-3 py-2 text-sm font-medium text-gray-600">Ngày</th>
                  <th class="text-left px-3 py-2 text-sm font-medium text-gray-600">Khách hàng</th>
                  <th class="text-right px-3 py-2 text-sm font-medium text-gray-600">Tổng tiền</th>
                  <th class="text-left px-3 py-2 text-sm font-medium text-gray-600">Phương thức</th>
                  <th class="text-left px-3 py-2 text-sm font-medium text-gray-600">Trạng thái</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="inv in filteredHistory"
                  :key="inv.id"
                  class="border-b !border-gray-200 hover:bg-gray-50 cursor-pointer"
                  @click="viewInvoiceDetail(inv)"
                >
                  <td class="px-3 py-3 text-sm text-black font-medium">{{ inv.code }}</td>
                  <td class="px-3 py-3 text-sm text-gray-600">{{ inv.date }}</td>
                  <td class="px-3 py-3 text-sm text-gray-700">{{ inv.customer }}</td>
                  <td class="px-3 py-3 text-sm text-right font-semibold text-black">{{ formatCurrency(inv.paidAmount || inv.totalAmount) }}</td>
                  <td class="px-3 py-3">
                    <span :class="['px-2 py-0.5 rounded text-xs font-medium',
                      inv.paymentMethod === 'cash' ? 'bg-green-100 text-green-700' :
                      inv.paymentMethod === 'transfer' ? 'bg-blue-100 text-blue-700' :
                      'bg-purple-100 text-purple-700']">
                      {{ inv.paymentMethod === 'cash' ? 'Tiền mặt' : inv.paymentMethod === 'transfer' ? 'Chuyển khoản' : inv.paymentMethod }}
                    </span>
                  </td>
                  <td class="px-3 py-3">
                    <span :class="['px-2 py-0.5 rounded text-xs font-medium',
                      inv.status === 'paid' ? 'bg-green-100 text-green-700' :
                      inv.status === 'refunded' ? 'bg-red-100 text-red-600' :
                      'bg-yellow-100 text-yellow-700']">
                      {{ inv.status === 'paid' ? 'Đã TT' : inv.status === 'refunded' ? 'Hoàn tiền' : 'Chờ TT' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="historyPagination.lastPage > 1" class="flex items-center justify-between mt-4 pt-4 border-t !border-gray-200">
              <p class="text-sm text-gray-500">
                Trang {{ historyPagination.currentPage }} / {{ historyPagination.lastPage }}
                ({{ historyPagination.total }} hoá đơn)
              </p>
              <div class="flex items-center gap-1">
                <button
                  @click="historyPagination.currentPage > 1 && (historyPagination.currentPage--, fetchInvoiceHistory())"
                  :disabled="historyPagination.currentPage <= 1"
                  class="w-8 h-8 rounded-lg border !border-gray-300 flex items-center justify-center disabled:opacity-50"
                >
                  &lt;
                </button>
                <button
                  @click="historyPagination.currentPage < historyPagination.lastPage && (historyPagination.currentPage++, fetchInvoiceHistory())"
                  :disabled="historyPagination.currentPage >= historyPagination.lastPage"
                  class="w-8 h-8 rounded-lg border !border-gray-300 flex items-center justify-center disabled:opacity-50"
                >
                  &gt;
                </button>
              </div>
            </div>
          </div>
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
import invoiceService from '@/services/invoiceService'
import { useRoute } from 'vue-router'

const route = useRoute()

const loading = ref(false)
const searchQuery = ref('')
const selectedLichHen = ref(null)
const isCreateInvoiceOpen = ref(false)
const lichHenList = ref([])
const activeInvoiceTab = ref('pending')

// Invoice history state
const historyLoading = ref(false)
const invoiceHistory = ref([])
const historyPeriod = ref('7days')
const historyMethod = ref('all')
const historyStatus = ref('all')
const historyPagination = ref({ currentPage: 1, lastPage: 1, total: 0 })

// Fetch lịch hẹn đã khám xong, chưa thanh toán + chờ thu thuốc bổ sung
const fetchPendingPayments = async () => {
  loading.value = true
  try {
    const [resChuaTT, resChoThuoc] = await Promise.all([
      api.get('/lich-hen-all', {
        params: { trang_thai: 'completed', chua_thanh_toan: 1, per_page: 100 }
      }),
      api.get('/lich-hen-all', {
        params: { trang_thai: 'completed', cho_thu_thuoc: 1, per_page: 100 }
      }),
    ])

    const dataChuaTT = resChuaTT.data?.data
    const listChuaTT = (Array.isArray(dataChuaTT) ? dataChuaTT : (dataChuaTT?.data || []))
      .filter(item => !item.thanh_toan_id)
      .map(item => ({ ...item, _loai: 'chua_thanh_toan' }))

    const dataChoThuoc = resChoThuoc.data?.data
    const listChoThuoc = (Array.isArray(dataChoThuoc) ? dataChoThuoc : (dataChoThuoc?.data || []))
      .map(item => ({ ...item, _loai: 'thu_thuoc_bo_sung' }))

    // Merge, deduplicate by id
    const merged = [...listChuaTT]
    for (const item of listChoThuoc) {
      if (!merged.find(m => m.id === item.id)) {
        merged.push(item)
      }
    }
    lichHenList.value = merged
  } catch (err) {
    console.error('Lỗi tải danh sách:', err)
  } finally {
    loading.value = false
  }
}

// Fetch invoice history
const fetchInvoiceHistory = async () => {
  historyLoading.value = true
  try {
    const params = {
      page: historyPagination.value.currentPage,
      per_page: 15,
      period: historyPeriod.value,
    }
    if (historyMethod.value !== 'all') params.hinh_thuc = historyMethod.value
    if (historyStatus.value !== 'all') params.trang_thai = historyStatus.value
    if (searchQuery.value) params.search = searchQuery.value

    const res = await invoiceService.getInvoices(params)
    if (res.status) {
      const d = res.data || res
      invoiceHistory.value = d.invoices || []
      historyPagination.value = {
        currentPage: d.pagination?.current_page || 1,
        lastPage: d.pagination?.last_page || 1,
        total: d.pagination?.total || 0,
      }
    }
  } catch (err) {
    console.error('Lỗi tải lịch sử:', err)
  } finally {
    historyLoading.value = false
  }
}

const filteredList = computed(() => {
  if (!searchQuery.value) return lichHenList.value
  const q = searchQuery.value.toLowerCase()
  return lichHenList.value.filter(item =>
    item.khach_hang?.full_name?.toLowerCase().includes(q) ||
    item.thu_cung?.ten_thu_cung?.toLowerCase().includes(q) ||
    item.dich_vu?.ten?.toLowerCase().includes(q) ||
    item.dich_vus?.some(d => d.ten?.toLowerCase().includes(q))
  )
})

const filteredHistory = computed(() => {
  if (!searchQuery.value || activeInvoiceTab.value !== 'history') return invoiceHistory.value
  const q = searchQuery.value.toLowerCase()
  return invoiceHistory.value.filter(inv =>
    inv.code?.toLowerCase().includes(q) ||
    inv.customer?.toLowerCase().includes(q) ||
    inv.phone?.toLowerCase().includes(q)
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

const viewInvoiceDetail = async (inv) => {
  try {
    const res = await invoiceService.getInvoiceDetail(inv.id)
    if (res.status) {
      alert(`Chi tiết hoá đơn: ${inv.code}\nKhách: ${inv.customer}\nTổng: ${formatCurrency(inv.totalAmount)}`)
    }
  } catch (err) {
    console.error('Lỗi tải chi tiết:', err)
  }
}

const formatCurrency = (val) => {
  return new Intl.NumberFormat('vi-VN').format(val) + ' ₫'
}

const getDisplayAmount = (item) => {
  if (item?.tong_tien_hien_thi !== null && item?.tong_tien_hien_thi !== undefined) {
    return Number(item.tong_tien_hien_thi) || 0
  }
  if (item?.tong_tien !== null && item?.tong_tien !== undefined) {
    return Number(item.tong_tien) || 0
  }
  return Number(item?.dich_vu?.gia_tien) || 0
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
