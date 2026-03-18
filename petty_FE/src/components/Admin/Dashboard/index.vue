<template>
  <div class="w-full px-8 py-6">
    <div class="w-full">
      <!-- Stats Cards Grid -->
      <div class="grid grid-cols-4 gap-6 mb-6">
        <!-- Card 1: Doanh thu hôm nay -->
        <div
          class="bg-white border-l-4 border-l-green-500 border-t border-r border-b border-gray-100 rounded-[14px] p-6"
        >
          <div class="flex items-center justify-between mb-4">
            <p class="font-nunitoSans font-medium text-[#4b5563] text-sm">
              Doanh thu hôm nay
            </p>
            <DollarIcon class="w-6 h-6 text-green-500" />
          </div>
          <div class="flex flex-col gap-2">
            <p class="font-nunitoSans font-medium text-[#16a34a] text-3xl">
              {{ formatCurrency(stats.todayRevenue) }}
            </p>
            <div class="flex gap-1 items-center">
              <ArrowUpIcon class="w-4 h-4 text-green-500" />
              <span
                class="font-nunitoSans font-medium text-[#16a34a] text-base"
              >
                {{ stats.revenueChange }}%
              </span>
              <span
                class="font-nunitoSans font-medium text-[#4b5563] text-base"
              >
                so với hôm qua
              </span>
            </div>
          </div>
        </div>

        <!-- Card 2: Lịch hẹn hôm nay -->
        <div
          class="bg-white border-l-4 border-l-blue-500 border-t border-r border-b border-gray-100 rounded-[14px] p-6"
        >
          <div class="flex items-center justify-between mb-4">
            <p class="font-nunitoSans font-medium text-[#4b5563] text-sm">
              Lịch hẹn hôm nay
            </p>
            <CalendarIcon class="w-6 h-6 text-blue-500" />
          </div>
          <div class="flex flex-col gap-2">
            <p class="font-nunitoSans font-medium text-[#2563eb] text-3xl">
              {{ stats.todayAppointments }}
            </p>
            <div class="flex gap-4 items-center">
              <div class="flex gap-1 items-center">
                <TickIcon class="w-4 h-4 text-green-500" />
                <span
                  class="font-nunitoSans font-medium text-[#4b5563] text-base"
                >
                  {{ stats.completedAppointments }} hoàn thành
                </span>
              </div>
              <div class="flex gap-1 items-center">
                <CancelIcon class="w-4 h-4 text-red-500" />
                <span
                  class="font-nunitoSans font-medium text-[#4b5563] text-base"
                >
                  {{ stats.cancelledAppointments }} hủy
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 3: Khách hàng mới -->
        <div
          class="bg-white border-l-4 border-l-purple-500 border-t border-r border-b border-gray-100 rounded-[14px] p-6"
        >
          <div class="flex items-center justify-between mb-4">
            <p class="font-nunitoSans font-medium text-[#4b5563] text-sm">
              Khách hàng mới
            </p>
            <UsersIcon class="w-6 h-6 text-purple-500" />
          </div>
          <div class="flex flex-col gap-2">
            <p class="font-nunitoSans font-medium text-[#9333ea] text-3xl">
              {{ stats.newCustomers }}
            </p>
            <p class="font-nunitoSans font-medium text-[#4b5563] text-base">
              Khách lần đầu tới khám
            </p>
          </div>
        </div>

        <!-- Card 4: Cảnh báo Kho -->
        <div
          class="bg-white border-l-4 border-l-red-500 border-t border-r border-b border-gray-100 rounded-[14px] p-6"
        >
          <div class="flex items-center justify-between mb-4">
            <p class="font-nunitoSans font-medium text-[#4b5563] text-sm">
              Cảnh báo Kho
            </p>
            <WarningIcon class="w-6 h-6 text-red-500" />
          </div>
          <div class="flex flex-col gap-2">
            <p class="font-nunitoSans font-medium text-[#dc2626] text-3xl">
              {{ stats.stockAlerts }}
            </p>
            <p class="font-nunitoSans font-medium text-[#4b5563] text-base">
              Thuốc sắp hết hạn/hết hàng
            </p>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-[1fr_358px] gap-6 mb-6">
        <!-- Revenue Chart -->
        <div
          class="bg-white border !border-gray-100 shadow-sm rounded-[14px] p-6"
        >
          <div class="flex items-center justify-between mb-8">
            <h3 class="font-nunitoSans font-medium text-black text-lg">
              Doanh thu 7 ngày gần nhất
            </h3>
            <button
              class="bg-[#f3f4f6] border !border-gray-200 rounded-lg px-3 py-2 flex items-center gap-2"
            >
              <span class="font-nunitoSans font-medium text-black text-base">
                7 ngày
              </span>
              <ChevronDownIcon />
            </button>
          </div>

          <!-- Column Chart -->
          <div class="h-[300px] relative">
            <apexchart
              type="bar"
              height="300"
              :options="revenueChartOptions"
              :series="revenueChartSeries"
            ></apexchart>
          </div>

          <div class="flex items-center justify-center gap-2 mt-4">
            <div class="w-3 h-3 rounded-sm bg-[#0d9488]"></div>
            <span class="font-nunitoSans font-medium text-[#0d9488] text-base">
              Doanh thu
            </span>
          </div>
        </div>

        <!-- Revenue Distribution Pie Chart -->
        <div
          class="bg-white border !border-gray-100 shadow-sm rounded-[14px] p-6"
        >
          <h3 class="font-nunitoSans font-medium text-black text-lg mb-6">
            Tỷ trọng doanh thu
          </h3>

          <!-- Pie Chart -->
          <div class="h-[300px] mb-4 relative flex items-center justify-center">
            <apexchart
              type="donut"
              height="300"
              :options="pieChartOptions"
              :series="pieChartSeries"
            ></apexchart>
          </div>

          <!-- Legend -->
          <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-[#0d9488]"></div>
                <span
                  class="font-nunitoSans font-medium text-[#374151] text-base"
                >
                  Lâm sàng
                </span>
              </div>
              <span class="font-nunitoSans font-medium text-black text-base"
                >45%</span
              >
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-[#06b6d4]"></div>
                <span
                  class="font-nunitoSans font-medium text-[#374151] text-base"
                >
                  Spa & Grooming
                </span>
              </div>
              <span class="font-nunitoSans font-medium text-black text-base"
                >30%</span
              >
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-[#a855f7]"></div>
                <span
                  class="font-nunitoSans font-medium text-[#374151] text-base"
                >
                  Nội trú & Điều trị
                </span>
              </div>
              <span class="font-nunitoSans font-medium text-black text-base"
                >15%</span
              >
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-[#fb923c]"></div>
                <span
                  class="font-nunitoSans font-medium text-[#374151] text-base"
                >
                  Khác
                </span>
              </div>
              <span class="font-nunitoSans font-medium text-black text-base"
                >10%</span
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Section: Doctors & Activities -->
      <div class="grid grid-cols-2 gap-6">
        <!-- Bác sĩ đang trực -->
        <div
          class="bg-white border !border-gray-100 shadow-sm rounded-[14px] p-6"
        >
          <div class="flex items-center gap-2 mb-8">
            <h3 class="font-nunitoSans font-medium text-black text-lg">
              Bác sĩ đang trực
            </h3>
          </div>

          <div class="flex flex-col gap-4">
            <div
              v-for="doctor in doctors"
              :key="doctor.id"
              class="bg-[#f3f4f6] rounded-[10px] px-3 py-3 flex items-center justify-between"
            >
              <div class="flex items-center gap-3">
                <div
                  class="bg-[#0d9488] rounded-full w-10 h-10 flex items-center justify-center"
                >
                  <span
                    class="font-nunitoSans font-medium text-white text-base"
                  >
                    {{ doctor.initials }}
                  </span>
                </div>
                <div class="flex flex-col">
                  <p class="font-nunitoSans font-medium text-black text-base">
                    {{ doctor.name }}
                  </p>
                </div>
              </div>
              <div
                class="border !border-gray-200 rounded-lg px-2 py-1"
                :class="doctor.isActive ? 'bg-green-100' : 'bg-gray-200'"
              >
                <span
                  class="font-medium text-sm"
                  :class="doctor.isActive ? 'text-green-600' : 'text-gray-500'"
                >
                  {{ doctor.statusLabel }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Hoạt động gần đây -->
        <div
          class="bg-white border !border-gray-100 shadow-sm rounded-[14px] p-6"
        >
          <h3 class="font-nunitoSans font-medium text-black text-lg mb-8">
            Hoạt động gần đây
          </h3>

          <div class="flex flex-col gap-4">
            <div
              v-for="activity in activities"
              :key="activity.id"
              class="relative pl-5"
            >
              <div
                class="absolute left-0 top-2 w-2 h-2 rounded-full"
                :class="activity.colorClass"
              ></div>
              <div class="flex flex-col">
                <p class="font-nunitoSans font-medium text-black text-base">
                  {{ activity.text }}
                </p>
                <p class="font-nunitoSans font-medium text-[#6b7280] text-base">
                  {{ activity.time }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
// Icon SVG
import WarningIcon from "@/assets/svg/warning.svg";
import DollarIcon from "@/assets/svg/dollar.svg";
import CalendarIcon from "@/assets/svg/calendar.svg";
import UsersIcon from "@/assets/svg/users.svg";
import TickIcon from "@/assets/svg/tick.svg";
import CancelIcon from "@/assets/svg/cancel.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import ArrowUpIcon from "@/assets/svg/arrow-up.svg";

// Icons from Figma
const icons = {
  revenue:
    "https://www.figma.com/api/mcp/asset/04d7756f-ddb8-4eb9-bd34-d589e4d33e19",
  calendar:
    "https://www.figma.com/api/mcp/asset/1ae8c621-ceff-4d3c-a154-dd78148fd745",
  users:
    "https://www.figma.com/api/mcp/asset/4fb86ec6-6a45-4a7c-951a-06a4c898c34d",
  warning:
    "https://www.figma.com/api/mcp/asset/5d8e76f1-fd64-435f-bda0-9ed95f949800",
  arrowUp:
    "https://www.figma.com/api/mcp/asset/59aa0818-ee27-42a8-8f0f-366c8ce84bae",
  check:
    "https://www.figma.com/api/mcp/asset/757c9048-2756-4649-a3fc-f8016f6d65c2",
  cancel:
    "https://www.figma.com/api/mcp/asset/80f4431b-a99a-4517-b775-b3dfff4c5216",
  chevronDown:
    "https://www.figma.com/api/mcp/asset/54afa36e-0deb-4712-9267-114647c779ea",
  chartLegend:
    "https://www.figma.com/api/mcp/asset/1498c99f-1b98-48fe-ab5e-46e668749e8f",
  doctor:
    "https://www.figma.com/api/mcp/asset/9a494e17-cbd6-4c70-8aee-d90c131ba391",
};

// Chart images (these are exported from Figma)
const charts = {
  revenueChart:
    "https://www.figma.com/api/mcp/asset/e5489b73-4bdf-4a02-8845-60764e1ef7e2",
  pieChart:
    "https://www.figma.com/api/mcp/asset/24674e33-3f5f-4ca7-b184-b6f8a51768b3",
};

// Dashboard stats data
const stats = ref({
  todayRevenue: 15500000,
  revenueChange: 12.5,
  todayAppointments: 45,
  completedAppointments: 32,
  cancelledAppointments: 3,
  newCustomers: 12,
  stockAlerts: 5,
});

// Revenue Chart Data (7 days: T2-CN)
const revenueChartSeries = ref([
  {
    name: "Doanh thu",
    data: [2100000, 1850000, 2400000, 2200000, 2800000, 2500000, 1700000], // VND
  },
]);

const revenueChartOptions = ref({
  chart: {
    type: "bar",
    height: 300,
    toolbar: {
      show: false,
    },
    fontFamily: "Nunito Sans, sans-serif",
  },
  plotOptions: {
    bar: {
      borderRadius: 8,
      columnWidth: "60%",
      dataLabels: {
        position: "top",
      },
    },
  },
  dataLabels: {
    enabled: false,
  },
  colors: ["#0d9488"], // Teal color
  xaxis: {
    categories: ["T2", "T3", "T4", "T5", "T6", "T7", "CN"],
    labels: {
      style: {
        colors: "#6b7280",
        fontSize: "14px",
        fontWeight: 500,
      },
    },
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
  },
  yaxis: {
    labels: {
      style: {
        colors: "#6b7280",
        fontSize: "12px",
      },
      formatter: function (value) {
        if (value >= 1000000) {
          return (value / 1000000).toFixed(1) + "M";
        } else if (value >= 1000) {
          return (value / 1000).toFixed(0) + "K";
        }
        return value + "đ";
      },
    },
    min: 0,
    max: 3000000,
    tickAmount: 4,
  },
  grid: {
    borderColor: "#e5e7eb",
    strokeDashArray: 5,
    xaxis: {
      lines: {
        show: false,
      },
    },
    yaxis: {
      lines: {
        show: true,
      },
    },
    padding: {
      top: 0,
      right: 0,
      bottom: 0,
      left: 10,
    },
  },
  tooltip: {
    enabled: true,
    y: {
      formatter: function (value) {
        return formatCurrency(value);
      },
    },
    style: {
      fontSize: "14px",
      fontFamily: "Nunito Sans, sans-serif",
    },
  },
});

// Pie Chart Data (Revenue Distribution)
const pieChartSeries = ref([45, 30, 15, 10]); // Percentages

const pieChartOptions = ref({
  chart: {
    type: "donut",
    fontFamily: "Nunito Sans, sans-serif",
  },
  labels: ["Lâm sàng", "Spa & Grooming", "Nội trú & Điều trị", "Khác"],
  colors: ["#0d9488", "#06b6d4", "#a855f7", "#fb923c"], // Teal, Cyan, Purple, Orange
  dataLabels: {
    enabled: false, // Hide labels on chart
  },
  plotOptions: {
    pie: {
      donut: {
        size: "70%", // Larger donut for better visual
        labels: {
          show: false,
        },
      },
      expandOnClick: false,
    },
  },
  legend: {
    show: false, // We'll use custom legend below
  },
  stroke: {
    show: true,
    width: 3,
    colors: ["#fff"],
  },
  tooltip: {
    enabled: true,
    y: {
      formatter: function (val) {
        return val + "%";
      },
      title: {
        formatter: function (seriesName) {
          return seriesName + ": ";
        },
      },
    },
    style: {
      fontSize: "14px",
      fontFamily: "Nunito Sans, sans-serif",
    },
  },
  states: {
    hover: {
      filter: {
        type: "lighten",
        value: 0.1,
      },
    },
    active: {
      filter: {
        type: "darken",
        value: 0.1,
      },
    },
  },
});

// Doctors data
const doctors = ref([
  {
    id: 1,
    name: "BS. Nguyễn Văn A",
    initials: "NVA",
    status: "Đang khám",
    statusLabel: "Đang khám",
    isActive: true,
  },
  {
    id: 2,
    name: "BS. Trần Thị B",
    initials: "TTB",
    status: "Rảnh",
    statusLabel: "Rảnh",
    isActive: false,
  },
  {
    id: 3,
    name: "Y tá Lê Văn C",
    initials: "LVC",
    status: "Đang khám",
    statusLabel: "Đang khám",
    isActive: true,
  },
]);

// Recent activities data
const activities = ref([
  {
    id: 1,
    text: "Y tá Lê Văn C vừa nhập kho 50 liều Vaccine Rabies",
    time: "5 phút trước",
    colorClass: "bg-[#a855f7]",
  },
  {
    id: 2,
    text: "Lịch hẹn mới: Khách hàng Nguyễn Thị D đặt lịch tắm rửa cho Milo",
    time: "15 phút trước",
    colorClass: "bg-[#3b82f6]",
  },
  {
    id: 3,
    text: "Hoàn thành thanh toán hóa đơn HD001234 - 350.000đ",
    time: "30 phút trước",
    colorClass: "bg-[#22c55e]",
  },
  {
    id: 4,
    text: "BS. Nguyễn Văn A cập nhật hồ sơ bệnh án cho Luna",
    time: "1 giờ trước",
    colorClass: "bg-[#f97316]",
  },
]);

// Helper function to format currency
const formatCurrency = (amount) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(amount);
};
</script>

<style scoped>
/* Custom scrollbar for activities if needed */
</style>
