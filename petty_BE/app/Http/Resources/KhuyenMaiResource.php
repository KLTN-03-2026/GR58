<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KhuyenMaiResource extends JsonResource
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
            'ten_khuyen_mai' => $this->ten_khuyen_mai,
            'mo_ta' => $this->mo_ta,
            'loai_khuyen_mai' => $this->loai_khuyen_mai,
            'ma_code' => $this->ma_code,
            'gia_tri_don_toi_thieu' => $this->gia_tri_don_toi_thieu,
            'loai_khach_hang' => $this->loai_khach_hang,
            'hinh_thuc_giam' => $this->hinh_thuc_giam,
            'giam_toi_da' => $this->giam_toi_da,
            'gia_tri_giam' => $this->gia_tri_giam,
            'tu_ngay' => $this->tu_ngay,
            'den_ngay' => $this->den_ngay,
            'tong_so_luong' => $this->tong_so_luong,
            'so_luong_da_dung' => $this->so_luong_da_dung,
            'gioi_han_moi_khach' => $this->gioi_han_moi_khach,
            'trang_thai' => $this->trang_thai,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Dịch vụ áp dụng - Đảm bảo tên dịch vụ được trả về
            'dich_vus' => $this->whenLoaded('dichVus', function () {
                return $this->dichVus->map(function ($dichVu) {
                    return [
                        'id' => $dichVu->id,
                        'ten' => $dichVu->ten,
                        'ma_dich_vu' => $dichVu->ma_dich_vu,
                        'gia_tien' => $dichVu->gia_tien ?? null,
                    ];
                });
            }),

            // Thêm thông tin bổ sung
            'is_active' => $this->isActive(),
            'has_quantity_available' => $this->hasQuantityAvailable(),
        ];
    }
}
