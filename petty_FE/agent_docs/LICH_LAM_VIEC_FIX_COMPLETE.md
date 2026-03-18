# 🔧 HƯỚNG DẪN SỬA LỖI LỊCH LÀM VIỆC BÁC SĨ

## 📋 Tổng quan

Khi admin phân công ca làm việc cho bác sĩ, lịch sẽ tự động hiển thị trong giao diện của bác sĩ.

---

## 🎯 Vấn đề đã sửa

**Trước khi sửa:** Frontend gọi sai endpoint `/lich-lam-viec` thay vì `/lich-lam-viec/cua-toi`
**Sau khi sửa:** Frontend gọi đúng endpoint theo backend routes

---

## 🔄 FLOW HOẠT ĐỘNG

### 1️⃣ **Admin phân công ca làm việc**

```
Admin (QuanLyLichLamViec/index.vue)
    ↓
POST /lich-lam-viec
    ↓
LichLamViecController@store
    ↓
Tạo record trong bảng lich_lam_viec
    {
        nhan_vien_id: ID bác sĩ được phân công
        ngay_lam: Ngày làm việc (YYYY-MM-DD)
        thoi_gian_truc: ca_sang | ca_chieu | ca_toi
        phong_truc: Tên phòng trực
        ghi_chu: Ghi chú (nếu có)
    }
```

### 2️⃣ **Bác sĩ xem lịch của mình**

```
Doctor (LichLamViec/index.vue)
    ↓
GET /lich-lam-viec/cua-toi
    ↓
LichLamViecController@getMySchedule
    ↓
Trả về lịch làm việc và lịch hẹn của bác sĩ đang đăng nhập
    {
        status: true,
        data: {
            nhan_vien: {...},
            period: { start_date, end_date },
            schedule: [
                {
                    date: "2025-12-12",
                    lich_lam_viec: [
                        {
                            id: 1,
                            thoi_gian_truc: "ca_sang",
                            ghi_chu: "..."
                        }
                    ],
                    lich_hen: [...]
                }
            ],
            statistics: {...}
        }
    }
```

### 3️⃣ **Bác sĩ xem lịch hôm nay**

```
GET /lich-lam-viec/cua-toi/hom-nay
    ↓
LichLamViecController@getMyTodaySchedule
    ↓
Trả về lịch làm việc và lịch hẹn hôm nay
    {
        status: true,
        data: {
            date: "2025-12-12",
            nhan_vien: {...},
            lich_lam_viec: [...],
            lich_hen: [...],
            statistics: {
                work_shifts: 2,
                appointments: 5,
                pending_appointments: 1,
                confirmed_appointments: 4
            }
        }
    }
```

---

## 📂 FILES ĐÃ SỬA

### 1. **src/services/lichLamViecService.js**

```javascript
// ✅ TRƯỚC (SAI)
export const getMySchedule = async (startDate, endDate) => {
  const response = await api.get("/lich-lam-viec", {
    params: { start_date: startDate, end_date: endDate },
  });
  return response.data;
};

// ✅ SAU (ĐÚNG)
export const getMySchedule = async (startDate, endDate) => {
  const response = await api.get("/lich-lam-viec/cua-toi", {
    params: { start_date: startDate, end_date: endDate },
  });
  return response.data;
};
```

### 2. **Các endpoint đã cập nhật**

| Chức năng                                        | Endpoint                         | Method | Middleware                                |
| ------------------------------------------------ | -------------------------------- | ------ | ----------------------------------------- |
| Bác sĩ xem lịch của mình (theo khoảng thời gian) | `/lich-lam-viec/cua-toi`         | GET    | auth:sanctum                              |
| Bác sĩ xem lịch hôm nay                          | `/lich-lam-viec/cua-toi/hom-nay` | GET    | auth:sanctum                              |
| Admin tạo ca làm việc                            | `/lich-lam-viec`                 | POST   | staff.only + permission:lich_lam_viec_tao |
| Admin xem tất cả lịch làm việc                   | `/lich-lam-viec`                 | GET    | staff.only + permission:lich_lam_viec_xem |
| Admin xem lịch của bác sĩ cụ thể                 | `/lich-lam-viec/bac-si/{id}`     | GET    | staff.only + permission:lich_lam_viec_xem |
| Admin xem lịch hôm nay của tất cả bác sĩ         | `/lich-lam-viec/hom-nay`         | GET    | staff.only + permission:lich_lam_viec_xem |

---

## 🧪 KIỂM TRA

### Test Case 1: Admin phân công ca làm việc

```bash
# 1. Admin tạo ca làm việc cho bác sĩ
POST /api/lich-lam-viec
{
    "nhan_vien_id": 5,
    "ngay_lam": "2025-12-12",
    "thoi_gian_truc": "ca_sang",
    "phong_truc": "Phòng khám tổng quát"
}

# 2. Kết quả mong đợi
{
    "status": true,
    "message": "Lịch làm việc đã được tạo.",
    "data": {
        "id": 123,
        "nhan_vien_id": 5,
        "ngay_lam": "2025-12-12",
        "thoi_gian_truc": "ca_sang",
        "phong_truc": "Phòng khám tổng quát",
        ...
    }
}
```

### Test Case 2: Bác sĩ xem lịch

```bash
# Bác sĩ (id=5) đăng nhập và gọi API
GET /api/lich-lam-viec/cua-toi?start_date=2025-12-09&end_date=2025-12-15

# Kết quả: Phải thấy ca làm việc vừa được admin phân công
{
    "status": true,
    "data": {
        "schedule": [
            {
                "date": "2025-12-12",
                "lich_lam_viec": [
                    {
                        "id": 123,
                        "thoi_gian_truc": "ca_sang",
                        "ghi_chu": null
                    }
                ],
                "lich_hen": []
            }
        ]
    }
}
```

### Test Case 3: Bác sĩ xem lịch hôm nay

```bash
# Bác sĩ xem lịch hôm nay (ngày 12/12/2025)
GET /api/lich-lam-viec/cua-toi/hom-nay

# Kết quả: Phải thấy ca làm việc hôm nay
{
    "status": true,
    "data": {
        "date": "2025-12-12",
        "lich_lam_viec": [
            {
                "id": 123,
                "thoi_gian_truc": "ca_sang",
                "ghi_chu": null
            }
        ],
        "lich_hen": [],
        "statistics": {
            "work_shifts": 1,
            "appointments": 0
        }
    }
}
```

---

## 📊 CẤU TRÚC DỮ LIỆU

### Response từ getMySchedule (`/lich-lam-viec/cua-toi`)

```javascript
{
    status: true,
    data: {
        nhan_vien: {
            id: 5,
            full_name: "Bác sĩ Nguyễn Văn A",
            email: "nguyenvana@email.com",
            vai_tro: "bac_si",
            chuc_danh: "Bác sĩ thú y"
        },
        period: {
            start_date: "2025-12-09",
            end_date: "2025-12-15"
        },
        schedule: [
            {
                date: "2025-12-12",
                lich_lam_viec: [
                    {
                        id: 123,
                        thoi_gian_truc: "ca_sang",  // ca_sang | ca_chieu | ca_toi
                        ghi_chu: "Khám tổng quát"
                    }
                ],
                lich_hen: [
                    {
                        id: 456,
                        ngay_gio: "2025-12-12 14:00:00",
                        trang_thai: "confirmed",
                        khach_hang: "Nguyễn Thị B",
                        thu_cung: "Mèo Miu",
                        dich_vu: "Tiêm phòng",
                        ghi_chu: "Lần đầu tiêm"
                    }
                ]
            }
        ],
        statistics: {
            total_work_days: 5,      // Tổng số ngày làm việc
            total_appointments: 12   // Tổng số lịch hẹn
        }
    }
}
```

### Response từ getMyTodaySchedule (`/lich-lam-viec/cua-toi/hom-nay`)

```javascript
{
    status: true,
    data: {
        date: "2025-12-12",
        nhan_vien: {
            id: 5,
            full_name: "Bác sĩ Nguyễn Văn A",
            vai_tro: "bac_si",
            chuc_danh: "Bác sĩ thú y"
        },
        lich_lam_viec: [
            {
                id: 123,
                thoi_gian_truc: "ca_sang",
                ghi_chu: "Khám tổng quát"
            },
            {
                id: 124,
                thoi_gian_truc: "ca_chieu",
                ghi_chu: "Phẫu thuật"
            }
        ],
        lich_hen: [
            {
                id: 456,
                ngay_gio: "2025-12-12 14:00:00",
                trang_thai: "confirmed",
                khach_hang: "Nguyễn Thị B",
                khach_hang_phone: "0123456789",
                thu_cung: "Mèo Miu",
                dich_vu: "Tiêm phòng",
                ghi_chu: "Lần đầu tiêm"
            }
        ],
        statistics: {
            work_shifts: 2,              // Số ca làm việc hôm nay
            appointments: 1,             // Tổng số lịch hẹn hôm nay
            pending_appointments: 0,     // Lịch hẹn chờ xác nhận
            confirmed_appointments: 1    // Lịch hẹn đã xác nhận
        }
    }
}
```

---

## 🎨 HIỂN THỊ TRÊN UI

### **Trong Doctor/LichLamViec/index.vue**

Dữ liệu từ API sẽ được map vào `timeSlots`:

```javascript
timeSlots.value = [
    {
        name: "Ca sáng",
        time: "07:00 - 11:00",
        schedule: [null, {...}, null, null, null, null, null]
        //          T2    T3    T4    T5    T6    T7    CN
    },
    {
        name: "Ca chiều",
        time: "13:00 - 17:00",
        schedule: [null, null, {...}, null, null, null, null]
    },
    {
        name: "Ca tối",
        time: "18:00 - 22:00",
        schedule: [null, null, null, null, null, null, null]
    }
]
```

Mỗi cell có ca làm việc sẽ hiển thị:

- 🏥 Tên phòng trực
- ⏰ Thời gian ca
- 👥 Số lượng lịch hẹn
- ✅ Trạng thái (upcoming/in-progress/completed)

---

## ⚠️ LƯU Ý QUAN TRỌNG

### 1. Authentication

- Endpoint `/lich-lam-viec/cua-toi` yêu cầu `auth:sanctum`
- User phải đăng nhập với role `NhanVien` hoặc `Admin`
- Nếu là Admin, phải truyền thêm `nhan_vien_id` query parameter

### 2. Date Format

- **Backend nhận:** `YYYY-MM-DD` (ví dụ: `2025-12-12`)
- **Backend trả về datetime:** `YYYY-MM-DD HH:mm:ss`
- **Frontend hiển thị:** `DD/MM/YYYY`

### 3. Shift Types (thoi_gian_truc)

- `ca_sang` - Ca sáng (07:00 - 11:00)
- `ca_chieu` - Ca chiều (13:00 - 17:00)
- `ca_toi` - Ca tối (18:00 - 22:00)
- `ca_dang_ky` - Ca đăng ký (bác sĩ tự đăng ký, chờ admin duyệt)

### 4. Appointment Status

- `pending` / `cho_xac_nhan` - Chờ xác nhận
- `confirmed` / `da_xac_nhan` - Đã xác nhận
- `in-progress` / `dang_xu_ly` - Đang xử lý
- `completed` / `hoan_thanh` - Hoàn thành
- `cancelled` / `da_huy` - Đã hủy

---

## 🚀 CẬP NHẬT REALTIME

### Khi admin tạo ca mới

1. Admin tạo ca làm việc → `POST /lich-lam-viec`
2. Backend lưu vào database
3. Backend gửi notification cho bác sĩ được phân công
4. Bác sĩ refresh trang hoặc component tự động re-fetch sau 30s
5. Lịch mới xuất hiện trên calendar của bác sĩ

### Auto Refresh

```javascript
// Trong Doctor/LichLamViec/index.vue
onMounted(() => {
  fetchScheduleData();

  // Auto refresh mỗi 5 phút
  setInterval(() => {
    fetchScheduleData();
  }, 300000); // 300000ms = 5 phút
});
```

---

## 🔍 DEBUG

### Nếu lịch không hiển thị

**1. Kiểm tra Console**

```javascript
console.log("Schedule data:", scheduleData.value);
console.log("Time slots:", timeSlots.value);
```

**2. Kiểm tra Network Tab**

- Request URL có đúng `/lich-lam-viec/cua-toi` không?
- Response có trả về `status: true` không?
- Response có chứa `schedule` array không?

**3. Kiểm tra Authorization**

- Token có hợp lệ không?
- User có role `NhanVien` hoặc `Admin` không?

**4. Kiểm tra Database**

```sql
SELECT * FROM lich_lam_viec
WHERE nhan_vien_id = 5
AND ngay_lam BETWEEN '2025-12-09' AND '2025-12-15';
```

---

## ✅ CHECKLIST HOÀN THÀNH

- [x] Sửa endpoint `/lich-lam-viec` → `/lich-lam-viec/cua-toi`
- [x] Sửa endpoint `/lich-lam-viec/hom-nay` → `/lich-lam-viec/cua-toi/hom-nay`
- [x] Cập nhật service `getMySchedule()`
- [x] Cập nhật service `getMyTodaySchedule()`
- [x] Giữ nguyên service `getTodaySchedule()` cho admin
- [x] Tạo tài liệu hướng dẫn

---

## 📞 HỖ TRỢ

Nếu gặp vấn đề, kiểm tra:

1. Backend routes trong `api.php`
2. Frontend service trong `lichLamViecService.js`
3. Component logic trong `Doctor/LichLamViec/index.vue`
4. API response format trong `LichLamViecController.php`

---

**Tạo bởi:** GitHub Copilot  
**Ngày:** 12/12/2025  
**Version:** 1.0.0
