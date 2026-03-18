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
          <button
            class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-[1px] flex items-center justify-between gap-2 min-w-[192px]"
          >
            <span
              class="font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >Tất cả</span
            >
            <ChevronDownIcon />
          </button>
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
            {{ staffList.length }} nhân viên
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
            Tổng: {{ customerList.length }} khách hàng
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
                v-for="customer in customerList"
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
                      :class="[
                        'rounded-lg w-8 h-8 flex border !border-gray-800 items-center justify-center transition-colors',
                        customer.status === 'active'
                          ? 'hover:bg-gray-100'
                          : 'hover:bg-green-50',
                      ]"
                    >
                      <PasswordIcon class="w-4 h-4 text-black" />
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
            Hiển thị 1 - {{ customerList.length }} của
            {{ customerList.length }} khách hàng
          </p>
          <div class="flex items-center gap-1">
            <button
              class="opacity-50 rounded-lg h-9 px-3 py-2 flex items-center gap-2"
              disabled
            >
              <ChevronLeftIcon class="w-4 h-4" />
            </button>
            <button
              class="bg-white border !border-[#009689] rounded-lg w-9 h-9 flex items-center justify-center"
            >
              <span
                class="font-nunito font-medium text-sm leading-5 text-[#009689] tracking-tight"
                >1</span
              >
            </button>
            <button
              class="opacity-50 rounded-lg h-9 px-3 py-2 flex items-center gap-2"
              disabled
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
        () => {
          isViewStaffModalOpen = false; /* Add logic to open edit modal */
        }
      "
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
import { ref, computed, onMounted } from "vue";
import ThemNhanVien from "./ThemNhanVien/index.vue";
import ChiTietNhanVien from "./ChiTietNhanVien/index.vue";
import DatMatKhau from "./DatMatKhau/index.vue";
import ChiTietKhachHang from "./ChiTietKhachHang/index.vue";
import { listNhanVien } from "@/utils/nhanVien";
import api from "@/utils/api";
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

// Active Tab
const activeTab = ref("staff"); // 'staff' or 'customer'
const isAddStaffModalOpen = ref(false);
const isViewStaffModalOpen = ref(false);
const selectedStaffForView = ref(null);
const isResetPasswordModalOpen = ref(false);
const selectedStaffForReset = ref(null);
const isViewCustomerModalOpen = ref(false);
const selectedCustomerForView = ref(null);

// Search Queries
const staffSearchQuery = ref("");
const customerSearchQuery = ref("");

// Staff List Data (populated from API)
const staffList = ref([]);

// Pagination
const pageSize = ref(5);
const currentPage = ref(1);
// how many numbered page buttons are currently shown (start with 1)
const pagesShownCount = ref(1);

const totalPages = computed(() =>
  Math.max(1, Math.ceil(staffList.value.length / pageSize.value))
);

const pagedStaff = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value;
  return staffList.value.slice(start, start + pageSize.value);
});

const startIndex = computed(() =>
  staffList.value.length === 0
    ? 0
    : (currentPage.value - 1) * pageSize.value + 1
);
const endIndex = computed(() =>
  Math.min(staffList.value.length, currentPage.value * pageSize.value)
);

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
    avatar:
      it.avatar || it.anh_dai_dien || "https://www.gravatar.com/avatar?d=mp",
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
  }));
};

onMounted(async () => {
  try {
    const items = await listNhanVien();
    populateStaffList(items);
  } catch (e) {
    console.error("Failed to load staff list", e);
  }
});

// Customer List Data
const customerList = ref([
  {
    id: 1,
    name: "Trần Thị Hương",
    phone: "0912345678",
    zalo: "0912345678",
    petCount: "2 bé",
    totalSpent: 5200000,
    joinDate: "2024-11-20",
    status: "active",
    avatar:
      "https://www.figma.com/api/mcp/asset/69ab4ed0-d5a9-4482-8a8e-baeaf0347487",
    email: "huong.tran@email.com",
    rank: "Gold",
    rankIcon: "🥇",
    address: "123 Lê Lợi, Quận 1, TP.HCM",
    pets: ["Milo", "Luna"],
    recentVisits: [
      {
        service: "Khám tổng quát",
        date: "15/11/2024",
        doctor: "BS. Nguyễn Văn A",
        cost: 500000,
      },
    ],
  },
  {
    id: 2,
    name: "Nguyễn Văn Kiên",
    phone: "0923456789",
    zalo: null,
    petCount: "1 bé",
    totalSpent: 2800000,
    joinDate: "2024-10-15",
    status: "active",
    avatar:
      "https://www.figma.com/api/mcp/asset/69ab4ed0-d5a9-4482-8a8e-baeaf0347487",
    email: "kien.nguyen@email.com",
    rank: "Silver",
    rankIcon: "🥈",
    address: "456 Nguyễn Trãi, Quận 5, TP.HCM",
    pets: ["Lu"],
    recentVisits: [
      {
        service: "Tiêm phòng",
        date: "10/10/2024",
        doctor: "BS. Phạm Minh D",
        cost: 300000,
      },
    ],
  },
  {
    id: 3,
    name: "Lê Thị Mai",
    phone: "0934567890",
    zalo: "0934567890",
    petCount: "3 bé",
    totalSpent: 8900000,
    joinDate: "2024-09-01",
    status: "active",
    avatar:
      "https://www.figma.com/api/mcp/asset/69ab4ed0-d5a9-4482-8a8e-baeaf0347487",
    email: "mai.le@email.com",
    rank: "Gold",
    rankIcon: "🥇",
    address: "789 Võ Văn Kiệt, Quận 1, TP.HCM",
    pets: ["Mimi", "Nunu", "Kiki"],
    recentVisits: [
      {
        service: "Spa trọn gói",
        date: "01/11/2024",
        doctor: "KTV. Trần Thị B",
        cost: 800000,
      },
    ],
  },
  {
    id: 4,
    name: "Phạm Thanh Nam",
    phone: "0945678901",
    zalo: null,
    petCount: "1 bé",
    totalSpent: 450000,
    joinDate: "2024-11-10",
    status: "blocked",
    avatar:
      "https://www.figma.com/api/mcp/asset/69ab4ed0-d5a9-4482-8a8e-baeaf0347487",
    email: "nam.pham@email.com",
    rank: "Bronze",
    rankIcon: "🥉",
    address: "321 Điện Biên Phủ, Bình Thạnh, TP.HCM",
    pets: ["Rex"],
    recentVisits: [
      {
        service: "Tư vấn dinh dưỡng",
        date: "10/11/2024",
        doctor: "BS. Nguyễn Văn A",
        cost: 0,
      },
    ],
  },
]);

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
  selectedStaffForView.value = staff;
  isViewStaffModalOpen.value = true;
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

// Toggle customer status locally (no backend endpoint provided in the repo attachments)
const handleToggleCustomerStatus = (customer) => {
  if (!customer) return;
  // assume statuses: 'active' or 'blocked'
  customer.status = customer.status === "active" ? "blocked" : "active";
  showSuccessToast(
    "Thành công",
    customer.status === "active"
      ? "Khách hàng đã được kích hoạt."
      : "Khách hàng đã bị chặn."
  );
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
