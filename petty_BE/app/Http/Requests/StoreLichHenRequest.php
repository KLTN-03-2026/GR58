<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLichHenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // route is protected by auth:sanctum in routes, so allow here
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = $this->user();
        $isStaff = $user && !($user instanceof \App\Models\KhachHang);

        $rules = [
            'ngay_gio' => ['required', 'date'],
            'dia_chi' => ['nullable', 'string', 'max:500'],
            'ghi_chu' => ['nullable', 'string'],
            'huong_dan' => ['nullable', 'string'],
            'trang_thai' => ['nullable', 'string', 'in:pending,confirmed,completed,cancelled'],
            'thu_cung_id' => ['required', 'exists:thu_cungs,id'],
            'dich_vu_ids' => ['required', 'array', 'min:1'],
            'dich_vu_ids.*' => ['required', 'exists:dich_vus,id'],
            'nhan_vien_id' => ['nullable', 'exists:nhan_viens,id'],
            'thanh_toan_id' => ['nullable', 'exists:thanh_toans,id'],
            'phuong_thuc_thanh_toan' => ['nullable', 'string', 'in:online,offline'],
        ];

        // Staff must provide khach_hang_id for walk-in appointments
        if ($isStaff) {
            $rules['khach_hang_id'] = ['required', 'exists:khach_hangs,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'khach_hang_id.required' => 'Vui lòng chọn khách hàng.',
            'khach_hang_id.exists' => 'Khách hàng không tồn tại.',
        ];
    }
}
