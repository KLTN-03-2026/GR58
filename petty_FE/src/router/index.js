import { createRouter, createWebHistory } from "vue-router"; // cài vue-router: npm install vue-router@next --save
import { getToken } from "../utils/auth";

const routes = [
  /********************** Trang Chủ ************************* */
  {
    path: "/",
    component: () => import("../components/trangchu/index.vue"),
    meta: { layout: "trangchu" },
  },
  {
    path: "/dich-vu",
    component: () => import("../components/trangchu/dichvu/index.vue"),
    meta: { layout: "main" },
  },
  {
    path: "/lien-he",
    component: () => import("../components/trangchu/Contact/index.vue"),
    meta: { layout: "main" },
  },
  {
    path: "/about",
    component: () => import("../components/trangchu/AboutUs/index.vue"),
    meta: { layout: "main" },
  },
  /********************** Khách Hàng ************************* */
  {
    path: "/khach-hang/dang-ky",
    component: () => import("../components/KhachHang/DangKi/index.vue"),
    meta: { layout: "dangki_dangnhap" },
  },
  {
    path: "/khach-hang/dang-nhap",
    component: () => import("../components/KhachHang/DangNhap/index.vue"),
    meta: { layout: "dangki_dangnhap" },
  },
  {
    path: "/auth/callback",
    component: () => import("../components/Auth/Callback.vue"),
    meta: { layout: "dangki_dangnhap" },
  },
  {
    path: "/khach-hang/trang-cua-toi",
    component: () => import("../components/KhachHang/QuanLyTaiKhoan/index.vue"),
    meta: { layout: "dangki_dangnhap", requiresAuth: true },
  },
  {
    path: "/khach-hang/quan-ly-thong-tin-ca-nhan",
    component: () =>
      import("../components/KhachHang/QuanLyThongTinCaNhan/index.vue"),
    meta: { layout: "dangki_dangnhap", requiresAuth: true },
  },
  {
    path: "/khach-hang/tro-giup-lien-he",
    component: () => import("../components/KhachHang/TroGiupLienHe/index.vue"),
    meta: { layout: "dangki_dangnhap", requiresAuth: true },
  },
  {
    path: "/khach-hang/thanh-toan",
    component: () => import("../components/KhachHang/ThanhToan/index.vue"),
    meta: { layout: "dangki_dangnhap", requiresAuth: true },
  },
  {
    path: "/khach-hang/lich-hen",
    component: () => import("../components/KhachHang/LichHen/index.vue"),
    meta: { layout: "dangki_dangnhap", requiresAuth: true },
  },
  {
    path: "/khach-hang/thu-cung-cua-toi",
    component: () => import("../components/KhachHang/ThuCungCuaToi/index.vue"),
    meta: { layout: "dangki_dangnhap", requiresAuth: true },
  },

  //********************** Admin ************************* */
  {
    path: "/admin/dang-nhap",
    component: () => import("../components/Admin/DangNhap/index.vue"),
    meta: { layout: "dangki_dangnhap" },
  },

  //********************** Nhân Viên ************************* */
  {
    path: "/nhan-vien/dang-nhap",
    component: () => import("../components/NhanVien/DangNhap/index.vue"),
    meta: { layout: "dangki_dangnhap" },
  },

  {
    path: "/admin",
    component: () => import("../layout/wrapper/AdminLayout.vue"),
    // Require auth for admin area so we redirect to admin login before components mount
    meta: { requiresAuth: true, adminOnly: true },
    children: [
      {
        path: "dashboard",
        component: () => import("../components/Admin/Dashboard/index.vue"),
      },
      {
        path: "quan-ly-dich-vu",
        component: () =>
          import("../components/Admin/TaiNguyen/QuanLyDichVu/index.vue"),
      },
      {
        path: "kho-thuoc-vat-tu",
        component: () =>
          import("../components/Admin/TaiNguyen/QuanLyKhoThuocVatTu/index.vue"),
      },
      {
        path: "tai-khoan",
        component: () =>
          import("../components/Admin/NhanSu/QuanLyTaiKhoan/index.vue"),
      },
      {
        path: "lich-lam-viec",
        component: () =>
          import("../components/Admin/NhanSu/QuanLyLichLamViec/index.vue"),
      },
      {
        path: "quan-ly-lich-hen",
        component: () =>
          import("../components/Admin/VanHanh/QuanLyLichHen/index.vue"),
      },
      {
        path: "ho-so-benh-an",
        component: () =>
          import("../components/Admin/VanHanh/HoSoBenhAn/index.vue"),
      },
      {
        path: "danh-sach-hoa-don",
        component: () =>
          import("../components/Admin/TaiChinhHoaDon/DanhSachHoaDon/index.vue"),
      },
      {
        path: "phieu-chi",
        component: () =>
          import("../components/Admin/TaiChinhHoaDon/PhieuChi/index.vue"),
      },
      {
        path: "bai-viet",
        component: () =>
          import("../components/Admin/TruyenThong/BaiViet/index.vue"),
      },
      {
        path: "bai-viet/them-moi",
        component: () =>
          import(
            "../components/Admin/TruyenThong/BaiViet/ThemBaiMoi/index.vue"
          ),
      },
      {
        path: "bai-viet/chinh-sua/:id",
        component: () =>
          import(
            "../components/Admin/TruyenThong/BaiViet/ChinhSuaBaiViet/index.vue"
          ),
      },
      {
        path: "khuyen-mai",
        component: () =>
          import("../components/Admin/TruyenThong/KhuyenMai/index.vue"),
      },
      {
        path: "khuyen-mai/them-moi",
        component: () =>
          import(
            "../components/Admin/TruyenThong/KhuyenMai/TaoKhuyenMai/index.vue"
          ),
      },
      {
        path: "khuyen-mai/chinh-sua/:id",
        component: () =>
          import(
            "../components/Admin/TruyenThong/KhuyenMai/ChinhSuaKhuyenMai/index.vue"
          ),
      },
      {
        path: "bao-cao-doanh-thu",
        component: () =>
          import("../components/Admin/BaoCao/DoanhThu/index.vue"),
      },
      {
        path: "bao-cao-hieu-suat",
        component: () =>
          import("../components/Admin/BaoCao/HieuSuat/index.vue"),
      },
      {
        path: "bao-cao-thuoc-vat-tu",
        component: () =>
          import("../components/Admin/BaoCao/KhoThuoc&VatTu/index.vue"),
      },
      {
        path: "cau-hinh",
        component: () => import("../components/Admin/CauHinh/index.vue"),
      },
      {
        path: "phan-quyen",
        component: () =>
          import("../components/Admin/CauHinh/PhanQuyen/index.vue"),
      },
      {
        path: "trang-ca-nhan",
        component: () => import("../components/Admin/TrangCaNhan/index.vue"),
      },
    ],
  },

  //********************** Bác Sĩ ************************* */
  // {
  //   path: "/doctor/dang-nhap",
  //   component: () => import("../components/Admin/DangNhap/index.vue"),
  //   meta: { layout: "dangki_dangnhap" },
  // },
  {
    path: "/doctor",
    component: () => import("../layout/wrapper/DoctorLayout.vue"),
    meta: { requiresAuth: true },
    children: [
      {
        path: "dashboard",
        component: () => import("../components/Doctor/Dashboard/index.vue"),
      },
      {
        path: "lich-kham",
        component: () => import("../components/Doctor/LichKham/index.vue"),
      },
      {
        path: "lich-kham/phieu-kham/:id",
        component: () =>
          import("../components/Doctor/LichKham/PhieuKhamBenh/index.vue"),
        props: true,
      },
      {
        path: "benh-an",
        component: () => import("../components/Doctor/HoSoBenhAn/index.vue"),
      },
      {
        path: "benh-an/chi-tiet",
        component: () =>
          import("../components/Doctor/HoSoBenhAn/ChiTietHoSoBA/index.vue"),
      },
      {
        path: "can-lam-sang",
        component: () => import("../components/Doctor/CanLamSang/index.vue"),
      },
      {
        path: "kho-thuoc",
        component: () => import("../components/Doctor/KhoThuoc/index.vue"),
      },
      {
        path: "trang-ca-nhan",
        component: () => import("../components/Doctor/TrangCaNhan/index.vue"),
      },
      {
        path: "lich-lam-viec",
        component: () => import("../components/Doctor/LichLamViec/index.vue"),
      },
    ],
  },
  //********************** Bác Sĩ ************************* */s

  //********************** Y Tá ************************* */
  {
    path: "/nurse",
    component: () => import("../layout/wrapper/NurseLayout.vue"),
    meta: { requiresAuth: true },
    children: [
      {
        path: "dashboard",
        component: () => import("../components/Nurse/Dashboard/index.vue"),
      },
      {
        path: "lich-hen",
        component: () => import("../components/Nurse/LichHen/index.vue"),
      },
      {
        path: "khach-hang",
        component: () => import("../components/Nurse/KhachHang/index.vue"),
      },
      {
        path: "hoa-don",
        component: () => import("../components/Nurse/HoaDon/index.vue"),
      },
      {
        path: "lich-lam-viec",
        component: () => import("../components/Nurse/LichLamViec/index.vue"),
      },
      {
        path: "kho-thuoc-vat-tu",
        component: () => import("../components/Nurse/KhoThuocVatTu/index.vue"),
      },
      {
        path: "trang-ca-nhan",
        component: () => import("../components/Nurse/TrangCaNhan/index.vue"),
      },
      {
        path: "phieu-chi",
        component: () => import("../components/Nurse/PhieuChi/index.vue"),
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes: routes,
});

// Global navigation guard: redirect to login when a route requires auth
// router.beforeEach((to, from, next) => {
//   const token = getToken();
//   const requiresAuth = to.matched.some(
//     (record) => record.meta && record.meta.requiresAuth
//   );

//   if (requiresAuth && !token) {
//     // If any matched record is admin-only, redirect to admin login first
//     const isAdminRoute = to.matched.some(
//       (record) => record.meta && record.meta.adminOnly
//     );

//     if (isAdminRoute) {
//       next({ path: "/admin/dang-nhap", query: { redirect: to.fullPath } });
//       return;
//     }

//     // If route is for employees (doctor, nurse), redirect to employee login
//     if (to.path.startsWith("/doctor") || to.path.startsWith("/nurse")) {
//       next({ path: "/nhan-vien/dang-nhap", query: { redirect: to.fullPath } });
//       return;
//     }

//     // Default: redirect to customer login
//     next({ path: "/khach-hang/dang-nhap", query: { redirect: to.fullPath } });
//     return;
//   }
//   next();
// });

export default router;
