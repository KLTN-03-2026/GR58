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
        $tongTienDichVu = 0;
        if ($this->relationLoaded('dichVus') && $this->dichVus && $this->dichVus->count() > 0) {
            $tongTienDichVu = (float) $this->dichVus->sum(function ($dv) {
                return (float) ($dv->pivot->thanh_tien ?? (($dv->pivot->don_gia ?? $dv->gia_tien ?? 0) * ($dv->pivot->so_luong ?? 1)));
            });
        } elseif ($this->relationLoaded('dichVu') && $this->dichVu) {
            $tongTienDichVu = (float) ($this->dichVu->gia_tien ?? 0);
        } else {
            $tongTienDichVu = (float) ($this->tong_tien ?? 0);
        }

        $tongTienThuoc = 0;
        $donThuoc = null;
        if ($this->relationLoaded('phieuKham') && $this->phieuKham) {
            $donThuoc = $this->phieuKham->don_thuoc;
            if (is_array($donThuoc) && !empty($donThuoc)) {
                $tongTienThuoc = (float) collect($donThuoc)->sum(function ($item) {
                    $soLuong = (float) ($item['so_luong'] ?? $item['quantity'] ?? 0);
                    $donGia = (float) ($item['don_gia'] ?? $item['unit_price'] ?? 0);
                    return $soLuong * $donGia;
                });
            }
        }

        $tongTienHienThi = $this->da_thanh_toan
            ? $tongTienThuoc
            : ($tongTienDichVu + $tongTienThuoc);

        return [
            'id' => $this->id,
            'ngay_gio' => $this->ngay_gio,
            'ngay_hen' => $this->ngay_hen,
            'gio_hen' => $this->gio_hen,
            'trang_thai' => $this->trang_thai,
            'nguon_goc' => $this->nguon_goc,
            'ghi_chu' => $this->ghi_chu,
            'huong_dan' => $this->huong_dan,
            'thoi_gian_checkin' => $this->thoi_gian_checkin ? $this->thoi_gian_checkin->format('Y-m-d H:i:s') : null,
            'thoi_gian_bat_dau_kham' => $this->thoi_gian_bat_dau_kham ? $this->thoi_gian_bat_dau_kham->format('Y-m-d H:i:s') : null,
            'thoi_gian_hoan_thanh' => $this->thoi_gian_hoan_thanh ? $this->thoi_gian_hoan_thanh->format('Y-m-d H:i:s') : null,
            'tong_tien' => $this->tong_tien,
            'tong_tien_hien_thi' => $tongTienHienThi,
            'da_thanh_toan' => $this->da_thanh_toan,
            'da_thu_thuoc' => $this->da_thu_thuoc,
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
                    'loai_thu_cung'  => $this->thuCung->loai_thu_cung,
                    'giong_thu_cung' => $this->thuCung->giong_thu_cung,
                    'giong_loai'     => $this->thuCung->giong_loai,
                    'giong'          => $this->thuCung->giong,
                    'tuoi_thu_cung'  => $this->thuCung->tuoi_thu_cung
                        ? $this->thuCung->tuoi_thu_cung->format('Y-m-d')
                        : null,
                    'can_nang'       => $this->thuCung->can_nang,
                    'anh_dai_dien'   => $this->thuCung->anh_dai_dien,
                    'anh_dai_dien_url' => $this->thuCung->anh_dai_dien_url,
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
            'dich_vus' => $this->whenLoaded('dichVus', function () {
                return $this->dichVus->map(function ($dv) {
                    return [
                        'id'         => $dv->id,
                        'ten'        => $dv->ten,
                        'gia_tien'   => (float) $dv->gia_tien,
                        'thoi_gian_thuc_hien' => $dv->thoi_gian_thuc_hien,
                        'so_luong'   => (int) $dv->pivot->so_luong,
                        'don_gia'    => (float) $dv->pivot->don_gia,
                        'thanh_tien' => (float) $dv->pivot->thanh_tien,
                    ];
                });
            }),
            'tong_thoi_gian_uoc_tinh' => $this->whenLoaded('dichVus', function () {
                return $this->dichVus->sum('thoi_gian_thuc_hien');
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
            'thanh_toans' => $this->whenLoaded('thanhToans', function () {
                return $this->thanhToans;
            }),
            'phieu_kham' => $this->whenLoaded('phieuKham', function () use ($donThuoc) {
                return [
                    'id' => $this->phieuKham->id,
                    'don_thuoc' => $donThuoc,
                ];
            }),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
