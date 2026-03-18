<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBaiVietRequest extends FormRequest
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
            'ten_bai_viet' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'mo_ta' => 'nullable|string',
            'anh_bai_viet' => 'nullable|string',
            'trang_thai' => 'nullable|in:published,hidden',
            'phan_loai_bai_viet_id' => 'nullable|exists:phan_loai_bai_viets,id',
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'ten_bai_viet.required' => 'Tên bài viết là bắt buộc.',
            'ten_bai_viet.string' => 'Tên bài viết phải là chuỗi ký tự.',
            'ten_bai_viet.max' => 'Tên bài viết không được dài hơn :max ký tự.',
            'noi_dung.required' => 'Nội dung bài viết là bắt buộc.',
            'noi_dung.string' => 'Nội dung bài viết phải là chuỗi ký tự.',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự.',
            'anh_bai_viet.string' => 'Đường dẫn ảnh phải là chuỗi ký tự.',
            'trang_thai.in' => 'Trạng thái phải là "published" hoặc "hidden".',
            'phan_loai_bai_viet_id.exists' => 'Phân loại bài viết không tồn tại.',
        ];
    }

    /**
     * Attributes names (optional, used in messages).
     */
    public function attributes(): array
    {
        return [
            'ten_bai_viet' => 'tên bài viết',
            'noi_dung' => 'nội dung bài viết',
            'mo_ta' => 'mô tả',
            'anh_bai_viet' => 'ảnh bài viết',
            'trang_thai' => 'trạng thái',
            'phan_loai_bai_viet_id' => 'phân loại bài viết',
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
