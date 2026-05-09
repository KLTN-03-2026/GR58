<template>
  <div class="w-full min-h-screen px-8 py-6 flex flex-col gap-6" @click.self="openDropdown = null">
    <!-- Page Header -->
    <div class="flex flex-col gap-2">
      <h1 class="text-2xl font-semibold text-black">Danh sách Hóa đơn</h1>
      <p class="text-gray-500 font-medium text-base">Quản lý và theo dõi dòng tiền</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-3 gap-6">
      <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 flex items-center justify-between">
        <div class="flex flex-col gap-1">
          <p class="text-sm font-medium text-black">Doanh thu hôm nay</p>
          <p class="font-nunito text-[30px] leading-9 text-[#00a63e] tracking-[0.3955px]">
            {{ formatCurrency(summary.doanh_thu_hom_nay) }}
          </p>
          <p class="font-nunito text-xs leading-4 text-[#6a7282]">Đã trừ hoàn tiền</p>
        </div>
        <div class="bg-green-100 rounded-full w-12 h-12 flex items-center justify-center">
          <ArrowUpIcon class="w-6 h-6 text-green-500" />
        </div>
      </div>

      <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 flex items-center justify-between">
        <div class="flex flex-col gap-1">
          <p class="text-sm font-medium text-black">Chưa thanh toán</p>
          <p class="font-nunito text-[30px] leading-9 text-[#f54900] tracking-[0.3955px]">
            {{ formatCurrency(summary.chua_thanh_toan) }}
          </p>
          <p class="font-nunito text-xs leading-4 text-[#6a7282]">Tổng giá trị HĐ chưa thanh toán</p>
        </div>
        <div class="bg-[#ffedd4] rounded-full w-12 h-12 flex items-center justify-center">
          <InforIcon class="w-6 h-6 text-[#f54900]" />
        </div>
      </div>

      <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 flex items-center justify-between">
        <div class="flex flex-col gap-1">
          <p class="text-sm font-medium text-black">Đã hoàn tiền</p>
          <p class="font-nunito text-[30px] leading-9 text-[#9810fa] tracking-[0.3955px]">
            {{ formatCurrency(summary.da_hoan_tien) }}
          </p>
          <p class="font-nunito text-xs leading-4 text-[#6a7282]">Tiền trả lại khách</p>
        </div>
        <div class="bg-purple-100 rounded-full w-12 h-12 flex items-center justify-center">
          <AroundIcon class="w-6 h-6 text-[#9810fa]" />
        </div>
      </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div class="flex flex-col gap-4">
        <!-- Search -->
        <div class="relative">
          <input
            v-model="filters.search"
            @input="onSearchInput"
            type="text"
            placeholder="Tìm theo Mã hóa đơn, Tên khách hàng, SĐT..."
            class="bg-[#f3f3f5] border !border-transparent rounded-lg h-9 pl-10 pr-3 w-full font-nunito text-sm text-[#717182] tracking-tight focus:outline-none focus:ring-2 focus:ring-[#009689]"
          />
          <SearchIcon class="absolute left-3 top-[9px] w-5 h-5" />
        </div>

        <!-- Filters Row -->
        <div class="grid grid-cols-4 gap-4">

          <!-- Period Dropdown -->
          <div class="relative">
            <button
              @click.stop="toggleDropdown('period')"
              class="bg-[#f3f3f5] border !border-gray-300 rounded-lg h-9 px-[13px] w-full flex items-center justify-between hover:bg-gray-200 transition-colors"
            >
              <span class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight">
                {{ periodOptions.find(o => o.value === filters.period)?.label }}
              </span>
              <ChevronDownIcon />
            </button>
            <div
              v-if="openDropdown === 'period'"
              class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
            >
              <button
                v-for="opt in periodOptions"
                :key="opt.value"
                @click.stop="selectFilter('period', opt.value)"
                class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 transition-colors"
                :class="filters.period === opt.value ? 'text-[#009689] font-semibold bg-teal-50' : 'text-gray-700'"
              >
                {{ opt.label }}
              </button>
            </div>
          </div>

          <!-- Status Dropdown -->
          <div class="relative">
            <button
              @click.stop="toggleDropdown('status')"
              class="bg-[#f3f3f5] border !border-gray-300 rounded-lg h-9 px-[13px] w-full flex items-center justify-between hover:bg-gray-200 transition-colors"
            >
              <span class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight">
                {{ statusOptions.find(o => o.value === filters.trang_thai)?.label }}
              </span>
              <ChevronDownIcon />
            </button>
            <div
              v-if="openDropdown === 'status'"
              class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
            >
              <button
                v-for="opt in statusOptions"
                :key="opt.value"
                @click.stop="selectFilter('trang_thai', opt.value)"
                class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 transition-colors"
                :class="filters.trang_thai === opt.value ? 'text-[#009689] font-semibold bg-teal-50' : 'text-gray-700'"
              >
                {{ opt.label }}
              </button>
            </div>
          </div>

          <!-- Payment Method Dropdown -->
          <div class="relative">
            <button
              @click.stop="toggleDropdown('method')"
              class="bg-[#f3f3f5] border !border-gray-300 rounded-lg h-9 px-[13px] w-full flex items-center justify-between hover:bg-gray-200 transition-colors"
            >
              <span class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight">
                {{ methodOptions.find(o => o.value === filters.hinh_thuc)?.label }}
              </span>
              <ChevronDownIcon />
            </button>
            <div
              v-if="openDropdown === 'method'"
              class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
            >
              <button
                v-for="opt in methodOptions"
                :key="opt.value"
                @click.stop="selectFilter('hinh_thuc', opt.value)"
                class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 transition-colors"
                :class="filters.hinh_thuc === opt.value ? 'text-[#009689] font-semibold bg-teal-50' : 'text-gray-700'"
              >
                {{ opt.label }}
              </button>
            </div>
          </div>

          <!-- Export Excel -->
          <button
            @click="exportExcel"
            class="bg-white border !border-gray-300 rounded-lg h-9 px-3 flex items-center justify-center gap-2 hover:bg-gray-50 transition-colors"
          >
            <DownloadIcon class="w-4 h-4" />
            <span class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight">Xuất Excel</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Invoice Table -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <!-- Loading -->
      <div v-if="loading" class="flex items-center justify-center py-16">
        <div class="w-8 h-8 border-4 border-[#009689] border-t-transparent rounded-full animate-spin"></div>
        <span class="ml-3 text-gray-500 text-sm">Đang tải dữ liệu...</span>
      </div>

      <!-- Empty -->
      <div v-else-if="!invoices.length" class="flex flex-col items-center justify-center py-16">
        <p class="text-gray-400 text-sm">Không có hóa đơn nào</p>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-black/10">
              <th class="text-left px-2 py-[10px] font-nunito font-medium text-sm text-neutral-950">Mã HĐ</th>
              <th class="text-left px-2 py-[10px] font-nunito font-medium text-sm text-neutral-950">Thời gian</th>
              <th class="text-left px-2 py-[10px] font-nunito font-medium text-sm text-neutral-950">Khách hàng</th>
              <th class="text-right px-2 py-[10px] font-nunito font-medium text-sm text-neutral-950">Tổng giá trị</th>
              <th class="text-right px-2 py-[10px] font-nunito font-medium text-sm text-neutral-950">Đã thanh toán</th>
              <th class="text-left px-2 py-[10px] font-nunito font-medium text-sm text-neutral-950">Hình thức</th>
              <th class="text-left px-2 py-[10px] font-nunito font-medium text-sm text-neutral-950">Trạng thái</th>
              <th class="text-left px-2 py-[10px] font-nunito font-medium text-sm text-neutral-950">Người thu</th>
              <th class="text-right px-2 py-[10px] font-nunito font-medium text-sm text-neutral-950">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(invoice, index) in invoices"
              :key="invoice.id ?? index"
              class="border-b border-black/10 hover:bg-gray-50 transition-colors"
            >
              <td class="px-2 py-4">
                <p class="font-nunito font-medium text-base text-[#009689]">{{ invoice.code }}</p>
              </td>
              <td class="px-2 py-[8.5px]">
                <div class="flex flex-col gap-1">
                  <p class="font-nunito text-sm text-[#101828]">{{ invoice.time }}</p>
                  <p class="font-nunito text-xs text-[#6a7282]">{{ invoice.date }}</p>
                </div>
              </td>
              <td class="px-2 py-[8.5px]">
                <div class="flex flex-col gap-1">
                  <p class="font-nunito text-sm text-[#101828]">{{ invoice.customer }}</p>
                  <p class="font-nunito text-xs text-[#6a7282]">Pet: {{ invoice.petName }}</p>
                </div>
              </td>
              <td class="px-2 py-[19px] text-right">
                <p class="font-nunito text-sm text-[#101828]">{{ formatCurrency(invoice.totalAmount) }}</p>
              </td>
              <td class="px-2 py-5 text-right">
                <p :class="['font-nunito text-sm', invoice.paidAmount > 0 ? getPaidAmountColor(invoice.status) : 'text-[#99a1af]']">
                  {{ invoice.paidAmount > 0 ? formatCurrency(invoice.paidAmount) : '0 đ' }}
                </p>
              </td>
              <td class="px-2 py-[17.5px]">
                <span :class="['inline-flex items-center px-2 py-[3px] rounded-lg font-nunito font-medium text-xs border', getPaymentMethodStyle(invoice.paymentMethod)]">
                  {{ getPaymentMethodLabel(invoice.paymentMethod) }}
                </span>
              </td>
              <td class="px-2 py-[17.5px]">
                <span :class="['inline-flex items-center px-2 py-[3px] rounded-lg font-nunito font-medium text-xs', getStatusStyle(invoice.status)]">
                  {{ getStatusLabel(invoice.status) }}
                </span>
              </td>
              <td class="px-2 py-[8.5px]">
                <p class="font-nunito text-sm text-[#101828]">{{ invoice.collector }}</p>
              </td>
              <td class="px-2 py-[12.5px]">
                <div class="flex items-center justify-end gap-2">
                  <button @click="handleViewInvoice(invoice)" class="w-9 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg transition-colors">
                    <EyeIcon class="w-4 h-4" />
                  </button>
                  <button class="w-9 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg transition-colors">
                    <PrintIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
          <p class="text-sm text-gray-500">
            Hiển thị {{ (pagination.current_page - 1) * pagination.per_page + 1 }}–{{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}
            / {{ pagination.total }} hóa đơn
          </p>
          <div class="flex items-center gap-2">
            <button
              @click="changePage(pagination.current_page - 1)"
              :disabled="pagination.current_page === 1"
              class="px-3 py-1 rounded-lg border text-sm disabled:opacity-40 hover:bg-gray-50 transition-colors"
            >Trước</button>
            <span class="text-sm text-gray-600">Trang {{ pagination.current_page }} / {{ pagination.last_page }}</span>
            <button
              @click="changePage(pagination.current_page + 1)"
              :disabled="pagination.current_page === pagination.last_page"
              class="px-3 py-1 rounded-lg border text-sm disabled:opacity-40 hover:bg-gray-50 transition-colors"
            >Sau</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <ChiTietHoaDon
      v-if="isInvoiceDetailModalOpen"
      :invoice="selectedInvoice"
      @close="isInvoiceDetailModalOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import ChiTietHoaDon from './invoice-detail/index.vue'
import { getInvoices, getInvoiceDetail } from '@/services/invoiceService'

import ArrowUpIcon     from '@/assets/svg/arrow-up.svg'
import ChevronDownIcon from '@/assets/svg/chevron-down.svg'
import PrintIcon       from '@/assets/svg/print.svg'
import SearchIcon      from '@/assets/svg/search.svg'
import EyeIcon         from '@/assets/svg/eye.svg'
import AroundIcon      from '@/assets/svg/around.svg'
import InforIcon       from '@/assets/svg/infor.svg'
import DownloadIcon    from '@/assets/svg/download.svg'

// ─── State ────────────────────────────────────────────────────────────
const loading    = ref(false)
const invoices   = ref([])
const openDropdown = ref(null)
const isInvoiceDetailModalOpen = ref(false)
const selectedInvoice = ref(null)

const summary = reactive({
  doanh_thu_hom_nay: 0,
  chua_thanh_toan: 0,
  da_hoan_tien: 0,
})

const pagination = reactive({
  total: 0, per_page: 20, current_page: 1, last_page: 1,
})

const filters = reactive({
  search: '', period: 'today', trang_thai: 'all', hinh_thuc: 'all',
})

// ─── Options ──────────────────────────────────────────────────────────
const periodOptions = [
  { value: 'today',      label: 'Hôm nay' },
  { value: '7days',      label: '7 ngày qua' },
  { value: '30days',     label: '30 ngày qua' },
  { value: 'this_month', label: 'Tháng này' },
]

const statusOptions = [
  { value: 'all',            label: 'Tất cả' },
  { value: 'da_thanh_toan',  label: 'Đã thanh toán' },
  { value: 'cho_thanh_toan', label: 'Chưa thanh toán' },
  { value: 'hoan_tien',      label: 'Đã hoàn tiền' },
]

const methodOptions = [
  { value: 'all',      label: 'Tất cả' },
  { value: 'tien_mat', label: 'Tiền mặt' },
  { value: 'vnpay',    label: 'VNPay' },
  { value: 'momo',     label: 'MoMo' },
  { value: 'ket_hop',  label: 'Kết hợp' },
]

// ─── Fetch ────────────────────────────────────────────────────────────
let searchTimer = null

const fetchData = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      search:     filters.search   || undefined,
      period:     filters.period,
      trang_thai: filters.trang_thai !== 'all' ? filters.trang_thai : undefined,
      hinh_thuc:  filters.hinh_thuc  !== 'all' ? filters.hinh_thuc  : undefined,
      page,
      per_page: pagination.per_page,
    }

    const res = await getInvoices(params)

    if (res.status) {
      invoices.value = res.data.invoices ?? []

      Object.assign(summary, res.data.summary)
      Object.assign(pagination, res.data.pagination)
    }
  } catch (err) {
    console.error('Lỗi tải hóa đơn:', err)
  } finally {
    loading.value = false
  }
}

// ─── Events ───────────────────────────────────────────────────────────
const onSearchInput = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => fetchData(1), 400)
}

// Dùng @click.stop trên từng dropdown nên không cần document listener
const toggleDropdown = (name) => {
  openDropdown.value = openDropdown.value === name ? null : name
}

const selectFilter = (key, value) => {
  filters[key] = value
  openDropdown.value = null
  fetchData(1)
}

const changePage = (page) => {
  if (page < 1 || page > pagination.last_page) return
  fetchData(page)
}

const exportExcel = () => {
  const token  = localStorage.getItem('auth_token') ?? localStorage.getItem('token') ?? ''
  const params = new URLSearchParams({
    export:     'excel',
    period:     filters.period,
    trang_thai: filters.trang_thai,
    hinh_thuc:  filters.hinh_thuc,
    search:     filters.search,
    token,
  })
  window.open(`/api/invoices/export?${params}`, '_blank')
}

const handleViewInvoice = async (invoice) => {
  try {
    const res = await getInvoiceDetail(invoice.id)
    selectedInvoice.value = res.status ? res.data : invoice
  } catch {
    selectedInvoice.value = invoice
  }
  isInvoiceDetailModalOpen.value = true
}

// ─── Lifecycle ────────────────────────────────────────────────────────
onMounted(() => fetchData())

// Đóng dropdown khi click ra ngoài bằng cách dùng @click.stop trực tiếp
// nên không cần thêm document event listener

// ─── Formatters ───────────────────────────────────────────────────────
const formatCurrency = (amount) =>
  new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', minimumFractionDigits: 0 })
    .format(amount ?? 0).replace('₫', 'đ')

const getPaymentMethodLabel = (m) =>
  ({ cash: 'Tiền mặt', vnpay: 'VNPay', momo: 'MoMo', transfer: 'Chuyển khoản' }[m] ?? m)

const getPaymentMethodStyle = (m) =>
  ({ cash: 'bg-gray-100 border-black/10 text-[#364153]', vnpay: 'bg-purple-100 border-black/10 text-[#8200db]', momo: 'bg-pink-100 border-black/10 text-[#e11d48]', transfer: 'bg-blue-100 border-black/10 text-[#1447e6]' }[m] ?? 'bg-gray-100 border-black/10 text-[#364153]')

const getStatusLabel = (s) =>
  ({ paid: 'Đã thanh toán', unpaid: 'Chưa thanh toán', refunded: 'Đã hoàn tiền', refunding: 'Đang hoàn tiền' }[s] ?? s)

const getStatusStyle = (s) =>
  ({ paid: 'bg-green-100 text-[#008236]', unpaid: 'bg-[#ffedd4] text-[#ca3500]', refunded: 'bg-blue-100 text-[#1447e6]', refunding: 'bg-[#fef9c2] text-[#a65f00]' }[s] ?? 'bg-gray-100 text-[#364153]')

const getPaidAmountColor = (s) =>
  ({ paid: 'text-[#00a63e]', refunded: 'text-[#155dfc]', refunding: 'text-[#d08700]' }[s] ?? 'text-[#101828]')
</script>

<style scoped>
::-webkit-scrollbar { height: 8px; }
::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: #555; }
</style>