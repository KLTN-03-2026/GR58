<template>
  <div class="flex flex-col gap-12 items-center py-16 w-full">
    <!-- Hero Section with Background Image -->
    <!-- Hero Section with Background Image -->
    <div class="h-[580px] relative w-full overflow-hidden">
      <!-- Background Image with Overlay -->
      <img
        src="/src/assets/img_imports/public_img/hp-pic15.png"
        alt="Pet Care Background"
        class="absolute w-full h-full object-cover mix-blend-multiply"
      />
      <div class="absolute inset-0 bg-black/50"></div>

      <!-- Hero Content -->
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
            <label
              class="text-sm font-medium text-[#364153] leading-[14px] tracking-[-0.15px]"
            >
              Tìm kiếm
            </label>
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Tên dịch vụ..."
                class="w-full h-9 bg-[#f3f3f5] border !border-transparent rounded-lg pl-10 pr-3 py-1 text-sm text-[#717182] tracking-[-0.15px] focus:outline-none focus:ring-2 focus:ring-[#009689]"
              />
              <Search
                class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"
              />
            </div>
          </div>

          <!-- Pet Type Filter -->
          <div class="flex flex-col gap-3 mb-6">
            <label
              class="text-sm font-medium text-[#364153] leading-[14px] tracking-[-0.15px]"
            >
              Dành cho ai?
            </label>
            <div class="flex flex-col gap-3">
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="selectedPetTypes"
                  type="checkbox"
                  value="dog"
                  class="w-4 h-4 bg-[#f3f3f5] border border-black/10 rounded shadow-sm"
                />
                <span
                  class="text-sm text-[#364153] leading-5 tracking-[-0.15px]"
                  >Chó</span
                >
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="selectedPetTypes"
                  type="checkbox"
                  value="cat"
                  class="w-4 h-4 bg-[#f3f3f5] border border-black/10 rounded shadow-sm"
                />
                <span
                  class="text-sm text-[#364153] leading-5 tracking-[-0.15px]"
                  >Mèo</span
                >
              </label>
            </div>
          </div>

          <!-- Service Type Filter -->
          <div class="flex flex-col gap-3">
            <label
              class="text-sm font-medium text-[#364153] leading-[14px] tracking-[-0.15px]"
            >
              Loại dịch vụ
            </label>
            <div class="flex flex-col gap-3">
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="selectedServiceTypes"
                  type="checkbox"
                  value="exam"
                  class="w-4 h-4 bg-[#f3f3f5] border border-black/10 rounded shadow-sm"
                />
                <span
                  class="text-sm text-[#364153] leading-5 tracking-[-0.15px]"
                  >Khám & Điều trị</span
                >
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="selectedServiceTypes"
                  type="checkbox"
                  value="vaccine"
                  class="w-4 h-4 bg-[#f3f3f5] border border-black/10 rounded shadow-sm"
                />
                <span
                  class="text-sm text-[#364153] leading-5 tracking-[-0.15px]"
                  >Tiêm phòng</span
                >
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="selectedServiceTypes"
                  type="checkbox"
                  value="spa"
                  class="w-4 h-4 bg-[#f3f3f5] border border-black/10 rounded shadow-sm"
                />
                <span
                  class="text-sm text-[#364153] leading-5 tracking-[-0.15px]"
                  >Spa & Grooming</span
                >
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="selectedServiceTypes"
                  type="checkbox"
                  value="boarding"
                  class="w-4 h-4 bg-[#f3f3f5] border border-black/10 rounded shadow-sm"
                />
                <span
                  class="text-sm text-[#364153] leading-5 tracking-[-0.15px]"
                  >Lưu chuồng/Trông giữ</span
                >
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="selectedServiceTypes"
                  type="checkbox"
                  value="surgery"
                  class="w-4 h-4 bg-[#f3f3f5] border border-black/10 rounded shadow-sm"
                />
                <span
                  class="text-sm text-[#364153] leading-5 tracking-[-0.15px]"
                  >Phẫu thuật & Xét nghiệm</span
                >
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Services Grid -->
      <div class="flex-1 flex flex-col gap-4">
        <!-- Results Count -->
        <p class="text-sm text-[#4a5565] leading-5 tracking-[-0.15px]">
          Tìm thấy {{ filteredServices.length }} dịch vụ
        </p>

        <!-- Service Cards Grid -->
        <div class="grid grid-cols-3 gap-6">
          <div
            v-for="service in paginatedServices"
            :key="service.id"
            class="bg-white border !border-gray-300 shadow-sm rounded-[14px] overflow-hidden flex flex-col"
          >
            <!-- Service Image -->
            <div class="relative h-48 bg-[#e5e7eb]">
              <img
                v-if="service.image"
                :src="service.image"
                :alt="service.name"
                class="w-full h-full object-cover"
              />
              <div
                v-if="service.isPopular"
                class="absolute top-3 left-3 bg-[#ff6900] text-white text-xs font-medium px-2.5 py-1 rounded-lg leading-4"
              >
                Phổ biến
              </div>
            </div>

            <!-- Service Content -->
            <div class="p-4 flex flex-col flex-1">
              <h3
                class="text-base font-normal text-[#101828] leading-6 tracking-[-0.31px] mb-2 line-clamp-2"
              >
                {{ service.name }}
              </h3>
              <p class="text-xs text-[#6a7282] leading-4 mb-4">
                {{ service.category }}
              </p>
              <p
                class="text-xl font-normal text-[#f54900] leading-7 tracking-[-0.45px] mb-4"
              >
                {{ service.price }}
              </p>
              <div class="flex items-center gap-2 mb-4">
                <Clock
                  class="w-4 h-4 text-green-600"
                />
                <span
                  class="text-sm text-[#4a5565] leading-5 tracking-[-0.15px]"
                >
                  {{ service.duration }}
                </span>
              </div>
              <button
                class="w-full h-9 bg-[#009689] text-white text-sm font-medium rounded-lg flex items-center justify-center gap-2 hover:bg-[#00897b] transition-colors"
                @click="bookService(service)"
              >
                <Calendar
                  class="w-4 h-4"
                />
                Đặt Lịch
              </button>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-8">
          <p class="text-sm text-[#4a5565] leading-5 tracking-[-0.15px]">
            Hiển thị {{ startIndex + 1 }} - {{ endIndex }} của
            {{ filteredServices.length }} dịch vụ
          </p>
          <div class="flex items-center gap-1">
            <button
              :disabled="currentPage === 1"
              :class="[
                'h-9 px-3 rounded-lg flex items-center gap-2 text-sm font-medium tracking-[-0.15px]',
                currentPage === 1
                  ? 'opacity-50 cursor-not-allowed'
                  : 'hover:bg-gray-100',
              ]"
              @click="currentPage--"
            >
              <ChevronLeft
                class="w-4 h-4 text-gray-600"
              />
            </button>

            <button
              v-for="page in totalPages"
              :key="page"
              :class="[
                'w-9 h-9 rounded-lg text-sm font-medium tracking-[-0.15px] flex items-center justify-center',
                page === currentPage
                  ? 'bg-white border border-black/10'
                  : 'hover:bg-gray-100',
              ]"
              @click="currentPage = page"
            >
              {{ page }}
            </button>

            <button
              :disabled="currentPage === totalPages"
              :class="[
                'h-9 px-3 rounded-lg flex items-center gap-2 text-sm font-medium tracking-[-0.15px]',
                currentPage === totalPages
                  ? 'opacity-50 cursor-not-allowed'
                  : 'hover:bg-gray-100',
              ]"
              @click="currentPage++"
            >
              <ChevronRight
                class="w-4 h-4 text-gray-600"
              />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Search, Clock, Calendar, ChevronLeft, ChevronRight } from 'lucide-vue-next';

export default {
  name: "DichVu",
  components: {
    Search,
    Clock,
    Calendar,
    ChevronLeft,
    ChevronRight
  },
  data() {
    return {
      searchQuery: "",
      selectedPetTypes: [],
      selectedServiceTypes: [],
      currentPage: 1,
      itemsPerPage: 9,
      services: [
        {
          id: 1,
          name: "Tiêm Vaccine 7 Bệnh (Chó)",
          category: "Tiêm phòng",
          price: "150.000 ₫",
          duration: "Khoảng 15 phút",
          isPopular: true,
          petType: "dog",
          serviceType: "vaccine",
          image: null,
        },
        {
          id: 2,
          name: "Tiêm Vaccine 4 Bệnh (Mèo)",
          category: "Tiêm phòng",
          price: "200.000 ₫",
          duration: "Khoảng 15 phút",
          isPopular: false,
          petType: "cat",
          serviceType: "vaccine",
          image: null,
        },
        {
          id: 3,
          name: "Tiêm Phòng Dại",
          category: "Tiêm phòng",
          price: "120.000 ₫",
          duration: "Khoảng 15 phút",
          isPopular: true,
          petType: "dog",
          serviceType: "vaccine",
          image: null,
        },
        {
          id: 4,
          name: "Khám Tổng Quát",
          category: "Khám & Điều trị",
          price: "150.000 ₫",
          duration: "Khoảng 30 phút",
          isPopular: true,
          petType: "dog",
          serviceType: "exam",
          image: null,
        },
        {
          id: 5,
          name: "Khám Chuyên Khoa Da Liễu",
          category: "Khám & Điều trị",
          price: "250.000 ₫",
          duration: "Khoảng 45 phút",
          isPopular: false,
          petType: "dog",
          serviceType: "exam",
          image: null,
        },
        {
          id: 6,
          name: "Spa Tắm & Cắt Tỉa (Chó Nhỏ)",
          category: "Spa & Grooming",
          price: "200.000 ₫",
          duration: "Khoảng 60 phút",
          isPopular: false,
          petType: "dog",
          serviceType: "spa",
          image: null,
        },
        {
          id: 7,
          name: "Spa Tắm & Cắt Tỉa (Chó Lớn)",
          category: "Spa & Grooming",
          price: "350.000 ₫",
          duration: "Khoảng 90 phút",
          isPopular: false,
          petType: "dog",
          serviceType: "spa",
          image: null,
        },
        {
          id: 8,
          name: "Spa Cao Cấp Cho Mèo",
          category: "Spa & Grooming",
          price: "300.000 ₫",
          duration: "Khoảng 90 phút",
          isPopular: true,
          petType: "cat",
          serviceType: "spa",
          image: null,
        },
        {
          id: 9,
          name: "Xét Nghiệm Máu Tổng Quát",
          category: "Phẫu thuật & Xét nghiệm",
          price: "250.000 ₫",
          duration: "Khoảng 30 phút",
          isPopular: false,
          petType: "dog",
          serviceType: "surgery",
          image: null,
        },
      ],
    };
  },
  computed: {
    filteredServices() {
      return this.services.filter((service) => {
        // Search filter
        const matchesSearch =
          !this.searchQuery ||
          service.name.toLowerCase().includes(this.searchQuery.toLowerCase());

        // Pet type filter
        const matchesPetType =
          this.selectedPetTypes.length === 0 ||
          this.selectedPetTypes.includes(service.petType);

        // Service type filter
        const matchesServiceType =
          this.selectedServiceTypes.length === 0 ||
          this.selectedServiceTypes.includes(service.serviceType);

        return matchesSearch && matchesPetType && matchesServiceType;
      });
    },
    totalPages() {
      return Math.ceil(this.filteredServices.length / this.itemsPerPage);
    },
    startIndex() {
      return (this.currentPage - 1) * this.itemsPerPage;
    },
    endIndex() {
      return Math.min(
        this.startIndex + this.itemsPerPage,
        this.filteredServices.length
      );
    },
    paginatedServices() {
      return this.filteredServices.slice(this.startIndex, this.endIndex);
    },
  },
  watch: {
    filteredServices() {
      // Reset to first page when filters change
      this.currentPage = 1;
    },
  },
  methods: {
    bookService(service) {
      this.$router.push({
        path: "/customer/appointments/book",
        query: { service_id: service.id },
      });
    },
  },
  mounted() {
    console.log("Trang Dịch Vụ đã được load!");
  },
};
</script>

<style scoped>
/* Import Nunito Sans font */
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}

/* Hide default checkbox */
input[type="checkbox"] {
  appearance: none;
  -webkit-appearance: none;
  cursor: pointer;
}

input[type="checkbox"]:checked {
  background-color: #009689;
  border-color: #009689;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3E%3Cpath d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: center;
  background-size: 12px;
}
</style>