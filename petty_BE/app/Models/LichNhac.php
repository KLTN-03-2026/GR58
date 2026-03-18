<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LichNhac extends Model
{
    use HasFactory;

    protected $table = 'lich_nhacs';

    protected $fillable = [
        'ngay_nhac',
        'phan_loai',
        'noi_dung',
        'ghi_chu',
        'trang_thai',
        'ho_so_benh_an_id',
    ];

    protected $casts = [
        'ngay_nhac' => 'datetime',
    ];

    // Constants cho phân loại
    const PHAN_LOAI = [
        'tai_kham' => 'Tái khám',
        'tiem_phong' => 'Tiêm phòng',
        'xet_nghiem' => 'Xét nghiệm',
        'thuoc' => 'Uống thuốc',
        'cham_soc' => 'Chăm sóc',
        'khac' => 'Khác',
    ];

    // Constants cho trạng thái
    const TRANG_THAI = [
        'chua_gui' => 'Chưa gửi',
        'da_gui' => 'Đã gửi',
        'hoan_thanh' => 'Hoàn thành',
        'huy' => 'Hủy',
    ];

    /**
     * Quan hệ với bảng HoSoBenhAn
     */
    public function hoSoBenhAn()
    {
        return $this->belongsTo(HoSoBenhAn::class, 'ho_so_benh_an_id');
    }

    /**
     * Kiểm tra xem lịch nhắc đã quá hạn chưa
     */
    public function isOverdue()
    {
        return $this->ngay_nhac < now() && $this->trang_thai === 'chua_gui';
    }

    /**
     * Scope để lấy lịch nhắc chưa gửi
     */
    public function scopeChuaGui($query)
    {
        return $query->where('trang_thai', 'chua_gui');
    }

    /**
     * Scope để lấy lịch nhắc đã gửi
     */
    public function scopeDaGui($query)
    {
        return $query->where('trang_thai', 'da_gui');
    }

    /**
     * Scope để lấy lịch nhắc theo phân loại
     */
    public function scopePhanLoai($query, $phanLoai)
    {
        return $query->where('phan_loai', $phanLoai);
    }

    /**
     * Scope để lấy lịch nhắc sắp tới (trong X ngày)
     */
    public function scopeSapToi($query, $days = 7)
    {
        return $query->where('ngay_nhac', '>=', now())
            ->where('ngay_nhac', '<=', now()->addDays($days))
            ->where('trang_thai', 'chua_gui');
    }
}
