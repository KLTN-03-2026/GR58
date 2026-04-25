<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThanhToan extends Model
{
    protected $fillable = [
        'ma_thanh_toan',
        'lich_hen_id',
        'khach_hang_id',
        'tong_tien_goc',
        'so_tien_giam',
        'tong_tien_sau_giam',
        'hinh_thuc_thanh_toan',
        'tien_mat',
        'tien_online',
        'ma_giao_dich_online',
        'khuyen_mai_id',
        'ma_giam_gia',
        'loai_giam',
        'trang_thai',
        'nhan_vien_id',
        'admin_id',
        'ngay_thanh_toan',
        'ghi_chu',
    ];

    protected $casts = [
        'ngay_thanh_toan'    => 'datetime',
        'tong_tien_goc'      => 'float',
        'so_tien_giam'       => 'float',
        'tong_tien_sau_giam' => 'float',
        'tien_mat'           => 'float',
        'tien_online'        => 'float',
    ];

    public function lichHen(): BelongsTo
    {
        return $this->belongsTo(LichHen::class, 'lich_hen_id');
    }

    public function khachHang(): BelongsTo
    {
        return $this->belongsTo(KhachHang::class, 'khach_hang_id');
    }

    public function khuyenMai(): BelongsTo
    {
        return $this->belongsTo(KhuyenMai::class, 'khuyen_mai_id');
    }

    public function nhanVien(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}