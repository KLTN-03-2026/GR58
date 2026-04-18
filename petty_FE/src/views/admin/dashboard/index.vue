<template>
  <div class="dashboard-page">

    <!-- ── HEADER + BỘ LỌC THỜI GIAN ── -->
    <div class="dashboard-header">
      <div>
        <h1 class="page-title">Dashboard Tổng Quan</h1>
        <p class="page-sub">Dữ liệu từ {{ meta.from }} đến {{ meta.to }}</p>
      </div>

      <div class="filter-bar">
        <button
          v-for="opt in presets"
          :key="opt.key"
          class="preset-btn"
          :class="{ active: activePreset === opt.key }"
          @click="applyPreset(opt)"
        >
          {{ opt.label }}
        </button>

        <div class="date-range">
          <input type="date" v-model="customFrom" @change="applyCustomRange" />
          <span>–</span>
          <input type="date" v-model="customTo" @change="applyCustomRange" />
        </div>
      </div>
    </div>

    <!-- ── LOADING ── -->
    <div v-if="loading" class="loading-overlay">
      <div class="spinner"></div>
      <span>Đang tải dữ liệu...</span>
    </div>

    <template v-else-if="data">
      <!-- ── KPI CARDS ── -->
      <div class="kpi-grid">
        <div class="kpi-card">
          <p class="kpi-label">Doanh thu</p>
          <p class="kpi-value">{{ formatCurrency(data.kpi.doanh_thu.value) }}</p>
          <p class="kpi-change" :class="changeClass(data.kpi.doanh_thu.change)">
            <span>{{ changeIcon(data.kpi.doanh_thu.change) }}</span>
            {{ Math.abs(data.kpi.doanh_thu.change) }}% so với kỳ trước
          </p>
        </div>

        <div class="kpi-card">
          <p class="kpi-label">Lịch hẹn</p>
          <p class="kpi-value">{{ data.kpi.lich_hen.value.toLocaleString('vi-VN') }}</p>
          <p class="kpi-change" :class="changeClass(data.kpi.lich_hen.change)">
            <span>{{ changeIcon(data.kpi.lich_hen.change) }}</span>
            {{ Math.abs(data.kpi.lich_hen.change) }}% so với kỳ trước
          </p>
        </div>

        <div class="kpi-card">
          <p class="kpi-label">Khách hàng mới</p>
          <p class="kpi-value">{{ data.kpi.khach_hang_moi.value.toLocaleString('vi-VN') }}</p>
          <p class="kpi-change" :class="changeClass(data.kpi.khach_hang_moi.change)">
            <span>{{ changeIcon(data.kpi.khach_hang_moi.change) }}</span>
            {{ Math.abs(data.kpi.khach_hang_moi.change) }}% so với kỳ trước
          </p>
        </div>

        <div class="kpi-card">
          <p class="kpi-label">Chi phí</p>
          <p class="kpi-value">{{ formatCurrency(data.kpi.chi_phi.value) }}</p>
          <p class="kpi-change" :class="changeClass(data.kpi.chi_phi.change, true)">
            <span>{{ changeIcon(data.kpi.chi_phi.change) }}</span>
            {{ Math.abs(data.kpi.chi_phi.change) }}% so với kỳ trước
          </p>
        </div>
      </div>

      <!-- ── DOANH THU + TRẠNG THÁI LỊCH HẸN ── -->
      <div class="row-2-1">
        <div class="card">
          <h3 class="card-title">Biểu đồ doanh thu</h3>
          <apexchart
            type="bar"
            height="240"
            :options="revenueChartOptions"
            :series="revenueChartSeries"
          />
        </div>

        <div class="card">
          <h3 class="card-title">Trạng thái lịch hẹn</h3>
          <apexchart
            type="donut"
            height="240"
            :options="donutOptions"
            :series="donutSeries"
          />
        </div>
      </div>

      <!-- ── TOP DỊCH VỤ + NHÂN VIÊN ── -->
      <div class="row-2-1">
        <div class="card">
          <h3 class="card-title">Top 5 dịch vụ phổ biến</h3>
          <apexchart
            type="bar"
            height="220"
            :options="topServicesOptions"
            :series="topServicesSeries"
          />
        </div>

        <div class="card">
          <h3 class="card-title">Hiệu suất nhân viên</h3>
          <div class="staff-table-wrap">
            <table class="staff-table">
              <thead>
                <tr>
                  <th>Tên</th>
                  <th>Vai trò</th>
                  <th>Lịch hẹn</th>
                  <th>Tỷ lệ HT</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="s in data.staff_performance" :key="s.name">
                  <td>{{ s.name }}</td>
                  <td>
                    <span class="badge" :class="s.vai_tro === 'Bác sĩ' ? 'badge-blue' : 'badge-teal'">
                      {{ s.vai_tro }}
                    </span>
                  </td>
                  <td>{{ s.total }}</td>
                  <td>
                    <span class="badge" :class="rateClass(s.completion_rate)">
                      {{ s.completion_rate }}%
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ── KHÁCH HÀNG + PHÂN HẠNG + KHO ── -->
      <div class="row-3">
        <div class="card">
          <h3 class="card-title">Khách hàng mới (6 tháng)</h3>
          <apexchart
            type="bar"
            height="200"
            :options="newCustomersOptions"
            :series="newCustomersSeries"
          />
        </div>

        <div class="card">
          <h3 class="card-title">Phân hạng khách hàng</h3>
          <div class="rank-list">
            <div v-for="r in data.customer_rank" :key="r.rank" class="rank-row">
              <span class="rank-label">
                <span class="rank-icon" :class="'rank-' + r.rank.toLowerCase()">
                  {{ r.rank === 'Diamond' ? '◆' : r.rank === 'Gold' ? '★' : '●' }}
                </span>
                {{ r.rank }}
              </span>
              <div class="rank-bar-wrap">
                <div
                  class="rank-bar"
                  :class="'rank-bar-' + r.rank.toLowerCase()"
                  :style="{ width: r.percentage + '%' }"
                ></div>
              </div>
              <span class="rank-pct">{{ r.percentage }}%</span>
              <span class="rank-count">({{ r.count }})</span>
            </div>
          </div>
        </div>

        <div class="card">
          <h3 class="card-title">
            Cảnh báo kho hàng
            <span v-if="data.inventory_alerts.length" class="alert-badge">
              {{ data.inventory_alerts.length }}
            </span>
          </h3>
          <div v-if="!data.inventory_alerts.length" class="empty-state">
            Kho hàng đang ổn định
          </div>
          <table v-else class="inv-table">
            <thead>
              <tr>
                <th>Mặt hàng</th>
                <th>Tồn kho</th>
                <th>Tối thiểu</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in data.inventory_alerts" :key="item.ma_hang_hoa">
                <td>
                  <p class="item-name">{{ item.name }}</p>
                  <p class="item-code">{{ item.ma_hang_hoa }}</p>
                </td>
                <td>
                  <span class="badge" :class="item.ton_kho === 0 ? 'badge-red' : 'badge-orange'">
                    {{ item.ton_kho }} {{ item.don_vi_tinh }}
                  </span>
                </td>
                <td class="text-muted">{{ item.dinh_muc_toi_thieu }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>

    <!-- ── LỖI ── -->
    <div v-else-if="error" class="error-state">
      <p>{{ error }}</p>
      <button @click="fetchData">Thử lại</button>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'

// ── Đặt tên component ──
const apexchart = VueApexCharts

// ── State ──
const loading     = ref(false)
const error       = ref(null)
const data        = ref(null)
const meta        = ref({ from: '', to: '' })
const activePreset = ref('30d')

// Date range
const today      = new Date().toISOString().split('T')[0]
const customFrom = ref(
  new Date(Date.now() - 29 * 86400000).toISOString().split('T')[0]
)
const customTo   = ref(today)

// Preset buttons
const presets = [
  { key: 'today',  label: 'Hôm nay',    days: 0  },
  { key: '7d',     label: '7 ngày',     days: 6  },
  { key: '30d',    label: '30 ngày',    days: 29 },
  { key: 'month',  label: 'Tháng này',  days: null },
  { key: 'year',   label: 'Năm nay',    days: null },
]

// ── Helpers ──
function formatCurrency(val) {
  if (val >= 1_000_000_000) return (val / 1_000_000_000).toFixed(1) + ' tỷ'
  if (val >= 1_000_000)     return (val / 1_000_000).toFixed(1) + ' tr'
  return new Intl.NumberFormat('vi-VN').format(Math.round(val)) + ' đ'
}

function changeClass(val, invert = false) {
  if (val === 0) return 'neutral'
  const positive = invert ? val < 0 : val > 0
  return positive ? 'up' : 'down'
}

function changeIcon(val) {
  if (val > 0) return '▲'
  if (val < 0) return '▼'
  return '─'
}

function rateClass(rate) {
  if (rate >= 90) return 'badge-green'
  if (rate >= 70) return 'badge-orange'
  return 'badge-red'
}

// ── Fetch API ──
async function fetchData() {
  loading.value = true
  error.value   = null
  try {
    const token = localStorage.getItem('auth_token_admin')
    const { data: res } = await axios.get('/api/statistics/dashboard', {
      headers: { Authorization: `Bearer ${token}` },
      params:  { from: customFrom.value, to: customTo.value },
    })
    data.value = res.data
    meta.value = res.meta
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Không thể tải dữ liệu. Vui lòng thử lại.'
  } finally {
    loading.value = false
  }
}

// ── Preset xử lý ──
function applyPreset(opt) {
  activePreset.value = opt.key
  const now = new Date()
  customTo.value = today

  if (opt.key === 'today') {
    customFrom.value = today
  } else if (opt.key === 'month') {
    customFrom.value = new Date(now.getFullYear(), now.getMonth(), 1)
      .toISOString().split('T')[0]
  } else if (opt.key === 'year') {
    customFrom.value = `${now.getFullYear()}-01-01`
  } else {
    const d = new Date(Date.now() - opt.days * 86400000)
    customFrom.value = d.toISOString().split('T')[0]
  }
  fetchData()
}

function applyCustomRange() {
  activePreset.value = 'custom'
  fetchData()
}

onMounted(() => fetchData())

// ──────────────────────────────────────────
// CHART OPTIONS
// ──────────────────────────────────────────

// 1. Biểu đồ doanh thu
const revenueChartSeries = computed(() => [{
  name: 'Doanh thu',
  data: data.value?.revenue_chart?.data?.map(d => d.value) ?? [],
}])

const revenueChartOptions = computed(() => ({
  chart: { toolbar: { show: false }, fontFamily: 'inherit' },
  plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
  dataLabels: { enabled: false },
  xaxis: {
    categories: data.value?.revenue_chart?.data?.map(d => {
      if (data.value.revenue_chart.group_by === 'day') {
        const [, m, d2] = d.label.split('-')
        return `${d2}/${m}`
      }
      return d.label
    }) ?? [],
    labels: { rotate: -45, style: { fontSize: '11px' } },
    tickAmount: Math.min(14, (data.value?.revenue_chart?.data?.length ?? 0)),
  },
  yaxis: {
    labels: {
      formatter: v => v >= 1_000_000
        ? (v / 1_000_000).toFixed(0) + 'tr'
        : v.toLocaleString('vi-VN'),
    },
  },
  colors: ['#378ADD'],
  tooltip: {
    y: { formatter: v => new Intl.NumberFormat('vi-VN').format(Math.round(v)) + ' đ' },
  },
  grid: { borderColor: '#f0f0f0' },
}))

// 2. Donut trạng thái lịch hẹn
const donutSeries = computed(() => {
  const s = data.value?.appointment_status
  if (!s) return [0, 0, 0, 0]
  return [s.completed, s.confirmed, s.pending, s.cancelled]
})

const donutOptions = computed(() => ({
  chart: { fontFamily: 'inherit' },
  labels: ['Hoàn thành', 'Đã xác nhận', 'Chờ xử lý', 'Đã huỷ'],
  colors: ['#1D9E75', '#378ADD', '#EF9F27', '#E24B4A'],
  legend: { position: 'bottom', fontSize: '12px' },
  dataLabels: { enabled: true, formatter: v => Math.round(v) + '%' },
  plotOptions: { pie: { donut: { size: '60%' } } },
  tooltip: { y: { formatter: v => v + ' lịch hẹn' } },
}))

// 3. Top dịch vụ (horizontal bar)
const topServicesSeries = computed(() => [{
  name: 'Lịch hẹn',
  data: data.value?.top_services?.map(s => s.total_appointments) ?? [],
}])

const topServicesOptions = computed(() => ({
  chart: { toolbar: { show: false }, fontFamily: 'inherit' },
  plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '55%' } },
  dataLabels: { enabled: false },
  xaxis: { categories: data.value?.top_services?.map(s => s.name) ?? [] },
  colors: ['#5DCAA5'],
  tooltip: {
    y: {
      formatter: (v, { dataPointIndex }) => {
        const s = data.value?.top_services?.[dataPointIndex]
        const rev = s ? new Intl.NumberFormat('vi-VN').format(Math.round(s.revenue)) + ' đ' : ''
        return `${v} lịch · DT: ${rev}`
      },
    },
  },
  grid: { borderColor: '#f0f0f0' },
}))

// 4. Khách hàng mới
const newCustomersSeries = computed(() => [{
  name: 'Khách mới',
  data: data.value?.new_customers_chart?.map(d => d.value) ?? [],
}])

const newCustomersOptions = computed(() => ({
  chart: { toolbar: { show: false }, fontFamily: 'inherit' },
  plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
  dataLabels: { enabled: false },
  xaxis: { categories: data.value?.new_customers_chart?.map(d => d.label) ?? [] },
  colors: ['#639922'],
  grid: { borderColor: '#f0f0f0' },
}))
</script>

<style scoped>
.dashboard-page {
  padding: 24px;
  max-width: 1400px;
  margin: 0 auto;
  font-family: inherit;
}

/* Header */
.dashboard-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
  margin-bottom: 24px;
}
.page-title {
  font-size: 22px;
  font-weight: 500;
  margin: 0 0 4px;
}
.page-sub {
  font-size: 13px;
  color: #888;
  margin: 0;
}

/* Filter */
.filter-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}
.preset-btn {
  padding: 6px 14px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: transparent;
  font-size: 13px;
  cursor: pointer;
  color: #555;
  transition: all .15s;
}
.preset-btn:hover { background: #f5f5f5; }
.preset-btn.active {
  background: #E6F1FB;
  border-color: #378ADD;
  color: #185FA5;
  font-weight: 500;
}
.date-range {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #888;
}
.date-range input {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 5px 10px;
  font-size: 13px;
  outline: none;
}

/* Loading */
.loading-overlay {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 80px 0;
  color: #888;
  font-size: 14px;
}
.spinner {
  width: 24px;
  height: 24px;
  border: 3px solid #eee;
  border-top-color: #378ADD;
  border-radius: 50%;
  animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* KPI Grid */
.kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  margin-bottom: 20px;
}
@media (max-width: 900px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }

.kpi-card {
  background: #f8f8f8;
  border-radius: 10px;
  padding: 16px 18px;
}
.kpi-label {
  font-size: 12px;
  color: #888;
  margin: 0 0 8px;
}
.kpi-value {
  font-size: 24px;
  font-weight: 500;
  margin: 0 0 6px;
  color: #1a1a1a;
}
.kpi-change {
  font-size: 12px;
  margin: 0;
}
.kpi-change.up      { color: #1D9E75; }
.kpi-change.down    { color: #E24B4A; }
.kpi-change.neutral { color: #888; }

/* Layout rows */
.row-2-1 {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 16px;
  margin-bottom: 16px;
}
.row-3 {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 16px;
}
@media (max-width: 1024px) {
  .row-2-1, .row-3 { grid-template-columns: 1fr; }
}

/* Card */
.card {
  background: #fff;
  border: 1px solid #ebebeb;
  border-radius: 12px;
  padding: 18px 20px;
}
.card-title {
  font-size: 14px;
  font-weight: 500;
  margin: 0 0 16px;
  color: #1a1a1a;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Badge */
.badge {
  display: inline-block;
  padding: 2px 10px;
  border-radius: 8px;
  font-size: 11px;
  font-weight: 500;
}
.badge-blue   { background: #E6F1FB; color: #185FA5; }
.badge-teal   { background: #E1F5EE; color: #0F6E56; }
.badge-green  { background: #EAF3DE; color: #3B6D11; }
.badge-orange { background: #FAEEDA; color: #854F0B; }
.badge-red    { background: #FCEBEB; color: #A32D2D; }

/* Alert badge */
.alert-badge {
  background: #FCEBEB;
  color: #A32D2D;
  font-size: 11px;
  padding: 2px 8px;
  border-radius: 10px;
  font-weight: 500;
}

/* Staff table */
.staff-table-wrap { overflow-x: auto; }
.staff-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}
.staff-table th {
  text-align: left;
  padding: 8px 10px;
  border-bottom: 1px solid #f0f0f0;
  color: #888;
  font-weight: 400;
  font-size: 12px;
}
.staff-table td {
  padding: 8px 10px;
  border-bottom: 1px solid #f8f8f8;
  color: #333;
}

/* Rank */
.rank-list { display: flex; flex-direction: column; gap: 12px; margin-top: 8px; }
.rank-row { display: flex; align-items: center; gap: 8px; font-size: 13px; }
.rank-label { width: 80px; display: flex; align-items: center; gap: 6px; color: #444; }
.rank-icon { font-size: 12px; }
.rank-silver .rank-icon { color: #888; }
.rank-gold   .rank-icon { color: #BA7517; }
.rank-diamond .rank-icon { color: #378ADD; }
.rank-bar-wrap { flex: 1; height: 8px; background: #f0f0f0; border-radius: 4px; overflow: hidden; }
.rank-bar { height: 100%; border-radius: 4px; transition: width .4s; }
.rank-bar-silver  { background: #B4B2A9; }
.rank-bar-gold    { background: #EF9F27; }
.rank-bar-diamond { background: #378ADD; }
.rank-pct   { font-weight: 500; color: #333; min-width: 36px; text-align: right; }
.rank-count { font-size: 11px; color: #999; min-width: 40px; }

/* Inventory */
.inv-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.inv-table th {
  text-align: left;
  padding: 6px 8px;
  border-bottom: 1px solid #f0f0f0;
  color: #888;
  font-weight: 400;
}
.inv-table td { padding: 7px 8px; border-bottom: 1px solid #fafafa; }
.item-name { font-size: 12px; color: #333; margin: 0 0 2px; font-weight: 500; }
.item-code { font-size: 11px; color: #999; margin: 0; }
.text-muted { color: #999; }

/* Empty / Error */
.empty-state { text-align: center; color: #aaa; font-size: 13px; padding: 24px 0; }
.error-state { text-align: center; padding: 60px 0; }
.error-state p { color: #E24B4A; margin-bottom: 12px; }
.error-state button {
  padding: 8px 20px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: transparent;
  cursor: pointer;
  font-size: 13px;
}
</style>