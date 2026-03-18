<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NhanVienRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only allow authenticated users (middleware EnsureAdmin will be applied on route)
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:nhan_viens,email',
            'phone' => 'required|digits:10',
            'address' => 'required|string',
            'anh_dai_dien' => 'required|string',
            'vai_tro' => 'required|in:bac_si,y_ta',
            'chuc_danh' => 'required|string|max:255',
            'nam_kinh_nghiem' => 'required|integer|min:0',
            'chung_chi_hanh_nghe' => 'required|string',
            'bang_cap_chuyen_mon' => 'required|string',
            // password is required when creating (POST) but optional when updating (PUT/PATCH)
            'trang_thai' => 'required|in:hoat_dong,da_khoa',
        ];

        // Allow creating without providing a password (controller will generate one if missing)
        // Enforce a reasonable minimum length for passwords
        $rules['password'] = $this->isMethod('post') ? 'nullable|string|min:8|max:64|confirmed' : 'nullable|string|min:8|max:64|confirmed';

        return $rules;
    }

    /**
     * Custom messages for validation errors (Vietnamese).
     * This prevents returning translation keys like "validation.required" when locale files are missing.
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Họ và tên là bắt buộc.',
            'full_name.string' => 'Họ và tên không hợp lệ.',
            'full_name.max' => 'Họ và tên không được quá :max ký tự.',

            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Định dạng email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',

            'phone.required' => 'Số điện thoại là bắt buộc.',
            'phone.digits' => 'Số điện thoại phải gồm 10 chữ số.',

            'vai_tro.required' => 'Vai trò là bắt buộc.',
            'vai_tro.in' => "Vai trò không hợp lệ. Giá trị hợp lệ: 'bac_si' (bác sĩ), 'y_ta' (y tá).",

            'chuc_danh.string' => 'Chức danh không hợp lệ.',
            'chuc_danh.max' => 'Chức danh không được quá :max ký tự.',
            'chuc_danh.required' => 'Chức danh là bắt buộc.',

            'nam_kinh_nghiem.integer' => 'Năm kinh nghiệm phải là số nguyên.',
            'nam_kinh_nghiem.min' => 'Năm kinh nghiệm không được nhỏ hơn :min.',
            'nam_kinh_nghiem.required' => 'Năm kinh nghiệm là bắt buộc.',

            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.string' => 'Mật khẩu không hợp lệ.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'password.max' => 'Mật khẩu không được quá :max ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',

            'trang_thai.in' => "Trạng thái không hợp lệ. Giá trị hợp lệ: 'hoat_dong', 'da_khoa'.",
            'trang_thai.required' => 'Trạng thái là bắt buộc.',

            // address, anh_dai_dien, chung_chi_hanh_nghe, bang_cap_chuyen_mon required messages
            'address.required' => 'Địa chỉ là bắt buộc.',
            'address.string' => 'Địa chỉ không hợp lệ.',
            'anh_dai_dien.required' => 'Ảnh đại diện là bắt buộc.',
            'anh_dai_dien.string' => 'Ảnh đại diện không hợp lệ.',
            'chung_chi_hanh_nghe.required' => 'Chứng chỉ hành nghề là bắt buộc.',
            'chung_chi_hanh_nghe.string' => 'Chứng chỉ hành nghề không hợp lệ.',
            'bang_cap_chuyen_mon.required' => 'Bằng cấp chuyên môn là bắt buộc.',
            'bang_cap_chuyen_mon.string' => 'Bằng cấp chuyên môn không hợp lệ.',
        ];
    }

    /**
     * Human readable attribute names (Vietnamese) used in validation messages.
     */
    public function attributes(): array
    {
        return [
            'full_name' => 'Họ và tên',
            'email' => 'Email',
            'phone' => 'Số điện thoại',
            'address' => 'Địa chỉ',
            'anh_dai_dien' => 'Ảnh đại diện',
            'vai_tro' => 'Vai trò',
            'chuc_danh' => 'Chức danh',
            'nam_kinh_nghiem' => 'Năm kinh nghiệm',
            'chung_chi_hanh_nghe' => 'Chứng chỉ hành nghề',
            'bang_cap_chuyen_mon' => 'Bằng cấp chuyên môn',
            'password' => 'Mật khẩu',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
