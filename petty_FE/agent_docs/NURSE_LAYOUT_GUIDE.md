# 🩺 Nurse Layout - Hướng dẫn sử dụng

## 📁 Files đã tạo

### 1. NurseLayout.vue
**Đường dẫn:** `src/layout/wrapper/NurseLayout.vue`

Layout wrapper cho role Nurse (Điều dưỡng viên), giống hệt cấu trúc của AdminLayout và DoctorLayout.

### 2. Menu Configuration
**File:** `src/config/menuConfig.js`

Đã thêm cấu hình menu cho Nurse với các items:

```javascript
nurse: {
  title: 'Petty Nurse',
  subtitle: 'Điều dưỡng viên',
  menuItems: [
    {
      key: 'dashboard',
      label: 'Dashboard',
      path: '/nurse/dashboard',
      badge: 3  // Badge đỏ hiển thị số 3
    },
    {
      key: 'lichHen',
      label: 'Lịch hẹn',
      path: '/nurse/lich-hen'
    },
    {
      key: 'khachHang',
      label: 'Khách hàng',
      path: '/nurse/khach-hang'
    },
    {
      key: 'taiChinhHoaDon',
      label: 'Tài chính & Hóa đơn',
      path: '/nurse/tai-chinh-hoa-don',
      badge: 2  // Badge đỏ hiển thị số 2
    },
    {
      key: 'lichLamViec',
      label: 'Lịch làm việc',
      path: '/nurse/lich-lam-viec'
    },
    {
      key: 'khoThuocVatTu',
      label: 'Kho Thuốc & Vật Tư',
      path: '/nurse/kho-thuoc-vat-tu'
    },
    {
      key: 'phieuChi',
      label: 'Phiếu Chi',
      path: '/nurse/phieu-chi'
    }
  ]
}
```

## 🎨 Màu sắc & Styling

- **Màu nền Sidebar:** `#2f5755` (giống Admin & Doctor)
- **Màu active:** `#5a9690` với border trắng bên phải
- **Màu hover:** `#3a6664`
- **Badge:** Background `#fb2c36` (đỏ) với text trắng

## 🚀 Cách sử dụng trong Router

Thêm vào `src/router/index.js`:

```javascript
import NurseLayout from '@/layout/wrapper/NurseLayout.vue'

const routes = [
  // ... các routes khác
  {
    path: '/nurse',
    component: NurseLayout,
    meta: { requiresAuth: true, role: 'nurse' },
    children: [
      {
        path: 'dashboard',
        name: 'NurseDashboard',
        component: () => import('@/components/Nurse/Dashboard/index.vue')
      },
      {
        path: 'lich-hen',
        name: 'NurseLichHen',
        component: () => import('@/components/Nurse/LichHen/index.vue')
      },
      {
        path: 'khach-hang',
        name: 'NurseKhachHang',
        component: () => import('@/components/Nurse/KhachHang/index.vue')
      },
      {
        path: 'tai-chinh-hoa-don',
        name: 'NurseTaiChinhHoaDon',
        component: () => import('@/components/Nurse/TaiChinhHoaDon/index.vue')
      },
      {
        path: 'lich-lam-viec',
        name: 'NurseLichLamViec',
        component: () => import('@/components/Nurse/LichLamViec/index.vue')
      },
      {
        path: 'kho-thuoc-vat-tu',
        name: 'NurseKhoThuocVatTu',
        component: () => import('@/components/Nurse/KhoThuocVatTu/index.vue')
      },
      {
        path: 'phieu-chi',
        name: 'NursePhieuChi',
        component: () => import('@/components/Nurse/PhieuChi/index.vue')
      }
    ]
  }
]
```

## 🔧 Cập nhật SideBar.vue

Đã thêm hỗ trợ **Badge** cho menu items. Nếu menu item có thuộc tính `badge`, sẽ hiển thị badge đỏ với số.

## 📂 Cấu trúc thư mục cần tạo

Bạn cần tạo các components trong folder:
```
src/components/Nurse/
├── Dashboard/
│   └── index.vue
├── LichHen/
│   └── index.vue
├── KhachHang/
│   └── index.vue
├── TaiChinhHoaDon/
│   └── index.vue
├── LichLamViec/
│   └── index.vue
├── KhoThuocVatTu/
│   └── index.vue
└── PhieuChi/
    └── index.vue
```

## ✅ Checklist

- [x] NurseLayout.vue đã tạo
- [x] Menu config cho Nurse đã thêm
- [x] Badge support đã thêm vào SideBar
- [x] Icons từ Figma đã được sử dụng
- [ ] Tạo các component pages cho Nurse
- [ ] Thêm routes vào router/index.js
- [ ] Kiểm tra authentication & role-based access

## 🎯 Điểm khác biệt so với Admin & Doctor

1. **Title & Subtitle:**
   - Admin: "Petty Admin" / "Quản trị hệ thống"
   - Doctor: "Petty Doctor" / "Bác sĩ thú y"
   - **Nurse: "Petty Nurse" / "Điều dưỡng viên"**

2. **Menu Items:**
   - Nurse có 7 menu items đơn giản (không có submenu)
   - Dashboard và "Tài chính & Hóa đơn" có badge thông báo
   - Tập trung vào: Lịch hẹn, Khách hàng, Tài chính, Kho thuốc, Phiếu chi

3. **Màu sắc:** Giữ nguyên theme xanh lá như Admin & Doctor

---

**Created:** December 5, 2025
**Author:** AI Assistant
**Framework:** Vue 3 + Tailwind CSS
