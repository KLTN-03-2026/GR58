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
              class="bg-white/20 rounded-full w-12 h-12 flex items-center justify-center p-2"
            >
              <AlertCircle class="w-full h-full text-white" />
            </div>
            <div class="flex flex-col">
              <p class="text-lg text-white leading-7 tracking-[-0.44px]">
                Cấp cứu 24/7 ? Gọi ngay hotline
              </p>
              <div class="flex items-center gap-2">
                <Phone class="w-6 h-6 text-white" />
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
                class="bg-[#cbfbf1] rounded-[10px] w-10 h-10 flex items-center justify-center shrink-0 p-2"
              >
                <MapPin class="w-full h-full text-teal-600" />
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
                <Info class="w-4 h-4 mt-0.5 text-[#7e2a0c]" />
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
                  class="bg-[#dbeafe] rounded-[10px] w-10 h-10 flex items-center justify-center p-2"
                >
                  <MessageCircle class="w-full h-full text-blue-600" />
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
                  class="bg-[#dbeafe] rounded-[10px] w-10 h-10 flex items-center justify-center p-2"
                >
                  <MessageSquare class="w-full h-full text-blue-600" />
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
                  class="bg-[#cbfbf1] rounded-[10px] w-10 h-10 flex items-center justify-center p-2"
                >
                  <Mail class="w-full h-full text-teal-600" />
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
                  class="bg-[#ffedd4] rounded-[10px] w-10 h-10 flex items-center justify-center p-2"
                >
                  <PhoneCall class="w-full h-full text-orange-500" />
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
          class="h-[436px] rounded-2xl shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1)] overflow-hidden bg-gray-200 flex items-center justify-center"
        >
          <div class="text-center text-gray-500 flex flex-col items-center gap-2">
            <Map class="w-12 h-12" />
            <p>Bản đồ khu vực</p>
          </div>
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
                    v-model="form.ho_ten"
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
                    Email *
                  </label>
                  <input
                    v-model="form.email"
                    type="email"
                    placeholder="email@example.com"
                    class="bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 text-sm text-[#717182] tracking-[-0.15px] focus:outline-none focus:ring-2 focus:ring-[#009689]"
                    required
                  />
                </div>
              </div>

              <div class="flex gap-4">
                <div class="flex-1 flex flex-col gap-0.5">
                  <label
                    class="text-sm font-medium text-[#0a0a0a] leading-[14px] tracking-[-0.15px]"
                  >
                    Số điện thoại
                  </label>
                  <input
                    v-model="form.so_dien_thoai"
                    type="tel"
                    placeholder="0909 xxx xxx"
                    class="bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 text-sm text-[#717182] tracking-[-0.15px] focus:outline-none focus:ring-2 focus:ring-[#009689]"
                  />
                </div>
                <div class="flex-1 flex flex-col gap-0.5">
                  <label
                    class="text-sm font-medium text-[#0a0a0a] leading-[14px] tracking-[-0.15px]"
                  >
                    Chủ đề *
                  </label>
                  <div class="relative">
                    <select
                      v-model="form.chu_de"
                      class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 text-sm text-[#717182] tracking-[-0.15px] appearance-none focus:outline-none focus:ring-2 focus:ring-[#009689]"
                      required
                    >
                      <option value="" disabled>Chọn chủ đề</option>
                      <option value="Đặt lịch khám">Đặt lịch khám</option>
                      <option value="Tư vấn dịch vụ">Tư vấn dịch vụ</option>
                      <option value="Cấp cứu">Cấp cứu</option>
                      <option value="Khác">Khác</option>
                    </select>
                    <ChevronDown class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" />
                  </div>
                </div>
              </div>

              <div class="flex flex-col gap-0.5">
                <label
                  class="text-sm font-medium text-[#0a0a0a] leading-[14px] tracking-[-0.15px]"
                >
                  Nội dung tin nhắn *
                </label>
                <textarea
                  v-model="form.noi_dung"
                  placeholder="Mô tả chi tiết câu hỏi hoặc vấn đề của bạn..."
                  class="bg-[#f3f3f5] border-0 rounded-lg px-3 py-2 text-sm text-[#717182] tracking-[-0.15px] h-16 resize-none focus:outline-none focus:ring-2 focus:ring-[#009689]"
                  required
                ></textarea>
              </div>

              <button
                type="submit"
                :disabled="isSubmitting"
                class="bg-[#0d9488] text-white h-12 rounded-lg flex items-center justify-center gap-2 text-sm font-medium tracking-[-0.15px] hover:bg-[#0f766e] transition-colors disabled:opacity-50"
              >
                <Send class="w-4 h-4" />
                {{ isSubmitting ? 'Đang gửi...' : 'Gửi tin nhắn' }}
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
              <HelpCircle class="w-5 h-5 text-gray-400" />
              <p
                class="text-sm font-medium text-[#101828] leading-5 tracking-[-0.15px]"
              >
                {{ faq.question }}
              </p>
            </div>
            <ChevronDown
              class="w-4 h-4 text-gray-500 transition-transform duration-300"
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
            <MessageCircle class="w-4 h-4" />
            Chat trực tiếp với chúng tôi
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import {
  AlertCircle,
  Phone,
  MapPin,
  Info,
  MessageCircle,
  MessageSquare,
  Mail,
  PhoneCall,
  ChevronDown,
  Send,
  HelpCircle,
  Map
} from 'lucide-vue-next';
import hoTroService from "@/services/hoTroService";

// Data
const form = ref({
  ho_ten: "",
  email: "",
  so_dien_thoai: "",
  chu_de: "",
  noi_dung: "",
});

const isSubmitting = ref(false);

const faqs = ref([
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
]);

// Methods
const submitForm = async () => {
  isSubmitting.value = true;
  try {
    await hoTroService.submitRequest(form.value);
    alert("Tin nhắn đã được gửi! Chúng tôi sẽ phản hồi sớm nhất.");
    // Reset form
    form.value = {
      ho_ten: "",
      email: "",
      so_dien_thoai: "",
      chu_de: "",
      noi_dung: "",
    };
  } catch (error) {
    console.error("Lỗi gửi yêu cầu:", error);
    alert("Có lỗi xảy ra. Vui lòng thử lại.");
  } finally {
    isSubmitting.value = false;
  }
};

const toggleFaq = (index) => {
  faqs.value[index].isOpen = !faqs.value[index].isOpen;
};

const openZalo = () => {
  console.log("Open Zalo");
};

const openMessenger = () => {
  window.open("https://m.me/vcms.petcare", "_blank");
};

const sendEmail = () => {
  window.location.href = "mailto:contact@vcms.vn";
};

const callPhone = () => {
  window.location.href = "tel:1900xxxx";
};

const openChat = () => {
  console.log("Open live chat");
};
</script>

<style scoped>
/* Transition for FAQ arrows */
.rotate-180 {
  transform: rotate(180deg);
}
</style>
