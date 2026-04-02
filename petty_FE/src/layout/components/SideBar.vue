<template>
  <div
    class="bg-[#2f5755] flex flex-col items-start h-full w-64"
    data-name="Sidebar"
  >
    <!-- Header Section with Logo -->
    <div class="border-b border-[#0f766e] h-[80px] w-64" data-name="Container">
      <div class="flex flex-col h-[80px] justify-center items-start px-6">
        <div class="flex gap-4 h-11 items-center w-full">
          <!-- Logo -->
          <div
            class="bg-white rounded-[10px] w-10 h-10 flex items-center justify-center"
          >
            <PettyIcon />
          </div>

          <!-- Title Container -->
          <div class="h-11 flex flex-col items-start">
            <h1
              class="font-nunitoSans font-medium text-white text-xl leading-6"
            >
              {{ menuData.title }}
            </h1>
            <p
              class="font-nunitoSans font-medium text-[#5eead4] text-xs leading-6"
            >
              {{ menuData.subtitle }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation Section -->
    <div class="flex-1 w-64 min-h-0 pt-4 overflow-y-auto" data-simplebar>
      <template v-for="item in menuData.menuItems">
        <!-- Single Menu Item (no children) -->
        <router-link
          v-if="item.type === 'single'"
          :key="`single-${item.key}`"
          :to="item.path"
          class="flex h-12 items-center justify-between pl-2 pr-6 cursor-pointer transition-colors"
          :class="
            activeItem === item.key
              ? 'bg-[#5a9690] border-r-4 border-white'
              : 'hover:bg-[#3a6664]'
          "
          @click="navigateTo(item.key)"
        >
          <div class="flex gap-3 items-center">
            <div
              class="w-5 h-5 bg-white"
              :style="{
                maskImage: `url(${item.icon})`,
                webkitMaskImage: `url(${item.icon})`,
                maskRepeat: 'no-repeat',
                webkitMaskRepeat: 'no-repeat',
                maskPosition: 'center',
                webkitMaskPosition: 'center',
                maskSize: 'contain',
                webkitMaskSize: 'contain',
              }"
            ></div>
            <span class="font-nunitoSans font-semibold text-white text-base">
              {{ item.label }}
            </span>
          </div>
          <!-- Badge -->
          <div
            v-if="item.badge"
            class="bg-[#fb2c36] rounded-lg px-2 py-0.5 min-w-[22px] h-[22px] flex items-center justify-center"
          >
            <span class="font-nunitoSans font-medium text-white text-xs">
              {{ item.badge }}
            </span>
          </div>
        </router-link>

        <!-- Group Menu Item (with children) -->
        <template v-else-if="item.type === 'group'">
          <div
            :key="`group-header-${item.key}`"
            class="flex h-12 items-center justify-between pl-2 pr-6 cursor-pointer hover:bg-[#3a6664] transition-colors"
            :class="{ 'bg-[#3a6664]': openSubmenus[item.key] }"
            @click="toggleSubmenu(item.key)"
          >
            <div class="flex gap-3 items-center">
              <div
                class="w-5 h-5 bg-white"
                :style="{
                  maskImage: `url(${item.icon})`,
                  webkitMaskImage: `url(${item.icon})`,
                  maskRepeat: 'no-repeat',
                  webkitMaskRepeat: 'no-repeat',
                  maskPosition: 'center',
                  webkitMaskPosition: 'center',
                  maskSize: 'contain',
                  webkitMaskSize: 'contain',
                }"
              ></div>
              <span class="font-nunitoSans font-medium text-white text-base">
                {{ item.label }}
              </span>
            </div>
            <button
              class="w-4 h-4 transition-transform duration-200"
              :class="{ 'rotate-180': openSubmenus[item.key] }"
            >
              <ChevronDownIcon class="w-full h-full text-white" />
            </button>
          </div>

          <!-- Submenu Items -->
          <div
            v-show="openSubmenus[item.key]"
            :key="`group-submenu-${item.key}`"
            class="flex flex-col bg-[#264a48] w-full transition-all duration-300 ease-in-out"
          >
            <router-link
              v-for="child in item.children"
              :key="child.key"
              :to="child.path"
              class="h-10 flex items-center pl-14 pr-6 cursor-pointer transition-colors"
              :class="
                activeItem === child.key
                  ? 'bg-[#5a9690] border-r-4 border-white'
                  : 'hover:text-[#5eead4] text-white'
              "
              @click="navigateTo(child.key)"
            >
              <span class="font-nunitoSans font-medium text-sm">{{
                child.label
              }}</span>
            </router-link>
          </div>
        </template>
      </template>
    </div>

    <!-- Footer Section -->
    <div class="border-t border-[#0f766e] h-[100px] w-64">
      <div class="flex flex-col gap-2 h-[93px] items-start px-2 pt-[17px]">
        <!-- Logout Button -->
        <button
          class="h-9 rounded-lg w-full flex items-center gap-3 px-8 py-2 hover:bg-[#3a6664]"
          @click="handleLogout"
        >
          <span class="font-medium text-white text-base"> Đăng xuất </span>
        </button>

        <!-- Version -->
        <div class="w-full px-8">
          <p class="font-medium text-[#5eead4] text-base">Phiên bản v1.0</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { getMenuByRole, sharedIcons } from "@/config/menuConfig";
import { logout } from "@/utils/auth";
// Icon SVG
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import PettyIcon from "@/assets/img_imports/healthicons_guide-dog.svg";
const router = useRouter();
const route = useRoute();

// Props
const props = defineProps({
  role: {
    type: String,
    default: "admin",
    validator: (value) => ["admin", "doctor"].includes(value),
  },
});

// Get menu configuration based on role
const menuData = computed(() => getMenuByRole(props.role));

// State for active item
const activeItem = ref("dashboard");

// State for submenu toggles - dynamically initialize based on menu items
const openSubmenus = ref({});

// Initialize submenus state
onMounted(() => {
  menuData.value.menuItems.forEach((item) => {
    if (item.type === "group") {
      openSubmenus.value[item.key] = false;
    }
  });

  // Set active item based on current route
  updateActiveItemFromRoute();
});

// Update active item based on current route
const updateActiveItemFromRoute = () => {
  const currentPath = route.path;

  // Check single items
  for (const item of menuData.value.menuItems) {
    if (item.type === "single" && item.path === currentPath) {
      activeItem.value = item.key;
      return;
    }

    // Check submenu items
    if (item.type === "group" && item.children) {
      for (const child of item.children) {
        if (child.path === currentPath) {
          activeItem.value = child.key;
          openSubmenus.value[item.key] = true; // Auto-open parent menu
          return;
        }
      }
    }
  }
};

// Toggle submenu
const toggleSubmenu = (menuKey) => {
  // If opening a closed menu, close all others first
  if (!openSubmenus.value[menuKey]) {
    Object.keys(openSubmenus.value).forEach((key) => {
      openSubmenus.value[key] = false;
    });
    openSubmenus.value[menuKey] = true;
  } else {
    // If closing an open menu, just close it
    openSubmenus.value[menuKey] = false;
  }
};

// Navigation handler
const navigateTo = (itemKey) => {
  activeItem.value = itemKey;
};

// Logout handler
const handleLogout = () => {
  console.log("Logging out...");
  logout(router);
};
</script>

<style scoped>
/* Additional custom styles if needed */
</style>