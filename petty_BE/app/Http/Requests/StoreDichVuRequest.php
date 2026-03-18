<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\DichVu;

class StoreDichVuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization handled by EnsureAdmin middleware
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $statusValues = implode(',', [DichVu::STATUS_KINH_DOANH, DichVu::STATUS_NGUNG]);

        return [
            'ten' => 'required|string|max:255',
            'gia_tien' => 'required|numeric|min:0',
            'thoi_gian_thuc_hien' => 'nullable|integer|min:0',
            'mo_ta' => 'nullable|string',
            'ma_dich_vu' => 'nullable|string|max:100|unique:dich_vus,ma_dich_vu',
            'huong_dan' => 'nullable|string',
            'anh_dich_vu' => 'nullable|string',
            'trang_thai' => 'required|in:' . $statusValues,
            'danh_muc_id' => 'nullable|exists:danh_muc_dich_vus,id',
        ];
    }

    public function messages(): array
    {
        return [
            'ten.required' => 'Tên dịch vụ là bắt buộc.',
            'gia_tien.required' => 'Giá tiền là bắt buộc.',
            'gia_tien.numeric' => 'Giá tiền phải là số.',
            'ma_dich_vu.unique' => 'Mã dịch vụ đã tồn tại.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
            'danh_muc_id.exists' => 'Danh mục dịch vụ không tồn tại.',
        ];
    }
}
