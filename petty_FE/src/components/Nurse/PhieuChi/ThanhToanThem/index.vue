<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-[9999] pt-24 font-nunito"
  >
    <div
      class="bg-white border !border-gray-300 rounded-[10px] shadow-xl w-full max-w-[510px] relative"
    >
      <!-- Close Button -->
      <button
        @click="handleClose"
        class="absolute right-4 top-4 opacity-70 hover:opacity-100 transition-opacity"
      >
        <CloseIcon class="text-black"></CloseIcon>
      </button>

      <div class="p-6 flex flex-col gap-4">
        <!-- Header -->
        <div class="flex flex-col gap-2">
          <h2 class="text-lg font-semibold text-neutral-950">
            Thanh toán thêm - {{ expenseCode }}
          </h2>
          <p class="text-sm text-[#717182] leading-5">
            Trả nợ dần cho {{ supplierName }}
          </p>
        </div>

        <!-- Form Content -->
        <div class="flex flex-col gap-4">
          <!-- Debt Info Card -->
          <div
            class="bg-orange-50 border !border-[#ffd6a7] rounded-[10px] p-4 flex flex-col gap-2"
          >
            <p class="text-sm text-[#4a5565] leading-5">Thông tin công nợ:</p>
            <div class="flex flex-col gap-1">
              <p class="text-sm text-neutral-950 leading-5">
                Tổng giá trị:
                <span class="font-bold">{{ formatCurrency(totalAmount) }}</span>
              </p>
              <p class="text-sm text-neutral-950 leading-5">
                Đã trả:
                <span class="font-bold text-[#00a63e]">{{
                  formatCurrency(paidAmount)
                }}</span>
              </p>
              <p class="text-sm text-neutral-950 leading-5">
                Còn nợ:
                <span class="font-bold text-[#e7000b]">{{
                  formatCurrency(remainingAmount)
                }}</span>
              </p>
            </div>
          </div>

          <!-- Số tiền thanh toán -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-neutral-950"
              >Số tiền thanh toán</label
            >
            <input
              type="text"
              v-model="formData.amount"
              :placeholder="`Tối đa: ${formatCurrency(remainingAmount)}`"
              @input="formatNumberInput('amount', $event)"
              class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 h-9 text-sm text-neutral-950 placeholder:text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689]"
            />
            <p v-if="amountError" class="text-xs text-red-500">
              {{ amountError }}
            </p>
          </div>

          <!-- Hình thức thanh toán -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-neutral-950"
              >Hình thức thanh toán</label
            >
            <div class="relative payment-method-dropdown">
              <button
                @click.stop="togglePaymentMethodDropdown"
                type="button"
                class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 flex items-center justify-between h-9 text-left"
              >
                <span class="text-sm text-neutral-950">
                  {{ formData.paymentMethodLabel }}
                </span>
                <ChevronDownIcon />
              </button>

              <!-- Dropdown menu -->
              <div
                v-if="showPaymentMethodDropdown"
                class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
              >
                <button
                  v-for="method in paymentMethods"
                  :key="method.value"
                  @click.stop="selectPaymentMethod(method)"
                  type="button"
                  class="w-full text-left px-3 py-2 hover:bg-gray-100 text-sm text-neutral-950 transition-colors"
                  :class="{
                    'bg-[#e6f7f5]': formData.paymentMethod === method.value,
                  }"
                >
                  {{ method.label }}
                </button>
              </div>
            </div>
          </div>

          <!-- Chi tiết tiền mặt và chuyển khoản (nếu chọn Cả hai) -->
          <div
            v-if="formData.paymentMethod === 'both'"
            class="flex flex-col gap-2"
          >
            <div class="flex gap-2">
              <div class="flex-1 flex flex-col gap-2">
                <label class="text-sm font-medium text-neutral-950">
                  Tiền mặt</label
                >
                <input
                  type="text"
                  v-model="formData.cashAmount"
                  placeholder="VD: 2000000"
                  @input="formatNumberInput('cashAmount', $event)"
                  class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 h-9 text-sm text-neutral-950 placeholder:text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689]"
                />
              </div>
              <div class="flex-1 flex flex-col gap-2">
                <label class="text-sm font-medium text-neutral-950"
                  >Chuyển khoản</label
                >
                <input
                  type="text"
                  v-model="formData.transferAmount"
                  placeholder="VD: 3000000"
                  @input="formatNumberInput('transferAmount', $event)"
                  class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 h-9 text-sm text-neutral-950 placeholder:text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689]"
                />
              </div>
            </div>
            <p v-if="bothPaymentError" class="text-xs text-red-500">
              {{ bothPaymentError }}
            </p>
          </div>

          <!-- Ghi chú -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-neutral-950"
              >Ghi chú (Optional)</label
            >
            <input
              type="text"
              v-model="formData.note"
              placeholder="VD: Trả đợt 2, Thanh toán nốt..."
              class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 h-9 text-sm text-neutral-950 placeholder:text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689]"
            />
          </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-2 pt-2">
          <button
            @click="handleClose"
            :disabled="isSubmitting"
            class="bg-white border !border-gray-300 rounded-lg px-4 py-2 h-9 text-sm font-medium text-neutral-950 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Hủy
          </button>
          <button
            @click="handleSubmit"
            :disabled="!isFormValid || isSubmitting"
            :class="[
              'bg-[#f54900] rounded-lg px-4 py-2 h-9 flex items-center gap-2 text-sm font-medium text-white transition-colors',
              isFormValid && !isSubmitting
                ? 'hover:bg-[#d43f00] cursor-pointer'
                : 'opacity-50 cursor-not-allowed',
            ]"
          >
            <svg
              v-if="isSubmitting"
              class="animate-spin h-4 w-4 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            <span>{{
              isSubmitting ? "Đang xử lý..." : "Xác nhận thanh toán"
            }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { updatePhieuChi, getPhieuChiById } from "@/utils/phieuChi";
import { showErrorToast, showSuccessToast } from "@/utils/toast";
//Icon SVG
import CloseIcon from "@/assets/svg/close.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";

// Props
const props = defineProps({
  expenseId: {
    type: Number,
    required: true,
  },
  expenseCode: {
    type: String,
    required: true,
    default: "PC0056",
  },
  supplierName: {
    type: String,
    required: true,
    default: "Công ty CP Dược Thú Y Việt Nam",
  },
  totalAmount: {
    type: Number,
    required: true,
    default: 15000000,
  },
  paidAmount: {
    type: Number,
    required: true,
    default: 5000000,
  },
  remainingAmount: {
    type: Number,
    required: true,
    default: 10000000,
  },
});

// Define emits
const emit = defineEmits(["close", "submit"]);

// Form data
const formData = ref({
  amount: "",
  paymentMethod: "cash", // 'cash', 'transfer', 'both'
  paymentMethodLabel: "Tiền mặt",
  cashAmount: "",
  transferAmount: "",
  note: "",
});

// Loading state
const isSubmitting = ref(false);

// Phiếu chi details
const phieuChiDetails = ref(null);

// Dropdown state
const showPaymentMethodDropdown = ref(false);

// Payment method options
const paymentMethods = [
  { value: "cash", label: "Tiền mặt" },
  { value: "transfer", label: "Chuyển khoản" },
];

// Load phiếu chi details
const loadPhieuChiDetails = async () => {
  try {
    const response = await getPhieuChiById(props.expenseId);
    if (response && response.status) {
      phieuChiDetails.value = response.data;
      console.log("Đã tải chi tiết phiếu chi:", response.data);
    }
  } catch (error) {
    console.error(" Lỗi tải chi tiết phiếu chi:", error);
    showErrorToast("Không thể tải thông tin phiếu chi");
  }
};

// Load when modal opens
onMounted(() => {
  loadPhieuChiDetails();
});

// Reset form when modal closes
const resetForm = () => {
  formData.value = {
    amount: "",
    paymentMethod: "cash",
    paymentMethodLabel: "💵 Tiền mặt",
    cashAmount: "",
    transferAmount: "",
    note: "",
  };
  showPaymentMethodDropdown.value = false;
};

// Watch for modal close
const handleClose = () => {
  resetForm();
  emit("close");
};

// Toggle dropdown
const togglePaymentMethodDropdown = (event) => {
  event.stopPropagation();
  showPaymentMethodDropdown.value = !showPaymentMethodDropdown.value;
};

// Select payment method
const selectPaymentMethod = (method) => {
  formData.value.paymentMethod = method.value;
  formData.value.paymentMethodLabel = method.label;
  showPaymentMethodDropdown.value = false;

  // Reset amounts based on method
  if (method.value === "cash") {
    formData.value.cashAmount = formData.value.amount;
    formData.value.transferAmount = "";
  } else if (method.value === "transfer") {
    formData.value.transferAmount = formData.value.amount;
    formData.value.cashAmount = "";
  } else {
    // both - user will input manually
    formData.value.cashAmount = "";
    formData.value.transferAmount = "";
  }
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest(".payment-method-dropdown")) {
    showPaymentMethodDropdown.value = false;
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});

// Format number input
const formatNumberInput = (field, event) => {
  // Remove non-numeric characters
  const value = event.target.value.replace(/\D/g, "");
  formData.value[field] = value;

  // Auto-update payment amounts based on method
  if (field === "amount") {
    if (formData.value.paymentMethod === "cash") {
      formData.value.cashAmount = value;
      formData.value.transferAmount = "";
    } else if (formData.value.paymentMethod === "transfer") {
      formData.value.transferAmount = value;
      formData.value.cashAmount = "";
    }
  }
};

// Amount validation error
const amountError = computed(() => {
  if (!formData.value.amount) return "";

  const amount = parseInt(formData.value.amount);
  if (isNaN(amount) || amount <= 0) {
    return "Số tiền phải lớn hơn 0";
  }
  if (amount > props.remainingAmount) {
    return `Số tiền không được vượt quá ${formatCurrency(
      props.remainingAmount
    )}`;
  }
  return "";
});

// Validation for "both" payment method
const bothPaymentError = computed(() => {
  if (formData.value.paymentMethod !== "both") return "";

  const cashAmount = parseInt(formData.value.cashAmount) || 0;
  const transferAmount = parseInt(formData.value.transferAmount) || 0;
  const totalPayment = cashAmount + transferAmount;
  const paymentAmount = parseInt(formData.value.amount) || 0;

  if (cashAmount === 0 && transferAmount === 0) {
    return "Vui lòng nhập số tiền mặt và/hoặc chuyển khoản";
  }

  if (totalPayment !== paymentAmount) {
    return `Tổng tiền mặt + chuyển khoản phải bằng ${formatCurrency(
      paymentAmount
    )}. Hiện tại: ${formatCurrency(totalPayment)}`;
  }

  return "";
});

// Form validation
const isFormValid = computed(() => {
  // Check if amount is valid
  const hasAmount =
    formData.value.amount.trim() !== "" &&
    parseInt(formData.value.amount) > 0 &&
    parseInt(formData.value.amount) <= props.remainingAmount;

  const hasPaymentMethod = formData.value.paymentMethod.trim() !== "";

  // Basic validation failed
  if (!hasAmount || !hasPaymentMethod) {
    return false;
  }

  // If payment method is 'both', validate cash and transfer amounts
  if (formData.value.paymentMethod === "both") {
    const cashAmount = parseInt(formData.value.cashAmount) || 0;
    const transferAmount = parseInt(formData.value.transferAmount) || 0;
    const totalPayment = cashAmount + transferAmount;
    const paymentAmount = parseInt(formData.value.amount);

    // Both amounts must be greater than 0 and sum must equal payment amount
    return (
      (cashAmount > 0 || transferAmount > 0) && totalPayment === paymentAmount
    );
  }

  return true;
});

// Format currency
const formatCurrency = (amount) => {
  return (
    new Intl.NumberFormat("vi-VN", {
      style: "decimal",
      minimumFractionDigits: 0,
    }).format(amount) + "đ"
  );
};

// Submit handler
const handleSubmit = async () => {
  if (!isFormValid.value || isSubmitting.value) return;

  try {
    isSubmitting.value = true;

    // Reload phiếu chi details to get latest data
    console.log("🔄 Đang tải lại chi tiết phiếu chi...");
    await loadPhieuChiDetails();

    // Ensure phieuChiDetails is loaded
    if (!phieuChiDetails.value) {
      showErrorToast("Không thể tải thông tin phiếu chi. Vui lòng thử lại.");
      return;
    }

    // Tính số tiền thanh toán thêm
    const additionalPayment = parseInt(formData.value.amount);

    // Lấy thông tin hiện tại từ phieuChiDetails (dữ liệu mới nhất từ API)
    const currentPaidAmount =
      phieuChiDetails.value?.so_tien_thanh_toan_ngay || props.paidAmount;
    const currentRemainingAmount =
      phieuChiDetails.value?.so_tien_con_no || props.remainingAmount;
    const totalAmount =
      phieuChiDetails.value?.tong_so_tien || props.totalAmount;

    // ✅ CÁCH TÍNH MỚI: Còn nợ mới = Còn nợ hiện tại - Thanh toán thêm
    const newRemainingAmount = Math.max(
      0,
      currentRemainingAmount - additionalPayment
    );

    // Tính tổng đã trả mới = Tổng tiền - Còn nợ mới
    const newTotalPaidAmount = totalAmount - newRemainingAmount;

    // Tính tiền mặt và chuyển khoản hiện tại
    let currentCashAmount = phieuChiDetails.value?.tien_mat || 0;
    let currentTransferAmount = phieuChiDetails.value?.tien_chuyen_khoan || 0;

    // Tính tiền mặt và chuyển khoản THÊM vào
    let additionalCash = 0;
    let additionalTransfer = 0;

    if (formData.value.paymentMethod === "cash") {
      additionalCash = additionalPayment;
    } else if (formData.value.paymentMethod === "transfer") {
      additionalTransfer = additionalPayment;
    } else if (formData.value.paymentMethod === "both") {
      additionalCash = parseInt(formData.value.cashAmount) || 0;
      additionalTransfer = parseInt(formData.value.transferAmount) || 0;
    }

    // Prepare update data - GỬI TỔNG ĐÃ TRẢ MỚI
    const updateData = {
      so_tien_thanh_toan_ngay: newTotalPaidAmount, // Backend sẽ tính: so_tien_con_no = tong_so_tien - so_tien_thanh_toan_ngay
      tien_mat: currentCashAmount + additionalCash,
      tien_chuyen_khoan: currentTransferAmount + additionalTransfer,
      ghi_chu: formData.value.note
        ? `${
            phieuChiDetails.value?.ghi_chu || ""
          }\n[Thanh toán thêm ${formatCurrency(
            additionalPayment
          )} vào ${new Date().toLocaleString("vi-VN")}]: ${
            formData.value.note
          }`.trim()
        : phieuChiDetails.value?.ghi_chu,
    };

    console.log("📊 Thông tin thanh toán:");
    console.log("  💰 Tổng giá trị:", formatCurrency(totalAmount));
    console.log(
      "  🔴 Còn nợ hiện tại:",
      formatCurrency(currentRemainingAmount)
    );
    console.log("  💳 Thanh toán thêm:", formatCurrency(additionalPayment));
    console.log("  🟢 Còn nợ mới:", formatCurrency(newRemainingAmount));
    console.log("  ✅ Đã trả cũ:", formatCurrency(currentPaidAmount));
    console.log("  🎯 Tổng đã trả mới:", formatCurrency(newTotalPaidAmount));
    console.log("📤 Gửi dữ liệu lên backend:", updateData);

    // Call API to update
    const response = await updatePhieuChi(props.expenseId, updateData);

    if (response && response.status) {
      console.log("📥 Response từ backend:", response.data);

      // Backend đã tính toán lại: so_tien_con_no và trang_thai
      const updatedData = response.data;
      const updatedPaidAmount = updatedData.so_tien_thanh_toan_ngay;
      const updatedRemainingAmount = updatedData.so_tien_con_no;
      const updatedStatus = updatedData.trang_thai;

      console.log("✅ Kết quả từ Backend:");
      console.log("  💵 Đã trả:", formatCurrency(updatedPaidAmount));
      console.log("  🔴 Còn nợ:", formatCurrency(updatedRemainingAmount));
      console.log("  📊 Trạng thái:", updatedStatus);

      // Kiểm tra đã thanh toán đủ chưa
      const isFullyPaid =
        updatedRemainingAmount === 0 ||
        updatedRemainingAmount <= 0 ||
        updatedStatus === "da_thanh_toan_du" ||
        updatedStatus === "hoan_thanh";

      if (isFullyPaid) {
        showSuccessToast(
          `✅ Thanh toán đủ! Phiếu ${props.expenseCode} đã hoàn thành.`
        );
      } else {
        showSuccessToast(
          `💰 Thanh toán thêm ${formatCurrency(
            additionalPayment
          )}. Còn nợ: ${formatCurrency(updatedRemainingAmount)}`
        );
      }

      // Emit success event với data đã được backend tính toán
      emit("submit", {
        ...updatedData,
        additionalPayment,
        paymentMethod: formData.value.paymentMethodLabel,
        note: formData.value.note,
      });

      // Close and reset
      handleClose();
    } else {
      showErrorToast(response.message || "Có lỗi xảy ra khi thanh toán");
    }
  } catch (error) {
    console.error("❌ Lỗi thanh toán:", error);
    const errorMessage =
      error.response?.data?.message ||
      error.response?.data?.errors ||
      error.message ||
      "Không thể thực hiện thanh toán";
    showErrorToast(errorMessage);
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
/* Ensure Nunito Sans font is applied */
.font-nunito {
  font-family: "Nunito Sans", sans-serif;
}
</style>
