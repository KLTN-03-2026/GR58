<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChiTietPhieuKhamResource extends JsonResource
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
            'hang_hoa' => [
                'id' => $this->hangHoa->id,
                'ten_mat_hang' => $this->hangHoa->ten_mat_hang,
                'gia_ban' => $this->hangHoa->gia_ban,
                'ton_kho' => $this->hangHoa->ton_kho ?? 0,
            ],
            'so_luong' => $this->so_luong,
            'don_vi' => $this->don_vi,
            'lieu_su_dung' => $this->lieu_su_dung,
            'tan_suat' => $this->tan_suat,
            'thoi_gian_dung' => $this->thoi_gian_dung,
            'ghi_chu' => $this->ghi_chu,
        ];
    }
}
