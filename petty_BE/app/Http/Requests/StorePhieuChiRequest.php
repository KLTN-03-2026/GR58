<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhieuChiRequest extends FormRequest
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
            'loai_phieu_chi' => 'required|in:chi_nhap_hang,chi_van_hanh',
            'ly_do_chi' => 'required|string',
            'tong_so_tien' => 'required|numeric|min:0',
            'so_tien_thanh_toan_ngay' => 'nullable|numeric|min:0',
            'tien_mat' => 'nullable|numeric|min:0',
            'tien_chuyen_khoan' => 'nullable|numeric|min:0',
            'doi_tuong_nhan_tien' => 'required_if:loai_phieu_chi,chi_van_hanh|string|nullable',
            'nha_cung_cap_id' => 'required_if:loai_phieu_chi,chi_nhap_hang|exists:nha_cung_caps,id|nullable',
            'ngay_chi' => 'required|date',
            'ghi_chu' => 'nullable|string',
            'anh_chung_tu' => 'nullable|array',
            'anh_chung_tu.*' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'loai_phieu_chi.required' => 'Vui lòng chọn loại phiếu chi',
            'loai_phieu_chi.in' => 'Loại phiếu chi không hợp lệ',
            'ly_do_chi.required' => 'Vui lòng nhập lý do chi',
            'tong_so_tien.required' => 'Vui lòng nhập tổng số tiền',
            'tong_so_tien.numeric' => 'Tổng số tiền phải là số',
            'tong_so_tien.min' => 'Tổng số tiền phải lớn hơn hoặc bằng 0',
            'doi_tuong_nhan_tien.required_if' => 'Vui lòng nhập đối tượng nhận tiền khi chi vận hành',
            'nha_cung_cap_id.required_if' => 'Vui lòng chọn nhà cung cấp khi chi nhập hàng',
            'nha_cung_cap_id.exists' => 'Nhà cung cấp không tồn tại',
            'ngay_chi.required' => 'Vui lòng chọn ngày chi',
            'ngay_chi.date' => 'Ngày chi không hợp lệ',
        ];
    }
}
