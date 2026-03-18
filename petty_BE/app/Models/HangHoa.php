<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HangHoa extends Model
{
    use HasFactory;

    protected $table = 'hang_hoas';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'ma_hang_hoa',
        'ten_mat_hang',
        'don_vi_tinh',
        'gia_von',
        'gia_ban',
        'dinh_muc_toi_thieu',
        'anh_san_pham',
        'tinh_trang',
        'danh_muc_hang_hoa_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'gia_von' => 'decimal:2',
        'gia_ban' => 'decimal:2',
        'dinh_muc_toi_thieu' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Append custom attributes to the model.
     *
     * @var array
     */
    protected $appends = ['ten_danh_muc_hang_hoa', 'tinh_trang_label'];

    /**
     * The category this product belongs to.
     */
    public function danhMuc()
    {
        return $this->belongsTo(\App\Models\DanhMucHangHoa::class, 'danh_muc_hang_hoa_id');
    }

    /**
     * Quan hệ với bảng ChiTietPhieuNhapKho
     */
    public function chiTietPhieuNhapKhos()
    {
        return $this->hasMany(\App\Models\ChiTietPhieuNhapKho::class, 'hang_hoa_id');
    }

    /**
     * Quan hệ với bảng KiemKe
     */
    public function kiemKes()
    {
        return $this->hasMany(\App\Models\KiemKe::class, 'hang_hoa_id');
    }

    /**
     * Get the category name.
     */
    public function getTenDanhMucHangHoaAttribute()
    {
        return $this->danhMuc?->ten_danh_muc_hang_hoa;
    }

    /**
     * Get the status label in Vietnamese.
     */
    public function getTinhTrangLabelAttribute()
    {
        $statusMap = [
            'hoat_dong' => 'Hoạt động',
            'ngung_kinh_doanh' => 'Ngừng kinh doanh',
        ];

        return $statusMap[$this->tinh_trang] ?? $this->tinh_trang;
    }
}
