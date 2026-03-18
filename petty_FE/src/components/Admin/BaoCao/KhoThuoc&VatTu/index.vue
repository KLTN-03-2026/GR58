<template>
  <div class="w-full min-h-screen px-8 py-6 flex flex-col gap-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-2">
      <h1 class="text-2xl font-semibold text-black">Báo cáo Kho & Vật tư</h1>
      <p class="text-gray-500 font-medium text-base">
        Phân tích tài chính kho hàng và tối ưu hóa luân chuyển
      </p>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div class="flex items-center justify-between gap-4">
        <!-- Month Filter -->
        <button
          class="flex items-center justify-between px-3 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex-1 max-w-md"
        >
          <span class="text-sm text-gray-900">Tháng này</span>
          <ChevronDownIcon class="w-4 h-4" />
        </button>

        <!-- Category Filter -->
        <button
          class="flex items-center justify-between px-3 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex-1 max-w-md"
        >
          <span class="text-sm text-gray-900">Tất cả</span>
          <ChevronDownIcon class="w-4 h-4" />
        </button>

        <!-- Export Button -->
        <button
          class="flex items-center gap-2 px-4 py-2 bg-[#5a9690] hover:bg-[#5a9690]/80 text-white rounded-lg transition-colors"
        >
          <DownloadIcon class="w-4 h-4 text-white" />
          <span class="text-sm font-medium">Xuất báo cáo Kiểm kê</span>
        </button>
      </div>
    </div>

    <!-- Stats Cards - Temporarily hidden -->
    <!-- <div class="grid grid-cols-3 gap-6">
      <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
        <div class="flex items-start justify-between mb-6">
          <div>
            <p class="text-sm text-gray-600 mb-1">Tổng Giá trị Tồn kho</p>
            <p class="text-xs text-gray-500">Current Inventory Value</p>
          </div>
        </div>
        <div class="mb-2">
          <p class="text-3xl font-normal text-teal-600">1.25B</p>
        </div>
        <p class="text-sm text-gray-600 mb-3">1.250.000.000 ₫</p>
        <div class="bg-blue-50 rounded px-2 py-2">
          <p class="text-xs text-blue-800">Số vốn đang "chôn" trong kho</p>
        </div>
      </div>

      <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
        <div class="flex items-start justify-between mb-6">
          <div>
            <p class="text-sm text-gray-600 mb-1">Giá trị Nhập trong kỳ</p>
            <p class="text-xs text-gray-500">Total Import</p>
          </div>
        </div>
        <div class="mb-2">
          <p class="text-3xl font-normal text-blue-600">200.0M</p>
        </div>
        <p class="text-sm text-gray-600 mb-3">200.000.000 ₫</p>
        <div class="bg-blue-50 rounded px-2 py-2">
          <p class="text-xs text-blue-800">Số tiền chi ra để mua hàng</p>
        </div>
      </div>

      <div class="bg-white border-2 !border-red-200 shadow-sm rounded-[14px] p-6">
        <div class="flex items-start justify-between mb-6">
          <div>
            <p class="text-sm text-gray-600 mb-1">Giá trị Hủy/Hao hụt</p>
            <p class="text-xs text-gray-500">Loss Value</p>
          </div>
        </div>
        <div class="mb-2">
          <p class="text-3xl font-normal text-red-600">5.5M</p>
        </div>
        <p class="text-sm text-gray-600 mb-3">5.500.000 ₫</p>
        <span class="inline-block bg-green-100 text-green-700 text-xs font-medium px-2 py-1 rounded-lg mb-2">
          ✓ 0.44% (Normal)
        </span>
        <div class="bg-red-50 rounded px-2 py-2">
          <p class="text-xs text-red-800">Tiền mất do hết hạn, vỡ, hỏng</p>
        </div>
      </div>
    </div> -->

    <!-- Charts Section -->
    <div class="grid grid-cols-3 gap-6 mb-6">
      <!-- Inventory Movement Chart -->
      <div
        class="col-span-2 bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
      >
        <div class="mb-4">
          <h3 class="text-base font-normal text-gray-900 mb-1">
            Biểu đồ Biến động Kho
          </h3>
          <p class="text-sm text-gray-600">
            So sánh Nhập - Xuất bán - Hủy theo thời gian
          </p>
        </div>

        <!-- ApexChart -->
        <div class="h-72 mb-4">
          <apexchart
            type="bar"
            height="288"
            :options="inventoryChartOptions"
            :series="inventoryChartSeries"
          ></apexchart>
        </div>

        <!-- Insight Box -->
        <div class="bg-amber-50 border !border-amber-200 rounded-lg p-3">
          <div class="flex items-start gap-2">
            <div>
              <p class="text-xs text-amber-900 mb-1">
                <span class="font-bold">Insight:</span> Nếu cột Đỏ (Hủy) tăng
                đột biến
              </p>
              <p class="text-xs text-amber-900">
                → Kiểm tra quy trình bảo quản, nhiệt độ tủ lạnh, và hạn sử dụng
                khi nhập hàng
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Inventory Distribution Chart -->
      <div
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
      >
        <div class="mb-4">
          <h3 class="text-base font-normal text-gray-900 mb-1">Tỷ trọng Kho</h3>
          <p class="text-sm text-gray-600">Phân bổ giá trị theo loại</p>
        </div>

        <!-- ApexChart Donut -->
        <div class="h-72 flex items-center justify-center mb-4">
          <apexchart
            type="donut"
            height="288"
            :options="donutChartOptions"
            :series="donutChartSeries"
          ></apexchart>
        </div>

        <!-- Legend -->
        <div class="space-y-2">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 bg-teal-600 rounded-full"></div>
              <span class="text-sm text-gray-700">Vắc-xin</span>
            </div>
            <span class="text-sm text-gray-900">50%</span>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
              <span class="text-sm text-gray-700">Thuốc</span>
            </div>
            <span class="text-sm text-gray-900">30%</span>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 bg-violet-500 rounded-full"></div>
              <span class="text-sm text-gray-700">Thức ăn</span>
            </div>
            <span class="text-sm text-gray-900">12%</span>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
              <span class="text-sm text-gray-700">Vật tư</span>
            </div>
            <span class="text-sm text-gray-900">6%</span>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
              <span class="text-sm text-gray-700">Khác</span>
            </div>
            <span class="text-sm text-gray-900">2%</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Selling Items Table -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div class="mb-4">
        <!-- <div class="flex items-center gap-2 mb-2">
          <img :src="iconTrending" alt="" class="w-5 h-5" />
        </div> -->
        <h3 class="text-base font-normal text-gray-900 mb-2">
          Top Hàng Bán Chạy & Lợi nhuận
        </h3>
        <p class="text-sm text-gray-600">
          Hàng có doanh thu cao - Nên nhập nhiều để được chiết khấu tốt
        </p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200">
              <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">
                Tên hàng
              </th>
              <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">
                Danh mục
              </th>
              <th
                class="text-right text-sm text-gray-600 font-normal py-3 px-2"
              >
                Số lượng bán
              </th>
              <th
                class="text-right text-sm text-gray-600 font-normal py-3 px-2"
              >
                Doanh thu
              </th>
              <th
                class="text-right text-sm text-gray-600 font-normal py-3 px-2"
              >
                Lợi nhuận gộp
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in topSellingItems"
              :key="item.id"
              class="border-b border-gray-100 hover:bg-gray-50"
            >
              <td class="py-4 px-2">
                <div class="flex items-center gap-2">
                  <span
                    class="inline-block bg-amber-100 text-amber-800 text-xs font-medium px-2 py-1 rounded"
                  >
                    {{ item.rank }}
                  </span>
                  <span class="text-sm text-gray-900">{{ item.name }}</span>
                </div>
              </td>
              <td class="py-4 px-2">
                <span
                  class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded"
                >
                  {{ item.category }}
                </span>
              </td>
              <td class="py-4 px-2 text-right text-sm text-gray-900">
                {{ item.quantity }}
              </td>
              <td class="py-4 px-2 text-right text-sm text-gray-900">
                {{ item.revenue }}
              </td>
              <td class="py-4 px-2">
                <div class="text-right">
                  <p class="text-sm text-gray-900 mb-1">{{ item.profit }}</p>
                  <p class="text-xs text-gray-500">{{ item.margin }}</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bg-blue-50 border !border-blue-200 rounded-lg p-3 mt-4">
        <p class="text-xs text-blue-900">
          <span class="font-bold">Action:</span> Nhập số lượng lớn để được chiết
          khấu tốt hơn từ nhà cung cấp
        </p>
      </div>
    </div>

    <!-- Dead Stock Table -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div class="mb-4">
        <!-- <div class="flex items-center gap-2 mb-2">
          <img :src="iconAlert" alt="" class="w-5 h-5" />
        </div> -->
        <h3 class="text-base font-normal text-gray-900 mb-2">
          Hàng Chậm luân chuyển (Dead Stock)
        </h3>
        <p class="text-sm text-gray-600">
          Hàng tồn kho > 90 ngày - Cần tạo khuyến mãi để thu hồi vốn
        </p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200">
              <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">
                Tên hàng
              </th>
              <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">
                Danh mục
              </th>
              <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">
                Ngày nhập gần nhất
              </th>
              <th
                class="text-right text-sm text-gray-600 font-normal py-3 px-2"
              >
                SL Tồn
              </th>
              <th
                class="text-right text-sm text-gray-600 font-normal py-3 px-2"
              >
                Số ngày lưu kho
              </th>
              <th
                class="text-right text-sm text-gray-600 font-normal py-3 px-2"
              >
                Giá trị tồn
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in deadStockItems"
              :key="item.id"
              class="border-b border-gray-100 hover:bg-gray-50"
            >
              <td class="py-3 px-2 text-sm text-gray-900">{{ item.name }}</td>
              <td class="py-3 px-2">
                <span
                  class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded"
                >
                  {{ item.category }}
                </span>
              </td>
              <td class="py-3 px-2">
                <!-- <div class="flex items-center gap-2">
                  <img :src="iconCalendar" alt="" class="w-4 h-4" />
                  <span class="text-sm text-gray-700">{{ item.importDate }}</span>
                </div> -->
                <span class="text-sm text-gray-700">{{ item.importDate }}</span>
              </td>
              <td class="py-3 px-2 text-right text-sm text-gray-900">
                {{ item.quantity }}
              </td>
              <td class="py-3 px-2 text-right">
                <span
                  class="inline-block bg-orange-100 text-orange-700 text-xs px-2 py-1 rounded"
                >
                  {{ item.daysInStock }}
                </span>
              </td>
              <td class="py-3 px-2 text-right text-sm text-gray-900">
                {{ item.value }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bg-blue-50 border !border-blue-200 rounded-lg p-3 mt-4">
        <p class="text-xs text-blue-900 mb-2">
          <span class="font-bold">Action Plan:</span>
        </p>
        <ul class="space-y-1 text-xs text-blue-900">
          <li>• Flash sale: Giảm 20-30% để đẩy hàng nhanh</li>
          <li>• Bundle với hàng hot (mua kèm giảm giá)</li>
          <li>• Return to supplier (nếu có thỏa thuận)</li>
          <li>• Donate (nhận tax benefit + PR tốt)</li>
        </ul>
      </div>
    </div>

    <!-- Expiring Soon Table -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div class="mb-4">
        <!-- <div class="flex items-center gap-2 mb-2">
          <img :src="iconWarning" alt="" class="w-5 h-5" />
        </div> -->
        <h3 class="text-base font-normal text-gray-900 mb-2">
          Cảnh báo Cận Date (Expiring Soon)
        </h3>
        <p class="text-sm text-gray-600">
          Hàng sắp hết hạn &lt; 30 ngày - Cần xử lý ngay để tránh loss
        </p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200">
              <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">
                Tên thuốc
              </th>
              <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">
                Số lô
              </th>
              <th class="text-left text-sm text-gray-600 font-normal py-3 px-2">
                Ngày hết hạn
              </th>
              <th
                class="text-right text-sm text-gray-600 font-normal py-3 px-2"
              >
                SL Còn lại
              </th>
              <th
                class="text-right text-sm text-gray-600 font-normal py-3 px-2"
              >
                Trạng thái
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in expiringItems"
              :key="item.id"
              class="border-b border-gray-100 hover:bg-gray-50"
            >
              <td class="py-3 px-2 text-sm text-gray-900">{{ item.name }}</td>
              <td class="py-3 px-2">
                <span
                  class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded font-mono"
                >
                  {{ item.lotNumber }}
                </span>
              </td>
              <td class="py-3 px-2">
                <!-- <div class="flex items-center gap-2">
                  <img :src="iconCalendar" alt="" class="w-4 h-4" />
                  <span class="text-sm text-gray-700">{{ item.expiryDate }}</span>
                </div> -->
                <span class="text-sm text-gray-700">{{ item.expiryDate }}</span>
              </td>
              <td class="py-3 px-2 text-right text-sm text-gray-900">
                {{ item.quantity }}
              </td>
              <td class="py-3 px-2 text-right">
                <span
                  :class="[
                    'inline-block text-xs px-2 py-1 rounded',
                    item.urgent
                      ? 'bg-red-100 text-red-700'
                      : 'bg-orange-100 text-orange-700',
                  ]"
                >
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
          <div class="flex items-center gap-2">
            <div class="w-4 h-4 bg-red-500 rounded"></div>
            <span>&lt; 7 ngày (URGENT)</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-4 h-4 bg-orange-500 rounded"></div>
            <span>7-30 ngày (Warning)</span>
          </div>
        </div>

        <div class="bg-red-50 border !border-red-200 rounded-lg p-3">
          <p class="text-xs text-red-900 mb-2">
            <span class="font-bold">Action Matrix:</span>
          </p>
          <div class="space-y-1 text-xs text-red-900">
            <p>
              <span class="font-bold">&lt; 7 ngày:</span> Flash sale 50% OFF |
              Chương trình từ thiện | Báo huỷ NCC
            </p>
            <p>
              <span class="font-bold">7-30 ngày:</span> Bundle promotion | Ưu
              tiên gợi ý cho khách | Bày trưng bày nổi bật
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Info Card -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div class="flex items-start gap-3">
        <!-- <img :src="iconInfo" alt="" class="w-6 h-6 mt-1" /> -->
        <div class="flex-1 space-y-6">
          <!-- Logic Section -->
          <div>
            <p class="text-sm text-gray-900 mb-2">
              <span class="font-bold"
                >Logic tính Giá trị Hủy/Hao hụt (Loss Value):</span
              >
            </p>
            <div class="bg-gray-100 rounded px-2 py-1 mb-2">
              <code class="text-xs text-gray-800"
                >Loss Value = Σ (Hàng hết hạn + Vỡ + Hỏng + Chênh lệch kiểm
                kê)</code
              >
            </div>
            <p class="text-xs text-gray-700">
              <span class="font-bold">Example:</span> Vắc-xin hết hạn 3M + Thuốc
              vỡ 1.5M + Chênh lệch kiểm kê 1M =
              <span class="font-bold">5.5M Loss</span>
            </p>
          </div>

          <!-- Alert Thresholds -->
          <div>
            <p class="text-sm text-gray-900 mb-2">
              <span class="font-bold">Alert Thresholds:</span>
            </p>
            <ul class="space-y-1 text-xs text-gray-700">
              <li>• &lt;1% of inventory value: Normal (Current: 0.44%)</li>
              <li>• 1-3%: Need attention</li>
              <li>• &gt;3%: Critical issue - Review processes</li>
            </ul>
          </div>

          <!-- Dead Stock Definition -->
          <div>
            <p class="text-sm text-gray-900 mb-2">
              <span class="font-bold">Dead Stock Definition:</span>
            </p>
            <p class="text-xs text-gray-700">
              Hàng nhập &gt; 90 ngày, bán &lt; 10% số lượng → Vốn bị "chôn"
              không sinh lời
            </p>
          </div>

          <!-- FIFO Rule -->
          <div>
            <p class="text-sm text-gray-900 mb-2">
              <span class="font-bold">FIFO Rule (First In First Out):</span>
            </p>
            <p class="text-xs text-gray-700">
              Luôn xuất hàng nhập trước để giảm thiểu loss. Kiểm tra date khi
              xuất kho
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import VueApexCharts from "vue3-apexcharts";

// Icon SVG
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import DownloadIcon from "@/assets/svg/download.svg";

// Inventory Bar Chart Configuration
const inventoryChartSeries = ref([
  {
    name: "Nhập kho",
    data: [45, 52, 38, 45, 48, 50, 42, 47, 49, 44],
  },
  {
    name: "Xuất bán",
    data: [35, 41, 36, 26, 45, 48, 52, 53, 41, 38],
  },
  {
    name: "Xuất hủy",
    data: [2, 3, 1.5, 2.5, 2, 1.8, 2.2, 1.5, 2.8, 2.3],
  },
]);

const inventoryChartOptions = ref({
  chart: {
    type: "bar",
    height: 288,
    toolbar: {
      show: false,
    },
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: "55%",
      borderRadius: 4,
    },
  },
  dataLabels: {
    enabled: false,
  },
  stroke: {
    show: true,
    width: 2,
    colors: ["transparent"],
  },
  xaxis: {
    categories: ["T1", "T2", "T3", "T4", "T5", "T6", "T7", "T8", "T9", "T10"],
    labels: {
      style: {
        fontSize: "12px",
        colors: "#6b7280",
      },
    },
  },
  yaxis: {
    title: {
      text: "Triệu đồng (M)",
      style: {
        fontSize: "12px",
        color: "#6b7280",
      },
    },
    labels: {
      style: {
        fontSize: "12px",
        colors: "#6b7280",
      },
      formatter: function (val) {
        return val.toFixed(1) + "M";
      },
    },
  },
  fill: {
    opacity: 1,
  },
  tooltip: {
    y: {
      formatter: function (val) {
        return val.toFixed(1) + " triệu đồng";
      },
    },
  },
  colors: ["#3b82f6", "#16a34a", "#ef4444"],
  legend: {
    position: "top",
    horizontalAlign: "center",
    fontSize: "12px",
    fontFamily: "Nunito Sans, sans-serif",
    markers: {
      width: 10,
      height: 10,
      radius: 2,
    },
  },
  grid: {
    borderColor: "#f3f4f6",
    strokeDashArray: 4,
  },
});

// Donut Chart Configuration
const donutChartSeries = ref([50, 30, 12, 6, 2]);

const donutChartOptions = ref({
  chart: {
    type: "donut",
    height: 288,
  },
  labels: ["Vắc-xin", "Thuốc", "Thức ăn", "Vật tư", "Khác"],
  colors: ["#14b8a6", "#3b82f6", "#8b5cf6", "#f59e0b", "#10b981"],
  plotOptions: {
    pie: {
      donut: {
        size: "65%",
        labels: {
          show: true,
          name: {
            show: true,
            fontSize: "14px",
            fontFamily: "Nunito Sans, sans-serif",
            color: "#374151",
          },
          value: {
            show: true,
            fontSize: "20px",
            fontFamily: "Nunito Sans, sans-serif",
            color: "#111827",
            formatter: function (val) {
              return val + "%";
            },
          },
          total: {
            show: true,
            label: "Tổng kho",
            fontSize: "14px",
            color: "#6b7280",
            formatter: function (w) {
              return "1.25B";
            },
          },
        },
      },
    },
  },
  dataLabels: {
    enabled: false,
  },
  legend: {
    show: false,
  },
  stroke: {
    width: 2,
    colors: ["#fff"],
  },
  tooltip: {
    y: {
      formatter: function (val) {
        return val + "%";
      },
    },
  },
});

// Icons from Figma
const iconChevronDown =
  "https://www.figma.com/api/mcp/asset/2ec93300-57ad-4337-8707-244920c76d1c";
const iconExport =
  "https://www.figma.com/api/mcp/asset/61a5eebe-6cf8-4b4a-965a-8f6e0c2fec7b";
const iconInventory =
  "https://www.figma.com/api/mcp/asset/a050a96f-f2fd-428d-bc98-afbe51c3c86d";
const iconImport =
  "https://www.figma.com/api/mcp/asset/490e80d7-059b-4bae-a94c-91364a974e17";
const iconLoss =
  "https://www.figma.com/api/mcp/asset/45f43398-4ecb-41d8-a522-04b7d1df62b0";
const iconChart =
  "https://www.figma.com/api/mcp/asset/76698a6a-779d-43a9-97b4-316996ac6138";
const iconInfo =
  "https://www.figma.com/api/mcp/asset/496c2fca-1d6f-41fd-8e33-9df4b5e5b991";
const iconTrending =
  "https://www.figma.com/api/mcp/asset/76698a6a-779d-43a9-97b4-316996ac6138";
const iconAlert =
  "https://www.figma.com/api/mcp/asset/76698a6a-779d-43a9-97b4-316996ac6138";
const iconWarning =
  "https://www.figma.com/api/mcp/asset/496c2fca-1d6f-41fd-8e33-9df4b5e5b991";
const iconCalendar =
  "https://www.figma.com/api/mcp/asset/2ec93300-57ad-4337-8707-244920c76d1c";
const chartImage =
  "https://www.figma.com/api/mcp/asset/22809b7a-1e4a-4de0-9c8b-8c41aa841e4e";

// Top Selling Items Data
const topSellingItems = ref([
  {
    id: 1,
    rank: "#1",
    name: "Vắc-xin Rabies",
    category: "Vắc-xin",
    quantity: "150",
    revenue: "45.0M",
    profit: "15.0M",
    margin: "33% margin",
  },
  {
    id: 2,
    rank: "#2",
    name: "Thuốc tẩy giun Drontal",
    category: "Thuốc",
    quantity: "200",
    revenue: "40.0M",
    profit: "12.0M",
    margin: "30% margin",
  },
  {
    id: 3,
    rank: "#3",
    name: "Vắc-xin 6 bệnh",
    category: "Vắc-xin",
    quantity: "120",
    revenue: "36.0M",
    profit: "10.8M",
    margin: "30% margin",
  },
  {
    id: 4,
    rank: "#4",
    name: "Thức ăn Royal Canin",
    category: "Thức ăn",
    quantity: "180",
    revenue: "32.4M",
    profit: "8.1M",
    margin: "25% margin",
  },
  {
    id: 5,
    rank: "#5",
    name: "Thuốc kháng sinh Amoxicillin",
    category: "Thuốc",
    quantity: "160",
    revenue: "28.8M",
    profit: "8.6M",
    margin: "30% margin",
  },
]);

// Dead Stock Items Data
const deadStockItems = ref([
  {
    id: 1,
    name: "Thuốc trị nấm Ketoconazole",
    category: "Thuốc",
    importDate: "15/08/2024",
    quantity: "100",
    daysInStock: "97 ngày",
    value: "5.0M",
  },
  {
    id: 2,
    name: "Vật tư băng gạc vô trùng",
    category: "Vật tư",
    importDate: "20/08/2024",
    quantity: "200",
    daysInStock: "92 ngày",
    value: "3.0M",
  },
  {
    id: 3,
    name: "Thức ăn dinh dưỡng Pro Plan",
    category: "Thức ăn",
    importDate: "10/08/2024",
    quantity: "50",
    daysInStock: "102 ngày",
    value: "8.0M",
  },
  {
    id: 4,
    name: "Thuốc tiêm an thần Xylazine",
    category: "Thuốc",
    importDate: "25/08/2024",
    quantity: "30",
    daysInStock: "87 ngày",
    value: "4.5M",
  },
]);

// Expiring Items Data
const expiringItems = ref([
  {
    id: 1,
    name: "Vitamin tổng hợp ABC",
    lotNumber: "LOT-2024-001",
    expiryDate: "25/12/2024",
    quantity: "50",
    status: "Còn 5 ngày",
    urgent: true,
  },
  {
    id: 2,
    name: "Kháng sinh Cephalexin",
    lotNumber: "LOT-2024-123",
    expiryDate: "28/12/2024",
    quantity: "30",
    status: "Còn 8 ngày",
    urgent: false,
  },
  {
    id: 3,
    name: "Thuốc chống viêm Meloxicam",
    lotNumber: "LOT-2024-456",
    expiryDate: "05/01/2025",
    quantity: "40",
    status: "Còn 16 ngày",
    urgent: false,
  },
  {
    id: 4,
    name: "Thuốc trị giun sán Ivermectin",
    lotNumber: "LOT-2024-999",
    expiryDate: "10/01/2025",
    quantity: "60",
    status: "Còn 21 ngày",
    urgent: false,
  },
  {
    id: 5,
    name: "Vắc-xin phòng dại Nobivac",
    lotNumber: "LOT-2024-789",
    expiryDate: "15/01/2025",
    quantity: "25",
    status: "Còn 26 ngày",
    urgent: false,
  },
]);
</script>

<style scoped>
/* Custom scrollbar for tables */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
