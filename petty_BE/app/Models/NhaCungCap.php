<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NhaCungCap extends Model
{
    protected $table = 'nha_cung_caps';

    protected $fillable = [
        'ma_nha_cung_cap',
        'ten_nha_cung_cap',
        'ten_nguoi_lien_he',
        'so_dien_thoai',
        'dia_chi',
        'email',
        'ma_so_thue',
        'mo_ta',
        'trang_thai',
    ];

    protected $rules = [
        'ma_nha_cung_cap' => 'required|string|max:50|unique:nha_cung_caps,ma_nha_cung_cap',
        'ten_nha_cung_cap' => 'required|string|max:255',
        'ten_nguoi_lien_he' => 'required|string|max:255',
        'so_dien_thoai' => 'required|string|max:15',
        'dia_chi' => 'required|string',
        'email' => 'required|email|unique:nha_cung_caps,email',
        'ma_so_thue' => 'required|string|max:50',
        'mo_ta' => 'required|string',
        'trang_thai' => 'required|in:hoat_dong,ngung_hoat_dong',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Quan hệ với bảng PhieuNhapKho
     * Một nhà cung cấp có thể có nhiều phiếu nhập kho
     */
    public function phieuNhapKhos(): HasMany
    {
        return $this->hasMany(PhieuNhapKho::class, 'nha_cung_cap_id');
    }

    /**
     * Quan hệ với bảng PhieuChi
     * Một nhà cung cấp có thể có nhiều phiếu chi (chi nhập hàng)
     */
    public function phieuChis(): HasMany
    {
        return $this->hasMany(PhieuChi::class, 'nha_cung_cap_id');
    }
}
