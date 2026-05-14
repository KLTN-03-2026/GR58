<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietPhieuKham extends Model
{
    protected $fillable = [
        'phieu_kham_id',
        'hang_hoa_id',
        'so_luong',
        'don_vi',
        'lieu_su_dung',
        'tan_suat',
        'thoi_gian_dung',
        'ghi_chu',
    ];

    protected $casts = [
        'so_luong' => 'integer',
    ];

    public function phieuKham(): BelongsTo
    {
        return $this->belongsTo(PhieuKham::class);
    }

    public function hangHoa(): BelongsTo
    {
        return $this->belongsTo(HangHoa::class);
    }
}
