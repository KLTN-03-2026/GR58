import { createRouter, createWebHistory } from "vue-router";
import { getToken } from "../utils/auth";

const routes = [
  /********************** Home ************************* */
  {
    path: "/",
    component: () => import("../components/trangchu/index.vue"),
    meta: { layout: "trangchu" },
  },
  {
    path: "/services",
    component: () => import("../components/trangchu/dichvu/index.vue"),
    meta: { layout: "main" },
  },
  {
    path: "/contact",
    component: () => import("../components/trangchu/Contact/index.vue"),
    meta: { layout: "main" },
  },
  {
    path: "/about",
    component: () => import("../components/trangchu/AboutUs/index.vue"),
    meta: { layout: "main" },
  },

  /********************** Customer ************************* */
  {
    path: "/customer/register",
    component: () => import("../views/customer/register/index.vue"),
    meta: { layout: "dangki_dangnhap" },
  },
  {
    path: "/customer/login",
    component: () => import("../views/customer/login/index.vue"),
    meta: { layout: "dangki_dangnhap" },
  },
  {
    path: "/auth/callback",
    component: () => import("../components/Auth/Callback.vue"),
    meta: { layout: "dangki_dangnhap" },
  },
  {
    path: "/verify-email",
    component: () => import("../views/customer/verify-email/index.vue"),
    meta: { layout: "default" },
  },
  {
    path: "/auth/verified",
    component: () => import("../views/auth/verified/index.vue"),
    meta: { layout: "default" },
  },
  {
    path: "/customer/my-account",
    component: () => import("../views/customer/account-management/index.vue"),
    meta: { layout: "dangki_dangnhap", requiresAuth: true },
  },
  {
    path: "/customer/personal-info",
    component: () => import("../views/customer/personal-info/index.vue"),
    meta: { layout: "dangki_dangnhap", requiresAuth: true },
  },
  {
    path: "/customer/help-contact",
    component: () => import("../views/customer/help-contact/index.vue"),
    meta: { layout: "dangki_dangnhap", requiresAuth: true },
  },
  {
    path: "/customer/payment",
    component: () => import("../views/customer/payment/index.vue"),
    meta: { layout: "dangki_dangnhap", requiresAuth: true },
  },
  {
    path: "/payment/success",
    component: () => import("../views/customer/payment/success/index.vue"),
    meta: { layout: "dangki_dangnhap" },
  },
  {
    path: "/customer/appointments/book",
    component: () => import("../views/customer/appointment/book-appointment/index.vue"),
    meta: { layout: "dangki_dangnhap", requiresAuth: true },
  },
  {
    path: "/customer/appointments",
    component: () => import("../views/customer/appointment/index.vue"),
    meta: { layout: "dangki_dangnhap", requiresAuth: true },
  },
  {
    path: "/customer/my-pets",
    component: () => import("../views/customer/my-pets/index.vue"),
    meta: { layout: "dangki_dangnhap", requiresAuth: true },
  },

  /********************** Admin ************************* */
  {
    path: "/admin/login",
    component: () => import("../views/admin/login/index.vue"),
    meta: { layout: "dangki_dangnhap" },
  },

  /********************** Staff ************************* */
  {
    path: "/staff/login",
    component: () => import("../views/staff/login/index.vue"),
    meta: { layout: "dangki_dangnhap" },
  },

  {
    path: "/admin",
    component: () => import("../layout/wrapper/AdminLayout.vue"),
    meta: { requiresAuth: true, adminOnly: true },
    children: [
      {
        path: "dashboard",
        component: () => import("../views/admin/dashboard/index.vue"),
      },
      {
        path: "service-management",
        component: () =>
          import("../views/admin/resource/service-management/index.vue"),
      },
      {
        path: "account-management",
        component: () =>
          import("../views/admin/personnel/account-management/index.vue"),
      },
      {
        path: "schedule-management",
        component: () =>
          import("../views/admin/personnel/schedule-management/index.vue"),
      },
      {
        path: "invoice-list",
        component: () =>
          import("../views/admin/finance-invoice/invoice-list/index.vue"),
      },
      {
        path: "posts",
        component: () =>
          import("../views/admin/communication/post/index.vue"),
      },
      {
        path: "posts/create",
        component: () =>
          import("../views/admin/communication/post/add-new-post/index.vue"),
      },
      {
        path: "posts/edit/:id",
        component: () =>
          import("../views/admin/communication/post/edit-post/index.vue"),
      },
      {
        path: "khuyen-mai",
        component: () =>
          import("../views/admin/communication/promotion/index.vue"),
      },
      {
        path: "khuyen-mai/them-moi",
        component: () =>
          import("../views/admin/communication/promotion/create-promotion/index.vue"),
      },
      {
        path: "khuyen-mai/chinh-sua/:id",
        component: () =>
          import("../views/admin/communication/promotion/edit-promotion/index.vue"),
      },
      {
        path: "report/revenue",
        component: () =>
          import("../views/admin/report/revenue/index.vue"),
      },
      {
        path: "report/performance",
        component: () =>
          import("../views/admin/report/performance/index.vue"),
      },
      {
        path: "report/inventory",
        component: () =>
          import("../views/admin/report/inventory/index.vue"),
      },
      {
        path: "configuration",
        component: () => import("../views/admin/configuration/index.vue"),
      },
      {
        path: "permissions",
        component: () =>
          import("../views/admin/configuration/permissions/index.vue"),
      },
      {
        path: "profile",
        component: () => import("../views/admin/profile/index.vue"),
      },
    ],
  },

  /********************** Doctor ************************* */
  {
    path: "/doctor",
    component: () => import("../layout/wrapper/DoctorLayout.vue"),
    meta: { requiresAuth: true },
    children: [
      {
        path: "dashboard",
        component: () => import("../views/doctor/dashboard/index.vue"),
      },
      {
        path: "appointments",
        component: () => import("../views/doctor/appointment/index.vue"),
      },
      {
        path: "appointments/examination/:id",
        component: () =>
          import("../views/doctor/appointment/examination-form/index.vue"),
        props: true,
      },
      {
        path: "medical-records",
        component: () => import("../views/doctor/medical-record/index.vue"),
      },
      {
        path: "medical-records/detail",
        component: () =>
          import("../views/doctor/medical-record/medical-record-detail/index.vue"),
      },
      {
        path: "clinical-testing",
        component: () => import("../views/doctor/clinical-testing/index.vue"),
      },
      {
        path: "pharmacy",
        component: () => import("../views/doctor/pharmacy/index.vue"),
      },
      {
        path: "profile",
        component: () => import("../views/doctor/profile/index.vue"),
      },
      {
        path: "schedule",
        component: () => import("../views/doctor/schedule/index.vue"),
      },
      {
        path: "lich-kham/phieu-kham/:id",
        component: () => import("../views/doctor/appointment/examination-form/index.vue"),
        props: true,
},
    ],
  },

  /********************** Nurse ************************* */
  {
    path: "/nurse",
    component: () => import("../layout/wrapper/NurseLayout.vue"),
    meta: { requiresAuth: true },
    children: [
      {
        path: "dashboard",
        component: () => import("../views/nurse/dashboard/index.vue"),
      },
      {
        path: "appointments",
        component: () => import("../views/nurse/appointment/index.vue"),
      },
      {
        path: "customers",
        component: () => import("../views/nurse/customer/index.vue"),
      },
      {
        path: "invoices",
        component: () => import("../views/nurse/invoice/index.vue"),
      },
      {
        path: "schedule",
        component: () => import("../views/nurse/schedule/index.vue"),
      },
      {
        path: "inventory",
        component: () => import("../views/nurse/inventory/index.vue"),
      },
      {
        path: "profile",
        component: () => import("../views/nurse/profile/index.vue"),
      },
      {
        path: "trang-ca-nhan",
        component: () => import("../views/nurse/profile/index.vue"),
      },
      {
        path: "expense-vouchers",
        component: () => import("../views/nurse/expense-voucher/index.vue"),
      },
      {
        path: "phieu-chi",
        component: () => import("../views/nurse/expense-voucher/index.vue"),
      },
      {
        path: "lich-hen",
        component: () => import("../views/nurse/appointment/index.vue"),
      },
      {
        path: "hoa-don",
        component: () => import("../views/nurse/invoice/index.vue"),
      },
      {
        path: "lich-lam-viec",
        component: () => import("../views/nurse/schedule/index.vue"),
      },
      {
        path: "kho-thuoc",
        component: () => import("../views/nurse/inventory/index.vue"),
      },
    
    ],
  },
  {
  path: "/admin/dashboard",
  component: () => import("@/views/admin/dashboard/index.vue"),
  meta: { requiresAdminAuth: true }
}
];

const router = createRouter({
  history: createWebHistory(),
  routes: routes,
});

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth) {
    let role = "customer";
    if (to.path.startsWith("/admin")) role = "admin";
    else if (to.path.startsWith("/doctor") || to.path.startsWith("/nurse") || to.path.startsWith("/staff") || to.path.startsWith("/receptionist") || to.path.startsWith("/assistant")) role = "staff";

    const token = getToken(role);
    if (!token) {
      if (role === "admin") return next({ path: "/admin/login", query: { redirect: to.fullPath } });
      if (role === "staff") return next({ path: "/staff/login", query: { redirect: to.fullPath } });
      return next({ path: "/customer/login", query: { redirect: to.fullPath } });
    }
  }
  next();
});


export default router;