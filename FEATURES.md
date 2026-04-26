# Petty VMCS — Danh sách chức năng theo Role

> Cập nhật: 2026-04-25  
> Dựa trên phân tích toàn bộ codebase (`petty_FE/src/router`, `petty_FE/src/views`, `petty_BE/routes/api.php`)

---

## ADMIN — Quản trị hệ thống

| # | Chức năng | Route | Mô tả |
|---|-----------|-------|-------|
| **Dashboard** ||||
| 1 | Tổng quan hệ thống | `/admin/dashboard` | KPI doanh thu, lịch hẹn, bệnh nhân, nhân viên; bộ lọc thời gian |
| **Quản lý Nhân sự** ||||
| 2 | Tài khoản nhân viên | `/admin/account-management` | Danh sách, thêm, xem chi tiết nhân viên & khách hàng |
| 3 | Đặt mật khẩu nhân viên | *(sub-page)* | Reset / set mật khẩu cho nhân viên |
| 4 | Khóa / mở khóa tài khoản | *(action)* | Khóa hoặc mở khóa tài khoản nhân viên |
| 5 | Lịch làm việc | `/admin/schedule-management` | Xem, tạo, xác nhận đăng ký lịch; phân công ca |
| 6 | Phân lịch tự động | *(action)* | Sinh lịch tháng theo thuật toán 4-slot cyclic |
| 7 | Quản lý đơn nghỉ phép | `/admin/leave-management` | Duyệt / từ chối đơn xin nghỉ, đề xuất người thay thế |
| **Quản lý Dịch vụ** ||||
| 8 | Dịch vụ | `/admin/service-management` | CRUD dịch vụ khám chữa bệnh |
| 9 | Danh mục dịch vụ | *(sub-page)* | CRUD danh mục dịch vụ |
| **Tài chính & Hóa đơn** ||||
| 10 | Danh sách hóa đơn | `/admin/invoice-list` | Xem danh sách, chi tiết phiếu chi |
| 11 | Xuất PDF hóa đơn | *(action)* | Xuất phiếu chi ra PDF |
| 12 | Lịch sử thanh toán | *(sub-page)* | Xem lịch sử và thanh toán thêm |
| **Truyền thông** ||||
| 13 | Quản lý bài viết diễn đàn | `/admin/posts` | CRUD bài viết; tạo, sửa, xóa bài viết |
| 14 | Danh mục bài viết | *(sub-page)* | CRUD danh mục bài viết |
| 15 | Khuyến mãi | `/admin/khuyen-mai` | CRUD khuyến mãi, đổi trạng thái active/inactive |
| **Báo cáo** ||||
| 16 | Báo cáo doanh thu | `/admin/report/revenue` | Doanh thu theo thời gian, từng dịch vụ |
| 17 | Báo cáo hiệu suất | `/admin/report/performance` | Hiệu suất nhân viên, tỷ lệ hoàn thành |
| 18 | Báo cáo kho / kiểm kê | `/admin/report/inventory` | Kiểm kê thuốc, phiếu nhập kho, xuất PDF |
| **Cấu hình** ||||
| 19 | Phân quyền | `/admin/permissions` | Xem, gán quyền cho từng nhân viên |
| 20 | Profile cá nhân | `/admin/profile` | Xem / sửa thông tin admin |

---

## BÁC SĨ — Khám bệnh & Hồ sơ bệnh án

| # | Chức năng | Route | Mô tả |
|---|-----------|-------|-------|
| **Dashboard** ||||
| 1 | Tổng quan bác sĩ | `/doctor/dashboard` | Danh sách bệnh nhân chờ khám, lịch hôm nay |
| 2 | Khám ưu tiên | *(sub-page)* | Đưa bệnh nhân lên ưu tiên khám trước |
| 3 | Đổi ca | *(sub-page)* | Yêu cầu đổi ca làm việc |
| **Lịch khám & Khám bệnh** ||||
| 4 | Danh sách lịch khám | `/doctor/appointments` | Xem bệnh nhân chờ khám, đang khám |
| 5 | Bắt đầu khám bệnh | `/doctor/appointments/examination/:id` | Mở phiếu khám cho bệnh nhân |
| 6 | Nhập kết quả lâm sàng | *(sub-page)* | Ghi kết quả khám, chẩn đoán |
| 7 | Kê đơn thuốc | *(sub-page)* | Tạo / xem đơn thuốc |
| 8 | Đặt lịch tái khám | *(sub-page)* | Tạo lịch hẹn tái khám |
| 9 | Hoàn thành khám | *(action)* | Đánh dấu ca khám hoàn tất |
| **Hồ sơ bệnh án** ||||
| 10 | Danh sách bệnh nhân | `/doctor/medical-records` | Tìm và xem danh sách bệnh nhân |
| 11 | Lịch sử khám | *(sub-page)* | Xem toàn bộ lịch sử khám của một thú cưng |
| 12 | Chi tiết hồ sơ bệnh án | *(sub-page)* | Xem chi tiết từng lần khám |
| **Cận lâm sàng** ||||
| 13 | Danh sách xét nghiệm | `/doctor/clinical-testing` | Xem danh sách xét nghiệm |
| 14 | Kết quả cận lâm sàng | *(sub-page)* | Xem và nhập kết quả xét nghiệm |
| **Kho thuốc** ||||
| 15 | Xem kho thuốc | `/doctor/pharmacy` | Xem danh sách thuốc có sẵn, tra cứu |
| **Lịch làm việc** ||||
| 16 | Lịch của tôi (calendar) | `/doctor/schedule/my-schedule` | Xem lịch làm việc cá nhân dạng calendar |
| 17 | Lịch hôm nay | *(sub-page)* | Ca ngày / ca đêm hôm nay |
| 18 | Đăng ký ca | *(sub-page)* | Tự đăng ký ca làm việc |
| 19 | Xin nghỉ phép | *(action)* | Gửi đơn xin nghỉ, xem hạn quota nghỉ |
| **Profile** ||||
| 20 | Profile cá nhân | `/doctor/profile` | Xem / sửa thông tin cá nhân bác sĩ |

---

## Y TÁ — Điều phối & Kho thuốc

| # | Chức năng | Route | Mô tả |
|---|-----------|-------|-------|
| **Dashboard** ||||
| 1 | Sảnh chờ / Dashboard | `/nurse/dashboard` | Sảnh chờ bệnh nhân, thống kê sắp đến / đang chờ / hoàn thành |
| **Lịch hẹn & Check-in** ||||
| 2 | Danh sách lịch hẹn | `/nurse/appointments` | Xem tất cả lịch hẹn, lọc trạng thái |
| 3 | Check-in bệnh nhân | *(sub-page)* | Xác nhận bệnh nhân đã đến, chuyển vào hàng chờ khám |
| 4 | Tạo lịch hẹn | *(sub-page)* | Tạo lịch hẹn mới cho bệnh nhân |
| 5 | Xác nhận / từ chối lịch | *(action)* | Xác nhận hoặc từ chối lịch hẹn |
| **Quản lý Khách hàng** ||||
| 6 | Danh sách khách hàng | `/nurse/customers` | Xem danh sách khách hàng |
| 7 | Tạo khách hàng mới | *(sub-page)* | Đăng ký khách hàng tại quầy |
| 8 | Sửa thông tin khách hàng | *(sub-page)* | Cập nhật thông tin khách hàng |
| 9 | Thêm thú cưng | *(sub-page)* | Thêm thú cưng mới cho khách hàng |
| **Hóa đơn** ||||
| 10 | Danh sách hóa đơn | `/nurse/invoices` | Xem danh sách hóa đơn |
| 11 | Tạo hóa đơn | *(sub-page)* | Tạo hóa đơn mới sau khám |
| 12 | Chi tiết hóa đơn | *(sub-page)* | Xem chi tiết, in hóa đơn |
| **Kho thuốc & Vật tư** ||||
| 13 | Kho thuốc & vật tư | `/nurse/inventory` | Xem danh sách thuốc / vật tư, tồn kho |
| 14 | Thêm thuốc / vật tư | *(sub-page)* | Thêm mặt hàng mới vào hệ thống |
| 15 | Danh mục hàng hóa | *(sub-page)* | Quản lý danh mục thuốc / vật tư |
| 16 | Quản lý nhà cung cấp | *(sub-page)* | Thêm, sửa, xóa nhà cung cấp; xem công nợ |
| 17 | Tạo phiếu nhập kho | *(sub-page)* | Lập phiếu nhập hàng từ nhà cung cấp |
| 18 | Chi tiết phiếu nhập | *(sub-page)* | Xem chi tiết, đổi trạng thái phiếu nhập |
| 19 | Xuất PDF phiếu nhập | *(action)* | Xuất phiếu nhập kho ra PDF |
| 20 | Thẻ kho | *(sub-page)* | Xem lịch sử nhập / xuất từng mặt hàng |
| **Phiếu chi** ||||
| 21 | Danh sách phiếu chi | `/nurse/expense-vouchers` | Xem danh sách phiếu chi, lọc theo trạng thái |
| 22 | Tạo phiếu chi | *(sub-page)* | Tạo phiếu chi mới |
| 23 | Chi tiết phiếu chi | *(sub-page)* | Xem chi tiết phiếu chi |
| 24 | Thanh toán thêm | *(sub-page)* | Ghi nhận thanh toán bổ sung |
| 25 | Xuất PDF phiếu chi | *(action)* | Xuất phiếu chi ra PDF |
| **Lịch làm việc** ||||
| 26 | Lịch của tôi (calendar) | `/nurse/schedule/my-schedule` | Xem lịch làm việc cá nhân dạng calendar |
| 27 | Lịch hôm nay | *(sub-page)* | Ca ngày / ca đêm hôm nay |
| 28 | Đăng ký ca | *(sub-page)* | Tự đăng ký ca làm việc |
| 29 | Xin nghỉ phép | *(action)* | Gửi đơn xin nghỉ, xem hạn quota nghỉ |
| **Profile** ||||
| 30 | Profile cá nhân | `/nurse/profile` | Xem / sửa thông tin cá nhân y tá |

---

## KHÁCH HÀNG — Chủ thú cưng

| # | Chức năng | Route | Mô tả |
|---|-----------|-------|-------|
| **Trang công khai (không cần đăng nhập)** ||||
| 1 | Trang chủ | `/` | Giới thiệu phòng khám, CTA đặt lịch |
| 2 | Dịch vụ | `/services` | Xem danh sách dịch vụ khám chữa bệnh |
| 3 | Giới thiệu | `/about` | Thông tin về phòng khám |
| 4 | Liên hệ | `/contact` | Form liên hệ |
| 5 | Diễn đàn — Danh sách bài viết | `/forum` | Xem bài viết, lọc theo danh mục, tìm kiếm |
| 6 | Diễn đàn — Chi tiết bài viết | `/forum/:id` | Đọc nội dung đầy đủ, xem bình luận |
| **Xác thực** ||||
| 7 | Đăng ký tài khoản | `/customer/register` | Tạo tài khoản bằng email |
| 8 | Đăng nhập | `/customer/login` | Đăng nhập email hoặc Google / Facebook OAuth |
| 9 | Xác minh email | `/verify-email` | Nhập / chờ xác minh email sau đăng ký; gửi lại email |
| 10 | Trang xác thực thành công | `/auth/verified` | Xác nhận email đã xác minh, tự đăng nhập |
| **Thú cưng** ||||
| 11 | Thú cưng của tôi | `/customer/my-pets` | Danh sách thú cưng |
| 12 | Thêm thú cưng | *(sub-page)* | Tạo hồ sơ thú cưng mới (tên, loài, giống, tuổi, ảnh) |
| 13 | Chi tiết thú cưng | *(sub-page)* | Xem thông tin chi tiết thú cưng |
| 14 | Xóa thú cưng | *(sub-page)* | Xóa hồ sơ thú cưng |
| **Đặt lịch & Lịch hẹn** ||||
| 15 | Đặt lịch khám | `/customer/appointments/book` | Chọn dịch vụ, thú cưng, ngày giờ; kiểm tra slot trống |
| 16 | Lịch hẹn của tôi | `/customer/appointments` | Xem danh sách lịch hẹn đã đặt |
| 17 | Chi tiết lịch hẹn | *(sub-page)* | Xem thông tin chi tiết lịch hẹn |
| 18 | Hủy lịch hẹn | *(sub-page)* | Hủy lịch hẹn đã đặt |
| 19 | Đổi lịch hẹn | *(sub-page)* | Chọn lại ngày / giờ khám |
| 20 | Xem kết quả khám | *(sub-page)* | Xem kết quả và đơn thuốc sau khi khám xong |
| **Thanh toán** ||||
| 21 | Danh sách hóa đơn | `/customer/payment` | Xem các hóa đơn cần thanh toán, lịch sử giao dịch |
| 22 | Thanh toán MoMo | *(action)* | Thanh toán trực tuyến qua cổng MoMo |
| 23 | Trang kết quả thanh toán | `/payment/success` | Xác nhận thanh toán thành công |
| 24 | Chi tiết hóa đơn | *(sub-page)* | Xem chi tiết từng hóa đơn |
| **Tài khoản** ||||
| 25 | Quản lý tài khoản | `/customer/my-account` | Tổng quan tài khoản, truy cập nhanh các mục |
| 26 | Thông tin cá nhân | `/customer/personal-info` | Xem / sửa tên, ảnh đại diện, số điện thoại, địa chỉ |
| 27 | Hỗ trợ / Liên hệ | `/customer/help-contact` | Gửi yêu cầu hỗ trợ, chat với chatbot AI |
| **Tương tác diễn đàn (cần đăng nhập)** ||||
| 28 | Bình luận bài viết | *(action)* | Gửi bình luận, reply 1 cấp |
| 29 | Like / Dislike | *(action)* | Like hoặc dislike bài viết và bình luận |
| 30 | Xóa bình luận của mình | *(action)* | Xóa bình luận do chính mình tạo |

---

## Tóm tắt

| Role | Số chức năng | Trọng tâm |
|------|-------------|-----------|
| Admin | ~20 | Quản trị toàn hệ thống — nhân sự, tài chính, báo cáo, cấu hình |
| Bác sĩ | ~20 | Quy trình khám bệnh — từ check-in đến kê đơn và tái khám |
| Y tá | ~30 | Điều phối + hậu cần — lịch hẹn, kho thuốc, hóa đơn, phiếu chi |
| Khách hàng | ~30 | Tự phục vụ — đặt lịch, quản lý thú cưng, thanh toán, diễn đàn |

> Y tá và khách hàng có phạm vi rộng nhất. Y tá xử lý hậu cần nội bộ; khách hàng tự phục vụ toàn bộ vòng đời từ đặt lịch đến thanh toán.
