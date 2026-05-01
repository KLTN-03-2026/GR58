<template>
  <div class="flex flex-col gap-12 items-center py-16 w-full">
    <!-- Hero Section with Background Image -->
    <div class="h-[580px] relative w-full overflow-hidden">
      <img
        src="/src/assets/img_imports/public_img/hp-pic15.png"
        alt="Pet Care Background"
        class="absolute w-full h-full object-cover mix-blend-multiply"
      />
      <div class="absolute inset-0 bg-black/50"></div>

      <div class="max-w-[1440px] mx-auto relative h-full w-full">
        <div class="absolute flex flex-col gap-9 left-[120px] top-28 w-[520px]">
          <p class="font-bold text-xl leading-7 text-white">Dịch Vụ</p>
          <p class="font-bold text-6xl leading-20 text-white">
            Hệ sinh thái chăm sóc toàn diện cho Thú Cưng
          </p>
          <p class="text-lg leading-7 text-white">
            Từ thăm khám, điều trị chuyên sâu đến Spa làm đẹp. Petty mang đến
            quy trình chuẩn y khoa giúp "Boss" luôn khỏe mạnh và hạnh phúc.
          </p>
        </div>
      </div>
    </div>

    <!-- Services Section with Filters -->
    <div class="flex gap-6 w-full max-w-[1216px] mx-auto">
      <!-- Sidebar Filters -->
      <div class="w-[304px] shrink-0">
        <div class="bg-white border !border-gray-300 rounded-[14px] p-6">
          <!-- Search -->
          <div class="flex flex-col gap-2 mb-6">
            <label class="text-sm font-medium text-[#364153] leading-[14px] tracking-[-0.15px]">
              Tìm kiếm
            </label>
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Tên dịch vụ..."
                class="w-full h-9 bg-[#f3f3f5] border !border-transparent rounded-lg pl-10 pr-3 py-1 text-sm text-[#717182] tracking-[-0.15px] focus:outline-none focus:ring-2 focus:ring-[#009689]"
              />
              <Search class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" />
            </div>
          </div>

          <!-- Category Filter (từ API) -->
          <div class="flex flex-col gap-3 mb-6">
            <label class="text-sm font-medium text-[#364153] leading-[14px] tracking-[-0.15px]">
              Danh mục
            </label>
            <div class="flex flex-col gap-2">
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  type="radio"
                  name="category"
                  :value="null"
                  v-model="selectedCategoryId"
                  class="w-4 h-4 accent-[#009689]"
                />
                <span class="text-sm text-[#364153] leading-5">Tất cả</span>
              </label>
              <label v-for="cat in categories" :key="cat.id" class="flex items-center gap-2 cursor-pointer">
                <input
                  type="radio"
                  name="category"
                  :value="cat.id"
                  v-model="selectedCategoryId"
                  class="w-4 h-4 accent-[#009689]"
                />
                <span class="text-sm text-[#364153] leading-5">{{ cat.ten_nhom }}</span>
              </label>
            </div>
          </div>

          <!-- Status Filter -->
          <div class="flex flex-col gap-3">
            <label class="text-sm font-medium text-[#364153] leading-[14px] tracking-[-0.15px]">
              Trạng thái
            </label>
            <div class="flex flex-col gap-2">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" v-model="showActive" class="w-4 h-4 bg-[#f3f3f5] border border-black/10 rounded shadow-sm" />
                <span class="text-sm text-[#364153] leading-5">Đang kinh doanh</span>
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Services Grid -->
      <div class="flex-1 flex flex-col gap-4">
        <!-- Loading State -->
        <div v-if="isLoading" class="grid grid-cols-3 gap-6">
          <div v-for="i in 6" :key="i" class="bg-white border border-gray-200 rounded-[14px] overflow-hidden animate-pulse">
            <div class="h-48 bg-gray-200"></div>
            <div class="p-4 space-y-3">
              <div class="h-4 bg-gray-200 rounded w-3/4"></div>
              <div class="h-3 bg-gray-200 rounded w-1/2"></div>
              <div class="h-5 bg-gray-200 rounded w-1/3"></div>
              <div class="h-9 bg-gray-200 rounded"></div>
            </div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="flex flex-col items-center justify-center py-20 gap-4">
          <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <p class="text-gray-600 font-medium">Không thể tải dịch vụ. Vui lòng thử lại.</p>
          <button @click="loadServices" class="bg-[#009689] text-white px-6 py-2 rounded-lg font-medium hover:bg-[#00897b] transition-colors">
            Thử lại
          </button>
        </div>

        <template v-else>
          <!-- Results Count -->
          <p class="text-sm text-[#4a5565] leading-5 tracking-[-0.15px]">
            Tìm thấy {{ filteredServices.length }} dịch vụ
          </p>

          <!-- Empty state -->
          <div v-if="filteredServices.length === 0" class="flex flex-col items-center justify-center py-20 gap-3">
            <div class="w-16 h-16 bg-gray-100 text-gray-300 rounded-full flex items-center justify-center">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-gray-500 font-medium">Không tìm thấy dịch vụ phù hợp</p>
            <button @click="resetFilters" class="text-[#009689] font-medium hover:underline text-sm">
              Xóa bộ lọc
            </button>
          </div>

          <!-- Service Cards Grid -->
          <div v-else class="grid grid-cols-3 gap-6">
            <div
              v-for="service in paginatedServices"
              :key="service.id"
              class="bg-white border !border-gray-300 shadow-sm rounded-[14px] overflow-hidden flex flex-col"
            >
              <!-- Service Image -->
              <div class="relative h-48 bg-[#e5e7eb]">
                <img
                  v-if="service.imageUrl"
                  :src="service.imageUrl"
                  :alt="service.ten"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full flex items-center justify-center">
                  <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div
                  v-if="service.isPopular"
                  class="absolute top-3 left-3 bg-[#ff6900] text-white text-xs font-medium px-2.5 py-1 rounded-lg leading-4"
                >
                  Phổ biến
                </div>
                <!-- Status badge -->
                <div
                  v-if="service.trang_thai !== 'kinh_doanh'"
                  class="absolute top-3 right-3 bg-gray-700/80 text-white text-xs font-medium px-2.5 py-1 rounded-lg"
                >
                  Tạm ngưng
                </div>
              </div>

              <!-- Service Content -->
              <div class="p-4 flex flex-col flex-1">
                <h3 class="text-base font-semibold text-[#101828] leading-6 tracking-[-0.31px] mb-1 line-clamp-2">
                  {{ service.ten }}
                </h3>
                <p class="text-xs text-[#6a7282] leading-4 mb-3">
                  {{ service.ten_nhom || 'Dịch vụ' }}
                </p>
                <p v-if="service.mo_ta" class="text-xs text-gray-500 leading-4 mb-3 line-clamp-2">
                  {{ service.mo_ta }}
                </p>
                <p class="text-xl font-semibold text-[#f54900] leading-7 tracking-[-0.45px] mb-4">
                  {{ formatPrice(service.gia_tien) }}
                </p>
                <div v-if="service.thoi_gian_thuc_hien" class="flex items-center gap-2 mb-4">
                  <Clock class="w-4 h-4 text-green-600" />
                  <span class="text-sm text-[#4a5565] leading-5 tracking-[-0.15px]">
                    {{ formatDuration(service.thoi_gian_thuc_hien) }}
                  </span>
                </div>
                <button
                  :disabled="service.trang_thai !== 'kinh_doanh'"
                  class="mt-auto w-full h-9 bg-[#009689] text-white text-sm font-medium rounded-lg flex items-center justify-center gap-2 hover:bg-[#00897b] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                  @click="bookService(service)"
                >
                  <Calendar class="w-4 h-4" />
                  Đặt Lịch
                </button>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="flex items-center justify-between mt-8">
            <p class="text-sm text-[#4a5565] leading-5 tracking-[-0.15px]">
              Hiển thị {{ startIndex + 1 }} - {{ endIndex }} của {{ filteredServices.length }} dịch vụ
            </p>
            <div class="flex items-center gap-1">
              <button
                :disabled="currentPage === 1"
                :class="['h-9 px-3 rounded-lg flex items-center gap-2 text-sm font-medium tracking-[-0.15px]', currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100']"
                @click="currentPage--"
              >
                <ChevronLeft class="w-4 h-4 text-gray-600" />
              </button>

              <button
                v-for="page in totalPages"
                :key="page"
                :class="['w-9 h-9 rounded-lg text-sm font-medium tracking-[-0.15px] flex items-center justify-center', page === currentPage ? 'bg-white border border-black/10 font-bold' : 'hover:bg-gray-100']"
                @click="currentPage = page"
              >
                {{ page }}
              </button>

              <button
                :disabled="currentPage === totalPages"
                :class="['h-9 px-3 rounded-lg flex items-center gap-2 text-sm font-medium tracking-[-0.15px]', currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100']"
                @click="currentPage++"
              >
                <ChevronRight class="w-4 h-4 text-gray-600" />
              </button>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { Search, Clock, Calendar, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { dichVuService } from '@/services/dichVuService';

const router = useRouter();

const services = ref([]);
const categories = ref([]);
const isLoading = ref(false);
const error = ref(false);

const searchQuery = ref('');
const selectedCategoryId = ref(null);
const showActive = ref(false);
const currentPage = ref(1);
const itemsPerPage = 9;

const API_BASE = import.meta.env.VITE_API_BASE?.replace('/api', '') || 'http://127.0.0.1:8000';

const getImageUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('http') || path.startsWith('data:')) return path;
  return `${API_BASE}/storage/${path}`;
};

const formatPrice = (price) => {
  if (!price && price !== 0) return 'Liên hệ';
  return new Intl.NumberFormat('vi-VN').format(price) + ' ₫';
};

const formatDuration = (minutes) => {
  if (!minutes) return '';
  if (minutes < 60) return `Khoảng ${minutes} phút`;
  const h = Math.floor(minutes / 60);
  const m = minutes % 60;
  return m > 0 ? `Khoảng ${h}h${m}p` : `Khoảng ${h} giờ`;
};

const loadServices = async () => {
  isLoading.value = true;
  error.value = false;
  try {
    const [svcRes, catRes] = await Promise.all([
      dichVuService.getAll(),
      dichVuService.getCategories(),
    ]);

    const rawServices = Array.isArray(svcRes) ? svcRes : (svcRes.data || []);
    const rawCategories = Array.isArray(catRes) ? catRes : (catRes.data || []);

    // Normalize services
    services.value = rawServices.map(s => ({
      ...s,
      imageUrl: getImageUrl(s.anh_dich_vu),
      isPopular: false,
    }));

    categories.value = rawCategories;
  } catch (err) {
    console.error('Error loading services:', err);
    error.value = true;
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  loadServices();
});

const filteredServices = computed(() => {
  return services.value.filter(s => {
    const matchSearch = !searchQuery.value || s.ten.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchCategory = !selectedCategoryId.value || s.danh_muc_id === selectedCategoryId.value;
    const matchStatus = !showActive.value || s.trang_thai === 'kinh_doanh';
    return matchSearch && matchCategory && matchStatus;
  });
});

const totalPages = computed(() => Math.ceil(filteredServices.value.length / itemsPerPage));
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage);
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, filteredServices.value.length));
const paginatedServices = computed(() => filteredServices.value.slice(startIndex.value, endIndex.value));

watch(filteredServices, () => {
  currentPage.value = 1;
});

const resetFilters = () => {
  searchQuery.value = '';
  selectedCategoryId.value = null;
  showActive.value = false;
};

const bookService = (service) => {
  router.push({
    path: '/customer/appointments/book',
    query: { service_id: service.id },
  });
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
