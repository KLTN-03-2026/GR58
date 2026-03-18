<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DanhMucHangHoaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Adjust authorization as needed (e.g., check admin)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ten_danh_muc_hang_hoa' => ['required', 'string', 'max:255'],
            'mo_ta' => ['nullable', 'string'],
        ];
    }

    /**
     * Custom messages for validation errors (Vietnamese).
     */
    public function messages(): array
    {
        return [
            'ten_danh_muc_hang_hoa.required' => 'Vui lòng nhập tên danh mục hàng hóa.',
            'ten_danh_muc_hang_hoa.string' => 'Tên danh mục phải là chuỗi ký tự.',
            'ten_danh_muc_hang_hoa.max' => 'Tên danh mục không được dài hơn :max ký tự.',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự.',
        ];
    }
}
