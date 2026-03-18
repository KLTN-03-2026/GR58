# 🚀 Quick Reference - Đăng ký Ca Làm việc

## 🎯 Nhanh gọn

### Chức năng gì được thêm?

Cho phép bác sĩ/nhân viên tự đăng ký ca làm việc qua modal popup.

### Nó ở đâu?

Tab "Đăng ký ca trực" → Nút "🚀 Gửi đăng ký" → Modal hiện lên

### Nó hoạt động như nào?

1. User click button "🚀 Gửi đăng ký"
2. Modal hiện danh sách ca trống
3. User chọn ca + nhập ghi chú
4. Click "Gửi đăng ký"
5. Backend tự động duyệt
6. Lịch cập nhật

---

## 📋 File liên quan

### Created Files

```
src/components/Doctor/LichLamViec/DangKyCa/index.vue
DANG_KY_CA_LAM_VIEC_GUIDE.md
DANG_KY_CA_FLOW_DIAGRAM.md
DANG_KY_CA_SUMMARY.md
DANG_KY_CA_QUICK_REF.md (file này)
```

### Modified Files

```
src/components/Doctor/LichLamViec/index.vue
```

---

## 🔗 API Endpoints

### Fetch Shifts

```
GET /lich-lam-viec
  Params: tu_ngay, den_ngay, per_page
  Returns: [{id, ngay_lam, thoi_gian_truc, phong_truc, ...}]
```

### Submit Registration

```
POST /lich-dang-ky/dang-ky-nhan-vien
  Body: {ngay_gio, lich_lam_viec_id, ghi_chu}
  Returns: {id, nhan_vien_id, trang_thai: 'da_xac_nhan', ...}
```

---

## 💾 Component Usage

```vue
<template>
  <!-- Button -->
  <button @click="showRegisterModal = true">🚀 Gửi đăng ký</button>

  <!-- Modal -->
  <DangKyCa
    :is-open="showRegisterModal"
    :week-start-date="formatDate(startOfWeek)"
    :week-end-date="formatDate(endOfWeek)"
    @close="showRegisterModal = false"
    @success="handleSuccess"
  />
</template>

<script setup>
import { ref } from "vue";
import DangKyCa from "./DangKyCa/index.vue";

const showRegisterModal = ref(false);

const handleSuccess = () => {
  showRegisterModal.value = false;
  fetchScheduleData(); // Refresh
};
</script>
```

---

## 🎨 Modal Props & Events

### Props

```javascript
{
  isOpen: Boolean,           // Control modal visibility
  weekStartDate: String,     // Format: YYYY-MM-DD
  weekEndDate: String,       // Format: YYYY-MM-DD
}
```

### Emits

```javascript
close; // Called when user clicks close/cancel
success; // Called after successful submission
```

---

## 🔄 State in Modal

```javascript
// Parent (LichLamViec/index.vue)
showRegisterModal = ref(false);

// Child (DangKyCa/index.vue)
availableShifts = ref([]);
selectedShiftId = ref(null);
isSubmitting = ref(false);
formData = ref({ ghi_chu: "" });
```

---

## ✅ Validation

### Frontend

- Bắt buộc chọn ca
- Ghi chú tùy chọn

### Backend

- User phải là staff
- lich_lam_viec_id phải tồn tại
- Auto set nhan_vien_id = current user
- Auto set trang_thai = 'da_xac_nhan'

---

## 🎯 Key Methods

```javascript
// In DangKyCa component

// Fetch shifts từ API
fetchAvailableShifts();
// GET /lich-lam-viec
// Update availableShifts

// Select shift
selectShift(shift);
// Set selectedShiftId = shift.id

// Submit registration
submitRegistration();
// POST /lich-dang-ky/dang-ky-nhan-vien
// Show loading
// Handle success/error
// Emit success or show error

// Close modal
closeModal();
// Reset all states
// Emit close
```

---

## 🎨 Color Codes

| Color      | Meaning      |
| ---------- | ------------ |
| 🟢 Emerald | Ca Sáng      |
| 🟡 Amber   | Ca Chiều     |
| ⚫ Slate   | Ca Tối       |
| 🔵 Blue    | Primary/Info |
| 🟢 Green   | Success      |
| 🔴 Red     | Error        |

---

## ⚡ Performance

- Lazy load (fetch only when modal opens)
- No unnecessary re-renders
- Efficient state management

---

## 🐛 Common Issues

| Issue                  | Solution                                       |
| ---------------------- | ---------------------------------------------- |
| Modal không hiện       | Check `showRegisterModal = true`               |
| Shifts không load      | Check API `/lich-lam-viec`                     |
| Submit fail            | Check API `/lich-dang-ky/dang-ky-nhan-vien`    |
| Lịch không update      | Check `fetchScheduleData()` in success handler |
| Button submit disabled | Select shift first                             |

---

## 📱 Responsive Breakpoints

- Mobile: < 768px (full width)
- Tablet: 768px - 1024px (90% width)
- Desktop: > 1024px (max-w-2xl)

---

## 🔒 Security

- Auth middleware: `staff.only` on backend
- Auto set user ID: Backend sets nhan_vien_id
- Auto set status: Backend sets trang_thai
- Validate IDs: Backend validates all IDs

---

## 📚 Learn More

- **Full Guide**: DANG_KY_CA_LAM_VIEC_GUIDE.md
- **Flow Diagram**: DANG_KY_CA_FLOW_DIAGRAM.md
- **Summary**: DANG_KY_CA_SUMMARY.md

---

## 🆘 Need Help?

1. Check API endpoints working
2. Check auth token valid
3. Check backend middleware
4. Check browser console errors
5. Check network requests
6. Check toast notifications

---

**Status**: ✅ Ready to use!
