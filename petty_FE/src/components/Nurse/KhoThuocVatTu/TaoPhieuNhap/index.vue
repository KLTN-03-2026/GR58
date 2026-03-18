<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white border !border-gray-300 rounded-[10px] shadow-lg w-[860px] max-h-[90vh] overflow-y-auto relative"
    >
      <!-- Header -->
      <div class="flex flex-col gap-2 px-6 pt-6 pb-4">
        <h2 class="font-semibold text-lg text-black">Tạo phiếu nhập hàng</h2>
        <p class="font-medium text-sm text-gray-500">
          Nhập thông tin phiếu nhập kho mới
        </p>
      </div>

      <!-- Content -->
      <div class="flex flex-col gap-4 px-6 py-1">
        <!-- Row 1: Supplier & Date -->
        <div class="grid grid-cols-2 gap-4">
          <!-- Supplier -->
          <div class="flex flex-col gap-0 relative">
            <label class="font-medium text-sm text-black mb-0">
              Nhà cung cấp (*)
            </label>
            <button
              @click="showSupplierDropdown = !showSupplierDropdown"
              type="button"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-[13px] py-0.5 flex items-center justify-between hover:bg-gray-200 transition-colors mt-0"
            >
              <span
                class="font-medium text-sm text-black"
                :class="formData.supplierName ? 'text-black' : 'text-gray-500'"
              >
                {{ formData.supplierName || "Chọn nhà cung cấp" }}
              </span>
              <ChevronDownIcon />
            </button>

            <!-- Supplier Dropdown -->
            <div
              v-if="showSupplierDropdown"
              class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto z-50"
            >
              <div
                v-for="supplier in suppliers"
                :key="supplier.id"
                @click="selectSupplier(supplier)"
                class="px-3 py-2 hover:bg-gray-50 cursor-pointer font-medium text-sm"
              >
                {{ supplier.name || supplier.ten_nha_cung_cap }}
              </div>
              <div
                v-if="suppliers.length === 0"
                class="px-3 py-2 text-gray-400 font-medium text-sm"
              >
                Không có nhà cung cấp
              </div>
            </div>
          </div>

          <!-- Import Date -->
          <div class="flex flex-col gap-0">
            <label class="font-medium text-sm text-black mb-0">
              Ngày nhập (*)
            </label>
            <input
              v-model="formData.importDate"
              type="date"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-medium text-sm text-black tracking-tight outline-none mt-0"
            />
          </div>
        </div>

        <!-- Notes -->
        <div class="flex flex-col gap-0">
          <label class="font-medium text-sm text-black mb-0"> Ghi chú </label>
          <textarea
            v-model="formData.notes"
            placeholder="Nhập ghi chú về phiếu nhập (nếu có)..."
            rows="3"
            class="bg-[#f3f3f5] border-none rounded-lg px-3 py-2 font-medium text-sm text-black tracking-tight outline-none placeholder:text-[#717182] resize-none mt-0"
          ></textarea>
        </div>

        <!-- Import Items Section -->
        <div
          class="bg-gray-50 border !border-gray-300 rounded-[10px] p-[17px] flex flex-col gap-3"
        >
          <!-- Section Header -->
          <div class="flex items-center justify-between h-8">
            <label class="font-medium text-sm text-black mb-0">
              Danh sách hàng nhập
            </label>
            <button
              @click="addItem"
              class="bg-white border border-gray-200/60 rounded-lg h-8 px-[11px] py-0 flex items-center gap-2 hover:bg-gray-50 transition-colors"
            >
              <AddIcon />
              <span class="font-medium text-sm text-black tracking-tight"
                >Thêm dòng</span
              >
            </button>
          </div>

          <!-- Items Table -->
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-200/60">
                  <th class="text-left py-[10px] px-2 min-w-[155px]">
                    <span class="font-medium text-sm text-black tracking-tight"
                      >Tên hàng hóa (*)</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2 min-w-[50px]">
                    <span class="font-medium text-sm text-black tracking-tight"
                      >Đơn vị</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2 min-w-[87px]">
                    <span class="font-medium text-sm text-black tracking-tight"
                      >Số lượng (*)</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2 min-w-[80px]">
                    <span class="font-medium text-sm text-black tracking-tight"
                      >Đơn giá (*)</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2 min-w-[62px]">
                    <span class="font-medium text-sm text-black tracking-tight"
                      >Số lô (*)</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2 min-w-[157px]">
                    <span class="font-medium text-sm text-black tracking-tight"
                      >Hạn sử dụng (*)</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2 min-w-[78px]">
                    <span class="font-medium text-sm text-black tracking-tight"
                      >Thành tiền</span
                    >
                  </th>
                  <th class="py-[10px] px-2 w-[52px]"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in formData.items" :key="index">
                  <!-- Product Name -->
                  <td class="py-2 px-2 relative">
                    <button
                      @click="
                        showProductDropdown[index] = !showProductDropdown[index]
                      "
                      type="button"
                      class="bg-[#f3f3f5] border-none rounded-lg h-9 px-[13px] py-0.5 flex items-center justify-between hover:bg-gray-200 transition-colors w-full"
                    >
                      <span
                        class="font-nunito text-sm leading-5 tracking-tight"
                        :class="
                          item.productName
                            ? 'text-neutral-950'
                            : 'text-[#717182]'
                        "
                      >
                        {{ item.productName || "Chọn hàng hóa" }}
                      </span>
                      <ChevronDownIcon />
                    </button>

                    <!-- Product Dropdown -->
                    <div
                      v-if="showProductDropdown[index]"
                      class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto z-50 min-w-[200px]"
                    >
                      <div
                        v-for="product in products"
                        :key="product.id"
                        @click="selectProduct(product, index)"
                        class="px-3 py-2 hover:bg-gray-50 cursor-pointer font-nunito text-sm"
                      >
                        {{ product.name || product.ten_mat_hang }}
                      </div>
                      <div
                        v-if="products.length === 0"
                        class="px-3 py-2 text-gray-400 font-nunito text-sm"
                      >
                        Không có hàng hóa
                      </div>
                    </div>
                  </td>

                  <!-- Unit -->
                  <td class="py-2 px-2">
                    <input
                      v-model="item.unit"
                      type="text"
                      disabled
                      class="bg-gray-100 border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none opacity-50 w-full"
                    />
                  </td>

                  <!-- Quantity -->
                  <td class="py-2 px-2">
                    <input
                      v-model.number="item.quantity"
                      type="number"
                      placeholder="1"
                      class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none w-full"
                      @input="calculateTotal(index)"
                    />
                  </td>

                  <!-- Unit Price -->
                  <td class="py-2 px-2">
                    <input
                      v-model.number="item.unitPrice"
                      type="number"
                      placeholder="0"
                      class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-[#717182] tracking-tight outline-none w-full"
                      @input="calculateTotal(index)"
                    />
                  </td>

                  <!-- Lot Number -->
                  <td class="py-2 px-2">
                    <input
                      v-model="item.lotNumber"
                      type="text"
                      placeholder="LOT-2025-A"
                      class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-[#717182] tracking-tight outline-none w-full"
                    />
                  </td>

                  <!-- Expiry Date -->
                  <td class="py-2 px-2">
                    <input
                      v-model="item.expiryDate"
                      type="date"
                      class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none w-full"
                    />
                  </td>

                  <!-- Total Price -->
                  <td class="py-2 px-2">
                    <span
                      class="font-medium text-sm text-black tracking-tight"
                      >{{ formatCurrency(item.total) }}</span
                    >
                  </td>

                  <!-- Delete Button -->
                  <td class="py-2 px-2">
                    <button
                      @click="removeItem(index)"
                      class="rounded-lg h-8 w-9 flex items-center justify-center hover:bg-gray-100 transition-colors"
                    >
                      <TrashIcon class="w-4 h-4 text-red-500" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Total Amount -->
        <div
          class="bg-teal-50 rounded-[10px] px-4 h-14 flex items-center justify-between"
        >
          <span
            class="font-medium text-base leading-6 text-[#364153] tracking-tight"
          >
            Tổng tiền thanh toán:
          </span>
          <span
            class="font-medium text-base leading-6 text-[#009689] tracking-tight"
          >
            {{ formatCurrency(totalAmount) }}
          </span>
        </div>
      </div>

      <!-- Footer -->
      <div
        class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-200/60"
      >
        <button
          @click="$emit('close')"
          class="bg-white border !border-gray-300 rounded-lg h-9 px-4 py-2 hover:bg-gray-50 transition-colors"
        >
          <span class="font-medium text-sm text-black tracking-tight">Hủy</span>
        </button>
        <button
          @click="handleSubmit"
          class="bg-[#5a9690] rounded-lg h-9 px-4 py-2 hover:bg-[#5a9690]/80 transition-colors"
          :disabled="!isFormValid || isSubmitting"
          :class="{
            'opacity-50 cursor-not-allowed': !isFormValid || isSubmitting,
          }"
        >
          <span class="font-medium text-sm text-white tracking-tight">{{
            isSubmitting ? "Đang lưu..." : "Lưu phiếu nhập"
          }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { createPhieuNhapKho } from "@/utils/phieuNhapKho";
import { getNhaCungCaps } from "@/utils/nhaCungCap";
import { listHangHoa } from "@/utils/hangHoa";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
//Icon SVG
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import AddIcon from "@/assets/svg/add.svg";
import TrashIcon from "@/assets/svg/trash.svg";
// Props
defineProps({
  // No props needed for now
});

// Emits
const emit = defineEmits(["close", "save"]);

// Data
const suppliers = ref([]);
const products = ref([]);
const isSubmitting = ref(false);
const showSupplierDropdown = ref(false);
const showProductDropdown = ref({});

// Form Data
const formData = ref({
  supplierId: null,
  supplierName: "",
  importDate: new Date().toISOString().split("T")[0],
  receiptCode: "",
  notes: "", // Ghi chú cho phiếu nhập
  items: [
    {
      productId: null,
      productName: "",
      unit: "",
      quantity: 1,
      unitPrice: 0,
      lotNumber: "",
      expiryDate: "",
      notes: "",
      total: 0,
    },
  ],
  // Thông tin phiếu chi
  phieuChi: {
    receiptCode: "",
    reason: "Thanh toán nhập hàng",
    paymentDate: new Date().toISOString().split("T")[0],
    receiver: "",
    notes: "",
  },
});

// Computed
const totalAmount = computed(() => {
  return formData.value.items.reduce((sum, item) => sum + (item.total || 0), 0);
});

const isFormValid = computed(() => {
  if (
    !formData.value.supplierId ||
    !formData.value.importDate ||
    !formData.value.receiptCode
  ) {
    return false;
  }

  return formData.value.items.every(
    (item) =>
      item.productId &&
      item.quantity > 0 &&
      item.unitPrice > 0 &&
      item.lotNumber &&
      item.expiryDate
  );
});

// Lifecycle
onMounted(async () => {
  await loadSuppliers();
  await loadProducts();
  generateReceiptCode();
});

// Methods
const loadSuppliers = async () => {
  try {
    const response = await getNhaCungCaps();
    if (response.status) {
      suppliers.value = response.data || [];
    }
  } catch (error) {
    console.error("Error loading suppliers:", error);
    showErrorToast("Không thể tải danh sách nhà cung cấp");
  }
};

const loadProducts = async () => {
  try {
    const data = await listHangHoa();
    products.value = data || [];
  } catch (error) {
    console.error("Error loading products:", error);
    showErrorToast("Không thể tải danh sách hàng hóa");
  }
};

const generateReceiptCode = () => {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, "0");
  const day = String(now.getDate()).padStart(2, "0");
  const hours = String(now.getHours()).padStart(2, "0");
  const minutes = String(now.getMinutes()).padStart(2, "0");
  const seconds = String(now.getSeconds()).padStart(2, "0");

  formData.value.receiptCode = `PN${year}${month}${day}${hours}${minutes}${seconds}`;
  formData.value.phieuChi.receiptCode = `PC${year}${month}${day}${hours}${minutes}${seconds}`;
};

const selectSupplier = (supplier) => {
  formData.value.supplierId = supplier.id;
  formData.value.supplierName = supplier.name || supplier.ten_nha_cung_cap;
  formData.value.phieuChi.receiver =
    supplier.contact ||
    supplier.ten_nguoi_lien_he ||
    supplier.name ||
    supplier.ten_nha_cung_cap;
  showSupplierDropdown.value = false;
};

const selectProduct = (product, index) => {
  const item = formData.value.items[index];
  item.productId = product.id;
  item.productName = product.name || product.ten_mat_hang;
  item.unit = product.unit || product.don_vi_tinh;
  item.unitPrice = product.costPrice || product.gia_von || 0;
  calculateTotal(index);
  showProductDropdown.value[index] = false;
};

const addItem = () => {
  formData.value.items.push({
    productId: null,
    productName: "",
    unit: "",
    quantity: 1,
    unitPrice: 0,
    lotNumber: "",
    expiryDate: "",
    notes: "",
    total: 0,
  });
};

const removeItem = (index) => {
  if (formData.value.items.length > 1) {
    formData.value.items.splice(index, 1);
  }
};

const calculateTotal = (index) => {
  const item = formData.value.items[index];
  item.total = (item.quantity || 0) * (item.unitPrice || 0);
};

const formatCurrency = (amount) => {
  return `${(amount || 0).toLocaleString("vi-VN")} ₫`;
};

const handleSubmit = async () => {
  if (!isFormValid.value || isSubmitting.value) return;

  try {
    isSubmitting.value = true;

    // Chuẩn bị dữ liệu gửi lên backend
    // Backend tự lấy thông tin user từ token, không cần truyền nhan_vien_id hay admin_id
    const payload = {
      ma_phieu_nhap: formData.value.receiptCode,
      ngay_nhap: formData.value.importDate,
      ghi_chu: formData.value.notes || "", // Ghi chú của phiếu nhập
      trang_thai: "cho_duyet",
      nha_cung_cap_id: formData.value.supplierId,
      phieu_chi: {
        ma_phieu_chi: formData.value.phieuChi.receiptCode,
        ly_do: formData.value.phieuChi.reason,
        ngay_chi: formData.value.phieuChi.paymentDate,
        nguoi_nhan: formData.value.phieuChi.receiver,
        ghi_chu: formData.value.phieuChi.notes || "",
      },
      chi_tiet: formData.value.items.map((item) => ({
        hang_hoa_id: item.productId,
        so_luong: item.quantity,
        so_lo: item.lotNumber,
        han_su_dung: item.expiryDate,
        don_gia: item.unitPrice,
        ghi_chu: item.notes || "",
      })),
    };

    // Gọi API
    const response = await createPhieuNhapKho(payload);

    if (response.status) {
      showSuccessToast(response.message || "Tạo phiếu nhập kho thành công");
      emit("save", response.data);
      emit("close");
    } else {
      showErrorToast(
        response.message || "Có lỗi xảy ra khi tạo phiếu nhập kho"
      );
    }
  } catch (error) {
    console.error("Error creating phieu nhap kho:", error);

    // Xử lý lỗi validation từ backend
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      if (errors) {
        const firstError = Object.values(errors)[0];
        showErrorToast(firstError[0] || "Dữ liệu không hợp lệ");
      } else {
        showErrorToast(error.response.data.message || "Dữ liệu không hợp lệ");
      }
    } else {
      showErrorToast(
        error.response?.data?.message || "Có lỗi xảy ra khi tạo phiếu nhập kho"
      );
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
/* Remove number input arrows */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
  appearance: textfield;
}

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
