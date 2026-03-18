<template>
  <div
    class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center"
  >
    <div
      class="bg-white border !border-gray-300 rounded-[10px] shadow-lg w-[656px] max-h-[90vh] overflow-y-auto relative"
    >
      <!-- Loading State -->
      <div v-if="isLoading" class="flex items-center justify-center py-20">
        <div class="flex flex-col items-center gap-3">
          <div
            class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#009689]"
          ></div>
          <p class="font-nunito text-sm text-[#717182]">Đang tải dữ liệu...</p>
        </div>
      </div>

      <!-- Content -->
      <template v-else>
        <!-- Header -->
        <div class="flex items-center justify-between px-6 pt-6 pb-4">
          <div class="flex flex-col gap-0">
            <h2
              class="font-nunito font-semibold text-lg leading-[18px] text-neutral-950 tracking-tight"
            >
              Chi tiết Phiếu nhập {{ receiptData?.code || "#PN001" }}
            </h2>
            <p
              class="font-nunito text-sm leading-5 text-[#717182] tracking-tight"
            >
              Thông tin chi tiết phiếu nhập kho
            </p>
          </div>
        </div>

        <!-- Content -->
        <div class="flex flex-col gap-6 px-6 py-4">
          <!-- General Info Section -->
          <div
            class="bg-gray-50 border !border-gray-300 rounded-[10px] p-[17px] flex flex-col gap-3"
          >
            <h3
              class="font-nunito text-sm leading-5 text-[#364153] tracking-tight"
            >
              Thông tin chung
            </h3>

            <div class="grid grid-cols-3 gap-4">
              <!-- Supplier -->
              <div class="flex flex-col gap-1">
                <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                  Nhà cung cấp
                </p>
                <p
                  class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                >
                  {{ receiptData?.supplier || "N/A" }}
                </p>
              </div>

              <!-- Import Date -->
              <div class="flex flex-col gap-1">
                <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                  Ngày nhập
                </p>
                <p
                  class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                >
                  {{ formatDate(receiptData?.date) }}
                </p>
              </div>

              <!-- Importer -->
              <div class="flex flex-col gap-1">
                <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                  Người nhập
                </p>
                <p
                  class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                >
                  {{ receiptData?.user || "N/A" }}
                </p>
              </div>
            </div>

            <!-- Notes if exists -->
            <div
              v-if="receiptData?._original?.ghi_chu"
              class="flex flex-col gap-1 pt-2 border-t border-gray-200/60"
            >
              <p class="font-nunito text-xs leading-4 text-[#6a7282]">
                Ghi chú
              </p>
              <p
                class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
              >
                {{ receiptData._original.ghi_chu }}
              </p>
            </div>
          </div>

          <!-- Items List Section -->
          <div class="flex flex-col gap-3">
            <h3
              class="font-nunito text-sm leading-5 text-[#364153] tracking-tight"
            >
              Danh sách hàng hóa
            </h3>

            <!-- Items Table -->
            <div class="border !border-gray-300 rounded-[10px] overflow-hidden">
              <table class="w-full">
                <thead class="bg-gray-50">
                  <tr class="border-b border-gray-200/60">
                    <th class="text-left py-[10px] px-2 w-[43px]">
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >STT</span
                      >
                    </th>
                    <th class="text-left py-[10px] px-2 min-w-[160px]">
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >Tên hàng hóa</span
                      >
                    </th>
                    <th class="text-left py-[10px] px-2 w-[45px]">
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >ĐVT</span
                      >
                    </th>
                    <th class="text-right py-[10px] px-2 w-[74px]">
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >Số lượng</span
                      >
                    </th>
                    <th class="text-right py-[10px] px-2 w-[75px]">
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >Đơn giá</span
                      >
                    </th>
                    <th class="text-left py-[10px] px-2 w-[115px]">
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >Lô / Hạn dùng</span
                      >
                    </th>
                    <th class="text-right py-[10px] px-2 w-[95px]">
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >Thành tiền</span
                      >
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(item, index) in detailItems"
                    :key="index"
                    class="border-b border-gray-200/60"
                  >
                    <!-- STT -->
                    <td class="py-4 px-2 text-center">
                      <span
                        class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                      >
                        {{ index + 1 }}
                      </span>
                    </td>

                    <!-- Product Name -->
                    <td class="py-2 px-2">
                      <div class="flex flex-col gap-0">
                        <span
                          class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                        >
                          {{ item.productName }}
                        </span>
                        <span
                          class="font-nunito text-xs leading-4 text-[#6a7282]"
                        >
                          {{ item.productCode }}
                        </span>
                      </div>
                    </td>

                    <!-- Unit -->
                    <td class="py-4 px-2">
                      <span
                        class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                      >
                        {{ item.unit }}
                      </span>
                    </td>

                    <!-- Quantity -->
                    <td class="py-4 px-2 text-right">
                      <span
                        class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                      >
                        {{ item.quantity }}
                      </span>
                    </td>

                    <!-- Unit Price -->
                    <td class="py-4 px-2 text-right">
                      <span
                        class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                      >
                        {{ formatCurrency(item.unitPrice) }}
                      </span>
                    </td>

                    <!-- Lot / Expiry -->
                    <td class="py-2 px-2">
                      <div class="flex flex-col gap-1">
                        <span
                          class="font-nunito text-xs leading-4 text-[#101828]"
                        >
                          Lô: {{ item.lotNumber }}
                        </span>
                        <span
                          class="font-nunito text-xs leading-4 text-[#f54900]"
                        >
                          HSD: {{ formatDate(item.expiryDate) }}
                        </span>
                      </div>
                    </td>

                    <!-- Total -->
                    <td class="py-4 px-2 text-right">
                      <span
                        class="font-nunito text-sm leading-5 text-[#009689] tracking-tight"
                      >
                        {{ formatCurrency(item.total) }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Summary Section -->
          <div
            class="border-t border-gray-200/60 pt-[17px] pl-[287px] flex flex-col gap-2"
          >
            <!-- Total Quantity -->
            <div class="flex items-center justify-between">
              <span
                class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
              >
                Tổng số lượng:
              </span>
              <span
                class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
              >
                {{ totalQuantity }} sản phẩm
              </span>
            </div>

            <!-- Total Amount -->
            <div
              class="border-t border-gray-200/60 pt-[9px] flex items-center justify-between"
            >
              <span
                class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
              >
                Tổng tiền hàng:
              </span>
              <span
                class="font-nunito text-lg leading-7 text-[#009689] tracking-tight"
              >
                {{ formatCurrency(totalAmount) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div
          class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-200/60"
        >
          <button
            @click="$emit('close')"
            class="bg-white border !border-gray-400 rounded-lg h-9 px-4 py-2 hover:bg-gray-50 transition-colors"
            :disabled="isPrinting"
          >
            <span
              class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >Đóng</span
            >
          </button>
          <button
            @click="handlePrint"
            class="bg-[#5a9690] rounded-lg h-9 px-3 py-2 flex items-center gap-2 hover:bg-[#007d72] transition-colors"
            :disabled="isPrinting || isLoading"
            :class="{
              'opacity-50 cursor-not-allowed': isPrinting || isLoading,
            }"
          >
            <!-- Loading spinner khi đang xuất PDF -->
            <div
              v-if="isPrinting"
              class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"
            ></div>
            <span
              class="font-nunito font-medium text-sm leading-5 text-white tracking-tight"
              >{{ isPrinting ? "Đang xuất PDF..." : "In phiếu nhập" }}</span
            >
          </button>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from "vue";
import {
  getChiTietPhieuNhapKho,
  exportPhieuNhapKhoPdf,
} from "@/utils/phieuNhapKho";
import { showErrorToast, showSuccessToast } from "@/utils/toast";

// Props
const props = defineProps({
  receipt: {
    type: Object,
    required: true,
  },
});

// Emits
const emit = defineEmits(["close", "print"]);

// Icons from Figma (expire in 7 days)
const iconPrint =
  "https://www.figma.com/api/mcp/asset/53268957-177a-4bdd-8dcf-af164696ce79";

// Data
const isLoading = ref(false);
const isPrinting = ref(false);
const receiptData = ref(null);
const detailItems = ref([]);

// Computed
const totalQuantity = computed(() => {
  return detailItems.value.reduce((sum, item) => sum + (item.quantity || 0), 0);
});

const totalAmount = computed(() => {
  return detailItems.value.reduce((sum, item) => sum + (item.total || 0), 0);
});

// Methods
const formatCurrency = (amount) => {
  return `${(amount || 0).toLocaleString("vi-VN")} ₫`;
};

const formatDate = (dateString) => {
  if (!dateString) return "N/A";

  const date = new Date(dateString);
  if (isNaN(date.getTime())) return dateString;

  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const year = date.getFullYear();

  return `${day}/${month}/${year}`;
};

const getStatusText = (status) => {
  const statusMap = {
    cho_duyet: "Chờ duyệt",
    da_duyet: "Đã duyệt",
    da_nhap_kho: "Đã nhập kho",
    huy: "Đã hủy",
  };
  return statusMap[status] || status;
};

const getStatusBadgeClass = (status) => {
  const classMap = {
    cho_duyet: "bg-yellow-100 border border-yellow-300 text-yellow-700",
    da_duyet: "bg-blue-100 border border-blue-300 text-blue-700",
    da_nhap_kho: "bg-green-100 border border-[#b9f8cf] text-[#008236]",
    huy: "bg-red-100 border border-red-300 text-red-700",
  };
  return classMap[status] || "bg-gray-100 border border-gray-300 text-gray-700";
};

const loadChiTiet = async () => {
  if (!props.receipt || !props.receipt.id) {
    console.error("Receipt ID not provided");
    return;
  }

  try {
    isLoading.value = true;
    const response = await getChiTietPhieuNhapKho(props.receipt.id);

    if (response && response.status) {
      const data = response.data;

      // Gán dữ liệu phiếu nhập
      receiptData.value = {
        id: data.id,
        code: data.ma_phieu_nhap,
        supplier: data.nha_cung_cap?.ten_nha_cung_cap || "N/A",
        date: data.ngay_nhap,
        user: data.nhan_vien?.full_name || data.admin?.ho_ten || "N/A",
        status: data.trang_thai,
        total: data.tong_tien || 0,
        _original: data,
      };

      // Map chi tiết items
      if (data.chi_tiet && Array.isArray(data.chi_tiet)) {
        detailItems.value = data.chi_tiet.map((item) => ({
          productName: item.hang_hoa?.ten_mat_hang || "N/A",
          productCode: item.hang_hoa?.ma_hang_hoa || "N/A",
          unit: item.hang_hoa?.don_vi_tinh || "N/A",
          quantity: item.so_luong || 0,
          unitPrice: item.don_gia || 0,
          lotNumber: item.so_lo || "N/A",
          expiryDate: item.han_su_dung,
          total: (item.so_luong || 0) * (item.don_gia || 0),
          notes: item.ghi_chu,
        }));
      }
    }
  } catch (error) {
    console.error("Error loading receipt detail:", error);
    showErrorToast("Không thể tải chi tiết phiếu nhập");
  } finally {
    isLoading.value = false;
  }
};

const handlePrint = async () => {
  if (!receiptData.value || !receiptData.value.id) {
    showErrorToast("Không tìm thấy thông tin phiếu nhập");
    return;
  }

  try {
    isPrinting.value = true;

    // Gọi API để lấy PDF blob
    const pdfBlob = await exportPhieuNhapKhoPdf(receiptData.value.id);

    // Tạo URL từ blob
    const url = window.URL.createObjectURL(pdfBlob);

    // Tạo link ẩn để download
    const link = document.createElement("a");
    link.href = url;
    link.download = `phieu-nhap-kho-${receiptData.value.code}.pdf`;
    document.body.appendChild(link);
    link.click();

    // Cleanup
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);

    showSuccessToast("Đã tải xuống phiếu nhập kho");
    emit("print", receiptData.value);
  } catch (error) {
    console.error("Error printing receipt:", error);
    showErrorToast("Không thể xuất PDF phiếu nhập kho");
  } finally {
    isPrinting.value = false;
  }
};

// Lifecycle
onMounted(() => {
  // Nếu receipt đã có đầy đủ thông tin từ parent, sử dụng luôn
  if (props.receipt._original?.chi_tiet) {
    receiptData.value = props.receipt;
    const data = props.receipt._original;

    if (data.chi_tiet && Array.isArray(data.chi_tiet)) {
      detailItems.value = data.chi_tiet.map((item) => ({
        productName: item.hang_hoa?.ten_mat_hang || "N/A",
        productCode: item.hang_hoa?.ma_hang_hoa || "N/A",
        unit: item.hang_hoa?.don_vi_tinh || "N/A",
        quantity: item.so_luong || 0,
        unitPrice: item.don_gia || 0,
        lotNumber: item.so_lo || "N/A",
        expiryDate: item.han_su_dung,
        total: (item.so_luong || 0) * (item.don_gia || 0),
        notes: item.ghi_chu,
      }));
    }
  } else {
    // Nếu không có, gọi API để lấy chi tiết
    loadChiTiet();
  }
});
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
