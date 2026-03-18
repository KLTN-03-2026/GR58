<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DichVu extends Model
{
    use HasFactory;

    protected $table = 'dich_vus';

    protected $fillable = [
        'ten',
        'gia_tien',
        'thoi_gian_thuc_hien',
        'mo_ta',
        'ma_dich_vu',
        'huong_dan',
        'anh_dich_vu',
        'trang_thai',
        'danh_muc_id',
    ];

    protected $casts = [
        'gia_tien' => 'decimal:2',
        'thoi_gian_thuc_hien' => 'integer',
    ];

    // Status constants
    public const STATUS_KINH_DOANH = 'kinh_doanh';
    public const STATUS_NGUNG = 'ngung';

    // Relationship: each service belongs to a category
    public function danhMuc()
    {
        return $this->belongsTo(DanhMucDichVu::class, 'danh_muc_id');
    }

    // Relationship: a service can have many appointments
    public function lichHens()
    {
        return $this->hasMany(LichHen::class, 'dich_vu_id');
    }

    // Relationship: a service can have many promotions
    public function khuyenMais()
    {
        return $this->belongsToMany(KhuyenMai::class, 'khuyen_mai_dich_vu', 'dich_vu_id', 'khuyen_mai_id')
            ->withTimestamps();
    }
}
