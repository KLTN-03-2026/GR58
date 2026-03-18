# Hướng dẫn sử dụng Multi-Role Sidebar System

## 📂 Cấu trúc File đã tạo

```
src/
├── config/
│   └── menuConfig.js           # Cấu hình menu cho từng role
├── layout/
│   ├── components/
│   │   └── SideBar.vue         # Component Sidebar tái sử dụng
│   └── wrapper/
│       ├── AdminLayout.vue     # Layout wrapper cho Admin
│       └── DoctorLayout.vue    # Layout wrapper cho Doctor
└── components/
    └── Admin/
        └── Dashboard/
            └── DoctorDashboard.vue  # Demo component dashboard Doctor
```

---

## 🎯 Các thay đổi chính

### 1. **menuConfig.js** - Cấu hình menu tập trung
- Chứa cấu hình menu cho cả Admin và Doctor
- Dễ dàng thêm role mới (staff, customer, etc.)
- Mỗi role có:
  - `title`: Tiêu đề sidebar
  - `subtitle`: Phụ đề
  - `menuItems`: Danh sách menu items với icon URLs từ Figma

**Cấu trúc menu item:**
```javascript
{
  key: 'dashboard',           // Unique identifier
  label: 'Dashboard',         // Hiển thị
  icon: 'https://...',        // Figma icon URL
  path: '/admin/dashboard',   // Route path
  type: 'single'              // 'single' hoặc 'group'
}
```

**Menu group (có submenu):**
```javascript
{
  key: 'taiNguyen',
  label: 'Tài nguyên',
  icon: 'https://...',
  type: 'group',
  children: [
    { key: 'quanLyDichVu', label: 'Quản lý Dịch vụ', path: '/admin/...' },
    { key: 'khoThuoc', label: 'Kho thuốc', path: '/admin/...' }
  ]
}
```

### 2. **SideBar.vue** - Component động
**Props:**
- `role`: String ('admin' | 'doctor') - Default: 'admin'

**Features:**
- ✅ Tự động render menu dựa trên role
- ✅ Tự động highlight menu active theo route
- ✅ Tự động mở submenu chứa route hiện tại
- ✅ Xử lý cả single items và group items
- ✅ Animation toggle submenu
- ✅ Giữ nguyên màu sắc (#2f5755, #5a9690)

**Ví dụ sử dụng:**
```vue
<SideBar role="admin" />
<SideBar role="doctor" />
```

### 3. **AdminLayout.vue & DoctorLayout.vue**
- Wrapper layouts sử dụng SideBar với role tương ứng
- Giữ nguyên structure cũ (sidebar-wrapper, topbar, page-wrapper)
- Pass `role` prop cho cả SideBar và Header

---

## 🚀 Cách sử dụng

### Trong Router (src/router/index.js)

#### Cho routes Admin:
```javascript
{
  path: '/admin',
  component: () => import('@/layout/wrapper/AdminLayout.vue'),
  children: [
    {
      path: 'dashboard',
      component: () => import('@/components/Admin/Dashboard/index.vue')
    },
    {
      path: 'bai-viet',
      component: () => import('@/components/Admin/TruyenThong/BaiViet/index.vue')
    },
    // ... other admin routes
  ]
}
```

#### Cho routes Doctor:
```javascript
{
  path: '/doctor',
  component: () => import('@/layout/wrapper/DoctorLayout.vue'),
  children: [
    {
      path: 'dashboard',
      component: () => import('@/components/Admin/Dashboard/DoctorDashboard.vue')
    },
    {
      path: 'lich-kham',
      component: () => import('@/components/Doctor/LichKham/index.vue')
    },
    {
      path: 'benh-nhan',
      component: () => import('@/components/Doctor/BenhNhan/index.vue')
    },
    // ... other doctor routes
  ]
}
```

---

## 📝 Thêm Role mới (Ví dụ: Staff)

### Bước 1: Thêm config vào `menuConfig.js`
```javascript
export const menuConfig = {
  admin: { ... },
  doctor: { ... },
  staff: {
    title: 'Petty Staff',
    subtitle: 'Nhân viên hệ thống',
    menuItems: [
      {
        key: 'dashboard',
        label: 'Dashboard',
        icon: 'https://figma.com/...',
        path: '/staff/dashboard',
        type: 'single'
      },
      // ... more items
    ]
  }
}
```

### Bước 2: Tạo layout wrapper mới
```vue
<!-- src/layout/wrapper/StaffLayout.vue -->
<template>
  <div class="wrapper">
    <div class="sidebar-wrapper">
      <SideBar role="staff" />
    </div>
    <div class="topbar">
      <Header role="staff" />
    </div>
    <div class="page-wrapper">
      <div class="page-content">
        <router-view />
      </div>
    </div>
  </div>
</template>
```

### Bước 3: Thêm routes
```javascript
{
  path: '/staff',
  component: () => import('@/layout/wrapper/StaffLayout.vue'),
  children: [...]
}
```

---

## 🎨 Customization

### Thay đổi màu sắc Sidebar
Trong `SideBar.vue`, tìm các class Tailwind:
- `bg-[#2f5755]` - Màu nền sidebar
- `bg-[#5a9690]` - Màu active item
- `bg-[#3a6664]` - Màu hover
- `bg-[#264a48]` - Màu submenu background
- `text-[#5eead4]` - Màu subtitle và hover text

### Thay đổi icon
Cập nhật URL trong `menuConfig.js`:
```javascript
icon: 'https://new-icon-url.com/icon.svg'
```

---

## 🔧 Testing

### Test Sidebar Doctor
1. Chạy dev server: `npm run dev`
2. Thêm route test vào router:
```javascript
{
  path: '/test-doctor',
  component: () => import('@/layout/wrapper/DoctorLayout.vue'),
  children: [
    {
      path: '',
      component: () => import('@/components/Admin/Dashboard/DoctorDashboard.vue')
    }
  ]
}
```
3. Truy cập: `http://localhost:5173/test-doctor`

### Kiểm tra tính năng
- ✅ Menu items hiển thị đúng
- ✅ Icons load từ Figma
- ✅ Click vào menu item highlight đúng
- ✅ Submenu toggle (chỉ admin có submenu)
- ✅ Logo và title hiển thị đúng theo role
- ✅ Logout button hoạt động

---

## 📊 So sánh Admin vs Doctor

| Feature           | Admin                          | Doctor                        |
|-------------------|--------------------------------|-------------------------------|
| **Title**         | Petty Admin                    | Petty Doctor                  |
| **Subtitle**      | Quản trị hệ thống              | Hệ thống Bác sĩ               |
| **Menu Items**    | 8 items (6 groups + 2 single)  | 6 items (all single)          |
| **Has Submenus**  | ✅ Yes                         | ❌ No                         |
| **Color Scheme**  | #2f5755, #5a9690               | #2f5755, #5a9690 (giống)      |
| **Icon Source**   | Figma Design System            | Figma Design System           |

---

## ⚠️ Lưu ý quan trọng

1. **Route paths phải match với config**: 
   - Path trong `menuConfig.js` phải giống với path trong router
   
2. **Active state tự động**:
   - Component tự động highlight menu dựa trên `route.path`
   - Không cần manually set active state

3. **Submenu auto-expand**:
   - Khi vào route con, submenu cha tự động mở
   - Chỉ 1 submenu được mở tại 1 thời điểm

4. **Icon URLs từ Figma**:
   - Các URL có thể expire sau một thời gian
   - Nên download và host locally sau khi hoàn thiện

5. **Header component**:
   - Hiện tại đã pass `role` prop
   - Cần update Header.vue nếu muốn custom theo role

---

## 🛠️ Troubleshooting

### Lỗi: "Cannot read property 'menuItems' of undefined"
**Nguyên nhân**: Role không tồn tại trong config
**Giải pháp**: Kiểm tra role prop và menuConfig.js

### Menu không highlight khi navigate
**Nguyên nhân**: Path không match
**Giải pháp**: 
```javascript
// menuConfig.js
path: '/admin/dashboard'  // Phải match với

// router/index.js
path: '/admin/dashboard'  // route path này
```

### Submenu không toggle
**Nguyên nhân**: Item type không phải 'group'
**Giải pháp**: Set `type: 'group'` trong menuConfig

### Icon không hiển thị
**Nguyên nhân**: URL không hợp lệ hoặc CORS
**Giải pháp**: 
1. Check console network tab
2. Thử URL trực tiếp trên browser
3. Download icon và host local nếu cần

---

## 📚 Tài liệu tham khảo

- Vue Router: https://router.vuejs.org/
- Vue 3 Composition API: https://vuejs.org/guide/extras/composition-api-faq.html
- Tailwind CSS: https://tailwindcss.com/docs

---

## ✅ Checklist Migration

Khi migrate từ sidebar cũ sang sidebar mới:

- [x] Tạo menuConfig.js với admin config
- [x] Tạo menuConfig.js với doctor config
- [x] Refactor SideBar.vue accept role prop
- [x] Tạo AdminLayout.vue
- [x] Tạo DoctorLayout.vue
- [ ] Update router với AdminLayout
- [ ] Update router với DoctorLayout
- [ ] Test navigation admin
- [ ] Test navigation doctor
- [ ] Update Header.vue cho role support
- [ ] Test logout functionality
- [ ] Download và host icons locally (optional)

---

**Tác giả**: GitHub Copilot  
**Ngày tạo**: 2024  
**Version**: 1.0
