<template>
  <div
    class="bg-white border-b border-[#e6e6e6] shadow-sm flex flex-col justify-center px-6 py-10 w-full h-full"
    data-name="Header"
  >
    <div class="flex flex-col gap-2.5 items-start w-full">
      <div class="flex items-center justify-between w-full">
        <!-- Breadcrumbs / Page Title -->
        <div class="h-5">
          <div class="flex gap-2 items-center h-5">
            <div class="flex gap-2 items-center h-5">
              <p
                class="font-nunitoSans font-bold text-[#0d9488] text-base leading-6"
              >
                {{ pageTitle }}
              </p>
            </div>
          </div>
        </div>

        <!-- Right Section: Search, Notification, User Profile -->
        <div class="flex gap-4 items-center w-auto">
          <!-- Search Input -->
          <div class="relative h-9 w-96">
            <div
              class="absolute bg-[#f3f4f6] border !border-gray-300 h-9 left-0 rounded-lg top-0 w-96"
            >
              <div
                class="flex h-9 items-center overflow-hidden pl-10 pr-3 py-1 rounded-lg w-96"
              >
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Tìm kiếm..."
                  class="font-nunitoSans font-medium text-[#6b7280] text-sm bg-transparent border-none outline-none w-full"
                  @keyup.enter="handleSearch"
                />
              </div>
            </div>
            <div class="absolute left-3 top-2.5 w-4 h-4">
              <searchIcon />
            </div>
          </div>

          <!-- Notification Button -->
          <button
            class="relative rounded-lg w-9 h-9 hover:bg-gray-100 flex items-center justify-center"
            @click="handleNotificationClick"
            aria-label="Thông báo"
          >
            <notificationIcon class="w-4 h-4" />
            <!-- Notification Badge -->
            <div
              v-if="hasUnreadNotifications"
              class="absolute bg-[#ef4444] left-6 top-1 rounded-full w-2 h-2"
            />
          </button>

          <!-- User Profile Button -->
          <button
            class="flex gap-2 items-center justify-center rounded-lg hover:bg-gray-50 p-1 transition-colors"
            @click="handleProfileClick"
          >
            <!-- Avatar: show image if available, otherwise initials -->
            <div>
              <template v-if="userAvatar">
                <img
                  :src="userAvatar"
                  alt="Avatar"
                  class="rounded-full w-8 h-8 object-cover"
                />
              </template>
              <template v-else>
                <div
                  class="bg-[#0d9488] rounded-full w-8 h-8 flex items-center justify-center"
                >
                  <p
                    class="font-nunitoSans font-medium text-white text-sm text-center"
                  >
                    {{ userInitials }}
                  </p>
                </div>
              </template>
            </div>

            <!-- User Info -->
            <div class="flex flex-col items-start">
              <p
                class="font-nunitoSans font-medium text-black text-sm leading-6"
              >
                {{ userName }}
              </p>
              <p
                class="font-nunitoSans font-medium text-[#6b7280] text-sm leading-6"
              >
                {{ userEmail }}
              </p>
            </div>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import { showSuccessToast, showInfoToast } from "@/utils/toast";
import { getUser, logout } from "@/utils/auth";
// Icon SVG
import searchIcon from "@/assets/svg/search.svg";
import notificationIcon from "@/assets/svg/notification.svg";

const router = useRouter();
const route = useRoute();

// Props
const props = defineProps({
  title: {
    type: String,
    default: "Dashboard",
  },
  // Optional user prop; if not provided we'll prefer the stored auth_user
  user: {
    type: Object,
    default: () => null,
  },
});

// Prefer stored auth_user (set by login) if present
// getUser() không có role → resolveRole() tự detect từ URL (doctor/nurse/admin...)
const storedUser = getUser();

// Normalized user data used by the template
const userData = computed(() => {
  // NhanVien dùng full_name, KhachHang dùng full_name, Admin dùng ho_ten hoặc name
  if (storedUser && Object.keys(storedUser).length > 0) {
    return {
      ho_ten: storedUser.full_name || storedUser.ho_ten || storedUser.name || null,
      email: storedUser.email || null,
      anh_dai_dien: storedUser.anh_dai_dien || storedUser.avatar || null,
    };
  }
  // fall back to prop
  return {
    ho_ten: props.user?.name || null,
    email: props.user?.email || null,
    anh_dai_dien: props.user?.avatar || null,
  };
});
// State
const searchQuery = ref("");
const showUserMenu = ref(false);
const hasUnreadNotifications = ref(true); // Example: set to true to show notification badge

// Computed
const pageTitle = computed(() => props.title);
const userName = computed(() => userData.value.ho_ten || "Người dùng");
const userEmail = computed(() => userData.value.email || "");

const userInitials = computed(() => {
  // If avatar exists, we won't show initials (template will prefer avatar)
  const name = userData.value.ho_ten || "";
  if (!name) return "AD";
  const parts = name.trim().split(/\s+/);
  if (parts.length >= 2) {
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
  }
  return name.substring(0, 2).toUpperCase();
});

const userAvatar = computed(() => {
  const a = userData.value.anh_dai_dien;
  if (!a) return null;
  // If avatar is stored as relative path on server, you may need to prefix with base URL.
  return a;
});

// Methods
const handleSearch = () => {
  console.log("Search query:", searchQuery.value);
  // Implement search logic here
  // Example: router.push({ name: 'search', query: { q: searchQuery.value } });
};

const handleProfileClick = () => {
  if (route.path.startsWith("/doctor")) {
    router.push("/doctor/profile");
  } else if (route.path.startsWith("/nurse")) {
    router.push("/nurse/profile");
  } else {
    router.push("/admin/profile");
  }
};

const handleNotificationClick = () => {
  console.log("Notification clicked");
  // Implement notification panel toggle or navigation
  // Example: router.push({ name: 'notifications' });
};

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value;
};

const handleProfile = () => {
  console.log("Navigate to profile");
  showUserMenu.value = false;
  // router.push({ name: 'profile' });
};

const handleSettings = () => {
  console.log("Navigate to settings");
  showUserMenu.value = false;
  // router.push({ name: 'settings' });
};

const handleLogout = async () => {
  showUserMenu.value = false;

  try {
    // Get current user role to determine correct logout endpoint
    const userRole =
      localStorage.getItem("user_role") || sessionStorage.getItem("user_role");

    // Determine logout endpoint based on role
    if (
      userRole === "bac_si" ||
      userRole === "y_ta" ||
      userRole === "le_tan" ||
      userRole === "tro_ly"
    ) {
      // Employee logout
      await axios.post("http://127.0.0.1:8000/api/nhan-vien/dang-xuat");
    } else {
      // Admin logout (default)
      await axios.post("http://127.0.0.1:8000/api/admin/dang-xuat");
    }
  } catch (err) {
    console.error("Logout error:", err);
  }

  logout(router);
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  const dropdown = event.target.closest(".absolute.right-6");
  const button = event.target.closest("button");

  if (!dropdown && !button?.querySelector(".bg-\\[\\#0d9488\\]")) {
    showUserMenu.value = false;
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>

<style scoped>
/* Remove input default styles */
input::placeholder {
  color: #6b7280;
}

input:focus {
  outline: none;
}
</style>
