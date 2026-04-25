<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use App\Models\LichNghi;
use Carbon\Carbon;

class StoreLichNghiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ngay_nghi' => ['required', 'date'],
            'ly_do'     => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function passedValidation(): void
    {
        $user     = $this->user();
        $ngayNghi = Carbon::parse($this->ngay_nghi);

        // Không trùng ngày đã đăng ký (bất kể trạng thái)
        $exists = LichNghi::where('nhan_vien_id', $user->id)
            ->where('ngay_nghi', $ngayNghi->toDateString())
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'ngay_nghi' => ['Ngày nghỉ này đã được đăng ký'],
            ]);
        }
    }
}
