<template>
  <div class="wrapper">
    <!-- Sidebar Wrapper: Admin Role -->
    <div class="sidebar-wrapper">
      <SideBar role="admin" />
    </div>
    
    <!-- Header -->
    <div class="topbar">
      <Header role="admin" />
    </div>
    
    <!-- Content -->
    <div class="page-wrapper">
      <div class="page-content">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup>
import SideBar from "../components/SideBar.vue";
import Header from "../components/Header.vue";
import "../../assets/js/bootstrap.bundle.min.js";
import "../../assets/js/jquery.min.js";
import "../../assets/plugins/simplebar/js/simplebar.min.js";
import "../../assets/plugins/metismenu/js/metisMenu.min.js";
import "../../assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js";
import "../../assets/js/index.js";
import "../../assets/js/app.js";
import "../../assets/js/pace.min.js";
</script>

<style scoped>
@import "../../assets/plugins/simplebar/css/simplebar.css";
@import "../../assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css";
@import "../../assets/plugins/metismenu/css/metisMenu.min.css";
@import "../../assets/css/pace.min.css";
@import "../../assets/css/bootstrap.min.css";
@import "../../assets/css/bootstrap-extended.css";
@import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap");
@import "../../assets/css/app.css";
@import "../../assets/css/icons.css";
@import "../../assets/css/dark-theme.css";
@import "../../assets/css/semi-dark.css";
@import "../../assets/css/header-colors.css";
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css");

/* Custom Layout Overrides */
.sidebar-wrapper {
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  z-index: 1000;
  background-color: #2f5755;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.topbar {
  position: fixed;
  top: 0;
  left: 256px; /* Push right by sidebar width */
  right: 0;
}

.page-wrapper {
  margin-left: 256px;
  margin-top: 80px; 
  min-height: calc(100vh - 80px);
  background-color: #EEE;
  padding: 0;
}

.page-content {
  padding: 0 32px;
}
</style>
