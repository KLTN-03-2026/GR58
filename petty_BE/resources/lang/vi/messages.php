<?php

return [
    // General
    'server_error' => 'Đã xảy ra lỗi trên máy chủ. Vui lòng thử lại sau.',
    'validation_failed' => 'Vui lòng kiểm tra lại thông tin đã nhập.',
    'registration_success' => 'Đăng ký thành công. Vui lòng đăng nhập.',

    // Auth
    'login_failed' => 'Email hoặc mật khẩu không đúng.',
    'login_success' => 'Đăng nhập thành công.',
    'unauthorized' => 'Bạn cần đăng nhập để thực hiện hành động này.',
    'update_success' => 'Cập nhật thông tin thành công.',
    'not_found' => 'Không tìm thấy khách hàng.',
    'unauthorized_admin' => 'Chỉ quản trị viên mới có thể thực hiện hành động này.',
    // Khoa validation messages
    'ma_khoa_required' => 'Mã khoa là bắt buộc.',
    'ma_khoa_unique' => 'Mã khoa đã tồn tại.',
    'ma_khoa_max' => 'Mã khoa không được dài quá :max ký tự.',
    'ma_khoa_string' => 'Mã khoa phải là chuỗi hợp lệ.',

    'ten_khoa_required' => 'Tên khoa là bắt buộc.',
    'ten_khoa_max' => 'Tên khoa không được dài quá :max ký tự.',
    'ten_khoa_string' => 'Tên khoa phải là chuỗi hợp lệ.',

    'mo_ta_string' => 'Mô tả phải là chuỗi.',

    'trang_thai_required' => 'Trạng thái là bắt buộc.',
    'trang_thai_in' => 'Trạng thái không hợp lệ. Chỉ nhận các giá trị 0 hoặc 1.',


    // Specific messages
    'email_taken' => 'Email này đã được sử dụng. Vui lòng dùng email khác hoặc đăng nhập.',
    'password_mismatch' => 'Mật khẩu xác nhận không khớp.',
    'terms_required' => 'Bạn cần đồng ý với Điều Khoản và Chính Sách Bảo Mật để tiếp tục.',
    'missing_fields' => 'Thiếu thông tin bắt buộc. Vui lòng điền đầy đủ.',
    'phone_required' => 'Vui lòng nhập số điện thoại.',
    'phone_invalid' => 'Số điện thoại không hợp lệ. Vui lòng chỉ nhập chữ số (10 chữ số).',
    // Thu cưng / pets
    'result_too_large' => 'Tập kết quả quá lớn. Vui lòng phân trang hoặc thêm `force=1` để vượt qua (không khuyến nghị).',
    'forbidden' => 'Bạn không có quyền thực hiện hành động này.',
    'pet_save_error' => 'Lỗi khi lưu thú cưng. Vui lòng thử lại.',
    'pet_update_error' => 'Lỗi khi cập nhật thú cưng. Vui lòng thử lại.',
    'no_changes' => 'Không có thay đổi.',
    'pet_deleted' => 'Thu cưng đã được xóa.',

    // Lịch hẹn / appointments
    'invalid_date_format' => 'Định dạng ngày tháng không hợp lệ cho from_date hoặc to_date.',
    'appointment_unauthorized_view' => 'Bạn không có quyền xem lịch hẹn này.',
    'appointment_unauthorized_update' => 'Bạn không có quyền cập nhật lịch hẹn này.',
    'appointment_unauthorized_delete' => 'Bạn không có quyền xóa lịch hẹn này.',
    'appointment_deleted_success' => 'Lịch hẹn đã được xóa thành công.',
    'pet_not_owner' => 'Thu cưng được chọn không thuộc về khách hàng đã đăng nhập.',
    'logout_success' => 'Đã đăng xuất thành công.',
];
