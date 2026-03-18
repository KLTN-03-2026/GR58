<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LichDangKy extends Model
{
    use HasFactory;

    protected $table = 'lich_dang_kys';

    protected $fillable = [
        'ngay_gio',
        'ghi_chu',
        'trang_thai',
        'nhan_vien_id',
        'admin_id',
        'lich_lam_viec_id',
        'khach_hang_id',
        'thu_cung_id',
        'dich_vu_id',
    ];

    protected $casts = [
        'ngay_gio' => 'datetime',
    ];

    // Constants cho trạng thái
    const TRANG_THAI = [
        'chua_xac_nhan' => 'Chưa xác nhận',
        'da_xac_nhan' => 'Đã xác nhận',
        'tu_choi' => 'Từ chối',
    ];

    /**
     * Quan hệ với NhanVien
     */
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }

    /**
     * Quan hệ với Admin
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Quan hệ với LichLamViec
     */
    public function lichLamViec()
    {
        return $this->belongsTo(LichLamViec::class, 'lich_lam_viec_id');
    }

    /**
     * Quan hệ với KhachHang
     */
    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'khach_hang_id');
    }

    /**
     * Quan hệ với ThuCung
     */
    public function thuCung()
    {
        return $this->belongsTo(ThuCung::class, 'thu_cung_id');
    }

    /**
     * Quan hệ với DichVu
     */
    public function dichVu()
    {
        return $this->belongsTo(DichVu::class, 'dich_vu_id');
    }

    /**
     * Scope để lọc theo trạng thái
     */
    public function scopeTrangThai($query, $trangThai)
    {
        return $query->where('trang_thai', $trangThai);
    }

    /**
     * Scope lọc lịch chưa xác nhận
     */
    public function scopeChuaXacNhan($query)
    {
        return $query->where('trang_thai', 'chua_xac_nhan');
    }

    /**
     * Scope lọc lịch đã xác nhận
     */
    public function scopeDaXacNhan($query)
    {
        return $query->where('trang_thai', 'da_xac_nhan');
    }

    /**
     * Scope lọc lịch từ chối
     */
    public function scopeTuChoi($query)
    {
        return $query->where('trang_thai', 'tu_choi');
    }

    /**
     * Scope lọc theo ngày
     */
    public function scopeTheoNgay($query, $ngay)
    {
        return $query->whereDate('ngay_gio', $ngay);
    }

    /**
     * Scope lọc theo khoảng thời gian
     */
    public function scopeTrongKhoang($query, $tuNgay, $denNgay)
    {
        return $query->whereBetween('ngay_gio', [$tuNgay, $denNgay]);
    }
}
