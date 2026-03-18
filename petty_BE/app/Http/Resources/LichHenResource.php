<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LichHenResource extends JsonResource
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
            'ngay_gio' => $this->ngay_gio,
            'ngay_hen' => $this->ngay_hen,
            'gio_hen' => $this->gio_hen,
            'trang_thai' => $this->trang_thai,
            'ghi_chu' => $this->ghi_chu,
            'huong_dan' => $this->huong_dan,
            'thoi_gian_checkin' => $this->thoi_gian_checkin ? $this->thoi_gian_checkin->format('Y-m-d H:i:s') : null,
            'thoi_gian_bat_dau_kham' => $this->thoi_gian_bat_dau_kham ? $this->thoi_gian_bat_dau_kham->format('Y-m-d H:i:s') : null,
            'thoi_gian_hoan_thanh' => $this->thoi_gian_hoan_thanh ? $this->thoi_gian_hoan_thanh->format('Y-m-d H:i:s') : null,
            'khach_hang' => $this->whenLoaded('khachHang', function () {
                return [
                    'id' => $this->khachHang->id,
                    'full_name' => $this->khachHang->full_name ?? $this->khachHang->ho_ten ?? data_get($this->khachHang, 'ten'),
                    'so_dien_thoai' => $this->khachHang->so_dien_thoai,
                ];
            }),
            'thu_cung' => $this->whenLoaded('thuCung', function () {
                return [
                    'id' => $this->thuCung->id,
                    'ten_thu_cung' => $this->thuCung->ten_thu_cung ?? $this->thuCung->ten,
                    'giong' => $this->thuCung->giong,
                    'can_nang' => $this->thuCung->can_nang,
                ];
            }),
            'dich_vu' => $this->whenLoaded('dichVu', function () {
                return [
                    'id' => $this->dichVu->id,
                    'ten_dich_vu' => $this->dichVu->ten_dich_vu ?? $this->dichVu->ten,
                ];
            }),
            'nhan_vien' => $this->whenLoaded('nhanVien', function () {
                return [
                    'id' => $this->nhanVien->id,
                    'full_name' => $this->nhanVien->full_name ?? $this->nhanVien->ho_ten,
                    'chuc_danh' => $this->nhanVien->chuc_danh,
                ];
            }),
            'y_ta_checkin' => $this->whenLoaded('yTaCheckin', function () {
                return [
                    'id' => $this->yTaCheckin->id,
                    'full_name' => $this->yTaCheckin->full_name ?? $this->yTaCheckin->ho_ten,
                    'chuc_danh' => $this->yTaCheckin->chuc_danh,
                ];
            }),
            'thanh_toan' => $this->whenLoaded('thanhToan', function () {
                return $this->thanhToan;
            }),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
