<template>
  <div class="w-full min-h-screen px-8 py-6 flex flex-col gap-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-2">
      <h1 class="text-2xl font-semibold text-black">Báo cáo Doanh thu</h1>
      <p class="text-gray-500 font-medium text-base">
        Phân tích hiệu quả kinh doanh và xu hướng doanh thu
      </p>
    </div>

    <!-- Filter Card -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div class="flex items-center justify-between w-full gap-4">

        <!-- Period Filter -->
        <div class="relative flex-1" ref="periodDropdownRef">
          <button
            @click="showPeriodDropdown = !showPeriodDropdown"
            class="w-full flex items-center justify-between px-4 py-2 bg-gray-100 rounded-[8px]"
          >
            <span class="text-[14px] leading-[20px] text-neutral-950">
              {{ selectedPeriodLabel }}
            </span>
            <ChevronDownIcon class="w-4 h-4" />
          </button>
          <div
            v-if="showPeriodDropdown"
            class="absolute top-full left-0 mt-1 w-full bg-white border border-gray-200 rounded-[8px] shadow-lg z-20"
          >
            <button
              v-for="opt in periodOptions"
              :key="opt.value"
              @click="selectPeriod(opt)"
              class="w-full text-left px-4 py-2 text-[14px] hover:bg-gray-50 first:rounded-t-[8px] last:rounded-b-[8px]"
              :class="selectedPeriod === opt.value ? 'text-teal-600 font-semibold' : 'text-neutral-950'"
            >
              {{ opt.label }}
            </button>
          </div>
        </div>

        <!-- Custom date range (hiện khi chọn "Tùy chỉnh") -->
        <template v-if="selectedPeriod === 'custom'">
          <input
            type="date"
            v-model="customStart"
            @change="fetchReport"
            class="flex-1 px-4 py-2 bg-gray-100 rounded-[8px] text-[14px] border border-gray-200"
          />
          <span class="text-gray-400">–</span>
          <input
            type="date"
            v-model="customEnd"
            @change="fetchReport"
            class="flex-1 px-4 py-2 bg-gray-100 rounded-[8px] text-[14px] border border-gray-200"
          />
        </template>

        <!-- Service Filter -->
        <div class="relative flex-1" ref="serviceDropdownRef">
          <button
            @click="showServiceDropdown = !showServiceDropdown"
            class="w-full flex items-center justify-between px-4 py-2 bg-gray-100 rounded-[8px]"
          >
            <span class="text-[14px] leading-[20px] text-neutral-950 truncate">
              {{ selectedServiceLabel }}
            </span>
            <ChevronDownIcon class="w-4 h-4 flex-shrink-0" />
          </button>
          <div
            v-if="showServiceDropdown"
            class="absolute top-full left-0 mt-1 w-full bg-white border border-gray-200 rounded-[8px] shadow-lg z-20 max-h-48 overflow-y-auto"
          >
            <button
              @click="selectService({ id: 'all', ten: 'Tất cả dịch vụ' })"
              class="w-full text-left px-4 py-2 text-[14px] hover:bg-gray-50 first:rounded-t-[8px]"
              :class="selectedService === 'all' ? 'text-teal-600 font-semibold' : 'text-neutral-950'"
            >
              Tất cả dịch vụ
            </button>
            <button
              v-for="svc in services"
              :key="svc.id"
              @click="selectService(svc)"
              class="w-full text-left px-4 py-2 text-[14px] hover:bg-gray-50 last:rounded-b-[8px]"
              :class="selectedService === svc.id ? 'text-teal-600 font-semibold' : 'text-neutral-950'"
            >
              {{ svc.ten }}
            </button>
          </div>
        </div>

        <!-- Export Button -->
        <button
          @click="exportExcel"
          class="flex items-center justify-center gap-[8px] px-4 py-2 bg-[#5a9690] rounded-[8px] min-w-[180px]"
        >
          <DownloadIcon class="w-4 h-4 text-white" />
          <span class="font-medium text-[14px] text-white">Xuất báo cáo Excel</span>
        </button>
      </div>

      <!-- Period info -->
      <p v-if="reportData" class="text-[12px] text-gray-400 mt-3">
        Dữ liệu từ {{ formatDate(reportData.period.start) }} đến {{ formatDate(reportData.period.end) }}
      </p>
    </div>

    <!-- Loading skeleton -->
    <div v-if="isLoading" class="grid grid-cols-4 gap-6">
      <div
        v-for="i in 4"
        :key="i"
        class="bg-white border border-gray-300 shadow-sm rounded-[14px] p-[24px] h-[120px] animate-pulse bg-gray-100"
      />
    </div>

    <!-- Error state -->
    <div
      v-else-if="errorMessage"
      class="bg-red-50 border border-red-200 rounded-[14px] p-6 text-red-700 text-sm"
    >
      ⚠️ {{ errorMessage }}
    </div>

    <template v-else-if="reportData">
      <!-- Stats Cards -->
      <div class="grid grid-cols-4 gap-6">
        <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-[24px] flex flex-col gap-[16px]">
          <div class="flex flex-col gap-[4px]">
            <p class="text-[14px] text-[#4a5565]">Tổng Doanh thu</p>
            <p class="text-[12px] text-[#6a7282]">Gross Revenue</p>
          </div>
          <p class="text-[30px] leading-[36px] text-green-600">
            {{ formatCurrency(reportData.summary.total_revenue) }}
          </p>
        </div>

        <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-[24px] flex flex-col gap-[16px]">
          <div class="flex flex-col gap-[4px]">
            <p class="text-[14px] text-[#4a5565]">Lợi nhuận gộp</p>
            <p class="text-[12px] text-[#6a7282]">Gross Profit</p>
          </div>
          <p class="text-[30px] leading-[36px] text-[#009689]">
            {{ formatCurrency(reportData.summary.total_profit) }}
          </p>
        </div>

        <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-[24px] flex flex-col gap-[16px]">
          <div class="flex flex-col gap-[4px]">
            <p class="text-[14px] text-[#4a5565]">Số lượng đơn hàng</p>
            <p class="text-[12px] text-[#6a7282]">Transactions</p>
          </div>
          <p class="text-[30px] leading-[36px] text-blue-600">
            {{ reportData.summary.total_orders.toLocaleString('vi-VN') }}
          </p>
        </div>

        <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-[24px] flex flex-col gap-[16px]">
          <div class="flex flex-col gap-[4px]">
            <p class="text-[14px] text-[#4a5565]">Giá trị TB đơn</p>
            <p class="text-[12px] text-[#6a7282]">AOV (Average Order Value)</p>
          </div>
          <p class="text-[30px] leading-[36px] text-[#e17100]">
            {{ formatCurrency(reportData.summary.aov) }}
          </p>
        </div>
      </div>

      <!-- Chart and Donut Section -->
      <div class="flex gap-6 h-[528px]">
        <!-- Bar Chart -->
        <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] flex-1 flex flex-col gap-[24px] p-[24px]">
          <div class="flex flex-col gap-[6px]">
            <h2 class="text-[16px] leading-[16px] text-neutral-950">Xu hướng Doanh thu</h2>
            <p class="text-[14px] text-[#4a5565]">Biểu đồ theo dõi doanh thu và lợi nhuận theo thời gian</p>
          </div>

          <div class="flex-1 flex flex-col gap-[16px]">
            <div class="h-[350px] w-full">
              <apexchart
                type="bar"
                height="350"
                :options="barChartOptions"
                :series="barChartSeries"
              />
            </div>
            <div class="bg-blue-50 border !border-[#bedbff] rounded-[10px] p-[13px]">
              <p class="text-[14px] text-[#193cb8]">
                Nhấp vào cột để xem chi tiết hóa đơn của ngày đó
              </p>
            </div>
          </div>
        </div>

        <!-- Donut Chart -->
        <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] w-[357px] flex flex-col gap-[24px]">
          <div class="p-[24px] pb-0">
            <h2 class="text-[16px] leading-[16px] text-neutral-950 mb-[16px]">Cơ cấu nguồn thu</h2>
            <div class="flex items-center gap-[8px]">
              <button
                @click="revenueStructureType = 'department'"
                :class="[
                  'px-[12px] h-[32px] rounded-[8px] font-medium text-[14px] transition-colors',
                  revenueStructureType === 'department'
                    ? 'bg-teal-600 text-white'
                    : 'bg-white border !border-gray-300 text-neutral-950',
                ]"
              >
                Theo Khoa
              </button>
              <button
                @click="revenueStructureType = 'payment'"
                :class="[
                  'px-[13px] h-[32px] rounded-[8px] font-medium text-[14px] transition-colors',
                  revenueStructureType === 'payment'
                    ? 'bg-teal-600 text-white'
                    : 'bg-white border !border-gray-300 text-neutral-950',
                ]"
              >
                Theo PT Thanh toán
              </button>
            </div>
          </div>

          <div class="px-[24px] flex flex-col gap-[16px]">
            <div class="h-[250px] w-full flex items-center justify-center">
              <apexchart
                v-if="currentDonutSeries.length > 0"
                type="donut"
                height="250"
                :options="donutChartOptions"
                :series="currentDonutSeries"
              />
              <p v-else class="text-gray-400 text-sm">Không có dữ liệu</p>
            </div>

            <div class="flex flex-col gap-[8px] overflow-y-auto max-h-[120px]">
              <div
                v-for="(item, idx) in currentLegendData"
                :key="idx"
                class="flex items-center justify-between"
              >
                <div class="flex items-center gap-[8px]">
                  <div class="w-[12px] h-[12px] rounded-full" :style="{ backgroundColor: item.color }" />
                  <span class="text-[14px] text-[#364153]">{{ item.label }}</span>
                </div>
                <div class="flex flex-col items-end">
                  <p class="text-[14px] text-[#101828]">{{ item.percentage }}%</p>
                  <p class="text-[12px] text-[#6a7282]">{{ formatCurrencyShort(item.value) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Table Card -->
      <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] flex flex-col gap-[24px]">
        <div class="p-[24px] pb-0">
          <h2 class="text-[16px] leading-[16px] text-neutral-950">Chi tiết doanh thu theo ngày</h2>
          <p class="text-[14px] text-[#4a5565] mt-[6px]">Bảng dữ liệu chi tiết để đối chiếu và phân tích</p>
        </div>

        <div class="px-[24px] flex flex-col gap-[16px]">
          <!-- Empty state -->
          <div v-if="reportData.table.length === 0" class="flex flex-col items-center py-12 gap-3">
            <div class="text-gray-400 text-5xl">📊</div>
            <p class="text-gray-500 text-sm">Không có dữ liệu trong khoảng thời gian này</p>
          </div>

          <div v-else class="overflow-y-auto max-h-[420px]">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-300 sticky top-0 bg-white">
                  <th class="text-left px-[8px] py-[10px] text-[14px] font-medium text-neutral-950">Thời gian</th>
                  <th class="text-right px-[8px] py-[10px] text-[14px] font-medium text-neutral-950">Số đơn hàng</th>
                  <th class="text-right px-[8px] py-[10px] text-[14px] font-medium text-neutral-950">Doanh thu</th>
                  <th class="text-right px-[8px] py-[10px] text-[14px] font-medium text-neutral-950">Giá vốn (COGS)</th>
                  <th class="text-right px-[8px] py-[10px] text-[14px] font-medium text-neutral-950">Lợi nhuận gộp</th>
                  <th class="text-right px-[8px] py-[10px] text-[14px] font-medium text-neutral-950">Thu tiền mặt</th>
                  <th class="text-right px-[8px] py-[10px] text-[14px] font-medium text-neutral-950">Thu Online</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="row in reportData.table"
                  :key="row.date"
                  class="border-b border-gray-200 hover:bg-gray-50 transition-colors"
                >
                  <td class="px-[8px] py-[10px] text-[14px] text-[#101828]">{{ row.date }}</td>
                  <td class="px-[8px] py-[10px] text-right text-[14px] text-[#101828]">{{ row.orders }} đơn</td>
                  <td class="px-[8px] py-[10px] text-right text-[14px] text-[#101828]">{{ formatVND(row.revenue) }}</td>
                  <td class="px-[8px] py-[10px] text-right text-[14px] text-[#4a5565]">{{ formatVND(row.cogs) }}</td>
                  <td class="px-[8px] py-[10px] text-right text-[14px] text-green-600">{{ formatVND(row.profit) }}</td>
                  <td class="px-[8px] py-[10px] text-right text-[14px] text-[#e17100]">{{ formatVND(row.cash) }}</td>
                  <td class="px-[8px] py-[10px] text-right text-[14px] text-blue-600">{{ formatVND(row.online) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Summary footer -->
          <div class="bg-gray-50 rounded-[10px] p-[16px]">
            <div class="grid grid-cols-4 gap-[16px]">
              <div class="flex flex-col gap-[4px]">
                <p class="text-[12px] text-[#4a5565]">Tổng đơn hàng</p>
                <p class="text-[18px] text-[#101828]">
                  {{ reportData.summary.total_orders.toLocaleString('vi-VN') }} đơn
                </p>
              </div>
              <div class="flex flex-col gap-[4px]">
                <p class="text-[12px] text-[#4a5565]">Tổng doanh thu</p>
                <p class="text-[18px] text-green-600">{{ formatCurrencyShort(reportData.summary.total_revenue) }}</p>
              </div>
              <div class="flex flex-col gap-[4px]">
                <p class="text-[12px] text-[#4a5565]">Tổng lợi nhuận</p>
                <p class="text-[18px] text-[#009689]">{{ formatCurrencyShort(reportData.summary.total_profit) }}</p>
              </div>
              <div class="flex flex-col gap-[4px]">
                <p class="text-[12px] text-[#4a5565]">AOV trung bình</p>
                <p class="text-[18px] text-[#e17100]">{{ formatCurrencyShort(reportData.summary.aov) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import axios from 'axios'
import { getToken } from '@/utils/auth'

// Icons
import ChevronDownIcon from '@/assets/svg/chevron-down.svg'
import DownloadIcon    from '@/assets/svg/download.svg'

// ─── State ───────────────────────────────────────────────────────────────────
const isLoading        = ref(false)
const errorMessage     = ref('')
const reportData       = ref(null)
const services         = ref([])

const selectedPeriod      = ref('this_month')
const selectedService     = ref('all')
const customStart         = ref('')
const customEnd           = ref('')
const revenueStructureType = ref('department')

const showPeriodDropdown  = ref(false)
const showServiceDropdown = ref(false)
const periodDropdownRef   = ref(null)
const serviceDropdownRef  = ref(null)

const periodOptions = [
  { value: 'today',      label: 'Hôm nay'   },
  { value: '7days',      label: '7 ngày qua' },
  { value: '30days',     label: '30 ngày qua' },
  { value: 'this_month', label: 'Tháng này'  },
  { value: 'this_year',  label: 'Năm nay'    },
  { value: 'custom',     label: 'Tùy chỉnh'  },
]

// ─── Computed ─────────────────────────────────────────────────────────────────
const selectedPeriodLabel = computed(
  () => periodOptions.find(o => o.value === selectedPeriod.value)?.label ?? 'Tháng này'
)

const selectedServiceLabel = computed(() => {
  if (selectedService.value === 'all') return 'Tất cả dịch vụ'
  return services.value.find(s => s.id === selectedService.value)?.ten ?? 'Tất cả dịch vụ'
})

// Bar chart
const barChartSeries = computed(() => {
  if (!reportData.value) return []
  return [
    { name: 'Doanh thu', data: reportData.value.chart.revenue },
    { name: 'Lợi nhuận', data: reportData.value.chart.profit  },
  ]
})

const barChartOptions = computed(() => ({
  chart: {
    type: 'bar',
    height: 350,
    toolbar: { show: false },
    events: {
      dataPointSelection: (_event, _ctx, config) => {
        const date = reportData.value?.chart.categories[config.dataPointIndex]
        if (date) console.log('Clicked date:', date)
      },
    },
  },
  plotOptions: {
    bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 },
  },
  dataLabels: { enabled: false },
  stroke: { show: true, width: 2, colors: ['transparent'] },
  xaxis: {
    categories: reportData.value?.chart.categories ?? [],
    labels: { style: { fontSize: '12px', colors: '#6a7282' } },
  },
  yaxis: {
    title: { text: 'Triệu đồng (M)', style: { fontSize: '12px', color: '#6a7282' } },
    labels: {
      style: { fontSize: '12px', colors: '#6a7282' },
      formatter: v => v.toFixed(1) + 'M',
    },
  },
  fill: { opacity: 1 },
  tooltip: { y: { formatter: v => v.toFixed(1) + ' triệu đồng' } },
  colors: ['#16a34a', '#009689'],
  legend: {
    position: 'top',
    horizontalAlign: 'right',
    fontSize: '14px',
    markers: { width: 12, height: 12, radius: 3 },
  },
  grid: { borderColor: '#f1f1f1', strokeDashArray: 4 },
}))

// Donut
const currentDonutSeries = computed(() => {
  if (!reportData.value) return []
  const source = revenueStructureType.value === 'department'
    ? reportData.value.donut.by_service
    : reportData.value.donut.by_payment
  return source.map(item => item.percentage)
})

const currentLegendData = computed(() => {
  if (!reportData.value) return []
  return revenueStructureType.value === 'department'
    ? reportData.value.donut.by_service
    : reportData.value.donut.by_payment
})

const donutChartOptions = computed(() => {
  const source = currentLegendData.value
  return {
    chart: { type: 'donut', height: 250 },
    labels:  source.map(i => i.label),
    colors:  source.map(i => i.color),
    plotOptions: {
      pie: {
        donut: {
          size: '70%',
          labels: {
            show: true,
            total: {
              show: true,
              label: 'Tổng',
              fontSize: '14px',
              color: '#6a7282',
              formatter: () => formatCurrencyShort(reportData.value?.summary.total_revenue ?? 0),
            },
          },
        },
      },
    },
    dataLabels: { enabled: false },
    legend: { show: false },
    stroke: { width: 2, colors: ['#fff'] },
    tooltip: { y: { formatter: v => v + '%' } },
  }
})

// ─── API ──────────────────────────────────────────────────────────────────────
const fetchReport = async () => {
  isLoading.value   = true
  errorMessage.value = ''

  try {
    const token  = getToken('admin') || getToken('staff')
    const params = { period: selectedPeriod.value, service: selectedService.value }

    if (selectedPeriod.value === 'custom') {
      params.start = customStart.value
      params.end   = customEnd.value
    }

    const { data } = await axios.get('/api/statistics/revenue', {
      params,
      headers: { Authorization: `Bearer ${token}` },
    })

    if (data.status && data.data) {
      reportData.value = data.data
      services.value   = data.data.services ?? []
    } else {
      errorMessage.value = 'Không thể tải dữ liệu báo cáo.'
    }
  } catch (err) {
    console.error('Revenue report error:', err)
    errorMessage.value = err.response?.data?.message ?? 'Lỗi kết nối máy chủ.'
  } finally {
    isLoading.value = false
  }
}

// ─── Filter handlers ──────────────────────────────────────────────────────────
const selectPeriod = (opt) => {
  selectedPeriod.value    = opt.value
  showPeriodDropdown.value = false
  if (opt.value !== 'custom') fetchReport()
}

const selectService = (svc) => {
  selectedService.value    = svc.id
  showServiceDropdown.value = false
  fetchReport()
}

// Close dropdowns on outside click
const handleOutsideClick = (e) => {
  if (periodDropdownRef.value && !periodDropdownRef.value.contains(e.target)) {
    showPeriodDropdown.value = false
  }
  if (serviceDropdownRef.value && !serviceDropdownRef.value.contains(e.target)) {
    showServiceDropdown.value = false
  }
}

// ─── Helpers ──────────────────────────────────────────────────────────────────
const formatVND = (val) =>
  new Intl.NumberFormat('vi-VN').format(Math.round(val)) + ' ₫'

const formatCurrency = (val) => {
  if (val >= 1_000_000_000) return (val / 1_000_000_000).toFixed(1) + ' tỷ'
  if (val >= 1_000_000)     return (val / 1_000_000).toFixed(1) + 'M'
  if (val >= 1_000)         return (val / 1_000).toFixed(1) + 'K'
  return new Intl.NumberFormat('vi-VN').format(val) + ' ₫'
}

const formatCurrencyShort = formatCurrency

const formatDate = (dateStr) => {
  const d = new Date(dateStr)
  return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

const exportExcel = async () => {
  try {
    const token  = getToken('admin') || getToken('staff')
    const params = {
      period:  selectedPeriod.value,
      service: selectedService.value,
      export:  'excel',
    }
    if (selectedPeriod.value === 'custom') {
      params.start = customStart.value
      params.end   = customEnd.value
    }
    const response = await axios.get('/api/statistics/revenue', {
      params,
      headers:      { Authorization: `Bearer ${token}` },
      responseType: 'blob',
    })
    const url  = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href  = url
    const period = selectedPeriod.value === 'custom'
      ? `${customStart.value}-den-${customEnd.value}`
      : selectedPeriod.value
    link.download = `bao-cao-doanh-thu-${period}.xlsx`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  } catch (err) {
    console.error('Export error:', err)
    alert('Xuất Excel thất bại. Vui lòng thử lại.')
  }
}

// ─── Lifecycle ────────────────────────────────────────────────────────────────
onMounted(() => {
  fetchReport()
  document.addEventListener('click', handleOutsideClick)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleOutsideClick)
})
</script>

<style scoped>
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 3px; }
::-webkit-scrollbar-thumb { background: #888; border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: #555; }
</style>