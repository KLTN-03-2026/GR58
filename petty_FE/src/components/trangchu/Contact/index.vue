<template>
  <div class="flex flex-col gap-12 py-16 w-full">
    <!-- Hero Section -->
    <div class="flex flex-col items-start relative w-full">
      <div class="h-[580px] relative w-full overflow-hidden">
        <!-- Background Image with Overlay -->
        <div class="absolute h-full left-0 top-0 w-full overflow-hidden">
          <img
            src="/src/assets/img_imports/public_img/hp-pic16.png"
            alt="Pet Care Background"
            class="absolute w-full h-full object-cover mix-blend-multiply"
          />
          <div class="absolute inset-0 bg-black/50"></div>
        </div>

        <!-- Hero Content -->
        <div class="relative h-full w-full max-w-[1440px] mx-auto">
          <div
            class="absolute flex flex-col gap-9 left-[120px] top-28 w-[520px]"
          >
            <p class="font-bold text-xl leading-7 text-white">
              Liên hệ & Hỗ trợ
            </p>
            <p class="font-bold text-5xl leading-[48px] text-white">
              Luôn ở đây khi bạn và thú cưng cần
            </p>
            <p class="text-lg leading-7 text-white">
              Đội ngũ bác sĩ trực 24/7 tại PETTY. Dù là đặt lịch khám định kỳ
              hay cấp cứu khẩn cấp, chúng tôi luôn sẵn sàng lắng nghe......
            </p>
          </div>
        </div>
      </div>

      <!-- Emergency Hotline Banner -->
      <div class="bg-[#5a9690] w-full py-4">
        <div class="max-w-[1440px] mx-auto px-20">
          <div class="flex items-center gap-[60px]">
            <div
              class="bg-white/20 rounded-full w-12 h-12 flex items-center justify-center"
            >
              <img
                src="https://www.figma.com/api/mcp/asset/70f8e6f6-7ea3-41c2-b9a1-a7d85485dd9d"
                alt="Emergency"
                class="w-7 h-7"
              />
            </div>
            <div class="flex flex-col">
              <p class="text-lg text-white leading-7 tracking-[-0.44px]">
                Cấp cứu 24/7 ? Gọi ngay hotline
              </p>
              <div class="flex items-center gap-2">
                <img
                  src="https://www.figma.com/api/mcp/asset/b9aaa714-728a-4ecd-bb02-3cdc9174307a"
                  alt="Phone"
                  class="w-6 h-6"
                />
                <p class="text-2xl text-white leading-8 tracking-[0.07px]">
                  1900-xxxx
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Section -->
    <div class="flex gap-6 w-full max-w-[1216px] mx-auto">
      <!-- Left Column -->
      <div class="flex flex-col gap-6">
        <!-- Clinic Information Card -->
        <div
          class="bg-white border !border-gray-300 rounded-[14px] shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1)] p-6"
        >
          <div class="flex flex-col gap-6">
            <h3 class="text-xl text-[#101828] leading-7">
              Thông tin phòng khám
            </h3>

            <!-- Address Section -->
            <div class="flex gap-3">
              <div
                class="bg-[#cbfbf1] rounded-[10px] w-10 h-10 flex items-center justify-center shrink-0"
              >
                <img
                  src="https://www.figma.com/api/mcp/asset/2071990e-b340-4ec4-b11d-9a6283e6be60"
                  alt="Location"
                  class="w-5 h-5"
                />
              </div>
              <div class="flex flex-col gap-1">
                <p class="text-base font-medium text-[#101828] leading-6">
                  Địa chỉ
                </p>
                <p
                  class="text-base text-[#364153] leading-6 tracking-[-0.31px]"
                >
                  Phòng khám thú y PETTY
                </p>
                <p
                  class="text-base text-[#4a5565] leading-6 tracking-[-0.31px]"
                >
                  123 Đường Nguyễn Văn Linh, Quận 1, TP. Hồ Chí Minh
                </p>
              </div>
            </div>

            <!-- Working Hours -->
            <div class="border-t !border-gray-400 pt-4 flex flex-col gap-4">
              <div class="flex items-center justify-between">
                <p
                  class="text-base text-[#101828] leading-6 tracking-[-0.31px]"
                >
                  Giờ làm việc
                </p>
                <div
                  class="bg-[#dcfce7] px-3 py-1 rounded-full text-sm text-[#008236] leading-5 tracking-[-0.15px]"
                >
                  Đang mở cửa
                </div>
              </div>

              <div class="flex flex-col gap-2">
                <div
                  class="bg-[#f0fdfa] border !border-[#96f7e4] rounded-[10px] px-3 py-2 flex items-center justify-between"
                >
                  <p
                    class="text-base text-[#0b4f4a] leading-6 tracking-[-0.31px]"
                  >
                    Thứ 2 - Thứ 6
                  </p>
                  <p
                    class="text-base text-[#009689] leading-6 tracking-[-0.31px]"
                  >
                    08:00 - 20:00
                  </p>
                </div>

                <div
                  class="bg-[#f9fafb] rounded-[10px] px-3 py-2 flex items-center justify-between"
                >
                  <p
                    class="text-base text-[#364153] leading-6 tracking-[-0.31px]"
                  >
                    Thứ 7 - Chủ nhật
                  </p>
                  <p
                    class="text-base text-[#4a5565] leading-6 tracking-[-0.31px]"
                  >
                    09:00 - 18:00
                  </p>
                </div>
              </div>

              <div
                class="bg-[#fff7ed] border !border-[#ffd6a7] rounded-[10px] px-3 py-3 flex items-start gap-2"
              >
                <img
                  src="https://www.figma.com/api/mcp/asset/703fe1aa-eb63-4886-85f7-de232246d27b"
                  alt="Info"
                  class="w-4 h-4 mt-0.5"
                />
                <p class="text-sm text-[#7e2a0c] leading-5 tracking-[-0.15px]">
                  Cấp cứu 24/7: Luôn sẵn sàng
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Connect Card -->
        <div
          class="bg-white border !border-gray-300 rounded-[14px] shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1)] p-6"
        >
          <div class="flex flex-col gap-4">
            <h3 class="text-xl text-[#101828] leading-7 tracking-[-0.45px]">
              Kênh kết nối nhanh
            </h3>

            <div class="flex flex-col gap-3">
              <!-- Zalo -->
              <button
                class="bg-white border !border-gray-300 rounded-lg h-[66px] flex items-center gap-5 px-4 hover:bg-gray-50 transition-colors"
                @click="openZalo"
              >
                <div
                  class="bg-[#dbeafe] rounded-[10px] w-10 h-10 flex items-center justify-center"
                >
                  <img
                    src="https://www.figma.com/api/mcp/asset/831aaee1-c14f-4ae9-8bb9-e36992d9f0d3"
                    alt="Zalo"
                    class="w-4 h-4"
                  />
                </div>
                <div class="flex flex-col items-start">
                  <p
                    class="text-sm font-medium text-[#101828] leading-5 tracking-[-0.15px]"
                  >
                    Chat Zalo
                  </p>
                  <p
                    class="text-sm font-medium text-[#4a5565] leading-5 tracking-[-0.15px]"
                  >
                    @vcms_petcare
                  </p>
                </div>
              </button>

              <!-- Messenger -->
              <button
                class="bg-white border !border-gray-300 rounded-lg h-[66px] flex items-center gap-5 px-4 hover:bg-gray-50 transition-colors"
                @click="openMessenger"
              >
                <div
                  class="bg-[#dbeafe] rounded-[10px] w-10 h-10 flex items-center justify-center"
                >
                  <img
                    src="https://www.figma.com/api/mcp/asset/191c497d-6952-4c2d-baee-3551c5373054"
                    alt="Messenger"
                    class="w-4 h-4"
                  />
                </div>
                <div class="flex flex-col items-start">
                  <p
                    class="text-sm font-medium text-[#101828] leading-5 tracking-[-0.15px]"
                  >
                    Nhắn Messenger
                  </p>
                  <p
                    class="text-sm font-medium text-[#4a5565] leading-5 tracking-[-0.15px]"
                  >
                    m.me/vcms.petcare
                  </p>
                </div>
              </button>

              <!-- Email -->
              <button
                class="bg-white border !border-gray-300 rounded-lg h-[66px] flex items-center gap-5 px-4 hover:bg-gray-50 transition-colors"
                @click="sendEmail"
              >
                <div
                  class="bg-[#cbfbf1] rounded-[10px] w-10 h-10 flex items-center justify-center"
                >
                  <img
                    src="https://www.figma.com/api/mcp/asset/8883a3ac-b313-4ef6-939d-ee63aa564dda"
                    alt="Email"
                    class="w-4 h-4"
                  />
                </div>
                <div class="flex flex-col items-start">
                  <p
                    class="text-sm font-medium text-[#101828] leading-5 tracking-[-0.15px]"
                  >
                    Gửi Email
                  </p>
                  <p
                    class="text-sm font-medium text-[#4a5565] leading-5 tracking-[-0.15px]"
                  >
                    contact@vcms.vn
                  </p>
                </div>
              </button>

              <!-- Phone -->
              <button
                class="bg-white border !border-gray-300 rounded-lg h-[66px] flex items-center gap-5 px-4 hover:bg-gray-50 transition-colors"
                @click="callPhone"
              >
                <div
                  class="bg-[#ffedd4] rounded-[10px] w-10 h-10 flex items-center justify-center"
                >
                  <img
                    src="https://www.figma.com/api/mcp/asset/82db6522-475d-48d2-8ab9-e9676be4f23b"
                    alt="Phone"
                    class="w-4 h-4"
                  />
                </div>
                <div class="flex flex-col items-start">
                  <p
                    class="text-sm font-medium text-[#101828] leading-5 tracking-[-0.15px]"
                  >
                    Gọi điện
                  </p>
                  <p
                    class="text-sm font-medium text-[#4a5565] leading-5 tracking-[-0.15px]"
                  >
                    1900-xxxx
                  </p>
                </div>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div class="flex flex-col gap-6 flex-1">
        <!-- Map -->
        <div
          class="h-[436px] rounded-2xl shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1)] overflow-hidden"
        >
          <img
            src="https://www.figma.com/api/mcp/asset/ba8baeff-5ee5-444b-8307-1f8f9c9562e6"
            alt="Map"
            class="w-full h-full object-cover"
          />
        </div>

        <!-- Contact Form Card -->
        <div
          class="bg-white border !border-gray-300 rounded-[14px] shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1)] p-6"
        >
          <div class="flex flex-col gap-2">
            <h3 class="text-xl text-[#101828] leading-7 tracking-[-0.45px]">
              Gửi thắc mắc cho chúng tôi
            </h3>
            <p class="text-sm text-[#4a5565] leading-5 tracking-[-0.15px]">
              Chúng tôi sẽ phản hồi trong vòng
              <span class="text-[#009689]">2 giờ</span>
            </p>

            <form @submit.prevent="submitForm" class="flex flex-col gap-4 mt-4">
              <div class="flex gap-4">
                <div class="flex-1 flex flex-col gap-0.5">
                  <label
                    class="text-sm font-medium text-[#0a0a0a] leading-[14px] tracking-[-0.15px]"
                  >
                    Họ và tên *
                  </label>
                  <input
                    v-model="form.name"
                    type="text"
                    placeholder="Nguyễn Văn A"
                    class="bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 text-sm text-[#717182] tracking-[-0.15px] focus:outline-none focus:ring-2 focus:ring-[#009689]"
                    required
                  />
                </div>
                <div class="flex-1 flex flex-col gap-0.5">
                  <label
                    class="text-sm font-medium text-[#0a0a0a] leading-[14px] tracking-[-0.15px]"
                  >
                    Số điện thoại *
                  </label>
                  <input
                    v-model="form.phone"
                    type="tel"
                    placeholder="0909 xxx xxx"
                    class="bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 text-sm text-[#717182] tracking-[-0.15px] focus:outline-none focus:ring-2 focus:ring-[#009689]"
                    required
                  />
                </div>
              </div>

              <div class="flex flex-col gap-0.5">
                <label
                  class="text-sm font-medium text-[#0a0a0a] leading-[14px] tracking-[-0.15px]"
                >
                  Chủ đề *
                </label>
                <div class="relative">
                  <select
                    v-model="form.topic"
                    class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 text-sm text-[#717182] tracking-[-0.15px] appearance-none focus:outline-none focus:ring-2 focus:ring-[#009689]"
                    required
                  >
                    <option value="" disabled>Chọn chủ đề</option>
                    <option value="appointment">Đặt lịch khám</option>
                    <option value="service">Tư vấn dịch vụ</option>
                    <option value="emergency">Cấp cứu</option>
                    <option value="other">Khác</option>
                  </select>
                  <img
                    src="https://www.figma.com/api/mcp/asset/857f12bf-3829-4cea-aa62-ac65db5d1e9c"
                    alt="Dropdown"
                    class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
                  />
                </div>
              </div>

              <div class="flex flex-col gap-0.5">
                <label
                  class="text-sm font-medium text-[#0a0a0a] leading-[14px] tracking-[-0.15px]"
                >
                  Nội dung tin nhắn *
                </label>
                <textarea
                  v-model="form.message"
                  placeholder="Mô tả chi tiết câu hỏi hoặc vấn đề của bạn..."
                  class="bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 text-sm text-[#717182] tracking-[-0.15px] h-16 resize-none focus:outline-none focus:ring-2 focus:ring-[#009689]"
                  required
                ></textarea>
              </div>

              <button
                type="submit"
                class="bg-[#0d9488] text-white h-12 rounded-lg flex items-center justify-center gap-2 text-sm font-medium tracking-[-0.15px] hover:bg-[#0f766e] transition-colors"
              >
                <img
                  src="https://www.figma.com/api/mcp/asset/b9087ffe-3bb4-4c05-bb93-e0c654810366"
                  alt="Send"
                  class="w-4 h-4"
                />
                Gửi tin nhắn
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- FAQ Section -->
    <div
      class="bg-white border !border-gray-300 rounded-[14px] shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1)] w-full max-w-[1216px] mx-auto p-8"
    >
      <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-2">
          <h2 class="text-2xl text-[#101828] leading-8 tracking-[0.07px]">
            Câu hỏi thường gặp
          </h2>
          <p class="text-base text-[#4a5565] leading-6 tracking-[-0.31px]">
            Các câu hỏi phổ biến từ khách hàng
          </p>
        </div>

        <div class="flex flex-col">
          <button
            v-for="(faq, index) in faqs"
            :key="index"
            class="border-b border-black/10 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors rounded-lg px-0"
            @click="toggleFaq(index)"
          >
            <div class="flex items-center gap-3">
              <img
                src="https://www.figma.com/api/mcp/asset/5466e71c-1c49-41eb-98ef-7d33a530b54d"
                alt="Question"
                class="w-5 h-5"
              />
              <p
                class="text-sm font-medium text-[#101828] leading-5 tracking-[-0.15px]"
              >
                {{ faq.question }}
              </p>
            </div>
            <img
              src="https://www.figma.com/api/mcp/asset/4d67db5d-bf70-4187-a458-f26e4c1848ae"
              alt="Expand"
              class="w-4 h-4"
              :class="{ 'rotate-180': faq.isOpen }"
            />
          </button>
        </div>

        <!-- CTA Banner -->
        <div
          class="bg-[#f0fdfa] border !border-[#96f7e4] rounded-[10px] p-6 flex flex-col items-center gap-4"
        >
          <p class="text-base text-[#364153] leading-6 tracking-[-0.31px]">
            Không tìm thấy câu trả lời bạn cần?
          </p>
          <button
            class="bg-[#009689] text-white px-4 h-9 rounded-lg flex items-center gap-2 text-sm font-medium tracking-[-0.15px] hover:bg-[#00897b] transition-colors"
            @click="openChat"
          >
            <img
              src="https://www.figma.com/api/mcp/asset/86c31df9-38ab-4d55-95ea-2abe2437ec51"
              alt="Chat"
              class="w-4 h-4"
            />
            Chat trực tiếp với chúng tôi
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Contact",
  data() {
    return {
      form: {
        name: "",
        phone: "",
        topic: "",
        message: "",
      },
      faqs: [
        {
          question: "Phòng khám có chỗ đậu ô tô không?",
          isOpen: false,
        },
        {
          question: "Có nhận lưu chuồng qua đêm không?",
          isOpen: false,
        },
        {
          question: "Thanh toán bằng thẻ/QR được không?",
          isOpen: false,
        },
        {
          question: "Phòng khám có dịch vụ cấp cứu 24/7 không?",
          isOpen: false,
        },
      ],
    };
  },
  methods: {
    submitForm() {
      console.log("Form submitted:", this.form);
      // TODO: Implement form submission logic
      alert("Tin nhắn đã được gửi! Chúng tôi sẽ phản hồi sớm nhất.");
      // Reset form
      this.form = {
        name: "",
        phone: "",
        topic: "",
        message: "",
      };
    },
    toggleFaq(index) {
      this.faqs[index].isOpen = !this.faqs[index].isOpen;
    },
    openZalo() {
      console.log("Open Zalo");
      // TODO: Implement Zalo integration
    },
    openMessenger() {
      console.log("Open Messenger");
      window.open("https://m.me/vcms.petcare", "_blank");
    },
    sendEmail() {
      window.location.href = "mailto:contact@vcms.vn";
    },
    callPhone() {
      window.location.href = "tel:1900xxxx";
    },
    openChat() {
      console.log("Open live chat");
      // TODO: Implement live chat
    },
  },
  mounted() {
    console.log("Trang Liên hệ đã được load!");
  },
};
</script>

<style scoped>
/* Transition for FAQ arrows */
.rotate-180 {
  transform: rotate(180deg);
  transition: transform 0.3s ease;
}

button img {
  transition: transform 0.3s ease;
}
</style>
