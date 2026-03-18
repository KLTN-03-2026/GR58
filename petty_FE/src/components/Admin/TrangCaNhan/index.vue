<template>
  <div class="w-full min-h-screen px-8 py-6 flex gap-6">
    <!-- Left Sidebar - Admin Profile Card -->
    <div
      class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6 w-80 h-fit"
    >
      <div class="flex flex-col items-center">
        <!-- Avatar with Edit Button -->
        <div class="relative mb-4">
          <div
            class="w-32 h-32 bg-teal-600 rounded-full flex items-center justify-center border-4 border-gray-200"
          >
            <span class="text-white text-4xl font-normal">AD</span>
          </div>
          <button
            class="absolute bottom-0 right-0 bg-teal-600 hover:bg-teal-600/80 w-10 h-10 rounded-full flex items-center justify-center shadow-lg transition-colors"
          >
            <UploadIcon class="w-5 h-5 text-white" />
          </button>
        </div>

        <!-- Name and Badge -->
        <h3 class="text-base font-normal text-gray-900 mb-2">Nguyễn Văn</h3>
        <div
          class="bg-red-100 border !border-red-300 rounded-lg px-4 py-1 mb-6"
        >
          <span class="text-xs font-medium text-red-700">Admin</span>
        </div>

        <!-- Email Section -->
        <div class="w-full border-t !border-gray-300 pt-6 space-y-3">
          <div class="flex items-center gap-2 text-sm">
            <span class="text-gray-600">Email:</span>
          </div>
          <p class="text-sm text-gray-900 pl-6">admin@petty.vn</p>

          <div class="flex items-center gap-2 text-sm mt-3">
            <span class="text-gray-600">Mã nhân viên:</span>
          </div>
          <p class="text-sm text-gray-900 pl-6">#EMP001</p>
        </div>

        <!-- Join Date & Last Login -->
        <div class="w-full border-t !border-gray-300 pt-6 mt-6 space-y-3">
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-600">Ngày tham gia:</span>
            <span class="text-gray-900">20/11/2024</span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-600">Đăng nhập cuối:</span>
            <span class="text-teal-600">Vừa xong</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Content Area -->
    <div
      class="flex-1 bg-white border !border-gray-300 shadow-sm rounded-[14px] overflow-hidden"
    >
      <!-- Tab Navigation -->
      <div class="border-b !border-gray-300 flex">
        <button
          @click="activeTab = 'info'"
          :class="[
            'flex-1 h-14 flex items-center justify-center gap-2 text-sm transition-colors relative',
            activeTab === 'info'
              ? 'text-teal-700 bg-teal-50 border-b-2 border-teal-600'
              : 'text-gray-700 hover:bg-gray-50',
          ]"
        >
          <span>Thông tin cá nhân</span>
        </button>

        <button
          @click="activeTab = 'security'"
          :class="[
            'flex-1 h-14 flex items-center justify-center gap-2 text-sm transition-colors relative',
            activeTab === 'security'
              ? 'text-teal-700 bg-teal-50 border-b-2 border-teal-600'
              : 'text-gray-700 hover:bg-gray-50',
          ]"
        >
          <span>Bảo mật</span>
        </button>

        <button
          @click="activeTab = 'activity'"
          :class="[
            'flex-1 h-14 flex items-center justify-center gap-2 text-sm transition-colors relative',
            activeTab === 'activity'
              ? 'text-teal-700 bg-teal-50 border-b-2 border-teal-600'
              : 'text-gray-700 hover:bg-gray-50',
          ]"
        >
          <span>Nhật ký hoạt động</span>
        </button>
      </div>

      <!-- Tab Content -->
      <div class="p-6">
        <!-- Tab 1: Personal Info -->
        <div v-if="activeTab === 'info'" class="space-y-6">
          <!-- Personal Info Form -->
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="block text-sm font-normal text-gray-900">
                Họ và tên <span class="text-red-500">*</span>
              </label>
              <input
                v-model="formData.fullName"
                type="text"
                class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-700"
              />
            </div>

            <div class="space-y-2">
              <label class="block text-sm font-normal text-gray-900">
                Số điện thoại <span class="text-red-500">*</span>
              </label>
              <input
                v-model="formData.phone"
                type="text"
                class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-700"
              />
            </div>

            <div class="space-y-2">
              <label class="block text-sm font-normal text-gray-900"
                >Ngày sinh</label
              >
              <input
                v-model="formData.birthday"
                type="text"
                class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-700"
              />
            </div>

            <div class="space-y-3">
              <label class="block text-sm font-normal text-gray-900"
                >Giới tính</label
              >
              <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    v-model="formData.gender"
                    type="radio"
                    value="male"
                    class="w-4 h-4"
                  />
                  <span class="text-sm text-gray-900">Nam</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    v-model="formData.gender"
                    type="radio"
                    value="female"
                    class="w-4 h-4"
                  />
                  <span class="text-sm text-gray-900">Nữ</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    v-model="formData.gender"
                    type="radio"
                    value="other"
                    class="w-4 h-4"
                  />
                  <span class="text-sm text-gray-900">Khác</span>
                </label>
              </div>
            </div>
          </div>

          <div class="space-y-2">
            <label class="block text-sm font-normal text-gray-900"
              >Địa chỉ</label
            >
            <input
              v-model="formData.address"
              type="text"
              class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-700"
            />
          </div>

          <!-- Save Button -->
          <div class="border-t !border-gray-300 pt-4 flex justify-end">
            <button
              class="flex items-center gap-2 px-4 py-2 bg-[#5a9690] hover:bg-[#5a9690]/80 text-white rounded-lg transition-colors"
            >
              <span class="text-sm font-medium">Lưu thay đổi</span>
            </button>
          </div>
        </div>

        <!-- Tab 2: Security -->
        <div v-if="activeTab === 'security'" class="space-y-8">
          <!-- Change Password Section -->
          <div class="space-y-4">
            <h3 class="text-base font-normal text-gray-900">Đổi mật khẩu</h3>

            <div class="space-y-4">
              <div class="space-y-2">
                <label class="block text-sm font-normal text-gray-900">
                  Mật khẩu hiện tại <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input
                    v-model="securityData.currentPassword"
                    :type="showCurrentPassword ? 'text' : 'password'"
                    placeholder="Nhập mật khẩu hiện tại"
                    class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-700"
                  />
                </div>
              </div>

              <div class="space-y-2">
                <label class="block text-sm font-normal text-gray-900">
                  Mật khẩu mới <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input
                    v-model="securityData.newPassword"
                    :type="showNewPassword ? 'text' : 'password'"
                    placeholder="Nhập mật khẩu mới (tối thiểu 8 ký tự)"
                    class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-700"
                  />
                </div>
              </div>

              <div class="space-y-2">
                <label class="block text-sm font-normal text-gray-900">
                  Xác nhận mật khẩu mới <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input
                    v-model="securityData.confirmPassword"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    placeholder="Nhập lại mật khẩu mới"
                    class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-700"
                  />
                </div>
              </div>

              <button
                class="flex items-center gap-2 px-4 py-2 bg-[#5a9690] hover:bg-[#5a9690]/80 text-white rounded-lg transition-colors"
              >
                <span class="text-sm font-medium">Cập nhật mật khẩu</span>
              </button>
            </div>
          </div>

          <!-- 2FA Section -->
          <div class="border-t !border-gray-300 pt-6">
            <div class="flex items-center justify-between">
              <div class="space-y-1">
                <h3 class="text-base font-normal text-gray-900">
                  Xác thực 2 lớp (2FA)
                </h3>
                <p class="text-sm text-gray-600">
                  Kích hoạt xác thực qua Google Authenticator để tăng cường bảo
                  mật
                </p>
              </div>
              <button
                @click="toggle2FA"
                :class="[
                  'w-11 h-6 rounded-full transition-colors relative',
                  securityData.twoFactorEnabled ? 'bg-teal-600' : 'bg-gray-300',
                ]"
              >
                <div
                  :class="[
                    'absolute top-1 w-4 h-4 bg-white rounded-full transition-transform',
                    securityData.twoFactorEnabled
                      ? 'translate-x-6'
                      : 'translate-x-1',
                  ]"
                ></div>
              </button>
            </div>
          </div>

          <!-- Session Management -->
          <div class="border-t !border-gray-300 pt-6 space-y-4">
            <h3 class="text-base font-normal text-gray-900">
              Quản lý phiên đăng nhập
            </h3>

            <div class="space-y-3">
              <!-- Current Session -->
              <div
                class="bg-teal-50 border !border-teal-200 rounded-xl p-4 flex items-center justify-between"
              >
                <div class="flex items-center gap-3">
                  <div>
                    <p class="text-sm font-normal text-gray-900">
                      Chrome - Windows
                    </p>
                    <p class="text-xs text-gray-600">192.168.1.1</p>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <div class="w-2 h-2 bg-teal-600 rounded-full"></div>
                  <span class="text-xs text-teal-600">Hiện tại</span>
                </div>
              </div>

              <!-- Other Session -->
              <div
                class="bg-gray-50 border !border-gray-300 rounded-xl p-4 flex items-center justify-between"
              >
                <div class="flex items-center gap-3">
                  <div>
                    <p class="text-sm font-normal text-gray-900">
                      Safari - iPhone 14 Pro
                    </p>
                    <p class="text-xs text-gray-600">
                      103.21.45.67 • 2 giờ trước
                    </p>
                  </div>
                </div>
                <button
                  class="flex items-center gap-2 px-3 py-2 bg-white border !border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <span class="text-sm font-medium text-gray-900"
                    >Đăng xuất</span
                  >
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab 3: Activity Log -->
        <div v-if="activeTab === 'activity'" class="space-y-4">
          <h3 class="text-base font-normal text-gray-900">
            Lịch sử hoạt động gần đây
          </h3>

          <!-- Activity Table -->
          <div class="border !border-gray-300 rounded-xl overflow-hidden">
            <table class="w-full">
              <thead class="bg-gray-50 border-b !border-gray-300">
                <tr>
                  <th
                    class="px-4 py-3 text-left text-xs font-bold text-gray-700"
                  >
                    Hành động
                  </th>
                  <th
                    class="px-4 py-3 text-left text-xs font-bold text-gray-700"
                  >
                    Thời gian
                  </th>
                  <th
                    class="px-4 py-3 text-left text-xs font-bold text-gray-700"
                  >
                    Địa chỉ IP
                  </th>
                  <th
                    class="px-4 py-3 text-left text-xs font-bold text-gray-700"
                  >
                    Trạng thái
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(activity, index) in activityLog"
                  :key="index"
                  class="border-b !border-gray-300 last:border-0"
                >
                  <td class="px-4 py-4 text-sm text-gray-900">
                    {{ activity.action }}
                  </td>
                  <td class="px-4 py-4 text-sm text-gray-700">
                    <div>{{ activity.time }}</div>
                    <div>{{ activity.date }}</div>
                  </td>
                  <td class="px-4 py-4 text-sm text-gray-700">
                    {{ activity.ip }}
                  </td>
                  <td class="px-4 py-4">
                    <span
                      :class="[
                        'inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-xs font-medium',
                        activity.status === 'success'
                          ? 'bg-green-100 text-green-800 border border-green-300'
                          : 'bg-yellow-50 text-yellow-800 border border-yellow-300',
                      ]"
                    >
                      {{
                        activity.status === "success"
                          ? "Thành công"
                          : "Cảnh báo"
                      }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="flex items-center justify-between">
            <p class="text-sm text-gray-700">
              Hiển thị 1 - 10 trong tổng số 10 bản ghi
            </p>
            <div class="flex items-center gap-2">
              <button
                class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-900 opacity-50 cursor-not-allowed"
              >
                Trước
              </button>
              <button
                class="px-3 py-2 bg-[#5a9690] hover:bg-[#5a9690]/80 text-white rounded-lg text-sm font-medium"
              >
                1
              </button>
              <button
                class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-900 opacity-50 cursor-not-allowed"
              >
                Sau
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
//Icon SVG
import UploadIcon from "@/assets/svg/upload.svg";

// Active tab
const activeTab = ref("info");

// Form Data
const formData = ref({
  fullName: "Nguyễn Văn Admin",
  phone: "0901234567",
  birthday: "15/01/1990",
  gender: "male",
  address: "123 Nguyễn Huệ, Q1, TP.HCM",
});

// Security Data
const securityData = ref({
  currentPassword: "",
  newPassword: "",
  confirmPassword: "",
  twoFactorEnabled: false,
});

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const toggle2FA = () => {
  securityData.value.twoFactorEnabled = !securityData.value.twoFactorEnabled;
};

// Activity Log Data
const activityLog = ref([
  {
    action: "Đăng nhập hệ thống",
    time: "10:05",
    date: "30/11/2024",
    ip: "192.168.1.1",
    status: "success",
  },
  {
    action: "Cập nhật thông tin phòng khám",
    time: "09:45",
    date: "30/11/2024",
    ip: "192.168.1.1",
    status: "success",
  },
  {
    action: "Xóa phiếu nhập PN001",
    time: "09:30",
    date: "30/11/2024",
    ip: "192.168.1.1",
    status: "warning",
  },
  {
    action: "Thêm nhân viên mới",
    time: "08:20",
    date: "30/11/2024",
    ip: "192.168.1.1",
    status: "success",
  },
  {
    action: "Cập nhật giá dịch vụ",
    time: "16:30",
    date: "29/11/2024",
    ip: "192.168.1.5",
    status: "success",
  },
  {
    action: "Xuất báo cáo doanh thu",
    time: "15:15",
    date: "29/11/2024",
    ip: "192.168.1.5",
    status: "success",
  },
  {
    action: "Đăng nhập thất bại",
    time: "14:50",
    date: "29/11/2024",
    ip: "103.21.45.67",
    status: "warning",
  },
  {
    action: "Thay đổi mật khẩu",
    time: "10:00",
    date: "29/11/2024",
    ip: "192.168.1.1",
    status: "success",
  },
  {
    action: "Phân quyền cho nhân viên",
    time: "09:30",
    date: "29/11/2024",
    ip: "192.168.1.1",
    status: "success",
  },
  {
    action: "Xóa tài khoản nhân viên",
    time: "08:45",
    date: "29/11/2024",
    ip: "192.168.1.1",
    status: "warning",
  },
]);
</script>

<style scoped>
/* Custom radio button styling */
input[type="radio"] {
  accent-color: #030213;
}

input[type="radio"]:checked {
  background-color: #030213;
  border-color: #030213;
}
</style>
