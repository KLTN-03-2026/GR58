<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePhanLoaiBaiVietRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by middleware (EnsureAdmin). Return true here.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ten_phan_loai' => 'required|string|max:255|unique:phan_loai_bai_viets,ten_phan_loai',
            'mo_ta' => 'nullable|string',
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'ten_phan_loai.required' => 'Tên phân loại bài viết là bắt buộc.',
            'ten_phan_loai.string' => 'Tên phân loại bài viết phải là chuỗi ký tự.',
            'ten_phan_loai.max' => 'Tên phân loại bài viết không được dài hơn :max ký tự.',
            'ten_phan_loai.unique' => 'Tên phân loại bài viết đã tồn tại.',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự.',
        ];
    }

    /**
     * Attributes names (optional, used in messages).
     */
    public function attributes(): array
    {
        return [
            'ten_phan_loai' => 'tên phân loại bài viết',
            'mo_ta' => 'mô tả',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
