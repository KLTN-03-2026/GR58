# 🔍 DEBUG - Lấy Tên Bác Sĩ Không Thành Công

## Vấn đề

Modal "Phân công Bác sĩ" không hiển thị bác sĩ nào hoặc hiển thị lỗi.

## 🔧 Cách Debug

### 1. Mở DevTools Console

- Nhấn `F12` → Tab **Console**

### 2. Click "Chưa gán" để mở modal

- Quan sát console logs

### 3. Tìm các log sau:

```javascript
// Log 1: API Response
"All employees from API:"[Array];

// Log 2: Filtered doctors
"Filtered doctors:"[Array];

// Log 3: Lỗi (nếu có)
("Không tìm thấy bác sĩ. Tổng nhân viên: X. Kiểm tra vai_tro hoặc chuc_vu...");
```

---

## 📊 Các Trường Hợp

### ✅ Trường hợp 1: Có bác sĩ

```
Console log:
All employees from API: [5 items]
✓ Found doctor: Nguyễn Văn A
✓ Found doctor: Phạm Thị B
Filtered doctors: [2 items]

→ Modal sẽ hiển thị 2 bác sĩ
```

### ❌ Trường hợp 2: Không có bác sĩ

```
Console log:
All employees from API: [3 items]
Filtered doctors: []

Error message:
"Không tìm thấy bác sĩ. Tổng nhân viên: 3. Kiểm tra vai_tro hoặc chuc_vu..."

→ Nguyên nhân: Không có nhân viên nào có vai_tro='bac_si'
```

### ❌ Trường hợp 3: Không có API response

```
Console error:
"Error fetching doctors: ..."

→ Nguyên nhân: Endpoint /nhan-vien không hoạt động
```

---

## 🔎 Kiểm Tra Database

Chạy lệnh SQL để xem dữ liệu nhân viên:

```sql
SELECT id, ho_ten, vai_tro, chuc_vu, trang_thai FROM nhan_vien;

-- Nên thấy:
-- id | ho_ten | vai_tro | chuc_vu | trang_thai
-- 1  | Nguyễn Văn A | bac_si | Bác sĩ | hoat_dong
-- 2  | Phạm Thị B | doctor | Bác sĩ | hoat_dong
```

---

## 🛠️ Các Giá Trị Có Thể

### vai_tro (Vai trò)

```
'bac_si'    ← Tìm cái này
'doctor'    ← Hoặc cái này
'bacsi'     ← Hoặc viết không dấu
'nv'        ← Khác (skip)
'admin'     ← Khác (skip)
```

### chuc_vu (Chức vụ)

```
'Bác sĩ'    ← Tìm cái này
'bac_si'    ← Hoặc viết không dấu
'Nhân viên' ← Khác (skip)
```

### trang_thai (Trạng thái)

```
'hoat_dong'    → Available (Xanh)
'da_khoa'      → Unavailable (Xám)
'nghi_viec'    → Unavailable (Xám)
```

---

## ✅ Giải Pháp

### Nếu không có bác sĩ:

**Option 1: Thêm bác sĩ vào DB**

```sql
INSERT INTO nhan_vien (ho_ten, vai_tro, chuc_vu, trang_thai, email, password, ...)
VALUES
('Nguyễn Văn A', 'bac_si', 'Bác sĩ', 'hoat_dong', 'a@clinic.com', ...),
('Phạm Thị B', 'bac_si', 'Bác sĩ', 'hoat_dong', 'b@clinic.com', ...);
```

**Option 2: Update existing employees**

```sql
UPDATE nhan_vien
SET vai_tro = 'bac_si', chuc_vu = 'Bác sĩ', trang_thai = 'hoat_dong'
WHERE id IN (1, 2, 3);  -- IDs của bác sĩ
```

---

## 📋 Checklist

- [ ] Mở DevTools (F12)
- [ ] Click modal "Phân công Bác sĩ"
- [ ] Xem console logs
- [ ] Kiểm tra "vai_tro" & "chuc_vu" của nhân viên trong console
- [ ] Chạy SQL query để xác nhận dữ liệu DB
- [ ] Cập nhật DB nếu cần
- [ ] Refresh FE, mở lại modal
- [ ] Bác sĩ sẽ hiển thị ✅

---

## 📱 Network Debugging

Nếu vẫn lỗi:

1. Mở DevTools → **Network** tab
2. Click modal "Phân công Bác sĩ"
3. Tìm request `GET /nhan-vien`
4. Click vào → **Response** tab
5. Xem dữ liệu trả về:
   - Có `vai_tro` field không?
   - Giá trị `vai_tro` là gì?
   - Có `ho_ten` không?

---

## 💡 Tip

Filter logic hiện tại kiểm tra:

```javascript
vai_tro === "bac_si" ||
  vai_tro === "doctor" ||
  vai_tro === "bacsi" ||
  chuc_vu === "Bác sĩ" ||
  chuc_vu === "bac_si";
```

Nếu database sử dụng giá trị khác, hãy báo cáo console log để tôi update filter!
