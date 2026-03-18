<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KhuyenMai extends Model
{
    use HasFactory;

    protected $table = 'khuyen_mais';

    protected $fillable = [
        'ten_khuyen_mai',
        'mo_ta',
        'loai_khuyen_mai',
        'ma_code',
        'gia_tri_don_toi_thieu',
        'loai_khach_hang',
        'hinh_thuc_giam',
        'giam_toi_da',
        'gia_tri_giam',
        'tu_ngay',
        'den_ngay',
        'tong_so_luong',
        'so_luong_da_dung',
        'gioi_han_moi_khach',
        'trang_thai',
    ];

    protected $casts = [
        'gia_tri_don_toi_thieu' => 'decimal:2',
        'giam_toi_da' => 'decimal:2',
        'gia_tri_giam' => 'decimal:2',
        'tu_ngay' => 'datetime',
        'den_ngay' => 'datetime',
        'tong_so_luong' => 'integer',
        'so_luong_da_dung' => 'integer',
        'gioi_han_moi_khach' => 'integer',
    ];

    // Trạng thái constants
    public const TRANG_THAI_DANG_CHAY = 'dang_chay';
    public const TRANG_THAI_DA_KET_THUC = 'da_ket_thuc';

    /**
     * Kiểm tra khuyến mãi có còn hiệu lực không
     */
    public function isActive()
    {
        $now = now();
        return $this->trang_thai === self::TRANG_THAI_DANG_CHAY
            && $this->tu_ngay <= $now
            && $this->den_ngay >= $now
            && ($this->tong_so_luong === null || $this->so_luong_da_dung < $this->tong_so_luong);
    }

    /**
     * Kiểm tra khuyến mãi có còn số lượng không
     */
    public function hasQuantityAvailable()
    {
        if ($this->tong_so_luong === null) {
            return true;
        }
        return $this->so_luong_da_dung < $this->tong_so_luong;
    }

    /**
     * Tính giá trị giảm giá
     */
    public function calculateDiscount($orderAmount)
    {
        if ($this->hinh_thuc_giam === 'phan_tram') {
            $discount = $orderAmount * ($this->gia_tri_giam / 100);
            // Áp dụng giảm tối đa nếu có
            if ($this->giam_toi_da !== null && $discount > $this->giam_toi_da) {
                $discount = $this->giam_toi_da;
            }
            return $discount;
        } else {
            // Giảm theo số tiền cố định
            return $this->gia_tri_giam;
        }
    }

    /**
     * Scope để lọc khuyến mãi đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('trang_thai', self::TRANG_THAI_DANG_CHAY)
            ->where('tu_ngay', '<=', now())
            ->where('den_ngay', '>=', now());
    }

    /**
     * Scope để lọc khuyến mãi đang chạy
     */
    public function scopeDangChay($query)
    {
        return $query->where('trang_thai', self::TRANG_THAI_DANG_CHAY);
    }

    /**
     * Scope để lọc khuyến mãi đã kết thúc
     */
    public function scopeDaKetThuc($query)
    {
        return $query->where('trang_thai', self::TRANG_THAI_DA_KET_THUC);
    }

    /**
     * Scope để lọc khuyến mãi theo loại
     */
    public function scopeByType($query, $type)
    {
        return $query->where('loai_khuyen_mai', $type);
    }

    /**
     * Scope để lọc khuyến mãi theo mã code
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('ma_code', $code);
    }

    /**
     * Quan hệ nhiều-nhiều với bảng dịch vụ
     * Một khuyến mãi có thể áp dụng cho nhiều dịch vụ
     */
    public function dichVus()
    {
        return $this->belongsToMany(DichVu::class, 'khuyen_mai_dich_vu', 'khuyen_mai_id', 'dich_vu_id')
            ->withTimestamps();
    }
}
