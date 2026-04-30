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
            'tong_tien' => $this->tong_tien,
            'da_thanh_toan' => $this->da_thanh_toan,
            'phuong_thuc_thanh_toan' => $this->phuong_thuc_thanh_toan,
            'thoi_gian_thanh_toan' => $this->thoi_gian_thanh_toan,
            'khach_hang' => $this->whenLoaded('khachHang', function () {
                return [
                    'id'       => $this->khachHang->id,
                    'full_name'=> $this->khachHang->full_name ?? $this->khachHang->ho_ten,
                    'phone'    => $this->khachHang->phone ?? $this->khachHang->so_dien_thoai,
                    'rank'     => $this->khachHang->rank,
                ];
            }),
            'thu_cung' => $this->whenLoaded('thuCung', function () {
                return [
                    'id'             => $this->thuCung->id,
                    'ten_thu_cung'   => $this->thuCung->ten_thu_cung ?? $this->thuCung->ten,
                    'giong_thu_cung' => $this->thuCung->giong_thu_cung,
                    'tuoi_thu_cung'  => $this->thuCung->tuoi_thu_cung
                        ? $this->thuCung->tuoi_thu_cung->format('Y-m-d')
                        : null,
                    'can_nang'       => $this->thuCung->can_nang,
                ];
            }),
            'dich_vu' => $this->whenLoaded('dichVu', function () {
                return [
                    'id'         => $this->dichVu->id,
                    'ten_dich_vu'=> $this->dichVu->ten_dich_vu ?? $this->dichVu->ten,
                    'ten'        => $this->dichVu->ten,
                    'gia_tien'   => (float) $this->dichVu->gia_tien,
                    'mo_ta'      => $this->dichVu->mo_ta,
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
