<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YeuCauHoTro extends Model
{
    protected $table = 'yeu_cau_ho_tros';

    protected $fillable = [
        'khach_hang_id',
        'ho_ten',
        'email',
        'so_dien_thoai',
        'chu_de',
        'noi_dung',
        'trang_thai',
        'phan_hoi',
    ];

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'khach_hang_id');
    }
}
