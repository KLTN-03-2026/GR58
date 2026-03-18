<template>
  <!-- Header Component - Desktop & Mobile Responsive -->
  <header
    class="fixed top-0 left-0 right-0 z-50 px-6 md:px-14 py-0 h-16 flex items-center justify-between transition-all duration-300"
    :class="
      isScrolled
        ? 'bg-[#eeeeee]/40 backdrop-blur-md shadow-md border-b border-[#d4d4d8]/50'
        : 'bg-[#eeeeee] border-b border-[#d4d4d8]'
    "
  >
    <!-- Logo Section -->
    <router-link to="/" class="flex items-center gap-2 h-8 shrink-0">
      <!-- Petty Icon -->
      <div class="w-32 h-8 relative">
        <img
          src="/src/assets/img_imports/Petty Logo.png"
          alt="Petty Icon"
          class="w-full h-full object-contain"
        />
      </div>
    </router-link>

    <!-- Desktop Navigation Menu -->
    <nav class="hidden md:flex items-center gap-1.5 h-12">
      <!-- Trang Chủ -->
      <router-link
        to="/"
        class="flex items-center px-2.5 py-1.5 font-['Nunito_Sans'] font-bold text-base leading-5 text-gray-500 rounded-md transition-all duration-200 hover:text-black whitespace-nowrap"
        active-class="!text-[#222831] !font-black"
      >
        Trang Chủ
      </router-link>

      <!-- Dịch Vụ -->
      <router-link
        to="/dich-vu"
        class="flex items-center px-2.5 py-1.5 font-['Nunito_Sans'] font-bold text-base leading-5 text-gray-500 rounded-md transition-all duration-200 hover:text-black whitespace-nowrap"
        active-class="!text-[#222831] !font-black"
      >
        Dịch Vụ
      </router-link>

      <!-- Giới Thiệu -->
      <router-link
        to="/about"
        class="flex items-center px-2.5 py-1.5 font-['Nunito_Sans'] font-bold text-base leading-5 text-gray-500 rounded-md transition-all duration-200 hover:text-black whitespace-nowrap"
        active-class="!text-[#222831] !font-black"
      >
        Giới Thiệu
      </router-link>

      <!-- Liên Hệ -->
      <router-link
        to="/lien-he"
        class="flex items-center px-2.5 py-1.5 font-['Nunito_Sans'] font-bold text-base leading-5 text-gray-500 rounded-md transition-all duration-200 hover:text-black whitespace-nowrap"
        active-class="!text-[#222831] !font-black"
      >
        Liên Hệ
      </router-link>

      <!-- Diễn Đàn -->
      <router-link
        to="/dien-dan"
        class="flex items-center gap-0 px-2.5 py-1.5 font-['Nunito_Sans'] font-bold text-base leading-5 text-gray-500 rounded-md transition-all duration-200 hover:text-black whitespace-nowrap"
        active-class="!text-[#222831] !font-black"
      >
        <span>Diễn Đàn</span>
        <!-- External Link Icon - Import từ file SVG -->
        <ArrowIcon class="w-3 h-3 ml-0.5 [&_path]:stroke-[#90a1b9]" />
      </router-link>
    </nav>

    <!-- Mobile Menu Button -->
    <button
      @click="mobileMenuOpen = !mobileMenuOpen"
      class="md:hidden p-2 rounded-md hover:bg-gray-200 transition-colors z-50"
      aria-label="Toggle menu"
    >
      <svg
        class="w-6 h-6 text-gray-600"
        :class="{ hidden: mobileMenuOpen }"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 6h16M4 12h16M4 18h16"
        />
      </svg>
      <svg
        class="w-6 h-6 text-gray-600"
        :class="{ hidden: !mobileMenuOpen }"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 18L18 6M6 6l12 12"
        />
      </svg>
    </button>

    <!-- Right Side Buttons (Desktop) -->
    <div class="hidden md:flex items-center gap-1.5 shrink-0">
      <template v-if="user">
        <!-- Bell icon -->
        <div class="relative">
          <button
            @click="onBellClick"
            @mouseenter="showNotifications()"
            @mouseleave="startHideNotificationsTimer()"
            class="p-2 rounded-md hover:bg-gray-100 transition"
            aria-label="Thông báo"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="w-6 h-6 text-gray-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.5"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118.6 14.6V11a6 6 0 10-12 0v3.6c0 .538-.214 1.055-.595 1.445L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
              />
            </svg>
          </button>

          <!-- Notifications popup (hover) -->
          <div
            v-if="notificationsOpen"
            ref="notifRef"
            @mouseenter="clearHideNotificationsTimer()"
            @mouseleave="hideNotifications()"
            class="absolute right-0 mt-2 w-80 bg-white border !border-black/15 rounded-lg shadow-lg z-50 p-3"
            style="max-height: 320px; overflow: auto"
          >
            <ul class="space-y-3">
              <li
                v-for="(n, idx) in notifications"
                :key="idx"
                class="flex gap-3 items-start"
              >
                <div
                  class="flex-shrink-0 w-8 h-8 rounded-md bg-gray-100 flex items-center justify-center text-teal-600"
                >
                  <component
                    :is="iconMap[n.icon] || CalendarDot"
                    class="w-5 h-5"
                  />
                </div>
                <div class="flex-1">
                  <div class="text-sm font-semibold text-gray-800">
                    {{ n.title }}
                  </div>
                  <div class="text-xs text-gray-500">{{ n.message }}</div>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- User dropdown -->
        <div class="relative" ref="dropdownRef">
          <button
            @click="toggleDropdown"
            class="flex items-center gap-2 px-2 py-1 rounded-md hover:bg-gray-100 transition"
          >
            <img
              :src="userAvatar"
              alt="avatar"
              class="w-7 h-7 rounded-full object-cover"
            />
            <span class="text-sm font-semibold text-gray-700">{{ ten }}</span>
            <svg
              class="w-4 h-4 text-gray-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              />
            </svg>
          </button>

          <div
            v-if="dropdownOpen"
            class="absolute right-0 mt-3 w-60 bg-white border !border-black/15 rounded-md shadow-lg z-50"
          >
            <ul class="divide-y">
              <li
                class="px-3 py-2 hover:bg-gray-50 cursor-pointer"
                @click="navigateTo('/khach-hang/trang-cua-toi')"
              >
                <div class="flex items-center gap-3">
                  <div
                    class="w-8 h-8 flex items-center justify-center rounded-md bg-gray-100 text-gray-700"
                  >
                    <component :is="UserRound" class="w-4 h-4" />
                  </div>
                  <span class="text-sm text-gray-800">Trang của tôi</span>
                </div>
              </li>
              <li
                class="px-3 py-2 hover:bg-gray-50 cursor-pointer"
                @click="navigateTo('/khach-hang/quan-ly-thong-tin-ca-nhan')"
              >
                <div class="flex items-center gap-3">
                  <div
                    class="w-8 h-8 flex items-center justify-center rounded-md bg-gray-100 text-gray-700"
                  >
                    <component :is="InfoCircle" class="w-4 h-4" />
                  </div>
                  <span class="text-sm text-gray-800">Thông tin cá nhân</span>
                </div>
              </li>
              <li
                class="px-3 py-2 hover:bg-gray-50 cursor-pointer"
                @click="navigateTo('/khach-hang/thu-cung-cua-toi')"
              >
                <div class="flex items-center gap-3">
                  <div
                    class="w-8 h-8 flex items-center justify-center rounded-md bg-gray-100 text-teal-600"
                  >
                    <component :is="DogIcon" class="w-4 h-4" />
                  </div>
                  <span class="text-sm text-gray-800">Thú cưng của tôi</span>
                </div>
              </li>
              <li
                class="px-3 py-2 hover:bg-gray-50 cursor-pointer"
                @click="navigateTo('/khach-hang/lich-hen')"
              >
                <div class="flex items-center gap-3">
                  <div
                    class="w-8 h-8 flex items-center justify-center rounded-md bg-gray-100 text-gray-700"
                  >
                    <component :is="CalendarDot" class="w-4 h-4" />
                  </div>
                  <span class="text-sm text-gray-800">Lịch hẹn</span>
                </div>
              </li>
              <li
                class="px-3 py-2 hover:bg-gray-50 cursor-pointer"
                @click="navigateTo('/khach-hang/thanh-toan')"
              >
                <div class="flex items-center gap-3">
                  <div
                    class="w-8 h-8 flex items-center justify-center rounded-md bg-gray-100 text-gray-700"
                  >
                    <component :is="CreditCardPay" class="w-4 h-4" />
                  </div>
                  <span class="text-sm text-gray-800 "
                    >Hoá đơn & Thanh toán</span
                  >
                </div>
              </li>
              <li
                class="px-3 py-2 hover:bg-gray-50 cursor-pointer"
                @click="navigateTo('/khach-hang/tro-giup-lien-he')"
              >
                <div class="flex items-center gap-3">
                  <div
                    class="w-8 h-8 flex items-center justify-center rounded-md bg-gray-100 text-gray-700"
                  >
                    <component :is="HelpIcon" class="w-4 h-4" />
                  </div>
                  <span class="text-sm text-gray-800">Trợ giúp & Liên hệ</span>
                </div>
              </li>
              <li class="px-3 py-2">
                <button
                  @click="handleLogout"
                  class="w-full flex items-center gap-3 text-red-600 hover:bg-gray-50 px-1 py-1"
                >
                  <div
                    class="w-8 h-8 flex items-center justify-center rounded-md bg-gray-100 text-red-600"
                  >
                    <component :is="Logout2" class="w-4 h-4" />
                  </div>
                  <span class="text-sm font-semibold">Đăng Xuất</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </template>
      <template v-else>
        <!-- Đăng Ký Button -->
        <router-link to="/khach-hang/dang-ky" v-slot="{ isActive }" custom>
          <div
            :class="[
              'flex items-center gap-1.5 p-1.5 font-[\'Nunito Sans\'] font-bold text-sm leading-5 rounded-md transition-all duration-200 hover:bg-slate-50 hover:text-black cursor-pointer group',
              isActive ? '!bg-white !text-black' : 'text-gray-500',
            ]"
            @click="$router.push('/khach-hang/dang-ky')"
          >
            <Register
              :class="[
                'w-6 h-6 transition-all',
                isActive
                  ? '[&_path]:stroke-black'
                  : '[&_path]:stroke-gray-500 group-hover:[&_path]:stroke-black',
              ]"
            />
            <span>Đăng Ký</span>
          </div>
        </router-link>

        <!-- Đăng Nhập Button -->
        <router-link to="/khach-hang/dang-nhap" v-slot="{ isActive }" custom>
          <div
            :class="[
              'flex items-center gap-1.5 p-1.5 font-[\'Nunito Sans\'] font-bold text-sm leading-5 rounded-md transition-all duration-200 hover:bg-slate-50 hover:text-black cursor-pointer group',
              isActive ? '!bg-white !text-black' : 'text-gray-500',
            ]"
            @click="$router.push('/khach-hang/dang-nhap')"
          >
            <LogIn
              :class="[
                'w-6 h-6 transition-all',
                isActive
                  ? '[&_path]:stroke-black'
                  : '[&_path]:stroke-gray-500 group-hover:[&_path]:stroke-black',
              ]"
            />
            <span>Đăng Nhập</span>
          </div>
        </router-link>
      </template>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div
      v-if="mobileMenuOpen"
      class="md:hidden absolute top-16 left-0 right-0 bg-[#eeeeee] border-b border-[#d4d4d8] shadow-lg z-40"
    >
      <div class="flex flex-col p-4 gap-2">
        <!-- Mobile Navigation Links -->
        <router-link
          to="/"
          class="px-4 py-3 font-['Nunito_Sans'] font-bold text-base text-gray-700 rounded-md transition-colors hover:bg-[#d4d4d8]"
          @click="mobileMenuOpen = false"
        >
          Trang Chủ
        </router-link>
        <router-link
          to="/dich-vu"
          class="px-4 py-3 font-['Nunito_Sans'] font-bold text-base text-gray-700 rounded-md transition-colors hover:bg-[#d4d4d8]"
          @click="mobileMenuOpen = false"
        >
          Dịch Vụ
        </router-link>
        <router-link
          to="/about"
          class="px-4 py-3 font-['Nunito_Sans'] font-bold text-base text-gray-700 rounded-md transition-colors hover:bg-[#d4d4d8]"
          @click="mobileMenuOpen = false"
        >
          Giới Thiệu
        </router-link>
        <router-link
          to="/lien-he"
          class="px-4 py-3 font-['Nunito_Sans'] font-bold text-base text-gray-700 rounded-md transition-colors hover:bg-[#d4d4d8]"
          @click="mobileMenuOpen = false"
        >
          Liên Hệ
        </router-link>
        <router-link
          to="/dien-dan"
          class="px-4 py-3 font-['Nunito_Sans'] font-bold text-base text-gray-700 rounded-md transition-colors hover:bg-[#d4d4d8]"
          @click="mobileMenuOpen = false"
        >
          Diễn Đàn
        </router-link>

        <!-- Mobile Auth Buttons -->
        <div class="border-t border-gray-300 mt-2 pt-2 flex flex-col gap-2">
          <template v-if="user">
            <div class="px-4 py-3 text-sm text-gray-700">
              Xin chào, <span class="font-semibold">{{ ten }}</span>
            </div>
            <button
              @click="handleMobileLogout"
              class="text-left px-4 py-3 font-['DM_Sans'] font-semibold text-sm text-red-600 hover:bg-[#d4d4d8]"
            >
              Đăng xuất
            </button>
          </template>
          <template v-else>
            <router-link
              to="/khach-hang/dang-ky"
              class="flex items-center gap-2 px-4 py-3 font-['DM_Sans'] font-semibold text-sm text-gray-600 transition-colors rounded-md hover:bg-[#d4d4d8]"
              @click="mobileMenuOpen = false"
              >Đăng Ký</router-link
            >
            <router-link
              to="/khach-hang/dang-nhap"
              class="flex items-center gap-2 px-4 py-3 font-['DM_Sans'] font-semibold text-sm text-gray-600 transition-colors rounded-md hover:bg-[#d4d4d8]"
              @click="mobileMenuOpen = false"
              >Đăng Nhập</router-link
            >
          </template>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import ArrowIcon from "../../assets/svg/arrow-up-right.svg";
import Register from "../../assets/svg/registered.svg";
import LogIn from "../../assets/svg/log-in.svg";
// header/menu icons (from assets/svg)
import UserRound from "../../assets/svg/user-round.svg";
import InfoCircle from "../../assets/svg/info-circle.svg";
import DogIcon from "../../assets/svg/dog.svg";
import CalendarDot from "../../assets/svg/calendar-dot.svg";
import CreditCardPay from "../../assets/svg/credit-card-pay.svg";
import HelpIcon from "../../assets/svg/help.svg";
import Logout2 from "../../assets/svg/logout-2.svg";

// State for mobile menu toggle
const mobileMenuOpen = ref(false);

// State for scroll detection
const isScrolled = ref(false);

// Handle scroll event
const handleScroll = () => {
  isScrolled.value = window.scrollY > 10;
};

// Add scroll listener on mount
onMounted(() => {
  window.addEventListener("scroll", handleScroll);
});

// Remove scroll listener on unmount
onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
});

// --- auth-aware UI ---
import { useRouter } from "vue-router";
import { getUser, logout } from "../../utils/auth";

const router = useRouter();

// reactive user info (if any)
const user = ref(getUser());

// derive display name and avatar
function formatNameRaw(input) {
  if (!input) return "";
  const s = String(input).trim().replace(/\s+/g, " ");
  // split into words, title-case each word using locale-aware functions
  const parts = s.split(/\s+/).filter(Boolean);
  const formatted = parts
    .map((w) => {
      // keep acronyms (all uppercase) as-is, otherwise capitalize first char
      if (w === w.toUpperCase()) return w;
      const lower = w.toLocaleLowerCase("vi");
      return lower.charAt(0).toLocaleUpperCase("vi") + lower.slice(1);
    })
    .join(" ");
  return formatted;
}

const displayName = computed(() => {
  if (!user.value) return "";
  const full = user.value.full_name || user.value.name;
  if (full && String(full).trim()) return formatNameRaw(full);

  const email = user.value.email || "";
  if (!email.includes("@")) return formatNameRaw(email);

  const local = email.split("@")[0] || "";
  const nice = formatNameRaw(local.replace(/[._-]+/g, " "));
  return nice || email;
});
const userAvatar = computed(() => {
  if (!user.value) return "/src/assets/images/avatars/default.png";
  const src = user.value.anh_dai_dien || user.value.avatar || user.value.photo;
  return src || "/src/assets/images/avatars/default.png";
});

// split full name into "họ lót" (family + middle) and "tên" (given name)
const hoLot = computed(() => {
  const full = displayName.value || "";
  const parts = full.trim().split(/\s+/);
  if (parts.length <= 1) return "";
  return parts.slice(0, -1).join(" ");
});

const ten = computed(() => {
  const full = displayName.value || "";
  const parts = full.trim().split(/\s+/);
  return parts.length ? parts[parts.length - 1] : "";
});

// dropdown state
const dropdownOpen = ref(false);
const dropdownRef = ref(null);

// notifications popup state
const notificationsOpen = ref(false);
const notifRef = ref(null);
let hideNotificationsTimer = null;

const showNotifications = () => {
  notificationsOpen.value = true;
};

const hideNotifications = () => {
  notificationsOpen.value = false;
};

const startHideNotificationsTimer = () => {
  clearHideNotificationsTimer();
  hideNotificationsTimer = setTimeout(() => {
    notificationsOpen.value = false;
  }, 300);
};

const clearHideNotificationsTimer = () => {
  if (hideNotificationsTimer) {
    clearTimeout(hideNotificationsTimer);
    hideNotificationsTimer = null;
  }
};

// sample notifications (replace with real data from API later)
const notifications = [
  {
    title: "Tiêm phòng",
    message: "Đã đến lúc tiêm lại vaccine Care cho Bé Cún",
    icon: "svg-needle",
  },
  {
    title: "Lịch hẹn",
    message: "Lịch hẹn tiêm vaccine cho Bé Miu đã được xác nhận",
    icon: "svg-calendar",
  },
  {
    title: "Ưu đãi",
    message: "Khuyến mãi triệt sản 20% đến hết tháng này!",
    icon: "svg-paw",
  },
  {
    title: "Thanh toán & hóa đơn",
    message: "Hóa đơn #INV203 đã được thanh toán thành công",
    icon: "svg-receipt",
  },
  {
    title: "Lịch hẹn",
    message: "Bạn có lịch hẹn với Bác sĩ Minh vào 15:30 hôm nay",
    icon: "svg-calendar",
  },
];

// small mapping to inline SVG components for icons — simple inline icons
const SvgNeedle = {
  template: `<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M20 7L7 20M17 4l3 3-2 2-3-3 2-2z' /></svg>`,
};
// header/menu icons are imported above from assets/svg (UserRound, InfoCircle, DogIcon, CalendarDot, CreditCardPay, HelpIcon, Logout2)

const iconMap = {
  "svg-needle": SvgNeedle,
  "svg-calendar": CalendarDot,
  "svg-paw": DogIcon,
  "svg-receipt": CreditCardPay,
  user: UserRound,
  info: InfoCircle,
  help: HelpIcon,
  logout: Logout2,
};

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value;
};

const navigateTo = (path) => {
  dropdownOpen.value = false;
  notificationsOpen.value = false;
  try {
    router.push(path);
  } catch (e) {
    // fallback
    window.location.href = path;
  }
};

const handleLogout = () => {
  logout(router);
};

const handleMobileLogout = () => {
  handleLogout();
  mobileMenuOpen.value = false;
};

const onBellClick = () => {
  // placeholder: open notifications drawer or route
  // for now toggle dropdown as well
  dropdownOpen.value = !dropdownOpen.value;
};

// close dropdown when clicking outside
const onClickOutside = (e) => {
  // close user dropdown if clicking outside
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    dropdownOpen.value = false;
  }
  // close notifications popup if clicking outside
  if (notifRef.value && !notifRef.value.contains(e.target)) {
    notificationsOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener("click", onClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", onClickOutside);
});
</script>

<style scoped>
/* 
  Tất cả styling đã được chuyển sang Tailwind CSS!
  Không cần custom CSS nữa vì Tailwind đã cover hết.
*/
</style>
