<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDinhKemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required'  => 'Vui lòng chọn file để tải lên.',
            'file.file'      => 'Dữ liệu gửi lên phải là file hợp lệ.',
            'file.mimes'     => 'Chỉ chấp nhận file JPEG, PNG hoặc PDF.',
            'file.max'       => 'Kích thước file không được vượt quá 10 MB.',
        ];
    }
}
