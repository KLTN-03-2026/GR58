# 📊 Luồng Hoạt động Đăng ký Ca Làm việc

## 🔄 Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                  DOCTOR/NURSE LOGIN                         │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
        ┌─────────────────────────────┐
        │   Mở "Lịch làm việc"        │
        │   - Tab 1: Đăng ký ca      │
        │   - Tab 2: Lịch của tôi    │
        └────────────┬────────────────┘
                     │
                     ▼
        ┌─────────────────────────────┐
        │  Click "🚀 Gửi đăng ký"    │
        │                             │
        │  showRegisterModal = true   │
        └────────────┬────────────────┘
                     │
                     ▼
    ┌────────────────────────────────────┐
    │  📦 DangKyCa Modal Mở              │
    │  ────────────────────────────────  │
    │  fetchAvailableShifts()            │
    │  GET /lich-lam-viec                │
    │    ├─ tu_ngay: start              │
    │    ├─ den_ngay: end               │
    │    └─ per_page: 50                │
    └────────────┬───────────────────────┘
                 │
                 ▼
    ┌────────────────────────────────────┐
    │  Backend Response:                 │
    │  {                                 │
    │    data: [                         │
    │      {                             │
    │        id: 123,                    │
    │        ngay_lam: "2025-12-12",     │
    │        thoi_gian_truc: "ca_sang",  │
    │        phong_truc: "Phòng 1",      │
    │        ...                         │
    │      },                            │
    │      ...                           │
    │    ]                               │
    │  }                                 │
    └────────────┬───────────────────────┘
                 │
                 ▼
    ┌────────────────────────────────────┐
    │  🎯 User Actions:                  │
    │  1. Chọn ca (selectShift)         │
    │  2. Nhập ghi chú (formData)       │
    │  3. Click "Gửi đăng ký"           │
    └────────────┬───────────────────────┘
                 │
                 ▼
    ┌────────────────────────────────────┐
    │  💾 submitRegistration():          │
    │                                    │
    │  POST /lich-dang-ky/dang-ky-...   │
    │  {                                 │
    │    ngay_gio: "2025-12-12 06:...", │
    │    lich_lam_viec_id: 123,         │
    │    ghi_chu: "..."                 │
    │  }                                 │
    │                                    │
    │  isSubmitting = true (show loader) │
    └────────────┬───────────────────────┘
                 │
                 ▼
    ┌────────────────────────────────────┐
    │  🔐 Backend Processing:            │
    │  1. Verify user is staff           │
    │  2. Validate lich_lam_viec_id     │
    │  3. Auto set nhan_vien_id         │
    │  4. Auto set trang_thai:          │
    │     "da_xac_nhan"                  │
    │  5. Create LichDangKy record      │
    │  6. Return created record         │
    └────────────┬───────────────────────┘
                 │
        ┌────────┴──────────┐
        │                   │
   ✅ Success         ❌ Error
        │                   │
        ▼                   ▼
    ┌──────────────┐  ┌──────────────┐
    │ ✓ Toast:     │  │ ✗ Toast:     │
    │ Thành công   │  │ Lỗi          │
    │              │  │              │
    │ closeModal() │  │ Show error   │
    │ fetchSchedul │  │ Keep modal   │
    │ Data()       │  │ open         │
    └──────────────┘  └──────────────┘
        │
        ▼
    ┌──────────────┐
    │ Schedule     │
    │ Refreshed    │
    │ (show new    │
    │ registration)│
    └──────────────┘
```

---

## 🔀 State Management Flow

```
┌──────────────────────────────────────┐
│  parent: LichLamViec/index.vue       │
├──────────────────────────────────────┤
│  State:                              │
│  - showRegisterModal (ref)           │
│  - currentDate (ref)                 │
│  - startOfWeek (computed)            │
│  - endOfWeek (computed)              │
│  - scheduleData (ref)                │
│  - activeTab (ref)                   │
└──────────────┬───────────────────────┘
               │
         props: isOpen
         props: weekStartDate
         props: weekEndDate
               │
               ▼
    ┌──────────────────────────────────┐
    │  child: DangKyCa/index.vue       │
    ├──────────────────────────────────┤
    │  State:                          │
    │  - availableShifts (ref)         │
    │  - selectedShiftId (ref)         │
    │  - isSubmitting (ref)            │
    │  - formData (ref)                │
    │  - loading (ref)                 │
    └──────────────┬────────────────────┘
                   │
        emit: close
        emit: success
                   │
        ┌──────────┴──────────┐
        │                     │
        ▼                     ▼
    closeModal()        fetchScheduleData()
    showRegisterModal   Update parent data
    = false             Re-render schedule
```

---

## 🔗 API Interactions

### 1️⃣ Fetch Available Shifts

```
Component: DangKyCa
Trigger: Modal opened (watch: props.isOpen)
Endpoint: GET /lich-lam-viec
Params: {tu_ngay, den_ngay, per_page}
Response: Array of shifts
Store: availableShifts (ref)
```

### 2️⃣ Submit Registration

```
Component: DangKyCa
Trigger: User clicks "Gửi đăng ký"
Endpoint: POST /lich-dang-ky/dang-ky-nhan-vien
Payload: {ngay_gio, lich_lam_viec_id, ghi_chu}
Response: Created LichDangKy object
Action: Emit "success"
```

### 3️⃣ Refresh Schedule

```
Component: LichLamViec
Trigger: Modal emits "success"
Endpoint: GET /lich-lam-viec?tu_ngay=...&den_ngay=...
Response: Updated schedule data
Store: scheduleData (ref)
Update: Calendar view
```

---

## 🎭 User Interactions

```
MODAL STATE: Closed
├─ User sees: "🚀 Gửi đăng ký" button
├─ Action: Click button
└─ Result: showRegisterModal = true

MODAL STATE: Opening
├─ Component: DangKyCa mounts
├─ Action: fetch available shifts
└─ Result: Show shift list

MODAL STATE: Shifts Loaded
├─ User sees: List of available shifts
├─ Action: Click a shift card
└─ Result: Select shift + highlight

MODAL STATE: Shift Selected
├─ User sees: Selected shift highlighted
├─ Action: (Optional) Type note in textarea
├─ Action: Click "Gửi đăng ký" button
└─ Result: isSubmitting = true (show loader)

MODAL STATE: Submitting
├─ User sees: Button disabled with loader
├─ Action: POST request sent
└─ Result: Wait for response

MODAL STATE: Success
├─ API returns: Success response
├─ Component: closeModal()
├─ Parent: Refresh schedule
├─ User sees: Toast "Thành công"
└─ Result: Modal closes, schedule updates

MODAL STATE: Error
├─ API returns: Error response
├─ Component: Show error toast
├─ Button: Reset to enabled state
├─ Modal: Stays open
└─ User can: Retry or close
```

---

## 💾 Data Flow Example

```javascript
// STEP 1: User opens modal
showRegisterModal = true

// STEP 2: Modal fetches shifts
const response = await api.get('/lich-lam-viec', {
  params: {
    tu_ngay: '2025-12-09',
    den_ngay: '2025-12-15',
    per_page: 50
  }
})
// Returns: [
//   {id: 1, ngay_lam: '2025-12-10', thoi_gian_truc: 'ca_sang', ...},
//   {id: 2, ngay_lam: '2025-12-10', thoi_gian_truc: 'ca_chieu', ...},
//   ...
// ]

// STEP 3: User selects shift
selectShift({id: 1, ngay_lam: '2025-12-10', ...})
// selectedShiftId = 1

// STEP 4: User submits
submitRegistration()
// Find selected shift
const selectedShift = availableShifts.find(s => s.id === 1)

// Prepare payload
const payload = {
  ngay_gio: '2025-12-10 06:00:00',
  lich_lam_viec_id: 1,
  ghi_chu: '...'
}

// Send to backend
const response = await api.post(
  '/lich-dang-ky/dang-ky-nhan-vien',
  payload
)
// Returns: {id: 999, nhan_vien_id: 5, trang_thai: 'da_xac_nhan', ...}

// STEP 5: Close modal
closeModal()
// showRegisterModal = false
// selectedShiftId = null
// formData = {}

// STEP 6: Refresh schedule
fetchScheduleData()
// Fetch from /lich-lam-viec?tu_ngay=...&den_ngay=...
// Update calendar with new registration
```

---

## 🌳 Component Tree

```
App.vue
└── LichLamViec/index.vue (Doctor layout)
    ├── Tabs
    │   ├── Tab 1: Đăng ký ca trực
    │   │   ├── Week Navigation
    │   │   ├── Stats (Ca tuần, Tổng giờ, etc.)
    │   │   ├── View Mode Toggle
    │   │   └── [🚀 Gửi đăng ký Button] ← Opens modal
    │   │
    │   └── Tab 2: Lịch của tôi
    │       ├── Week Navigation
    │       ├── Calendar Table
    │       ├── Stats
    │       └── Notes
    │
    ├── Detail Modal (conditional)
    │   └── Shift details + appointments
    │
    └── 🆕 DangKyCa Modal (NEW!)
        ├── Shift List
        ├── Select shift
        ├── Add notes
        ├── Submit button
        └── Success/Error handling
```

---

## ⚡ Performance Considerations

- **Lazy Loading**: Modal only fetches shifts when opened
- **Memoization**: `computed` properties cache values
- **Debouncing**: Not needed (one-time fetch per open)
- **Pagination**: API supports per_page parameter
- **Caching**: Schedule refreshed after successful registration

---

## 🔒 Security

- **Auth Middleware**: `staff.only` on backend
- **User Validation**: Backend checks user is staff
- **ID Verification**: Backend validates lich_lam_viec_id
- **Automatic Fields**: Backend auto-fills nhan_vien_id (from authenticated user)
- **Status Control**: Backend auto-sets trang_thai (user can't override)

Enjoy! 🎉
