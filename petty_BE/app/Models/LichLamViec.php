<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LichLamViec extends Model
{
    protected $table = 'lich_lam_viecs';

    protected $fillable = [
        'ngay_lam',
        'phong_truc',
        'thoi_gian_truc',
        'nhan_vien_id',
    ];

    protected $casts = [
        'ngay_lam' => 'date',
    ];

    public function nhanVien(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }

    /**
     * Quan hệ với LichDangKy
     */
    public function lichDangKys()
    {
        return $this->hasMany(LichDangKy::class, 'lich_lam_viec_id');
    }

    // Shift keys
    public const CA_SANG = 'ca_sang';
    public const CA_CHIEU = 'ca_chieu';
    public const CA_TOI = 'ca_toi';

    /**
     * Predefined shift options with display label and time range.
     * key => [ 'label' => '...', 'range' => 'HH:MM - HH:MM' ]
     */
    public static array $shiftOptions = [
        self::CA_SANG => [
            'label' => 'Ca sáng',
            'range' => '08:00 - 16:00',
        ],
        self::CA_CHIEU => [
            'label' => 'Ca chiều',
            'range' => '13:00 - 21:00',
        ],
        self::CA_TOI => [
            'label' => 'Ca tối',
            'range' => '21:00 - 08:00',
        ],
    ];

    /**
     * Get human readable label for thoi_gian_truc.
     */
    public function getThoiGianTrucLabelAttribute(): ?string
    {
        $key = $this->attributes['thoi_gian_truc'] ?? null;
        return $key && isset(self::$shiftOptions[$key]) ? self::$shiftOptions[$key]['label'] : null;
    }

    /**
     * Get time range for thoi_gian_truc.
     */
    public function getThoiGianTrucRangeAttribute(): ?string
    {
        $key = $this->attributes['thoi_gian_truc'] ?? null;
        return $key && isset(self::$shiftOptions[$key]) ? self::$shiftOptions[$key]['range'] : null;
    }

    /**
     * Return list of allowed shift keys for validation.
     */
    public static function allowedShifts(): array
    {
        return array_keys(self::$shiftOptions);
    }
}
