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
        return [
            'ngay_gio' => ['required', 'date'],
            'dia_chi' => ['nullable', 'string', 'max:500'],
            'ghi_chu' => ['nullable', 'string'],
            'huong_dan' => ['nullable', 'string'],
            'trang_thai' => ['nullable', 'string', 'in:pending,confirmed,completed,cancelled'],
            'thu_cung_id' => ['required', 'exists:thu_cungs,id'],
            'dich_vu_id' => ['required', 'exists:dich_vus,id'],
            'nhan_vien_id' => ['nullable', 'exists:nhan_viens,id'],
            'thanh_toan_id' => ['nullable', 'exists:thanh_toans,id'],
        ];
    }
}
