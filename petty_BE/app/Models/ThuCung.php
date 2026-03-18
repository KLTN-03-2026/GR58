<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\KhachHang;

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

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'khach_hang_id');
    }
}
