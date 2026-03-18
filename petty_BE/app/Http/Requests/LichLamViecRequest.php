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
            'ngay_lam' => 'required|date',
            'phong_truc' => 'required|string|max:255',
            'thoi_gian_truc' => 'required|in:ca_sang,ca_chieu,ca_toi',
            'nhan_vien_id' => 'required|exists:nhan_viens,id',
        ];
    }
}
