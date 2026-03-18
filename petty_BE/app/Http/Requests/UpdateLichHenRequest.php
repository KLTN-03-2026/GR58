<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLichHenRequest extends FormRequest
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
        // Combined rules for different update methods (update, updateTime, patch)
        return [
            'nhan_vien_id' => 'nullable|exists:nhan_viens,id',
            'trang_thai' => 'nullable|string|in:Chưa xác nhận,Đã xác nhận,Đã hoàn thành,Đã hủy,pending,confirmed,in-progress,completed,cancelled',
            'ghi_chu' => 'nullable|string',
            'huong_dan' => 'nullable|string',
            'thu_cung_id' => 'nullable|exists:thu_cungs,id',
            'dich_vu_id' => 'nullable|exists:dich_vus,id',
            'ngay_gio' => 'nullable|date',
            'ly_do_huy' => 'nullable|string',
        ];
    }
}
