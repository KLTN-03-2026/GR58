<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhieuKhamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nhiet_do' => 'nullable|numeric|between:30,45',
            'can_nang' => 'nullable|numeric|min:0',
            'nhip_tim' => 'nullable|integer|between:30,200',
            'nhip_tho' => 'nullable|integer|between:5,50',
            'ly_do_den_kham' => 'nullable|string|max:255',
            'trieu_chung' => 'nullable|string',
            'chan_doan' => 'nullable|string',
            'ghi_chu' => 'nullable|string',
            'loai_chi_dinh' => 'nullable|in:chi_dinh_can_lam_sang,don_thuoc,hen_tai_kham',
            'ket_qua_can_lam_sang' => 'nullable|string',
            'tep_dinh_kem_can_lam_sang' => 'nullable|array',
            'tep_dinh_kem_can_lam_sang.*.name' => 'nullable|string|max:255',
            'tep_dinh_kem_can_lam_sang.*.url' => 'nullable|string',
            'tep_dinh_kem_can_lam_sang.*.type' => 'nullable|string|max:100',
            'thoi_gian_tra_ket_qua' => 'nullable|date',
        ];
    }
}
