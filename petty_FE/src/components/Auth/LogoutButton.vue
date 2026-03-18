<template>
  <button
    @click="handleLogout"
    class="px-3 py-1 rounded-md bg-red-600 text-white hover:bg-red-700 transition"
    title="Đăng xuất"
  >
    Đăng xuất
  </button>
</template>

<script setup>
import { useRouter } from "vue-router";
import { logout } from "../../utils/auth";

const router = useRouter();

const handleLogout = () => {
  logout(router);
};
</script>

<style scoped>
/* minimal styles kept inline in template classes */
</style>
