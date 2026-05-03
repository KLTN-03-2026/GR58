<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhieuKham extends Model
{
    protected $table = 'phieu_khams';

    protected $fillable = [
        'nhiet_do',
        'can_nang',
        'nhip_tim',
        'nhip_tho',
        'ly_do_den_kham',
        'trieu_chung',
        'chan_doan',
        'ghi_chu',
        'loai_chi_dinh',
        'ket_qua_can_lam_sang',
        'tep_dinh_kem_can_lam_sang',
        'thoi_gian_tra_ket_qua',
        'lich_hen_id',
        'nhan_vien_id',
    ];

    protected $casts = [
        'nhiet_do' => 'decimal:2',
        'can_nang' => 'decimal:2',
        'nhip_tim' => 'integer',
        'nhip_tho' => 'integer',
        'tep_dinh_kem_can_lam_sang' => 'array',
        'thoi_gian_tra_ket_qua' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Lịch hẹn liên kết
     */
    public function lichHen(): BelongsTo
    {
        return $this->belongsTo(LichHen::class, 'lich_hen_id');
    }

    /**
     * Nhân viên khám
     */
    public function nhanVien(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }
}
