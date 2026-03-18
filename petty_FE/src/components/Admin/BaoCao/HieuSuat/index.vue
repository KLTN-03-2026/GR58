<template>
  <div class="w-full min-h-screen px-8 py-6 flex flex-col gap-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-2">
      <h1 class="text-2xl font-semibold text-black">Báo cáo Hiệu suất</h1>
      <p class="text-gray-500 font-medium text-base">
        Đánh giá hiệu quả làm việc của nhân sự và dịch vụ
      </p>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col gap-6">
      <!-- Tabs -->
      <!-- <div
        class="bg-gray-200 rounded-[14px] h-[36px] w-[337.852px] flex items-center justify-center p-[3.5px]"
      >
        <button
          :class="[
            'flex-1 h-[29px] rounded-[14px] flex items-center justify-center gap-[8px] transition-colors',
            activeTab === 'staff'
              ? 'bg-white border border-transparent'
              : 'border border-transparent',
          ]"
          @click="activeTab = 'staff'"
        >
          <img :src="iconStaff" alt="" class="w-[16px] h-[16px]" />
          <span
            class="font-medium text-[14px] leading-[20px] tracking-[-0.1504px] text-neutral-950"
          >
            Hiệu suất Nhân sự
          </span>
        </button>
        <button
          :class="[
            'flex-1 h-[29px] rounded-[14px] flex items-center justify-center gap-[8px] transition-colors',
            activeTab === 'service'
              ? 'bg-white border border-transparent'
              : 'border border-transparent',
          ]"
          @click="activeTab = 'service'"
        >
          <img :src="iconService" alt="" class="w-[16px] h-[16px]" />
          <span
            class="font-medium text-[14px] leading-[20px] tracking-[-0.1504px] text-neutral-950"
          >
            Dịch vụ & Vận hành
          </span>
        </button>
      </div> -->

      <!-- Tab Content -->
      <div v-if="activeTab === 'staff'" class="flex flex-col gap-[24px]">
        <!-- Filter Card -->
        <div
          class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
        >
          <div class="flex items-center justify-between w-full">
            <button
              class="flex items-center justify-between px-[13px] py-px h-[36px] w-[423.18px] bg-gray-100 rounded-[8px]"
            >
              <span
                class="text-[14px] leading-[20px] tracking-[-0.1504px] text-neutral-950"
              >
                Tháng này
              </span>
              <ChevronDownIcon class="w-4 h-4" />
            </button>

            <button
              class="flex items-center justify-between px-[13px] py-px h-[36px] w-[423.18px] bg-gray-100 rounded-[8px]"
            >
              <span
                class="text-[14px] leading-[20px] tracking-[-0.1504px] text-neutral-950"
              >
                Tất cả
              </span>
              <ChevronDownIcon class="w-4 h-4" />
            </button>

            <button
              class="flex items-center justify-center gap-[8px] h-[36px] w-[192.641px] bg-[#5a9690] rounded-[8px]"
            >
              <DownloadIcon class="w-4 h-4 text-white" />
              <span
                class="font-medium text-[14px] leading-[20px] tracking-[-0.1504px] text-white"
              >
                Xuất bảng tính lương
              </span>
            </button>
          </div>
        </div>

        <!-- Top 3 Card -->
        <div
          class="bg-white border !border-gray-300 shadow-sm rounded-[14px] h-[400.5px] relative"
        >
          <div class="p-[24px] pb-0">
            <!-- <div class="flex items-center gap-[8px] mb-[6px]">
              <img :src="iconTrophy" alt="" class="w-[20px] h-[20px]" />
              <h2
                class="text-[16px] leading-[16px] tracking-[-0.3125px] text-neutral-950"
              >
                Bảng Vàng - Top 3 Xuất sắc nhất
              </h2>
            </div> -->
            <h2
              class="text-[16px] leading-[16px] tracking-[-0.3125px] text-neutral-950 mb-[6px]"
            >
              Bảng Vàng - Top 3 Xuất sắc nhất
            </h2>
            <p
              class="text-[14px] leading-[20px] tracking-[-0.1504px] text-[#4a5565]"
            >
              Xếp hạng dựa trên tổng doanh thu mang về trong kỳ
            </p>
          </div>

          <div
            class="absolute left-[25px] top-[95px] w-[1071px] h-[280.5px] flex items-end justify-center gap-[32px]"
          >
            <!-- Second Place -->
            <div
              class="flex flex-col items-center gap-[8px] w-[192px] h-[240.5px]"
            >
              <div
                class="text-[36px] leading-[40px] tracking-[0.3691px] h-[40px]"
              >
                🥈
              </div>
              <div class="flex-1 bg-gray-100 rounded-[10px] w-full relative">
                <div
                  class="absolute left-1/2 -translate-x-1/2 -top-[20px] w-[64px] h-[64px] border-4 border-gray-300 rounded-full overflow-hidden"
                >
                  <img
                    :src="staffImages[1]"
                    alt=""
                    class="w-full h-full object-cover"
                  />
                </div>
                <p
                  class="absolute left-1/2 -translate-x-1/2 top-[56px] text-[16px] leading-[24px] tracking-[-0.3125px] text-[#101828] text-center whitespace-nowrap"
                >
                  BS. Trần Thị B
                </p>
                <p
                  class="absolute left-1/2 -translate-x-1/2 top-[84px] text-[12px] leading-[16px] text-[#4a5565]"
                >
                  Bác sĩ
                </p>
                <p
                  class="absolute left-1/2 -translate-x-1/2 top-[108px] text-[18px] leading-[28px] tracking-[-0.4395px] text-[#009689]"
                >
                  132.0M
                </p>
              </div>
              <div
                class="bg-gray-100 border border-gray-300 rounded-[8px] h-[22px] px-[8px] flex items-center justify-center"
              >
                <span
                  class="font-medium text-[12px] leading-[16px] text-[#364153]"
                  >#2</span
                >
              </div>
            </div>

            <!-- First Place -->
            <div
              class="flex flex-col items-center gap-[8px] w-[208px] h-[280.5px]"
            >
              <div
                class="text-[48px] leading-[48px] tracking-[0.3516px] h-[48px]"
              >
                🥇
              </div>
              <div
                class="flex-1 border-2 border-[#ffb900] rounded-[10px] w-full relative"
              >
                <div
                  class="absolute left-1/2 -translate-x-1/2 top-[17.77px] w-[80px] h-[56.234px] border-4 border-[#ffb900] rounded-full overflow-hidden"
                >
                  <img
                    :src="staffImages[0]"
                    alt=""
                    class="w-full h-full object-cover"
                  />
                </div>
                <p
                  class="absolute left-1/2 -translate-x-1/2 top-[86px] text-[16px] leading-[24px] tracking-[-0.3125px] text-[#101828] text-center whitespace-nowrap"
                >
                  BS. Nguyễn Văn A
                </p>
                <p
                  class="absolute left-1/2 -translate-x-1/2 top-[114px] text-[12px] leading-[16px] text-[#4a5565]"
                >
                  Bác sĩ Trưởng khoa
                </p>
                <p
                  class="absolute left-1/2 -translate-x-1/2 top-[138px] text-[20px] leading-[28px] tracking-[-0.4492px] text-[#e17100]"
                >
                  150.0M
                </p>
              </div>
              <div
                class="bg-[#fe9a00] rounded-[8px] h-[22px] px-[12px] flex items-center justify-center"
              >
                <span class="font-medium text-[12px] leading-[16px] text-white"
                  >👑 Quán quân</span
                >
              </div>
            </div>

            <!-- Third Place -->
            <div
              class="flex flex-col items-center gap-[8px] w-[192px] h-[240.5px]"
            >
              <div
                class="text-[36px] leading-[40px] tracking-[0.3691px] h-[40px]"
              >
                🥉
              </div>
              <div class="flex-1 bg-gray-100 rounded-[10px] w-full relative">
                <div
                  class="absolute left-1/2 -translate-x-1/2 -top-[20px] w-[64px] h-[64px] border-4 border-[#ffb86a] rounded-full overflow-hidden"
                >
                  <img
                    :src="staffImages[2]"
                    alt=""
                    class="w-full h-full object-cover"
                  />
                </div>
                <p
                  class="absolute left-1/2 -translate-x-1/2 top-[56px] text-[16px] leading-[24px] tracking-[-0.3125px] text-[#101828] text-center whitespace-nowrap"
                >
                  Y tá Lê Văn C
                </p>
                <p
                  class="absolute left-1/2 -translate-x-1/2 top-[84px] text-[12px] leading-[16px] text-[#4a5565]"
                >
                  Y tá
                </p>
                <p
                  class="absolute left-1/2 -translate-x-1/2 top-[108px] text-[18px] leading-[28px] tracking-[-0.4395px] text-[#009689]"
                >
                  95.0M
                </p>
              </div>
              <div
                class="bg-[#ffedd4] border border-gray-300 rounded-[8px] h-[22px] px-[8px] flex items-center justify-center"
              >
                <span
                  class="font-medium text-[12px] leading-[16px] text-[#ca3500]"
                  >#3</span
                >
              </div>
            </div>
          </div>
        </div>

        <!-- Detail Table Card -->
        <div
          class="bg-white border !border-gray-300 shadow-sm rounded-[14px] flex flex-col gap-[24px] h-[620px]"
        >
          <div class="p-[24px] pb-0">
            <h2
              class="text-[16px] leading-[16px] tracking-[-0.3125px] text-neutral-950 mb-[6px]"
            >
              Chi tiết Hiệu suất Nhân viên
            </h2>
            <p
              class="text-[14px] leading-[20px] tracking-[-0.1504px] text-[#4a5565]"
            >
              Bảng dữ liệu đầy đủ để tính lương thưởng (KPI) và đánh giá năng
              lực
            </p>
          </div>

          <div class="px-[24px] flex flex-col gap-[24px]">
            <div class="overflow-y-auto h-[381.5px]">
              <table class="w-full">
                <thead>
                  <tr class="border-b border-gray-300">
                    <th
                      class="text-left px-[8px] py-[10px] font-medium text-[14px] leading-[20px] tracking-[-0.1504px] text-neutral-950"
                    >
                      Nhân viên
                    </th>
                    <th
                      class="text-right px-[8px] py-[10px] font-medium text-[14px] leading-[20px] tracking-[-0.1504px] text-neutral-950"
                    >
                      Số ca phục vụ
                    </th>
                    <th
                      class="text-right px-[8px] py-[10px] font-medium text-[14px] leading-[20px] tracking-[-0.1504px] text-neutral-950"
                    >
                      Tổng Doanh thu
                    </th>
                    <th
                      class="text-right px-[8px] py-[10px] font-medium text-[14px] leading-[20px] tracking-[-0.1504px] text-neutral-950"
                    >
                      Trung bình/Ca
                    </th>
                    <th
                      class="text-right px-[8px] py-[10px] font-medium text-[14px] leading-[20px] tracking-[-0.1504px] text-neutral-950"
                    >
                      Số giờ làm
                    </th>
                    <th
                      class="text-center px-[8px] py-[10px] font-medium text-[14px] leading-[20px] tracking-[-0.1504px] text-neutral-950"
                    >
                      Đánh giá
                    </th>
                    <th
                      class="text-right px-[8px] py-[10px] font-medium text-[14px] leading-[20px] tracking-[-0.1504px] text-neutral-950"
                    >
                      Thao tác
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="staff in staffData"
                    :key="staff.id"
                    class="border-b border-gray-300"
                  >
                    <td class="px-[8px] py-[8.5px]">
                      <div class="flex items-center gap-[12px]">
                        <div
                          class="w-[40px] h-[40px] rounded-full overflow-hidden"
                        >
                          <img
                            :src="staff.avatar"
                            alt=""
                            class="w-full h-full object-cover"
                          />
                        </div>
                        <div class="flex flex-col">
                          <p
                            class="text-[14px] leading-[20px] tracking-[-0.1504px] text-[#101828]"
                          >
                            {{ staff.name }}
                          </p>
                          <p class="text-[12px] leading-[16px] text-[#6a7282]">
                            {{ staff.position }}
                          </p>
                        </div>
                      </div>
                    </td>
                    <td class="px-[8px] py-[12px] text-right">
                      <p
                        class="text-[14px] leading-[20px] tracking-[-0.1504px] text-[#101828]"
                      >
                        {{ staff.shifts }} ca
                      </p>
                      <p class="text-[12px] leading-[16px] text-[#6a7282]">
                        Volume
                      </p>
                    </td>
                    <td class="px-[8px] py-[12px] text-right">
                      <p
                        class="text-[14px] leading-[20px] tracking-[-0.1504px] text-green-600"
                      >
                        {{ staff.revenue }}
                      </p>
                      <p class="text-[12px] leading-[16px] text-[#6a7282]">
                        Cơ sở hoa hồng
                      </p>
                    </td>
                    <td class="px-[8px] py-[12px] text-right">
                      <p
                        class="text-[14px] leading-[20px] tracking-[-0.1504px] text-[#009689]"
                      >
                        {{ staff.avgPerShift }}
                      </p>
                      <p class="text-[12px] leading-[16px] text-[#6a7282]">
                        Khả năng upsell
                      </p>
                    </td>
                    <td class="px-[8px] py-[10.5px] text-right">
                      <!-- <div class="flex items-center justify-end gap-[4px]">
                        <img
                          :src="iconClock"
                          alt=""
                          class="w-[16px] h-[16px]"
                        />
                        <p
                          class="text-[14px] leading-[20px] tracking-[-0.1504px] text-[#101828]"
                        >
                          {{ staff.hours }}h
                        </p>
                      </div> -->
                      <p
                        class="text-[14px] leading-[20px] tracking-[-0.1504px] text-[#101828]"
                      >
                        {{ staff.hours }}h
                      </p>
                      <p class="text-[12px] leading-[16px] text-[#6a7282]">
                        Attendance
                      </p>
                    </td>
                    <td class="px-[8px] py-[10.5px]">
                      <!-- <div class="flex items-center justify-center gap-[4px]">
                        <img :src="iconStar" alt="" class="w-[16px] h-[16px]" />
                        <span
                          class="text-[14px] leading-[20px] tracking-[-0.1504px] text-[#101828]"
                        >
                          {{ staff.rating }}
                        </span>
                        <span
                          class="text-[14px] leading-[20px] tracking-[-0.1504px] text-[#6a7282]"
                        >
                          /5.0
                        </span>
                      </div> -->
                      <p
                        class="text-[14px] leading-[20px] tracking-[-0.1504px] text-[#101828] text-center"
                      >
                        {{ staff.rating }}/5.0
                      </p>
                      <p
                        class="text-[12px] leading-[16px] text-[#6a7282] text-center"
                      >
                        Customer feedback
                      </p>
                    </td>
                    <td class="px-[8px] py-[12.5px] text-right">
                      <!-- <button
                        class="h-[32px] px-[12px] rounded-[8px] flex items-center gap-[8px] hover:bg-gray-100"
                      >
                        <img :src="iconEye" alt="" class="w-[16px] h-[16px]" />
                        <span
                          class="font-medium text-[14px] leading-[20px] tracking-[-0.1504px] text-neutral-950"
                        >
                          Xem chi tiết
                        </span>
                      </button> -->
                      <span
                        class="font-medium text-[14px] leading-[20px] tracking-[-0.1504px] text-neutral-950"
                      >
                        Xem chi tiết
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div
              class="bg-blue-50 border !border-[#bedbff] rounded-[10px] p-[17px]"
            >
              <div class="flex gap-[8px]">
                <!-- <img
                  :src="iconInfo"
                  alt=""
                  class="w-[20px] h-[20px] mt-[2px]"
                /> -->
                <div class="flex flex-col gap-[4px]">
                  <p
                    class="text-[14px] leading-[20px] tracking-[-0.1504px] text-[#1c398e]"
                  >
                    Logic tính Doanh thu Nhân viên
                  </p>
                  <p class="text-[12px] leading-[16px] text-[#193cb8]">
                    Trong mỗi hóa đơn, từng dòng dịch vụ có trường
                    <span
                      class="bg-blue-100 px-[4px] py-[2px] rounded-[4px] text-[#193cb8]"
                      >assigned_to</span
                    >
                    (Người thực hiện). Hệ thống cộng tổng tiền của các dòng được
                    gắn tên nhân viên đó.
                  </p>
                  <p class="text-[12px] leading-[16px] text-[#1447e6]">
                    VD: Hóa đơn 500k → Tiêm phòng (100k) gắn Y tá B | Khám bệnh
                    (200k) gắn BS A | Thuốc (200k) tính chung
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

// Icon SVG
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import DownloadIcon from "@/assets/svg/download.svg";

// Active tab state
const activeTab = ref("staff");

// Icon URLs from Figma
const iconStaff =
  "https://www.figma.com/api/mcp/asset/3bee3200-9445-44e8-b322-96e63babfe0a";
const iconService =
  "https://www.figma.com/api/mcp/asset/7de0ede5-6651-4da3-8d58-59b0d7a96b50";
const iconChevron =
  "https://www.figma.com/api/mcp/asset/5f813f2c-3637-4cd5-8115-518b0ed58d31";
const iconDownload =
  "https://www.figma.com/api/mcp/asset/81237080-3497-4e88-9ca8-bb5147f52528";
const iconTrophy =
  "https://www.figma.com/api/mcp/asset/4eaaa2f6-5ebf-41cf-8cec-204e207b523c";
const iconClock =
  "https://www.figma.com/api/mcp/asset/8c15b74b-a192-42a2-a3c4-ccad385f0cc4";
const iconStar =
  "https://www.figma.com/api/mcp/asset/404b356b-10fc-4496-a4a1-ad2547c80c3f";
const iconEye =
  "https://www.figma.com/api/mcp/asset/4c954846-fefd-432d-b146-cb0e2d3614ec";
const iconInfo =
  "https://www.figma.com/api/mcp/asset/53bdc53e-7b81-49da-9bc5-ba9df9b0922c";

// Staff images
const staffImages = [
  "https://www.figma.com/api/mcp/asset/6515534a-a05c-43a3-92c7-023d99d4eba7",
  "https://www.figma.com/api/mcp/asset/25655962-5529-4d68-b3e0-34e610c42124",
  "https://www.figma.com/api/mcp/asset/70431c94-1ac5-4b4a-a0f9-edce60dd1a2d",
];

// Staff data for table
const staffData = ref([
  {
    id: 1,
    name: "BS. Nguyễn Văn A",
    position: "Bác sĩ Trưởng khoa",
    avatar:
      "https://www.figma.com/api/mcp/asset/6515534a-a05c-43a3-92c7-023d99d4eba7",
    shifts: "120",
    revenue: "150.0M",
    avgPerShift: "1.3M",
    hours: "160",
    rating: "4.8",
  },
  {
    id: 2,
    name: "BS. Trần Thị B",
    position: "Bác sĩ",
    avatar:
      "https://www.figma.com/api/mcp/asset/25655962-5529-4d68-b3e0-34e610c42124",
    shifts: "110",
    revenue: "132.0M",
    avgPerShift: "1.2M",
    hours: "155",
    rating: "4.7",
  },
  {
    id: 3,
    name: "Y tá Lê Văn C",
    position: "Y tá",
    avatar:
      "https://www.figma.com/api/mcp/asset/70431c94-1ac5-4b4a-a0f9-edce60dd1a2d",
    shifts: "95",
    revenue: "95.0M",
    avgPerShift: "1.0M",
    hours: "148",
    rating: "4.9",
  },
  {
    id: 4,
    name: "Chuyên viên Phạm D",
    position: "Chuyên viên Spa",
    avatar:
      "https://www.figma.com/api/mcp/asset/398ffe5d-6736-43f8-bebf-2ea9e5d34f22",
    shifts: "88",
    revenue: "88.0M",
    avgPerShift: "1.0M",
    hours: "140",
    rating: "4.6",
  },
  {
    id: 5,
    name: "NV. Hoàng E",
    position: "Nhân viên Petshop",
    avatar:
      "https://www.figma.com/api/mcp/asset/a7c18fef-5ce3-4c29-961a-3b8a3c3686a6",
    shifts: "75",
    revenue: "65.0M",
    avgPerShift: "867K",
    hours: "130",
    rating: "4.5",
  },
  {
    id: 6,
    name: "Y tá Võ F",
    position: "Y tá",
    avatar:
      "https://www.figma.com/api/mcp/asset/12041ec8-31e9-4b46-a838-61782d2ae786",
    shifts: "82",
    revenue: "78.0M",
    avgPerShift: "951K",
    hours: "135",
    rating: "4.7",
  },
]);
</script>

<style scoped>
/* Custom scrollbar for table */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
