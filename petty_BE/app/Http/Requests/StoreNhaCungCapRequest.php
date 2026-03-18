<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNhaCungCapRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ma_nha_cung_cap' => 'required|string|max:50|unique:nha_cung_caps,ma_nha_cung_cap',
            'ten_nha_cung_cap' => 'required|string|max:255',
            'ten_nguoi_lien_he' => 'required|string|max:255',
            'so_dien_thoai' => 'required|string|max:15|regex:/^[0-9+\-\s()]+$/',
            'dia_chi' => 'required|string',
            'email' => 'required|email|unique:nha_cung_caps,email',
            'ma_so_thue' => 'required|string|max:50',
            'mo_ta' => 'required|string',
            'trang_thai' => 'sometimes|in:hoat_dong,ngung_hoat_dong',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ma_nha_cung_cap.required' => 'Mã nhà cung cấp là bắt buộc',
            'ma_nha_cung_cap.max' => 'Mã nhà cung cấp không được vượt quá 50 ký tự',
            'ma_nha_cung_cap.unique' => 'Mã nhà cung cấp này đã tồn tại',
            'ten_nha_cung_cap.required' => 'Tên nhà cung cấp là bắt buộc',
            'ten_nha_cung_cap.max' => 'Tên nhà cung cấp không được vượt quá 255 ký tự',
            'ten_nguoi_lien_he.required' => 'Tên người liên hệ là bắt buộc',
            'ten_nguoi_lien_he.max' => 'Tên người liên hệ không được vượt quá 255 ký tự',
            'so_dien_thoai.required' => 'Số điện thoại là bắt buộc',
            'so_dien_thoai.max' => 'Số điện thoại không được vượt quá 15 ký tự',
            'so_dien_thoai.regex' => 'Số điện thoại không đúng định dạng',
            'dia_chi.required' => 'Địa chỉ là bắt buộc',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email này đã được sử dụng',
            'ma_so_thue.required' => 'Mã số thuế là bắt buộc',
            'ma_so_thue.max' => 'Mã số thuế không được vượt quá 50 ký tự',
            'mo_ta.required' => 'Mô tả là bắt buộc',
            'trang_thai.in' => 'Trạng thái phải là hoat_dong hoặc ngung_hoat_dong',
        ];
    }
}
