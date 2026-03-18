<template>
  <div class="w-full min-h-screen px-8 py-6 flex flex-col gap-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-2">
      <h1 class="text-2xl font-semibold text-black">Cấu hình Hệ thống</h1>
      <p class="text-gray-500 font-medium text-base">
        Quản lý thông tin, phân quyền, và cài đặt vận hành
      </p>
    </div>

    <div class="flex gap-6">
      <!-- Sidebar Navigation -->
      <div
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-4 w-44 h-fit"
      >
        <div class="space-y-1">
          <!-- Thông tin chung -->
          <button
            @click="activeTab = 'info'"
            :class="[
              'flex items-center gap-3 w-full px-3 py-3 rounded-lg text-sm transition-colors',
              activeTab === 'info'
                ? 'bg-teal-50 text-teal-700'
                : 'text-gray-700 hover:bg-gray-50',
            ]"
          >
            <!-- <img :src="iconInfo" alt="" class="w-3 h-3" /> -->
            <span class="leading-4 whitespace-nowrap">Thông tin</span>
          </button>

          <!-- Phân quyền -->
          <!-- <button
            @click="activeTab = 'permission'"
            :class="[
              'flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm transition-colors',
              activeTab === 'permission'
                ? 'bg-teal-50 text-teal-700'
                : 'text-gray-700 hover:bg-gray-50',
            ]"
          >
            <img :src="iconPermission" alt="" class="w-4 h-4" />
            <span>Phân quyền</span>
          </button> -->

          <!-- Mẫu in -->
          <button
            @click="activeTab = 'template'"
            :class="[
              'flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm transition-colors',
              activeTab === 'template'
                ? 'bg-teal-50 text-teal-700'
                : 'text-gray-700 hover:bg-gray-50',
            ]"
          >
            <!-- <img :src="iconTemplate" alt="" class="w-4 h-4" /> -->
            <span>Mẫu in</span>
          </button>

          <!-- Vận hành -->
          <button
            @click="activeTab = 'operation'"
            :class="[
              'flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm transition-colors',
              activeTab === 'operation'
                ? 'bg-teal-50 text-teal-700'
                : 'text-gray-700 hover:bg-gray-50',
            ]"
          >
            <!-- <img :src="iconOperation" alt="" class="w-4 h-4" /> -->
            <span>Vận hành</span>
          </button>

          <!-- Kết nối -->
          <button
            @click="activeTab = 'connection'"
            :class="[
              'flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm transition-colors',
              activeTab === 'connection'
                ? 'bg-teal-50 text-teal-700'
                : 'text-gray-700 hover:bg-gray-50',
            ]"
          >
            <!-- <img :src="iconConnection" alt="" class="w-4 h-4" /> -->
            <span>Kết nối</span>
          </button>
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="flex-1">
        <!-- Tab 1: Thông tin chung -->
        <div v-if="activeTab === 'info'" class="space-y-6">
          <!-- Logo Section -->
          <div
            class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
          >
            <div class="mb-6">
              <h3 class="text-base font-normal text-gray-900 mb-1">
                Logo phòng khám
              </h3>
              <p class="text-sm text-gray-600">
                Hiển thị trên Hóa đơn, Đơn thuốc và App khách hàng
              </p>
            </div>
            <div class="flex items-center gap-6">
              <div
                class="w-32 h-32 bg-gray-100 border-2 border-gray-300 rounded-full flex items-center justify-center"
              >
                <!-- <img :src="iconBuilding" alt="" class="w-12 h-12" /> -->
              </div>
              <div>
                <button
                  class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors mb-2"
                >
                  <!-- <img :src="iconUpload" alt="" class="w-4 h-4" /> -->
                  <span class="text-sm font-medium text-gray-900"
                    >Tải lên Logo</span
                  >
                </button>
                <p class="text-xs text-gray-500">
                  PNG, JPG (Tối đa 2MB). Khuyến nghị: 300x300px
                </p>
              </div>
            </div>
          </div>

          <!-- Basic Info Section -->
          <div
            class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
          >
            <h3 class="text-base font-normal text-gray-900 mb-6">
              Thông tin cơ bản
            </h3>
            <div class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-900 mb-2"
                    >Tên phòng khám</label
                  >
                  <input
                    type="text"
                    v-model="clinicInfo.name"
                    class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-600"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-900 mb-2"
                    >Số điện thoại Hotline</label
                  >
                  <input
                    type="text"
                    v-model="clinicInfo.hotline"
                    class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-600"
                  />
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2"
                  >Địa chỉ</label
                >
                <input
                  type="text"
                  v-model="clinicInfo.address"
                  class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-600"
                />
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-900 mb-2"
                    >Email liên hệ</label
                  >
                  <input
                    type="email"
                    v-model="clinicInfo.email"
                    class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-600"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-900 mb-2"
                    >Website / Fanpage URL</label
                  >
                  <input
                    type="url"
                    v-model="clinicInfo.website"
                    class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-600"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Working Hours Section -->
          <div
            class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
          >
            <div class="mb-6">
              <h3 class="text-base font-normal text-gray-900 mb-1">
                Giờ làm việc mặc định
              </h3>
              <p class="text-sm text-gray-600">
                Hệ thống sử dụng để chặn khách đặt lịch vào giờ đóng cửa
              </p>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2"
                  >Thứ 2 - Thứ 6</label
                >
                <div class="flex items-center gap-2">
                  <input
                    type="time"
                    class="flex-1 px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm"
                  />
                  <span class="text-gray-600">-</span>
                  <input
                    type="time"
                    class="flex-1 px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm"
                  />
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2"
                  >Thứ 7 - Chủ nhật</label
                >
                <div class="flex items-center gap-2">
                  <input
                    type="time"
                    class="flex-1 px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm"
                  />
                  <span class="text-gray-600">-</span>
                  <input
                    type="time"
                    class="flex-1 px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Save Button -->
          <div class="flex justify-end">
            <button
              class="flex items-center gap-2 px-4 py-2 bg-[#5a9690] hover:bg-[#5a9690]/80 text-white rounded-lg transition-colors"
            >
              <!-- <img :src="iconSave" alt="" class="w-4 h-4" /> -->
              <span class="text-sm font-medium">Lưu thay đổi</span>
            </button>
          </div>
        </div>

        <!-- Tab 2: Phân quyền -->
        <div
          v-if="activeTab === 'permission'"
          class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
        >
          <div class="mb-6">
            <h3 class="text-base font-normal text-gray-900 mb-1">
              Phân quyền & Vai trò
            </h3>
            <p class="text-sm text-gray-600">
              Định nghĩa "Ai được làm gì" - RBAC (Role-Based Access Control)
            </p>
          </div>

          <div class="grid grid-cols-3 gap-6">
            <!-- Role List -->
            <div class="space-y-3">
              <div class="flex items-center justify-between mb-3">
                <label class="text-sm font-medium text-gray-900"
                  >Danh sách Vai trò</label
                >
                <button
                  class="flex items-center gap-2 px-3 py-1.5 bg-white border !border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium text-gray-900"
                >
                  <!-- <img :src="iconPlus" alt="" class="w-4 h-4" /> -->
                  Thêm
                </button>
              </div>

              <button
                v-for="role in roles"
                :key="role.id"
                @click="selectedRole = role.id"
                :class="[
                  'w-full flex items-center justify-between px-3 py-3 rounded-lg border transition-colors',
                  selectedRole === role.id
                    ? 'bg-teal-50 !border-teal-500 '
                    : 'bg-white !border-gray-300 hover:border-gray-300',
                ]"
              >
                <span class="text-base text-gray-900">{{ role.name }}</span>
                <span
                  :class="[
                    'px-2 py-0.5 rounded-lg text-xs font-medium',
                    role.badgeClass,
                  ]"
                >
                  {{ role.name }}
                </span>
              </button>
            </div>

            <!-- Permission Matrix -->
            <div class="col-span-2 space-y-3">
              <div class="mb-3">
                <span class="text-sm font-medium text-gray-900"
                  >Ma trận quyền -
                </span>
                <span class="text-sm font-medium text-teal-600">{{
                  getSelectedRoleName()
                }}</span>
              </div>

              <!-- Lịch hẹn Group -->
              <div
                class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-4"
              >
                <div class="flex items-center gap-2 mb-3">
                  <!-- <img :src="iconCalendar" alt="" class="w-4 h-4" /> -->
                  <span class="text-base text-gray-900">Nhóm Lịch hẹn</span>
                </div>
                <div class="grid grid-cols-4 gap-2">
                  <label class="flex items-center gap-2">
                    <input
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-900"
                    />
                    <span class="text-sm text-gray-700">Xem</span>
                  </label>
                  <label class="flex items-center gap-2">
                    <input
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-900"
                    />
                    <span class="text-sm text-gray-700">Tạo</span>
                  </label>
                  <label class="flex items-center gap-2">
                    <input
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-900"
                    />
                    <span class="text-sm text-gray-700">Sửa</span>
                  </label>
                  <label class="flex items-center gap-2">
                    <input
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-900"
                    />
                    <span class="text-sm text-gray-700">Xóa</span>
                  </label>
                </div>
              </div>

              <!-- Tài chính Group -->
              <div
                class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-4"
              >
                <div class="flex items-center gap-2 mb-3">
                  <!-- <img :src="iconMoney" alt="" class="w-4 h-4" /> -->
                  <span class="text-base text-gray-900">Nhóm Tài chính</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                  <label class="flex items-center gap-2">
                    <input
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-900"
                    />
                    <span class="text-sm text-gray-700">Xem doanh thu</span>
                  </label>
                  <label class="flex items-center gap-2">
                    <input
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-900"
                    />
                    <span class="text-sm text-gray-700">Thu tiền</span>
                  </label>
                  <label class="flex items-center gap-2">
                    <input
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-900"
                    />
                    <span class="text-sm text-gray-700">Hoàn tiền</span>
                  </label>
                </div>
              </div>

              <!-- Kho thuốc Group -->
              <div
                class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-4"
              >
                <div class="flex items-center gap-2 mb-3">
                  <!-- <img :src="iconMedicine" alt="" class="w-4 h-4" /> -->
                  <span class="text-base text-gray-900">Nhóm Kho thuốc</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                  <label class="flex items-center gap-2">
                    <input
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-900"
                    />
                    <span class="text-sm text-gray-700">Xem tồn</span>
                  </label>
                  <label class="flex items-center gap-2">
                    <input
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-900"
                    />
                    <span class="text-sm text-gray-700">Nhập kho</span>
                  </label>
                  <label class="flex items-center gap-2">
                    <input
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-900"
                    />
                    <span class="text-sm text-gray-700">Điều chỉnh</span>
                  </label>
                </div>
              </div>

              <!-- Bệnh án Group -->
              <div
                class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-4"
              >
                <div class="flex items-center gap-2 mb-3">
                  <!-- <img :src="iconMedical" alt="" class="w-4 h-4" /> -->
                  <span class="text-base text-gray-900">Nhóm Bệnh án</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                  <label class="flex items-center gap-2">
                    <input
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-900"
                    />
                    <span class="text-sm text-gray-700">Xem chi tiết</span>
                  </label>
                  <label class="flex items-center gap-2">
                    <input
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-900"
                    />
                    <span class="text-sm text-gray-700">Kê đơn</span>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Save Button -->
          <div class="flex justify-end mt-6">
            <button
              class="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors"
            >
              <!-- <img :src="iconSave" alt="" class="w-4 h-4" /> -->
              <span class="text-sm font-medium">Lưu phân quyền</span>
            </button>
          </div>
        </div>

        <!-- Tab 3: Mẫu in -->
        <div
          v-if="activeTab === 'template'"
          class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
        >
          <div class="mb-6">
            <h3 class="text-base font-normal text-gray-900 mb-1">
              Quản lý Mẫu in
            </h3>
            <p class="text-sm text-gray-600">
              Chỉnh sửa nội dung Hóa đơn, Đơn thuốc, Phiếu tiêm phòng
            </p>
          </div>

          <div class="grid grid-cols-3 gap-6">
            <!-- Template List -->
            <div class="space-y-3">
              <label class="text-sm font-medium text-gray-900"
                >Danh sách mẫu</label
              >

              <button
                v-for="template in templates"
                :key="template.id"
                @click="selectedTemplate = template.id"
                :class="[
                  'w-full flex items-center gap-3 px-3 py-3 rounded-lg border transition-colors text-left',
                  selectedTemplate === template.id
                    ? 'bg-teal-50 !border-teal-500'
                    : 'bg-white !border-gray-300 hover:border-gray-400',
                ]"
              >
                <span class="text-base text-gray-900">{{ template.name }}</span>
              </button>
            </div>

            <!-- Template Editor -->
            <div class="col-span-2 space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2"
                  >Trình soạn thảo</label
                >
                <div class="bg-gray-100 rounded-lg p-4 min-h-[300px]">
                  <p class="text-sm text-gray-700 mb-4">Kính gửi {Ten_KH},</p>
                  <p class="text-sm text-gray-700 mb-4">
                    Cám ơn quý khách đã tin tường sử dụng dịch vụ của chúng tôi.
                  </p>
                  <p class="text-sm text-gray-700 mb-2">Thông tin khám:</p>
                  <ul class="text-sm text-gray-700 mb-4 space-y-1">
                    <li>- Thú cưng: {Benh_nhan}</li>
                    <li>- Ngày khám: {Ngay_kham}</li>
                    <li>- Bác sĩ: {Bac_si}</li>
                    <li>- Chuẩn đoán: {Chuan_doan}</li>
                  </ul>
                  <p class="text-sm text-gray-700 mb-4">
                    Tổng chi phí: {Tong_tien}
                  </p>
                  <p class="text-sm text-gray-700">Trân trọng!</p>
                </div>
              </div>

              <div class="bg-blue-50 border !border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">
                  Biến số (Kéo thả)
                </h4>
                <div class="grid grid-cols-2 gap-2">
                  <div
                    v-for="variable in templateVariables"
                    :key="variable.key"
                    class="bg-white rounded px-3 py-2 border !border-blue-300"
                  >
                    <span class="text-xs text-blue-600 font-mono">{{
                      "{" + variable.key + "}"
                    }}</span>
                    <p class="text-xs text-gray-600">{{ variable.label }}</p>
                  </div>
                </div>
                <p class="text-xs text-blue-900 mt-3">
                  <span class="font-bold">Hướng dẫn:</span> Chọn biến số và dán
                  vào vùng soạn thảo. Khi in, hệ thống sẽ tự thay thế bằng dữ
                  liệu thực.
                </p>
              </div>

              <div class="flex items-center gap-3">
                <button
                  class="flex items-center gap-2 px-4 py-2 bg-white border !border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <span class="text-sm font-medium text-gray-900"
                    >Xem trước</span
                  >
                </button>
                <button
                  class="flex items-center gap-2 px-4 py-2 bg-[#5a9690] hover:bg-[#5a9690]/80 text-white rounded-lg transition-colors"
                >
                  <!-- <img :src="iconSave" alt="" class="w-4 h-4" /> -->
                  <span class="text-sm font-medium">Lưu mẫu</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab 4: Vận hành -->
        <div v-if="activeTab === 'operation'" class="space-y-6">
          <!-- Lịch hẹn Settings -->
          <div class="bg-white rounded-xl border !border-gray-300 p-6">
            <div class="flex items-center gap-2 mb-6">
              <h3 class="text-base font-normal text-gray-900">
                Quy tắc Lịch hẹn
              </h3>
            </div>
            <div class="grid grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2"
                  >Thời lượng mỗi ca khám</label
                >
                <select
                  class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-600"
                >
                  <option>30 phút</option>
                  <option>45 phút</option>
                  <option>60 phút</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2"
                  >Cho phép hủy trước</label
                >
                <div class="flex items-center gap-2">
                  <input
                    type="number"
                    value="24"
                    class="flex-1 px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-600"
                  />
                  <span class="text-sm text-gray-600">giờ</span>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2"
                  >Tiền cọc tối thiểu</label
                >
                <div class="flex items-center gap-2">
                  <input
                    type="number"
                    value="20"
                    class="flex-1 px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-600"
                  />
                  <span class="text-sm text-gray-600">%</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Kho & Thuốc Settings -->
          <div class="bg-white rounded-xl border !border-gray-300 p-6">
            <div class="flex items-center gap-2 mb-6">
              <h3 class="text-base font-normal text-gray-900">
                Quy tắc Kho & Thuốc
              </h3>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2"
                  >Cảnh báo tồn kho khi số lượng dưới</label
                >
                <div class="flex items-center gap-2">
                  <input
                    type="number"
                    value="10"
                    class="flex-1 px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-600"
                  />
                  <span class="text-sm text-gray-600">đơn vị</span>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2"
                  >Cảnh báo hết hạn trước</label
                >
                <div class="flex items-center gap-2">
                  <input
                    type="number"
                    value="30"
                    class="flex-1 px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-600"
                  />
                  <span class="text-sm text-gray-600">ngày</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Tài chính Settings -->
          <div class="bg-white rounded-xl border !border-gray-300 p-6">
            <div class="flex items-center gap-2 mb-6">
              <h3 class="text-base font-normal text-gray-900">
                Quy tắc Tài chính
              </h3>
            </div>
            <div class="grid grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2"
                  >VAT mặc định</label
                >
                <select
                  class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-600"
                >
                  <option>10%</option>
                  <option>8%</option>
                  <option>5%</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2"
                  >Điểm thưởng - Chi tiêu (đ)</label
                >
                <input
                  type="number"
                  value="10000"
                  class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-600"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2"
                  >Nhận được (điểm)</label
                >
                <input
                  type="number"
                  value="1"
                  class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-600"
                />
              </div>
            </div>
            <div class="bg-blue-50 border !border-blue-200 rounded-lg p-3 mt-4">
              <p class="text-xs text-blue-900">
                <span class="font-bold">Quy tắc hiện tại:</span> 10,000đ chi
                tiêu = 1 điểm
              </p>
            </div>
          </div>

          <!-- Save Button -->
          <div class="flex justify-end">
            <button
              class="flex items-center gap-2 px-4 py-2 bg-[#5a9690] hover:bg-[#5a9690]/80 text-white rounded-lg transition-colors"
            >
              <span class="text-sm font-medium">Lưu cấu hình</span>
            </button>
          </div>
        </div>

        <!-- Tab 5: Kết nối -->
        <div v-if="activeTab === 'connection'" class="space-y-6">
          <!-- VNPay / VietQR -->
          <div class="bg-white rounded-xl border !border-gray-300 p-6">
            <div class="flex items-center gap-2 mb-6">
              <h3 class="text-base font-normal text-gray-900">
                Cổng thanh toán VNPay / VietQR
              </h3>
            </div>

            <div
              class="bg-green-50 border !border-green-200 rounded-lg px-4 py-3 mb-6 flex items-center justify-between"
            >
              <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                <span class="text-sm font-medium text-gray-900"
                  >Trạng thái:
                  <span class="text-green-700">Đã kết nối</span></span
                >
              </div>
              <span
                class="inline-block bg-green-500 text-white text-xs font-medium px-2 py-1 rounded"
                >Active</span
              >
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2"
                  >Merchant ID</label
                >
                <input
                  type="text"
                  value="MERCHANT_123"
                  class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-600"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2"
                  >Secret Key</label
                >
                <input
                  type="password"
                  value="***"
                  class="w-full px-3 py-2 bg-gray-100 border-0 rounded-lg text-sm text-gray-600"
                />
              </div>
            </div>
          </div>

          <!-- Zalo OA -->
          <div class="bg-white rounded-xl border !border-gray-300 p-6">
            <div class="flex items-center gap-2 mb-6">
              <h3 class="text-base font-normal text-gray-900">
                Zalo OA (Zalo Official Account)
              </h3>
            </div>

            <div
              class="bg-red-50 border !border-red-200 rounded-lg px-4 py-3 mb-6 flex items-center justify-between"
            >
              <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                <span class="text-sm font-medium text-gray-900"
                  >Trạng thái:
                  <span class="text-red-700">Chưa kết nối</span></span
                >
              </div>
              <button
                class="flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-lg transition-colors text-sm font-medium"
              >
                Kết nối ngay
              </button>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-900 mb-2"
                >Cấu hình tin nhắn tự động</label
              >
              <div class="space-y-2">
                <label class="flex items-center gap-2">
                  <input type="checkbox" checked class="w-4 h-4 rounded" />
                  <span class="text-sm text-gray-700"
                    >Gửi tin xác nhận lịch hẹn</span
                  >
                </label>
                <label class="flex items-center gap-2">
                  <input type="checkbox" checked class="w-4 h-4 rounded" />
                  <span class="text-sm text-gray-700"
                    >Gửi tin nhắc lịch trước 24 giờ</span
                  >
                </label>
              </div>
            </div>

            <div class="bg-amber-50 border !border-amber-200 rounded-lg p-3">
              <p class="text-xs text-amber-900">
                <span class="font-bold">Lợi ích:</span> Tự động hóa việc gửi tin
                nhắn xác nhận và nhắc lịch, giảm tỷ lệ khách hàng quên hẹn.
              </p>
            </div>
          </div>

          <!-- Save Button -->
          <div class="flex justify-end">
            <button
              class="flex items-center gap-2 px-4 py-2 bg-[#5a9690] hover:bg-[#5a9690]/80 text-white rounded-lg transition-colors"
            >
              <span class="text-sm font-medium">Lưu cài đặt</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

// Active tab
const activeTab = ref("info");

// Clinic Info
const clinicInfo = ref({
  name: "Phòng khám Thú y ABC",
  hotline: "1900 xxxx",
  address: "123 Đường ABC, Quận 1, TP.HCM",
  email: "contact@vetclinic.com",
  website: "https://vetclinic.com",
});

// Roles
const roles = ref([
  { id: "admin", name: "Admin", badgeClass: "bg-red-100 text-red-700" },
  { id: "doctor", name: "Bác sĩ", badgeClass: "bg-blue-100 text-blue-700" },
  {
    id: "nurse",
    name: "Điều dưỡng",
    badgeClass: "bg-green-100 text-green-700",
  },
  {
    id: "receptionist",
    name: "Lễ tân",
    badgeClass: "bg-purple-100 text-purple-700",
  },
  {
    id: "assistant",
    name: "Trợ lý",
    badgeClass: "bg-amber-100 text-amber-700",
  },
]);

const selectedRole = ref("admin");

const getSelectedRoleName = () => {
  const role = roles.value.find((r) => r.id === selectedRole.value);
  return role ? role.name : "";
};

// Templates
const templates = ref([
  { id: "invoice", name: "Hóa đơn" },
  { id: "prescription", name: "Đơn thuốc" },
  { id: "vaccination", name: "Phiếu xét nghiệm" },
]);

const selectedTemplate = ref("invoice");

// Template Variables
const templateVariables = ref([
  { key: "Ten_KH", label: "Tên khách hàng" },
  { key: "SDT", label: "Số điện thoại" },
  { key: "Dia_chi", label: "Địa chỉ" },
  { key: "Ngay_kham", label: "Ngày khám" },
  { key: "Benh_nhan", label: "Tên thú cưng" },
  { key: "Chuan_doan", label: "Chuẩn đoán" },
  { key: "Tong_tien", label: "Tổng tiền" },
  { key: "Bac_si", label: "Tên bác sĩ" },
]);
</script>

<style scoped>
/* Custom styles if needed */
input[type="checkbox"]:checked {
  background-color: #030213;
  border-color: #030213;
}
</style>
