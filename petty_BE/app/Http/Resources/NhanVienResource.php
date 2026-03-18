<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NhanVienResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name ?? $this->ho_ten ?? data_get($this, 'ten_nhan_vien'),
            'email' => $this->email,
            'so_dien_thoai' => $this->so_dien_thoai,
            'vai_tro' => $this->vai_tro,
            'chuc_danh' => $this->chuc_danh,
            'phong_ban' => $this->phong_ban,
            'chuyen_mon' => $this->chuyen_mon,
            'trang_thai' => $this->trang_thai,
            'anh_dai_dien' => $this->anh_dai_dien,
        ];
    }
}
