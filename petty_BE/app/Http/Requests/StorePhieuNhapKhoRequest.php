<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhieuNhapKhoRequest extends FormRequest
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
            'ma_phieu_nhap' => 'required|string|max:50|unique:phieu_nhap_khos,ma_phieu_nhap',
            'ngay_nhap' => 'required|date',
            'ghi_chu' => 'nullable|string',
            'trang_thai' => 'sometimes|in:cho_duyet,da_duyet,huy',
            'nha_cung_cap_id' => 'required|exists:nha_cung_caps,id',

            // Phiếu chi
            'phieu_chi.ma_phieu_chi' => 'required|string|max:50|unique:phieu_chis,ma_phieu_chi',
            'phieu_chi.ngay_chi' => 'required|date',
            'phieu_chi.ly_do' => 'required|string',
            'phieu_chi.nguoi_nhan' => 'required|string',
            'phieu_chi.ghi_chu' => 'nullable|string',

            // Chi tiết phiếu nhập kho (array)
            'chi_tiet' => 'required|array|min:1',
            'chi_tiet.*.hang_hoa_id' => 'required|exists:hang_hoas,id',
            'chi_tiet.*.so_luong' => 'required|integer|min:1',
            'chi_tiet.*.so_lo' => 'required|string|max:50',
            'chi_tiet.*.han_su_dung' => 'required|date|after:today',
            'chi_tiet.*.don_gia' => 'required|numeric|min:0',
            'chi_tiet.*.ghi_chu' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ma_phieu_nhap.required' => 'Mã phiếu nhập là bắt buộc',
            'ma_phieu_nhap.unique' => 'Mã phiếu nhập đã tồn tại',
            'ngay_nhap.required' => 'Ngày nhập là bắt buộc',
            'ngay_nhap.date' => 'Ngày nhập không đúng định dạng',
            'nha_cung_cap_id.required' => 'Nhà cung cấp là bắt buộc',
            'nha_cung_cap_id.exists' => 'Nhà cung cấp không tồn tại',

            'phieu_chi.ma_phieu_chi.required' => 'Mã phiếu chi là bắt buộc',
            'phieu_chi.ma_phieu_chi.unique' => 'Mã phiếu chi đã tồn tại',
            'phieu_chi.ngay_chi.required' => 'Ngày chi là bắt buộc',
            'phieu_chi.ly_do.required' => 'Lý do chi là bắt buộc',
            'phieu_chi.nguoi_nhan.required' => 'Người nhận là bắt buộc',

            'chi_tiet.required' => 'Chi tiết phiếu nhập là bắt buộc',
            'chi_tiet.array' => 'Chi tiết phiếu nhập phải là một mảng',
            'chi_tiet.min' => 'Phải có ít nhất một mặt hàng',
            'chi_tiet.*.hang_hoa_id.required' => 'Hàng hóa là bắt buộc',
            'chi_tiet.*.hang_hoa_id.exists' => 'Hàng hóa không tồn tại',
            'chi_tiet.*.so_luong.required' => 'Số lượng là bắt buộc',
            'chi_tiet.*.so_luong.min' => 'Số lượng phải lớn hơn 0',
            'chi_tiet.*.so_lo.required' => 'Số lô là bắt buộc',
            'chi_tiet.*.han_su_dung.required' => 'Hạn sử dụng là bắt buộc',
            'chi_tiet.*.han_su_dung.after' => 'Hạn sử dụng phải sau ngày hôm nay',
            'chi_tiet.*.don_gia.required' => 'Đơn giá là bắt buộc',
            'chi_tiet.*.don_gia.min' => 'Đơn giá phải lớn hơn hoặc bằng 0',
        ];
    }
}
