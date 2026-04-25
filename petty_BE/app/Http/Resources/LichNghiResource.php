<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LichNghiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'nhan_vien'      => $this->whenLoaded('nhanVien', fn () => [
                'id'       => $this->nhanVien->id,
                'full_name'=> $this->nhanVien->full_name,
                'chuc_danh'=> $this->nhanVien->chuc_danh,
                'vai_tro'  => $this->nhanVien->vai_tro,
                'avatar'   => $this->nhanVien->anh_dai_dien,
            ]),
            'ngay_nghi'      => $this->ngay_nghi?->toDateString(),
            'ly_do'          => $this->ly_do,
            'trang_thai'     => $this->trang_thai,
            'loai_nghi'      => $this->loai_nghi,   // co_luong | khong_luong
            'ly_do_tu_choi'  => $this->ly_do_tu_choi,
            'created_at'     => $this->created_at?->toDateTimeString(),
        ];
    }
}
