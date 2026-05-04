<template>
  <div class="min-h-screen">
    <div class="container max-w-6xl mx-auto px-6 lg:px-12">
      <div class="mb-8">
        <h1 class="text-xl font-bold mb-1">Trợ giúp & Liên hệ</h1>
        <p class="text-lg font-semibold text-gray-700">
          Câu hỏi thường gặp và thông tin liên hệ
        </p>
      </div>

      <!-- Contact Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white/40 border !border-teal-200 rounded-2xl p-6 flex gap-3">
          <div class="bg-teal-100 rounded-xl w-12 h-12 flex items-center justify-center flex-shrink-0">
            <PhoneCallIcon class="w-6 h-6 text-teal-700" />
          </div>
          <div>
            <h3 class="font-bold text-base">Hotline</h3>
            <p class="text-teal-700 font-semibold">1900-xxxx</p>
            <p class="text-gray-700 font-semibold">24/7 hỗ trợ khẩn cấp</p>
          </div>
        </div>

        <div class="bg-white/40 border !border-teal-200 rounded-2xl p-6 flex gap-3">
          <div class="bg-teal-100 text-[#5A9690] rounded-xl w-12 h-12 flex items-center justify-center flex-shrink-0">
            <EmailAddressIcon class="text-teal-700" />
          </div>
          <div>
            <h3 class="font-bold text-base">Email</h3>
            <p class="text-teal-700 font-semibold">info@petcare.vn</p>
            <p class="text-gray-700 font-semibold">Phản hồi trong 24h</p>
          </div>
        </div>

        <div class="bg-white/40 border !border-teal-200 rounded-2xl p-6 flex gap-3">
          <div class="bg-teal-100 rounded-xl w-12 h-12 flex items-center justify-center flex-shrink-0">
            <QAMessIcon class="w-6 h-6 text-teal-700" />
          </div>
          <div>
            <h3 class="font-bold text-base">Zalo OA</h3>
            <p class="text-teal-700 font-semibold">@petcare</p>
            <p class="text-gray-700 font-semibold">Chat trực tiếp</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-[592px_1fr] gap-6 mb-8">
        <!-- FAQ Section -->
        <div class="bg-white border !border-black/15 rounded-2xl p-10">
          <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Câu hỏi thường gặp</h2>
            <p class="text-base font-semibold text-gray-500">
              Tìm câu trả lời nhanh cho các thắc mắc phổ biến
            </p>
          </div>

          <div class="space-y-3">
            <div
              v-for="(faq, index) in faqs"
              :key="index"
              class="border-b border-black/10 pb-3"
            >
              <div
                @click="toggleFaq(index)"
                class="flex justify-between items-center cursor-pointer gap-3"
              >
                <span class="text-base font-semibold flex-1">{{ faq.question }}</span>
                <ChevronDownIcon
                  class="w-6 h-6 transition-transform"
                  :class="{ 'rotate-180': activeFaq === index }"
                />
              </div>
              <div
                v-if="activeFaq === index"
                class="mt-3 text-sm leading-5 text-zinc-500 font-semibold"
              >
                <p v-html="faq.answer"></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Support Request Form -->
        <div class="bg-white border !border-black/15 rounded-2xl p-6">
          <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Gửi yêu cầu hỗ trợ</h2>
            <p class="text-base font-semibold text-gray-500">
              Điền form bên dưới, chúng tôi sẽ liên hệ trong 24h
            </p>
          </div>

          <!-- Success message -->
          <div v-if="submitSuccess" class="mb-6 bg-teal-50 border border-teal-200 rounded-xl p-4 flex items-start gap-3">
            <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
              <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
              <p class="font-semibold text-teal-800">Gửi thành công!</p>
              <p class="text-sm text-teal-700">Yêu cầu hỗ trợ của bạn đã được ghi nhận. Chúng tôi sẽ liên hệ trong 24h.</p>
            </div>
          </div>

          <form v-if="!submitSuccess" @submit.prevent="onSubmitForm">
            <div class="mb-4">
              <label class="block font-semibold text-base mb-2">Họ và tên *</label>
              <input
                v-model="form.ho_ten"
                type="text"
                required
                placeholder="Nguyễn Văn A"
                class="w-full h-11 bg-gray-100 border !border-black/10 rounded-lg px-3 font-semibold focus:outline-none focus:ring-2 focus:ring-teal-400/40 transition"
              />
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block font-semibold text-base mb-2">Email *</label>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  placeholder="email@example.com"
                  class="w-full h-11 bg-gray-100 border !border-black/10 rounded-lg px-3 font-semibold focus:outline-none focus:ring-2 focus:ring-teal-400/40 transition"
                />
              </div>
              <div>
                <label class="block font-semibold text-base mb-2">Số điện thoại</label>
                <input
                  v-model="form.so_dien_thoai"
                  type="tel"
                  placeholder="0912 345 678"
                  class="w-full h-11 bg-gray-100 border !border-black/10 rounded-lg px-3 font-semibold focus:outline-none focus:ring-2 focus:ring-teal-400/40 transition"
                />
              </div>
            </div>

            <div class="mb-4">
              <label class="block font-semibold text-base mb-2">Chủ đề *</label>
              <input
                v-model="form.chu_de"
                type="text"
                placeholder="VD: Thắc mắc về lịch hẹn"
                required
                class="w-full h-11 bg-gray-100 border !border-black/10 rounded-lg px-3 font-semibold focus:outline-none focus:ring-2 focus:ring-teal-400/40 transition"
              />
            </div>

            <div class="mb-6">
              <label class="block font-semibold text-base mb-2">Nội dung *</label>
              <textarea
                v-model="form.noi_dung"
                placeholder="Mô tả chi tiết vấn đề của bạn..."
                required
                rows="3"
                class="w-full bg-gray-100 border !border-black/10 rounded-lg px-3 py-3 font-semibold resize-vertical focus:outline-none focus:ring-2 focus:ring-teal-400/40 transition"
              ></textarea>
            </div>

            <!-- Error message -->
            <div v-if="submitError" class="mb-4 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
              <p class="text-sm text-red-700 font-semibold">{{ submitError }}</p>
            </div>

            <button
              type="submit"
              :disabled="isSubmitting"
              class="w-full bg-[#5a9690] hover:bg-[#4a8580] text-white rounded-lg py-3 flex items-center justify-center gap-2 font-semibold transition disabled:opacity-60 disabled:cursor-not-allowed"
            >
              <svg v-if="isSubmitting" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              <SendToIcon v-else />
              <span>{{ isSubmitting ? 'Đang gửi...' : 'Gửi yêu cầu' }}</span>
            </button>
          </form>

          <!-- Send another request button -->
          <div v-if="submitSuccess" class="mt-4">
            <button
              @click="resetForm"
              class="w-full border border-teal-300 text-teal-700 rounded-lg py-3 font-semibold hover:bg-teal-50 transition"
            >
              Gửi yêu cầu khác
            </button>
          </div>
        </div>
      </div>

      <!-- Clinic Info -->
      <div class="bg-white border !border-black/15 rounded-2xl p-6">
        <div class="mb-6">
          <h2 class="text-lg font-semibold mb-2">Thông tin phòng khám</h2>
          <p class="text-base font-semibold text-gray-500">
            Địa chỉ và giờ làm việc của chúng tôi
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[486px_1fr] gap-6">
          <div class="space-y-5">
            <div class="rounded-2xl overflow-hidden h-64">
              <img
                src="/src/assets/img_imports/public_img/im-address.png"
                alt="Clinic"
                class="w-full h-full object-cover"
              />
            </div>

            <div class="space-y-4">
              <div class="flex gap-3">
                <MapIcon class="w-6 h-6 text-teal-700" />
                <div>
                  <h4 class="font-semibold">Phòng khám Pettty - Chi nhánh Quận 1</h4>
                  <p class="font-semibold text-gray-700">123 Đường Nguyễn Huệ, Quận 1, TP.HCM</p>
                </div>
              </div>
              <div class="flex gap-3">
                <ClockIcon class="w-6 h-6 text-teal-700" />
                <div>
                  <h4 class="font-semibold">Giờ làm việc</h4>
                  <p class="font-semibold text-gray-700">Thứ 2 - Thứ 6: 8:00 - 20:00</p>
                  <p class="font-semibold text-gray-700">Thứ 7 - CN: 9:00 - 18:00</p>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <div
              @click="openGoogleMaps"
              class="bg-zinc-100 border !border-black/15 rounded-xl h-64 flex flex-col items-center justify-center gap-1 cursor-pointer hover:bg-zinc-200/50 transition"
            >
              <h4 class="font-semibold text-gray-700">Google Maps</h4>
              <p class="font-semibold text-gray-500">Nhấn để xem bản đồ</p>
            </div>

            <div>
              <h4 class="font-semibold mb-2">Chi nhánh khác:</h4>
              <div class="space-y-2">
                <div class="bg-teal-50 rounded-xl p-3">
                  <h5 class="font-semibold text-[#2f5755]">Chi nhánh Quận 3</h5>
                  <p class="font-semibold text-gray-700">456 Võ Văn Tần, Q.3, TP.HCM</p>
                </div>
                <div class="bg-teal-50 rounded-xl p-3">
                  <h5 class="font-semibold text-[#2f5755]">Chi nhánh Thủ Đức</h5>
                  <p class="font-semibold text-gray-700">789 Xa lộ Hà Nội, TP. Thủ Đức, TP.HCM</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { hoTroService } from "@/services/hoTroService";
import { getUser } from "@/utils/auth";
import { showSuccessToast, showErrorToast } from "@/utils/toast";

// Icons
import PhoneCallIcon from "@/assets/svg/phonecall.svg";
import QAMessIcon from "@/assets/svg/qa-mess.svg";
import EmailAddressIcon from "@/assets/svg/emailaddress.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import SendToIcon from "@/assets/svg/send-to.svg";
import MapIcon from "@/assets/svg/map.svg";
import ClockIcon from "@/assets/svg/clock.svg";

// Pre-fill with user info if logged in
const currentUser = getUser('customer');

const activeFaq = ref(null);
const isSubmitting = ref(false);
const submitSuccess = ref(false);
const submitError = ref('');

const form = ref({
  // KhachHang dùng full_name, NhanVien dùng full_name, fallback ho_ten/name
  ho_ten: currentUser?.full_name || currentUser?.ho_ten || currentUser?.name || '',
  email: currentUser?.email || '',
  // KhachHang dùng phone, fallback so_dien_thoai
  so_dien_thoai: currentUser?.phone || currentUser?.so_dien_thoai || '',
  chu_de: '',
  noi_dung: '',
});

const faqs = [
  {
    question: "Làm thế nào để đặt lịch khám cho thú cưng?",
    answer: `Bạn có thể đặt lịch khám bằng cách nhấn <strong>"Đặt lịch ngay"</strong> trên Trang chủ hoặc trong trang Dịch vụ khi xem chi tiết dịch vụ mong muốn.<br>
      Sau khi đăng nhập, bạn cũng có thể đặt lịch từ Trang của tôi hoặc Lịch hẹn.<br>
      Chọn thú cưng, dịch vụ và thời gian phù hợp, sau đó xác nhận.<br>
      Hệ thống sẽ gửi xác nhận qua Email và Zalo cho bạn.`,
  },
  {
    question: "Tôi có thể hủy hoặc đổi lịch hẹn không?",
    answer: `Có, bạn có thể hủy hoặc đổi lịch hẹn trước ít nhất 24 giờ so với giờ khám. Truy cập trang Lịch hẹn, chọn lịch cần thay đổi và nhấn <strong>"Đổi lịch"</strong> hoặc <strong>"Hủy hẹn"</strong>. Lưu ý: Việc hủy quá gần giờ khám có thể không được hoàn lại tiền cọc.`,
  },
  {
    question: "Chi phí khám bệnh cho thú cưng là bao nhiêu?",
    answer: `Chi phí khám tùy thuộc vào loại dịch vụ:<br>
      - Khám tổng quát: 150.000 - 300.000đ<br>
      - Tiêm phòng: 100.000 - 200.000đ/mũi<br>
      - Xét nghiệm: 200.000 - 500.000đ<br>
      - Phẫu thuật: Tùy theo ca (liên hệ trực tiếp)<br>
      Bạn sẽ nhận được báo giá chi tiết trước khi thực hiện dịch vụ.`,
  },
  {
    question: "Làm sao để thanh toán hóa đơn?",
    answer: `Sau khi khám, hóa đơn sẽ xuất hiện trong mục Thanh toán. Bạn có thể:<br>
      - Thanh toán online qua VNPay, MoMo<br>
      - Quét mã QR ngân hàng<br>
      - Thanh toán tiền mặt tại phòng khám<br>
      Sau khi thanh toán thành công, bạn có thể tải biên lai điện tử.`,
  },
  {
    question: "Tôi có thể xem lịch sử khám bệnh của thú cưng không?",
    answer: `Có, truy cập mục <strong>"Thú cưng của tôi"</strong>, chọn thú cưng cần xem, và nhấn <strong>"Xem chi tiết"</strong>. Bạn sẽ thấy đầy đủ lịch sử khám bệnh, tiêm phòng, và các ghi chú của bác sĩ.`,
  },
  {
    question: "Phòng khám có hỗ trợ dịch vụ khẩn cấp không?",
    answer: `Có, chúng tôi có dịch vụ cấp cứu 24/7 tại chi nhánh chính. Vui lòng gọi hotline <strong>1900-xxxx</strong> để được hỗ trợ nhanh nhất. Đối với các trường hợp khẩn cấp, hãy đến trực tiếp phòng khám mà không cần đặt lịch trước.`,
  },
];

const toggleFaq = (index) => {
  activeFaq.value = activeFaq.value === index ? null : index;
};

const onSubmitForm = async () => {
  submitError.value = '';
  isSubmitting.value = true;
  try {
    await hoTroService.submitRequest({
      ho_ten: form.value.ho_ten.trim(),
      email: form.value.email.trim(),
      so_dien_thoai: form.value.so_dien_thoai.trim() || null,
      chu_de: form.value.chu_de.trim(),
      noi_dung: form.value.noi_dung.trim(),
    });
    submitSuccess.value = true;
    showSuccessToast('Gửi thành công', 'Yêu cầu hỗ trợ đã được ghi nhận. Chúng tôi sẽ liên hệ trong 24h!');
  } catch (err) {
    const msg = err.response?.data?.message || 'Không thể gửi yêu cầu. Vui lòng thử lại sau.';
    submitError.value = msg;
    showErrorToast('Lỗi gửi yêu cầu', msg);
  } finally {
    isSubmitting.value = false;
  }
};

const resetForm = () => {
  submitSuccess.value = false;
  submitError.value = '';
  form.value = {
    ho_ten: currentUser?.full_name || currentUser?.ho_ten || currentUser?.name || '',
    email: currentUser?.email || '',
    so_dien_thoai: currentUser?.phone || currentUser?.so_dien_thoai || '',
    chu_de: '',
    noi_dung: '',
  };
};

const openGoogleMaps = () => {
  window.open("https://maps.google.com", "_blank");
};
</script>

<style scoped></style>
