```html
<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[1000] p-4"
  >
    <div
      class="bg-white border !border-gray-300 rounded-[10px] shadow-xl w-full max-w-[510px] max-h-[90vh] flex flex-col"
    >
      <!-- Fixed Header -->
      <div class="flex-shrink-0 p-6 pb-4 border-b border-gray-200">
        <div class="flex flex-col gap-2">
          <h2
            class="font-nunitoSans font-semibold text-lg leading-[18px] text-neutral-950 tracking-[-0.4395px]"
          >
            Thông tin Khách hàng
          </h2>
          <p
            class="font-nunitoSans text-sm leading-5 text-[#717182] tracking-[-0.1504px]"
          >
            Lịch sử khám và thông tin chi tiết
          </p>
        </div>
      </div>

      <!-- Scrollable Content -->
      <div class="flex-1 overflow-y-auto px-6 py-4">
        <div class="flex flex-col">
          <!-- Avatar -->
          <div class="flex justify-center mb-4">
            <div
              class="border-4 border-[#cbfbf1] rounded-full w-20 h-20 overflow-hidden"
            >
              <img
                :src="customer.avatar"
                alt="Avatar"
                class="w-full h-full object-cover"
              />
            </div>
          </div>

          <!-- Customer Information Section -->
          <div
            class="bg-gray-50 rounded-[10px] p-4 mb-4 grid grid-cols-2 gap-y-[16px] gap-x-[16px]"
          >
            <!-- Name -->
            <div class="flex flex-col gap-1">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Họ tên</span
              >
              <span
                class="font-nunitoSans text-base leading-6 text-[#101828] tracking-[-0.3125px]"
                >{{ customer.name }}</span
              >
            </div>

            <!-- Phone -->
            <div class="flex flex-col gap-1">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Số điện thoại</span
              >
              <span
                class="font-nunitoSans text-base leading-6 text-[#101828] tracking-[-0.3125px]"
                >{{ customer.phone }}</span
              >
            </div>

            <!-- Email -->
            <div class="flex flex-col gap-1">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Email</span
              >
              <span
                class="font-nunitoSans text-base leading-6 text-[#101828] tracking-[-0.3125px]"
                >{{ customer.email }}</span
              >
            </div>

            <!-- Rank -->
            <div class="flex flex-col gap-1">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Xếp hạng</span
              >
              <span
                :class="[
                  'inline-flex items-center px-2 py-[3px] rounded-lg text-xs leading-4 font-nunitoSans font-medium w-fit',
                  customer.rank === 'Gold'
                    ? 'bg-[#fef3c6] text-[#bb4d00]'
                    : customer.rank === 'Silver'
                    ? 'bg-gray-100 text-gray-700'
                    : 'bg-orange-100 text-orange-700',
                ]"
              >
                {{ customer.rankIcon }} {{ customer.rank }}
              </span>
            </div>

            <!-- Address (full width) -->
            <div class="flex flex-col gap-1 col-span-2">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Địa chỉ</span
              >
              <span
                class="font-nunitoSans text-base leading-6 text-[#101828] tracking-[-0.3125px]"
                >{{ customer.address }}</span
              >
            </div>

            <!-- Total Spending -->
            <div class="flex flex-col gap-1">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Tổng chi tiêu</span
              >
              <span
                class="font-nunitoSans text-base leading-6 text-[#009689] tracking-[-0.3125px]"
                >{{ formatCurrency(customer.totalSpent) }}</span
              >
            </div>

            <!-- Join Date -->
            <div class="flex flex-col gap-1">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Ngày tham gia</span
              >
              <span
                class="font-nunitoSans text-base leading-6 text-[#101828] tracking-[-0.3125px]"
                >{{ customer.joinDate }}</span
              >
            </div>

            <!-- Password -->
            <div class="flex flex-col gap-1">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Mật khẩu</span
              >
              <div class="flex items-center gap-2">
                <span class="font-nunitoSans text-sm leading-5 text-[#101828]"
                  >••••••••</span
                >
                <button
                  class="w-6 h-6 flex items-center justify-center hover:bg-gray-100 rounded transition-colors"
                >
                  <EyeIcon class="w-4 h-4" />
                </button>
              </div>
            </div>

            <!-- Status -->
            <div class="flex flex-col gap-1">
              <span
                class="font-nunitoSans text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >Trạng thái</span
              >
              <span
                :class="[
                  'inline-flex items-center gap-2 px-2 py-[3px] rounded-lg text-xs leading-4 font-nunitoSans font-medium w-fit',
                  customer.status === 'active'
                    ? 'bg-green-100 text-[#008236]'
                    : 'bg-gray-100 text-[#364153]',
                ]"
              >
                {{ customer.status === "active" ? "Hoạt động" : "Đã khóa" }}
              </span>
            </div>
          </div>

          <!-- Pets Section -->
          <div class="flex flex-col gap-2 mb-4">
            <label
              class="font-nunitoSans font-medium text-sm leading-[14px] text-neutral-950 tracking-[-0.1504px]"
            >
              Thú cưng
            </label>
            <div
              class="border !border-gray-300 rounded-[10px] h-[91px] relative"
            >
              <div
                class="border-b !border-gray-300 pt-[12px] pb-px px-3 h-[45px]"
              >
                <p
                  class="font-nunitoSans text-sm leading-5 text-[#101828] tracking-[-0.1504px]"
                >
                  {{ customer.pets[0] }}
                </p>
              </div>
              <div class="px-[13px] pt-[13px]">
                <p
                  class="font-nunitoSans text-sm leading-5 text-[#101828] tracking-[-0.1504px]"
                >
                  {{ customer.pets[1] }}
                </p>
              </div>
            </div>
          </div>

          <!-- Recent Visit History Section -->
          <div class="flex flex-col gap-2">
            <label
              class="font-nunitoSans font-medium text-sm leading-[14px] text-neutral-950 tracking-[-0.1504px]"
            >
              Lịch sử khám gần đây
            </label>
            <div
              class="border !border-gray-300 rounded-[10px] pt-[13px] pb-px px-[13px] h-[62px]"
            >
              <div class="flex items-center justify-between h-9">
                <div class="flex flex-col h-9">
                  <p
                    class="font-nunitoSans text-sm leading-5 text-[#101828] tracking-[-0.1504px]"
                  >
                    {{ customer.recentVisits[0].service }}
                  </p>
                  <p class="font-nunitoSans text-xs leading-4 text-[#6a7282]">
                    {{ customer.recentVisits[0].date }} -
                    {{ customer.recentVisits[0].doctor }}
                  </p>
                </div>
                <p
                  class="font-nunitoSans text-base leading-6 text-[#009689] tracking-[-0.3125px]"
                >
                  {{ formatCurrency(customer.recentVisits[0].cost) }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Fixed Footer -->
      <div class="flex-shrink-0 p-6 pt-4 border-t border-gray-200">
        <div class="flex justify-end">
          <button
            @click="$emit('close')"
            class="bg-white border !border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 transition-colors"
          >
            <span
              class="font-nunitoSans font-medium text-sm leading-5 text-neutral-950 tracking-[-0.1504px]"
              >Đóng</span
            >
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
//Icon SVG
import EyeIcon from "@/assets/svg/eye.svg";

// Props
const props = defineProps({
  customer: {
    type: Object,
    default: () => ({
      avatar:
        "https://www.figma.com/api/mcp/asset/69ab4ed0-d5a9-4482-8a8e-baeaf0347487",
      name: "Trần Thị Hương",
      phone: "0912345678",
      email: "huong.tran@email.com",
      rank: "Gold",
      rankIcon: "🥇",
      address: "123 Lê Lợi, Quận 1, TP.HCM",
      totalSpent: 5200000,
      joinDate: "2024-11-20",
      status: "active",
      pets: ["Milo", "Luna"],
      recentVisits: [
        {
          service: "Khám tổng quát",
          date: "15/11/2024",
          doctor: "BS. Nguyễn Văn A",
          cost: 500000,
        },
      ],
    }),
  },
});

// Emits
const emit = defineEmits(["close"]);

// Icons (from Figma - expire in 7 days)
const iconEye =
  "https://www.figma.com/api/mcp/asset/a5d78160-8824-4f43-94ef-f0a7bc907df0";
const iconCheck =
  "https://www.figma.com/api/mcp/asset/017923aa-6e03-4882-81a0-94827e125b5b";
const iconX =
  "https://www.figma.com/api/mcp/asset/1d86c67f-830e-4ef0-a6fe-45849993acf4";

// Methods
const formatCurrency = (amount) => {
  return amount.toLocaleString("vi-VN") + " ₫";
};
</script>

<style scoped>
/* Custom scrollbar for modal */
div::-webkit-scrollbar {
  width: 8px;
}

div::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

div::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

div::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
