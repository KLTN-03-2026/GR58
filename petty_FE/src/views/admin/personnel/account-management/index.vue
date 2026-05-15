<template>
  <div class="w-full min-h-screen px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col gap-2">
      <h1 class="font-semibold text-2xl text-black">Quản lý Người dùng</h1>
      <p class="font-medium text-base text-gray-500">
        Quản lý tài khoản nhân viên và khách hàng
      </p>
    </div>

    <!-- Content -->
    <div class="flex flex-col gap-8 mt-6">
      <!-- Tabs -->
      <div
        class="bg-[#f3f4f6] flex items-center p-1 rounded-[10px] shadow-sm w-fit"
      >
        <button
          @click="activeTab = 'staff'"
          :class="
            activeTab === 'staff'
              ? 'bg-white shadow-md text-[#0d9488]'
              : 'text-[#4b5563]'
          "
          class="font-medium text-sm leading-5 px-6 py-2.5 rounded-lg transition-all"
        >
          Danh sách Nhân viên
        </button>
        <button
          @click="activeTab = 'customer'"
          :class="
            activeTab === 'customer'
              ? 'bg-white shadow-md text-[#0d9488]'
              : 'text-[#4b5563]'
          "
          class="font-medium text-sm leading-5 px-6 py-2.5 rounded-lg transition-all"
        >
          Danh sách Khách hàng
        </button>
      </div>

      <!-- Staff List -->
      <div
        v-if="activeTab === 'staff'"
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
      >
        <!-- Card Header -->
        <div class="flex items-center justify-between mb-6">
          <h2 class="font-medium text-base leading-4 text-black">
            Danh sách Nhân viên
          </h2>
          <button
            class="bg-[#5a9690] rounded-lg h-9 px-3 py-2 flex items-center gap-2 hover:bg-[#007d72] transition-colors"
            @click="isAddStaffModalOpen = true"
          >
            <AddIcon class="text-white" />
            <span
              class="font-medium text-sm leading-5 text-white tracking-tight"
              >Tạo tài khoản mới</span
            >
          </button>
        </div>

        <!-- Search and Filter -->
        <div class="flex items-center gap-4 mb-6">
          <div class="flex-1 relative">
            <SearchIcon
              class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4"
            />
            <input
              v-model="staffSearchQuery"
              type="text"
              placeholder="Tìm theo tên, SĐT, email..."
              class="w-full bg-[#f3f3f5] border-none rounded-lg h-9 pl-10 pr-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182]"
            />
          </div>
          <div class="relative" ref="roleDropdownRef">
            <button
              type="button"
              @click="isRoleDropdownOpen = !isRoleDropdownOpen"
              class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-[1px] flex items-center justify-between gap-2 min-w-[192px] cursor-pointer hover:bg-[#e9eaec] transition-colors"
            >
              <span
                class="font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                >{{ roleFilterLabel }}</span
              >
              <ChevronDownIcon
                :class="[
                  'transition-transform',
                  isRoleDropdownOpen ? 'rotate-180' : '',
                ]"
              />
            </button>
            <div
              v-if="isRoleDropdownOpen"
              class="absolute right-0 top-full mt-1 w-full min-w-[192px] bg-white border border-gray-200 rounded-lg shadow-lg z-20 overflow-hidden"
            >
              <button
                v-for="opt in roleFilterOptions"
                :key="opt.value"
                type="button"
                @click="selectRoleFilter(opt.value)"
                :class="[
                  'w-full px-3 py-2 text-left text-sm font-medium transition-colors',
                  roleFilter === opt.value
                    ? 'bg-[#e6f4f3] text-[#0d9488]'
                    : 'text-neutral-950 hover:bg-gray-50',
                ]"
              >
                {{ opt.label }}
              </button>
            </div>
          </div>
        </div>

        <!-- Staff Table -->
        <div class="overflow-x-auto mb-6">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-200/60">
                <th
                  class="text-left py-2.5 px-2 font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                >
                  Nhân viên
                </th>
                <th
                  class="text-left py-2.5 px-2 font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                >
                  Thông tin liên hệ
                </th>
                <th
                  class="text-left py-2.5 px-2 font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                >
                  Vai trò
                </th>
                <th
                  class="text-left py-2.5 px-2 font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                >
                  Ngày vào làm
                </th>
                <th
                  class="text-left py-2.5 px-2 font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                >
                  Trạng thái
                </th>
                <th
                  class="text-left py-2.5 px-2 font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                >
                  Lần đăng nhập cuối
                </th>
                <th
                  class="text-right py-2.5 px-2 font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                >
                  Thao tác
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="staff in pagedStaff"
                :key="staff.id"
                class="border-b border-gray-200/60"
              >
                <td class="py-3 px-2">
                  <div class="flex items-center gap-3">
                    <img
                      :src="staff.avatar"
                      alt=""
                      class="w-10 h-10 rounded-full object-cover"
                    />
                    <span
                      class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                      >{{ staff.name }}</span
                    >
                  </div>
                </td>
                <td class="py-3 px-2">
                  <div class="flex flex-col">
                    <span
                      class="font-medium text-sm leading-6 text-[#101828] tracking-tight"
                      >{{ staff.email }}</span
                    >
                    <span
                      class="font-medium text-sm leading-6 text-[#6a7282] tracking-tight"
                      >{{ staff.phone }}</span
                    >
                  </div>
                </td>
                <td class="py-3 px-2">
                  <div class="flex flex-col gap-1">
                    <span
                      v-for="(role, index) in staff.roles"
                      :key="index"
                      :class="[
                        'inline-flex items-center justify-center px-2 py-[3px] rounded-lg text-xs leading-4 font-nunito font-medium w-fit',
                        role.color === 'blue'
                          ? 'bg-blue-100 text-[#1447e6]'
                          : role.color === 'green'
                          ? 'bg-green-100 text-[#008236]'
                          : role.color === 'purple'
                          ? 'bg-purple-100 text-[#8200db]'
                          : 'bg-[#ffe2e2] text-[#c10007]',
                      ]"
                    >
                      {{ role.name }}
                    </span>
                  </div>
                </td>
                <td class="py-3 px-2">
                  <span
                    class="font-medium text-sm leading-5 text-[#4a5565] tracking-tight"
                    >{{ staff.joinDate }}</span
                  >
                </td>
                <td class="py-3 px-2">
                  <span
                    :class="[
                      'inline-flex items-center gap-2 px-2 py-[3px] rounded-lg text-xs leading-4 font-nunito font-medium',
                      staff.status === 'active'
                        ? 'bg-green-100 text-[#008236]'
                        : 'bg-gray-100 text-[#364153]',
                    ]"
                  >
                    {{ staff.status === "active" ? "Hoạt động" : "Đã khóa" }}
                  </span>
                </td>
                <td class="py-3 px-2">
                  <span
                    class="font-medium text-sm leading-5 text-[#4a5565] tracking-tight"
                    >{{ staff.lastLogin }}</span
                  >
                </td>
                <td class="py-3 px-2">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      class="bg-white border border-gray-200/60 rounded-lg w-[38px] h-8 flex items-center justify-center hover:bg-gray-50 transition-colors"
                      @click="handleViewStaff(staff)"
                    >
                      <UpdateIcon class="w-4 h-4" />
                    </button>
                    <button
                      class="bg-white border border-gray-200/60 rounded-lg w-[38px] h-8 flex items-center justify-center hover:bg-gray-50 transition-colors"
                      @click="handleOpenResetPassword(staff)"
                    >
                      <PasswordIcon class="w-4 h-4" />
                    </button>
                    <button
                      v-if="staff.status === 'active'"
                      @click="toggleStaffStatus(staff)"
                      :disabled="staff._loading"
                      :class="[
                        'bg-white border !border-gray-300 rounded-lg px-3 h-8 transition-colors',
                        staff._loading
                          ? 'opacity-50 cursor-not-allowed'
                          : 'hover:bg-gray-50',
                      ]"
                    >
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >Khóa</span
                      >
                    </button>
                    <button
                      v-else
                      @click="toggleStaffStatus(staff)"
                      :disabled="staff._loading"
                      :class="[
                        'rounded-lg px-3 h-8 transition-colors',
                        staff._loading
                          ? 'opacity-50 cursor-not-allowed bg-[#009689]'
                          : 'bg-[#009689] hover:bg-[#009689]',
                      ]"
                    >
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-white tracking-tight"
                        >Kích hoạt</span
                      >
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between">
          <p
            class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
          >
            Hiển thị {{ startIndex }} - {{ endIndex }} của
            {{ filteredStaffList.length }} nhân viên
          </p>
          <div class="flex items-center gap-1">
            <button
              @click="prevPage"
              :disabled="pagesShownCount === 1"
              :class="[
                'rounded-lg h-9 px-3 py-2 flex items-center gap-2 text-[#6b7280]',
                pagesShownCount === 1 ? 'opacity-50' : 'hover:bg-gray-50',
              ]"
            >
              <ChevronLeftIcon />
            </button>

            <div class="flex items-center gap-2">
              <button
                v-for="p in pagesToShow"
                :key="p"
                @click="currentPage = p"
                :class="[
                  'w-9 h-9 rounded-lg flex items-center justify-center border',
                  currentPage === p
                    ? 'border-[#009689] text-[#009689] bg-white'
                    : 'border-gray-200 text-[#101828] bg-white',
                ]"
              >
                <span class="font-medium text-sm leading-5">{{ p }}</span>
              </button>
            </div>

            <button
              @click="nextPage"
              :disabled="pagesShownCount >= totalPages"
              :class="[
                'rounded-lg h-9 px-3 py-2 flex items-center gap-2 text-[#6b7280]',
                pagesShownCount >= totalPages
                  ? 'opacity-50'
                  : 'hover:bg-gray-50',
              ]"
            >
              <ChevronRightIcon />
            </button>
          </div>
        </div>
      </div>

      <!-- Customer List -->
      <div
        v-if="activeTab === 'customer'"
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
      >
        <!-- Card Header -->
        <div class="flex items-center justify-between mb-6">
          <h2
            class="font-nunito text-base leading-4 text-neutral-950 tracking-tight"
          >
            Danh sách Khách hàng
          </h2>
          <p
            class="font-nunito text-sm leading-5 text-[#6a7282] tracking-tight"
          >
            Tổng: {{ filteredCustomerList.length }} khách hàng
          </p>
        </div>

        <!-- Search -->
        <div class="mb-6 relative">
          <SearchIcon
            class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4"
          />
          <input
            v-model="customerSearchQuery"
            type="text"
            placeholder="Tìm theo tên, số điện thoại..."
            class="w-full bg-[#f3f3f5] border-none rounded-lg h-9 pl-10 pr-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182]"
          />
        </div>

        <!-- Customer Table -->
        <div class="overflow-x-auto mb-6">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-200/60">
                <th
                  class="text-left py-2.5 px-2 font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                >
                  Khách hàng
                </th>
                <th
                  class="text-left py-2.5 px-2 font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                >
                  Liên hệ
                </th>
                <th
                  class="text-left py-2.5 px-2 font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                >
                  Số lượng Thú cưng
                </th>
                <th
                  class="text-left py-2.5 px-2 font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                >
                  Tổng chi tiêu
                </th>
                <th
                  class="text-left py-2.5 px-2 font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                >
                  Ngày tham gia
                </th>
                <th
                  class="text-left py-2.5 px-2 font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                >
                  Trạng thái
                </th>
                <th
                  class="text-right py-2.5 px-2 font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                >
                  Thao tác
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="customer in pagedCustomers"
                :key="customer.id"
                class="border-b border-gray-200/60"
              >
                <td class="py-3 px-2">
                  <span
                    class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                    >{{ customer.name }}</span
                  >
                </td>
                <td class="py-3 px-2">
                  <div class="flex flex-col">
                    <span
                      class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
                      >{{ customer.phone }}</span
                    >
                    <span
                      v-if="customer.zalo"
                      class="font-nunito text-base leading-6 text-[#6a7282] tracking-tight"
                      >Zalo: {{ customer.zalo }}</span
                    >
                  </div>
                </td>
                <td class="py-3 px-2">
                  <span
                    class="font-nunito text-sm leading-5 text-[#009689] tracking-tight"
                    >{{ customer.petCount }}</span
                  >
                </td>
                <td class="py-3 px-2">
                  <span
                    class="font-nunito text-sm leading-5 text-[#009689] tracking-tight"
                    >{{ formatCurrency(customer.totalSpent) }}</span
                  >
                </td>
                <td class="py-3 px-2">
                  <span
                    class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                    >{{ customer.joinDate }}</span
                  >
                </td>
                <td class="py-3 px-2">
                  <span
                    :class="[
                      'inline-flex items-center gap-2 px-2 py-[3px] rounded-lg text-xs leading-4 font-nunito font-medium',
                      customer.status === 'active'
                        ? 'bg-green-100 text-[#008236]'
                        : 'bg-[#ffe2e2] text-[#c10007]',
                    ]"
                  >
                    {{ customer.status === "active" ? "Hoạt động" : "Bị chặn" }}
                  </span>
                </td>
                <td class="py-3 px-2">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      class="bg-white border border-gray-200/60 rounded-lg px-3 h-8 flex items-center gap-2 hover:bg-gray-50 transition-colors"
                      @click="handleViewCustomer(customer)"
                    >
                      <EyeIcon class="w-4 h-4" />
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >Xem chi tiết</span
                      >
                    </button>
                    <button
                      @click="handleToggleCustomerStatus(customer)"
                      :title="customer.status === 'active' ? 'Khóa tài khoản' : 'Mở khóa tài khoản'"
                      :class="[
                        'rounded-lg w-8 h-8 flex border !border-gray-800 items-center justify-center transition-colors',
                        customer.status === 'active'
                          ? 'hover:bg-red-50'
                          : 'hover:bg-green-50',
                      ]"
                    >
                      <!-- Đang active → hiện ổ khóa đóng (click sẽ khóa) -->
                      <PasswordIcon v-if="customer.status === 'active'" class="w-4 h-4 text-black" />
                      <!-- Đang blocked → hiện ổ khóa mở (click sẽ mở khóa) -->
                      <LockOpenIcon v-else class="w-4 h-4 text-green-600" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between">
          <p
            class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
          >
            Hiển thị {{ customerStartIndex }} - {{ customerEndIndex }} của
            {{ filteredCustomerList.length }} khách hàng
          </p>
          <div class="flex items-center gap-1">
            <button
              @click="prevCustomerPage"
              :disabled="customerPagesShownCount === 1"
              :class="[
                'rounded-lg h-9 px-3 py-2 flex items-center gap-2 text-[#6b7280]',
                customerPagesShownCount === 1 ? 'opacity-50' : 'hover:bg-gray-50',
              ]"
            >
              <ChevronLeftIcon class="w-4 h-4" />
            </button>

            <div class="flex items-center gap-2">
              <button
                v-for="p in customerPagesToShow"
                :key="p"
                @click="customerCurrentPage = p"
                :class="[
                  'w-9 h-9 rounded-lg flex items-center justify-center border',
                  customerCurrentPage === p
                    ? 'border-[#009689] text-[#009689] bg-white'
                    : 'border-gray-200 text-[#101828] bg-white',
                ]"
              >
                <span class="font-medium text-sm leading-5">{{ p }}</span>
              </button>
            </div>

            <button
              @click="nextCustomerPage"
              :disabled="customerPagesShownCount >= customerTotalPages"
              :class="[
                'rounded-lg h-9 px-3 py-2 flex items-center gap-2 text-[#6b7280]',
                customerPagesShownCount >= customerTotalPages
                  ? 'opacity-50'
                  : 'hover:bg-gray-50',
              ]"
            >
              <ChevronRightIcon class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <ThemNhanVien
      v-if="isAddStaffModalOpen"
      @close="isAddStaffModalOpen = false"
      @submit="handleAddStaff"
    />

    <ChiTietNhanVien
      v-if="isViewStaffModalOpen"
      :staff="selectedStaffForView"
      @close="isViewStaffModalOpen = false"
      @edit="
        (s) => {
          isViewStaffModalOpen = false;
          handleOpenEditStaff(s || selectedStaffForView);
        }
      "
    />

    <ChinhSuaNhanVien
      v-if="isEditStaffModalOpen"
      :staff="selectedStaffForEdit"
      @close="isEditStaffModalOpen = false"
      @updated="handleStaffUpdated"
    />

    <DatMatKhau
      v-if="isResetPasswordModalOpen"
      :staff-name="selectedStaffForReset?.name"
      @close="isResetPasswordModalOpen = false"
      @reset="handleResetPasswordSubmit"
    />

    <ChiTietKhachHang
      v-if="isViewCustomerModalOpen"
      :customer="selectedCustomerForView"
      @close="isViewCustomerModalOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from "vue";
import ThemNhanVien from "./add-staff/index.vue";
import ChiTietNhanVien from "./staff-detail/index.vue";
import ChinhSuaNhanVien from "./edit-staff/index.vue";
import DatMatKhau from "./set-password/index.vue";
import ChiTietKhachHang from "./customer-detail/index.vue";
import { listNhanVien } from "@/utils/nhanVien";
import api from "@/utils/api";
import { resolveImageUrl } from "@/utils/image";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
// Icon SVG
import AddIcon from "@/assets/svg/add.svg";
import SearchIcon from "@/assets/svg/search.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import ChevronLeftIcon from "@/assets/svg/chevron-left.svg";
import ChevronRightIcon from "@/assets/svg/chevron-right.svg";
import UpdateIcon from "@/assets/svg/update.svg";
import EyeIcon from "@/assets/svg/eye.svg";
import PasswordIcon from "@/assets/svg/password.svg";
import LockOpenIcon from "@/assets/svg/lock-open.svg";

// Active Tab
const activeTab = ref("staff"); // 'staff' or 'customer'
const isAddStaffModalOpen = ref(false);
const isViewStaffModalOpen = ref(false);
const selectedStaffForView = ref(null);
const isEditStaffModalOpen = ref(false);
const selectedStaffForEdit = ref(null);
const isResetPasswordModalOpen = ref(false);
const selectedStaffForReset = ref(null);
const isViewCustomerModalOpen = ref(false);
const selectedCustomerForView = ref(null);

// Search Queries
const staffSearchQuery = ref("");
const customerSearchQuery = ref("");

// Role filter dropdown
const roleFilter = ref("all"); // 'all' | 'bac_si' | 'y_ta'
const isRoleDropdownOpen = ref(false);
const roleDropdownRef = ref(null);
const roleFilterOptions = [
  { value: "all", label: "Tất cả" },
  { value: "bac_si", label: "Bác sĩ" },
  { value: "y_ta", label: "Y tá" },
];
const roleFilterLabel = computed(
  () =>
    roleFilterOptions.find((o) => o.value === roleFilter.value)?.label ||
    "Tất cả"
);
const selectRoleFilter = (value) => {
  roleFilter.value = value;
  isRoleDropdownOpen.value = false;
};
const handleClickOutsideRole = (e) => {
  if (
    roleDropdownRef.value &&
    !roleDropdownRef.value.contains(e.target)
  ) {
    isRoleDropdownOpen.value = false;
  }
};
onMounted(() => {
  document.addEventListener("click", handleClickOutsideRole);
});
onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutsideRole);
});

// Staff List Data (populated from API)
const staffList = ref([]);

// Pagination
const pageSize = ref(5);
const currentPage = ref(1);
// how many numbered page buttons are currently shown (start with 1)
const pagesShownCount = ref(1);

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredStaffList.value.length / pageSize.value))
);

// Filter staff by search query + role
const normalize = (s) =>
  String(s || "")
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "");

const filteredStaffList = computed(() => {
  const q = normalize(staffSearchQuery.value).trim();
  const role = roleFilter.value;
  return staffList.value.filter((s) => {
    // Role filter (compare against original backend role key stored in roles[0]?.name OR keep raw)
    if (role !== "all") {
      const matchedRole =
        (role === "bac_si" && s.roles?.[0]?.name === "Bác sĩ") ||
        (role === "y_ta" && s.roles?.[0]?.name === "Y tá");
      if (!matchedRole) return false;
    }
    if (!q) return true;
    return (
      normalize(s.name).includes(q) ||
      normalize(s.email).includes(q) ||
      normalize(s.phone).includes(q)
    );
  });
});

const pagedStaff = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value;
  return filteredStaffList.value.slice(start, start + pageSize.value);
});

const startIndex = computed(() =>
  filteredStaffList.value.length === 0
    ? 0
    : (currentPage.value - 1) * pageSize.value + 1
);
const endIndex = computed(() =>
  Math.min(filteredStaffList.value.length, currentPage.value * pageSize.value)
);

// Reset pagination whenever filter/search changes
watch([staffSearchQuery, roleFilter], () => {
  currentPage.value = 1;
  pagesShownCount.value = 1;
});

// Previous/Next here control the visible page buttons (not the current page selection)
const prevPage = () => {
  if (pagesShownCount.value > 1) {
    pagesShownCount.value -= 1;
    // ensure currentPage is within available pages
    if (currentPage.value > pagesShownCount.value)
      currentPage.value = pagesShownCount.value;
  }
};

const nextPage = () => {
  if (pagesShownCount.value < totalPages.value) {
    pagesShownCount.value += 1;
    // automatically select the newly shown page
    currentPage.value = pagesShownCount.value;
  }
};

// pagesToShow: show page buttons from 1..currentPage (so Next adds a new button)
const pagesToShow = computed(() => {
  const max = Math.max(1, Math.min(pagesShownCount.value, totalPages.value));
  const arr = [];
  for (let i = 1; i <= max; i++) arr.push(i);
  return arr;
});

// Helpers
const formatDate = (iso) => {
  if (!iso) return null;
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return null;
  const dd = String(d.getDate()).padStart(2, "0");
  const mm = String(d.getMonth() + 1).padStart(2, "0");
  const yyyy = d.getFullYear();
  return `${dd}/${mm}/${yyyy}`;
};

const formatDateTime = (iso) => {
  if (!iso) return null;
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return null;
  const dd = String(d.getDate()).padStart(2, "0");
  const mm = String(d.getMonth() + 1).padStart(2, "0");
  const yyyy = d.getFullYear();
  const hh = String(d.getHours()).padStart(2, "0");
  const min = String(d.getMinutes()).padStart(2, "0");
  return `${dd}/${mm}/${yyyy} ${hh}:${min}`;
};

const mapRole = (vai_tro) => {
  if (!vai_tro) return [{ name: "Chưa phân vai", color: "gray" }];
  const r = String(vai_tro).toLowerCase();
  if (r === "bac_si") return [{ name: "Bác sĩ", color: "blue" }];
  if (r === "y_ta") return [{ name: "Y tá", color: "green" }];
  // fallback
  return [{ name: r.replace(/_/g, " "), color: "purple" }];
};

const populateStaffList = (items) => {
  staffList.value = items.map((it) => ({
    id: it.id,
    name: it.full_name || it.name || "—",
    avatar: resolveImageUrl(it.avatar || it.anh_dai_dien, "https://www.gravatar.com/avatar?d=mp"),
    email: it.email || "",
    phone: it.so_dien_thoai || it.phone || "",
    roles: mapRole(it.vai_tro),
    joinDate: formatDate(it.created_at),
    // map backend trang_thai ('hoat_dong'|'da_khoa') to frontend 'active'|'locked'
    status:
      it.trang_thai === "hoat_dong"
        ? "active"
        : it.trang_thai === "da_khoa"
        ? "locked"
        : it.status || "active",
    lastLogin: it.last_login_at
      ? formatDateTime(it.last_login_at)
      : it.last_login
      ? formatDateTime(it.last_login)
      : "Chưa đăng nhập",
    // Keep the original backend record so detail/edit modals can prefill all fields
    _raw: it,
  }));
};

const reloadStaffList = async () => {
  try {
    const items = await listNhanVien();
    populateStaffList(items);
  } catch (e) {
    console.error("Failed to reload staff list", e);
  }
};

onMounted(async () => {
  try {
    const items = await listNhanVien();
    populateStaffList(items);
  } catch (e) {
    console.error("Failed to load staff list", e);
  }
  // Load customer list from API
  await loadCustomerList();

});

// Customer List Data
const customerList = ref([]);
const customerLoading = ref(false);

// Customer Pagination
const customerPageSize = ref(10);
const customerCurrentPage = ref(1);
const customerPagesShownCount = ref(1);

const loadCustomerList = async () => {
  customerLoading.value = true;
  try {
    const res = await api.get("/khach-hang");
    console.log("[loadCustomerList] raw response:", res.data);
    const items = res.data?.data || [];
    customerList.value = items.map((it) => {
      const mapped = {
        id: it.id,
        name: it.full_name || "—",
        phone: it.so_dien_thoai || it.phone || "",
        zalo: null,
        petCount: it.thu_cung?.length ? `${it.thu_cung.length} bé` : "0 bé",
        totalSpent: 0,
        joinDate: it.updated_at ? formatDate(it.updated_at) : "—",
        status: it.trang_thai === "blocked" ? "blocked" : "active",
        avatar: it.anh_dai_dien || "https://www.gravatar.com/avatar?d=mp",
        email: it.email || "",
        address: it.dia_chi || it.address || "",
        pets: (it.thu_cung || []).map((p) => p.ten_thu_cung),
        _raw: it,
      };
      console.log(`[loadCustomerList] id=${it.id} trang_thai="${it.trang_thai}" → status="${mapped.status}"`);
      return mapped;
    });
  } catch (e) {
    console.error("[loadCustomerList] error:", e);
  } finally {
    customerLoading.value = false;
  }
};

// Filter customer by search query (client-side)
// Match by word-start so "an" matches "An" in "Nguyễn Văn An" but not "Giang", "Lan", etc.
const filteredCustomerList = computed(() => {
  const q = normalize(customerSearchQuery.value).trim();
  if (!q) return customerList.value;
  return customerList.value.filter((c) => {
    // Phone: exact substring match is fine
    if (normalize(c.phone).includes(q)) return true;
    // Name: match only at word boundaries (start of any word)
    const nameParts = normalize(c.name).split(/\s+/);
    return nameParts.some((part) => part.startsWith(q));
  });
});

const customerTotalPages = computed(() =>
  Math.max(1, Math.ceil(filteredCustomerList.value.length / customerPageSize.value))
);

const pagedCustomers = computed(() => {
  const start = (customerCurrentPage.value - 1) * customerPageSize.value;
  return filteredCustomerList.value.slice(start, start + customerPageSize.value);
});

const customerStartIndex = computed(() =>
  filteredCustomerList.value.length === 0
    ? 0
    : (customerCurrentPage.value - 1) * customerPageSize.value + 1
);
const customerEndIndex = computed(() =>
  Math.min(filteredCustomerList.value.length, customerCurrentPage.value * customerPageSize.value)
);

// Reset customer pagination when search changes
watch(customerSearchQuery, () => {
  customerCurrentPage.value = 1;
  customerPagesShownCount.value = 1;
});

const prevCustomerPage = () => {
  if (customerPagesShownCount.value > 1) {
    customerPagesShownCount.value -= 1;
    if (customerCurrentPage.value > customerPagesShownCount.value)
      customerCurrentPage.value = customerPagesShownCount.value;
  }
};

const nextCustomerPage = () => {
  if (customerPagesShownCount.value < customerTotalPages.value) {
    customerPagesShownCount.value += 1;
    customerCurrentPage.value = customerPagesShownCount.value;
  }
};

const customerPagesToShow = computed(() => {
  const max = Math.max(1, Math.min(customerPagesShownCount.value, customerTotalPages.value));
  const arr = [];
  for (let i = 1; i <= max; i++) arr.push(i);
  return arr;
});

// Methods
const formatCurrency = (amount) => {
  return amount.toLocaleString("vi-VN") + " ₫";
};

const handleAddStaff = (data) => {
  console.log("New staff data:", data);
  // Logic to add new staff goes here
  isAddStaffModalOpen.value = false;
};

const handleViewStaff = (staff) => {
  // Prefer the original backend record so the detail modal shows all fields.
  selectedStaffForView.value = {
    ...(staff?._raw || {}),
    // Keep the FE-mapped fields the modal already uses (joinDate label, lastLogin, etc.)
    joinDate: staff?.joinDate,
    lastLogin: staff?.lastLogin,
    status: staff?.status,
    name: staff?.name,
    avatar: staff?.avatar,
    roles: staff?.roles,
  };
  isViewStaffModalOpen.value = true;
};

const handleOpenEditStaff = (staff) => {
  selectedStaffForEdit.value = staff?._raw
    ? { ...staff._raw, id: staff.id }
    : { ...staff };
  isEditStaffModalOpen.value = true;
};

const handleStaffUpdated = async () => {
  await reloadStaffList();
};

const handleOpenResetPassword = (staff) => {
  selectedStaffForReset.value = staff;
  isResetPasswordModalOpen.value = true;
};

const isResetSubmitting = ref(false);

const handleResetPasswordSubmit = async (data) => {
  // data is expected to contain { password, password_confirmation }
  const staff = selectedStaffForReset.value;
  if (!staff || !staff.id) {
    showErrorToast("Lỗi", "Không có nhân viên để đặt lại mật khẩu.");
    return;
  }

  try {
    isResetSubmitting.value = true;
    const payload = {
      password: data.password,
      password_confirmation:
        data.password_confirmation || data.password_confirmation,
    };
    await api.patch(`/nhan-vien/${staff.id}/mat-khau`, payload);
    showSuccessToast(
      "Thành công",
      `Đã đổi mật khẩu cho ${staff.name || staff.full_name || "nhân viên"}`
    );
    isResetPasswordModalOpen.value = false;
  } catch (e) {
    console.error("Reset password failed", e);
    const msg = e?.response?.data?.message || "Không thể đặt lại mật khẩu.";
    showErrorToast("Lỗi", msg);
  } finally {
    isResetSubmitting.value = false;
  }
};

// Toggle lock/unlock for a staff member by calling backend endpoints
const toggleStaffStatus = async (staff) => {
  if (!staff || !staff.id) return;
  const isActive = staff.status === "active";
  const action = isActive ? "khoa" : "mo-khoa";
  try {
    // set local loading flag
    staff._loading = true;
    await api.patch(`/nhan-vien/${staff.id}/${action}`);
    // update local status according to action
    staff.status = isActive ? "locked" : "active";
    showSuccessToast(
      "Thành công",
      isActive ? "Tài khoản đã bị khóa." : "Tài khoản đã được mở khóa."
    );
  } catch (e) {
    console.error("Toggle staff status failed", e);
    const msg =
      e?.response?.data?.message || "Không thể thay đổi trạng thái tài khoản.";
    showErrorToast("Lỗi", msg);
  } finally {
    staff._loading = false;
  }
};

const handleViewCustomer = (customer) => {
  selectedCustomerForView.value = customer;
  isViewCustomerModalOpen.value = true;
};

// Toggle customer status — call backend
const handleToggleCustomerStatus = async (customer) => {
  if (!customer) return;
  const newStatus = customer.status === "active" ? "blocked" : "active";
  console.log("[toggleCustomerStatus] customer:", customer);
  console.log("[toggleCustomerStatus] newStatus:", newStatus);
  try {
    const res = await api.patch(`/khach-hang/${customer.id}/trang-thai`, {
      trang_thai: newStatus,
    });
    console.log("[toggleCustomerStatus] response:", res.data);
    customer.status = newStatus;
    showSuccessToast(
      "Thành công",
      newStatus === "active"
        ? "Khách hàng đã được kích hoạt."
        : "Khách hàng đã bị chặn."
    );
  } catch (e) {
    console.error("[toggleCustomerStatus] error:", e);
    console.error("[toggleCustomerStatus] response data:", e?.response?.data);
    console.error("[toggleCustomerStatus] status code:", e?.response?.status);
    const msg = e?.response?.data?.message || "Không thể thay đổi trạng thái.";
    showErrorToast("Lỗi", msg);
  }
};
</script>

<style scoped>
/* Custom scrollbar for tables */
.overflow-x-auto::-webkit-scrollbar {
  height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
