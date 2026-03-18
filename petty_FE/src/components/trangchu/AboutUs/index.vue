<template>
  <div class="w-full min-h-screen bg-[#F8FAFC]">
    <!-- Hero Section -->
    <section class="container mx-auto px-6 md:px-12 py-16 md:py-24 relative overflow-hidden">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center flex-col-reverse">
        
        <div class="flex flex-col gap-6 items-start w-full max-w-xl z-10 shrink-0">
          <div class="inline-flex items-center gap-3 px-4 py-2 border border-[#5A9690]/30 bg-white shadow-sm rounded-full bg-white/10 backdrop-blur-sm">
            <svg class="w-5 h-5 text-[#5A9690]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-2.62 11-1.39 2.61-3.69 3.51-4 3.51"/><path d="M12 15a2 2 0 1 0 3-3"/><path d="M15 12a2 2 0 1 0 3-3"/></svg>
            <span class="text-[#2F5755] text-sm font-bold tracking-wide">Về Petty</span>
          </div>

          <h1 class="font-extrabold text-5xl md:text-6xl text-[#432323] leading-tight tracking-tight text-left">
            Tận tâm chăm sóc, <br>
            <span class="text-[#5A9690]">vì sức khỏe thú cưng</span>
          </h1>

          <p class="font-medium text-[#393E46] text-base md:text-lg leading-relaxed mt-2 text-left">
            Chúng tôi xây dựng hệ thống phòng khám thú y hiện đại – nơi công nghệ và chuyên môn kết hợp để mang đến quy trình chăm sóc, theo dõi và quản lý sức khỏe thú cưng toàn diện, minh bạch và tận tâm.
          </p>

          <div class="flex gap-4 items-center mt-4">
            <router-link to="/" class="bg-[#5A9690] text-white px-6 py-3 rounded-lg shadow hover:bg-[#4A7F79] hover:shadow-lg transition-all duration-300 flex items-center gap-2 font-semibold">
              Về Trang Chủ
            </router-link>
            <router-link to="/lien-he" class="border-2 border-[#E0D9D9] text-[#432323] px-6 py-3 rounded-lg hover:border-[#5A9690] hover:text-[#5A9690] hover:bg-white transition-all duration-300 font-semibold">
              Liên Hệ Chúng Tôi
            </router-link>
          </div>
        </div>

        <div class="w-full relative h-[300px] md:h-[450px] lg:h-[500px] rounded-[24px] overflow-hidden shadow-2xl group z-0">
          <div class="absolute inset-0 bg-gradient-to-tr from-[#5A9690]/20 to-transparent z-10"></div>
          <img src="/src/assets/img_imports/public_img/hp-pic4.png" alt="Petty Clinic Team" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
        </div>

      </div>
    </section>

    <!-- Core Values Section -->
    <section class="bg-white py-20 px-6 md:px-12">
      <div class="container mx-auto max-w-7xl">
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
          <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-[#432323]">Giá Trị Cốt Lõi</h2>
          <p class="text-[#393E46] text-base md:text-lg">Những nguyên tắc hướng dẫn mọi hoạt động và dịch vụ tại Petty.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Card 1 -->
          <div class="bg-[#F8FAFC] rounded-2xl p-8 border border-[#E0D9D9] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
            <div class="w-14 h-14 bg-white rounded-xl shadow-sm flex items-center justify-center mb-6 overflow-hidden">
               <svg class="w-7 h-7 text-[#5A9690]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" y1="22" x2="12" y2="12"/></svg>
            </div>
            <h3 class="text-xl font-bold text-[#432323] mb-3">Minh Bạch</h3>
            <p class="text-sm font-medium text-[#393E46] leading-relaxed">Mọi quy trình chẩn đoán, điều trị và thông tin sức khỏe của thú cưng đều được cập nhật rõ ràng trên hệ thống VCMS để chủ nuôi dễ dàng theo dõi.</p>
          </div>

          <!-- Card 2 -->
          <div class="bg-[#F8FAFC] rounded-2xl p-8 border border-[#E0D9D9] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
            <div class="w-14 h-14 bg-white rounded-xl shadow-sm flex items-center justify-center mb-6 overflow-hidden">
              <svg class="w-7 h-7 text-[#5A9690]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-2.62 11-1.39 2.61-3.69 3.51-4 3.51"/><path d="M12 15a2 2 0 1 0 3-3"/><path d="M15 12a2 2 0 1 0 3-3"/></svg>
            </div>
            <h3 class="text-xl font-bold text-[#432323] mb-3">Tận Tâm</h3>
            <p class="text-sm font-medium text-[#393E46] leading-relaxed">Trải nghiệm của thú cưng và sự an tâm của khách hàng luôn là ưu tiên hàng đầu trong từng nhịp đập và dịch vụ y tế của chúng tôi.</p>
          </div>

          <!-- Card 3 -->
          <div class="bg-[#F8FAFC] rounded-2xl p-8 border border-[#E0D9D9] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
            <div class="w-14 h-14 bg-white rounded-xl shadow-sm flex items-center justify-center mb-6 overflow-hidden">
               <svg class="w-7 h-7 text-[#5A9690]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-[#432323] mb-3">Chuyên Môn Cáo</h3>
            <p class="text-sm font-medium text-[#393E46] leading-relaxed">Đội ngũ y bác sĩ được đào tạo bài bản, áp dụng công nghệ mới nhất vào chẩn đoán và điều trị chuẩn y khoa quốc tế.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Team Section Placeholder -->
    <section class="bg-[#F8FAFC] py-20 px-6 md:px-12 border-t border-[#EEEEEE]">
      <div class="container mx-auto max-w-7xl">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
          <div class="space-y-4 max-w-2xl text-left">
            <h2 class="text-3xl md:text-4xl text-[#432323] font-bold">Đội ngũ của chúng tôi</h2>
            <p class="text-[#393E46] text-base">Những con người nhiệt huyết đứng sau Petty, đồng hành vì một mục tiêu chung.</p>
          </div>
          <router-link to="/" class="group flex items-center gap-2 text-[#5A9690] font-semibold hover:text-[#009689] transition-colors">
            Khám phá đội ngũ chuyên gia <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </router-link>
        </div>

        <!-- Avatars Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
           <div class="flex flex-col items-center gap-4 group cursor-pointer">
             <div class="w-32 h-32 md:w-48 md:h-48 rounded-full overflow-hidden border-4 border-white shadow-lg relative bg-white">
                <img src="/src/assets/img_imports/public_img/hp-pic11.jpg" alt="Team Member" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"/>
                <div class="absolute inset-0 bg-[#5A9690]/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
             </div>
             <div class="text-center">
               <h4 class="font-bold text-[#432323] text-lg">Dr. Nguyễn Văn A</h4>
               <p class="text-sm font-medium text-[#5A9690]">Trưởng Khoa Nội</p>
             </div>
           </div>
           
           <div class="flex flex-col items-center gap-4 group cursor-pointer">
             <div class="w-32 h-32 md:w-48 md:h-48 rounded-full overflow-hidden border-4 border-white shadow-lg relative bg-white">
                <img src="/src/assets/img_imports/public_img/hp-pic12.jpg" alt="Team Member" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"/>
                <div class="absolute inset-0 bg-[#5A9690]/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
             </div>
             <div class="text-center">
               <h4 class="font-bold text-[#432323] text-lg">Dr. Lê Thị B</h4>
               <p class="text-sm font-medium text-[#5A9690]">Chuyên gia Phẫu Thuật</p>
             </div>
           </div>

           <div class="flex flex-col items-center gap-4 group cursor-pointer">
             <div class="w-32 h-32 md:w-48 md:h-48 rounded-full overflow-hidden border-4 border-white shadow-lg relative bg-white">
                <img src="/src/assets/img_imports/public_img/hp-pic13.jpg" alt="Team Member" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"/>
                <div class="absolute inset-0 bg-[#5A9690]/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
             </div>
             <div class="text-center">
               <h4 class="font-bold text-[#432323] text-lg">Anh Trần C</h4>
               <p class="text-sm font-medium text-[#5A9690]">Quản lý Hệ thống VCMS</p>
             </div>
           </div>

           <div class="flex flex-col items-center gap-4 group cursor-pointer">
             <div class="w-32 h-32 md:w-48 md:h-48 rounded-full overflow-hidden border-4 border-white shadow-lg relative bg-white">
                <img src="/src/assets/img_imports/public_img/hp-pic14.jpg" alt="Team Member" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"/>
                <div class="absolute inset-0 bg-[#5A9690]/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
             </div>
             <div class="text-center">
               <h4 class="font-bold text-[#432323] text-lg">Chị Hoàng D</h4>
               <p class="text-sm font-medium text-[#5A9690]">Chăm sóc Trải nghiệm KH</p>
             </div>
           </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';

onMounted(() => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>
