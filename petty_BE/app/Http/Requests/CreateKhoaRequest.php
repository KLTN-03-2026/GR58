<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Lang;

class CreateKhoaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * For now allow; enforce auth via route middleware.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ma_khoa' => 'required|string|max:50|unique:khoas,ma_khoa',
            'ten_khoa' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'required|integer|in:0,1',
        ];
    }

    public function messages()
    {
        return [
            'ma_khoa.required' => Lang::get('messages.ma_khoa_required'),
            'ma_khoa.unique' => Lang::get('messages.ma_khoa_unique'),
            'ma_khoa.max' => Lang::get('messages.ma_khoa_max'),
            'ma_khoa.string' => Lang::get('messages.ma_khoa_string'),

            'ten_khoa.required' => Lang::get('messages.ten_khoa_required'),
            'ten_khoa.max' => Lang::get('messages.ten_khoa_max'),
            'ten_khoa.string' => Lang::get('messages.ten_khoa_string'),

            'mo_ta.string' => Lang::get('messages.mo_ta_string'),

            'trang_thai.required' => Lang::get('messages.trang_thai_required'),
            'trang_thai.in' => Lang::get('messages.trang_thai_in'),
        ];
    }

    /**
     * Handle a failed validation attempt and return project-consistent JSON.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'status' => false,
            'message' => Lang::get('messages.validation_failed'),
            'errors' => $validator->errors(),
        ], 422);

        throw new HttpResponseException($response);
    }
}
