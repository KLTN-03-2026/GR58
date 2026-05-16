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

    <!-- Service Detail Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div
          v-if="showModal"
          class="fixed inset-0 z-50 flex items-center justify-center p-4"
          @keydown.escape="closeModal"
        >
          <div class="absolute inset-0 bg-black/50" @click="closeModal"></div>

          <div
            class="relative bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-lg z-10"
            role="dialog"
            aria-modal="true"
          >
            <!-- Close Button -->
            <button
              @click="closeModal"
              class="absolute top-4 right-4 z-20 w-8 h-8 flex items-center justify-center rounded-full bg-white/90 text-[#393E46] hover:bg-gray-100 transition-colors"
            >
              <X class="w-5 h-5" />
            </button>

            <!-- Modal Image -->
            <div class="relative w-full h-64 sm:h-72 bg-[#e5e7eb]">
              <img
                v-if="selectedService?.imageUrl"
                :src="selectedService.imageUrl"
                :alt="selectedService.ten"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
              </div>
              <!-- Popular badge -->
              <div
                v-if="selectedService?.isPopular"
                class="absolute top-4 left-4 bg-[#ff6900] text-white text-xs font-semibold px-3 py-1 rounded-lg"
              >
                Phổ biến
              </div>
              <!-- Status badge -->
              <div
                v-if="selectedService?.trang_thai !== 'kinh_doanh'"
                class="absolute top-4 right-14 bg-gray-700/80 text-white text-xs font-medium px-3 py-1 rounded-lg"
              >
                Tạm ngưng
              </div>
            </div>

            <!-- Modal Content -->
            <div class="p-6">
              <!-- Category -->
              <span class="inline-block text-xs font-medium text-[#5A9690] bg-[#5A9690]/10 px-2.5 py-1 rounded-lg mb-3">
                {{ selectedService?.ten_nhom || 'Dịch vụ' }}
              </span>

              <!-- Name -->
              <h2 class="text-2xl font-bold text-[#432323] leading-8 mb-4">
                {{ selectedService?.ten }}
              </h2>

              <!-- Description -->
              <p v-if="selectedService?.mo_ta" class="text-[#393E46] leading-6 mb-6">
                {{ selectedService.mo_ta }}
              </p>

              <!-- Info Grid -->
              <div class="flex flex-wrap gap-6 mb-6 pb-6 border-b border-[#E0D9D9]">
                <div class="flex items-center gap-2">
                  <div class="w-9 h-9 rounded-lg bg-[#009689]/10 flex items-center justify-center">
                    <span class="text-[#009689] text-sm font-bold">₫</span>
                  </div>
                  <div>
                    <p class="text-xs text-[#393E46]">Giá dịch vụ</p>
                    <p class="text-lg font-bold text-[#f54900]">{{ formatPrice(selectedService?.gia_tien) }}</p>
                  </div>
                </div>
                <div v-if="selectedService?.thoi_gian_thuc_hien" class="flex items-center gap-2">
                  <div class="w-9 h-9 rounded-lg bg-[#009689]/10 flex items-center justify-center">
                    <Clock class="w-4 h-4 text-[#009689]" />
                  </div>
                  <div>
                    <p class="text-xs text-[#393E46]">Thời gian</p>
                    <p class="text-sm font-semibold text-[#432323]">{{ formatDuration(selectedService.thoi_gian_thuc_hien) }}</p>
                  </div>
                </div>
              </div>

              <!-- Book Button -->
              <button
                :disabled="selectedService?.trang_thai !== 'kinh_doanh'"
                class="w-full h-11 bg-[#009689] text-white font-semibold rounded-lg flex items-center justify-center gap-2 hover:bg-[#008177] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                @click="bookFromModal"
              >
                <Calendar class="w-5 h-5" />
                Đặt Lịch Ngay
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Booking Appointment Modal -->
    <BookAppointment
      :is-open="showBookingModal"
      :initial-data="{
        serviceId: selectedServiceForBooking?.id,
        serviceName: selectedServiceForBooking?.ten
      }"
      @close="closeBookingModal"
      @confirm="onBookingConfirm"
    />

    <!-- Services Section with Filters -->
    <div class="flex gap-6 w-full max-w-[1216px] mx-auto">
      <!-- Sidebar Filters -->
      <div class="w-[304px] shrink-0">
        <div class="sticky top-24 bg-white border !border-gray-300 rounded-lg p-6">
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
        <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="i in 4" :key="i" class="bg-white border border-gray-200 rounded-lg overflow-hidden animate-pulse">
            <div class="h-48 bg-gray-200"></div>
            <div class="p-4 space-y-2">
              <div class="h-4 bg-gray-200 rounded w-3/4"></div>
              <div class="h-3 bg-gray-200 rounded w-1/2"></div>
              <div class="h-9 bg-gray-200 rounded mt-2"></div>
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
          <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
              v-for="service in paginatedServices"
              :key="service.id"
              class="bg-white border !border-gray-300 shadow-sm rounded-lg overflow-hidden flex flex-col cursor-pointer hover:shadow-md transition-shadow duration-200"
              @click="openModal(service)"
            >
              <!-- Service Image -->
              <div class="relative h-44 bg-[#e5e7eb]">
                <img
                  v-if="service.imageUrl"
                  :src="service.imageUrl"
                  :alt="service.ten"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full flex items-center justify-center">
                  <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div
                  v-if="service.isPopular"
                  class="absolute top-2 left-2 bg-[#ff6900] text-white text-xs font-medium px-2 py-0.5 rounded-lg"
                >
                  Phổ biến
                </div>
                <div
                  v-if="service.trang_thai !== 'kinh_doanh'"
                  class="absolute top-2 right-2 bg-gray-700/80 text-white text-xs font-medium px-2 py-0.5 rounded-lg"
                >
                  Tạm ngưng
                </div>
              </div>

              <!-- Service Content -->
              <div class="p-3 flex flex-col flex-1">
                <h3 class="text-sm font-semibold text-[#432323] leading-5 mb-0.5 line-clamp-1">
                  {{ service.ten }}
                </h3>
                <p class="text-xs text-[#6a7282] leading-4 mb-2">
                  {{ service.ten_nhom || 'Dịch vụ' }}
                </p>
                <p class="text-lg font-bold text-[#f54900] leading-6 mb-3">
                  {{ formatPrice(service.gia_tien) }}
                </p>
                <button
                  :disabled="service.trang_thai !== 'kinh_doanh'"
                  class="mt-auto w-full h-8 bg-[#009689] text-white text-sm font-medium rounded-lg flex items-center justify-center gap-2 hover:bg-[#008177] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                  @click.stop="bookService(service)"
                >
                  <Calendar class="w-3.5 h-3.5" />
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
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { Search, Clock, Calendar, ChevronLeft, ChevronRight, X } from 'lucide-vue-next';
import { dichVuService } from '@/services/dichVuService';
import BookAppointment from '@/views/customer/appointment/book-appointment/index.vue';

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

const API_BASE = import.meta.env.VITE_API_BASE?.replace('/api', '') || 'http://localhost:8001';

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
  // Thay vì redirect, mở popup đặt lịch ngay tại đây
  selectedServiceForBooking.value = service;
  showBookingModal.value = true;
};

const selectedService = ref(null);
const showModal = ref(false);

// State cho booking modal
const selectedServiceForBooking = ref(null);
const showBookingModal = ref(false);

const openModal = (service) => {
  selectedService.value = service;
  showModal.value = true;
  document.body.style.overflow = 'hidden';
};

const closeModal = () => {
  showModal.value = false;
  selectedService.value = null;
  document.body.style.overflow = '';
};

const bookFromModal = () => {
  if (selectedService.value) {
    bookService(selectedService.value);
    closeModal();
  }
};

const closeBookingModal = () => {
  showBookingModal.value = false;
  selectedServiceForBooking.value = null;
};

const onBookingConfirm = () => {
  // Booking đã thành công, đóng modal
  showBookingModal.value = false;
  selectedServiceForBooking.value = null;
};

const handleEscape = (e) => {
  if (e.key === 'Escape' && showModal.value) closeModal();
};

onMounted(() => {
  loadServices();
  document.addEventListener('keydown', handleEscape);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape);
  document.body.style.overflow = '';
});
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

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}

.modal-enter-active > div:last-child,
.modal-leave-active > div:last-child {
  transition: transform 0.2s ease, opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from > div:last-child,
.modal-leave-to > div:last-child {
  transform: scale(0.95);
  opacity: 0;
}
</style>
