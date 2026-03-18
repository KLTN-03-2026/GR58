# 🎯 QUICK FIX CHECKLIST - Lịch Làm Việc

## 📋 Danh Sách Cần Làm

### ✅ BƯỚC 1: Xác Nhận Frontend Đã Fix (HOÀN THÀNH)

**File:** `src/services/lichLamViecService.js`

✅ **Đã cập nhật:**

- Dòng 10: `/lich-lam-viec/cua-toi` → `/lich-lam-viec`
- Dòng 27: `/lich-lam-viec/cua-toi/hom-nay` → `/lich-lam-viec/hom-nay`

**Xác nhận:**

```bash
# Mở file và check:
grep -n "api.get" src/services/lichLamViecService.js
```

**Kỳ vọng thấy:**

```
10:    const response = await api.get("/lich-lam-viec", {
27:    const response = await api.get("/lich-lam-viec/hom-nay");
```

---

### ⏳ BƯỚC 2: Cập Nhật Backend Routes (CẦN LÀM NGAY)

**File:** Backend `routes/api.php`

**Bạn cần thêm vào middleware('auth:sanctum') group:**

```php
// Lấy lịch làm việc của user trong khoảng ngày
Route::get('/lich-lam-viec', [LichLamViecController::class, 'getMySchedule']);

// Lấy lịch hôm nay
Route::get('/lich-lam-viec/hom-nay', [LichLamViecController::class, 'getMyTodaySchedule']);

// Lấy chi tiết lịch
Route::get('/lich-lam-viec/{id}', [LichLamViecController::class, 'show']);

// Tạo mới lịch
Route::post('/lich-lam-viec', [LichLamViecController::class, 'store']);
```

**⚠️ QUAN TRỌNG:**

- Custom routes PHẢI ĐẶT **TRƯỚC** `apiResource()` (nếu có)
- Nếu có dòng `Route::apiResource('lich-lam-viec', ...)` → **XÓABỎ** hoặc bỏ nó vào comment

**Tham khảo file:** `ROUTES_API_LICH_LAM_VIEC.php` trong project

---

### ✅ BƯỚC 3: Test API (Sau khi làm Bước 2)

**Dùng Postman / Thunder Client:**

```
GET http://localhost:8000/api/lich-lam-viec?start_date=2025-12-29&end_date=2025-01-04

Headers:
  Authorization: Bearer YOUR_TOKEN
  Accept: application/json
```

**Nếu thành công (200):**

```json
{
    "status": true,
    "data": {
        "nhan_vien": {...},
        "period": {...},
        "schedule": [
            {
                "date": "2025-12-29",
                "lich_lam_viec": [...],
                "lich_hen": [...]
            }
        ],
        "statistics": {...}
    }
}
```

**Nếu lỗi 404:**
→ Routes chưa được cập nhật đúng, kiểm tra lại Bước 2

**Nếu lỗi 401:**
→ Token hết hạn, logout rồi login lại

---

### ✅ BƯỚC 4: Test trong Vue Component

1. **Mở ứng dụng → Vào Lịch Làm Việc**
2. **Mở DevTools (F12) → Network tab**
3. **Bấm "Tuần trước" hoặc "Tuần sau"**
4. **Xem request:**
   - URL: `http://localhost:8000/api/lich-lam-viec?start_date=...&end_date=...`
   - Status: **200** (không phải 404, 500)
   - Response: Có dữ liệu

**Nếu schedule hiển thị:** ✅ **HOÀN THÀNH!**

**Nếu vẫn trống:**

- Kiểm tra Database có dữ liệu LichLamViec không
- Xem console logs
- Xem Laravel logs: `tail -f storage/logs/laravel.log`

---

## 🔍 Troubleshooting

### ❌ Problem: 404 Not Found

**Nguyên nhân:** Routes chưa được cập nhật

**Giải pháp:**

```bash
# Xác nhận routes
php artisan route:list | grep lich-lam-viec

# Kỳ vọng thấy:
# GET|HEAD /api/lich-lam-viec ..... LichLamViecController@getMySchedule
# GET|HEAD /api/lich-lam-viec/hom-nay ..... LichLamViecController@getMyTodaySchedule
```

---

### ❌ Problem: 500 Internal Server Error

**Nguyên nhân:** Backend error

**Giải pháp:**

```bash
# Xem logs
tail -f storage/logs/laravel.log

# Tìm dòng error có chứa 500 hoặc exception
```

---

### ❌ Problem: Schedule vẫn rỗng

**Nguyên nhân 1:** Database không có dữ liệu

```sql
SELECT COUNT(*) FROM lich_lam_viec
WHERE nhan_vien_id = 1
AND ngay_lam BETWEEN '2025-12-01' AND '2025-12-31';
```

**Nguyên nhân 2:** Admin chưa phân công lịch
→ Vào Admin dashboard, chọn nhân viên, phân công lịch

**Nguyên nhân 3:** User_id không khớp

```sql
-- Check user hiện tại
SELECT * FROM nhan_vien WHERE id = 1;

-- Check lịch của user đó
SELECT * FROM lich_lam_viec WHERE nhan_vien_id = 1;
```

---

## ✅ Quick Check List

- [ ] Frontend fix xong (`lichLamViecService.js`)
- [ ] Backend routes cập nhật (`routes/api.php`)
- [ ] Test API trực tiếp (Postman) → 200 OK
- [ ] DevTools Network tab → endpoint đúng
- [ ] Database → có dữ liệu LichLamViec
- [ ] Vue Component → bảng lịch hiển thị

---

## 🎯 Expected Result

Sau khi hoàn tất:

✅ User xem Lịch Làm Việc
✅ Bảng có 4 hàng (Sáng, Chiều, Tối, Phân công)
✅ Các ô lịch được populate từ database
✅ Ngày 31/12 hiển thị đúng
✅ Thống kê "Ca tuần này" cập nhật đúng

---

## 📞 Support

Nếu vẫn có vấn đề:

1. **Check logs:**

   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Check DevTools Console:**

   - Có error javascript không?
   - Request/response format đúng không?

3. **Check Database:**

   ```sql
   SELECT * FROM lich_lam_viec LIMIT 10;
   ```

4. **Check Routes:**
   ```bash
   php artisan route:list | grep lich-lam-viec
   ```

---

## 📝 Files Reference

| File                                          | Status     | Notes              |
| --------------------------------------------- | ---------- | ------------------ |
| `src/services/lichLamViecService.js`          | ✅ Fixed   | Endpoints updated  |
| `routes/api.php`                              | ⏳ Pending | Need to add routes |
| `src/components/Doctor/LichLamViec/index.vue` | ✅ OK      | No changes needed  |

---

**🎉 Khi hoàn tất, lịch sẽ hiển thị ngay!**
