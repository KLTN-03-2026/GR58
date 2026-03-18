<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietPhieuNhapKho extends Model
{
    protected $table = 'chi_tiet_phieu_nhap_khos';

    protected $fillable = [
        'phieu_nhap_kho_id',
        'hang_hoa_id',
        'so_luong',
        'so_lo',
        'han_su_dung',
        'don_gia',
        'thanh_tien',
        'ghi_chu',
    ];

    protected $casts = [
        'han_su_dung' => 'date',
        'don_gia' => 'decimal:2',
        'thanh_tien' => 'decimal:2',
        'so_luong' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Quan hệ với bảng PhieuNhapKho
     */
    public function phieuNhapKho(): BelongsTo
    {
        return $this->belongsTo(PhieuNhapKho::class, 'phieu_nhap_kho_id');
    }

    /**
     * Quan hệ với bảng HangHoa
     */
    public function hangHoa(): BelongsTo
    {
        return $this->belongsTo(HangHoa::class, 'hang_hoa_id');
    }

    /**
     * Quan hệ với bảng KiemKe
     */
    public function kiemKes()
    {
        return $this->hasMany(KiemKe::class, 'chi_tiet_phieu_nhap_kho_id');
    }
}
