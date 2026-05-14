<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLichNhacRequest extends FormRequest
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
            'phan_loai' => 'required|in:tai_kham,tiem_phong,xet_nghiem,thuoc,cham_soc,khac',
            'ngay_nhac' => 'required|date|after_or_equal:today',
            'noi_dung' => 'required|string|max:1000',
            'ghi_chu' => 'nullable|string|max:1000',
        ];
    }
}
