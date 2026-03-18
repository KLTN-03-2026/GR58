<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-[9999] pt-24 font-nunito"
  >
    <div
      class="bg-white border !border-gray-300 rounded-[10px] shadow-xl w-full max-w-[510px] max-h-[85vh] flex flex-col"
    >
      <!-- Header -->
      <div class="flex items-start justify-between px-6 pt-6 pb-4">
        <div class="flex flex-col gap-2">
          <h2 class="text-lg font-semibold text-neutral-950">
            Tạo phiếu chi mới
          </h2>
          <p class="text-sm text-[#717182]">
            Mã phiếu sẽ được tạo tự động (PCN/PCV + ngày + số thứ tự)
          </p>
        </div>
        <button
          @click="$emit('close')"
          class="opacity-70 hover:opacity-100 transition-opacity"
        >
          <CloseIcon class="text-black" />
        </button>
      </div>

      <!-- Form Content - Scrollable -->
      <div class="px-6 overflow-y-auto flex-1">
        <div class="flex flex-col gap-4 pb-6">
          <!-- Loại phiếu chi -->
          <div class="flex flex-col">
            <label class="text-sm font-medium text-neutral-950 mb-2"
              >Loại phiếu chi</label
            >
            <div class="flex flex-col gap-3">
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  type="radio"
                  v-model="formData.type"
                  value="chi_nhap_hang"
                  class="w-4 h-4 text-[#009689] focus:ring-[#009689]"
                />
                <span class="text-sm font-medium text-neutral-950"
                  >Chi nhập hàng (Liên kết với Kho)</span
                >
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  type="radio"
                  v-model="formData.type"
                  value="chi_van_hanh"
                  class="w-4 h-4 text-[#009689] focus:ring-[#009689]"
                />
                <span class="text-sm font-medium text-neutral-950"
                  >Chi phí vận hành (Điện, nước, thuê nhà...)</span
                >
              </label>
            </div>
          </div>

          <!-- Đối tượng nhận tiền -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-neutral-950"
              >Đối tượng nhận tiền</label
            >
            <div v-if="formData.type === 'chi_nhap_hang'" class="relative">
              <!-- Dropdown for Purchase Type -->
              <button
                @click.stop="toggleSupplierDropdown"
                type="button"
                class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 flex items-center justify-between h-9 text-left"
              >
                <span
                  class="text-sm"
                  :class="
                    formData.supplier ? 'text-neutral-950' : 'text-[#717182]'
                  "
                >
                  {{ formData.supplier || "Chọn nhà cung cấp" }}
                </span>
                <ChevronDownIcon />
              </button>

              <!-- Supplier Dropdown List -->
              <div
                v-if="showSupplierDropdown"
                class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto"
              >
                <div
                  v-if="loadingSuppliers"
                  class="px-3 py-2 text-sm text-[#717182]"
                >
                  Đang tải...
                </div>
                <div
                  v-else-if="suppliers.length === 0"
                  class="px-3 py-2 text-sm text-[#717182]"
                >
                  Không có nhà cung cấp nào
                </div>
                <button
                  v-for="supplier in suppliers"
                  :key="supplier.id"
                  @click.stop="selectSupplier(supplier)"
                  type="button"
                  class="w-full text-left px-3 py-2 hover:bg-gray-100 text-sm text-neutral-950 transition-colors"
                >
                  {{ supplier.ten_nha_cung_cap }}
                  <span class="text-xs text-[#717182]">
                    ({{ supplier.ma_nha_cung_cap }})</span
                  >
                </button>
              </div>
            </div>
            <input
              v-else
              type="text"
              v-model="formData.recipient"
              placeholder="VD: Điện lực TP.HCM, Chủ nhà..."
              class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 h-9 text-sm text-neutral-950 placeholder:text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689]"
            />
          </div>

          <!-- Lý do chi -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-neutral-950"
              >Lý do chi<span class="text-red-500">*</span></label
            >
            <textarea
              v-model="formData.reason"
              placeholder="VD: Trả tiền nhập Vắc-xin tháng 10, Tiền điện tháng 11..."
              rows="3"
              class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 text-sm text-neutral-950 placeholder:text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689] resize-none"
            ></textarea>
          </div>

          <!-- Ngày chi -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-neutral-950"
              >Ngày chi<span class="text-red-500">*</span></label
            >
            <input
              type="date"
              v-model="formData.date"
              class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 h-9 text-sm text-neutral-950 focus:outline-none focus:ring-2 focus:ring-[#009689]"
            />
          </div>

          <!-- Tổng số tiền -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-neutral-950"
              >Tổng số tiền</label
            >
            <input
              type="text"
              v-model="formData.totalAmount"
              placeholder="VD: 15000000"
              @input="formatNumberInput('totalAmount', $event)"
              class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 h-9 text-sm text-neutral-950 placeholder:text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689]"
            />
          </div>

          <!-- Số tiền thanh toán ngay -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-neutral-950"
              >Số tiền thanh toán ngay</label
            >
            <input
              type="text"
              v-model="formData.paidAmount"
              placeholder="VD: 5000000 (Nếu nhập ít hơn Tổng tiền -> Ghi nhận nợ)"
              @input="formatNumberInput('paidAmount', $event)"
              class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 h-9 text-sm text-neutral-950 placeholder:text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689]"
            />
            <p class="text-xs text-[#6a7282] leading-4">
              Tip: Nếu nhập ít hơn Tổng tiền, hệ thống sẽ tự động ghi nhận phần
              còn lại là Nợ
            </p>
          </div>

          <!-- Nguồn tiền -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-neutral-950"
              >Nguồn tiền<span class="text-red-500">*</span></label
            >
            <div class="relative">
              <button
                @click.stop="togglePaymentSourceDropdown"
                type="button"
                class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 flex items-center justify-between h-9 text-left"
              >
                <span class="text-sm text-neutral-950">
                  {{ getPaymentSourceLabel }}
                </span>
                <ChevronDownIcon />
              </button>

              <!-- Payment Source Dropdown -->
              <div
                v-if="showPaymentSourceDropdown"
                class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
              >
                <button
                  v-for="source in paymentSources"
                  :key="source.value"
                  @click.stop="selectPaymentSource(source)"
                  type="button"
                  class="w-full text-left px-3 py-2 hover:bg-gray-100 text-sm text-neutral-950 transition-colors"
                  :class="{
                    'bg-[#e6f7f5]': formData.paymentSource === source.value,
                  }"
                >
                  {{ source.label }}
                </button>
              </div>
            </div>
          </div>

          <!-- Chi tiết tiền mặt và chuyển khoản (nếu chọn Cả hai) -->
          <div v-if="formData.paymentSource === 'both'" class="flex gap-2">
            <div class="flex-1 flex flex-col gap-2">
              <label class="text-sm font-medium text-neutral-950"
                >Tiền mặt</label
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

          <!-- Chứng từ kèm theo -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-neutral-950"
              >Chứng từ kèm theo (Optional)</label
            >
            <button
              @click="handleFileUpload"
              type="button"
              class="w-full bg-white border border-black/10 rounded-lg h-9 flex items-center justify-center gap-2 hover:bg-gray-50 transition-colors"
            >
              <UploadIcon class="text-neutral-950 w-4 h-4" />
              <span class="text-sm font-medium text-neutral-950">
                {{
                  formData.attachments.length > 0
                    ? `Đã chọn ${formData.attachments.length} ảnh`
                    : "Upload ảnh hóa đơn"
                }}
              </span>
            </button>
            <p class="text-xs text-[#6a7282] leading-4">
              Chụp hóa đơn điện nước hoặc hóa đơn đỏ của NCC để lưu trữ
            </p>
          </div>

          <!-- Ghi chú -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-neutral-950"
              >Ghi chú (Optional)</label
            >
            <textarea
              v-model="formData.note"
              placeholder="Thêm ghi chú nếu cần..."
              rows="2"
              class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 text-sm text-neutral-950 placeholder:text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689] resize-none"
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div
        class="flex items-center justify-end gap-2 px-6 py-4 border-t border-black/10"
      >
        <button
          @click="$emit('close')"
          type="button"
          :disabled="isSubmitting"
          class="bg-white border !border-gray-300 rounded-lg px-4 py-2 h-9 text-sm font-medium text-neutral-950 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Hủy
        </button>
        <button
          @click="handleSubmit"
          type="button"
          :disabled="!isFormValid || isSubmitting"
          :class="[
            'bg-[#e7000b] rounded-lg px-4 py-2 h-9 text-sm font-medium text-white transition-colors flex items-center gap-2',
            isFormValid && !isSubmitting
              ? 'hover:bg-[#c4000a] cursor-pointer'
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
          <span>{{ isSubmitting ? "Đang tạo..." : "Tạo phiếu chi" }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from "vue";
import { createPhieuChi } from "@/utils/phieuChi";
import { getNhaCungCaps } from "@/utils/nhaCungCap";
import { showErrorToast, showSuccessToast } from "@/utils/toast";
// Icon SVG
import CloseIcon from "@/assets/svg/close.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import UploadIcon from "@/assets/svg/upload.svg";

// Icons
const iconClose =
  "https://www.figma.com/api/mcp/asset/63dd8b1e-1eb6-4c6d-829b-78b5f57b41e9";
const iconChevronDown =
  "https://www.figma.com/api/mcp/asset/6cfab6d3-a5cc-4c06-b534-3fd16a585a3f";
const iconUpload =
  "https://www.figma.com/api/mcp/asset/de9a42eb-3efd-4bfd-887c-0b2043ef30df";
const iconCheck =
  "https://www.figma.com/api/mcp/asset/c89b26f1-55c6-4d25-9216-c6454ab37981";

// Define emits
const emit = defineEmits(["close", "submit"]);

// Form data
const formData = ref({
  type: "chi_van_hanh", // 'chi_nhap_hang' or 'chi_van_hanh'
  supplier: "", // For chi_nhap_hang type
  supplierId: null, // ID nhà cung cấp
  recipient: "", // For chi_van_hanh type
  reason: "",
  totalAmount: "",
  paidAmount: "",
  cashAmount: "", // Tiền mặt
  transferAmount: "", // Tiền chuyển khoản
  paymentSource: "cash", // 'cash' or 'transfer' or 'both'
  attachments: [],
  note: "",
  date: new Date().toISOString().split("T")[0], // Ngày chi
});

// Loading state
const isSubmitting = ref(false);

// Danh sách nhà cung cấp
const suppliers = ref([]);
const loadingSuppliers = ref(false);

// Dropdown states
const showSupplierDropdown = ref(false);
const showPaymentSourceDropdown = ref(false);

// Load danh sách nhà cung cấp khi component mount
const loadSuppliers = async () => {
  try {
    loadingSuppliers.value = true;
    const response = await getNhaCungCaps();
    console.log("Response nhà cung cấp:", response);

    if (response && response.status) {
      // Kiểm tra nếu data là array trực tiếp
      if (Array.isArray(response.data)) {
        suppliers.value = response.data;
      }
      // Kiểm tra nếu data nằm trong pagination
      else if (response.data && Array.isArray(response.data.data)) {
        suppliers.value = response.data.data;
      }
      // Kiểm tra nếu data là object với key data
      else if (response.data && response.data.length !== undefined) {
        suppliers.value = response.data;
      }

      console.log("Danh sách nhà cung cấp:", suppliers.value);
    }
  } catch (error) {
    console.error("Lỗi tải danh sách nhà cung cấp:", error);
    showErrorToast("Không thể tải danh sách nhà cung cấp");
  } finally {
    loadingSuppliers.value = false;
  }
};

// Load suppliers when component mounts
loadSuppliers();

// Toggle dropdown functions
const toggleSupplierDropdown = () => {
  showSupplierDropdown.value = !showSupplierDropdown.value;
};

const togglePaymentSourceDropdown = () => {
  showPaymentSourceDropdown.value = !showPaymentSourceDropdown.value;
};

// Đóng dropdown khi click bên ngoài
const handleClickOutside = (event) => {
  // Kiểm tra xem click có phải ở trong dropdown không
  const supplierDropdown = event.target.closest(".relative");
  if (!supplierDropdown) {
    showSupplierDropdown.value = false;
    showPaymentSourceDropdown.value = false;
  }
};

// Thêm event listener khi component mount
onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

// Xóa event listener khi component unmount
onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});

// Watch để reset fields khi thay đổi loại phiếu chi
watch(
  () => formData.value.type,
  (newType) => {
    if (newType === "chi_nhap_hang") {
      formData.value.recipient = "";
    } else {
      formData.value.supplier = "";
      formData.value.supplierId = null;
    }
    // Đóng dropdown khi thay đổi type
    showSupplierDropdown.value = false;
  }
);

// Chọn nhà cung cấp
const selectSupplier = (supplier) => {
  formData.value.supplier = supplier.ten_nha_cung_cap;
  formData.value.supplierId = supplier.id;
  showSupplierDropdown.value = false;
};

// Chọn nguồn tiền
const paymentSources = [
  { value: "cash", label: "Tiền mặt tại két" },
  { value: "transfer", label: "Chuyển khoản" },
];

const selectPaymentSource = (source) => {
  formData.value.paymentSource = source.value;
  showPaymentSourceDropdown.value = false;

  // Reset payment amounts when changing source
  if (source.value === "cash") {
    formData.value.cashAmount = formData.value.paidAmount;
    formData.value.transferAmount = "";
  } else if (source.value === "transfer") {
    formData.value.transferAmount = formData.value.paidAmount;
    formData.value.cashAmount = "";
  } else {
    // both - user will input manually
    formData.value.cashAmount = "";
    formData.value.transferAmount = "";
  }
};

const getPaymentSourceLabel = computed(() => {
  const source = paymentSources.find(
    (s) => s.value === formData.value.paymentSource
  );
  return source ? source.label : "Tiền mặt tại két";
});

// Format number input
const formatNumberInput = (field, event) => {
  // Remove non-numeric characters
  const value = event.target.value.replace(/\D/g, "");
  formData.value[field] = value;

  // Auto-fill cash/transfer based on payment source
  if (field === "paidAmount") {
    if (formData.value.paymentSource === "cash") {
      formData.value.cashAmount = value;
      formData.value.transferAmount = "";
    } else if (formData.value.paymentSource === "transfer") {
      formData.value.transferAmount = value;
      formData.value.cashAmount = "";
    }
  }
};

// Convert file to base64
const fileToBase64 = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = (error) => reject(error);
  });
};

// File upload handler
const handleFileUpload = () => {
  const input = document.createElement("input");
  input.type = "file";
  input.accept = "image/*";
  input.multiple = true;
  input.onchange = async (e) => {
    const files = Array.from(e.target.files);

    try {
      // Convert files to base64
      const base64Files = await Promise.all(
        files.map((file) => fileToBase64(file))
      );
      formData.value.attachments = base64Files;
      showSuccessToast(`Đã tải lên ${files.length} ảnh`);
    } catch (error) {
      console.error("Error converting files:", error);
      showErrorToast("Không thể tải ảnh lên");
    }
  };
  input.click();
};

// Form validation
const isFormValid = computed(() => {
  const hasRecipient =
    formData.value.type === "chi_nhap_hang"
      ? formData.value.supplierId !== null
      : formData.value.recipient.trim() !== "";

  const hasValidPayment =
    formData.value.paymentSource === "both"
      ? formData.value.cashAmount.trim() !== "" ||
        formData.value.transferAmount.trim() !== ""
      : formData.value.paidAmount.trim() !== "";

  return (
    formData.value.type &&
    hasRecipient &&
    formData.value.reason.trim() !== "" &&
    formData.value.totalAmount.trim() !== "" &&
    formData.value.paidAmount.trim() !== "" &&
    hasValidPayment &&
    formData.value.date.trim() !== ""
  );
});

// Submit handler
const handleSubmit = async () => {
  if (!isFormValid.value || isSubmitting.value) return;

  try {
    isSubmitting.value = true;

    // Prepare data for API
    const submitData = {
      loai_phieu_chi: formData.value.type,
      ly_do_chi: formData.value.reason,
      tong_so_tien: parseInt(formData.value.totalAmount),
      so_tien_thanh_toan_ngay: parseInt(formData.value.paidAmount),
      tien_mat:
        formData.value.paymentSource === "cash"
          ? parseInt(formData.value.paidAmount)
          : formData.value.cashAmount
          ? parseInt(formData.value.cashAmount)
          : 0,
      tien_chuyen_khoan:
        formData.value.paymentSource === "transfer"
          ? parseInt(formData.value.paidAmount)
          : formData.value.transferAmount
          ? parseInt(formData.value.transferAmount)
          : 0,
      ngay_chi: formData.value.date,
      ghi_chu: formData.value.note || null,
      anh_chung_tu:
        formData.value.attachments.length > 0
          ? formData.value.attachments
          : null,
    };

    // Add specific fields based on type
    if (formData.value.type === "chi_nhap_hang") {
      submitData.nha_cung_cap_id = formData.value.supplierId;
      submitData.doi_tuong_nhan_tien = null;
    } else {
      submitData.doi_tuong_nhan_tien = formData.value.recipient;
      submitData.nha_cung_cap_id = null;
    }

    const response = await createPhieuChi(submitData);

    if (response && response.status) {
      showSuccessToast("Tạo phiếu chi thành công");
      emit("submit", response.data);
      emit("close");
    } else {
      showErrorToast(response.message || "Có lỗi xảy ra khi tạo phiếu chi");
    }
  } catch (error) {
    console.error("Error creating phieu chi:", error);
    const errorMessage =
      error.response?.data?.message ||
      error.response?.data?.errors ||
      "Không thể tạo phiếu chi";
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

/* Custom radio button styling */
input[type="radio"] {
  appearance: none;
  width: 16px;
  height: 16px;
  border: 1px solid rgba(0, 0, 0, 0.3);
  border-radius: 50%;
  outline: none;
  cursor: pointer;
  position: relative;
  flex-shrink: 0;
}

input[type="radio"]:checked {
  border-color: #009689;
}

input[type="radio"]:checked::after {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 8px;
  height: 8px;
  background-color: #009689;
  border-radius: 50%;
}

/* Scrollbar styling */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f3f3f5;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #c4c4c4;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a0a0a0;
}
</style>
