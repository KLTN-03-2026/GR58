<template>
  <div class="w-full min-h-screen px-8 py-6 flex flex-col gap-6">
    <!-- Header -->
    <div class="flex flex-col gap-2">
      <h1 class="text-2xl font-semibold text-black">Báo cáo Kho & Vật tư</h1>
      <p class="text-gray-500 font-medium text-base">Phân tích tài chính kho hàng và tối ưu hóa luân chuyển</p>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div class="flex items-center justify-between gap-4">
        <!-- Period filter -->
        <div class="relative flex-1 max-w-md">
          <button
            @click.stop="toggleDropdown('period')"
            class="flex items-center justify-between w-full px-3 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
          >
            <span class="text-sm text-gray-900">{{ periodOptions.find(o => o.value === filters.period)?.label }}</span>
            <ChevronDownIcon class="w-4 h-4" />
          </button>
          <div v-if="openDropdown === 'period'" class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
            <button
              v-for="opt in periodOptions" :key="opt.value"
              @click.stop="selectFilter('period', opt.value)"
              class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 transition-colors"
              :class="filters.period === opt.value ? 'text-[#009689] font-semibold bg-teal-50' : 'text-gray-700'"
            >{{ opt.label }}</button>
          </div>
        </div>

        <!-- Category filter -->
        <div class="relative flex-1 max-w-md">
          <button
            @click.stop="toggleDropdown('danh_muc')"
            class="flex items-center justify-between w-full px-3 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
          >
            <span class="text-sm text-gray-900">{{ danhMucs.find(o => o.value == filters.danh_muc)?.label || 'Tất cả' }}</span>
            <ChevronDownIcon class="w-4 h-4" />
          </button>
          <div v-if="openDropdown === 'danh_muc'" class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden max-h-48 overflow-y-auto">
            <button
              v-for="opt in danhMucs" :key="opt.value"
              @click.stop="selectFilter('danh_muc', opt.value)"
              class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 transition-colors"
              :class="filters.danh_muc == opt.value ? 'text-[#009689] font-semibold bg-teal-50' : 'text-gray-700'"
            >{{ opt.label }}</button>
          </div>
        </div>

        <!-- Export -->
        <button @click="handleExport" class="flex items-center gap-2 px-4 py-2 bg-[#5a9690] hover:bg-[#5a9690]/80 text-white rounded-lg transition-colors whitespace-nowrap">
          <DownloadIcon class="w-4 h-4 text-white" />
          <span class="text-sm font-medium">Xuất báo cáo Kiểm kê</span>
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex items-center justify-center py-20">
      <div class="w-8 h-8 border-4 border-[#009689] border-t-transparent rounded-full animate-spin"></div>
      <span class="ml-3 text-gray-500 text-sm">Đang tải dữ liệu...</span>
    </div>

    <template v-else>
      <!-- Charts Section -->
      <div class="grid grid-cols-3 gap-6">
        <!-- Movement Chart -->
        <div class="col-span-2 bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
          <div class="mb-4">
            <h3 class="text-base font-normal text-gray-900 mb-1">Biểu đồ Biến động Kho</h3>
            <p class="text-sm text-gray-600">So sánh Nhập - Xuất hủy theo thời gian</p>
          </div>
          <div class="h-72 mb-4">
            <apexchart
              v-if="movementChart.labels.length"
              type="bar" height="288"
              :options="inventoryChartOptions"
              :series="inventoryChartSeries"
            />
            <div v-else class="flex items-center justify-center h-full text-gray-400 text-sm">Không có dữ liệu</div>
          </div>
          <div class="bg-amber-50 border !border-amber-200 rounded-lg p-3">
            <p class="text-xs text-amber-900">
              <span class="font-bold">Insight:</span> Nếu cột Đỏ (Hủy) tăng đột biến →
              Kiểm tra quy trình bảo quản, nhiệt độ tủ lạnh, và hạn sử dụng khi nhập hàng
            </p>
          </div>
        </div>

        <!-- Donut Chart -->
        <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
          <div class="mb-4">
            <h3 class="text-base font-normal text-gray-900 mb-1">Tỷ trọng Kho</h3>
            <p class="text-sm text-gray-600">Phân bổ giá trị theo loại</p>
          </div>
          <div class="h-72 flex items-center justify-center mb-4">
            <apexchart
              v-if="donutChart.series.length"
              type="donut" height="288"
              :options="donutChartOptions"
              :series="donutChart.series"
            />
            <div v-else class="text-gray-400 text-sm">Không có dữ liệu</div>
          </div>
          <!-- Legend -->
          <div class="space-y-2">
            <div v-for="(label, i) in donutChart.labels" :key="i" class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: donutColors[i] || '#ccc' }"></div>
                <span class="text-sm text-gray-700">{{ label }}</span>
              </div>
              <span class="text-sm text-gray-900">{{ donutChart.series[i] }}%</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Selling Table -->
      <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
        <div class="mb-4">
          <h3 class="text-base font-normal text-gray-900 mb-2">Top Hàng Bán Chạy & Lợi nhuận</h3>
          <p class="text-sm text-gray-600">Hàng có doanh thu cao - Nên nhập nhiều để được chiết khấu tốt</p>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-200">
                <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">Tên hàng</th>
                <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">Danh mục</th>
                <th class="text-right text-sm text-gray-600 font-normal py-3 px-2">Số lượng nhập</th>
                <th class="text-right text-sm text-gray-600 font-normal py-3 px-2">Doanh thu (dự kiến)</th>
                <th class="text-right text-sm text-gray-600 font-normal py-3 px-2">Lợi nhuận gộp</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!topSelling.length">
                <td colspan="5" class="py-8 text-center text-gray-400 text-sm">Không có dữ liệu</td>
              </tr>
              <tr v-for="item in topSelling" :key="item.id" class="border-b border-gray-100 hover:bg-gray-50">
                <td class="py-4 px-2">
                  <div class="flex items-center gap-2">
                    <span class="inline-block bg-amber-100 text-amber-800 text-xs font-medium px-2 py-1 rounded">{{ item.rank }}</span>
                    <span class="text-sm text-gray-900">{{ item.name }}</span>
                  </div>
                </td>
                <td class="py-4 px-2">
                  <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">{{ item.category }}</span>
                </td>
                <td class="py-4 px-2 text-right text-sm text-gray-900">{{ item.quantity }}</td>
                <td class="py-4 px-2 text-right text-sm text-gray-900">{{ item.revenue }}</td>
                <td class="py-4 px-2 text-right">
                  <p class="text-sm text-gray-900 mb-1">{{ item.profit }}</p>
                  <p class="text-xs text-gray-500">{{ item.margin }}</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="bg-blue-50 border !border-blue-200 rounded-lg p-3 mt-4">
          <p class="text-xs text-blue-900"><span class="font-bold">Action:</span> Nhập số lượng lớn để được chiết khấu tốt hơn từ nhà cung cấp</p>
        </div>
      </div>

      <!-- Dead Stock Table -->
      <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
        <div class="mb-4">
          <h3 class="text-base font-normal text-gray-900 mb-2">Hàng Chậm luân chuyển (Dead Stock)</h3>
          <p class="text-sm text-gray-600">Hàng tồn kho > 90 ngày - Cần tạo khuyến mãi để thu hồi vốn</p>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-200">
                <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">Tên hàng</th>
                <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">Danh mục</th>
                <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">Ngày nhập gần nhất</th>
                <th class="text-right text-sm text-gray-600 font-normal py-3 px-2">SL Tồn</th>
                <th class="text-right text-sm text-gray-600 font-normal py-3 px-2">Số ngày lưu kho</th>
                <th class="text-right text-sm text-gray-600 font-normal py-3 px-2">Giá trị tồn</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!deadStock.length">
                <td colspan="6" class="py-8 text-center text-gray-400 text-sm">Không có hàng chậm luân chuyển</td>
              </tr>
              <tr v-for="item in deadStock" :key="item.id" class="border-b border-gray-100 hover:bg-gray-50">
                <td class="py-3 px-2 text-sm text-gray-900">{{ item.name }}</td>
                <td class="py-3 px-2">
                  <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">{{ item.category }}</span>
                </td>
                <td class="py-3 px-2 text-sm text-gray-700">{{ item.importDate }}</td>
                <td class="py-3 px-2 text-right text-sm text-gray-900">{{ item.quantity }}</td>
                <td class="py-3 px-2 text-right">
                  <span class="inline-block bg-orange-100 text-orange-700 text-xs px-2 py-1 rounded">{{ item.daysInStock }}</span>
                </td>
                <td class="py-3 px-2 text-right text-sm text-gray-900">{{ item.value }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="bg-blue-50 border !border-blue-200 rounded-lg p-3 mt-4">
          <p class="text-xs text-blue-900 mb-2"><span class="font-bold">Action Plan:</span></p>
          <ul class="space-y-1 text-xs text-blue-900">
            <li>• Flash sale: Giảm 20-30% để đẩy hàng nhanh</li>
            <li>• Bundle với hàng hot (mua kèm giảm giá)</li>
            <li>• Return to supplier (nếu có thỏa thuận)</li>
          </ul>
        </div>
      </div>

      <!-- Inventory Check Log Table -->
      <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-base font-normal text-gray-900 mb-1">Lịch sử Kiểm kê Kho (Y tá)</h3>
            <p class="text-sm text-gray-600">Toàn bộ các lần đối chiếu tồn kho thực tế với hệ thống</p>
          </div>
          <div class="flex items-center gap-3 text-xs text-gray-500">
            <div class="flex items-center gap-1"><span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500"></span>Thừa</div>
            <div class="flex items-center gap-1"><span class="inline-block w-2.5 h-2.5 rounded-full bg-red-500"></span>Thiếu</div>
            <div class="flex items-center gap-1"><span class="inline-block w-2.5 h-2.5 rounded-full bg-gray-400"></span>Đúng</div>
          </div>
        </div>

        <!-- Filter hàng hóa -->
        <div class="flex items-center gap-3 mb-4">
          <div class="relative">
            <input
              v-model="kiemKeSearch"
              type="text"
              placeholder="Tìm tên hàng hóa..."
              class="bg-gray-100 border-none rounded-lg h-9 w-52 pl-3 pr-3 text-sm text-gray-900 outline-none placeholder:text-gray-400"
            />
          </div>
          <div class="relative">
            <button
              @click.stop="toggleDropdown('kiem_ke_trang_thai')"
              class="flex items-center justify-between gap-2 px-3 py-2 bg-gray-100 rounded-lg text-sm text-gray-700 hover:bg-gray-200 transition-colors min-w-[140px]"
            >
              {{ kiemKeTrangThaiLabel }}
              <ChevronDownIcon class="w-4 h-4" />
            </button>
            <div v-if="openDropdown === 'kiem_ke_trang_thai'" class="absolute z-30 top-full mt-1 w-44 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
              <button
                v-for="opt in kiemKeTrangThaiOptions" :key="opt.value"
                @click.stop="kiemKeTrangThai = opt.value; openDropdown = null"
                class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 transition-colors"
                :class="kiemKeTrangThai === opt.value ? 'text-[#009689] font-semibold bg-teal-50' : 'text-gray-700'"
              >{{ opt.label }}</button>
            </div>
          </div>
          <span class="text-xs text-gray-400 ml-auto">{{ filteredKiemKeLogs.length }} bản ghi</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-200">
                <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">Ngày kiểm kê</th>
                <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">Hàng hóa</th>
                <th class="text-right text-sm text-gray-600 font-normal py-3 px-2">Tồn máy</th>
                <th class="text-right text-sm text-gray-600 font-normal py-3 px-2">Thực tế</th>
                <th class="text-right text-sm text-gray-600 font-normal py-3 px-2">Chênh lệch</th>
                <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">Lý do</th>
                <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">Người kiểm kê</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loadingKiemKe">
                <td colspan="7" class="py-8 text-center">
                  <div class="flex items-center justify-center gap-2 text-gray-400 text-sm">
                    <div class="w-5 h-5 border-2 border-[#009689] border-t-transparent rounded-full animate-spin"></div>
                    Đang tải...
                  </div>
                </td>
              </tr>
              <tr v-else-if="!filteredKiemKeLogs.length">
                <td colspan="7" class="py-8 text-center text-gray-400 text-sm">Không có dữ liệu kiểm kê</td>
              </tr>
              <tr
                v-for="log in filteredKiemKeLogs" :key="log.id"
                class="border-b border-gray-100 hover:bg-gray-50 transition-colors"
              >
                <td class="py-3 px-2 text-sm text-gray-700 whitespace-nowrap">{{ formatDate(log.ngay_kiem_ke) }}</td>
                <td class="py-3 px-2">
                  <p class="text-sm text-gray-900">{{ log.hang_hoa?.ten_mat_hang ?? '—' }}</p>
                  <p class="text-xs text-gray-400">{{ log.hang_hoa?.ma_hang_hoa }}</p>
                </td>
                <td class="py-3 px-2 text-right text-sm text-gray-700">{{ log.so_luong_he_thong }}</td>
                <td class="py-3 px-2 text-right text-sm text-gray-700">{{ log.so_luong_thuc_te }}</td>
                <td class="py-3 px-2 text-right">
                  <span
                    class="inline-block text-xs font-semibold px-2 py-1 rounded"
                    :class="{
                      'bg-green-100 text-green-700': log.chenh_lech > 0,
                      'bg-red-100 text-red-700': log.chenh_lech < 0,
                      'bg-gray-100 text-gray-500': log.chenh_lech === 0,
                    }"
                  >
                    {{ log.chenh_lech > 0 ? '+' : '' }}{{ log.chenh_lech }}
                    ({{ log.trang_thai_chenh_lech }})
                  </span>
                </td>
                <td class="py-3 px-2 text-sm text-gray-700">{{ log.ly_do ?? '—' }}</td>
                <td class="py-3 px-2">
                  <p class="text-sm text-gray-900">{{ log.nguoi_kiem_ke_info?.ho_ten ?? '—' }}</p>
                  <p class="text-xs text-gray-400">{{ log.nguoi_kiem_ke_info?.chuc_danh ?? log.nguoi_kiem_ke_info?.type }}</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Expiring Soon Table -->
      <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
        <div class="mb-4">
          <h3 class="text-base font-normal text-gray-900 mb-2">Cảnh báo Cận Date (Expiring Soon)</h3>
          <p class="text-sm text-gray-600">Hàng sắp hết hạn &lt; 30 ngày - Cần xử lý ngay để tránh loss</p>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-200">
                <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">Tên thuốc</th>
                <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">Số lô</th>
                <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">Ngày hết hạn</th>
                <th class="text-right text-sm text-gray-600 font-normal py-3 px-2">SL Còn lại</th>
                <th class="text-right text-sm text-gray-600 font-normal py-3 px-2">Trạng thái</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!expiringSoon.length">
                <td colspan="5" class="py-8 text-center text-gray-400 text-sm">Không có hàng sắp hết hạn</td>
              </tr>
              <tr v-for="item in expiringSoon" :key="item.id" class="border-b border-gray-100 hover:bg-gray-50">
                <td class="py-3 px-2 text-sm text-gray-900">{{ item.name }}</td>
                <td class="py-3 px-2">
                  <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded font-mono">{{ item.lotNumber }}</span>
                </td>
                <td class="py-3 px-2 text-sm text-gray-700">{{ item.expiryDate }}</td>
                <td class="py-3 px-2 text-right text-sm text-gray-900">{{ item.quantity }}</td>
                <td class="py-3 px-2 text-right">
                  <span :class="['inline-block text-xs px-2 py-1 rounded', item.urgent ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700']">
                    {{ item.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mt-4 space-y-3">
          <div class="flex items-center gap-4 text-xs text-gray-700">
            <span class="font-medium">Mức độ:</span>
            <div class="flex items-center gap-2"><div class="w-4 h-4 bg-red-500 rounded"></div><span>&lt; 7 ngày (URGENT)</span></div>
            <div class="flex items-center gap-2"><div class="w-4 h-4 bg-orange-500 rounded"></div><span>7-30 ngày (Warning)</span></div>
          </div>
          <div class="bg-red-50 border !border-red-200 rounded-lg p-3">
            <p class="text-xs text-red-900 mb-1"><span class="font-bold">Action Matrix:</span></p>
            <p class="text-xs text-red-900"><span class="font-bold">&lt; 7 ngày:</span> Flash sale 50% OFF | Chương trình từ thiện | Báo huỷ NCC</p>
            <p class="text-xs text-red-900"><span class="font-bold">7-30 ngày:</span> Bundle promotion | Ưu tiên gợi ý cho khách</p>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { getInventoryReport, exportInventoryReport, getKiemKeLogs } from '@/services/inventoryReportService'
import ChevronDownIcon from '@/assets/svg/chevron-down.svg'
import DownloadIcon    from '@/assets/svg/download.svg'

// ─── State ────────────────────────────────────────────────────────────
const loading     = ref(false)
const openDropdown = ref(null)

const movementChart = ref({ labels: [], series: [] })
const donutChart    = ref({ labels: [], series: [], total: '0' })
const topSelling    = ref([])
const deadStock     = ref([])
const expiringSoon  = ref([])
const danhMucs      = ref([{ value: 'all', label: 'Tất cả' }])

const filters = reactive({ period: 'this_year', danh_muc: 'all' })

// ─── Kiểm kê state ────────────────────────────────────────────────────
const kiemKeLogs     = ref([])
const loadingKiemKe  = ref(false)
const kiemKeSearch   = ref('')
const kiemKeTrangThai = ref('all')

const kiemKeTrangThaiOptions = [
  { value: 'all',  label: 'Tất cả' },
  { value: 'thieu', label: 'Thiếu' },
  { value: 'thua',  label: 'Thừa' },
  { value: 'dung',  label: 'Đúng' },
]

const kiemKeTrangThaiLabel = computed(() =>
  kiemKeTrangThaiOptions.find(o => o.value === kiemKeTrangThai.value)?.label ?? 'Tất cả'
)

const filteredKiemKeLogs = computed(() => {
  let list = kiemKeLogs.value
  const q = kiemKeSearch.value.trim().toLowerCase()
  if (q) {
    list = list.filter(log =>
      log.hang_hoa?.ten_mat_hang?.toLowerCase().includes(q) ||
      log.hang_hoa?.ma_hang_hoa?.toLowerCase().includes(q)
    )
  }
  if (kiemKeTrangThai.value !== 'all') {
    list = list.filter(log => {
      if (kiemKeTrangThai.value === 'thua')  return log.chenh_lech > 0
      if (kiemKeTrangThai.value === 'thieu') return log.chenh_lech < 0
      if (kiemKeTrangThai.value === 'dung')  return log.chenh_lech === 0
      return true
    })
  }
  return list
})

// ─── Options ──────────────────────────────────────────────────────────
const periodOptions = [
  { value: 'this_month', label: 'Tháng này' },
  { value: '3months',    label: '3 tháng qua' },
  { value: '6months',    label: '6 tháng qua' },
  { value: 'this_year',  label: 'Năm nay' },
]

const donutColors = ['#14b8a6', '#3b82f6', '#8b5cf6', '#f59e0b', '#10b981', '#ef4444', '#ec4899']

// ─── Chart options (computed để reactive với data) ────────────────────
const inventoryChartSeries = computed(() => movementChart.value.series ?? [])

const inventoryChartOptions = computed(() => ({
  chart: { type: 'bar', height: 288, toolbar: { show: false } },
  plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 } },
  dataLabels: { enabled: false },
  stroke: { show: true, width: 2, colors: ['transparent'] },
  xaxis: {
    categories: movementChart.value.labels ?? [],
    labels: { style: { fontSize: '12px', colors: '#6b7280' } },
  },
  yaxis: {
    title: { text: 'Triệu đồng (M)', style: { fontSize: '12px', color: '#6b7280' } },
    labels: {
      style: { fontSize: '12px', colors: '#6b7280' },
      formatter: (val) => val.toFixed(1) + 'M',
    },
  },
  fill: { opacity: 1 },
  tooltip: { y: { formatter: (val) => val.toFixed(1) + ' triệu đồng' } },
  colors: ['#3b82f6', '#ef4444'],
  legend: { position: 'top', horizontalAlign: 'center', fontSize: '12px' },
  grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
}))

const donutChartOptions = computed(() => ({
  chart: { type: 'donut', height: 288 },
  labels: donutChart.value.labels ?? [],
  colors: donutColors,
  plotOptions: {
    pie: {
      donut: {
        size: '65%',
        labels: {
          show: true,
          total: {
            show: true,
            label: 'Tổng kho',
            fontSize: '14px',
            color: '#6b7280',
            formatter: () => donutChart.value.total ?? '0',
          },
        },
      },
    },
  },
  dataLabels: { enabled: false },
  legend: { show: false },
  stroke: { width: 2, colors: ['#fff'] },
  tooltip: { y: { formatter: (val) => val + '%' } },
}))

// ─── Helpers ──────────────────────────────────────────────────────────
const formatDate = (dateStr) => {
  if (!dateStr) return '—'
  const d = new Date(dateStr)
  if (isNaN(d.getTime())) return dateStr
  return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`
}

// ─── Fetch ────────────────────────────────────────────────────────────
const fetchData = async () => {
  loading.value = true
  try {
    const res = await getInventoryReport({
      period:    filters.period,
      danh_muc:  filters.danh_muc !== 'all' ? filters.danh_muc : undefined,
    })

    if (res.status) {
      const d = res.data
      movementChart.value = d.movement_chart ?? { labels: [], series: [] }
      donutChart.value    = d.donut_chart    ?? { labels: [], series: [], total: '0' }
      topSelling.value    = d.top_selling    ?? []
      deadStock.value     = d.dead_stock     ?? []
      expiringSoon.value  = d.expiring_soon  ?? []
      if (d.danh_mucs?.length) danhMucs.value = d.danh_mucs
    }
  } catch (err) {
    console.error('Lỗi tải báo cáo kho:', err)
  } finally {
    loading.value = false
  }
}

const fetchKiemKeLogs = async () => {
  loadingKiemKe.value = true
  try {
    const res = await getKiemKeLogs()
    if (res.status) {
      // API trả về paginated: res.data.data hoặc res.data trực tiếp là array
      const rows = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])
      kiemKeLogs.value = rows
    }
  } catch (err) {
    console.error('Lỗi tải lịch sử kiểm kê:', err)
  } finally {
    loadingKiemKe.value = false
  }
}

// ─── Events ───────────────────────────────────────────────────────────
const toggleDropdown = (name) => {
  openDropdown.value = openDropdown.value === name ? null : name
}

const selectFilter = (key, value) => {
  filters[key] = value
  openDropdown.value = null
  fetchData()
}

const handleExport = async () => {
  try {
    await exportInventoryReport({
      period:   filters.period,
      danh_muc: filters.danh_muc !== 'all' ? filters.danh_muc : undefined,
    })
  } catch (err) {
    console.error('Export error:', err)
    alert('Xuất Excel thất bại. Vui lòng thử lại.')
  }
}

onMounted(() => {
  fetchData()
  fetchKiemKeLogs()
})
</script>

<style scoped>
.overflow-x-auto::-webkit-scrollbar { height: 6px; }
.overflow-x-auto::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
.overflow-x-auto::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
.overflow-x-auto::-webkit-scrollbar-thumb:hover { background: #555; }
</style>