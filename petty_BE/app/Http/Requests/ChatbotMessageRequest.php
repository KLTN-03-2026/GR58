<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['nullable', 'string', 'max:1000', 'required_without:images'],
            'images' => ['nullable', 'array', 'max:4', 'required_without:message'],
            'images.*' => ['required_with:images', 'string', 'starts_with:data:image/'],
            'history' => ['nullable', 'array', 'max:10'],
            'history.*.role' => ['required_with:history', 'string', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:1000'],
            'history.*.images' => ['nullable', 'array', 'max:4'],
            'history.*.images.*' => ['required_with:history.*.images', 'string', 'starts_with:data:image/'],
        ];
    }
}
