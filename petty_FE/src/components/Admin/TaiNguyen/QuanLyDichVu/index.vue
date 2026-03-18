<template>
  <div class="flex flex-col gap-6 px-8 py-6 w-full">
    <!-- Page Header -->
    <div class="flex flex-col gap-1 items-start">
      <h1 class="font-semibold text-2xl text-black">Quản lý Dịch Vụ</h1>
      <p class="font-medium text-base text-gray-500">
        Tổ chức và phân bổ nhân sự theo bộ phận
      </p>
    </div>

    <!-- Card Container -->
    <div
      class="bg-white border !border-black/15 shadow-sm rounded-[14px] p-4 min-h-[662px]"
    >
      <!-- Card Header with Actions -->
      <div class="flex items-center justify-between h-9 p-6">
        <h3
          class="font-nunito text-base leading-4 text-neutral-950 tracking-tight"
        >
          Danh sách dịch vụ
        </h3>
        <div class="flex items-center gap-3">
          <button
            @click="handleManageCategories"
            class="bg-white border !border-[#5a9690] rounded-lg px-3 py-2 flex items-center gap-2 hover:bg-gray-50 transition-colors"
          >
            <FolderIcon class="w-4 h-4 text-[#009689]" />
            <span class="font-medium text-sm text-[#009689]">
              Danh Mục Dịch Vụ
            </span>
          </button>
          <button
            @click="handleAddService"
            class="bg-[#5a9690] rounded-lg px-3 py-2 flex items-center gap-2 hover:bg-[#4a7f79] transition-colors"
          >
            <AddIcon class="w-4 h-4 text-white" />
            <span class="font-nunitoSans font-medium text-sm text-white">
              Thêm dịch vụ
            </span>
          </button>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="flex items-center gap-4 h-9 px-6 mt-7">
        <div class="relative w-96 h-9">
          <SearchIcon
            class="absolute left-3 top-2.5 w-4 h-4 pointer-events-none"
          />
          <input
            v-model="searchQuery"
            type="text"
            class="bg-[#f3f3f5] rounded-lg h-9 w-full pl-10 pr-3 py-1 font-nunitoSans text-sm text-black placeholder:text-[#717182] outline-none"
            placeholder="Tìm tên dịch vụ..."
          />
        </div>

        <button
          @click="toggleGroupFilter"
          class="bg-[#f3f3f5] rounded-lg h-9 px-3 py-px flex items-center justify-between gap-2 opacity-50 cursor-not-allowed"
          disabled
        >
          <span
            class="font-nunitoSans text-sm text-[#09090b] whitespace-nowrap"
          >
            Tất cả Danh Mục
          </span>
          <ChevronDownIcon />
        </button>

        <button
          @click="toggleStatusFilter"
          class="bg-[#f3f3f5] rounded-lg h-9 px-3 py-px flex items-center justify-between gap-2 hover:bg-gray-200 transition-colors"
        >
          <span
            class="font-nunitoSans text-sm text-[#09090b] whitespace-nowrap"
          >
            Tất cả trạng thái
          </span>
          <ChevronDownIcon />
        </button>
      </div>

      <!-- Services Table -->
      <div class="px-6 mt-6 overflow-hidden max-h-[429.5px]">
        <table class="w-full border-collapse">
          <thead>
            <tr class="border-b border-black/10">
              <th
                class="font-nunitoSans font-medium text-sm text-[#09090b] text-left py-2.5 px-2 h-10 w-[91.555px]"
              >
                Ảnh
              </th>
              <th
                class="font-nunitoSans font-medium text-sm text-[#09090b] text-left py-2.5 px-2 h-10 w-[291.383px]"
              >
                Tên dịch vụ
              </th>
              <th
                class="font-nunitoSans font-medium text-sm text-[#09090b] text-left py-2.5 px-2 h-10 w-[167.813px]"
              >
                Phân loại
              </th>
              <th
                class="font-nunitoSans font-medium text-sm text-[#09090b] text-left py-2.5 px-2 h-10 w-[119.219px]"
              >
                Giá bán
              </th>
              <th
                class="font-nunitoSans font-medium text-sm text-[#09090b] text-left py-2.5 px-2 h-10 w-[109.695px]"
              >
                Thời gian
              </th>
              <th
                class="font-nunitoSans font-medium text-sm text-[#09090b] text-left py-2.5 px-2 h-10 w-[163.266px]"
              >
                Trạng thái
              </th>
              <th
                class="font-nunitoSans font-medium text-sm text-[#09090b] text-right py-2.5 px-2 h-10 w-[143.07px]"
              >
                Thao tác
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="service in services"
              :key="service.id"
              class="border-b border-black/10"
            >
              <!-- Image -->
              <td class="py-2 px-2 h-[65px] align-middle">
                <img
                  :src="service.image"
                  alt=""
                  class="w-12 h-12 rounded-[10px] object-cover"
                />
              </td>

              <!-- Name and Code -->
              <td class="py-2 px-2 h-[65px] align-middle">
                <div class="flex flex-col">
                  <p class="font-nunitoSans text-sm text-[#101828] m-0">
                    {{ service.name }}
                  </p>
                  <p
                    class="font-nunitoSans text-xs text-[#6a7282] m-0 whitespace-pre-wrap"
                  >
                    (Mã: {{ service.code }})
                  </p>
                </div>
              </td>

              <!-- Category -->
              <td class="py-2 px-2 h-[65px] align-middle">
                <div class="flex flex-col">
                  <p class="font-nunitoSans text-sm text-[#101828] m-0">
                    {{ service.category }}
                  </p>
                  <p
                    class="font-nunitoSans text-xs text-[#6a7282] m-0 whitespace-pre-wrap"
                  >
                    {{ service.mo_ta }}
                  </p>
                </div>
              </td>

              <!-- Price -->
              <td class="py-2 px-2 h-[65px] align-middle">
                <p class="font-nunitoSans text-sm text-[#101828] m-0">
                  {{ formatPrice(service.price) }}
                </p>
              </td>

              <!-- Duration -->
              <td class="py-2 px-2 h-[65px] align-middle">
                <p
                  class="font-nunitoSans text-sm text-[#4a5565] m-0 whitespace-pre-wrap"
                >
                  {{ service.duration }} phút
                </p>
              </td>

              <!-- Status -->
              <td class="py-2 px-2 h-[65px] align-middle">
                <span
                  class="inline-block px-2 py-0.5 rounded-lg font-nunitoSans font-medium text-xs text-center"
                  :class="{
                    'bg-[#dcfce7] text-[#008236]': service.status === 'active',
                    'bg-gray-100 text-[#364153]': service.status === 'inactive',
                  }"
                >
                  {{ service.status === "active" ? "Kinh doanh" : "Ngừng" }}
                </span>
              </td>

              <!-- Actions -->
              <td class="py-2 px-2 h-[65px] align-middle">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="handleEdit(service)"
                    :title="
                      service.status === 'active' ? 'Xem chi tiết' : 'Chỉnh sửa'
                    "
                    class="bg-white border !border-black/15 rounded-lg w-[38px] h-8 flex items-center justify-center p-px hover:bg-gray-50 transition-colors"
                  >
                    <UpdateIcon class="w-4 h-4" />
                  </button>
                  <button
                    @click="handleDelete(service)"
                    title="Xóa"
                    class="bg-white border !border-black/15 rounded-lg w-[38px] h-8 flex items-center justify-center p-px hover:bg-gray-50 transition-colors"
                  >
                    <TrashIcon class="w-4 h-4 text-red-500" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between h-10 px-6 mt-6">
        <div class="h-10 flex items-center">
          <p class="font-nunitoSans text-sm text-[#4a5565] m-0">
            Hiển thị
            <span v-if="totalItems === 0">0</span>
            <span v-else>
              {{ (currentPage - 1) * perPage + 1 }} -
              {{ Math.min(currentPage * perPage, totalItems) }}
            </span>
            của {{ totalItems }} dịch vụ
          </p>
        </div>
        <div class="flex items-center gap-1 h-9">
          <button
            @click="handlePrevPage"
            :disabled="currentPage <= 1 || loadingServices"
            class="bg-transparent rounded-lg px-3 py-2 h-9 flex items-center gap-1 hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <ChevronLeftIcon class="w-4 h-4" />
          </button>
          <template v-for="p in pages" :key="p">
            <button
              @click="goToPage(p)"
              :disabled="p === currentPage || loadingServices"
              class="bg-white border rounded-lg w-9 h-9 flex items-center justify-center font-nunitoSans font-medium text-sm text-[#09090b] hover:bg-gray-50 transition-colors"
              :class="{
                '!border-[#5a9690]': p === currentPage,
                '!border-black/10': p !== currentPage,
              }"
            >
              {{ p }}
            </button>
          </template>
          <button
            @click="handleNextPage"
            :disabled="currentPage >= lastPage || loadingServices"
            class="bg-transparent rounded-lg px-3 py-2 h-9 flex items-center gap-1 hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <ChevronRightIcon class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Manage Categories Modal -->
    <DanhMucDichVu
      v-if="isManageCategoriesModalOpen"
      @close="isManageCategoriesModalOpen = false"
    />

    <!-- Add Service Modal -->
    <div
      v-if="isAddServiceModalOpen"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-[1000] pt-24"
    >
      <div class="w-[600px] max-h-[85vh] flex flex-col shadow-xl">
        <ThemDichVu
          @close="isAddServiceModalOpen = false"
          @save="handleSaveService"
        />
      </div>
    </div>

    <!-- Edit Service Modal -->
    <div
      v-if="isEditServiceModalOpen"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-[1000] pt-24"
    >
      <div class="w-[600px] max-h-[85vh] flex flex-col shadow-xl">
        <ChinhSuaDichVu
          :service="selectedServiceForEdit"
          @close="isEditServiceModalOpen = false"
          @update="handleUpdateService"
          @openCreateCategory="handleOpenCreateCategoryFromEdit"
        />
      </div>
    </div>

    <!-- Delete Service Modal -->
    <div
      v-if="isDeleteServiceModalOpen"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[1000]"
    >
      <XoaDichVu
        :modal-type="deleteServiceModalType"
        :service="selectedServiceForDelete"
        :appointments="serviceAppointments"
        @close="isDeleteServiceModalOpen = false"
        @deleted="handleModalDeleted"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import api, { attachToken } from "@/utils/api";
import { showErrorToast } from "@/utils/toast";
import DanhMucDichVu from "./DanhMucDichVu/index.vue";
import ThemDichVu from "./ThemDichVu/index.vue";
import ChinhSuaDichVu from "./ChinhSuaDichVu/index.vue";
import XoaDichVu from "./XoaDichVu/index.vue";
// Icon SVG
import FolderIcon from "@/assets/svg/folder.svg";
import AddIcon from "@/assets/svg/add.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import SearchIcon from "@/assets/svg/search.svg";
import UpdateIcon from "@/assets/svg/update.svg";
import TrashIcon from "@/assets/svg/trash.svg";
import ChevronLeftIcon from "@/assets/svg/chevron-left.svg";
import ChevronRightIcon from "@/assets/svg/chevron-right.svg";

const services = ref([]);
const loadingServices = ref(false);

// Pagination state
const currentPage = ref(1);
const perPage = ref(6); // show 6 per page by default
const totalItems = ref(0);
const lastPage = ref(1);

// API origin used to build absolute image URLs when backend returns relative paths
const API_BASE = import.meta.env.VITE_API_BASE || "http://127.0.0.1:8000/api";
const API_ORIGIN = API_BASE.replace(/\/api\/?$/, "");

const mapApiToView = (item) => {
  // decide image
  let img = item.anh_dich_vu || item.image || "";
  if (img && !/^https?:\/\//i.test(img)) {
    if (!img.startsWith("/")) img = "/" + img;
    img = API_ORIGIN + img;
  }

  return {
    id: item.id,
    name: item.ten || item.name || "",
    code: item.ma_dich_vu || item.code || "",
    category: item.ten_nhom || (item.danh_muc && item.danh_muc.ten_nhom) || "",
    mo_ta: item.mo_ta || item.description || "",
    price: item.gia_tien || 0,
    duration: item.thoi_gian_thuc_hien || item.duration || 0,
    status: item.trang_thai === "kinh_doanh" ? "active" : "inactive",
    image: img,
  };
};

const fetchServices = async (page = currentPage.value) => {
  loadingServices.value = true;
  try {
    // Request paginated list
    const res = await api.get(
      `/dich-vu?per_page=${perPage.value}&page=${page}`
    );
    const items = (res && res.data && res.data.data) || [];
    services.value = items.map(mapApiToView);

    // read pagination meta when provided by backend
    const meta = (res && res.data && res.data.meta) || null;
    if (meta) {
      currentPage.value = meta.current_page || page;
      perPage.value = meta.per_page || perPage.value;
      totalItems.value = meta.total || totalItems.value;
      lastPage.value = meta.last_page || lastPage.value;
    } else {
      // fallback: if backend returned array without meta, set defaults
      currentPage.value = page;
      totalItems.value = services.value.length;
      lastPage.value = 1;
    }
  } catch (e) {
    console.error("fetchServices error", e);
    // optional: show toast if utils/toast available
  } finally {
    loadingServices.value = false;
  }
};

onMounted(() => {
  fetchServices();
});

const handlePrevPage = () => {
  if (currentPage.value > 1) {
    fetchServices(currentPage.value - 1);
  }
};

const handleNextPage = () => {
  if (currentPage.value < lastPage.value) {
    fetchServices(currentPage.value + 1);
  }
};

const pages = computed(() => {
  const n = Number(lastPage.value) || 1;
  const arr = [];
  for (let i = 1; i <= n; i++) arr.push(i);
  return arr;
});

const goToPage = (p) => {
  if (p === currentPage.value || loadingServices.value) return;
  fetchServices(p);
};

const searchQuery = ref("");
const isManageCategoriesModalOpen = ref(false);
const isAddServiceModalOpen = ref(false);
const isEditServiceModalOpen = ref(false);
const selectedServiceForEdit = ref(null);
const isDeleteServiceModalOpen = ref(false);
const selectedServiceForDelete = ref(null);
const deleteServiceModalType = ref("confirm"); // 'error' or 'confirm'
const serviceAppointments = ref([]);

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  })
    .format(price)
    .replace("₫", "₫");
};

const handleManageCategories = () => {
  isManageCategoriesModalOpen.value = true;
};

const handleAddService = () => {
  isAddServiceModalOpen.value = true;
};

const handleSaveService = (data) => {
  // data is the backend-created item (controller returns created resource)
  try {
    const view = mapApiToView(data);
    services.value.unshift(view);
    // update total count if we have a server-side total
    totalItems.value = (Number(totalItems.value) || 0) + 1;
  } catch (e) {
    console.error("handleSaveService mapping error", e);
  } finally {
    isAddServiceModalOpen.value = false;
  }
};

const toggleDepartmentFilter = () => {
  console.log("Toggle department filter");
};

const toggleGroupFilter = () => {
  console.log("Toggle group filter");
};

const toggleStatusFilter = () => {
  console.log("Toggle status filter");
};

const handleEdit = (service) => {
  selectedServiceForEdit.value = service;
  isEditServiceModalOpen.value = true;
};

const handleUpdateService = (data) => {
  try {
    const view = mapApiToView(data);
    const index = services.value.findIndex((s) => s.id === view.id);
    if (index !== -1) {
      services.value[index] = { ...services.value[index], ...view };
    }
  } catch (e) {
    console.error("handleUpdateService error", e);
  } finally {
    isEditServiceModalOpen.value = false;
  }
};

const handleOpenCreateCategoryFromEdit = () => {
  isEditServiceModalOpen.value = false;
  isManageCategoriesModalOpen.value = true;
};

const handleDelete = (service) => {
  selectedServiceForDelete.value = service;
  // Always allow deletion from the UI (open confirm modal)
  deleteServiceModalType.value = "confirm";
  serviceAppointments.value = [];
  isDeleteServiceModalOpen.value = true;
};

const handleSubmitDeleteService = async (data) => {
  try {
    try {
      attachToken();
    } catch (_) {}
    const res = await api.delete(`/dich-vu/${data.serviceId}`);
    if (res && res.data && res.data.status) {
      const index = services.value.findIndex((s) => s.id === data.serviceId);
      if (index !== -1) services.value.splice(index, 1);
      // decrement totalItems to keep pagination info in sync
      totalItems.value = Math.max((Number(totalItems.value) || 0) - 1, 0);
    } else {
      // Try to show backend message when delete failed
      const msg =
        (res && res.data && res.data.message) || "Có lỗi khi xóa dịch vụ.";
      showErrorToast("Lỗi", msg);
      console.warn("Delete failed", res);
    }
  } catch (e) {
    console.error("handleSubmitDeleteService error", e);
    // Extract message from error response if available and show to user
    const msg =
      e && e.response && e.response.data && e.response.data.message
        ? e.response.data.message
        : "Có lỗi khi xóa dịch vụ.";
    showErrorToast("Lỗi", msg);
  } finally {
    isDeleteServiceModalOpen.value = false;
  }
};

// Handler for when XoaDichVu emits 'deleted' after successful delete
const handleModalDeleted = (data) => {
  try {
    const id = data && data.serviceId;
    const index = services.value.findIndex((s) => s.id === id);
    if (index !== -1) services.value.splice(index, 1);
    // decrement totalItems so the UI shows the updated total
    totalItems.value = Math.max((Number(totalItems.value) || 0) - 1, 0);
  } catch (e) {
    console.error("handleModalDeleted error", e);
  } finally {
    isDeleteServiceModalOpen.value = false;
  }
};
</script>

<style scoped>
/* Minimal custom styles - most styling is now in Tailwind classes */
</style>
