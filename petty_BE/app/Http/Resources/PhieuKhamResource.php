<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhieuKhamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nhiet_do' => $this->nhiet_do,
            'can_nang' => $this->can_nang,
            'nhip_tim' => $this->nhip_tim,
            'nhip_tho' => $this->nhip_tho,
            'ly_do_den_kham' => $this->ly_do_den_kham,
            'trieu_chung' => $this->trieu_chung,
            'chan_doan' => $this->chan_doan,
            'ghi_chu' => $this->ghi_chu,
            'loai_chi_dinh' => $this->loai_chi_dinh,
            'ket_qua_can_lam_sang' => $this->ket_qua_can_lam_sang,
            'tep_dinh_kem_can_lam_sang' => $this->tep_dinh_kem_can_lam_sang ?? [],
            'thoi_gian_tra_ket_qua' => $this->thoi_gian_tra_ket_qua
                ? $this->thoi_gian_tra_ket_qua->format('Y-m-d H:i:s')
                : null,
            
            'lich_hen' => new LichHenResource($this->whenLoaded('lichHen')),
            'nhan_vien' => new NhanVienResource($this->whenLoaded('nhanVien')),
            
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
