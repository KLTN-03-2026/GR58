<template>
  <footer class="relative w-full min-h-[1672px] overflow-hidden bg-[#5a9690]">
    <!-- Background Decorative Grid -->
    <div
      class="absolute top-[-259px] left-[-200px] w-[2172px] h-[1236px] rotate-[15deg]  opacity-30 pointer-events-none z-0 hidden lg:block ">
      <div class="grid grid-cols-7 grid-rows-5 gap-12">
        <template v-for="(item, index) in footerGridItems" :key="index">
          <div v-if="item.type === 'placeholder'" class="bg-gray-300 rounded-[36px] overflow-hidden"></div>
          <div v-else class="rounded-[36px] overflow-hidden group cursor-pointer">
            <img :src="item.src" :alt="item.alt"
              class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-110" />
          </div>
        </template>
      </div>
    </div>

    <!-- Main Content -->
    <div
      class="relative z-10 h-[1672px] flex flex-col items-center gap-[500px] px-6 py-24 md:px-16 md:py-20 lg:px-32 lg:py-24 bg-[linear-gradient(to_right,#5a9690_0%,rgba(90,150,144,1)_10%,rgba(90,150,144,0.1)_50%,rgba(90,150,144,1)_90%,#5a9690_100%)]">
      <!-- CTA Section -->
      <div class="flex w-full max-w-5xl flex-col items-center gap-24 px-6 py-32 md:gap-16 md:py-20">
        <div class="flex w-full flex-col items-center gap-6">
          <!-- Badge -->
          <div class="flex items-center gap-1.5 rounded-full border border-white bg-white/20 px-3 py-1.5 text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/40">
            <span class="font-nunito font-medium text-sm">Bắt Đầu</span>
          </div>

          <!-- Title + Description -->
          <div class="flex w-full max-w-3xl flex-col items-center gap-8 text-center">
            <h1 class="text-5xl leading-tight md:text-6xl lg:text-7xl">
              <span class="font-nunito font-extrabold text-gray-100">Chăm sóc thú cưng dễ dàng hơn với PETTY</span>
            </h1>
            <p class="font-dmSans text-base leading-7 text-gray-100 md:text-lg lg:max-w-2xl">
              Từ đặt lịch, tra cứu hồ sơ đến theo dõi sức khỏe – tất cả chỉ trong một nền tảng. Giúp bạn đồng hành cùng thú cưng mọi lúc, mọi nơi, với sự hỗ trợ chuyên nghiệp từ đội ngũ bác sĩ thú y.
            </p>
          </div>

          <!-- CTA Button - Siêu mượt -->
          <button
            class="group flex items-center gap-2.5 rounded-2xl bg-gradient-to-r from-gray-100 to-gray-50 px-6 py-3 shadow-lg transition-all duration-300 ease-out hover:-translate-y-1.5 hover:shadow-2xl hover:from-gray-50 hover:to-white">
            <Rocket class="h-6 w-6 text-[#2f5755]" />
            <span class="font-nunitoSans text-base font-bold text-[#2f5755] tracking-wide">Đặt lịch ngay</span>
          </button>
        </div>
      </div>

      <!-- Footer Info -->
      <div
        class="flex flex-col items-center gap-16 md:flex-row md:items-start md:justify-between md:gap-32">
        <!-- Brand Section -->
        <div class="flex flex-col gap-10">
          <!-- Logo + Mô tả -->
          <div class="flex flex-col gap-5">
            <div class="rounded-xl md:w-64 bg-white/80 p-2 md:p-4 backdrop-blur-sm shadow-md transition-transform duration-300 hover:scale-105">
              <img src="/src/assets/img_imports/Petty Logo.png"
                alt="Dog Icon" class="h-full w-full" />
            </div>
            <p class="max-w-md font-nunitoSans w-full text-md font-semibold leading-7 text-white opacity-90 md:w-97">
              Sứ mệnh của chúng tôi là mang lại trải nghiệm chăm sóc thú cưng toàn diện, hiện đại và dễ dàng hơn cho mọi chủ nuôi – thông qua hệ thống quản lý thông minh và đội ngũ bác sĩ tận tâm.
            </p>
          </div>

          <!-- Social Links -->
          <div class="w-80">
            <p class="font-nunitoSans text-lg font-extrabold leading-8 text-gray-100">
              Theo dõi chúng tôi
            </p>
            <div class="mt-3 flex gap-4">
              <a href="#" aria-label="YouTube"
                class="group flex h-12 w-13 items-center justify-center rounded-full bg-white/90 p-3 shadow-md transition-all duration-300 ease-out hover:-translate-y-1 hover:bg-white hover:shadow-xl hover:ring-4 hover:ring-white/30">
                <img src="https://cdn-icons-png.flaticon.com/512/174/174883.png" alt="YouTube"
                  class="h-6 w-6 transition-transform duration-300 group-hover:scale-110" />
              </a>
              <a href="#" aria-label="LinkedIn"
                class="group flex h-12 w-13 items-center justify-center rounded-full bg-white/90 p-3 shadow-md transition-all duration-300 ease-out hover:-translate-y-1 hover:bg-white hover:shadow-xl hover:ring-4 hover:ring-white/30">
                <img src="https://cdn-icons-png.flaticon.com/512/174/174857.png" alt="LinkedIn"
                  class="h-6 w-6 transition-transform duration-300 group-hover:scale-110" />
              </a>
              <a href="#" aria-label="Instagram"
                class="group flex h-12 w-13 items-center justify-center rounded-full bg-white/90 p-3 shadow-md transition-all duration-300 ease-out hover:-translate-y-1 hover:bg-white hover:shadow-xl hover:ring-4 hover:ring-white/30">
                <img src="https://cdn-icons-png.flaticon.com/512/174/174855.png" alt="Instagram"
                  class="h-6 w-6 transition-transform duration-300 group-hover:scale-110" />
              </a>
              <a href="#" aria-label="Facebook"
                class="group flex h-12 w-13 items-center justify-center rounded-full bg-white/90 p-3 shadow-md transition-all duration-300 ease-out hover:-translate-y-1 hover:bg-white hover:shadow-xl hover:ring-4 hover:ring-white/30">
                <img src="https://cdn-icons-png.flaticon.com/512/174/174848.png" alt="Facebook"
                  class="h-6 w-6 transition-transform duration-300 group-hover:scale-110" />
              </a>
            </div>
          </div>
        </div>

        <!-- Columns -->
        <div class="flex w-full max-w-2xl flex-col gap-10 md:w-[641px] md:flex-row md:gap-16">
          <!-- Quick Links -->
          <div class="flex-1">
            <h3 class="font-nunito text-sm font-extrabold text-gray-100 underline underline-offset-[6px] decoration-white/50">
              Liên Kết Nhanh
            </h3>
            <div class="mt-5 flex flex-col gap-3">
              <a href="#" class="group flex items-center gap-2 text-gray-100 transition-all duration-300 ease-out hover:text-white hover:translate-x-2">
                <span class="font-nunitoSans text-sm font-semibold underline-offset-4 group-hover:underline">Trang Chủ</span>
                <img src="https://cdn-icons-png.flaticon.com/512/545/545682.png" alt="Arrow"
                  class="h-1.5 w-1.5 opacity-70 transition-all duration-300 group-hover:translate-x-2 group-hover:opacity-100" />
              </a>
              <a href="#" class="group flex items-center gap-2 text-gray-100 transition-all duration-300 ease-out hover:text-white hover:translate-x-2">
                <span class="font-nunitoSans text-sm font-semibold underline-offset-4 group-hover:underline">Dịch Vụ</span>
                <img src="https://cdn-icons-png.flaticon.com/512/545/545682.png" alt="Arrow"
                  class="h-1.5 w-1.5 opacity-70 transition-all duration-300 group-hover:translate-x-2 group-hover:opacity-100" />
              </a>
              <a href="#" class="group flex items-center gap-2 text-gray-100 transition-all duration-300 ease-out hover:text-white hover:translate-x-2">
                <span class="font-nunitoSans text-sm font-semibold underline-offset-4 group-hover:underline">Giới Thiệu</span>
                <img src="https://cdn-icons-png.flaticon.com/512/545/545682.png" alt="Arrow"
                  class="h-1.5 w-1.5 opacity-70 transition-all duration-300 group-hover:translate-x-2 group-hover:opacity-100" />
              </a>
              <a href="#" class="group flex items-center gap-2 text-gray-100 transition-all duration-300 ease-out hover:text-white hover:translate-x-2">
                <span class="font-nunitoSans text-sm font-semibold underline-offset-4 group-hover:underline">Chính Sách</span>
                <img src="https://cdn-icons-png.flaticon.com/512/545/545682.png" alt="Arrow"
                  class="h-1.5 w-1.5 opacity-70 transition-all duration-300 group-hover:translate-x-2 group-hover:opacity-100" />
              </a>
              <a href="#" class="group flex items-center gap-2 text-gray-100 transition-all duration-300 ease-out hover:text-white hover:translate-x-2">
                <span class="font-nunitoSans text-sm font-semibold underline-offset-4 group-hover:underline">Diễn Đàn</span>
                <img src="https://cdn-icons-png.flaticon.com/512/545/545682.png" alt="Arrow"
                  class="h-1.5 w-1.5 opacity-70 transition-all duration-300 group-hover:translate-x-2 group-hover:opacity-100" />
              </a>
            </div>
          </div>

          <!-- Resources -->
          <div class="flex-1">
            <h3 class="font-nunito text-sm font-extrabold text-gray-100 underline underline-offset-[6px] decoration-white/50">
              Tài nguyên
            </h3>
            <div class="mt-5 flex flex-col gap-3">
              <a href="#" class="group flex items-center gap-2 text-gray-100 transition-all duration-300 ease-out hover:text-white hover:translate-x-2">
                <span class="font-nunitoSans text-sm font-semibold underline-offset-4 group-hover:underline">Câu hỏi thường gặp (FAQ)</span>
                <img src="https://cdn-icons-png.flaticon.com/512/545/545682.png" alt="Arrow"
                  class="h-1.5 w-1.5 opacity-70 transition-all duration-300 group-hover:translate-x-2 group-hover:opacity-100" />
              </a>
              <a href="#" class="group flex items-center gap-2 text-gray-100 transition-all duration-300 ease-out hover:text-white hover:translate-x-2">
                <span class="font-nunitoSans text-sm font-semibold underline-offset-4 group-hover:underline">Case Study</span>
                <img src="https://cdn-icons-png.flaticon.com/512/545/545682.png" alt="Arrow"
                  class="h-1.5 w-1.5 opacity-70 transition-all duration-300 group-hover:translate-x-2 group-hover:opacity-100" />
              </a>
              <a href="#" class="group flex items-center gap-2 text-gray-100 transition-all duration-300 ease-out hover:text-white hover:translate-x-2">
                <span class="font-nunitoSans text-sm font-semibold underline-offset-4 group-hover:underline">Chính sách bảo mật</span>
                <img src="https://cdn-icons-png.flaticon.com/512/545/545682.png" alt="Arrow"
                  class="h-1.5 w-1.5 opacity-70 transition-all duration-300 group-hover:translate-x-2 group-hover:opacity-100" />
              </a>
              <a href="#" class="group flex items-center gap-2 text-gray-100 transition-all duration-300 ease-out hover:text-white hover:translate-x-2">
                <span class="font-nunitoSans text-sm font-semibold underline-offset-4 group-hover:underline">Điều khoản dịch vụ</span>
                <img src="https://cdn-icons-png.flaticon.com/512/545/545682.png" alt="Arrow"
                  class="h-1.5 w-1.5 opacity-70 transition-all duration-300 group-hover:translate-x-2 group-hover:opacity-100" />
              </a>
            </div>
          </div>

          <!-- Newsletter -->
          <div class="w-64">
            <form class="flex flex-col gap-2">
              <label class="font-dmSans text-sm font-medium text-gray-100">Đăng ký nhận tin</label>
              <div class="relative">
                <input type="email" placeholder="Email của bạn"
                  class="w-full rounded-lg border border-white/30 bg-white/10 py-3 pl-4 pr-28 text-sm font-dmSans text-white placeholder:text-gray-300 backdrop-blur-sm transition-all duration-300 focus:border-white/60 focus:outline-none focus:ring-2 focus:ring-white/30" />
                <button type="submit"
                  class="absolute right-1.5 top-3 rounded-md bg-gradient-to-r from-[#432323] to-[#5a3a3a] px-3 py-1.5 text-xs font-bold text-white transition-all duration-300 ease-out hover:scale-105 hover:from-[#5a3a3a] hover:to-[#6b4a4a] hover:shadow-lg">
                  Đăng Ký
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import Rocket from "@/assets/svg/rocket.svg";

// Helper to create image item
const img = (num, alt = 'Pet') => ({
  type: 'image',
  src: `/src/assets/img_imports/footer img/foot-pic${num}.jpg`,
  alt
});

// Helper for placeholder
const ph = () => ({ type: 'placeholder' });

const footerGridItems = [
  // Row 1
  ph(), img(1), img(2), img(3), img(4), img(5), ph(),
  // Row 2
  ph(), img(6), img(7), img(8), img(9), img(10), ph(),
  // Row 3
  ph(), img(11), img(12), img(13), img(14), img(15), ph(),
  // Row 4
  ph(), img(16), img(17), img(18), img(19), img(20), ph(),
  // Row 5
  ph(), img(21, 'Chó Golden Retriever'), img(22, 'Mèo con ngủ'), img(23, 'Chó Pug'), img(24, 'Hamster ăn hạt'), img(25, 'Mèo tam thể'), ph(),
];
</script>

<style scoped>
</style>