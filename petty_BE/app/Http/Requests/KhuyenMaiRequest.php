<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KhuyenMaiRequest extends FormRequest
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
        $khuyenMaiId = $this->route('khuyen_mai') ? $this->route('khuyen_mai')->id : null;

        return [
            'ten_khuyen_mai' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'loai_khuyen_mai' => ['required', Rule::in(['ma_giam_gia', 'chuong_trinh_tu_dong'])],

            // Mã code - required nếu là mã giảm giá
            'ma_code' => [
                Rule::requiredIf(function () {
                    return $this->input('loai_khuyen_mai') === 'ma_giam_gia';
                }),
                'nullable',
                'string',
                'max:50',
                Rule::unique('khuyen_mais', 'ma_code')->ignore($khuyenMaiId),
            ],

            'gia_tri_don_toi_thieu' => 'nullable|numeric|min:0',
            'loai_khach_hang' => ['required', Rule::in(['tat_ca', 'vip'])],
            'hinh_thuc_giam' => ['required', Rule::in(['phan_tram', 'so_tien'])],

            // Giảm tối đa - required nếu giảm theo phần trăm
            'giam_toi_da' => [
                Rule::requiredIf(function () {
                    return $this->input('hinh_thuc_giam') === 'phan_tram';
                }),
                'nullable',
                'numeric',
                'min:0',
            ],

            'gia_tri_giam' => 'required|numeric|min:0',
            'tu_ngay' => 'required|date',
            'den_ngay' => 'required|date|after:tu_ngay',
            'tong_so_luong' => 'nullable|integer|min:1',
            'gioi_han_moi_khach' => 'nullable|integer|min:1',
            'trang_thai' => ['nullable', Rule::in(['dang_chay', 'da_ket_thuc'])],

            // Dịch vụ áp dụng
            'dich_vu_ids' => 'nullable|array',
            'dich_vu_ids.*' => 'exists:dich_vus,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ten_khuyen_mai.required' => 'Vui lòng nhập tên khuyến mãi',
            'loai_khuyen_mai.required' => 'Vui lòng chọn loại khuyến mãi',
            'loai_khuyen_mai.in' => 'Loại khuyến mãi không hợp lệ',
            'ma_code.required' => 'Vui lòng nhập mã code cho mã giảm giá',
            'ma_code.unique' => 'Mã code đã tồn tại',
            'loai_khach_hang.required' => 'Vui lòng chọn loại khách hàng',
            'loai_khach_hang.in' => 'Loại khách hàng không hợp lệ',
            'hinh_thuc_giam.required' => 'Vui lòng chọn hình thức giảm',
            'hinh_thuc_giam.in' => 'Hình thức giảm không hợp lệ',
            'giam_toi_da.required' => 'Vui lòng nhập giá trị giảm tối đa khi chọn giảm theo phần trăm',
            'gia_tri_giam.required' => 'Vui lòng nhập giá trị giảm',
            'gia_tri_giam.min' => 'Giá trị giảm phải lớn hơn 0',
            'tu_ngay.required' => 'Vui lòng chọn ngày bắt đầu',
            'den_ngay.required' => 'Vui lòng chọn ngày kết thúc',
            'den_ngay.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
            'dich_vu_ids.array' => 'Danh sách dịch vụ không hợp lệ',
            'dich_vu_ids.*.exists' => 'Dịch vụ không tồn tại',
        ];
    }
}
