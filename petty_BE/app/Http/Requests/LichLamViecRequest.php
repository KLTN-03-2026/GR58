<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LichLamViecRequest extends FormRequest
{
    public function authorize(): bool
    {
        // allow authenticated users; route is protected by auth:sanctum
        return true;
    }

    public function rules(): array
    {
        return [
            'ngay_lam'       => 'required|date',
            'thoi_gian_truc' => 'required|in:ca_sang,ca_chieu,ca_toi',
            'nhan_vien_id'   => 'required|exists:nhan_viens,id',
            'phong_truc'     => 'nullable|string|max:255',
            'ghi_chu'        => 'nullable|string',
        ];
    }
}
