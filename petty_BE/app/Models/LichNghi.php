<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LichNghi extends Model
{
    protected $table = 'lich_nghis';

    protected $fillable = [
        'nhan_vien_id',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'ngay_nghi',
        'ly_do',
        'trang_thai',
        'loai_nghi',
        'ly_do_tu_choi',
        'admin_id',
    ];

    protected $casts = [
        'ngay_nghi' => 'date',
    ];

    const CHO_DUYET    = 'cho_duyet';
    const DA_DUYET     = 'da_duyet';
    const TU_CHOI      = 'tu_choi';

    const CO_LUONG     = 'co_luong';     // trong quota 4 ngày/tháng
    const KHONG_LUONG  = 'khong_luong';  // vượt quota

    public function nhanVien(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
