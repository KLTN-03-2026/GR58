<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDanhMucDichVuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by middleware (EnsureAdmin). Return true here.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ten_nhom' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'ten_nhom.required' => 'Tên nhóm là bắt buộc.',
            'ten_nhom.string' => 'Tên nhóm phải là chuỗi ký tự.',
            'ten_nhom.max' => 'Tên nhóm không được dài hơn :max ký tự.',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự.',
        ];
    }

    /**
     * Attributes names (optional, used in messages).
     */
    public function attributes(): array
    {
        return [
            'ten_nhom' => 'tên nhóm',
            'mo_ta' => 'mô tả',
        ];
    }
}
