<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-[1000] pt-24"
  >
    <div
      class="bg-white border !border-gray-300 rounded-[10px] shadow-xl w-[510px] relative"
    >
      <div class="p-6 flex flex-col gap-4">
        <!-- Close Button -->
        <button
          @click="$emit('close')"
          class="absolute right-6 top-4 w-4 h-4 opacity-70 hover:opacity-100 transition-opacity"
        >
          <CloseIcon />
        </button>

        <!-- Header -->
        <div class="flex flex-col gap-2">
          <h2
            class="font-nunito font-semibold text-lg leading-[18px] text-neutral-950 tracking-tight"
          >
            Đặt lại Mật khẩu
          </h2>
          <p
            class="font-nunito text-sm leading-5 text-[#717182] tracking-tight"
          >
            Bạn đang đặt lại mật khẩu cho
            <span class="font-bold">{{ staffName }}</span>
          </p>
        </div>

        <!-- Warning Alert -->
        <div
          class="bg-orange-50 border !border-[#ffd6a7] rounded-[10px] flex items-center gap-2 p-[13px]"
        >
          <WarningIcon class="w-4 h-4 text-[#ca3500]" />
          <p
            class="font-nunito text-sm leading-5 text-[#ca3500] tracking-tight"
          >
            Hành động này sẽ thay đổi mật khẩu của nhân viên
          </p>
        </div>

        <!-- New Password Input -->
        <div class="flex flex-col gap-2">
          <label
            class="font-nunito font-medium text-sm leading-[14px] text-neutral-950 tracking-tight"
          >
            Mật khẩu mới <span class="text-red-500">*</span>
          </label>
          <input
            v-model="newPassword"
            type="password"
            placeholder="Nhập mật khẩu mới"
            class="bg-[#f3f3f5] border !border-transparent rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight placeholder:text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent"
          />
        </div>

        <!-- Require Password Change Checkbox -->
        <div class="flex items-center gap-2">
          <button
            @click="requirePasswordChange = !requirePasswordChange"
            :class="[
              'w-4 h-4 rounded border flex items-center justify-center flex-shrink-0',
              requirePasswordChange
                ? 'bg-[#030213] border-[#030213]'
                : 'bg-white border-gray-300',
            ]"
          >
            <svg
              v-if="requirePasswordChange"
              class="w-3 h-3 text-white"
              viewBox="0 0 12 12"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M10 3L4.5 8.5L2 6"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
          </button>
          <label
            class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight cursor-pointer"
            @click="requirePasswordChange = !requirePasswordChange"
          >
            Yêu cầu đổi mật khẩu trong lần đăng nhập tới
          </label>
        </div>

        <!-- Footer Buttons -->
        <div class="flex items-center justify-end gap-2 mt-2">
          <button
            @click="$emit('close')"
            class="bg-white border !border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 transition-colors"
          >
            <span
              class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >Hủy</span
            >
          </button>
          <button
            @click="handleResetPassword"
            :disabled="!newPassword"
            :class="[
              'rounded-lg h-9 px-4 py-2 transition-colors',
              newPassword
                ? 'bg-[#5a9690] hover:bg-[#5a9690]/80'
                : 'bg-gray-300 cursor-not-allowed',
            ]"
          >
            <span
              class="font-nunito font-medium text-sm leading-5 text-white tracking-tight"
              >Đặt lại mật khẩu</span
            >
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
//Icon SVG
import CloseIcon from "@/assets/svg/close.svg";
import WarningIcon from "@/assets/svg/warning.svg";

// Props
const props = defineProps({
  staffName: {
    type: String,
    default: "BS. Nguyễn Văn A",
  },
});

// Emits
const emit = defineEmits(["close", "reset"]);

// Reactive state
const newPassword = ref("");
const requirePasswordChange = ref(true);

// Icons (from Figma - expire in 7 days)
const iconWarning =
  "https://www.figma.com/api/mcp/asset/3aaf8d24-1adc-40c4-885f-7ea9bd97f6ae";
const iconCheck =
  "https://www.figma.com/api/mcp/asset/0909ccdc-9950-47f2-8bca-c38edc73d629";
const iconClose =
  "https://www.figma.com/api/mcp/asset/63d41565-8975-4a89-9a92-69ca9fd93cb4";

// Methods
const handleResetPassword = () => {
  if (!newPassword.value) return;

  emit("reset", {
    newPassword: newPassword.value,
    requirePasswordChange: requirePasswordChange.value,
  });
};
</script>

<style scoped>
/* No additional styles needed - using Tailwind CSS */
</style>
