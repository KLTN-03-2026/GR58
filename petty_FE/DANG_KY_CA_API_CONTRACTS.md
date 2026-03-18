# 📡 API Contracts - Đăng ký Ca Làm việc

## 1️⃣ GET /lich-lam-viec - Fetch Available Shifts

### Request

```http
GET /lich-lam-viec?tu_ngay=2025-12-09&den_ngay=2025-12-15&per_page=50
```

### Query Parameters

| Parameter | Type          | Required | Example    | Description           |
| --------- | ------------- | -------- | ---------- | --------------------- |
| tu_ngay   | string (date) | ❌       | 2025-12-09 | Start date YYYY-MM-DD |
| den_ngay  | string (date) | ❌       | 2025-12-15 | End date YYYY-MM-DD   |
| per_page  | integer       | ❌       | 50         | Results per page      |

### Response (Success - 200)

```json
{
  "success": true,
  "message": "Lấy danh sách lịch làm việc thành công",
  "data": {
    "data": [
      {
        "id": 1,
        "ngay_lam": "2025-12-10",
        "thoi_gian_truc": "ca_sang",
        "phong_truc": "Phòng khám 1",
        "ghi_chu": "Phòng khám thường",
        "created_at": "2025-12-09T10:00:00",
        "updated_at": "2025-12-09T10:00:00"
      },
      {
        "id": 2,
        "ngay_lam": "2025-12-10",
        "thoi_gian_truc": "ca_chieu",
        "phong_truc": "Phòng khám 2",
        "ghi_chu": "Phòng khám ngoài",
        "created_at": "2025-12-09T10:00:00",
        "updated_at": "2025-12-09T10:00:00"
      }
    ],
    "total": 10,
    "per_page": 50,
    "current_page": 1,
    "last_page": 1
  }
}
```

### Response (Error - 500)

```json
{
  "success": false,
  "message": "Lỗi khi lấy danh sách lịch làm việc: ...",
  "data": null
}
```

### Response Fields

| Field          | Type     | Description                     |
| -------------- | -------- | ------------------------------- |
| id             | integer  | Shift ID                        |
| ngay_lam       | string   | Date YYYY-MM-DD                 |
| thoi_gian_truc | enum     | 'ca_sang', 'ca_chieu', 'ca_toi' |
| phong_truc     | string   | Room name                       |
| ghi_chu        | string   | Notes                           |
| created_at     | datetime | Creation timestamp              |
| updated_at     | datetime | Update timestamp                |

### Thoi Gian Truc Values

| Value    | Name  | Time                     |
| -------- | ----- | ------------------------ |
| ca_sang  | Sáng  | 06:00 - 12:00            |
| ca_chieu | Chiều | 13:00 - 18:00            |
| ca_toi   | Tối   | 19:00 - 07:00 (next day) |

---

## 2️⃣ POST /lich-dang-ky/dang-ky-nhan-vien - Submit Registration

### Request

```http
POST /lich-dang-ky/dang-ky-nhan-vien
Content-Type: application/json
Authorization: Bearer {token}
```

### Request Body

```json
{
  "ngay_gio": "2025-12-10 06:00:00",
  "lich_lam_viec_id": 1,
  "ghi_chu": "Xin phòng khám 1"
}
```

### Request Fields

| Field            | Type     | Required | Example             | Description       |
| ---------------- | -------- | -------- | ------------------- | ----------------- |
| ngay_gio         | datetime | ✅       | 2025-12-10 06:00:00 | Datetime of shift |
| lich_lam_viec_id | integer  | ✅       | 1                   | Shift ID          |
| ghi_chu          | string   | ❌       | "Xin phòng khám 1"  | Notes (optional)  |

### Request Validation

```php
[
  'ngay_gio' => 'required|date',
  'ghi_chu' => 'nullable|string',
  'lich_lam_viec_id' => 'required|exists:lich_lam_viecs,id',
]
```

### Response (Success - 201)

```json
{
  "success": true,
  "message": "Đăng ký lịch làm việc thành công",
  "data": {
    "id": 999,
    "nhan_vien_id": 5,
    "ngay_gio": "2025-12-10 06:00:00",
    "ghi_chu": "Xin phòng khám 1",
    "trang_thai": "da_xac_nhan",
    "admin_id": null,
    "lich_lam_viec_id": 1,
    "khach_hang_id": null,
    "thu_cung_id": null,
    "dich_vu_id": null,
    "created_at": "2025-12-09T11:30:00",
    "updated_at": "2025-12-09T11:30:00",
    "nhan_vien": {
      "id": 5,
      "ho_ten": "Nguyễn Văn A",
      "email": "doctor@example.com",
      "avatar": "https://...",
      "chuc_vu": "Bác sĩ"
    },
    "lich_lam_viec": {
      "id": 1,
      "ngay_lam": "2025-12-10",
      "thoi_gian_truc": "ca_sang",
      "phong_truc": "Phòng khám 1"
    }
  }
}
```

### Response (Validation Error - 422)

```json
{
  "success": false,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "ngay_gio": ["Ngày giờ là bắt buộc"],
    "lich_lam_viec_id": ["Lịch làm việc không tồn tại"]
  }
}
```

### Response (Auth Error - 403)

```json
{
  "success": false,
  "message": "Chỉ nhân viên mới có thể đăng ký lịch làm việc",
  "data": null
}
```

### Response (Server Error - 500)

```json
{
  "success": false,
  "message": "Lỗi khi đăng ký lịch: ...",
  "data": null
}
```

### Response Fields

| Field        | Type     | Notes                     |
| ------------ | -------- | ------------------------- |
| id           | integer  | Registration ID           |
| nhan_vien_id | integer  | Auto-set from auth user   |
| ngay_gio     | datetime | Shift datetime            |
| trang_thai   | enum     | Auto-set to 'da_xac_nhan' |
| ghi_chu      | string   | User notes                |
| created_at   | datetime | Timestamp                 |
| updated_at   | datetime | Timestamp                 |

### Auto-set Fields by Backend

```javascript
{
  "nhan_vien_id": auth_user.id,           // From authenticated user
  "trang_thai": "da_xac_nhan",            // Auto confirm
  "admin_id": null,                        // Not set by user
  "khach_hang_id": null,                   // Not applicable
  "thu_cung_id": null,                     // Not applicable
  "dich_vu_id": null                       // Not applicable
}
```

---

## 🔄 Request/Response Flow

### Successful Flow

```
1. Frontend GET /lich-lam-viec
   ↓ (receive shifts array)

2. User selects shift
   ↓

3. Frontend POST /lich-dang-ky/dang-ky-nhan-vien
   ↓

4. Backend validates
   - User is staff
   - lich_lam_viec_id exists
   - ngay_gio is valid datetime
   ↓

5. Backend creates LichDangKy
   - Auto sets nhan_vien_id
   - Auto sets trang_thai = 'da_xac_nhan'
   ↓

6. Backend returns 201 with created object
   ↓

7. Frontend shows success toast
8. Frontend emits "success" event
9. Parent refreshes schedule data
```

### Error Flow

```
Frontend POST /lich-dang-ky/dang-ky-nhan-vien
   ↓
Backend validation fails (422)
   OR
Backend server error (500)
   OR
Auth error (403)
   ↓
Backend returns error response
   ↓
Frontend catches error
   ↓
Frontend shows error toast
   ↓
Modal stays open for retry
```

---

## 🧪 Testing Examples

### Test 1: Successful Registration

```bash
curl -X POST http://localhost:8000/api/lich-dang-ky/dang-ky-nhan-vien \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "ngay_gio": "2025-12-10 06:00:00",
    "lich_lam_viec_id": 1,
    "ghi_chu": "Xin phòng khám 1"
  }'
```

Expected: 201 with created record

### Test 2: Fetch Shifts

```bash
curl -X GET "http://localhost:8000/api/lich-lam-viec?tu_ngay=2025-12-09&den_ngay=2025-12-15&per_page=50" \
  -H "Authorization: Bearer {token}"
```

Expected: 200 with shifts array

### Test 3: Invalid lich_lam_viec_id

```bash
curl -X POST http://localhost:8000/api/lich-dang-ky/dang-ky-nhan-vien \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "ngay_gio": "2025-12-10 06:00:00",
    "lich_lam_viec_id": 99999
  }'
```

Expected: 422 with validation error

### Test 4: Not Staff User

```bash
# Using customer token instead of staff token
curl -X POST http://localhost:8000/api/lich-dang-ky/dang-ky-nhan-vien \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {customer_token}" \
  -d '{...}'
```

Expected: 403 Forbidden

---

## 📊 Data Types Reference

### DateTime Format

```
Format: YYYY-MM-DD HH:MM:SS
Example: 2025-12-10 06:00:00
```

### Date Format

```
Format: YYYY-MM-DD
Example: 2025-12-10
```

### Enum: thoi_gian_truc

```
ca_sang  → Morning (06:00-12:00)
ca_chieu → Afternoon (13:00-18:00)
ca_toi   → Night (19:00-07:00)
```

### Enum: trang_thai

```
chua_xac_nhan → Pending confirmation
da_xac_nhan   → Confirmed
tu_choi       → Rejected
```

---

## 🔐 Authentication

### Required Header

```
Authorization: Bearer {jwt_token}
```

### Middleware: staff.only

- Checks if user is instance of NhanVien model
- Returns 403 if not staff

### Auto Fields

- `nhan_vien_id` taken from auth user
- Cannot be overridden by client

---

## ⚠️ Error Codes

| Code | Message          | Cause             |
| ---- | ---------------- | ----------------- |
| 200  | Success (GET)    | Valid request     |
| 201  | Created (POST)   | Resource created  |
| 403  | Forbidden        | User is not staff |
| 422  | Validation Error | Invalid data      |
| 500  | Server Error     | Backend exception |

---

## 📈 Performance Notes

- No heavy joins in response
- Uses pagination (per_page parameter)
- Indexes on: nhan_vien_id, ngay_lam, trang_thai
- Consider caching shifts list

---

## 🔗 Related Endpoints

- `GET /lich-dang-ky/lich-cua-toi` - Get my registrations
- `GET /lich-dang-ky/chua-xac-nhan` - Pending registrations (admin)
- `GET /lich-dang-ky/da-xac-nhan` - Confirmed registrations (admin)
- `PUT /lich-dang-ky/{id}/xac-nhan` - Confirm registration (admin)
- `PUT /lich-dang-ky/{id}/tu-choi` - Reject registration (admin)

---

**Last Updated**: 2025-12-11
**Status**: ✅ Production Ready
