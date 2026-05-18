<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffCreatePetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_thu_cung' => 'required|string|max:191',
            'loai_thu_cung' => 'required|string|max:50',
            'giong_thu_cung' => 'nullable|string|max:100',
            'tuoi_thu_cung' => 'nullable|date',
            'gioi_tinh' => 'nullable|in:duc,cai',
            'can_nang' => 'nullable|numeric|min:0',
            'anh_dai_dien' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_thu_cung.required' => 'Vui lòng nhập tên thú cưng.',
            'ten_thu_cung.max' => 'Tên thú cưng không được vượt quá 191 ký tự.',
            'loai_thu_cung.required' => 'Vui lòng chọn loại thú cưng.',
            'loai_thu_cung.max' => 'Loại thú cưng không được vượt quá 50 ký tự.',
            'giong_thu_cung.max' => 'Giống thú cưng không được vượt quá 100 ký tự.',
            'tuoi_thu_cung.date' => 'Ngày sinh không hợp lệ.',
            'gioi_tinh.in' => 'Giới tính phải là "đực" hoặc "cái".',
            'can_nang.numeric' => 'Cân nặng phải là số.',
            'can_nang.min' => 'Cân nặng phải lớn hơn hoặc bằng 0.',
        ];
    }
}
