<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NhanVien extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'nhan_viens';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'address',
        'anh_dai_dien',
        'vai_tro',
        'chuc_danh',
        'nam_kinh_nghiem',
        'chung_chi_hanh_nghe',
        'bang_cap_chuyen_mon',
        'password',
        'trang_thai',
        'phan_quyen_id',
    ];

    // Ẩn mật khẩu khi serialize
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'nam_kinh_nghiem' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Mutator: hash password automatically when set.
     */
    public function setPasswordAttribute($value)
    {
        if ($value && !\str_starts_with($value, '$2y$')) {
            $this->attributes['password'] = bcrypt($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Check if the employee account is active (not locked).
     */
    public function isActive(): bool
    {
        return $this->trang_thai === 'hoat_dong';
    }

    /**
     * Khoa relationship (many-to-many)
     */
    public function khoas(): BelongsToMany
    {
        return $this->belongsToMany(Khoa::class, 'khoa_nhan_vien', 'nhan_vien_id', 'khoa_id');
    }

    /**
     * Lịch hẹn do nhân viên phụ trách
     */
    public function lichHens()
    {
        return $this->hasMany(LichHen::class, 'nhan_vien_id');
    }

    /**
     * Lịch đăng ký (nếu có bảng LichDangKy)
     */
    public function lichDangKys()
    {
        return $this->hasMany(LichDangKy::class, 'nhan_vien_id');
    }

    /**
     * Bài viết do nhân viên tạo
     */
    public function baiViets()
    {
        return $this->hasMany(BaiViet::class, 'nhan_vien_id');
    }

    /**
     * Hồ sơ bệnh án mà nhân viên phụ trách
     */
    public function hoSoBenhAns()
    {
        return $this->hasMany(HoSoBenhAn::class, 'nhan_vien_id');
    }

    /**
     * Quan hệ với bảng PhanQuyen (belongsTo - mỗi nhân viên có 1 vai trò)
     */
    public function phanQuyen()
    {
        return $this->belongsTo(PhanQuyen::class, 'phan_quyen_id');
    }

    /**
     * Phân quyền (many-to-many) - giữ lại nếu cần
     */
    public function phanQuyens(): BelongsToMany
    {
        return $this->belongsToMany(PhanQuyen::class, 'phan_quyen_nhan_vien', 'nhan_vien_id', 'phan_quyen_id');
    }

    /**
     * Kiểm tra xem nhân viên có quyền không
     */
    public function hasPermission($permission)
    {
        if (!$this->phanQuyen) {
            return false;
        }
        return $this->phanQuyen->hasPermission($permission);
    }

    /**
     * Lịch làm việc của nhân viên
     */
    public function lichLamViecs()
    {
        return $this->hasMany(LichLamViec::class, 'nhan_vien_id');
    }

    /**
     * Quan hệ với bảng KiemKe
     */
    public function kiemKes()
    {
        return $this->hasMany(KiemKe::class, 'nhan_vien_id');
    }

    /**
     * Quan hệ với bảng PhieuChi
     */
    public function phieuChis()
    {
        return $this->hasMany(PhieuChi::class, 'nhan_vien_id');
    }
}
