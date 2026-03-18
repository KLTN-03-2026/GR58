<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhieuChiRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'loai_phieu_chi' => 'nullable|in:chi_nhap_hang,chi_van_hanh',
            'ly_do_chi' => 'nullable|string',
            'tong_so_tien' => 'nullable|numeric|min:0',
            'so_tien_thanh_toan_ngay' => 'nullable|numeric|min:0',
            'tien_mat' => 'nullable|numeric|min:0',
            'tien_chuyen_khoan' => 'nullable|numeric|min:0',
            'doi_tuong_nhan_tien' => 'nullable|string',
            'nha_cung_cap_id' => 'nullable|exists:nha_cung_caps,id',
            'ngay_chi' => 'nullable|date',
            'ghi_chu' => 'nullable|string',
            'anh_chung_tu' => 'nullable|array',
            'anh_chung_tu.*' => 'nullable|string',
        ];
    }
}
