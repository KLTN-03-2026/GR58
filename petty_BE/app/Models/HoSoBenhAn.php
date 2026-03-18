<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HoSoBenhAn extends Model
{
    use HasFactory;

    protected $table = 'ho_so_benh_ans';

    protected $fillable = [
        'ma_benh_an',
        'noi_dung',
        'khach_hang_id',
        'thu_cung_id',
        'nhan_vien_id',
    ];

    /**
     * Quan hệ với KhachHang
     */
    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'khach_hang_id');
    }

    /**
     * Quan hệ với ThuCung
     */
    public function thuCung()
    {
        return $this->belongsTo(ThuCung::class, 'thu_cung_id');
    }

    /**
     * Quan hệ với NhanVien
     */
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }

    /**
     * Quan hệ với bảng LichNhac
     */
    public function lichNhacs()
    {
        return $this->hasMany(LichNhac::class, 'ho_so_benh_an_id');
    }
}
