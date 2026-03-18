<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Khoa extends Model
{
    use HasFactory;

    protected $fillable = [
        'ten_khoa',
        'ma_khoa',
        'mo_ta',
        'chuyen_mon',
        'trang_thai',
    ];

    protected $casts = [
        'trang_thai' => 'integer',
    ];

    // Relationship: khoa belongsToMany danh muc dich vu
    public function danhMucDichVus()
    {
        return $this->belongsToMany(DanhMucDichVu::class, 'danh_muc_dich_vu_khoa', 'khoa_id', 'danh_muc_dich_vu_id');
    }

    // Relationship: khoa belongsToMany nhan vien
    public function nhanViens()
    {
        return $this->belongsToMany(NhanVien::class, 'khoa_nhan_vien', 'khoa_id', 'nhan_vien_id');
    }
}
