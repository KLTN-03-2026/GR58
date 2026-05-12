<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\KhachHang;
use App\Models\LichHen;
use App\Models\PhieuKham;
use App\Helpers\PetImageHelper;

class ThuCung extends Model
{
    protected $table = 'thu_cungs';

    protected $fillable = [
        'anh_dai_dien',
        'ten_thu_cung',
        'loai_thu_cung',
        'giong_thu_cung',
        'tuoi_thu_cung',
        'khach_hang_id',
        'gioi_tinh',
        'can_nang',
    ];

    /**
     * Cast attributes to proper types.
     * Store `tuoi_thu_cung` as a date instance.
     */
    protected $casts = [
        'tuoi_thu_cung' => 'date',
    ];

    /**
     * Accessor to get full URL for pet avatar
     */
    protected $appends = ['anh_dai_dien_url'];

    public function getAnhDaiDienUrlAttribute()
    {
        return PetImageHelper::getImageUrl($this->anh_dai_dien, $this->loai_thu_cung ?? 'khac', $this->gioi_tinh);
    }

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'khach_hang_id');
    }

    public function lichHens()
    {
        return $this->hasMany(LichHen::class, 'thu_cung_id');
    }

    public function phieuKhams()
    {
        return $this->hasManyThrough(
            PhieuKham::class,
            LichHen::class,
            'thu_cung_id',   // FK on lich_hens
            'lich_hen_id',   // FK on phieu_khams
            'id',            // local key on thu_cungs
            'id'             // local key on lich_hens
        );
    }
}
