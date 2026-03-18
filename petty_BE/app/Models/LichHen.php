<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichHen extends Model
{
    use HasFactory;

    protected $table = 'lich_hens';

    protected $fillable = [
        'ngay_gio',
        'ghi_chu',
        'huong_dan',
        'trang_thai',
        'nguon_goc',
        'thoi_gian_checkin',
        'y_ta_checkin_id',
        'thoi_gian_bat_dau_kham',
        'thoi_gian_hoan_thanh',
        'khach_hang_id',
        'thu_cung_id',
        'dich_vu_id',
        'thanh_toan_id',
        'nhan_vien_id',
    ];

    protected $casts = [
        'ngay_gio' => 'datetime',
        'thoi_gian_checkin' => 'datetime',
        'thoi_gian_bat_dau_kham' => 'datetime',
        'thoi_gian_hoan_thanh' => 'datetime',
    ];

    // Relationships
    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'khach_hang_id');
    }

    public function thuCung()
    {
        return $this->belongsTo(ThuCung::class, 'thu_cung_id');
    }

    public function dichVu()
    {
        return $this->belongsTo(DichVu::class, 'dich_vu_id');
    }

    public function thanhToan()
    {
        return $this->belongsTo(ThanhToan::class, 'thanh_toan_id');
    }

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }

    public function yTaCheckin()
    {
        return $this->belongsTo(NhanVien::class, 'y_ta_checkin_id');
    }
}
