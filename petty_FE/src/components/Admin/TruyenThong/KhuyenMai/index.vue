<template>
  <div class="w-full min-h-screen px-8 py-6 flex flex-col gap-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-black">
          Quản lý Mã giảm giá & CTKM
        </h1>
        <p class="text-gray-500 font-medium text-base">
          Tạo và quản lý các chương trình khuyến mãi
        </p>
      </div>
      <button
        @click="router.push('/admin/khuyen-mai/them-moi')"
        class="bg-[#5a9690] text-white rounded-lg px-4 py-2 h-9 flex items-center gap-2 text-sm font-medium hover:bg-[#5a9690]/80 transition-colors"
      >
        <AddIcon class="w-4 h-4" />
        Tạo khuyến mãi mới
      </button>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div class="flex flex-col gap-4">
        <!-- Search Bar -->
        <div class="relative w-full">
          <SearchIcon class="absolute left-3 top-2.5 w-5 h-5" />
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Tìm theo tên chương trình, mã code (VD: TET2025)..."
            class="w-full h-9 pl-10 pr-3 py-1 bg-[#f3f3f5] border-0 rounded-lg text-sm text-[#717182] placeholder-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689]"
          />
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-2 gap-4">
          <!-- Status Filter -->
          <button
            class="bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 flex items-center justify-between h-9 hover:bg-gray-200 transition-colors"
          >
            <span class="text-sm text-neutral-950">Tất cả trạng thái</span>
            <ChevronDownIcon />
          </button>

          <!-- Type Filter -->
          <button
            class="bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 flex items-center justify-between h-9 hover:bg-gray-200 transition-colors"
          >
            <span class="text-sm text-neutral-950">Tất cả loại hình</span>
            <ChevronDownIcon />
          </button>
        </div>
      </div>
    </div>

    <!-- Promotions Table -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-12">
        <svg
          class="animate-spin h-8 w-8 text-[#009689]"
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
      </div>

      <!-- Empty State -->
      <div
        v-else-if="promotions.length === 0"
        class="flex flex-col items-center justify-center py-12"
      >
        <!-- <img :src="iconVoucher" alt="" class="w-16 h-16 opacity-30 mb-4" /> -->
        <p class="text-lg text-gray-500 mb-2">Chưa có khuyến mãi nào</p>
        <!-- <p class="text-sm text-gray-400 mb-4">
          Tạo khuyến mãi đầu tiên để bắt đầu
        </p>
        <button
          @click="router.push('/admin/khuyen-mai/them-moi')"
          class="bg-[#00a63e] text-white rounded-lg px-4 py-2 flex items-center gap-2 text-sm font-medium hover:bg-[#008c35] transition-colors"
        >
          <AddIcon class="w-4 h-4" />
          Tạo khuyến mãi mới
        </button> -->
      </div>

      <!-- Table with Data -->
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-black/10">
              <th
                class="text-left text-sm font-medium text-neutral-950 py-2.5 px-2 w-[220px]"
              >
                Tên chương trình
              </th>
              <th
                class="text-left text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Mức giảm
              </th>
              <th
                class="text-left text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Loại hình
              </th>
              <th
                class="text-left text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Thời gian
              </th>
              <th
                class="text-left text-sm font-medium text-neutral-950 py-2.5 px-2 w-[180px]"
              >
                Lượt sử dụng
              </th>
              <th
                class="text-left text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Trạng thái
              </th>
              <th
                class="text-right text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Thao tác
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="promo in promotions"
              :key="promo.id"
              class="border-b border-black/10 hover:bg-gray-50 transition-colors last:border-0"
            >
              <!-- Name & Code -->
              <td class="py-4 px-2 align-top">
                <div class="flex flex-col gap-1">
                  <p class="text-sm text-[#101828] font-normal leading-5">
                    {{ promo.name }}
                  </p>
                  <p v-if="promo.code" class="text-sm text-[#6a7282] leading-5">
                    (Mã:
                    <span class="text-[#009689] font-normal">{{
                      promo.code
                    }}</span
                    >)
                  </p>
                </div>
              </td>

              <!-- Value -->
              <td class="py-4 px-2 align-top">
                <div class="flex flex-col gap-1">
                  <p class="text-sm text-[#101828] font-normal leading-5">
                    {{ promo.value }}
                  </p>
                  <p
                    class="text-xs text-[#6a7282] leading-4"
                    v-if="promo.maxDiscount"
                  >
                    Tối đa: {{ promo.maxDiscount }}
                  </p>
                </div>
              </td>

              <!-- Type -->
              <td class="py-4 px-2 align-top">
                <span
                  class="inline-flex items-center gap-2 px-2 py-1 rounded-lg border text-xs font-medium h-[22px]"
                  :class="getTypeClass(promo.type)"
                >
                  <!-- <img
                    :src="promo.type === 'voucher' ? iconVoucher : iconAuto"
                    alt=""
                    class="w-3 h-3"
                  /> -->
                  {{ promo.typeLabel }}
                </span>
              </td>

              <!-- Date -->
              <td class="py-4 px-2 align-top">
                <div class="flex items-start gap-2">
                  <span class="text-sm text-[#4a5565] leading-5">{{
                    promo.dateRange
                  }}</span>
                </div>
              </td>

              <!-- Usage Progress -->
              <td class="py-4 px-2 align-top">
                <div class="flex flex-col gap-2 w-full max-w-[160px]">
                  <div
                    class="flex justify-between items-center text-sm leading-5"
                  >
                    <span class="text-[#101828]"
                      >{{ promo.used }} / {{ promo.total }}</span
                    >
                    <span class="text-[#6a7282]"
                      >{{ Math.round((promo.used / promo.total) * 100) }}%</span
                    >
                  </div>
                  <div
                    class="w-full h-2 bg-[#030213]/20 rounded-full overflow-hidden"
                  >
                    <div
                      class="h-full bg-[#030213] rounded-full"
                      :style="{ width: `${(promo.used / promo.total) * 100}%` }"
                    ></div>
                  </div>
                </div>
              </td>

              <!-- Status -->
              <td class="py-4 px-2 align-top">
                <div class="flex flex-col gap-[7.5px]">
                  <span
                    class="inline-flex items-center justify-center px-2 py-0.5 rounded-lg border-0 text-xs font-medium w-fit h-[22px]"
                    :class="getStatusClass(promo.status)"
                  >
                    {{ promo.statusLabel }}
                  </span>

                  <!-- Toggle Switch -->
                  <div class="flex items-center gap-2">
                    <button
                      class="relative w-8 h-[18.4px] rounded-full transition-colors duration-200 ease-in-out focus:outline-none"
                      :class="promo.isEnabled ? 'bg-[#030213]' : 'bg-[#cbced4]'"
                      @click="toggleStatus(promo)"
                    >
                      <span
                        class="absolute top-[1px] w-4 h-4 bg-white rounded-full transition-transform duration-200 ease-in-out"
                        :class="promo.isEnabled ? 'left-[15px]' : 'left-[1px]'"
                      ></span>
                    </button>
                    <span class="text-xs text-[#4a5565] leading-4">{{
                      promo.isEnabled ? "Đang bật" : "Đã tắt"
                    }}</span>
                  </div>
                </div>
              </td>

              <!-- Actions -->
              <td class="py-4 px-2 align-top">
                <div class="flex items-center justify-end gap-2">
                  <!-- <button
                    class="w-9 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
                    title="Xem chi tiết"
                  >
                    <EyeIcon class="w-4 h-4" />
                  </button> -->
                  <button
                    @click="
                      router.push(`/admin/khuyen-mai/chinh-sua/${promo.id}`)
                    "
                    class="w-9 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
                    title="Chỉnh sửa"
                  >
                    <UpdateIcon class="w-4 h-4" />
                  </button>
                  <!-- <button 
                    @click="handleDelete(promo)"
                    class="w-9 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
                    title="Xóa"
                  >
                    <img :src="iconDelete" alt="" class="w-4 h-4" />
                  </button> -->
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="pagination.last_page > 1"
        class="flex items-center justify-between mt-6 pt-6 border-t border-black/10"
      >
        <div class="text-sm text-[#4a5565]">
          Hiển thị {{ pagination.from || 0 }} - {{ pagination.to || 0 }} trong
          tổng số {{ pagination.total }} khuyến mãi
        </div>
        <div class="flex items-center gap-2">
          <button
            @click="
              pagination.current_page--;
              loadPromotions();
            "
            :disabled="pagination.current_page === 1"
            class="px-3 py-1 border border-black/10 rounded-lg text-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Trước
          </button>
          <div class="flex items-center gap-1">
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="
                pagination.current_page = page;
                loadPromotions();
              "
              class="px-3 py-1 border rounded-lg text-sm"
              :class="
                page === pagination.current_page
                  ? 'bg-[#00a63e] text-white border-[#00a63e]'
                  : 'border-black/10 hover:bg-gray-50'
              "
            >
              {{ page }}
            </button>
          </div>
          <button
            @click="
              pagination.current_page++;
              loadPromotions();
            "
            :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1 border border-black/10 rounded-lg text-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Sau
          </button>
        </div>
      </div>
    </div>

    <!-- Modals could be added here -->
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useRouter } from "vue-router";
import { khuyenMaiAPI } from "@/utils/khuyenMai";

// Icon SVG
import AddIcon from "@/assets/svg/add.svg";
import SearchIcon from "@/assets/svg/search.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import EyeIcon from "@/assets/svg/eye.svg";
import UpdateIcon from "@/assets/svg/update.svg";

// Icons from Figma
const iconPlus =
  "https://www.figma.com/api/mcp/asset/bd491dc9-ee5e-4b10-88a8-62b03bef9093";
const iconSearch =
  "https://www.figma.com/api/mcp/asset/8df55cc2-682a-4eaf-baa1-55b3c1c1c558";
const iconChevronDown =
  "https://www.figma.com/api/mcp/asset/8d066934-07ff-48dd-abfa-6acf31386f7a";
const iconVoucher =
  "https://www.figma.com/api/mcp/asset/ad1dd362-a99c-4f98-a2de-06d8e30e7664";
const iconAuto =
  "https://www.figma.com/api/mcp/asset/00b0f86b-3ecd-43b4-b04c-bb40a7cbe478";
const iconCalendar =
  "https://www.figma.com/api/mcp/asset/bc5fe324-77bb-4170-975f-5a239914c750";
const iconEye =
  "https://www.figma.com/api/mcp/asset/0fa890c9-168a-4cb8-a147-33294ab5f8a1";
const iconEdit =
  "https://www.figma.com/api/mcp/asset/d29d4188-00c3-466a-be7b-334b9e84fe7a";
const iconDelete =
  "https://www.figma.com/api/mcp/asset/d7d8ae1f-948d-45bc-9bb4-944566004825";

const router = useRouter();
const searchQuery = ref("");
const promotions = ref([]);
const loading = ref(false);
const statusFilter = ref("");
const typeFilter = ref("");

// Pagination
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});

// Load promotions from API
const loadPromotions = async () => {
  loading.value = true;
  try {
    const params = {
      page: pagination.value.current_page,
      per_page: pagination.value.per_page,
    };

    if (searchQuery.value) {
      params.search = searchQuery.value;
    }

    if (statusFilter.value) {
      params.trang_thai = statusFilter.value;
    }

    if (typeFilter.value) {
      params.loai_khuyen_mai = typeFilter.value;
    }

    const response = await khuyenMaiAPI.getAll(params);
    console.log("API Response:", response.data);

    if (response.data.success) {
      const data = response.data.data;

      // Map API data to UI format
      promotions.value = data.data.map((item) => ({
        id: item.id,
        name: item.ten_khuyen_mai,
        code: item.ma_code || "",
        value: formatDiscountValue(item),
        maxDiscount: item.giam_toi_da ? formatCurrency(item.giam_toi_da) : "",
        type: item.loai_khuyen_mai === "ma_giam_gia" ? "voucher" : "auto",
        typeLabel:
          item.loai_khuyen_mai === "ma_giam_gia" ? "Voucher" : "Tự động",
        dateRange: formatDateRange(item.tu_ngay, item.den_ngay),
        used: item.so_luong_da_dung || 0,
        total: item.tong_so_luong || 0,
        status: mapStatus(item.trang_thai),
        statusLabel: getStatusLabel(item.trang_thai),
        isEnabled: item.trang_thai === "dang_chay",
        rawData: {
          ...item,
          dich_vu_ids: item.dich_vus ? item.dich_vus.map((dv) => dv.id) : [],
        },
      }));

      // Update pagination
      pagination.value = {
        current_page: data.current_page,
        last_page: data.last_page,
        per_page: data.per_page,
        total: data.total,
      };
    }
  } catch (error) {
    console.error("Error loading promotions:", error);
    alert("Có lỗi xảy ra khi tải danh sách khuyến mãi");
  } finally {
    loading.value = false;
  }
};

// Format discount value
const formatDiscountValue = (item) => {
  if (item.hinh_thuc_giam === "phan_tram") {
    return `${item.gia_tri_giam}%`;
  } else {
    return formatCurrency(item.gia_tri_giam);
  }
};

// Format currency
const formatCurrency = (value) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(value);
};

// Format date range
const formatDateRange = (startDate, endDate) => {
  const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  return `${formatDate(startDate)} - ${formatDate(endDate)}`;
};

// Map status from API to UI
const mapStatus = (trangThai) => {
  const statusMap = {
    dang_chay: "active",
    sap_dien_ra: "scheduled",
    da_ket_thuc: "expired",
    tam_dung: "paused",
  };
  return statusMap[trangThai] || "active";
};

// Get status label
const getStatusLabel = (trangThai) => {
  const labelMap = {
    dang_chay: "Đang chạy",
    sap_dien_ra: "Sắp diễn ra",
    da_ket_thuc: "Đã kết thúc",
    tam_dung: "Tạm dừng",
  };
  return labelMap[trangThai] || "Đang chạy";
};

// Helper for status styles
const getStatusClass = (status) => {
  const classes = {
    active: "bg-green-100 text-[#008236]",
    scheduled: "bg-blue-100 text-[#1447e6]",
    expired: "bg-gray-100 text-[#364153]",
    paused: "bg-[#ffedd4] text-[#ca3500]",
  };
  return classes[status] || "bg-gray-100 text-gray-700";
};

// Helper for type styles
const getTypeClass = (type) => {
  const classes = {
    voucher: "bg-purple-50 text-[#8200db] border-[#e9d4ff]",
    auto: "bg-amber-50 text-[#bb4d00] border-[#fee685]",
  };
  return classes[type] || "bg-gray-50 text-gray-700 border-gray-200";
};

// Toggle status
const toggleStatus = async (promo) => {
  try {
    // Gọi API changeStatus - backend sẽ tự động toggle trạng thái
    const response = await khuyenMaiAPI.changeStatus(promo.id);

    if (response.data.success) {
      // Cập nhật UI dựa trên response từ backend
      const updatedData = response.data.data;

      promo.isEnabled = updatedData.trang_thai === "dang_chay";
      promo.status = mapStatus(updatedData.trang_thai);
      promo.statusLabel = getStatusLabel(updatedData.trang_thai);
      promo.rawData.trang_thai = updatedData.trang_thai;

      alert(response.data.message);
    }
  } catch (error) {
    console.error("Error toggling status:", error);
    alert("Có lỗi xảy ra khi cập nhật trạng thái");
  }
};

// Delete promotion
const handleDelete = async (promo) => {
  if (confirm(`Bạn có chắc chắn muốn xóa khuyến mãi "${promo.name}"?`)) {
    try {
      await khuyenMaiAPI.delete(promo.id);
      alert("Xóa khuyến mãi thành công!");
      loadPromotions();
    } catch (error) {
      console.error("Error deleting promotion:", error);
      alert("Có lỗi xảy ra khi xóa khuyến mãi");
    }
  }
};

// Watch search query
watch(searchQuery, () => {
  // Debounce search
  setTimeout(() => {
    pagination.value.current_page = 1;
    loadPromotions();
  }, 500);
});

// Computed for visible pages
const visiblePages = computed(() => {
  const pages = [];
  const current = pagination.value.current_page;
  const total = pagination.value.last_page;

  // Show max 5 pages
  let start = Math.max(1, current - 2);
  let end = Math.min(total, current + 2);

  // Adjust if at the beginning or end
  if (current <= 3) {
    end = Math.min(5, total);
  }
  if (current >= total - 2) {
    start = Math.max(1, total - 4);
  }

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  return pages;
});

// Load data on mount
onMounted(() => {
  loadPromotions();
});
</script>

<style scoped>
.font-nunito {
  font-family: "Nunito Sans", sans-serif;
}
/* Custom scrollbar for table if needed, though Tailwind's overflow-auto handles basic scrolling */
</style>
