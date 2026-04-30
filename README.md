<div align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel" />
  <img src="https://upload.wikimedia.org/wikipedia/commons/9/95/Vue.js_Logo_2.svg" width="100" alt="Vue.js" />
</div>

# 🐾 Petty VMCS (Veterinary Management Clinic System) SaaS

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white)](https://vuejs.org/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4+-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net/)

Một hệ thống quản lý phòng khám thú y toàn diện (SaaS) được xây dựng với kiến trúc hiện đại, phân quyền đa lớp, giúp tối ưu hóa quy trình làm việc từ đặt lịch hẹn, khám chữa bệnh, đến quản lý kho thuốc và tài chính.

---

## 🌟 Tổng quan dự án

**Petty VMCS** cung cấp giải pháp chuyển đổi số cho các phòng khám thú y với 4 vai trò chính:

1. **👑 Admin (Quản trị viên):** Quản lý tổng thể hệ thống, nhân sự, tài chính, báo cáo thống kê doanh thu/hiệu suất.
2. **🩺 Bác sĩ:** Trực tiếp khám bệnh, ghi nhận hồ sơ bệnh án, kê đơn thuốc, và theo dõi kết quả cận lâm sàng.
3. **💉 Y tá / Lễ tân:** Điều phối lịch hẹn, check-in, quản lý kho thuốc, nhà cung cấp, và xuất hóa đơn/phiếu chi.
4. **🐶 Khách hàng:** Tự phục vụ với các tính năng: Đặt lịch trực tuyến, theo dõi hồ sơ thú cưng, tra cứu lịch sử khám, thanh toán (MoMo), và tương tác trên cộng đồng/diễn đàn.

---

## 🚀 Công nghệ sử dụng

### 💻 Frontend (`petty_FE`)
- **Framework:** Vue 3 (Composition API)
- **Build Tool:** Vite
- **Styling:** Tailwind CSS (v3.4+)
- **Networking:** Axios
- **Khác:** Vue Router, ApexCharts (cho báo cáo), Lucide Icons, Vue Toastification

### ⚙️ Backend (`petty_BE`)
- **Framework:** Laravel 12
- **Ngôn ngữ:** PHP 8.2
- **Authentication:** Laravel Sanctum
- **Tiện ích:** DomPDF (Xuất PDF hóa đơn, phiếu chi), Socialite (OAuth)
- **Database ORM:** Eloquent ORM

---

## 📂 Cấu trúc Repository

Dự án được chia thành hai phần chính:

```text
Petty/
├── petty_BE/           # Backend (Laravel 12 RESTful API)
│   ├── app/            # Models, Controllers, Services
│   ├── routes/         # api.php (Các API endpoints)
│   ├── database/       # Migrations & Seeders
│   └── public/         # Tài nguyên public
│
├── petty_FE/           # Frontend (Vue 3 + Vite)
│   ├── src/
│   │   ├── components/ # Các components UI dùng chung
│   │   ├── layout/     # Bố cục trang (Sidebar, Header, TopBar)
│   │   ├── router/     # Quản lý routing
│   │   ├── services/   # Các API Client Services (Axios)
│   │   └── views/      # Các trang theo từng Role (admin, doctor, nurse, customer, forum)
│   └── package.json    # Cấu hình thư viện Frontend
│
├── FEATURES.md         # Danh sách chi tiết các chức năng hệ thống
└── TASK_ASSIGNMENT.md  # Phân chia công việc phát triển nhóm
```

---

## 🛠 Hướng dẫn cài đặt & Chạy thử

### 1. Yêu cầu hệ thống
- PHP >= 8.2
- Composer
- Node.js (phiên bản LTS) & npm
- MySQL / MariaDB

### 2. Khởi chạy Backend (`petty_BE`)

```bash
cd petty_BE

# Cài đặt thư viện PHP
composer install

# Copy file cấu hình biến môi trường
cp .env.example .env

# Sinh key ứng dụng
php artisan key:generate

# Chạy migration & tạo dữ liệu mẫu
php artisan migrate --seed

# Khởi động server
php artisan serve
```
*(Server backend mặc định sẽ chạy ở `http://localhost:8000`)*

### 3. Khởi chạy Frontend (`petty_FE`)

```bash
cd petty_FE

# Cài đặt thư viện Node
npm install

# Khởi động môi trường phát triển
npm run dev
```
*(Truy cập ứng dụng frontend trên trình duyệt theo đường dẫn Vite cung cấp)*

---

## 📝 Quy ước & Nguyên tắc Phát triển (Coding Standards)

- **Ngôn ngữ mã nguồn:** Tiếng Anh cho biến, hàm, logic codebase. Tiếng Việt cho comment, tài liệu (docs) và nội dung hiển thị cho người dùng.
- **Frontend:** Chỉ sử dụng Vue 3 Composition API (`<script setup>`).
- **Backend:** Code tuân thủ chuẩn PSR-12, đóng gói logic trong Services/Repositories, luôn sử dụng `DB::transaction()` cho các luồng xử lý phức tạp.
- **Git Flow:** Mỗi thành viên phát triển trên branch riêng (VD: `feature/tên-chức-năng`). Luôn rebase với `main` trước khi push. Hạn chế tối đa việc sửa file router/api chung ngoài phạm vi được phân công.

---
<div align="center">
  <i>Được phát triển với ❤️ cho Đồ án Tốt Nghiệp / KLTN-03-2026/GR58</i>
</div>
