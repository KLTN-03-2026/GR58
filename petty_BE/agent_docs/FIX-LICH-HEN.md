## ✅ ĐÃ FIX LỖI KHÁCH HÀNG KHÔNG ĐẶT LỊCH HẸN ĐƯỢC

### 🔧 Thay đổi:

1. **Route API `/api/lich-hen` (POST)**
   - ✅ Đã bỏ middleware `staff.only` và `permission:lich_hen_tao`
   - ✅ Cho phép **cả khách hàng và staff** đặt lịch hẹn
   - ⚠️ Các route khác (GET, PUT, DELETE) vẫn giữ phân quyền cho staff

2. **LichHenController@store**
   - ✅ Xử lý đúng khi **khách hàng** đặt lịch:
     - Tự động lấy `khach_hang_id` từ user đăng nhập
     - Kiểm tra thú cưng có thuộc về khách hàng không
   - ✅ Xử lý đúng khi **Admin/NhanVien** tạo lịch hẹn:
     - Yêu cầu `khach_hang_id` phải có trong request
     - Không kiểm tra owner của thú cưng

### 📋 Kết quả:

**Khách hàng** có thể:
- ✅ Đặt lịch hẹn (POST /api/lich-hen)
- ✅ Chỉ đặt lịch cho thú cưng của mình

**Staff (Admin/NhanVien)** có thể:
- ✅ Tạo lịch hẹn cho bất kỳ khách hàng nào (cần quyền `lich_hen_tao`)
- ✅ Xem tất cả lịch hẹn (cần quyền `lich_hen_xem`)
- ✅ Sửa, xóa, xác nhận lịch hẹn (cần quyền tương ứng)

### 🧪 Test ngay:
Khách hàng đăng nhập và thử đặt lịch hẹn lại → Sẽ thành công!
