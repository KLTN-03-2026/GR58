# Router Configuration Update

## 📋 Thay đổi chính

### Trước đây (Old approach):
```javascript
{
  path: "/admin/dashboard",
  component: () => import("../components/Admin/Dashboard/index.vue"),
  meta: { layout: "sidebar" }  // ❌ Dùng meta để chọn layout
}
```

### Bây giờ (New approach):
```javascript
{
  path: "/admin",
  component: () => import("../layout/wrapper/AdminLayout.vue"),  // ✅ Layout là parent route
  children: [
    {
      path: "dashboard",  // Thành /admin/dashboard
      component: () => import("../components/Admin/Dashboard/index.vue")
    }
    // ... other admin pages
  ]
}
```

## 🎯 Lợi ích của cách mới

1. **Rõ ràng hơn**: Layout được định nghĩa là component, không phải meta tag
2. **Tái sử dụng code**: Mỗi role có layout riêng (AdminLayout, DoctorLayout)
3. **Type-safe**: Vue Router hiểu đúng cấu trúc nested routes
4. **Dễ maintain**: Thêm role mới chỉ cần tạo layout wrapper và route group

## 📂 Cấu trúc Routes hiện tại

```
/admin (AdminLayout)
  ├─ /dashboard
  ├─ /quan-ly-dich-vu
  ├─ /kho-thuoc-vat-tu
  ├─ /tai-khoan
  ├─ /lich-lam-viec
  ├─ /quan-ly-lich-hen
  ├─ /ho-so-benh-an
  ├─ /danh-sach-hoa-don
  ├─ /phieu-chi
  ├─ /bai-viet
  │   ├─ /them-moi
  │   └─ /chinh-sua/:id
  ├─ /khuyen-mai
  │   ├─ /them-moi
  │   └─ /chinh-sua/:id
  ├─ /bao-cao-doanh-thu
  ├─ /bao-cao-hieu-suat
  ├─ /bao-cao-thuoc-vat-tu
  ├─ /cau-hinh
  └─ /trang-ca-nhan

/doctor (DoctorLayout)
  ├─ /dashboard
  ├─ /lich-kham
  ├─ /benh-nhan
  ├─ /can-lam-sang
  ├─ /kho-thuoc
  └─ /ca-nhan
```

## 🔧 Cách test

### Test Admin Layout:
```
npm run dev
Truy cập: http://localhost:5173/admin/dashboard
```

### Test Doctor Layout:
```
npm run dev
Truy cập: http://localhost:5173/doctor/dashboard
```

## ⚠️ Lưu ý quan trọng

1. **Login paths giữ nguyên**: `/admin/dang-nhap` không nằm trong AdminLayout (không có sidebar khi login)

2. **Navigation Guard**: Router guard đã được config để redirect đúng:
   - Admin routes require token
   - Redirect về `/admin/dang-nhap` nếu chưa login

3. **Children routes**: Path trong children KHÔNG có `/` đầu tiên
   - ✅ `path: "dashboard"` → `/admin/dashboard`
   - ❌ `path: "/dashboard"` → `/dashboard` (sai!)

4. **Placeholder components**: Các trang Doctor đã tạo file placeholder để không bị lỗi 404

## 🚀 Thêm route mới

### Thêm admin route:
```javascript
{
  path: "/admin",
  component: () => import("../layout/wrapper/AdminLayout.vue"),
  children: [
    // ... existing routes
    {
      path: "new-page",  // → /admin/new-page
      component: () => import("../components/Admin/NewPage/index.vue")
    }
  ]
}
```

### Thêm doctor route:
```javascript
{
  path: "/doctor",
  component: () => import("../layout/wrapper/DoctorLayout.vue"),
  children: [
    // ... existing routes
    {
      path: "new-page",  // → /doctor/new-page
      component: () => import("../components/Doctor/NewPage/index.vue")
    }
  ]
}
```

## 📊 So sánh

| Aspect           | Old (meta layout) | New (nested routes) |
|------------------|-------------------|---------------------|
| Layout control   | Meta tag          | Parent component    |
| Type safety      | ❌ No             | ✅ Yes              |
| Code reuse       | ❌ Limited        | ✅ Full             |
| Scalability      | ❌ Hard           | ✅ Easy             |
| Maintainability  | ❌ Complex        | ✅ Simple           |
| Performance      | Same              | Same                |

## ✅ Checklist Migration

- [x] Tạo AdminLayout.vue
- [x] Tạo DoctorLayout.vue
- [x] Update router với nested routes
- [x] Tạo placeholder components cho Doctor
- [x] Test admin routes
- [ ] Test doctor routes
- [ ] Update authentication logic nếu cần
- [ ] Deploy và test production

---

**Note**: Nếu cần rollback về cách cũ, chỉ cần uncomment các routes cũ và xóa nested structure.
