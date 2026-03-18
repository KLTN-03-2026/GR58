# Hướng Dẫn Quản Lý Lịch Làm Việc Bác Sĩ

## 📋 Tổng Quan

Hệ thống cung cấp các API để admin xem và quản lý:

-   **Lịch làm việc** của bác sĩ/nhân viên (ca trực)
-   **Lịch hẹn** của bệnh nhân với bác sĩ cụ thể
-   **Thống kê** tổng hợp theo ngày/tháng

---

## 🔐 Yêu Cầu Quyền

Tất cả API này yêu cầu:

-   ✅ Đăng nhập (`auth:sanctum`)
-   ✅ Quyền `lich_lam_viec_xem`

---

## 📍 API Endpoints

### 1. Xem Lịch Làm Việc và Lịch Hẹn của Một Bác Sĩ

**Endpoint:** `GET /api/lich-lam-viec/bac-si/{nhanVienId}`

**Mô tả:** Lấy tất cả lịch làm việc và lịch hẹn của một bác sĩ cụ thể trong khoảng thời gian

**Headers:**

```
Authorization: Bearer {token}
```

**Query Parameters:**

```
start_date: YYYY-MM-DD (optional, mặc định: đầu tháng hiện tại)
end_date: YYYY-MM-DD (optional, mặc định: cuối tháng hiện tại)
```

**Request Example:**

```bash
GET /api/lich-lam-viec/bac-si/5?start_date=2025-12-01&end_date=2025-12-31
Authorization: Bearer {token}
```

**Response Success (200):**

```json
{
    "status": true,
    "data": {
        "nhan_vien": {
            "id": 5,
            "full_name": "Bác sĩ Nguyễn Văn A",
            "email": "bacsi@example.com",
            "vai_tro": "bac_si",
            "chuc_danh": "Bác sĩ Thú Y"
        },
        "period": {
            "start_date": "2025-12-01",
            "end_date": "2025-12-31"
        },
        "schedule": [
            {
                "date": "2025-12-07",
                "lich_lam_viec": [
                    {
                        "id": 1,
                        "thoi_gian_truc": "ca_sang",
                        "ghi_chu": "Trực khoa nội"
                    },
                    {
                        "id": 2,
                        "thoi_gian_truc": "ca_chieu",
                        "ghi_chu": null
                    }
                ],
                "lich_hen": [
                    {
                        "id": 10,
                        "ngay_gio": "2025-12-07 09:00:00",
                        "trang_thai": "da_xac_nhan",
                        "khach_hang": "Trần Thị B",
                        "thu_cung": "Chó Alaska - Max",
                        "dich_vu": "Khám tổng quát",
                        "ghi_chu": "Chó bị tiêu chảy"
                    },
                    {
                        "id": 11,
                        "ngay_gio": "2025-12-07 10:30:00",
                        "trang_thai": "cho_xac_nhan",
                        "khach_hang": "Lê Văn C",
                        "thu_cung": "Mèo Ba Tư - Miu",
                        "dich_vu": "Tiêm phòng",
                        "ghi_chu": null
                    }
                ]
            },
            {
                "date": "2025-12-08",
                "lich_lam_viec": [
                    {
                        "id": 3,
                        "thoi_gian_truc": "ca_toi",
                        "ghi_chu": "Trực cấp cứu"
                    }
                ],
                "lich_hen": [
                    {
                        "id": 12,
                        "ngay_gio": "2025-12-08 19:00:00",
                        "trang_thai": "da_xac_nhan",
                        "khach_hang": "Phạm Thị D",
                        "thu_cung": "Chó Poodle - Béo",
                        "dich_vu": "Phẫu thuật",
                        "ghi_chu": "Phẫu thuật cắt polyp"
                    }
                ]
            }
        ],
        "statistics": {
            "total_work_days": 3,
            "total_appointments": 3
        }
    }
}
```

**Response Error (404):**

```json
{
    "message": "No query results for model [App\\Models\\NhanVien] 999"
}
```

---

### 2. Xem Lịch Làm Việc Hôm Nay (Tất Cả Nhân Viên)

**Endpoint:** `GET /api/lich-lam-viec/hom-nay`

**Mô tả:** Lấy danh sách tất cả nhân viên làm việc trong ngày hôm nay (hoặc ngày được chỉ định)

**Headers:**

```
Authorization: Bearer {token}
```

**Query Parameters:**

```
date: YYYY-MM-DD (optional, mặc định: hôm nay)
```

**Request Example:**

```bash
GET /api/lich-lam-viec/hom-nay?date=2025-12-07
Authorization: Bearer {token}
```

**Response Success (200):**

```json
{
    "status": true,
    "data": {
        "date": "2025-12-07",
        "employees": [
            {
                "nhan_vien": {
                    "id": 5,
                    "full_name": "Bác sĩ Nguyễn Văn A",
                    "email": "bacsi@example.com",
                    "vai_tro": "bac_si",
                    "chuc_danh": "Bác sĩ Thú Y"
                },
                "lich_lam_viec": [
                    {
                        "id": 1,
                        "thoi_gian_truc": "ca_sang",
                        "ghi_chu": "Trực khoa nội"
                    },
                    {
                        "id": 2,
                        "thoi_gian_truc": "ca_chieu",
                        "ghi_chu": null
                    }
                ],
                "lich_hen_count": 2
            },
            {
                "nhan_vien": {
                    "id": 8,
                    "full_name": "Điều dưỡng Trần Thị B",
                    "email": "ytab@example.com",
                    "vai_tro": "dieu_duong",
                    "chuc_danh": "Điều dưỡng trưởng"
                },
                "lich_lam_viec": [
                    {
                        "id": 4,
                        "thoi_gian_truc": "ca_sang",
                        "ghi_chu": "Hỗ trợ bác sĩ"
                    }
                ],
                "lich_hen_count": 0
            }
        ],
        "total_employees": 2
    }
}
```

---

### 3. Danh Sách Lịch Làm Việc (Có Filter)

**Endpoint:** `GET /api/lich-lam-viec`

**Mô tả:** Lấy danh sách lịch làm việc với các bộ lọc

**Headers:**

```
Authorization: Bearer {token}
```

**Query Parameters:**

```
ngay_lam: YYYY-MM-DD (optional)
nhan_vien_id: số (optional)
thoi_gian_truc: ca_sang|ca_chieu|ca_toi (optional)
per_page: số (optional, mặc định: 20)
```

**Request Example:**

```bash
GET /api/lich-lam-viec?nhan_vien_id=5&ngay_lam=2025-12-07
Authorization: Bearer {token}
```

**Response Success (200):**

```json
{
    "status": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "nhan_vien_id": 5,
                "ngay_lam": "2025-12-07",
                "thoi_gian_truc": "ca_sang",
                "ghi_chu": "Trực khoa nội",
                "created_at": "2025-12-01T10:00:00.000000Z",
                "updated_at": "2025-12-01T10:00:00.000000Z",
                "nhan_vien": {
                    "id": 5,
                    "full_name": "Bác sĩ Nguyễn Văn A",
                    "email": "bacsi@example.com",
                    "vai_tro": "bac_si"
                }
            }
        ],
        "total": 1,
        "per_page": 20,
        "last_page": 1
    }
}
```

---

## 📊 Use Cases

### Use Case 1: Admin Xem Lịch Bác Sĩ Trong Tháng

```bash
# Lấy lịch làm việc và lịch hẹn của bác sĩ ID=5 trong tháng 12/2025
GET /api/lich-lam-viec/bac-si/5?start_date=2025-12-01&end_date=2025-12-31
Authorization: Bearer {admin_token}

# Kết quả:
# - Hiển thị tất cả ngày bác sĩ có lịch làm việc
# - Mỗi ngày hiển thị: ca trực + danh sách lịch hẹn
# - Thống kê tổng số ngày làm việc và số lịch hẹn
```

### Use Case 2: Xem Nhân Viên Trực Hôm Nay

```bash
# Lấy danh sách tất cả nhân viên làm việc hôm nay
GET /api/lich-lam-viec/hom-nay
Authorization: Bearer {admin_token}

# Kết quả:
# - Danh sách nhân viên đang trực hôm nay
# - Ca trực của mỗi người
# - Số lượng lịch hẹn của mỗi người
```

### Use Case 3: Tìm Bác Sĩ Trực Ca Sáng

```bash
# Lọc lịch làm việc ca sáng ngày 07/12/2025
GET /api/lich-lam-viec?ngay_lam=2025-12-07&thoi_gian_truc=ca_sang
Authorization: Bearer {admin_token}

# Kết quả:
# - Danh sách bác sĩ trực ca sáng
# - Thông tin chi tiết từng người
```

---

## 🎨 Hiển Thị Trên Frontend

### Dashboard Admin - Lịch Bác Sĩ

```javascript
// Lấy lịch của bác sĩ
const response = await fetch(
    "/api/lich-lam-viec/bac-si/5?start_date=2025-12-01&end_date=2025-12-31",
    {
        headers: {
            Authorization: `Bearer ${token}`,
        },
    }
);

const data = await response.json();

// Hiển thị trên calendar
data.data.schedule.forEach((day) => {
    // day.date: "2025-12-07"
    // day.lich_lam_viec: [{ thoi_gian_truc: "ca_sang", ... }]
    // day.lich_hen: [{ ngay_gio: "...", khach_hang: "...", ... }]

    renderCalendarDay(day);
});
```

### Dashboard Admin - Nhân Viên Trực Hôm Nay

```javascript
// Lấy danh sách nhân viên trực hôm nay
const response = await fetch("/api/lich-lam-viec/hom-nay", {
    headers: {
        Authorization: `Bearer ${token}`,
    },
});

const data = await response.json();

// Hiển thị danh sách
data.data.employees.forEach((emp) => {
    console.log(
        `${emp.nhan_vien.full_name} - Ca: ${emp.lich_lam_viec
            .map((l) => l.thoi_gian_truc)
            .join(", ")}`
    );
    console.log(`Số lịch hẹn: ${emp.lich_hen_count}`);
});
```

---

## 📝 Lưu Ý

1. **Phân quyền:**

    - Chỉ Admin hoặc user có quyền `lich_lam_viec_xem` mới xem được
    - Bác sĩ có thể xem lịch của chính mình

2. **Thời gian:**

    - Mặc định lấy dữ liệu trong tháng hiện tại
    - Có thể tùy chỉnh khoảng thời gian bằng `start_date` và `end_date`

3. **Ca trực:**

    - `ca_sang`: Ca sáng (7:00 - 12:00)
    - `ca_chieu`: Ca chiều (13:00 - 17:00)
    - `ca_toi`: Ca tối (17:00 - 22:00)

4. **Trạng thái lịch hẹn:**

    - `cho_xac_nhan`: Chờ xác nhận
    - `da_xac_nhan`: Đã xác nhận
    - `hoan_thanh`: Hoàn thành
    - `da_huy`: Đã hủy

5. **Performance:**
    - API được optimize với eager loading
    - Dữ liệu được nhóm theo ngày để dễ hiển thị
    - Có thống kê tổng hợp

---

## 🚀 Ví Dụ Tích Hợp

### React Component

```jsx
import React, { useEffect, useState } from "react";

function DoctorSchedule({ doctorId }) {
    const [schedule, setSchedule] = useState(null);

    useEffect(() => {
        const fetchSchedule = async () => {
            const res = await fetch(
                `/api/lich-lam-viec/bac-si/${doctorId}?start_date=2025-12-01&end_date=2025-12-31`,
                {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                    },
                }
            );
            const data = await res.json();
            setSchedule(data.data);
        };

        fetchSchedule();
    }, [doctorId]);

    if (!schedule) return <div>Loading...</div>;

    return (
        <div>
            <h2>{schedule.nhan_vien.full_name}</h2>
            <p>Chức danh: {schedule.nhan_vien.chuc_danh}</p>

            <h3>Thống kê:</h3>
            <ul>
                <li>Số ngày làm việc: {schedule.statistics.total_work_days}</li>
                <li>
                    Tổng số lịch hẹn: {schedule.statistics.total_appointments}
                </li>
            </ul>

            <h3>Lịch làm việc:</h3>
            {schedule.schedule.map((day) => (
                <div key={day.date}>
                    <h4>{day.date}</h4>

                    <p>Ca trực:</p>
                    <ul>
                        {day.lich_lam_viec.map((shift) => (
                            <li key={shift.id}>{shift.thoi_gian_truc}</li>
                        ))}
                    </ul>

                    <p>Lịch hẹn ({day.lich_hen.length}):</p>
                    <ul>
                        {day.lich_hen.map((appointment) => (
                            <li key={appointment.id}>
                                {appointment.ngay_gio} -{" "}
                                {appointment.khach_hang} - {appointment.dich_vu}
                            </li>
                        ))}
                    </ul>
                </div>
            ))}
        </div>
    );
}
```

Hệ thống đã sẵn sàng để hiển thị lịch làm việc và lịch hẹn của bác sĩ một cách chi tiết! 🎉
