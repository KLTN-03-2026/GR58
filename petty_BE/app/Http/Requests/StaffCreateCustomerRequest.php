<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffCreateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/\D+/', '', (string) $this->phone)
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:191',
            'phone' => ['required', 'regex:/^[0-9]{10}$/', 'unique:khach_hangs,phone'],
            'email' => ['nullable', 'email', 'unique:khach_hangs,email'],
            'address' => 'nullable|string|max:255',
            'anh_dai_dien' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Vui lòng nhập tên khách hàng.',
            'full_name.max' => 'Tên khách hàng không được vượt quá 191 ký tự.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại phải có 10 chữ số.',
            'phone.unique' => 'Số điện thoại này đã được đăng ký.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được đăng ký.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
        ];
    }
}
