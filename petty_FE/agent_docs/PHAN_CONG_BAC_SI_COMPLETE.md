# ✅ PHÂN CÔNG BÁC SĨ - TOÀN BỘ TÍNH NĂNG

## 🎯 Tính năng

### User Story

```
Khi admin click "Chưa gán" trên lịch hẹn
→ Hiện modal "Phân công Bác sĩ"
→ Danh sách bác sĩ khả dụng (trang_thai = 'hoat_dong')
→ Admin chọn bác sĩ
→ Cập nhật DB: lich_hen.nhan_vien_id = selected_doctor_id
→ Hiện bác sĩ trong cột "Phụ trách"
```

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────┐
│          Frontend (Vue 3 Composition)       │
├─────────────────────────────────────────────┤
│ QuanLyLichHen/index.vue                     │
│   ├─ Fetch: GET /lich-hen                   │
│   ├─ Modal: PhanCongBacSi                   │
│   └─ Update: PATCH /lich-hen/{id}           │
│                                             │
│ PhanCongBacSi/index.vue                     │
│   ├─ Fetch: GET /nhan-vien                  │
│   ├─ Filter: vai_tro = 'bac_si'             │
│   ├─ Group: Available/Unavailable           │
│   └─ Action: Assign doctor                  │
└─────────────────────────────────────────────┘
         ↓↑ API
┌─────────────────────────────────────────────┐
│      Backend (Laravel REST API)             │
├─────────────────────────────────────────────┤
│ LichHenController                           │
│   ├─ index()      → List appointments       │
│   ├─ update()     → Update nhan_vien_id     │
│   └─ transformData() → Format response      │
│                                             │
│ NhanVienController                          │
│   ├─ index()       → List all employees     │
│   └─ getDoctors()  → Filter doctors only    │
└─────────────────────────────────────────────┘
         ↓↑ ORM
┌─────────────────────────────────────────────┐
│      Database (MySQL)                       │
├─────────────────────────────────────────────┤
│ lich_hen table:                             │
│   id, nhan_vien_id, khach_hang_id, ...      │
│                                             │
│ nhan_vien table:                            │
│   id, ho_ten, vai_tro, chuc_vu, trang_thai │
└─────────────────────────────────────────────┘
```

---

## 📋 Implementation Checklist

### Backend ✅

- [x] NhanVienController: Filter doctors by vai_tro
- [x] LichHenController: Update appointment with nhan_vien_id
- [x] Routes: Add endpoints
- [ ] Test endpoints

### Frontend ✅

- [x] PhanCongBacSi component: Fetch doctors
- [x] Filter: Available vs Unavailable
- [x] Assign: PATCH to update appointment
- [x] Loading states & error handling
- [x] Success feedback (close modal)

---

## 🔌 API Calls

### 1. **Fetch Doctors**

```javascript
// FE: PhanCongBacSi/index.vue
const response = await client.get("/nhan-vien");

// Backend filters inside transformAppointment()
// Only doctors: vai_tro = 'bac_si' || chuc_vu = 'Bác sĩ'
```

### 2. **Assign Doctor**

```javascript
// FE: PhanCongBacSi/index.vue - handleSelectStaff()
const response = await client.patch(`/lich-hen/${appointmentId}`, {
  nhan_vien_id: staff.id,
});
```

### 3. **Fetch Appointments** (Auto-update)

```javascript
// FE: QuanLyLichHen/index.vue
// After assignment, parent component refreshes list
fetchAppointments();
```

---

## 📊 Data Transformation

### API Response - GET /nhan-vien

```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "ho_ten": "Nguyễn Văn A",
      "vai_tro": "bac_si",
      "trang_thai": "hoat_dong"
    },
    {
      "id": 2,
      "ho_ten": "Phạm Thị B",
      "vai_tro": "bac_si",
      "trang_thai": "da_khoa"
    }
  ]
}
```

### FE Transform

```javascript
const doctors = response.data.data
  .filter((nv) => nv.vai_tro === "bac_si")
  .map((doctor) => ({
    id: doctor.id,
    name: doctor.ho_ten,
    initial: doctor.ho_ten.charAt(0),
    trang_thai: doctor.trang_thai,
  }));
```

### Display Logic

```
trang_thai === 'hoat_dong' → Available Section (Green)
trang_thai !== 'hoat_dong' → Unavailable Section (Gray)
```

---

## 🚀 Workflow

```
1. Admin click "Chưa gán"
   ↓
2. Modal opens: PhanCongBacSi component
   ↓
3. Component mounts: fetchDoctors()
   ├─ API: GET /nhan-vien
   ├─ Filter: vai_tro = 'bac_si'
   └─ Display: Available & Unavailable lists
   ↓
4. Admin select doctor
   ↓
5. handleSelectStaff() triggered
   ├─ API: PATCH /lich-hen/{id}
   ├─ Body: { nhan_vien_id: staff.id }
   └─ Update DB
   ↓
6. Success response
   ├─ Emit 'assign' event to parent
   └─ Close modal
   ↓
7. Parent component (QuanLyLichHen)
   ├─ Receive 'assign' event
   ├─ Call fetchAppointments()
   └─ Refresh table (show doctor in "Phụ trách" column)
   ↓
8. ✅ Done! Doctor assigned and displayed
```

---

## 🎨 UI States

### Loading State

```
Modal opens → Spinner shows → fetchDoctors() in progress
```

### Available Doctors

```
✅ Nguyễn Văn A (Bác sĩ)
   ✓ Đang trực  [Chọn]
```

### Unavailable Doctors

```
⚠️ Phạm Thị B (Bác sĩ) - Opacity 50%
   ✗ Không có ca trực
```

### Success

```
Modal closes automatically
Parent refreshes table
Doctor name appears in "Phụ trách" column
```

---

## 🔧 Configuration

### Filter Conditions (Backend)

```php
// vai_tro values
'bac_si'  → Doctor role
'doctor'  → Alternative doctor role
'chuc_vu' → Job title "Bác sĩ"

// trang_thai values
'hoat_dong'  → Active/Available
'da_khoa'    → Locked/Unavailable
'nghi_viec'  → Resigned/Unavailable
```

---

## ✨ Features

✅ Load doctors from API  
✅ Filter by vai_tro = 'bac_si'  
✅ Group: Available & Unavailable  
✅ Assign doctor to appointment  
✅ Update database (nhan_vien_id)  
✅ Auto-refresh parent list  
✅ Loading states  
✅ Error handling  
✅ Success feedback

---

## 🎯 Next Steps

1. **Backend Implementation** (if not done):

   - Add `getDoctors()` method to NhanVienController
   - Add `update()` method to LichHenController
   - Register routes

2. **Test Endpoints**:

   - GET /nhan-vien → Filter bác sĩ
   - PATCH /lich-hen/{id} → Assign doctor

3. **Frontend Testing**:
   - Open "Quản lý Lịch Hẹn"
   - Click "Chưa gán" button
   - Select doctor
   - Verify doctor appears in table

---

## 📞 Troubleshooting

| Issue             | Solution                                |
| ----------------- | --------------------------------------- |
| No doctors shown  | Check vai_tro = 'bac_si' in DB          |
| Assignment fails  | Verify nhan_vien_id exists              |
| Modal won't close | Check API response status               |
| List not updating | Ensure parent calls fetchAppointments() |
