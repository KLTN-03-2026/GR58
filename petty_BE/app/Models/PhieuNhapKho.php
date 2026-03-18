<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhieuNhapKho extends Model
{
    protected $table = 'phieu_nhap_khos';

    protected $fillable = [
        'ma_phieu_nhap',
        'ngay_nhap',
        'tong_tien',
        'ghi_chu',
        'trang_thai',
        'nha_cung_cap_id',
        'phieu_chi_id',
        'nhan_vien_id',
        'admin_id',
    ];

    protected $casts = [
        'ngay_nhap' => 'date',
        'tong_tien' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Quan hệ với bảng NhaCungCap
     * Một phiếu nhập kho thuộc về một nhà cung cấp
     */
    public function nhaCungCap(): BelongsTo
    {
        return $this->belongsTo(NhaCungCap::class, 'nha_cung_cap_id');
    }

    /**
     * Quan hệ với bảng PhieuChi
     * Một phiếu nhập kho có thể có một phiếu chi
     */
    public function phieuChi(): BelongsTo
    {
        return $this->belongsTo(PhieuChi::class, 'phieu_chi_id');
    }

    /**
     * Quan hệ với bảng NhanVien
     * Người tạo phiếu nhập kho
     */
    public function nhanVien(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }

    /**
     * Quan hệ với bảng Admin
     * Admin phê duyệt phiếu nhập kho
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Quan hệ với bảng ChiTietPhieuNhapKho
     * Một phiếu nhập kho có nhiều chi tiết
     */
    public function chiTietPhieuNhapKhos(): HasMany
    {
        return $this->hasMany(ChiTietPhieuNhapKho::class, 'phieu_nhap_kho_id');
    }
}
