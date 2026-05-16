<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhieuKhamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lich_hen_id' => 'required|exists:lich_hens,id',
            'nhiet_do' => 'nullable|numeric|between:30,45',
            'can_nang' => 'nullable|numeric|min:0',
            'nhip_tim' => 'nullable|integer|between:30,200',
            'nhip_tho' => 'nullable|integer|between:5,50',
            'ly_do_den_kham' => 'nullable|string|max:255',
            'trieu_chung' => 'nullable|string',
            'chan_doan' => 'nullable|string',
            'ghi_chu' => 'nullable|string',
            'loai_chi_dinh' => 'required|in:chi_dinh_can_lam_sang,don_thuoc,hen_tai_kham',
            'don_thuoc' => 'nullable|array',
            'don_thuoc.*.ten' => 'required|string',
            'don_thuoc.*.so_luong' => 'required|numeric|min:1',
            'don_thuoc.*.don_gia' => 'required|numeric|min:0',
            'don_thuoc.*.don_vi' => 'nullable|string',
            'don_thuoc.*.lieu_dung' => 'nullable|string',
            'don_thuoc.*.ghi_chu' => 'nullable|string',
        ];
    }
}
