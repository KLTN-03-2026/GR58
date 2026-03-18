# 🔧 BACKEND - API Endpoint Phân Công Bác Sĩ

## Cần thêm vào Backend

### 1. **NhanVienController** - Filter Doctors

```php
/**
 * Get list of doctors only
 */
public function getDoctors()
{
    // Filter employees by role/chuc_vu = 'bac_si' or 'doctor'
    $doctors = NhanVien::where(function($query) {
        $query->where('vai_tro', 'bac_si')
              ->orWhere('vai_tro', 'doctor')
              ->orWhere('chuc_vu', 'Bác sĩ');
    })->get();

    return response()->json([
        'status' => true,
        'data' => $doctors,
    ]);
}
```

### 2. **LichHenController** - Update Appointment with Doctor

**Thêm method mới:**

```php
/**
 * Assign a doctor (nhan_vien) to an appointment
 */
public function assignDoctor(Request $request, LichHen $lichHen)
{
    $validated = $request->validate([
        'nhan_vien_id' => ['required', 'exists:nhan_vien,id'],
    ]);

    $lichHen->nhan_vien_id = $validated['nhan_vien_id'];
    $lichHen->save();

    $payload = $this->transformData($lichHen->load(['thuCung', 'dichVu', 'nhanVien', 'khachHang']));

    return response()->json([
        'status' => true,
        'message' => 'Phân công bác sĩ thành công',
        'data' => $payload,
    ]);
}
```

**Hoặc cập nhật PATCH endpoint:**

```php
/**
 * Update appointment (flexible update)
 */
public function update(Request $request, LichHen $lichHen)
{
    $validated = $request->validate([
        'nhan_vien_id' => ['nullable', 'exists:nhan_vien,id'],
        'trang_thai' => ['nullable', 'string'],
        'ngay_gio' => ['nullable', 'date'],
    ]);

    $lichHen->update($validated);

    $payload = $this->transformData($lichHen->fresh()->load(['thuCung', 'dichVu', 'nhanVien', 'khachHang']));

    return response()->json([
        'status' => true,
        'message' => 'Cập nhật lịch hẹn thành công',
        'data' => $payload,
    ]);
}
```

### 3. **Routes - Thêm vào `routes/api.php`**

```php
// Doctors
Route::get('/nhan-vien/doctors', [NhanVienController::class, 'getDoctors']);

// Appointments
Route::patch('/lich-hen/{lichHen}', [LichHenController::class, 'update']);
Route::post('/lich-hen/{lichHen}/assign-doctor', [LichHenController::class, 'assignDoctor']);
```

---

## 📊 Data Structure

### NhanVien Model

```php
{
  "id": 1,
  "ho_ten": "Nguyễn Văn A",
  "email": "doctor@clinic.com",
  "vai_tro": "bac_si",        // ← Filter by this
  "chuc_vu": "Bác sĩ",
  "trang_thai": "hoat_dong"   // ← Show as available/unavailable
}
```

### Filter Conditions

```php
// Điều kiện lọc bác sĩ
vai_tro = 'bac_si'  OR  vai_tro = 'doctor'  OR  chuc_vu = 'Bác sĩ'

// Trạng thái hiển thị
trang_thai = 'hoat_dong'     → Available (Đang trực)
trang_thai = 'da_khoa'       → Unavailable (Không trực)
trang_thai = 'nghi_viec'     → Unavailable (Nghỉ việc)
```

---

## 🔗 API Endpoints

| Method | Endpoint                       | Purpose                        |
| ------ | ------------------------------ | ------------------------------ |
| GET    | `/nhan-vien`                   | Lấy tất cả nhân viên           |
| GET    | `/nhan-vien/doctors`           | Lấy chỉ bác sĩ                 |
| PATCH  | `/lich-hen/{id}`               | Cập nhật lịch hẹn (gán bác sĩ) |
| POST   | `/lich-hen/{id}/assign-doctor` | Gán bác sĩ (alternative)       |

---

## ✅ Kiểm tra

1. **Test Endpoint**:

```bash
GET http://localhost:8000/api/nhan-vien/doctors
# Response: Array of doctors with vai_tro = 'bac_si'
```

2. **Assign Doctor**:

```bash
PATCH http://localhost:8000/api/lich-hen/1
Content-Type: application/json
{
  "nhan_vien_id": 5
}
```

3. **Response**:

```json
{
  "status": true,
  "message": "Phân công bác sĩ thành công",
  "data": {
    "id": 1,
    "nhan_vien_id": 5,
    "nhan_vien": {
      "id": 5,
      "ho_ten": "Nguyễn Văn A",
      "vai_tro": "bac_si"
    }
  }
}
```
