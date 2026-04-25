<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KhoaController;
use App\Http\Controllers\LichLamViecController;
use App\Http\Controllers\NhanVienController;
use App\Models\KhachHang;
use App\Http\Controllers\ThuCungController;
use App\Http\Controllers\LichHenController;
use App\Http\Controllers\DichVuController;
use App\Http\Controllers\DanhMucDichVuController;
use App\Http\Controllers\DanhMucHangHoaController;
use App\Http\Controllers\HangHoaController;
use App\Http\Controllers\PhanLoaiBaiVietController;
use App\Http\Controllers\BaiVietController;
use App\Http\Controllers\BinhLuanController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\NhaCungCapController;
use App\Http\Controllers\PhieuNhapKhoController;
use App\Http\Controllers\KiemKeController;
use App\Http\Controllers\PhieuChiController;
use App\Http\Controllers\KhuyenMaiController;
use App\Http\Controllers\PhanQuyenController;
use App\Http\Controllers\LichDangKyController;
use App\Http\Controllers\PhieuKhamController;
use App\Http\Controllers\HoSoBenhAnController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LichNghiController;
use App\Http\Controllers\ThanhToanController;

Route::post('/khach-hang/dang-ki', [KhachHangController::class, 'dangKi']);
Route::post('/khach-hang/dang-nhap', [KhachHangController::class, 'dangNhap']);
Route::post('/khach-hang/gui-lai-xac-nhan', [KhachHangController::class, 'guiLaiXacNhan']);

// Admin login route
Route::post('/admin/dang-nhap', [AdminController::class, 'dangNhap']);
Route::post('/chatbot/message', [ChatbotController::class, 'message'])->middleware('throttle:20,1');

// Nhân viên login route
Route::post('/nhan-vien/dang-nhap', [NhanVienController::class, 'dangNhap']);


// Routes đăng nhập mạng xã hội
Route::get('/auth/google', [KhachHangController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [KhachHangController::class, 'handleGoogleCallback']);
Route::get('/auth/facebook', [KhachHangController::class, 'redirectToFacebook']);
Route::get('/auth/facebook/callback', [KhachHangController::class, 'handleFacebookCallback']);

// Route xác thực Email
Route::get('/email/verify/{id}/{hash}', [KhachHangController::class, 'verifyEmail'])->name('verification.verify');

Route::middleware('auth:sanctum')->group(function () {
    // Lấy thông tin user hiện tại (Admin hoặc NhanVien) kèm quyền
    Route::get('/user', function (Request $request) {
        $user = $request->user();

        // Load thông tin vai trò và quyền nếu có (chỉ cho Admin và NhanVien, không phải KhachHang)
        if ($user && method_exists($user, 'load') && !($user instanceof \App\Models\KhachHang)) {
            $user->load('phanQuyen');
        }

        $userData = [
            'status' => true,
            'data' => $user,
            'user_type' => $user instanceof \App\Models\Admin ? 'admin' : ($user instanceof \App\Models\NhanVien ? 'nhan_vien' : 'khach_hang'),
        ];

        // Thêm thông tin debug về quyền
        if ($user && method_exists($user, 'hasPermission')) {
            $userData['has_phan_quyen'] = $user->phanQuyen ? true : false;
            $userData['phan_quyen_id'] = $user->phan_quyen_id ?? null;
            $userData['has_phieu_nhap_kho_tao'] = $user->phanQuyen ? $user->phanQuyen->phieu_nhap_kho_tao : null;
        }

        return response()->json($userData);
    });

    // Debug route để kiểm tra user đang đăng nhập
    Route::get('/debug-auth', function (Request $request) {
        $user = $request->user();

        return response()->json([
            'authenticated' => $user ? true : false,
            'user_id' => $user ? $user->id : null,
            'user_email' => $user ? ($user->email ?? $user->email ?? 'N/A') : null,
            'user_class' => $user ? get_class($user) : null,
            'is_admin' => $user instanceof \App\Models\Admin,
            'is_nhanvien' => $user instanceof \App\Models\NhanVien,
            'is_khachhang' => $user instanceof \App\Models\KhachHang,
            'is_user' => $user instanceof \App\Models\User,
            'token_name' => $request->user() ? $request->user()->currentAccessToken()?->name : null,
            'phan_quyen_id' => $user ? ($user->phan_quyen_id ?? null) : null,
        ]);
    });

    Route::post('/khach-hang/cap-nhat', [KhachHangController::class, 'capNhat']);
    Route::put('/khach-hang/{id}', [KhachHangController::class, 'update']);
    Route::get('/khach-hang', [KhachHangController::class, 'index'])->middleware('staff.only');

    // Thu cưng routes (require authentication)
    Route::get('/thu-cung', [ThuCungController::class, 'index']);
    Route::get('/thu-cung/{thuCung}', [ThuCungController::class, 'show']);
    Route::post('/thu-cung', [ThuCungController::class, 'store']);
    Route::put('/thu-cung/{thuCung}', [ThuCungController::class, 'update']);
    Route::patch('/thu-cung/{thuCung}', [ThuCungController::class, 'update']);
    Route::delete('/thu-cung/{thuCung}', [ThuCungController::class, 'destroy']);

    // Slot còn trống (public, auth required)
    Route::get('/lich-hen/available-slots', [LichHenController::class, 'availableSlots']);

    // Lịch hẹn - Khách hàng có thể tạo và xem lịch của mình
    Route::post('/lich-hen', [LichHenController::class, 'store']); // Khách hàng đặt lịch
    Route::get('/lich-hen', [LichHenController::class, 'index']); // Khách hàng xem lịch của mình
    Route::get('/lich-hen/{lichHen}', [LichHenController::class, 'show']); // Khách hàng xem chi tiết lịch của mình
    Route::match(['put', 'patch'], '/lich-hen/{lichHen}/ngay-gio', [LichHenController::class, 'updateNgayGio']); // Khách hàng đổi lịch của mình
    Route::delete('/lich-hen/{lichHen}', [LichHenController::class, 'destroy']); // Khách hàng hủy lịch của mình

    // Thanh toán
    Route::post('/payment/momo/create', [PaymentController::class, 'createMoMoPayment']);
    Route::post('/payment/update-status', [PaymentController::class, 'updatePaymentStatus']);

    // Lịch hẹn - Staff quản lý (xem tất cả, sửa, xác nhận)
    Route::get('/lich-hen-all', [LichHenController::class, 'indexAll'])->middleware(['staff.only', 'permission:lich_hen_xem']); // Staff xem tất cả lịch hẹn
    Route::match(['put', 'patch'], '/lich-hen/{lichHen}', [LichHenController::class, 'update'])->middleware(['staff.only', 'permission:lich_hen_sua']);
    Route::patch('/lich-hen/{lichHen}/xac-nhan', [LichHenController::class, 'confirm'])->middleware(['staff.only', 'permission:lich_hen_xac_nhan']);

    // Lịch hẹn - Check-in (Y tá)
    Route::post('/lich-hen/{lichHen}/check-in', [LichHenController::class, 'checkIn'])->middleware('staff.only'); // Y tá check-in lịch hẹn
    Route::get('/lich-hen-cho-check-in', [LichHenController::class, 'lichHenChoCheckIn'])->middleware('staff.only'); // Danh sách lịch hẹn chờ check-in
    Route::get('/lich-hen-da-check-in', [LichHenController::class, 'lichHenDaCheckIn'])->middleware('staff.only'); // Danh sách lịch hẹn đã check-in

    // Lịch hẹn - Khám bệnh (Bác sĩ)
    Route::get('/benh-nhan-cho-kham', [LichHenController::class, 'benhNhanChoKham'])->middleware('staff.only'); // Danh sách bệnh nhân chờ khám
    Route::post('/lich-hen/{lichHen}/bat-dau-kham', [LichHenController::class, 'batDauKham'])->middleware('staff.only'); // Bác sĩ bắt đầu khám
    Route::get('/benh-nhan-dang-kham', [LichHenController::class, 'benhNhanDangKham'])->middleware('staff.only'); // Danh sách bệnh nhân đang khám
    Route::post('/lich-hen/{lichHen}/hoan-thanh-kham', [LichHenController::class, 'hoanThanhKham'])->middleware('staff.only'); // Hoàn thành khám

    // Lịch làm việc: tạo mới - Staff only
    Route::post('/lich-lam-viec', [LichLamViecController::class, 'store'])->middleware(['staff.only', 'permission:lich_lam_viec_tao']);

    // Lịch làm việc của bác sĩ/nhân viên đang đăng nhập (phải đặt TRƯỚC route có {lichLamViec})
    // Cho phép cả khách hàng xem (để đặt lịch hẹn)
    Route::get('/lich-lam-viec/cua-toi', [LichLamViecController::class, 'getMySchedule'])->middleware('auth:sanctum');
    Route::get('/lich-lam-viec/cua-toi/hom-nay', [LichLamViecController::class, 'getMyTodaySchedule'])->middleware('auth:sanctum');

    // Lịch làm việc và lịch hẹn của bác sĩ/nhân viên (cho admin) - Staff only
    Route::get('/lich-lam-viec/bac-si/{nhanVienId}', [LichLamViecController::class, 'getScheduleByDoctor'])->middleware(['staff.only', 'permission:lich_lam_viec_xem']);
    Route::get('/lich-lam-viec/hom-nay', [LichLamViecController::class, 'getTodaySchedule'])->middleware(['staff.only', 'permission:lich_lam_viec_xem']);

    // Lịch làm việc: sinh lịch tự động (phải đặt TRƯỚC route có {lichLamViec})
    Route::post('/lich-lam-viec/generate', [LichLamViecController::class, 'generate'])->middleware(['staff.only', 'permission:lich_lam_viec_tao']);

    // Lịch làm việc: list & view (phải đặt SAU các route cụ thể) - Staff only
    Route::get('/lich-lam-viec', [LichLamViecController::class, 'index'])->middleware(['staff.only', 'permission:lich_lam_viec_xem']);
    Route::get('/lich-lam-viec/{lichLamViec}', [LichLamViecController::class, 'show'])->middleware(['staff.only', 'permission:lich_lam_viec_xem']);

    // Lịch nghỉ - Nhân viên (bác sĩ/y tá)
    Route::get('/lich-nghi/quota', [LichNghiController::class, 'quota'])->middleware('staff.only');
    Route::get('/lich-nghi', [LichNghiController::class, 'index'])->middleware('staff.only');
    Route::post('/lich-nghi', [LichNghiController::class, 'store'])->middleware('staff.only');
    Route::delete('/lich-nghi/{lichNghi}', [LichNghiController::class, 'destroy'])->middleware('staff.only');

    // Lịch nghỉ - Admin
    Route::get('/admin/lich-nghi', [LichNghiController::class, 'indexAll'])->middleware(['staff.only', 'permission:lich_lam_viec_xem']);
    Route::post('/admin/lich-nghi', [LichNghiController::class, 'storeAdmin'])->middleware(['staff.only', 'permission:lich_lam_viec_tao']);
    Route::patch('/admin/lich-nghi/{lichNghi}/duyet', [LichNghiController::class, 'duyet'])->middleware(['staff.only', 'permission:lich_lam_viec_tao']);
    Route::patch('/admin/lich-nghi/{lichNghi}/tu-choi', [LichNghiController::class, 'tuChoi'])->middleware(['staff.only', 'permission:lich_lam_viec_tao']);
    Route::get('/admin/lich-nghi/{lichNghi}/suggest', [LichNghiController::class, 'suggestThayThe'])->middleware(['staff.only', 'permission:lich_lam_viec_xem']);

    // Lịch đăng ký - CRUD
    Route::get('/lich-dang-ky', [LichDangKyController::class, 'index']); // Khách hàng và Staff xem lịch đăng ký
    Route::post('/lich-dang-ky', [LichDangKyController::class, 'store']); // Tạo mới lịch đăng ký
    Route::get('/lich-dang-ky/statuses', [LichDangKyController::class, 'getStatuses']); // Lấy danh sách trạng thái
    Route::get('/lich-dang-ky/chua-xac-nhan', [LichDangKyController::class, 'chuaXacNhan'])->middleware('staff.only'); // Lịch chưa xác nhận
    Route::get('/lich-dang-ky/da-xac-nhan', [LichDangKyController::class, 'daXacNhan'])->middleware('staff.only'); // Lịch đã xác nhận
    Route::get('/lich-dang-ky/tu-choi', [LichDangKyController::class, 'danhSachTuChoi'])->middleware('staff.only'); // Lịch từ chối

    // Xem danh sách lịch theo từng nhân viên
    Route::get('/lich-dang-ky/theo-nhan-vien', [LichDangKyController::class, 'danhSachTheoNhanVien'])->middleware('staff.only'); // Danh sách lịch nhóm theo nhân viên

    // Nhân viên tự đăng ký lịch làm việc
    Route::post('/lich-dang-ky/dang-ky-nhan-vien', [LichDangKyController::class, 'dangKyNhanVien'])->middleware('staff.only'); // Nhân viên tự đăng ký
    Route::get('/lich-dang-ky/lich-cua-toi', [LichDangKyController::class, 'lichCuaToi'])->middleware('staff.only'); // Xem tất cả lịch của mình
    Route::get('/lich-dang-ky/ca-da-xac-nhan/cua-toi', [LichDangKyController::class, 'caDaXacNhanCuaToi'])->middleware('staff.only'); // Xem ca đã đăng ký (đã xác nhận)

    Route::get('/lich-dang-ky/{lichDangKy}', [LichDangKyController::class, 'show']); // Xem chi tiết
    Route::match(['put', 'patch'], '/lich-dang-ky/{lichDangKy}', [LichDangKyController::class, 'update']); // Cập nhật
    Route::delete('/lich-dang-ky/{lichDangKy}', [LichDangKyController::class, 'destroy']); // Xóa

    // Lịch đăng ký - Chức năng đặc biệt (Staff only)
    Route::put('/lich-dang-ky/{lichDangKy}/xac-nhan', [LichDangKyController::class, 'xacNhan'])->middleware('staff.only'); // Xác nhận lịch
    Route::put('/lich-dang-ky/{lichDangKy}/tu-choi', [LichDangKyController::class, 'tuChoi'])->middleware('staff.only'); // Từ chối lịch
    Route::put('/lich-dang-ky/{lichDangKy}/doi-trang-thai', [LichDangKyController::class, 'doiTrangThai'])->middleware('staff.only'); // Đổi trạng thái
});

// Public: list services
Route::get('/dich-vu', [DichVuController::class, 'index']);
// Public: view single service
Route::get('/dich-vu/{dichVu}', [DichVuController::class, 'show']);

// Public: list and view service categories
Route::get('/danh-muc-dich-vu', [DanhMucDichVuController::class, 'index']);
Route::get('/danh-muc-dich-vu/{danhMucDichVu}', [DanhMucDichVuController::class, 'show']);

// Public: list and view product categories
Route::get('/danh-muc-hang-hoa', [DanhMucHangHoaController::class, 'index']);
Route::get('/danh-muc-hang-hoa/{danhMucHangHoa}', [DanhMucHangHoaController::class, 'show']);

// Public: list and view products
Route::get('/hang-hoa', [HangHoaController::class, 'index']);
Route::get('/hang-hoa/{hangHoa}', [HangHoaController::class, 'show']);

// Public: list and view article categories
Route::get('/phan-loai-bai-viet', [PhanLoaiBaiVietController::class, 'index']);
Route::get('/phan-loai-bai-viet/{phanLoaiBaiViet}', [PhanLoaiBaiVietController::class, 'show']);

// Public: list and view articles
Route::get('/bai-viet', [BaiVietController::class, 'index']);
Route::get('/bai-viet/{baiViet}', [BaiVietController::class, 'show']);

// Public: get comments for a post
Route::get('/bai-viet/{baiVietId}/binh-luan', [BinhLuanController::class, 'index']);

// Public: get reactions for a post or comment
Route::get('/reactions', [ReactionController::class, 'index']);

// Admin login route
Route::post('/admin/dang-nhap', [AdminController::class, 'dangNhap']);
// route cho momo webhook
Route::post('/payment/momo/ipn', [PaymentController::class, 'momoIPN']);
// Admin authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/dang-xuat', [AdminController::class, 'dangXuat']);
    Route::post('/nhan-vien/dang-xuat', [NhanVienController::class, 'dangXuat']);

    // Khoa: tạo mới (chỉ admin và nhân viên có quyền)
    Route::post('/khoa', [KhoaController::class, 'store'])->middleware(['staff.only', 'permission:khoa_tao']);
    // Nhân viên: tạo mới (chỉ admin và nhân viên có quyền)
    Route::post('/nhan-vien', [NhanVienController::class, 'store'])->middleware(['staff.only', 'permission:nhan_vien_tao']);
    // Nhân viên: đổi mật khẩu (chỉ admin và nhân viên có quyền)
    Route::patch('/nhan-vien/{nhanVien}/mat-khau', [NhanVienController::class, 'changePassword'])->middleware(['staff.only', 'permission:nhan_vien_doi_mat_khau']);
    // Nhân viên: khóa tài khoản (chỉ admin và nhân viên có quyền)
    Route::patch('/nhan-vien/{nhanVien}/khoa', [NhanVienController::class, 'lockAccount'])->middleware(['staff.only', 'permission:nhan_vien_khoa_tai_khoan']);
    // Nhân viên: mở khóa tài khoản (chỉ admin và nhân viên có quyền)
    Route::patch('/nhan-vien/{nhanVien}/mo-khoa', [NhanVienController::class, 'unlockAccount'])->middleware(['staff.only', 'permission:nhan_vien_mo_khoa_tai_khoan']);
    // Nhân viên: danh sách (chỉ admin và nhân viên có quyền)
    Route::get('/nhan-vien', [NhanVienController::class, 'index'])->middleware(['staff.only', 'permission:nhan_vien_xem']);
    // Danh mục dịch vụ: tạo mới (staff only)
    Route::post('/danh-muc-dich-vu', [\App\Http\Controllers\DanhMucDichVuController::class, 'store'])->middleware('staff.only');
    // Danh mục hàng hóa: tạo mới (staff only)
    Route::post('/danh-muc-hang-hoa', [\App\Http\Controllers\DanhMucHangHoaController::class, 'store'])->middleware('staff.only');
    // Hàng hóa: tạo mới (staff only)
    Route::post('/hang-hoa', [HangHoaController::class, 'store'])->middleware(['staff.only', 'permission:hang_hoa_tao']);
    // Hàng hóa: cập nhật & xóa (staff only)
    Route::match(['put', 'patch'], '/hang-hoa/{hangHoa}', [HangHoaController::class, 'update'])->middleware(['staff.only', 'permission:hang_hoa_sua']);
    Route::delete('/hang-hoa/{hangHoa}', [HangHoaController::class, 'destroy'])->middleware(['staff.only', 'permission:hang_hoa_xoa']);
    // Hàng hóa: đổi trạng thái (staff only)
    Route::patch('/hang-hoa/{hangHoa}/trang-thai', [HangHoaController::class, 'changeStatus'])->middleware(['staff.only', 'permission:hang_hoa_doi_trang_thai']);
    // Dich vu: tạo mới (staff only)
    Route::post('/dich-vu', [\App\Http\Controllers\DichVuController::class, 'store'])->middleware(['staff.only', 'permission:dich_vu_tao']);
    // Dich vu: update & delete (staff only)
    Route::match(['put', 'patch'], '/dich-vu/{dichVu}', [\App\Http\Controllers\DichVuController::class, 'update'])->middleware(['staff.only', 'permission:dich_vu_sua']);
    Route::delete('/dich-vu/{dichVu}', [\App\Http\Controllers\DichVuController::class, 'destroy'])->middleware(['staff.only', 'permission:dich_vu_xoa']);
    // File upload for staff (images etc.)
    Route::post('/upload', [\App\Http\Controllers\UploadController::class, 'store'])->middleware('staff.only');
    Route::post('/upload-image', [\App\Http\Controllers\UploadController::class, 'store'])->middleware('staff.only');
    // Danh mục dịch vụ: xóa (staff only)
    Route::delete('/danh-muc-dich-vu/{danhMucDichVu}', [\App\Http\Controllers\DanhMucDichVuController::class, 'destroy'])->middleware('staff.only');
    // Danh mục hàng hoa: update & delete (staff only)
    Route::match(['put', 'patch'], '/danh-muc-hang-hoa/{danhMucHangHoa}', [\App\Http\Controllers\DanhMucHangHoaController::class, 'update'])->middleware('staff.only');
    Route::delete('/danh-muc-hang-hoa/{danhMucHangHoa}', [\App\Http\Controllers\DanhMucHangHoaController::class, 'destroy'])->middleware('staff.only');
    // Phân loại bài viết: tạo mới, cập nhật, xóa (staff only)
    Route::post('/phan-loai-bai-viet', [PhanLoaiBaiVietController::class, 'store'])->middleware('staff.only');
    Route::match(['put', 'patch'], '/phan-loai-bai-viet/{phanLoaiBaiViet}', [PhanLoaiBaiVietController::class, 'update'])->middleware('staff.only');
    Route::delete('/phan-loai-bai-viet/{phanLoaiBaiViet}', [PhanLoaiBaiVietController::class, 'destroy'])->middleware('staff.only');
    // Bài viết: tạo mới, cập nhật, xóa (staff only)
    Route::post('/bai-viet', [BaiVietController::class, 'store'])->middleware('staff.only');
    Route::match(['put', 'patch'], '/bai-viet/{baiViet}', [BaiVietController::class, 'update'])->middleware('staff.only');
    Route::delete('/bai-viet/{baiViet}', [BaiVietController::class, 'destroy'])->middleware('staff.only');

    // Bình luận: tạo, sửa, xóa (authenticated users)
    Route::post('/bai-viet/{baiVietId}/binh-luan', [BinhLuanController::class, 'store']);
    Route::match(['put', 'patch'], '/binh-luan/{id}', [BinhLuanController::class, 'update']);
    Route::delete('/binh-luan/{id}', [BinhLuanController::class, 'destroy']);

    // Reactions: toggle reaction (authenticated users)
    Route::post('/reactions/toggle', [ReactionController::class, 'toggle']);

    // Nhà cung cấp: CRUD (staff only)
    Route::get('/nha-cung-cap', [NhaCungCapController::class, 'index'])->middleware('staff.only');
    Route::get('/nha-cung-cap/with-debt', [NhaCungCapController::class, 'indexWithDebt'])->middleware('staff.only');
    Route::get('/nha-cung-cap/{nhaCungCap}', [NhaCungCapController::class, 'show'])->middleware('staff.only');
    Route::post('/nha-cung-cap', [NhaCungCapController::class, 'store'])->middleware('staff.only');
    Route::match(['put', 'patch'], '/nha-cung-cap/{nhaCungCap}', [NhaCungCapController::class, 'update'])->middleware('staff.only');
    Route::patch('/nha-cung-cap/{nhaCungCap}/trang-thai', [NhaCungCapController::class, 'changeStatus'])->middleware('staff.only');
    Route::delete('/nha-cung-cap/{nhaCungCap}', [NhaCungCapController::class, 'destroy'])->middleware('staff.only');

    // Phiếu nhập kho: CRUD (staff only)
    Route::get('/phieu-nhap-kho', [PhieuNhapKhoController::class, 'index'])->middleware(['staff.only', 'permission:phieu_nhap_kho_xem']);
    Route::get('/phieu-nhap-kho/{phieuNhapKho}', [PhieuNhapKhoController::class, 'show'])->middleware(['staff.only', 'permission:phieu_nhap_kho_xem']);
    Route::post('/phieu-nhap-kho', [PhieuNhapKhoController::class, 'store'])->middleware(['staff.only', 'permission:phieu_nhap_kho_tao']);
    Route::patch('/phieu-nhap-kho/{phieuNhapKho}/trang-thai', [PhieuNhapKhoController::class, 'changeStatus'])->middleware(['staff.only', 'permission:phieu_nhap_kho_doi_trang_thai']);
    Route::get('/phieu-nhap-kho/{phieuNhapKho}/pdf', [PhieuNhapKhoController::class, 'exportPdf'])->middleware(['staff.only', 'permission:phieu_nhap_kho_xuat_pdf']);
    Route::delete('/phieu-nhap-kho/{phieuNhapKho}', [PhieuNhapKhoController::class, 'destroy'])->middleware(['staff.only', 'permission:phieu_nhap_kho_xoa']);

    // Kiểm kê: CRUD (staff only)
    Route::get('/kiem-ke', [KiemKeController::class, 'index'])->middleware(['staff.only', 'permission:kiem_ke_xem']);
    Route::get('/kiem-ke/{id}', [KiemKeController::class, 'show'])->middleware(['staff.only', 'permission:kiem_ke_xem']);
    Route::post('/kiem-ke', [KiemKeController::class, 'store'])->middleware(['staff.only', 'permission:kiem_ke_tao']);
    Route::match(['put', 'patch'], '/kiem-ke/{id}', [KiemKeController::class, 'update'])->middleware(['staff.only', 'permission:kiem_ke_sua']);
    Route::delete('/kiem-ke/{id}', [KiemKeController::class, 'destroy'])->middleware(['staff.only', 'permission:kiem_ke_xoa']);

    // Phiếu chi: CRUD (staff only)
    Route::get('/phieu-chi', [PhieuChiController::class, 'index'])->middleware(['staff.only', 'permission:phieu_chi_xem']);
    Route::get('/phieu-chi/{id}', [PhieuChiController::class, 'show'])->middleware(['staff.only', 'permission:phieu_chi_xem']);
    Route::post('/phieu-chi', [PhieuChiController::class, 'store'])->middleware(['staff.only', 'permission:phieu_chi_tao']);
    Route::match(['put', 'patch'], '/phieu-chi/{id}', [PhieuChiController::class, 'update'])->middleware(['staff.only', 'permission:phieu_chi_sua']);
    Route::delete('/phieu-chi/{id}', [PhieuChiController::class, 'destroy'])->middleware(['staff.only', 'permission:phieu_chi_xoa']);
    Route::get('/phieu-chi/{id}/pdf', [PhieuChiController::class, 'exportPdf'])->middleware(['staff.only', 'permission:phieu_chi_xuat_pdf']);
    Route::get('/phieu-chi/{id}/lich-su-thanh-toan', [PhieuChiController::class, 'getLichSuThanhToan'])->middleware(['staff.only', 'permission:phieu_chi_xem']);
    Route::post('/phieu-chi/{id}/thanh-toan-them', [PhieuChiController::class, 'thanhToanThem'])->middleware(['staff.only', 'permission:phieu_chi_thanh_toan']);

    // Khuyến mãi: CRUD (staff only)
    Route::get('/khuyen-mai', [KhuyenMaiController::class, 'index'])->middleware('staff.only');
    Route::get('/khuyen-mai/{id}', [KhuyenMaiController::class, 'show'])->middleware('staff.only');
    Route::post('/khuyen-mai', [KhuyenMaiController::class, 'store'])->middleware('staff.only');
    Route::match(['put', 'patch'], '/khuyen-mai/{id}', [KhuyenMaiController::class, 'update'])->middleware('staff.only');
    Route::patch('/khuyen-mai/{id}/trang-thai', [KhuyenMaiController::class, 'changeStatus'])->middleware('staff.only');

    Route::delete('/khuyen-mai/{id}', [KhuyenMaiController::class, 'destroy'])->middleware('staff.only');

    // Phân quyền và vai trò: CRUD (staff only)
    Route::get('/phan-quyen', [PhanQuyenController::class, 'index'])->middleware('staff.only');
    Route::get('/phan-quyen/danh-sach/tat-ca-quyen', [PhanQuyenController::class, 'getAllPermissions'])->middleware('staff.only');
    Route::get('/phan-quyen/{id}', [PhanQuyenController::class, 'show'])->middleware('staff.only');
    Route::match(['put', 'patch'], '/phan-quyen/{id}', [PhanQuyenController::class, 'update'])->middleware('staff.only');

    // Phiếu khám: CRUD (staff only - bác sĩ/y tá khám bệnh)
    Route::get('/phieu-kham', [PhieuKhamController::class, 'index'])->middleware('staff.only'); // Lấy danh sách
    Route::post('/phieu-kham', [PhieuKhamController::class, 'store'])->middleware('staff.only'); // Tạo phiếu khám
    Route::get('/phieu-kham/{id}', [PhieuKhamController::class, 'show'])->middleware('staff.only'); // Lấy chi tiết
    Route::match(['put', 'patch'], '/phieu-kham/{phieuKham}', [PhieuKhamController::class, 'update'])->middleware('staff.only'); // Cập nhật
    Route::delete('/phieu-kham/{phieuKham}', [PhieuKhamController::class, 'destroy'])->middleware('staff.only'); // Xóa
    Route::get('/phieu-kham/thu-cung/{thuCungId}', [PhieuKhamController::class, 'getByPet'])->middleware('staff.only'); // Lấy theo thú cưng
    Route::get('/phieu-kham/bac-si/{nhanVienId}', [PhieuKhamController::class, 'getByDoctor'])->middleware('staff.only'); // Lấy theo bác sĩ
    Route::get('/phieu-kham-hom-nay', [PhieuKhamController::class, 'getTodayExaminations'])->middleware('staff.only'); // Lấy hôm nay

    // Kiểm tra mã khuyến mãi (public - cho khách hàng)
    Route::post('/khuyen-mai/check-code', [KhuyenMaiController::class, 'checkCode']);

    // Hồ sơ bệnh án: danh sách bệnh nhân (staff only - bác sĩ xem)
    Route::get('/ho-so-benh-an', [HoSoBenhAnController::class, 'index'])->middleware('staff.only');
    // Lịch sử khám của 1 thú cưng
    Route::get('/ho-so-benh-an/thu-cung/{thuCungId}', [HoSoBenhAnController::class, 'lichSuKham'])->middleware('staff.only');   
});
// Trong Route::middleware('auth:sanctum')->group(function () { ... })
Route::get('/statistics/dashboard', [\App\Http\Controllers\Api\StatisticController::class, 'getDashboardData'])
    ->middleware(['auth:sanctum', 'staff.only']);

Route::post('/thanh-toan/preview', [ThanhToanController::class, 'preview'])
    ->middleware('staff.only');

Route::post('/thanh-toan', [ThanhToanController::class, 'store'])
    ->middleware('staff.only');

Route::get('/thanh-toan/{id}', [ThanhToanController::class, 'show'])
    ->middleware('staff.only');

// ── Thêm dịch vụ vào lịch hẹn (nhiều dịch vụ) ──
Route::post('/lich-hen/{id}/them-dich-vu', [LichHenController::class, 'themDichVu'])
    ->middleware('staff.only');

Route::delete('/lich-hen/{id}/xoa-dich-vu/{dich_vu_id}', [LichHenController::class, 'xoaDichVu'])
    ->middleware('staff.only');
