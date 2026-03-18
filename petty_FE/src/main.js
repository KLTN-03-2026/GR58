import { createApp } from "vue";
import App from "./App.vue";
import VueApexCharts from "vue3-apexcharts";
import router from "./router";
import Default from "./layout/wrapper/index.vue";
import trangchu from "./layout/wrapper/layout_rong.vue";
import dangki_dangnhap from "./layout/wrapper/layout_dangki_dangnhap.vue";
import MainLayout from "./layout/wrapper/MainLayout.vue";
// import sidebar from "./layout/wrapper/layout_sidebar.vue"; // ❌ Deprecated - Use AdminLayout/DoctorLayout instead

import "./assets/css/main.css";

// Toast plugin
// npm install --save vue-toastification
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import "./assets/css/toast-override.css";
import axios from "axios";
import { attachToken } from "@/utils/api";
import {
  logout as authLogout,
  getToken as authGetToken,
  getUser as authGetUser,
} from "./utils/auth";

const app = createApp(App);

// install router and toast plugin
app.use(router);
app.use(VueApexCharts); // Đăng ký global component
app.use(Toast, {
  // optional default options
  position: "top-right",
  timeout: 4000,
  closeOnClick: true,
  pauseOnHover: true,
});

const existingToken =
  localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
if (existingToken) {
  axios.defaults.headers.common["Authorization"] = `Bearer ${existingToken}`;
}

// ensure shared api client also has the token
attachToken();

// expose simple auth helper on globalProperties for components: this.$auth.logout(router)
app.config.globalProperties.$auth = {
  logout: (router) => authLogout(router),
  getToken: authGetToken,
  getUser: authGetUser,
};

app.component("default-layout", Default);
app.component("trangchu-layout", trangchu);
app.component("dangki_dangnhap-layout", dangki_dangnhap);
app.component("main-layout", MainLayout);
// app.component("sidebar-layout", sidebar); // ❌ Deprecated - Use AdminLayout/DoctorLayout instead

app.mount("#app");
