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

        <!-- Category Filter -->
        <div class="relative">
          <button
            @click="showCategoryFilter = !showCategoryFilter"
            class="bg-[#f3f3f5] rounded-lg h-9 px-3 py-px flex items-center justify-between gap-2 hover:bg-gray-200 transition-colors"
          >
            <span class="font-nunitoSans text-sm text-[#09090b] whitespace-nowrap">
              {{ selectedCategory || 'Tất cả Danh Mục' }}
            </span>
            <ChevronDownIcon />
          </button>
          <div v-if="showCategoryFilter" class="absolute top-10 left-0 w-48 z-50 bg-white border border-gray-200 rounded-lg shadow overflow-hidden">
            <button @click="selectedCategory = ''; showCategoryFilter = false"
              class="w-full text-left px-3 py-2 hover:bg-gray-100 text-sm transition-colors">
              Tất cả Danh Mục
            </button>
            <button v-for="cat in categoryOptions" :key="cat" @click="selectedCategory = cat; showCategoryFilter = false"
              class="w-full text-left px-3 py-2 hover:bg-gray-100 text-sm transition-colors">
              {{ cat }}
            </button>
          </div>
        </div>

        <!-- Status Filter -->
        <div class="relative">
          <button
            @click="showStatusFilter = !showStatusFilter"
            class="bg-[#f3f3f5] rounded-lg h-9 px-3 py-px flex items-center justify-between gap-2 hover:bg-gray-200 transition-colors"
          >
            <span class="font-nunitoSans text-sm text-[#09090b] whitespace-nowrap">
              {{ selectedStatus === '' ? 'Tất cả trạng thái' : (selectedStatus === 'active' ? 'Kinh doanh' : 'Ngừng') }}
            </span>
            <ChevronDownIcon />
          </button>
          <div v-if="showStatusFilter" class="absolute top-10 left-0 w-44 z-50 bg-white border border-gray-200 rounded-lg shadow overflow-hidden">
            <button @click="selectedStatus = ''; showStatusFilter = false"
              class="w-full text-left px-3 py-2 hover:bg-gray-100 text-sm transition-colors">
              Tất cả trạng thái
            </button>
            <button @click="selectedStatus = 'active'; showStatusFilter = false"
              class="w-full text-left px-3 py-2 hover:bg-gray-100 text-sm transition-colors">
              Kinh doanh
            </button>
            <button @click="selectedStatus = 'inactive'; showStatusFilter = false"
              class="w-full text-left px-3 py-2 hover:bg-gray-100 text-sm transition-colors">
              Ngừng
            </button>
          </div>
        </div>
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
              v-for="service in paginatedServices"
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
              <td class="py-2 px-2 h-[65px] align-middle max-w-[220px]">
                <div class="flex flex-col">
                  <p class="font-nunitoSans text-sm text-[#101828] m-0 font-medium">
                    {{ service.category }}
                  </p>
                  <p class="font-nunitoSans text-xs text-[#6a7282] m-0 line-clamp-2">
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
            <span v-if="filteredServices.length === 0">0</span>
            <span v-else>
              {{ (currentPage - 1) * perPage + 1 }} -
              {{ Math.min(currentPage * perPage, filteredServices.length) }}
            </span>
            của {{ filteredServices.length }} dịch vụ
          </p>
        </div>
        <div class="flex items-center gap-1 h-9">
          <button
            @click="currentPage--"
            :disabled="currentPage <= 1"
            class="bg-transparent rounded-lg px-3 py-2 h-9 flex items-center gap-1 hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <ChevronLeftIcon class="w-4 h-4" />
          </button>
          <template v-for="p in totalPages" :key="p">
            <button
              @click="currentPage = p"
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
            @click="currentPage++"
            :disabled="currentPage >= totalPages"
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
import { ref, onMounted, computed, watch } from "vue";
import api, { attachToken } from "@/utils/api";
import { showErrorToast } from "@/utils/toast";
import DanhMucDichVu from "./service-category/index.vue";
import ThemDichVu from "./add-service/index.vue";
import ChinhSuaDichVu from "./edit-service/index.vue";
import XoaDichVu from "./delete-service/index.vue";
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

const currentPage = ref(1);
const perPage = 5;

const searchQuery = ref("");
const selectedCategory = ref("");
const selectedStatus = ref("");
const showCategoryFilter = ref(false);
const showStatusFilter = ref(false);

const API_BASE = import.meta.env.VITE_API_BASE || "http://localhost:8001/api";
const API_ORIGIN = API_BASE.replace(/\/api\/?$/, "");

const mapApiToView = (item) => {
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

const fetchServices = async () => {
  loadingServices.value = true;
  try {
    const res = await api.get("/dich-vu");
    const items = (res && res.data && res.data.data) || [];
    services.value = items.map(mapApiToView);
  } catch (e) {
    console.error("fetchServices error", e);
  } finally {
    loadingServices.value = false;
  }
};

onMounted(() => { fetchServices(); });

const categoryOptions = computed(() => {
  const cats = services.value.map(s => s.category).filter(Boolean);
  return [...new Set(cats)];
});

const filteredServices = computed(() => {
  return services.value.filter(s => {
    const matchSearch = !searchQuery.value || s.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchCategory = !selectedCategory.value || s.category === selectedCategory.value;
    const matchStatus = !selectedStatus.value || s.status === selectedStatus.value;
    return matchSearch && matchCategory && matchStatus;
  });
});

const totalPages = computed(() => Math.max(1, Math.ceil(filteredServices.value.length / perPage)));

const paginatedServices = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  return filteredServices.value.slice(start, start + perPage);
});

watch([searchQuery, selectedCategory, selectedStatus], () => {
  currentPage.value = 1;
});

const isManageCategoriesModalOpen = ref(false);
const isAddServiceModalOpen = ref(false);
const isEditServiceModalOpen = ref(false);
const selectedServiceForEdit = ref(null);
const isDeleteServiceModalOpen = ref(false);
const selectedServiceForDelete = ref(null);
const deleteServiceModalType = ref("confirm");
const serviceAppointments = ref([]);

const formatPrice = (price) => {
  return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" })
    .format(price).replace("₫", "₫");
};

const handleManageCategories = () => { isManageCategoriesModalOpen.value = true; };
const handleAddService = () => { isAddServiceModalOpen.value = true; };

const handleSaveService = () => {
  isAddServiceModalOpen.value = false;
  fetchServices();
};

const handleEdit = (service) => {
  selectedServiceForEdit.value = service;
  isEditServiceModalOpen.value = true;
};

const handleUpdateService = () => {
  isEditServiceModalOpen.value = false;
  fetchServices();
};

const handleOpenCreateCategoryFromEdit = () => {
  isEditServiceModalOpen.value = false;
  isManageCategoriesModalOpen.value = true;
};

const handleDelete = (service) => {
  selectedServiceForDelete.value = service;
  deleteServiceModalType.value = "confirm";
  serviceAppointments.value = [];
  isDeleteServiceModalOpen.value = true;
};

const handleModalDeleted = (data) => {
  try {
    const id = data && data.serviceId;
    const index = services.value.findIndex((s) => s.id === id);
    if (index !== -1) services.value.splice(index, 1);
  } catch (e) {
    console.error("handleModalDeleted error", e);
  } finally {
    isDeleteServiceModalOpen.value = false;
  }
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
