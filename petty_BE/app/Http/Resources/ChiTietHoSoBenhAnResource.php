<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChiTietHoSoBenhAnResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'thu_cung' => [
                'id' => $this->resource['thu_cung']->id,
                'ten_thu_cung' => $this->resource['thu_cung']->ten_thu_cung,
                'loai' => $this->resource['thu_cung']->loai,
                'giong' => $this->resource['thu_cung']->giong,
                'tuoi' => $this->resource['thu_cung']->tuoi,
                'can_nang' => $this->resource['thu_cung']->can_nang,
                'anh_dai_dien' => $this->resource['thu_cung']->anh_dai_dien,
            ],
            'khach_hang' => [
                'id' => $this->resource['khach_hang']->id,
                'ho_ten' => $this->resource['khach_hang']->ho_ten,
                'so_dien_thoai' => $this->resource['khach_hang']->so_dien_thoai,
            ],
            'ho_so_benh_an' => $this->resource['ho_so_benh_an'] ? [
                'id' => $this->resource['ho_so_benh_an']->id,
                'ma_benh_an' => $this->resource['ho_so_benh_an']->ma_benh_an,
                'noi_dung' => $this->resource['ho_so_benh_an']->noi_dung,
            ] : null,
            'phieu_khams' => $this->resource['phieu_khams']->map(function ($phieuKham) {
                return [
                    'id' => $phieuKham->id,
                    'created_at' => $phieuKham->created_at,
                    'ly_do_den_kham' => $phieuKham->ly_do_den_kham,
                    'trieu_chung' => $phieuKham->trieu_chung,
                    'chan_doan' => $phieuKham->chan_doan,
                    'ket_qua_can_lam_sang' => $phieuKham->ket_qua_can_lam_sang,
                    'ghi_chu' => $phieuKham->ghi_chu,
                    'nhan_vien' => [
                        'id' => $phieuKham->nhanVien->id,
                        'ho_ten' => $phieuKham->nhanVien->ho_ten,
                    ],
                    'don_thuoc' => ChiTietPhieuKhamResource::collection($phieuKham->chiTietPhieuKhams),
                    'dinh_kem' => $phieuKham->dinhKems->map(function ($dinhKem) {
                        return [
                            'id' => $dinhKem->id,
                            'loai_tep' => $dinhKem->loai_tep,
                            'duong_dan' => $dinhKem->duong_dan,
                            'ten_tep' => $dinhKem->ten_tep,
                        ];
                    }),
                    'lich_nhacs' => LichNhacResource::collection($phieuKham->lichNhacs ?? []),
                ];
            }),
            'lich_nhacs_dang_cho' => LichNhacResource::collection($this->resource['lich_nhacs_dang_cho'] ?? []),
        ];
    }
}
