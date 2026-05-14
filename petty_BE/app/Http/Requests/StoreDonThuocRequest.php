<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonThuocRequest extends FormRequest
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
            'items' => 'required|array|min:1',
            'items.*.hang_hoa_id' => 'required|exists:hang_hoas,id',
            'items.*.so_luong' => 'required|integer|min:1',
            'items.*.don_vi' => 'nullable|string|max:50',
            'items.*.lieu_su_dung' => 'nullable|string|max:100',
            'items.*.tan_suat' => 'nullable|string|max:100',
            'items.*.thoi_gian_dung' => 'nullable|string|max:100',
            'items.*.ghi_chu' => 'nullable|string|max:1000',
        ];
    }
}
