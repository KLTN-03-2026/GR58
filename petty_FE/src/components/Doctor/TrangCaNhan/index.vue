<template>
  <div class="relative w-full h-full px-8 py-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-1 mb-6">
      <h1 class="text-2xl font-semibold text-black">Hồ sơ Bác sĩ</h1>
      <p class="text-base font-medium text-gray-500">
        Quản lý thông tin cá nhân và hồ sơ chuyên môn
      </p>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-[362px_1fr] gap-6 w-full">
      <!-- Left Column - Profile Card -->
      <div
        class="bg-white border-2 !border-[#cbfbf1] rounded-[14px] p-6 shadow-sm"
      >
        <div class="flex flex-col items-center h-full">
          <!-- Profile Picture -->
          <div class="relative mb-6">
            <div
              class="w-32 h-32 rounded-full border-4 border-[#cbfbf1] overflow-hidden"
            >
              <img
                :src="
                  doctorProfile.avatar ||
                  'https://www.figma.com/api/mcp/asset/4af27492-9c88-43d5-b84b-905cf059ab56'
                "
                alt="Doctor Avatar"
                class="w-full h-full object-cover"
              />
            </div>
            <!-- Upload Button -->
            <button
              @click="uploadAvatar"
              class="absolute bottom-0 right-0 w-10 h-10 bg-[#009689] rounded-full shadow-lg flex items-center justify-center hover:bg-[#007d72] transition-colors"
            >
              <svg
                class="w-5 h-5 text-white"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M10 3.5a1 1 0 011 1v4.5h4.5a1 1 0 110 2H11v4.5a1 1 0 11-2 0V11H4.5a1 1 0 110-2H9V4.5a1 1 0 011-1z"
                />
              </svg>
            </button>
          </div>

          <!-- Name & Badge -->
          <div class="text-center mb-6">
            <h2 class="text-lg font-semibold text-black mb-2">
              {{ doctorProfile.name }}
            </h2>
            <span
              class="inline-block px-3 py-1 bg-blue-100 text-[#1447e6] text-xs font-medium rounded-lg"
            >
              {{ doctorProfile.role }}
            </span>
          </div>

          <!-- Work Status -->
          <div class="bg-gray-50 rounded-[10px] p-4 w-full mb-6 space-y-3">
            <label class="text-sm font-medium text-black"
              >Trạng thái làm việc</label
            >
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-700">
                {{ workStatus.active ? "Hoạt động" : "Không hoạt động" }}
              </span>
              <button
                @click="toggleWorkStatus"
                :class="[
                  'w-11 h-6 rounded-full transition-colors relative',
                  workStatus.active ? 'bg-[#030213]' : 'bg-gray-300',
                ]"
              >
                <div
                  :class="[
                    'w-5 h-5 bg-white rounded-full transition-transform absolute top-0.5',
                    workStatus.active
                      ? 'translate-x-[22px]'
                      : 'translate-x-[2px]',
                  ]"
                ></div>
              </button>
            </div>
            <p class="text-xs text-gray-500">
              Lễ tân có thể xếp lịch khám cho bạn
            </p>
          </div>

          <!-- Join Date -->
          <div
            class="border-t !border-gray-300 pt-4 w-full flex items-center gap-2"
          >
            <svg
              class="w-4 h-4 text-gray-600"
              viewBox="0 0 16 16"
              fill="currentColor"
            >
              <path
                d="M11 2v2H5V2H3v2H2a1 1 0 00-1 1v9a1 1 0 001 1h12a1 1 0 001-1V5a1 1 0 00-1-1h-1V2h-2zM3 6h10v7H3V6z"
              />
            </svg>
            <span class="text-sm text-gray-600">
              Tham gia từ: {{ doctorProfile.joinDate }}
            </span>
          </div>
        </div>
      </div>

      <!-- Right Column - Tabs & Content -->
      <div class="flex flex-col gap-6">
        <!-- Tabs -->
        <div class="bg-gray-100 h-10 rounded-[14px] p-1 grid grid-cols-2 gap-1">
          <button
            @click="activeTab = 'personal'"
            :class="[
              'h-8 rounded-[10px] text-sm font-medium text-black transition-colors flex items-center justify-center gap-2',
              activeTab === 'personal'
                ? 'bg-white shadow-sm'
                : 'hover:bg-gray-50',
            ]"
          >
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="currentColor">
              <path
                d="M8 2a3 3 0 100 6 3 3 0 000-6zM4 11a3 3 0 00-3 3v1h14v-1a3 3 0 00-3-3H4z"
              />
            </svg>
            Thông tin cá nhân
          </button>
          <button
            @click="activeTab = 'professional'"
            :class="[
              'h-8 rounded-[10px] text-sm font-medium text-black transition-colors flex items-center justify-center gap-2',
              activeTab === 'professional'
                ? 'bg-white shadow-sm'
                : 'hover:bg-gray-50',
            ]"
          >
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="currentColor">
              <path
                d="M3 2a1 1 0 00-1 1v10a1 1 0 001 1h10a1 1 0 001-1V3a1 1 0 00-1-1H3zm2 3h6v1H5V5zm0 3h6v1H5V8z"
              />
            </svg>
            Hồ sơ chuyên môn
          </button>
        </div>

        <!-- Content Card -->
        <div class="bg-white border !border-gray-300 rounded-[14px] shadow-sm">
          <!-- Personal Information Tab -->
          <div v-if="activeTab === 'personal'" class="p-6">
            <h3 class="text-base font-semibold text-black mb-6">
              Thông tin hành chính & Liên hệ
            </h3>

            <div class="space-y-4">
              <!-- Full Name -->
              <div class="space-y-2">
                <label class="text-sm font-medium text-black">Họ và tên</label>
                <input
                  v-model="personalInfo.fullName"
                  type="text"
                  class="w-full h-10 px-3 py-2 bg-gray-50 border !border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
                />
              </div>

              <!-- Email (Disabled) -->
              <div class="space-y-2">
                <label class="text-sm font-medium text-black"
                  >Email đăng nhập</label
                >
                <input
                  v-model="personalInfo.email"
                  type="email"
                  disabled
                  class="w-full h-10 px-3 py-2 bg-gray-100 border !border-gray-300 rounded-lg text-sm text-gray-600 cursor-not-allowed"
                />
                <p class="text-xs text-gray-500">
                  Email không thể thay đổi vì được dùng làm định danh
                </p>
              </div>

              <!-- Address -->
              <div class="space-y-2">
                <label class="text-sm font-medium text-black">Địa chỉ</label>
                <textarea
                  v-model="personalInfo.address"
                  placeholder="Nhập địa chỉ liên hệ"
                  class="w-full h-20 px-3 py-2 bg-gray-50 border !border-gray-300 rounded-lg text-sm text-gray-900 resize-none focus:outline-none focus:ring-2 focus:ring-[#009689]"
                ></textarea>
              </div>

              <!-- Security Section -->
              <div class="border-t !border-gray-300 pt-4 mt-6">
                <label class="text-sm font-medium text-black mb-4 block">
                  Bảo mật
                </label>
                <div class="flex items-center justify-between gap-3">
                  <button
                    @click="showChangePasswordModal = true"
                    class="bg-white border !border-gray-300 h-10 px-4 rounded-lg flex items-center gap-2 text-sm font-medium text-black hover:bg-gray-50 transition-colors"
                  >
                    <svg
                      class="w-4 h-4"
                      viewBox="0 0 16 16"
                      fill="currentColor"
                    >
                      <path
                        d="M8 2a3 3 0 00-3 3v1H4a1 1 0 00-1 1v6a1 1 0 001 1h8a1 1 0 001-1V7a1 1 0 00-1-1h-1V5a3 3 0 00-3-3zm0 2a1 1 0 011 1v1H7V5a1 1 0 011-1z"
                      />
                    </svg>
                    Đổi mật khẩu
                  </button>

                  <button
                    @click="savePersonalInfo"
                    class="bg-[#009689] h-10 px-4 rounded-lg flex items-center gap-2 text-sm font-medium text-white hover:bg-[#007d72] transition-colors"
                  >
                    <svg
                      class="w-4 h-4"
                      viewBox="0 0 16 16"
                      fill="currentColor"
                    >
                      <path
                        d="M13.854 3.646a.5.5 0 010 .708l-7 7a.5.5 0 01-.708 0l-3.5-3.5a.5.5 0 11.708-.708L6.5 10.293l6.646-6.647a.5.5 0 01.708 0z"
                      />
                    </svg>
                    Lưu thay đổi
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Professional Information Tab -->
          <div v-if="activeTab === 'professional'" class="p-6">
            <h3 class="text-base font-semibold text-black mb-6">
              Hồ sơ chuyên môn
            </h3>

            <div class="space-y-4">
              <!-- License Number -->
              <div class="space-y-2">
                <label class="text-sm font-medium text-black"
                  >Số chứng chỉ hành nghề</label
                >
                <input
                  v-model="professionalInfo.licenseNumber"
                  type="text"
                  placeholder="Nhập số chứng chỉ"
                  class="w-full h-10 px-3 py-2 bg-gray-50 border !border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
                />
              </div>

              <!-- Specialization -->
              <div class="space-y-2">
                <label class="text-sm font-medium text-black">Chuyên môn</label>
                <input
                  v-model="professionalInfo.specialization"
                  type="text"
                  placeholder="VD: Phẫu thuật, Nội khoa..."
                  class="w-full h-10 px-3 py-2 bg-gray-50 border !border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
                />
              </div>

              <!-- Years of Experience -->
              <div class="space-y-2">
                <label class="text-sm font-medium text-black"
                  >Số năm kinh nghiệm</label
                >
                <input
                  v-model="professionalInfo.yearsOfExperience"
                  type="number"
                  placeholder="VD: 5"
                  class="w-full h-10 px-3 py-2 bg-gray-50 border !border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
                />
              </div>

              <!-- Qualifications -->
              <div class="space-y-2">
                <label class="text-sm font-medium text-black"
                  >Bằng cấp & Chứng chỉ</label
                >
                <textarea
                  v-model="professionalInfo.qualifications"
                  placeholder="Liệt kê các bằng cấp, chứng chỉ..."
                  class="w-full h-20 px-3 py-2 bg-gray-50 border !border-gray-300 rounded-lg text-sm text-gray-900 resize-none focus:outline-none focus:ring-2 focus:ring-[#009689]"
                ></textarea>
              </div>

              <!-- Save Button -->
              <div class="border-t !border-gray-300 pt-4 mt-6 flex justify-end">
                <button
                  @click="saveProfessionalInfo"
                  class="bg-[#009689] h-10 px-4 rounded-lg flex items-center gap-2 text-sm font-medium text-white hover:bg-[#007d72] transition-colors"
                >
                  <svg class="w-4 h-4" viewBox="0 0 16 16" fill="currentColor">
                    <path
                      d="M13.854 3.646a.5.5 0 010 .708l-7 7a.5.5 0 01-.708 0l-3.5-3.5a.5.5 0 11.708-.708L6.5 10.293l6.646-6.647a.5.5 0 01.708 0z"
                    />
                  </svg>
                  Lưu thay đổi
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Change Password Modal -->
    <transition name="fade">
      <div
        v-if="showChangePasswordModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-6"
        @click.self="showChangePasswordModal = false"
      >
        <div class="bg-white rounded-[14px] p-6 w-full max-w-md space-y-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-black">Đổi mật khẩu</h3>
            <button
              @click="showChangePasswordModal = false"
              class="text-gray-400 hover:text-gray-600 transition-colors"
            >
              <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                <path
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                />
              </svg>
            </button>
          </div>

          <div class="space-y-4">
            <div class="space-y-2">
              <label class="text-sm font-medium text-black"
                >Mật khẩu hiện tại</label
              >
              <input
                v-model="passwordChange.current"
                type="password"
                class="w-full h-10 px-3 py-2 bg-gray-50 border !border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#009689]"
              />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium text-black">Mật khẩu mới</label>
              <input
                v-model="passwordChange.new"
                type="password"
                class="w-full h-10 px-3 py-2 bg-gray-50 border !border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#009689]"
              />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium text-black"
                >Xác nhận mật khẩu mới</label
              >
              <input
                v-model="passwordChange.confirm"
                type="password"
                class="w-full h-10 px-3 py-2 bg-gray-50 border !border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#009689]"
              />
            </div>
          </div>

          <div class="flex gap-3 pt-4">
            <button
              @click="showChangePasswordModal = false"
              class="flex-1 h-10 bg-white border !border-gray-300 rounded-lg text-sm font-medium text-black hover:bg-gray-50 transition-colors"
            >
              Hủy
            </button>
            <button
              @click="changePassword"
              class="flex-1 h-10 bg-[#009689] rounded-lg text-sm font-medium text-white hover:bg-[#007d72] transition-colors"
            >
              Xác nhận
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: "TrangCaNhan",
  data() {
    return {
      // Active Tab
      activeTab: "personal", // 'personal' or 'professional'

      // Doctor Profile
      doctorProfile: {
        avatar:
          "https://www.figma.com/api/mcp/asset/4af27492-9c88-43d5-b84b-905cf059ab56",
        name: "BS. Nguyễn Văn A",
        role: "Bác sĩ chính",
        joinDate: "20/11/2023",
      },

      // Work Status
      workStatus: {
        active: true,
      },

      // Personal Information
      personalInfo: {
        fullName: "BS. Nguyễn Văn A",
        email: "nguyenvana@vietvetclinic.com",
        address: "",
      },

      // Professional Information
      professionalInfo: {
        licenseNumber: "",
        specialization: "",
        yearsOfExperience: null,
        qualifications: "",
      },

      // Password Change Modal
      showChangePasswordModal: false,
      passwordChange: {
        current: "",
        new: "",
        confirm: "",
      },
    };
  },

  methods: {
    // Toggle Work Status
    toggleWorkStatus() {
      this.workStatus.active = !this.workStatus.active;
      // TODO: Update work status in backend
      console.log("Work status changed:", this.workStatus.active);
    },

    // Upload Avatar
    uploadAvatar() {
      // TODO: Implement file upload
      console.log("Upload avatar clicked");
      // Create file input programmatically
      const input = document.createElement("input");
      input.type = "file";
      input.accept = "image/*";
      input.onchange = (e) => {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = (event) => {
            this.doctorProfile.avatar = event.target.result;
            // TODO: Upload to server
          };
          reader.readAsDataURL(file);
        }
      };
      input.click();
    },

    // Save Personal Information
    savePersonalInfo() {
      console.log("Saving personal info:", this.personalInfo);
      // TODO: Implement API call to save personal information
      alert("Thông tin cá nhân đã được lưu!");
    },

    // Save Professional Information
    saveProfessionalInfo() {
      console.log("Saving professional info:", this.professionalInfo);
      // TODO: Implement API call to save professional information
      alert("Hồ sơ chuyên môn đã được lưu!");
    },

    // Change Password
    changePassword() {
      if (this.passwordChange.new !== this.passwordChange.confirm) {
        alert("Mật khẩu xác nhận không khớp!");
        return;
      }

      if (this.passwordChange.new.length < 6) {
        alert("Mật khẩu mới phải có ít nhất 6 ký tự!");
        return;
      }

      console.log("Changing password...");
      // TODO: Implement API call to change password
      this.showChangePasswordModal = false;
      this.passwordChange = {
        current: "",
        new: "",
        confirm: "",
      };
      alert("Mật khẩu đã được thay đổi thành công!");
    },
  },

  mounted() {
    // TODO: Load doctor profile data from API
    console.log("Doctor profile page mounted");
  },
};
</script>

<style scoped>
/* Import Nunito Sans font */
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;500;600;700&display=swap");

/* Fade transition for modal */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* Input focus styles */
input:focus,
textarea:focus {
  outline: none;
}

/* Disabled input styles */
input:disabled {
  cursor: not-allowed;
}

/* Button hover effects */
button {
  transition: all 0.2s ease;
}

button:active {
  transform: scale(0.98);
}
</style>