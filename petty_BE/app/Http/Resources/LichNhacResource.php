<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LichNhacResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ngay_nhac' => $this->ngay_nhac,
            'phan_loai' => $this->phan_loai,
            'phan_loai_label' => \App\Models\LichNhac::PHAN_LOAI[$this->phan_loai] ?? $this->phan_loai,
            'noi_dung' => $this->noi_dung,
            'ghi_chu' => $this->ghi_chu,
            'trang_thai' => $this->trang_thai,
            'ho_so_benh_an_id' => $this->ho_so_benh_an_id,
        ];
    }
}
