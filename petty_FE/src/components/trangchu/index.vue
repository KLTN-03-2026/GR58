<template>
  <div class="w-full overflow-x-hidden">
    <section
      class="container mx-auto flex flex-col lg:flex-row gap-12 lg:gap-16 items-center justify-between px-6 md:px-12 py-16 md:py-24"
    >
      <div class="flex flex-col gap-[24px] items-start w-full lg:w-1/2 max-w-xl shrink-0">
        <div class="inline-grid relative shrink-0">
          <h1
            class="font-extrabold text-5xl md:text-6xl lg:text-[64px] text-[#432323] leading-tight tracking-tight text-left"
          >
            Đặt lịch dễ<br />Chăm sóc mê!
          </h1>
          <p class="font-medium text-[#393e46] w-full text-left mt-6">
            <span class="text-base md:text-lg]"
              >Cùng bạn chăm sóc thú cưng tốt hơn – khám phá, đặt lịch và đồng
              hành cùng bác sĩ tại
            </span>
            <span class="font-black text-[#2f5755] text-[20px] leading-[24px]"
              >Petty
            </span>
            <span class="text-[18px] leading-[24px]"> </span>
            <span class="font-medium text-[18px] leading-[24px]">nhé</span>
          </p>
        </div>

        <div class="flex gap-4 items-center">
          <button
            @click="showBookingModal = true"
            class="bg-[#5a9690] flex items-center justify-center px-6 py-3 rounded-2xl shadow-[0px_1px_2px_0px_rgba(0,0,0,0.1)] hover:shadow-lg transition-all"
          >
            <span
              class="font-semibold text-[#eeeeee] text-[18px] leading-[20px] tracking-[0.4px]"
            >
              Đặt Lịch Ngay
            </span>
          </button>
          <button
            @click="router.push('/services')"
            class="border-2 border-[#5a9690] flex items-center justify-center px-6 py-3 rounded-2xl hover:bg-[#5a9690] transition-all group"
          >
            <span
              class="font-semibold text-[#222831] group-hover:text-white text-[18px] leading-[20px] tracking-[0.4px]"
            >
              Xem Dịch Vụ
            </span>
          </button>
        </div>

        <div class="inline-grid relative shrink-0">
          <div class="flex gap-3 items-center">
            <div class="flex items-center h-[39px] pr-[9.75px]">
              <div class="relative group/avatar mr-[-9.75px]">
                <div
                  class="absolute inset-0 bg-[#5a9690] rounded-full blur-md opacity-0 group-hover/avatar:opacity-50 transition-opacity duration-300"
                ></div>
                <img
                  :src="avatarImages[0]"
                  alt="Customer"
                  class="relative w-[39px] h-[39px] rounded-full object-cover border-2 border-white aspect-square group-hover/avatar:scale-110 transition-transform duration-300"
                />
              </div>
              <div class="relative group/avatar mr-[-9.75px]">
                <div
                  class="absolute inset-0 bg-[#5a9690] rounded-full blur-md opacity-0 group-hover/avatar:opacity-50 transition-opacity duration-300"
                ></div>
                <img
                  :src="avatarImages[1]"
                  alt="Customer"
                  class="relative w-[39px] h-[39px] rounded-full object-cover border-2 border-white aspect-square group-hover/avatar:scale-110 transition-transform duration-300"
                />
              </div>
              <div class="relative group/avatar mr-[-9.75px]">
                <div
                  class="absolute inset-0 bg-[#5a9690] rounded-full blur-md opacity-0 group-hover/avatar:opacity-50 transition-opacity duration-300"
                ></div>
                <img
                  :src="avatarImages[2]"
                  alt="Customer"
                  class="relative w-[39px] h-[39px] rounded-full object-cover border-2 border-white aspect-square group-hover/avatar:scale-110 transition-transform duration-300"
                />
              </div>
              <div class="relative group/avatar">
                <div
                  class="absolute inset-0 bg-[#5a9690] rounded-full blur-md opacity-0 group-hover/avatar:opacity-50 transition-opacity duration-300"
                ></div>
                <img
                  :src="avatarImages[3]"
                  alt="Customer"
                  class="relative w-[39px] h-[39px] rounded-full object-cover border-2 border-white aspect-square group-hover/avatar:scale-110 transition-transform duration-300"
                />
              </div>
            </div>
            <div class="leading-tight text-left ml-2 mt-2">
              <p class="font-medium text-[14px] leading-[18px] text-[#222831]">
                <span class="font-semibold">Chúng tôi có </span>
                <span class="font-extrabold text-[#5a9690]">10k+</span>
              </p>
              <p class="font-black text-[14px] leading-[18px] text-[#432323]">
                Khách Hàng tại Việt Nam
              </p>
            </div>
          </div>
        </div>
      </div>

      <div
        class="w-full lg:w-1/2 h-[300px] md:h-[480px] rounded-3xl relative shrink-0 overflow-hidden shadow-2xl group"
      >
        <img
          :src="heroImage"
          alt="PETTY VCMS Hero"
          class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
        />
      </div>
    </section>
    <!--Services -->
    <section id="services" class="bg-[#5a9690] py-16">
      <div class="flex flex-col gap-[60px] items-center mx-auto">
        <!-- Trust By -->
        <div class="flex flex-col gap-6 items-center w-full">
          <p class="text-2xl font-semibold text-white text-center">
            Được tin chọn bởi 1k+ khách hàng và đối tác thú y
          </p>
          <div class="flex flex-wrap gap-8 lg:gap-12 items-center justify-center w-full px-4">
            <div
              v-for="item in trustItems"
              :key="item.label"
              class="flex gap-3 items-center group hover:scale-110 transition-transform"
            >
              <component :is="item.icon" class="w-[41px] h-[41px] text-white" />
              <span
                class="text-4xl font-extrabold text-white whitespace-nowrap"
                >{{ item.label }}</span
              >
            </div>
          </div>
        </div>

        <!-- Services Header -->
        <div class="flex flex-col gap-[38px] items-center w-full max-w-[544px]">
          <div
            class="inline-flex items-center gap-4 px-[6px] py-[6px] border-2 border-white bg-white/10 rounded-full"
          >
            <div
              class="w-[34px] h-[34px] bg-white rounded-full relative shadow-lg"
            >
              <div class="w-32 h-32 flex items-center justify-center">
                <SunIcon class="w-full h-full text-[#2f5755]" />
              </div>
            </div>
            <span class="text-2xl font-bold text-white pr-2">Dịch Vụ</span>
          </div>
          <div class="flex flex-col gap-5 items-center text-center w-full">
            <h2
              class="text-6xl md:text-7xl lg:text-8xl font-semibold text-white whitespace-nowrap"
            >
              <span>Dịch Vụ </span>
              <span class="italic">Chính</span>
            </h2>
            <p class="text-lg font-medium text-white leading-8">
              Chúng tôi cung cấp các dịch vụ thú y toàn diện, giúp thú cưng của
              bạn được chăm sóc tận tâm, an toàn và hiệu quả nhất
            </p>
          </div>
        </div>

        <!-- Service Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-7xl mx-auto px-6">
          <!-- Card 1 - Tiêm phòng Vaccine -->
          <div
            class="relative w-full aspect-[4/3] cursor-pointer group hover:shadow-2xl transition-all duration-300 rounded-2xl overflow-hidden"
          >
            <img
              src="/src/assets/img_imports/public_img/hp-pic7.png"
              alt="Dịch vụ"
              class="absolute inset-0 w-full h-full object-cover"
            />
            <div
              class="absolute inset-0 bg-gradient-to-r from-black/40 via-black/20 to-transparent"
            ></div>

            <div
              class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-[#EEE] backdrop-blur-sm rounded-xl px-6 py-3 w-[90%] md:w-[320px] shadow-lg"
            >
              <div class="flex gap-2 items-start">
                <div
                  class="w-9 h-9 bg-[#432323] rounded-md flex items-center justify-center shrink-0 mr-4"
                >
                  <VaccineIcon class="text-[#EEEEEE]" />
                </div>
                <div class="flex flex-col gap-2">
                  <h3
                    class="text-base font-extrabold text-[#432323] leading-tight"
                  >
                    Tiêm phòng Vaccine
                  </h3>
                  <p class="text-sm font-medium text-[#393e46] leading-relaxed">
                    Kiểm tra sức khỏe toàn diện và tiêm phòng định kỳ để thú
                    cưng luôn khỏe mạnh, an toàn
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 2 - Phẫu thuật triệt sản -->
          <div
            class="relative w-full aspect-[4/3] cursor-pointer group hover:shadow-2xl transition-all duration-300 rounded-2xl overflow-hidden"
          >
            <img
              src="/src/assets/img_imports/public_img/hp-pic2.png"
              alt="Dịch vụ"
              class="absolute inset-0 w-full h-full object-cover"
            />
            <div
              class="absolute inset-0 bg-gradient-to-r from-black/40 via-black/20 to-transparent"
            ></div>

            <div
              class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-[#EEE] backdrop-blur-sm rounded-xl px-6 py-3 w-[90%] md:w-[320px] shadow-lg"
            >
              <div class="flex gap-2 items-start">
                <div
                  class="w-9 h-9 bg-[#432323] rounded-md flex items-center justify-center shrink-0 mr-4"
                >
                  <VaccineIcon class="text-[#EEEEEE]" />
                </div>
                <div class="flex flex-col gap-2">
                  <h3
                    class="text-base font-extrabold text-[#432323] leading-tight"
                  >
                    Phẫu thuật triệt sản
                  </h3>
                  <p class="text-sm font-medium text-[#393e46] leading-relaxed">
                    Quy trình triệt sản an toàn, giúp thú cưng khỏe mạnh và sống
                    vui hơn
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 3 - Điều trị nội trú -->
          <div
            class="relative w-full aspect-[4/3] cursor-pointer group hover:shadow-2xl transition-all duration-300 rounded-2xl overflow-hidden"
          >
            <img
              src="/src/assets/img_imports/public_img/hp-pic3.png"
              alt="Dịch vụ"
              class="absolute inset-0 w-full h-full object-cover"
            />
            <div
              class="absolute inset-0 bg-gradient-to-r from-black/40 via-black/20 to-transparent"
            ></div>

            <div
              class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-[#EEE] backdrop-blur-sm rounded-xl px-6 py-3 w-[90%] md:w-[320px] shadow-lg"
            >
              <div class="flex gap-2 items-start">
                <div
                  class="w-9 h-9 bg-[#432323] rounded-md flex items-center justify-center shrink-0 mr-4"
                >
                  <VaccineIcon class="text-[#EEEEEE]" />
                </div>
                <div class="flex flex-col gap-2">
                  <h3
                    class="text-base font-extrabold text-[#432323] leading-tight"
                  >
                    Điều trị nội trú
                  </h3>
                  <p class="text-sm font-medium text-[#393e46] leading-relaxed">
                    Chăm sóc toàn diện cho thú cưng trong môi trường an toàn và
                    tiện nghi
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Khám phá -->
        <div
          class="inline-flex items-center gap-3 px-2 py-2 bg-white border-2 border-white rounded-full shadow-lg hover:shadow-xl hover:scale-105 transition-all cursor-pointer"
        >
          <div
            class="w-[34px] h-[34px] bg-[#2f5755] rounded-full flex items-center justify-center shadow-md hover:bg-[#3a6b68] transition-colors"
          >
            <svg
              class="w-5 h-5 text-white"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                d="M5 12h14M12 5l7 7-7 7"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
          </div>
          <span class="text-2xl font-bold text-[#2f5755] pr-3"
            >Khám phá thêm</span
          >
        </div>

        <!-- About Content -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center max-w-7xl mx-auto w-full mt-12 px-6">
          <div
            class="lg:col-span-7 h-[300px] md:h-[414px] w-full rounded-[24px] overflow-hidden shadow-2xl relative lg:-mr-12 z-10"
          >
            <img
              src="/src/assets/img_imports/public_img/hp-pic4.png"
              alt="About PETTY"
              class="w-full h-full object-cover"
            />
          </div>
          <div
            class="lg:col-span-5 bg-[#eeeeee] px-6 lg:px-8 py-8 rounded-2xl shadow-xl w-full h-auto z-20 lg:-ml-12 flex flex-col gap-4 justify-center"
          >
            <div class="flex flex-col gap-8 w-full">
              <div
                class="inline-flex items-center gap-2 px-[10px] py-[6px] border-2 border-[#5a9690] rounded-full bg-[#eeeeee] w-fit"
              >
                <div
                  class="w-[34px] h-[34px] bg-[#2f5755] rounded-full flex items-center justify-center shadow-lg"
                >
                  <BriefcaseIcon class="w-6 h-6 text-white" />
                </div>
                <span class="text-base font-bold text-[#2f5755]"
                  >Về chúng tôi</span
                >
              </div>
              <div class="flex flex-col gap-1">
                <h2 class="text-4xl font-bold text-[#432323] leading-4">
                  Sứ mệnh cốt lõi
                </h2>
                <p
                  class="text-base font-semibold text-[#222831] leading-4 mt-4"
                >
                  Vì sức khỏe và hạnh phúc của từng thú cưng
                </p>
                <p class="text-sm font-light text-[#393e46] leading-4">
                  Chúng tôi xây dựng hệ thống phòng khám thú y hiện đại – nơi
                  công nghệ và chuyên môn kết hợp để mang đến quy trình chăm
                  sóc, theo dõi và quản lý sức khỏe thú cưng toàn diện, minh
                  bạch và tận tâm.
                </p>
              </div>
              <router-link
                to="/about"
                class="inline-flex items-center gap-1 px-[10px] py-1 border-2 border-[#5a9690] rounded-full bg-[#2f5755] hover:bg-[#3a6b68] transition-all cursor-pointer w-fit"
              >
                <div
                  class="w-[34px] h-[34px] bg-[#5a9690] rounded-full flex items-center justify-center shadow-lg hover:bg-[#6ab0a8] transition-colors"
                >
                  <svg
                    class="w-5 h-5 text-white"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path
                      d="M5 12h14M12 5l7 7-7 7"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </div>
                <span class="text-xs font-bold text-white pr-3">Biết Thêm</span>
              </router-link>
            </div>
          </div>
        </div>

        <!-- Thống kê -->
        <div
          ref="statsSection"
          class="grid grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-10 justify-items-center w-full max-w-7xl mx-auto mt-16 px-6 pb-12 cursor-default"
        >
          <div class="flex gap-4 lg:gap-9 items-center w-full">
            <div class="flex flex-col gap-6 w-full">
              <p class="text-5xl font-black text-[#eeeeee] leading-[48px]">
                {{ stats.partners }}+
              </p>
              <div class="flex flex-col gap-0">
                <p
                  class="text-xl font-bold text-[#e0d9d9] leading-5 whitespace-nowrap"
                >
                  Phòng khám đối tác
                </p>
                <p class="text-sm font-normal text-white leading-4 mt-3">
                  Kết nối trong hệ thống VCMS trên toàn quốc
                </p>
              </div>
            </div>
            <div class="hidden lg:block w-px h-32 bg-white/30"></div>
          </div>
          <div class="flex gap-4 lg:gap-9 items-center w-full">
            <div class="flex flex-col gap-6 w-full">
              <p class="text-5xl font-black text-[#eeeeee] leading-[48px]">
                {{ stats.doctors }}+
              </p>
              <div class="flex flex-col gap-0">
                <p
                  class="text-xl font-bold text-[#e0d9d9] leading-5 whitespace-nowrap"
                >
                  Bác sĩ & chuyên viên
                </p>
                <p class="text-sm font-normal text-white leading-4 mt-3">
                  Đồng hành mang đến giải pháp thú y chuẩn y khoa
                </p>
              </div>
            </div>
            <div class="hidden lg:block w-px h-32 bg-white/30"></div>
          </div>
          <div class="flex gap-4 lg:gap-9 items-center w-full">
            <div class="flex flex-col gap-6 w-full">
              <p class="text-5xl font-black text-[#eeeeee] leading-[48px]">
                {{ stats.rating }}★
              </p>
              <div class="flex flex-col gap-0">
                <p
                  class="text-xl font-bold text-[#e0d9d9] leading-5 whitespace-nowrap"
                >
                  Mức độ hài lòng
                </p>
                <p class="text-sm font-normal text-white leading-4 mt-3">
                  Hàng nghìn chủ nuôi tin tưởng và quay lại
                </p>
              </div>
            </div>
            <div class="hidden lg:block w-px h-32 bg-white/30"></div>
          </div>
          <div class="flex flex-col gap-6 w-full">
            <p class="text-5xl font-black text-[#eeeeee] leading-[48px]">
              {{ Number(stats.pets).toLocaleString("vi-VN") }}+
            </p>
            <div class="flex flex-col gap-0">
              <p
                class="text-xl font-bold text-[#e0d9d9] leading-5 whitespace-nowrap"
              >
                Thú cưng được chăm sóc
              </p>
              <p class="text-sm font-normal text-white leading-4 mt-3">
                Mỗi ca khám là một hành trình tận tâm
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="bg-[#eeeeee] py-24 px-6 md:px-16 lg:px-32">
      <div class="text-center max-w-2xl mx-auto space-y-10 mb-16">
        <div class="flex justify-center">
          <div
            class="inline-flex items-center gap-3 px-2 py-[6px] border-2 border-[#5a9690] rounded-full shadow-sm hover:shadow-md transition-shadow"
          >
            <div
              class="w-[34px] h-[34px] bg-[#2f5755] rounded-full relative shadow-lg"
            >
              <div class="w-32 h-32 flex items-center justify-center">
                <SunIcon class="w-full h-full text-white" />
              </div>
            </div>
            <span class="text-[#2f5755] text-xl md:text-2xl font-bold"
              >Chọn chúng tôi</span
            >
          </div>
        </div>
        <div class="space-y-5">
          <h2
            class="text-6xl md:text-7xl lg:text-8xl font-semibold text-[#5a9690]"
          >
            Nên là <span class="font-black">Petty</span>
          </h2>
          <p class="text-base md:text-lg text-[#393e46]">
            Không chỉ là phòng khám, chúng tôi là người bạn đồng hành tận tâm –
            mang đến dịch vụ thú y chuẩn y khoa, trải nghiệm tiện lợi và sự an
            tâm tuyệt đối cho chủ nuôi
          </p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-12 lg:gap-16">
        <div
          class="bg-[#5a9690] p-6 rounded-2xl shadow-lg flex flex-col gap-16 min-h-56 transition-all hover:-translate-y-2 hover:shadow-xl"
        >
          <div class="w-10 h-10 flex items-center justify-center">
            <GoalIcon class="w-full h-full" />
          </div>
          <div>
            <h3 class="text-xl font-black text-white mb-2">
              Chuyên môn vững vàng
            </h3>
            <p class="text-sm text-white">
              Đội ngũ bác sĩ thú y giàu kinh nghiệm, tận tâm trong từng ca khám
              và điều trị
            </p>
          </div>
        </div>

        <div class="row-span-2 rounded-2xl overflow-hidden shadow-xl">
          <img
            src="/src/assets/img_imports/public_img/hp-pic5.png"
            alt="PETTY Services"
            class="w-full h-full object-cover"
          />
        </div>

        <div
          class="bg-[#5a9690] p-6 rounded-2xl shadow-lg flex flex-col gap-16 min-h-56 transition-all hover:-translate-y-2 hover:shadow-xl"
        >
          <div class="w-10 h-10 rounded flex items-center justify-center">
            <CertificateIcon class="w-full h-full" />
          </div>
          <div>
            <h3 class="text-xl font-black text-white mb-2">
              Quy trình minh bạch
            </h3>
            <p class="text-sm text-white">
              Từ chẩn đoán đến theo dõi, mọi bước đều được cập nhật rõ ràng trên
              hệ thống VCMS
            </p>
          </div>
        </div>

        <div
          class="bg-[#5a9690] p-6 rounded-2xl shadow-lg flex flex-col gap-16 min-h-56 transition-all hover:-translate-y-2 hover:shadow-xl"
        >
          <div class="w-10 h-10 rounded flex items-center justify-center">
            <HandHeartIcon class="w-full h-full" />
          </div>
          <div>
            <h3 class="text-xl font-black text-white mb-2">
              Chăm sóc kịp thời
            </h3>
            <p class="text-sm text-white">
              Đặt lịch, nhắc hẹn và theo dõi sức khỏe thú cưng dễ dàng, mọi lúc
              mọi nơi
            </p>
          </div>
        </div>

        <div
          class="bg-[#5a9690] p-6 rounded-2xl shadow-lg flex flex-col gap-16 min-h-56 transition-all hover:-translate-y-2 hover:shadow-xl"
        >
          <div class="w-10 h-10 rounded flex items-center justify-center">
            <DogIcon class="w-full h-full" />
          </div>
          <div>
            <h3 class="text-xl font-black text-white mb-2">
              Tận tâm với thú cưng
            </h3>
            <p class="text-sm text-white">
              Chúng tôi coi mỗi thú cưng như một người bạn – được yêu thương và
              chăm sóc chu đáo
            </p>
          </div>
        </div>
      </div>
    </section>

    <section class="bg-[#5a9690] py-20 px-6 md:px-12 lg:px-20">
      <div class="max-w-[1400px] mx-auto text-center space-y-16">
        <div class="text-center max-w-2xl mx-auto space-y-10">
          <div class="flex justify-center">
            <div
              class="inline-flex items-center gap-3 px-2 py-[6px] border-2 border-white rounded-full bg-white/10 backdrop-blur-sm"
            >
              <div
                class="w-[34px] h-[34px] bg-white rounded-full relative shadow-lg"
              >
                <div class="w-32 h-32 flex items-center justify-center">
                  <SunIcon class="w-full h-full text-[#2f5755]" />
                </div>
              </div>
              <p class="text-white text-lg md:text-xl lg:text-2xl font-bold">
                Chuyên Nghiệp
              </p>
            </div>
          </div>
          <div class="space-y-5">
            <h1
              class="text-6xl md:text-7xl lg:text-8xl font-semibold text-white"
            >
              Chuyên Môn
            </h1>
            <p class="text-base md:text-lg text-white max-w-2xl mx-auto">
              Chúng tôi kết hợp chuyên môn thú y với công nghệ hiện đại để mang
              đến quy trình chăm sóc minh bạch, an toàn và hiệu quả cho cả phòng
              khám lẫn chủ nuôi
            </p>
          </div>
        </div>

        <div
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-8"
        >
          <div
            v-for="card in expertiseCards"
            :key="card.title"
            class="bg-[#eeeeee] rounded-2xl px-6 py-12 flex flex-col gap-4 w-full h-full relative group hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 cursor-pointer"
          >
            <div class="w-8 h-8">
              <img
                :src="card.icon"
                alt=""
                class="w-full h-full object-contain"
              />
            </div>
            <div class="flex-1">
              <h3
                class="text-xl md:text-2xl font-bold text-[#432323] leading-tight"
              >
                {{ card.title }}
              </h3>
              <p
                class="text-sm md:text-base text-[#393e46] leading-relaxed mt-2"
              >
                {{ card.desc }}
              </p>
            </div>
          </div>
        </div>

        <a
          href="#"
          class="inline-flex items-center gap-3 px-4 py-2 bg-white border-2 border-white rounded-full shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 group"
        >
          <div
            class="w-10 h-10 bg-[#2f5755] rounded-full flex items-center justify-center shadow-md group-hover:bg-[#3a6b68] transition-colors"
          >
            <svg
              class="w-6 h-6 text-white"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                d="M5 12h14M12 5l7 7-7 7"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
          </div>
          <p class="text-xl md:text-2xl font-bold text-[#2f5755] pr-3">
            Khám phá thêm
          </p>
        </a>
      </div>
    </section>

    <section class="py-24 px-6 md:px-16 lg:px-32">
      <div class="max-w-7xl mx-auto space-y-24">
        <div class="space-y-16">
          <div class="text-center max-w-2xl mx-auto space-y-10">
            <div class="flex justify-center">
              <div
                class="flex items-center gap-3 px-2 py-[6px] border-2 border-[#5a9690] rounded-full shadow-sm hover:shadow-md transition-shadow"
              >
                <div
                  class="w-[34px] h-[34px] bg-[#2f5755] rounded-full relative shadow-lg"
                >
                  <div class="w-32 h-32 flex items-center justify-center">
                    <SunIcon class="w-full h-full text-white" />
                  </div>
                </div>
                <p class="text-[#2f5755] text-xl md:text-2xl font-medium">
                  Phản Hồi
                </p>
              </div>
            </div>
            <div class="space-y-5">
              <h1
                class="text-6xl md:text-7xl lg:text-8xl font-semibold text-[#5a9690]"
              >
                Khách Hàng
              </h1>
              <p class="text-base md:text-lg text-[#393e46]">
                Những trải nghiệm thực tế từ các phòng khám và chủ nuôi tin
                tưởng sử dụng PETTY— niềm tin của bạn là động lực phát triển của
                chúng tôi
              </p>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-5 space-y-6">
              <div
                class="bg-[#f7fee7] p-8 rounded-xl shadow-md flex flex-col items-end text-right group hover:shadow-xl hover:-translate-y-1 transition-all"
              >
                <div
                  class="bg-white px-3 py-1 rounded-full shadow text-xs font-bold text-[#393e46] mb-4"
                >
                  Phát Triển
                </div>
                <div class="text-8xl md:text-9xl font-bold text-[#432323]">
                  95%
                </div>
                <p class="text-lg font-semibold text-[#393e46] mt-2">
                  Mức độ hài lòng của khách hàng
                </p>
              </div>
              <div class="grid grid-cols-2 gap-6">
                <div
                  class="bg-[#d6d3d1] p-6 rounded-xl shadow-md flex flex-col items-end text-right group hover:shadow-xl hover:-translate-y-1 transition-all"
                >
                  <div
                    class="bg-white px-3 py-1 rounded-full shadow text-xs font-bold text-[#393e46] mb-3"
                  >
                    Chi Nhánh
                  </div>
                  <div class="text-7xl font-bold text-[#222831] mt-2">30+</div>
                  <p class="text-base font-semibold text-[#393e46] mt-6">
                    Phòng khám sử dụng VCMS
                  </p>
                </div>
                <div
                  class="bg-[#d6d3d1] p-6 rounded-xl shadow-md flex flex-col items-end text-right group hover:shadow-xl hover:-translate-y-1 transition-all"
                >
                  <div
                    class="bg-white px-3 py-1 rounded-full shadow text-xs font-bold text-[#393e46] mb-3"
                  >
                    Người Dùng
                  </div>
                  <div class="text-7xl font-bold text-[#222831] mt-2">4.8</div>
                  <p class="text-base font-semibold text-[#393e46] mt-6">
                    4.8 sao đánh giá trên Google
                  </p>
                </div>
              </div>
            </div>

            <div class="lg:col-span-7">
              <div
                class="bg-[#2f5755] p-12 md:p-20 rounded-xl shadow-md h-full flex flex-col justify-between text-white group hover:shadow-2xl hover:-translate-y-1 transition-all"
              >
                <div class="flex justify-between items-start mb-6">
                  <div
                    class="bg-[#bef264] px-3 py-2 rounded flex items-center gap-1"
                  >
                    <StarIcon class="w-full h-full" />
                    <span class="text-xl font-bold text-[#2f5755]">4.8</span>
                  </div>
                  <div class="bg-white px-3 py-1 rounded-full shadow">
                    <span class="text-xs font-bold text-[#393e46]"
                      >Phản Hồi</span
                    >
                  </div>
                </div>
                <p
                  class="text-xl md:text-2xl font-semibold text-[#eeeeee] leading-relaxed flex-1"
                >
                  "Từ khi phòng khám dùng VCMS, việc đặt lịch và theo dõi sức
                  khỏe cho bé cưng dễ hơn rất nhiều. Tôi có thể xem lịch tiêm,
                  kết quả khám và ghi chú của bác sĩ ngay trên điện thoại – cực
                  kỳ tiện và yên tâm !"
                </p>
                <div class="mt-8 space-y-6">
                  <div class="flex items-center gap-4">
                    <img
                      src="/src/assets/img_imports/public_img/hp-pic12.jpg"
                      alt=""
                      class="w-14 h-14 rounded-full object-cover ring-4 ring-white/20"
                    />
                    <div>
                      <p class="font-bold text-base text-white">Minh Anh</p>
                      <p class="text-sm text-[#eeeeee]/80">
                        Chủ của mèo Miu Miu
                      </p>
                    </div>
                  </div>
                  <div class="flex gap-4">
                    <button
                      class="bg-[#eeeeee] p-3 rounded-xl hover:bg-white transition-colors"
                    >
                      <ArrowLeftIcon class="w-4 h-4 text-[#2f5755]" />
                    </button>
                    <button
                      class="bg-[#eeeeee] p-3 rounded-xl hover:bg-white transition-colors"
                    >
                      <ArrowRightIcon class="w-4 h-4 text-[#2f5755]" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-16">
          <div class="text-center max-w-3xl mx-auto space-y-10">
            <div class="flex justify-center">
              <div
                class="inline-flex items-center gap-3 px-2 py-[6px] border-2 border-[#5a9690] rounded-full shadow-sm hover:shadow-md transition-shadow"
              >
                <div
                  class="w-[34px] h-[34px] bg-[#2f5755] rounded-full relative shadow-lg"
                >
                  <div class="w-32 h-32 flex items-center justify-center">
                    <SunIcon class="w-full h-full text-white" />
                  </div>
                </div>
                <p class="text-[#2f5755] text-xl md:text-2xl font-medium">
                  Câu Chuyện
                </p>
              </div>
            </div>
            <div class="space-y-5">
              <h1
                class="text-6xl md:text-7xl lg:text-8xl font-semibold text-[#5a9690]"
              >
                Khởi Nguồn
              </h1>
              <p class="text-base md:text-lg text-[#393e46] max-w-2xl mx-auto">
                Từ công nghệ quản lý đến trải nghiệm thực tế – cùng khám phá
                cách PETTY giúp phòng khám thú y vận hành hiệu quả và mang lại
                sự an tâm cho chủ nuôi
              </p>
            </div>
          </div>

          <div
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12"
          >
            <div
              v-for="post in blogPosts"
              :key="post.id"
              class="group cursor-pointer"
            >
              <div class="relative overflow-hidden rounded-3xl shadow-lg mb-5">
                <img
                  :src="post.image"
                  alt=""
                  class="w-full h-64 md:h-72 object-cover group-hover:scale-105 transition-transform duration-500"
                />
                <!-- <div
                class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full text-xs font-bold text-[#393e46] shadow">
                {{ post.badge }}
              </div> -->
              </div>
              <div class="space-y-3 md:text-left">
                <p class="text-[#393e46] text-sm font-medium">
                  {{ post.date }}
                </p>
                <h3
                  class="text-xl md:text-2xl font-bold text-[#432323] leading-tight line-clamp-2"
                >
                  {{ post.title }}
                </h3>
                <button
                  class="inline-flex items-center gap-2 text-[#2f5755] border border-[#2f5755] rounded-full px-4 py-2 font-semibold text-sm hover:gap-3 hover:bg-[#2f5755] hover:text-white transition-all group"
                >
                  <span>Xem Thêm</span>
                  <ArrowRightIcon class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <BookingStartModal :is-open="showBookingModal" @close="showBookingModal = false" />
  </div>
</template>

<script setup>
// Import các thư viện và component cần thiết
import { ref, onMounted, reactive } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import axios from "axios";
import BookingStartModal from "./BookingStartModal.vue";

const showBookingModal = ref(false);
import SunIcon from "@/assets/svg/sun-medium.svg";
import VaccineIcon from "@/assets/svg/vaccine.svg";
import BriefcaseIcon from "@/assets/svg/briefcase.svg";
import GoalIcon from "@/assets/svg/goal.svg";
import CertificateIcon from "@/assets/svg/certificate-2.svg";
import HandHeartIcon from "@/assets/svg/hand-heart.svg";
import DogIcon from "@/assets/svg/dog2.svg";
import StarIcon from "@/assets/svg/star.svg";
import ArrowLeftIcon from "@/assets/svg/arrow-left.svg";
import ArrowRightIcon from "@/assets/svg/arrow-right.svg";
import HospitalCircleIcon from "@/assets/svg/hospital-circle.svg";
import UsersIcon from "@/assets/svg/users.svg";
import BrandBaiduIcon from "@/assets/svg/brand-baidu.svg";
import StethoscopeIcon from "@/assets/svg/stethoscope.svg";

const route = useRoute();
const router = useRouter();
const toast = useToast();

// Xử lý callback sau khi đăng nhập qua mạng xã hội
onMounted(async () => {
  const token = route.query.token;
  const verified = route.query.verified;

  if (token && verified === "true") {
    try {
      // Lưu token vào localStorage
      localStorage.setItem("auth_token", token);
      axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

      // Thử gọi API để lấy thông tin người dùng
      try {
        const response = await axios.get("http://127.0.0.1:8000/api/user");

        if (response.data && response.data.status) {
          // Lưu thông tin người dùng
          localStorage.setItem("auth_user", JSON.stringify(response.data.data));
        }
      } catch (apiError) {
        console.warn(
          "API /api/user không khả dụng, bỏ qua việc lấy thông tin người dùng:",
          apiError
        );
        // Không có endpoint /api/user, nhưng vẫn giữ token
        // Thông tin người dùng sẽ được tải khi cần thiết
      }

      toast.success("Đăng nhập thành công!");

      // Xóa tham số truy vấn khỏi URL
      router.replace({ path: "/", query: {} });

      // Tải lại trang để cập nhật header
      setTimeout(() => {
        window.location.reload();
      }, 500);
    } catch (error) {
      console.error("Lỗi tự động đăng nhập:", error);
      toast.error("Có lỗi xảy ra khi đăng nhập. Vui lòng thử lại.");

      // Xóa token lỗi
      localStorage.removeItem("auth_token");
      localStorage.removeItem("auth_user");
      delete axios.defaults.headers.common["Authorization"];
    }
  }
});

const avatarImages = [
  "./src/assets/img_imports/public_img/hp-pic13.jpg",
  "./src/assets/img_imports/public_img/hp-pic14.jpg",
  "./src/assets/img_imports/public_img/hp-pic12.jpg",
  "./src/assets/img_imports/public_img/hp-pic11.jpg",
];

const heroImage = "/src/assets/img_imports/public_img/hp-pic6.png";
const trustItems = [
  { icon: HospitalCircleIcon, label: "Bệnh Viện" },
  { icon: UsersIcon, label: "Khách Hàng" },
  { icon: BrandBaiduIcon, label: "Thương Hiệu" },
  { icon: StethoscopeIcon, label: "Bác Sĩ" },
];

const expertiseCards = [
  {
    icon: "/src/assets/svg/book-plus.svg",
    title: "Kinh nghiệm chuyên sâu trong ngành thú y",
    desc: "Hơn 10 năm kinh nghiệm vận hành và đồng hành cùng hàng chục phòng khám trên toàn quốc",
  },
  {
    icon: "/src/assets/svg/prong.svg",
    title: "Giải pháp quản lý tối ưu",
    desc: "Tự động hóa quy trình khám, đặt lịch và hồ sơ bệnh án – giúp đội ngũ làm việc nhanh hơn, chính xác hơn",
  },
  {
    icon: "/src/assets/svg/database-cog.svg",
    title: "Dữ liệu và phân tích chuyên sâu",
    desc: "Theo dõi, đánh giá và tối ưu hiệu quả điều trị dựa trên dữ liệu thực tế.",
  },
  {
    icon: "/src/assets/svg/user-up.svg",
    title: "Lấy khách hàng làm trung tâm",
    desc: "Lắng nghe nhu cầu, tối ưu trải nghiệm – vì mỗi thú cưng đều xứng đáng được chăm sóc tốt nhất",
  },
  {
    icon: "/src/assets/svg/brand-apple-news.svg",
    title: "Đổi mới và phát triển liên tục",
    desc: "Ứng dụng công nghệ mới, cập nhật quy trình khám và điều trị chuẩn quốc tế",
  },
  {
    icon: "/src/assets/svg/plug-connected.svg",
    title: "Kết nối toàn diện – từ phòng khám đến chủ nuôi",
    desc: "Tạo nên hệ sinh thái thú y hiện đại, giúp mọi bên cùng đồng hành vì sức khỏe thú cưng",
  },
];

const blogPosts = [
  {
    id: 1,
    image: "/src/assets/img_imports/public_img/hp-pic10.png",
    badge: "Tối ưu vận hành phòng khám với hệ thống VCMS",
    date: "Ngày: 25/09/2025",
    title: "Tối ưu vận hành phòng khám với hệ thống VCMS",
  },
  {
    id: 2,
    image: "/src/assets/img_imports/public_img/hp-pic9.png",
    badge: "Chuyển đổi số trong chăm sóc thú y",
    date: "Ngày: 10/09/2025",
    title:
      "Chuyển đổi số trong chăm sóc thú y: Hành trình từ giấy tờ đến nền tảng số",
  },
  {
    id: 3,
    image:
      "/src/assets/img_imports/public_img/hp-pic9.png",
    badge: "Xây dựng niềm tin với chủ nuôi",
    date: "Ngày: 02/09/2025",
    title: "Xây dựng niềm tin với chủ nuôi qua trải nghiệm kỹ thuật số",
  },
];

// Animation Logic

const statsSection = ref(null);
const stats = reactive({
  partners: 0,
  doctors: 0,
  rating: 0,
  pets: 0,
});

const animateStats = () => {
  const duration = 2000; // 2 seconds

  const animate = (key, end, isDecimal = false) => {
    let start = 0;
    const startTime = performance.now();

    const step = (currentTime) => {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);

      // Easing function for smooth effect (easeOutQuad)
      const easeProgress = 1 - (1 - progress) * (1 - progress);

      let current = start + (end - start) * easeProgress;

      if (isDecimal) {
        stats[key] = current.toFixed(1);
      } else {
        stats[key] = Math.floor(current);
      }

      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        stats[key] = isDecimal ? end : end; // Ensure final value is exact
      }
    };

    requestAnimationFrame(step);
  };

  animate("partners", 30);
  animate("doctors", 100);
  animate("rating", 4.9, true);
  animate("pets", 10000);
};

// Add observer in existing onMounted or new one.
// Since onMounted is already imported and used, let's append the observer logic there or use a separate onMounted hook (Vue allows multiple).
onMounted(() => {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          animateStats(); // Start animation
          observer.unobserve(entry.target); // Run only once
        }
      });
    },
    { threshold: 0.5 }
  );

  if (statsSection.value) {
    observer.observe(statsSection.value);
  }
});
</script>

<style>
body {
  margin: 0;
  padding: 0;
}
</style>

<style scoped>
@keyframes float {
  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-20px);
  }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>