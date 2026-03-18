<template>
  <div class="flex flex-col gap-6 w-full">
    <!-- Page Header -->
    <div class="flex flex-col h-[60px]">
      <h1 class="text-2xl font-medium text-[#101828] tracking-[0.0703px] leading-9">
        Hồ sơ Nhân viên
      </h1>
      <p class="text-base text-[#4a5565] tracking-[-0.3125px] leading-6">
        Quản lý thông tin cá nhân và hồ sơ chuyên môn
      </p>
    </div>

    <!-- Main Content -->
    <div class="flex items-start justify-between gap-6">
      <!-- Left Section: Profile Card -->
      <div class="bg-white border-2 border-[#cbfbf1] rounded-[14px] p-6 w-[362.664px] flex-shrink-0">
        <div class="flex flex-col items-center gap-6">
          <!-- Avatar with Edit Button -->
          <div class="relative w-32 h-32">
            <div class="w-32 h-32 rounded-full border-4 border-[#cbfbf1] overflow-hidden">
              <img
                :src="profileData.avatar"
                alt="Profile"
                class="w-full h-full object-cover"
              />
            </div>
            <button
              @click="uploadAvatar"
              class="absolute bottom-0 right-0 w-10 h-10 bg-[#009689] rounded-full shadow-[0px_10px_15px_-3px_rgba(0,0,0,0.1),0px_4px_6px_-4px_rgba(0,0,0,0.1)] flex items-center justify-center hover:bg-[#00786f] transition-colors"
            >
              <img :src="ICONS.camera" alt="Upload" class="w-5 h-5" />
            </button>
          </div>

          <!-- Name and Role -->
          <div class="flex flex-col items-center gap-2.5 w-full">
            <h2 class="text-base text-[#101828] tracking-[-0.3125px] text-center">
              {{ profileData.name }}
            </h2>
            <div class="bg-[#cbfbf1] border border-transparent rounded-lg px-6 py-0.5">
              <span class="text-xs font-medium text-[#00786f]">
                {{ profileData.role }}
              </span>
            </div>
          </div>

          <!-- Status Toggle -->
          <div class="bg-gray-50 rounded-[10px] p-4 w-full flex flex-col gap-3">
            <label class="text-sm font-medium text-neutral-950 tracking-[-0.1504px]">
              Trạng thái
            </label>
            <div class="flex items-center justify-between">
              <span class="text-sm text-[#364153] tracking-[-0.1504px]">
                {{ profileData.isActive ? 'Hoạt động' : 'Không hoạt động' }}
              </span>
              <button
                @click="profileData.isActive = !profileData.isActive"
                class="w-8 h-[18.398px] rounded-full transition-colors relative"
                :class="profileData.isActive ? 'bg-[#030213]' : 'bg-gray-300'"
              >
                <div
                  class="absolute top-0.5 w-4 h-4 bg-white rounded-full transition-transform"
                  :class="profileData.isActive ? 'left-[15px]' : 'left-0.5'"
                />
              </button>
            </div>
          </div>

          <!-- Join Date -->
          <div class="border-t border-black/10 pt-1 w-full flex items-center gap-2">
            <img :src="ICONS.calendar" alt="Calendar" class="w-4 h-4" />
            <span class="text-sm text-[#4a5565] tracking-[-0.1504px]">
              Tham gia từ: {{ profileData.joinDate }}
            </span>
          </div>
        </div>
      </div>

      <!-- Right Section: Tabs and Content -->
      <div class="flex flex-col gap-8 flex-1">
        <!-- Tabs -->
        <div class="bg-[#ececf0] rounded-[14px] p-1 flex gap-2.5">
          <button
            @click="activeTab = 'personal'"
            class="flex items-center justify-center gap-3.5 px-28 py-1 rounded-[14px] text-sm font-medium tracking-[-0.1504px] transition-colors"
            :class="activeTab === 'personal' ? 'bg-white text-neutral-950' : 'text-neutral-950'"
          >
            <img :src="ICONS.user" alt="User" class="w-4 h-4" />
            <span>Thông tin cá nhân</span>
          </button>
          <button
            @click="activeTab = 'professional'"
            class="flex-1 flex items-center justify-center gap-3.5 px-24 py-1 rounded-[14px] text-sm font-medium tracking-[-0.1504px] transition-colors"
            :class="activeTab === 'professional' ? 'bg-white text-neutral-950' : 'text-neutral-950'"
          >
            <img :src="ICONS.briefcase" alt="Briefcase" class="w-4 h-4" />
            <span>Hồ sơ chuyên môn</span>
          </button>
        </div>

        <!-- Personal Information Tab -->
        <div v-if="activeTab === 'personal'" class="bg-white border border-black/10 rounded-[14px]">
          <div class="p-6 flex flex-col gap-6">
            <h3 class="text-base text-neutral-950 tracking-[-0.3125px]">
              Thông tin hành chính & Liên hệ
            </h3>

            <div class="flex flex-col gap-4">
              <!-- Full Name -->
              <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-neutral-950 tracking-[-0.1504px]">
                  Họ và tên
                </label>
                <input
                  v-model="profileData.name"
                  type="text"
                  class="h-9 bg-[#f3f3f5] rounded-lg px-3 text-sm text-[#717182] tracking-[-0.1504px] border-0 focus:outline-none focus:ring-2 focus:ring-[#009689]"
                />
              </div>

              <!-- Position -->
              <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-neutral-950 tracking-[-0.1504px]">
                  Chức vụ
                </label>
                <input
                  v-model="profileData.position"
                  type="text"
                  class="h-9 bg-[#f3f3f5] rounded-lg px-3 text-sm text-[#717182] tracking-[-0.1504px] border-0 focus:outline-none focus:ring-2 focus:ring-[#009689]"
                />
              </div>

              <!-- Email (Disabled) -->
              <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-neutral-950 tracking-[-0.1504px]">
                  Email đăng nhập
                </label>
                <input
                  v-model="profileData.email"
                  type="email"
                  disabled
                  class="h-9 bg-gray-50 rounded-lg px-3 text-sm text-neutral-950 tracking-[-0.1504px] border-0 opacity-50 cursor-not-allowed"
                />
                <p class="text-xs text-[#6a7282]">
                  Email không thể thay đổi vì được dùng làm định danh
                </p>
              </div>

              <!-- Phone -->
              <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-neutral-950 tracking-[-0.1504px]">
                  Số điện thoại
                </label>
                <input
                  v-model="profileData.phone"
                  type="tel"
                  class="h-9 bg-[#f3f3f5] rounded-lg px-3 text-sm text-[#717182] tracking-[-0.1504px] border-0 focus:outline-none focus:ring-2 focus:ring-[#009689]"
                />
              </div>

              <!-- Address -->
              <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-neutral-950 tracking-[-0.1504px]">
                  Địa chỉ
                </label>
                <textarea
                  v-model="profileData.address"
                  rows="3"
                  placeholder="Nhập địa chỉ liên hệ"
                  class="bg-[#f3f3f5] rounded-lg px-3 py-2 text-sm text-[#717182] tracking-[-0.1504px] border-0 resize-none focus:outline-none focus:ring-2 focus:ring-[#009689]"
                ></textarea>
              </div>

              <!-- Security & Save -->
              <div class="border-t border-black/10 pt-4 flex items-center justify-between">
                <div class="flex flex-col gap-2">
                  <label class="text-sm font-medium text-neutral-950 tracking-[-0.1504px]">
                    Bảo mật
                  </label>
                  <button
                    @click="changePassword"
                    class="h-9 bg-white border border-black/10 rounded-lg px-3 flex items-center gap-2 hover:bg-gray-50 transition-colors"
                  >
                    <img :src="ICONS.key" alt="Key" class="w-4 h-4" />
                    <span class="text-sm font-medium text-neutral-950 tracking-[-0.1504px]">
                      Đổi mật khẩu
                    </span>
                  </button>
                </div>
                <button
                  @click="savePersonalInfo"
                  class="h-9 bg-[#009689] rounded-lg px-3 flex items-center gap-2 hover:bg-[#00786f] transition-colors"
                >
                  <img :src="ICONS.save" alt="Save" class="w-4 h-4" />
                  <span class="text-sm font-medium text-white tracking-[-0.1504px]">
                    Lưu thay đổi
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Professional Profile Tab -->
        <div v-else-if="activeTab === 'professional'" class="bg-white border border-black/10 rounded-[14px]">
          <div class="p-6 flex flex-col gap-6">
            <!-- Header -->
            <div class="flex flex-col gap-2">
              <h3 class="text-base text-neutral-950 tracking-[-0.3125px]">
                Năng lực chuyên môn
              </h3>
              <p class="text-sm text-[#4a5565] tracking-[-0.1504px]">
                Thông tin kỹ năng và kinh nghiệm làm việc
              </p>
            </div>

            <div class="flex flex-col gap-4">
              <!-- Years of Experience -->
              <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                  <img :src="ICONS.experience" alt="Experience" class="w-4 h-4" />
                  <label class="text-sm font-medium text-neutral-950 tracking-[-0.1504px]">
                    Số năm kinh nghiệm
                  </label>
                </div>
                <input
                  v-model="professionalData.yearsOfExperience"
                  type="number"
                  class="h-9 bg-[#f3f3f5] rounded-lg px-3 text-sm text-[#717182] tracking-[-0.1504px] border-0 focus:outline-none focus:ring-2 focus:ring-[#009689]"
                />
                <p class="text-xs text-[#6a7282]">
                  Số năm làm việc trong lĩnh vực chăm sóc thú cưng
                </p>
              </div>

              <!-- License -->
              <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                  <img :src="ICONS.certificate" alt="Certificate" class="w-4 h-4" />
                  <label class="text-sm font-medium text-neutral-950 tracking-[-0.1504px]">
                    Chứng chỉ hành nghề
                  </label>
                </div>
                <input
                  v-model="professionalData.license"
                  type="text"
                  class="h-9 bg-[#f3f3f5] rounded-lg px-3 text-sm text-[#717182] tracking-[-0.1504px] border-0 focus:outline-none focus:ring-2 focus:ring-[#009689]"
                />
                <p class="text-xs text-[#6a7282]">
                  Nhập mã số hoặc tên chứng chỉ quan trọng nhất
                </p>
              </div>

              <!-- Education -->
              <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                  <img :src="ICONS.education" alt="Education" class="w-4 h-4" />
                  <label class="text-sm font-medium text-neutral-950 tracking-[-0.1504px]">
                    Bằng cấp & Học vấn
                  </label>
                </div>
                <textarea
                  v-model="professionalData.education"
                  rows="4"
                  placeholder="Liệt kê các bằng cấp, chứng chỉ quan trọng..."
                  class="bg-[#f3f3f5] rounded-lg px-3 py-2 text-sm text-[#717182] tracking-[-0.1504px] border-0 resize-none focus:outline-none focus:ring-2 focus:ring-[#009689]"
                ></textarea>
                <p class="text-xs text-[#6a7282]">
                  Gợi ý: Sử dụng bullet point (•) để liệt kê từng mục
                </p>
              </div>

              <!-- Preview Card -->
              <div class="bg-teal-50 border border-[#96f7e4] rounded-[10px] p-4">
                <h4 class="text-sm font-bold text-[#0b4f4a] tracking-[-0.1504px] mb-4">
                  Xem trước hồ sơ:
                </h4>
                <div class="bg-white rounded p-3 flex flex-col gap-2">
                  <p class="text-base text-neutral-950 tracking-[-0.3125px]">
                    <span class="font-bold">{{ profileData.name }}</span> - {{ profileData.position }}
                  </p>
                  <p class="text-base text-[#4a5565] tracking-[-0.3125px]">
                    {{ professionalData.yearsOfExperience }} năm kinh nghiệm
                  </p>
                  <p class="text-base text-[#4a5565] tracking-[-0.3125px]">
                    {{ professionalData.license }}
                  </p>
                  <div class="text-sm text-[#4a5565] tracking-[-0.1504px] leading-5 whitespace-pre-line">
                    {{ professionalData.education }}
                  </div>
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
import { ref } from 'vue'

// Icons from Figma (7-day expiry)
const ICONS = {
  camera: 'https://www.figma.com/api/mcp/asset/5b58fd04-0e9e-4f79-99eb-9921d92a9953',
  calendar: 'https://www.figma.com/api/mcp/asset/24075e0d-5a7b-414b-aef5-c78cdd447054',
  user: 'https://www.figma.com/api/mcp/asset/17ce3446-8a3c-4e79-89ec-fa4251da4d31',
  briefcase: 'https://www.figma.com/api/mcp/asset/acbbc696-dc7a-4c6b-aa14-198fe72a6dc6',
  key: 'https://www.figma.com/api/mcp/asset/a52f7f62-8c98-4c48-ab3e-6db7c3b523f4',
  save: 'https://www.figma.com/api/mcp/asset/6e6ac91b-c2d4-4513-8509-05d0a56abc90',
  experience: 'https://www.figma.com/api/mcp/asset/8173197d-e45c-455b-baa4-5bdfab96f2db',
  certificate: 'https://www.figma.com/api/mcp/asset/e2805f46-ae53-48fc-8b42-83f39536f619',
  education: 'https://www.figma.com/api/mcp/asset/987eec08-d907-4562-a841-61c1ab6e53ed'
}

// State
const activeTab = ref('personal')

// Profile Data (replace with API call)
const profileData = ref({
  avatar: 'https://www.figma.com/api/mcp/asset/d5f5c669-705f-4e41-aa19-efe6fa4d18d9',
  name: 'Nguyễn Thị C',
  role: 'Y tá',
  position: 'Y tá kiêm Thu ngân',
  email: 'c.nguyen@clinic.com',
  phone: '0987.654.321',
  address: '',
  isActive: true,
  joinDate: '01/01/2024'
})

// Professional Data (replace with API call)
const professionalData = ref({
  yearsOfExperience: 5,
  license: 'Chứng chỉ hành nghề Y tá số 789012/BYT',
  education: `• Cử nhân Điều dưỡng - Đại học Y Dược TP.HCM (2019)
• Chứng chỉ Chăm sóc thú cưng chuyên nghiệp - Trường Thú y TP.HCM (2020)
• Chứng chỉ Spa & Grooming thú cưng - Pet Care Academy (2021)`
})

// Methods
const uploadAvatar = () => {
  // TODO: Implement avatar upload
  console.log('Upload avatar')
}

const changePassword = () => {
  // TODO: Implement password change modal
  console.log('Change password')
}

const savePersonalInfo = () => {
  // TODO: Implement API call to save personal info
  console.log('Save personal info', profileData.value)
}

// TODO: Fetch user profile data from API when component mounts
// TODO: Implement avatar upload functionality
// TODO: Add validation for form inputs
// TODO: Add success/error toast messages after saving
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700;800&display=swap');

* {
  font-family: 'Nunito Sans', sans-serif;
}
</style>
