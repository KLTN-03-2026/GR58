# 🧪 Hướng Dẫn Test Fix Lịch Admin Phân Công

## ⚡ Quick Test (2 phút)

### Bước 1: Start Dev Server

```bash
cd c:\xampp\htdocs\PETTY_VCMS_FE
npm run dev
```

### Bước 2: Mở Trình Duyệt

1. Vào http://localhost:5173 (hoặc port Vite của bạn)
2. Login với tài khoản bác sĩ/nhân viên
3. Navigate → **Lịch làm việc**

### Bước 3: Mở DevTools

- Nhấn **F12**
- Chọn tab **Console**
- Xem logs:
  ```
  📅 Starting to process schedule data...
  ✅ Found X shift(s) for 2025-12-XX
  📊 Calendar update summary:
    - Sáng (8h-12h): X shift(s)
    - Chiều (13h-17h): X shift(s)
    - Tối (18h-21h): X shift(s)
    - Phân công (Các giờ khác): X shift(s)  ← Phải > 0 nếu có lịch admin
  ```

### Bước 4: Kiểm Tra Giao Diện

✅ **Dòng 4 "Phân công (Các giờ khác)"** phải có ô màu xanh  
✅ Ô hiển thị tên ca hoặc ghi chú  
✅ Nếu nhiều ca → có text "+X ca khác"

---

## 🔍 Test Chi Tiết

### Test Case 1: Admin Phân Ca Tiêu Chuẩn

**Setup:**

```sql
-- Thêm ca sáng vào database
INSERT INTO lich_lam_viec (nhan_vien_id, ngay_lam, thoi_gian_truc, ghi_chu)
VALUES (YOUR_ID, '2025-12-11', 'ca_sang', 'Phòng khám A');
```

**Expected:**

- Dòng 1 "Sáng" hiển thị "Ca Sáng - Phòng khám A"

---

### Test Case 2: Admin Phân Ca Tùy Chỉnh

**Setup:**

```sql
-- Thêm ca phân công đặc biệt
INSERT INTO lich_lam_viec (nhan_vien_id, ngay_lam, thoi_gian_truc, ghi_chu)
VALUES (YOUR_ID, '2025-12-11', 'ca_phan_cong', 'Ca cấp cứu 14h-18h');
```

**Expected:**

- Dòng 4 "Phân công" hiển thị "Phân công - Ca cấp cứu 14h-18h"
- Console log: `Admin-assigned shift (non-standard time) for 2025-12-11: ca_phan_cong`

---

### Test Case 3: Nhiều Ca Phân Công Cùng Ngày

**Setup:**

```sql
-- Thêm 2 ca phân công
INSERT INTO lich_lam_viec (nhan_vien_id, ngay_lam, thoi_gian_truc, ghi_chu)
VALUES
  (YOUR_ID, '2025-12-11', 'ca_truc_dem', 'Trực đêm 20h-6h'),
  (YOUR_ID, '2025-12-11', 'ca_ho_tro', 'Hỗ trợ phẫu thuật 10h');
```

**Expected:**

- Dòng 4 "Phân công" hiển thị ca đầu tiên
- Có text "+1 ca khác" phía dưới
- Ô có border xanh lá (upcoming) hoặc cam (ongoing)

---

### Test Case 4: Kết Hợp Ca Admin + Ca Đăng Ký

**Setup:**

```sql
-- Admin phân ca
INSERT INTO lich_lam_viec (nhan_vien_id, ngay_lam, thoi_gian_truc, ghi_chu)
VALUES (YOUR_ID, '2025-12-11', 'ca_khac', 'Phòng B');
```

```javascript
// Bác sĩ tự đăng ký ca (qua UI hoặc API)
POST /api/lich-dang-ky
{
  "ngay_gio": "2025-12-11 15:00:00",
  "ghi_chu": "Tự đăng ký ca chiều"
}
```

**Expected:**

- Dòng 4 "Phân công" có 2 ca
- Hiển thị "+1 ca khác"

---

## 🐛 Troubleshooting

### Vấn đề 1: Dòng "Phân công" vẫn trống

**Kiểm tra:**

```javascript
// Console → tìm log này
console.log("fetchScheduleData response:", data);
```

**Nếu `schedule: []`:**

- Backend chưa trả về dữ liệu
- Kiểm tra routes backend đã thêm chưa (xem `ROUTES_API_LICH_LAM_VIEC.php`)
- Kiểm tra database có data chưa

**Nếu có data nhưng không hiển thị:**

- Kiểm tra `thoi_gian_truc` có giá trị gì
- Xem log: `Admin-assigned shift (non-standard time)...`

---

### Vấn đề 2: API trả về 404

**Fix:**

```php
// File: routes/api.php
// Thêm trong middleware auth:sanctum
Route::get('/lich-lam-viec', [LichLamViecController::class, 'getMySchedule']);
```

**Verify:**

```bash
php artisan route:list | grep lich-lam-viec
```

---

### Vấn đề 3: Console log không xuất hiện

**Kiểm tra:**

1. Hard refresh: `Ctrl + Shift + R`
2. Clear cache: DevTools → Application → Clear storage
3. Check file đã save chưa: `git status` hoặc xem icon VSCode

---

## 📊 Expected Console Output (Full Example)

```javascript
fetchScheduleData response: {
  status: true,
  data: {
    nhan_vien: { id: 5, full_name: "Nguyễn Văn A" },
    period: { start: "2025-12-09", end: "2025-12-15" },
    schedule: [
      {
        date: "2025-12-09",
        lich_lam_viec: [
          { id: 1, thoi_gian_truc: "ca_sang", ghi_chu: "Phòng A" },
          { id: 2, thoi_gian_truc: "ca_phan_cong", ghi_chu: "Cấp cứu" }
        ],
        lich_hen: []
      },
      // ... more days
    ],
    statistics: { total_work_days: 5 }
  }
}

📅 Starting to process schedule data...
Processing day 2025-12-09, shifts: (2) [{...}, {...}]
✅ Found 2 shift(s) for 2025-12-09
Admin-assigned shift (non-standard time) for 2025-12-09: ca_phan_cong

📊 Calendar update summary:
  - Sáng (8h-12h): 1 shift(s)
  - Chiều (13h-17h): 0 shift(s)
  - Tối (18h-21h): 0 shift(s)
  - Phân công (Các giờ khác): 1 shift(s)
```

---

## ✅ Checklist Trước Khi Deploy

- [ ] Test với tài khoản bác sĩ khác nhau
- [ ] Test với tuần có/không có lịch phân công
- [ ] Test navigation: Tuần trước/Tuần sau
- [ ] Test nhiều ca trong 1 ô
- [ ] Test responsive (mobile/tablet)
- [ ] Console không có error màu đỏ
- [ ] API response time < 2s
- [ ] Backend routes đã thêm vào `routes/api.php`

---

## 📞 Nếu Cần Hỗ Trợ

1. **Console Logs:** Copy tất cả log và gửi
2. **Network Tab:**
   - F12 → Network
   - Filter: `lich-lam-viec`
   - Click request → Preview/Response → Screenshot
3. **Database:** Export sample data
4. **Screenshot:** Chụp màn hình lịch hiện tại

---

**Happy Testing! 🚀**
