<template>
  <div class="w-full min-h-screen px-8 py-6 flex flex-col gap-6">
    <!-- Header -->
    <div class="flex flex-col gap-2">
      <h1 class="text-2xl font-semibold text-black">Báo cáo Hiệu suất</h1>
      <p class="text-gray-500 font-medium text-base">Đánh giá hiệu quả làm việc của nhân sự và dịch vụ</p>
    </div>

    <!-- Filter Card -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div class="flex items-center justify-between w-full gap-4">

        <!-- Period -->
        <div class="relative flex-1" ref="periodRef">
          <button @click="showPeriod = !showPeriod"
            class="w-full flex items-center justify-between px-4 py-2 bg-gray-100 rounded-[8px]">
            <span class="text-[14px] text-neutral-950">{{ selectedPeriodLabel }}</span>
            <ChevronDownIcon class="w-4 h-4" />
          </button>
          <div v-if="showPeriod"
            class="absolute top-full left-0 mt-1 w-full bg-white border border-gray-200 rounded-[8px] shadow-lg z-20">
            <button v-for="opt in periodOptions" :key="opt.value" @click="selectPeriod(opt)"
              class="w-full text-left px-4 py-2 text-[14px] hover:bg-gray-50 first:rounded-t-[8px] last:rounded-b-[8px]"
              :class="selectedPeriod === opt.value ? 'text-teal-600 font-semibold' : 'text-neutral-950'">
              {{ opt.label }}
            </button>
          </div>
        </div>

        <!-- Custom date -->
        <template v-if="selectedPeriod === 'custom'">
          <input type="date" v-model="customStart" @change="fetchData"
            class="flex-1 px-4 py-2 bg-gray-100 rounded-[8px] text-[14px] border border-gray-200" />
          <span class="text-gray-400">–</span>
          <input type="date" v-model="customEnd" @change="fetchData"
            class="flex-1 px-4 py-2 bg-gray-100 rounded-[8px] text-[14px] border border-gray-200" />
        </template>

        <!-- Vai trò -->
        <div class="relative flex-1" ref="roleRef">
          <button @click="showRole = !showRole"
            class="w-full flex items-center justify-between px-4 py-2 bg-gray-100 rounded-[8px]">
            <span class="text-[14px] text-neutral-950">{{ selectedRoleLabel }}</span>
            <ChevronDownIcon class="w-4 h-4" />
          </button>
          <div v-if="showRole"
            class="absolute top-full left-0 mt-1 w-full bg-white border border-gray-200 rounded-[8px] shadow-lg z-20">
            <button v-for="r in roles" :key="r.value" @click="selectRole(r)"
              class="w-full text-left px-4 py-2 text-[14px] hover:bg-gray-50 first:rounded-t-[8px] last:rounded-b-[8px]"
              :class="selectedRole === r.value ? 'text-teal-600 font-semibold' : 'text-neutral-950'">
              {{ r.label }}
            </button>
          </div>
        </div>

        <!-- Export -->
        <button @click="exportExcel"
          class="flex items-center justify-center gap-2 px-4 py-2 bg-[#5a9690] rounded-[8px] min-w-[192px]">
          <DownloadIcon class="w-4 h-4 text-white" />
          <span class="font-medium text-[14px] text-white">Xuất bảng tính lương</span>
        </button>
      </div>

      <p v-if="reportData" class="text-[12px] text-gray-400 mt-3">
        Dữ liệu từ {{ formatDate(reportData.period.start) }} đến {{ formatDate(reportData.period.end) }}
      </p>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="grid grid-cols-4 gap-6">
      <div v-for="i in 4" :key="i" class="bg-gray-100 rounded-[14px] h-[100px] animate-pulse" />
    </div>

    <!-- Error -->
    <div v-else-if="errorMsg" class="bg-red-50 border border-red-200 rounded-[14px] p-6 text-red-700 text-sm">
      ⚠️ {{ errorMsg }}
    </div>

    <template v-else-if="reportData">
      <!-- KPI Cards -->
      <div class="grid grid-cols-4 gap-6">
        <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 flex flex-col gap-4">
          <div><p class="text-[14px] text-[#4a5565]">Tổng Doanh thu</p><p class="text-[12px] text-[#6a7282]">Gross Revenue</p></div>
          <p class="text-[28px] text-green-600">{{ formatCurrency(reportData.summary.total_revenue) }}</p>
        </div>
        <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 flex flex-col gap-4">
          <div><p class="text-[14px] text-[#4a5565]">Tổng ca làm việc</p><p class="text-[12px] text-[#6a7282]">Total Shifts</p></div>
          <p class="text-[28px] text-blue-600">{{ reportData.summary.total_ca.toLocaleString('vi-VN') }}</p>
        </div>
        <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 flex flex-col gap-4">
          <div><p class="text-[14px] text-[#4a5565]">Lịch hẹn hoàn thành</p><p class="text-[12px] text-[#6a7282]">Completion Rate</p></div>
          <p class="text-[28px] text-[#009689]">{{ reportData.summary.ty_le_ht }}%</p>
        </div>
        <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 flex flex-col gap-4">
          <div><p class="text-[14px] text-[#4a5565]">Số nhân viên</p><p class="text-[12px] text-[#6a7282]">Active Staff</p></div>
          <p class="text-[28px] text-[#e17100]">{{ reportData.summary.total_staff }}</p>
        </div>
      </div>

      <!-- Top 3 + Bar Chart -->
      <div class="flex gap-6">
        <!-- Top 3 Bảng Vàng -->
        <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] flex-1 p-6">
          <h2 class="text-[16px] text-neutral-950 mb-1">Bảng Vàng - Top 3 Xuất sắc nhất</h2>
          <p class="text-[14px] text-[#4a5565] mb-6">Xếp hạng dựa trên tổng doanh thu mang về trong kỳ</p>

          <div v-if="reportData.top3.length === 0" class="flex flex-col items-center py-8 text-gray-400">
            <p>Không có dữ liệu</p>
          </div>

          <div v-else class="flex items-end justify-center gap-8 h-[240px]">
            <!-- #2 -->
            <div v-if="reportData.top3[1]" class="flex flex-col items-center gap-2 w-[180px] h-[220px]">
              <div class="text-[32px]">🥈</div>
              <div class="flex-1 bg-gray-100 rounded-[10px] w-full relative pt-12 flex flex-col items-center pb-4">
                <div class="absolute -top-5 left-1/2 -translate-x-1/2 w-[56px] h-[56px] border-4 border-gray-300 rounded-full overflow-hidden bg-gray-200">
                  <img v-if="reportData.top3[1].anh_dai_dien" :src="avatarUrl(reportData.top3[1].anh_dai_dien)"
                    class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center text-gray-500 text-lg font-bold">
                    {{ reportData.top3[1].full_name?.charAt(0) }}
                  </div>
                </div>
                <p class="text-[14px] text-[#101828] text-center font-medium">{{ reportData.top3[1].full_name }}</p>
                <p class="text-[12px] text-[#4a5565]">{{ reportData.top3[1].chuc_danh || reportData.top3[1].vai_tro_label }}</p>
                <p class="text-[16px] text-[#009689] mt-1">{{ formatCurrency(reportData.top3[1].doanh_thu) }}</p>
              </div>
              <div class="bg-gray-100 border border-gray-300 rounded-[8px] px-3 py-0.5 text-[12px] text-[#364153] font-medium">#2</div>
            </div>

            <!-- #1 -->
            <div v-if="reportData.top3[0]" class="flex flex-col items-center gap-2 w-[200px] h-[260px]">
              <div class="text-[40px]">🥇</div>
              <div class="flex-1 border-2 border-[#ffb900] rounded-[10px] w-full relative pt-14 flex flex-col items-center pb-4">
                <div class="absolute -top-5 left-1/2 -translate-x-1/2 w-[64px] h-[64px] border-4 border-[#ffb900] rounded-full overflow-hidden bg-gray-200">
                  <img v-if="reportData.top3[0].anh_dai_dien" :src="avatarUrl(reportData.top3[0].anh_dai_dien)"
                    class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center text-gray-500 text-xl font-bold">
                    {{ reportData.top3[0].full_name?.charAt(0) }}
                  </div>
                </div>
                <p class="text-[15px] text-[#101828] text-center font-semibold">{{ reportData.top3[0].full_name }}</p>
                <p class="text-[12px] text-[#4a5565]">{{ reportData.top3[0].chuc_danh || reportData.top3[0].vai_tro_label }}</p>
                <p class="text-[20px] text-[#e17100] mt-1">{{ formatCurrency(reportData.top3[0].doanh_thu) }}</p>
              </div>
              <div class="bg-[#fe9a00] rounded-[8px] px-3 py-0.5 text-[12px] text-white font-medium">👑 Quán quân</div>
            </div>

            <!-- #3 -->
            <div v-if="reportData.top3[2]" class="flex flex-col items-center gap-2 w-[180px] h-[220px]">
              <div class="text-[32px]">🥉</div>
              <div class="flex-1 bg-gray-100 rounded-[10px] w-full relative pt-12 flex flex-col items-center pb-4">
                <div class="absolute -top-5 left-1/2 -translate-x-1/2 w-[56px] h-[56px] border-4 border-[#ffb86a] rounded-full overflow-hidden bg-gray-200">
                  <img v-if="reportData.top3[2].anh_dai_dien" :src="avatarUrl(reportData.top3[2].anh_dai_dien)"
                    class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center text-gray-500 text-lg font-bold">
                    {{ reportData.top3[2].full_name?.charAt(0) }}
                  </div>
                </div>
                <p class="text-[14px] text-[#101828] text-center font-medium">{{ reportData.top3[2].full_name }}</p>
                <p class="text-[12px] text-[#4a5565]">{{ reportData.top3[2].chuc_danh || reportData.top3[2].vai_tro_label }}</p>
                <p class="text-[16px] text-[#009689] mt-1">{{ formatCurrency(reportData.top3[2].doanh_thu) }}</p>
              </div>
              <div class="bg-gray-100 border border-gray-300 rounded-[8px] px-3 py-0.5 text-[12px] text-[#364153] font-medium">#3</div>
            </div>
          </div>
        </div>

        <!-- Bar chart doanh thu top 10 -->
        <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] w-[480px] p-6">
          <h2 class="text-[16px] text-neutral-950 mb-1">Doanh thu theo Nhân viên</h2>
          <p class="text-[14px] text-[#4a5565] mb-4">Top 10 nhân viên theo doanh thu</p>
          <div class="h-[280px]">
            <apexchart v-if="barChartSeries[0]?.data.length > 0"
              type="bar" height="280"
              :options="barChartOptions"
              :series="barChartSeries"
            />
            <div v-else class="flex items-center justify-center h-full text-gray-400 text-sm">Không có dữ liệu</div>
          </div>
        </div>
      </div>

      <!-- Bảng nhân viên -->
      <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] flex flex-col gap-6">
        <div class="p-6 pb-0">
          <h2 class="text-[16px] text-neutral-950">Bảng hiệu suất nhân viên</h2>
          <p class="text-[14px] text-[#4a5565] mt-1">Chi tiết hiệu suất và doanh thu từng nhân viên</p>
        </div>

        <div class="px-6 pb-6">
          <div v-if="reportData.staff.length === 0" class="flex flex-col items-center py-12 text-gray-400">
            <p>Không có dữ liệu trong kỳ này</p>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full min-w-[1100px]">
              <thead>
                <tr class="border-b border-gray-300 bg-gray-50">
                  <th class="text-left px-2 py-3 text-[13px] font-medium text-neutral-950 w-8">#</th>
                  <th class="text-left px-2 py-3 text-[13px] font-medium text-neutral-950">Nhân viên</th>
                  <th class="text-right px-2 py-3 text-[13px] font-medium text-neutral-950">Doanh thu</th>
                  <th class="text-right px-2 py-3 text-[13px] font-medium text-neutral-950">Số ca</th>
                  <th class="text-right px-2 py-3 text-[13px] font-medium text-neutral-950">Tỉ lệ HT</th>
                  <th class="text-right px-2 py-3 text-[13px] font-medium text-[#009689] border-l-2 border-teal-200">Lương cứng</th>
                  <th class="text-right px-2 py-3 text-[13px] font-medium text-[#009689]">Hoa hồng</th>
                  <th class="text-right px-2 py-3 text-[13px] font-medium text-[#009689]">Thưởng DT</th>
                  <th class="text-right px-2 py-3 text-[13px] font-bold text-white bg-[#0f766e]">TỔNG LƯƠNG</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(staff, idx) in reportData.staff" :key="staff.id"
                  class="border-b border-gray-200 hover:bg-teal-50 transition-colors">
                  <td class="px-2 py-3 text-[13px] text-[#6a7282]">{{ idx + 1 }}</td>
                  <td class="px-2 py-3">
                    <div class="flex items-center gap-2">
                      <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-200 flex-shrink-0 flex items-center justify-center text-sm font-bold text-gray-500">
                        <img v-if="staff.anh_dai_dien" :src="avatarUrl(staff.anh_dai_dien)" class="w-full h-full object-cover" />
                        <span v-else>{{ staff.full_name?.charAt(0) }}</span>
                      </div>
                      <div>
                        <p class="text-[13px] text-[#101828] font-medium">{{ staff.full_name }}</p>
                        <span class="text-[11px] px-1.5 py-0.5 rounded-full" :class="staff.vai_tro === 'bac_si' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700'">
                          {{ staff.vai_tro_label }}
                        </span>
                      </div>
                    </div>
                  </td>
                  <td class="px-2 py-3 text-right text-[13px] text-green-600 font-medium">{{ formatCurrency(staff.doanh_thu) }}</td>
                  <td class="px-2 py-3 text-right text-[13px] text-[#101828]">{{ staff.so_ca }} ca</td>
                  <td class="px-2 py-3 text-right">
                    <span class="text-[13px] font-medium px-2 py-0.5 rounded-full"
                      :class="staff.ty_le_ht >= 80 ? 'bg-green-100 text-green-700' : staff.ty_le_ht >= 50 ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-600'">
                      {{ staff.ty_le_ht }}%
                    </span>
                  </td>
                  <td class="px-2 py-3 text-right text-[13px] text-[#4a5565] border-l-2 border-teal-100">{{ formatVND(staff.luong_co_dinh) }}</td>
                  <td class="px-2 py-3 text-right text-[13px] text-blue-600">{{ formatVND(staff.hoa_hong) }}</td>
                  <td class="px-2 py-3 text-right text-[13px]">
                    <span :class="staff.thuong_dt > 0 ? 'text-[#e17100] font-medium' : 'text-gray-400'">
                      {{ staff.thuong_dt > 0 ? '+' + formatVND(staff.thuong_dt) : '—' }}
                    </span>
                  </td>
                  <td class="px-2 py-3 text-right">
                    <span class="text-[14px] font-bold text-[#065F46] bg-teal-50 px-2 py-1 rounded-[6px]">
                      {{ formatVND(staff.tong_luong) }}
                    </span>
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="bg-[#134E4A]">
                  <td colspan="5" class="px-2 py-3 text-[13px] font-bold text-white">TỔNG CỘNG ({{ reportData.staff.length }} nhân viên)</td>
                  <td class="px-2 py-3 text-right text-[13px] font-bold text-white border-l-2 border-teal-700">
                    {{ formatVND(reportData.staff.reduce((s,r) => s + r.luong_co_dinh, 0)) }}
                  </td>
                  <td class="px-2 py-3 text-right text-[13px] font-bold text-white">
                    {{ formatVND(reportData.staff.reduce((s,r) => s + r.hoa_hong, 0)) }}
                  </td>
                  <td class="px-2 py-3 text-right text-[13px] font-bold text-white">
                    {{ formatVND(reportData.staff.reduce((s,r) => s + r.thuong_dt, 0)) }}
                  </td>
                  <td class="px-2 py-3 text-right">
                    <span class="text-[15px] font-bold text-[#6EE7B7]">
                      {{ formatVND(reportData.staff.reduce((s,r) => s + r.tong_luong, 0)) }}
                    </span>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>

          <!-- Công thức lương -->
          <div class="bg-teal-50 border border-teal-200 rounded-[10px] p-4 mt-4">
            <p class="text-[14px] text-[#0f766e] font-semibold mb-3">📐 Công thức tính lương</p>
            <div class="grid grid-cols-3 gap-4 text-[12px] text-[#134e4a]">
              <div class="bg-white rounded-[8px] p-3 border border-teal-100">
                <p class="font-semibold mb-2">💼 Lương cứng (tính theo kỳ)</p>
                <p>Bác sĩ: <strong class="text-blue-700">15,000,000đ</strong>/tháng</p>
                <p>Y tá: <strong class="text-purple-700">8,000,000đ</strong>/tháng</p>
                <p class="mt-1 text-gray-500 italic">× (số ngày kỳ / ngày trong tháng)</p>
              </div>
              <div class="bg-white rounded-[8px] p-3 border border-teal-100">
                <p class="font-semibold mb-2">💰 Hoa hồng doanh thu</p>
                <p>Bác sĩ: <strong class="text-blue-700">3%</strong> × Doanh thu</p>
                <p>Y tá: <strong class="text-purple-700">1.5%</strong> × Doanh thu</p>
              </div>
              <div class="bg-white rounded-[8px] p-3 border border-teal-100">
                <p class="font-semibold mb-2">🏆 Thưởng doanh thu</p>
                <p>DT ≥ 100M: <strong class="text-green-700">+5,000,000đ</strong></p>
                <p>DT ≥ 50M: <strong class="text-blue-700">+2,000,000đ</strong></p>
                <p>DT ≥ 20M: <strong class="text-orange-700">+500,000đ</strong></p>
              </div>
            </div>
            <p class="mt-3 text-[12px] text-[#0f766e] font-medium">
              TỔNG = Lương cứng theo kỳ + Hoa hồng + Thưởng doanh thu
            </p>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
import { getToken } from '@/utils/auth'
import { resolveImageUrl } from '@/utils/image'

import ChevronDownIcon from '@/assets/svg/chevron-down.svg'
import DownloadIcon    from '@/assets/svg/download.svg'

// ─── State ───────────────────────────────────────────────────────────────────
const isLoading  = ref(false)
const errorMsg   = ref('')
const reportData = ref(null)

const selectedPeriod = ref('this_month')
const selectedRole   = ref('all')
const customStart    = ref('')
const customEnd      = ref('')

const showPeriod = ref(false)
const showRole   = ref(false)
const periodRef  = ref(null)
const roleRef    = ref(null)

const roles = [
  { value: 'all',    label: 'Tất cả' },
  { value: 'bac_si', label: 'Bác sĩ' },
  { value: 'y_ta',   label: 'Y tá'   },
]

const periodOptions = [
  { value: 'today',      label: 'Hôm nay'    },
  { value: '7days',      label: '7 ngày qua'  },
  { value: '30days',     label: '30 ngày qua' },
  { value: 'this_month', label: 'Tháng này'   },
  { value: 'this_year',  label: 'Năm nay'     },
  { value: 'custom',     label: 'Tùy chỉnh'   },
]

// ─── Computed ─────────────────────────────────────────────────────────────────
const selectedPeriodLabel = computed(() =>
  periodOptions.find(o => o.value === selectedPeriod.value)?.label ?? 'Tháng này'
)
const selectedRoleLabel = computed(() =>
  roles.find(r => r.value === selectedRole.value)?.label ?? 'Tất cả'
)

const barChartSeries = computed(() => {
  if (!reportData.value) return [{ name: 'Doanh thu (M)', data: [] }]
  return [{
    name: 'Doanh thu (M)',
    data: reportData.value.bar_chart.map(s => s.doanh_thu),
  }]
})

const barChartOptions = computed(() => ({
  chart: { type: 'bar', height: 280, toolbar: { show: false } },
  plotOptions: { bar: { horizontal: true, borderRadius: 4, dataLabels: { position: 'top' } } },
  dataLabels: { enabled: true, formatter: v => v.toFixed(1) + 'M', style: { fontSize: '11px', colors: ['#6a7282'] }, offsetX: 30 },
  xaxis: {
    categories: reportData.value?.bar_chart.map(s => s.name) ?? [],
    labels: { style: { fontSize: '11px', colors: '#6a7282' }, formatter: v => v + 'M' },
  },
  yaxis: { labels: { style: { fontSize: '11px', colors: '#6a7282' } } },
  colors: ['#009689'],
  grid: { borderColor: '#f1f1f1' },
  tooltip: { y: { formatter: v => v.toFixed(2) + ' triệu đồng' } },
}))

// ─── API ──────────────────────────────────────────────────────────────────────
const fetchData = async () => {
  isLoading.value = true
  errorMsg.value  = ''
  try {
    const token  = getToken('admin') || getToken('staff')
    const params = { period: selectedPeriod.value, vai_tro: selectedRole.value }
    if (selectedPeriod.value === 'custom') {
      params.start = customStart.value
      params.end   = customEnd.value
    }
    const { data } = await axios.get('/api/statistics/performance', {
      params,
      headers: { Authorization: `Bearer ${token}` },
    })
    if (data.status && data.data) {
      reportData.value = data.data
    } else {
      errorMsg.value = 'Không thể tải dữ liệu báo cáo.'
    }
  } catch (err) {
    errorMsg.value = err.response?.data?.message ?? 'Lỗi kết nối máy chủ.'
  } finally {
    isLoading.value = false
  }
}

// ─── Handlers ─────────────────────────────────────────────────────────────────
const selectPeriod = (opt) => {
  selectedPeriod.value = opt.value
  showPeriod.value = false
  if (opt.value !== 'custom') fetchData()
}
const selectRole = (r) => {
  selectedRole.value = r.value
  showRole.value = false
  fetchData()
}

const handleOutsideClick = (e) => {
  if (periodRef.value && !periodRef.value.contains(e.target)) showPeriod.value = false
  if (roleRef.value   && !roleRef.value.contains(e.target))   showRole.value   = false
}

// ─── Helpers ──────────────────────────────────────────────────────────────────
const formatVND = (val) => {
  if (!val && val !== 0) return '—'
  return new Intl.NumberFormat('vi-VN').format(Math.round(val)) + ' đ'
}

const formatCurrency = (val) => {
  if (!val) return '0 đ'
  if (val >= 1_000_000_000) return (val / 1_000_000_000).toFixed(1) + ' tỷ'
  if (val >= 1_000_000)     return (val / 1_000_000).toFixed(1) + 'M'
  if (val >= 1_000)         return (val / 1_000).toFixed(0) + 'K'
  return new Intl.NumberFormat('vi-VN').format(Math.round(val)) + ' đ'
}

const formatDate = (d) => new Date(d).toLocaleDateString('vi-VN')

const avatarUrl = (path) => resolveImageUrl(path, '')

const exportExcel = async () => {
  try {
    const token  = getToken('admin') || getToken('staff')
    const params = { period: selectedPeriod.value, vai_tro: selectedRole.value, export: 'excel' }
    if (selectedPeriod.value === 'custom') {
      params.start = customStart.value
      params.end   = customEnd.value
    }
    const response = await axios.get('/api/statistics/performance', {
      params,
      headers: { Authorization: `Bearer ${token}` },
      responseType: 'blob',
    })
    const url      = window.URL.createObjectURL(new Blob([response.data]))
    const link     = document.createElement('a')
    link.href      = url
    link.download  = `hieu-suat-nhan-vien-${selectedPeriod.value}.xlsx`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  } catch {
    alert('Tính năng xuất Excel chưa được hỗ trợ.')
  }
}

// ─── Lifecycle ────────────────────────────────────────────────────────────────
onMounted(() => {
  fetchData()
  document.addEventListener('click', handleOutsideClick)
})
onBeforeUnmount(() => document.removeEventListener('click', handleOutsideClick))
</script>

<style scoped>
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 3px; }
::-webkit-scrollbar-thumb { background: #888; border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: #555; }
</style>