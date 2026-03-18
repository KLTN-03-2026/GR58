<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class PhieuChi extends Model
{
    use HasFactory;

    protected $table = 'phieu_chis';

    protected $fillable = [
        'ma_phieu_chi',
        'loai_phieu_chi',
        'ly_do_chi',
        'tong_so_tien',
        'so_tien_thanh_toan_ngay',
        'so_tien_con_no',
        'tien_mat',
        'tien_chuyen_khoan',
        'anh_chung_tu',
        'doi_tuong_nhan_tien',
        'nha_cung_cap_id',
        'trang_thai',
        'admin_id',
        'nhan_vien_id',
        'nguoi_tao_type',
        'ngay_chi',
        'ghi_chu',
    ];

    protected $casts = [
        'ngay_chi' => 'date',
        'tong_so_tien' => 'decimal:2',
        'so_tien_thanh_toan_ngay' => 'decimal:2',
        'so_tien_con_no' => 'decimal:2',
        'tien_mat' => 'decimal:2',
        'tien_chuyen_khoan' => 'decimal:2',
        'anh_chung_tu' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Append custom attributes to the model.
     *
     * @var array
     */
    protected $appends = ['loai_phieu_chi_label', 'trang_thai_label', 'nguoi_tao_info'];

    /**
     * Quan hệ với bảng NhanVien
     */
    public function nhanVien(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }

    /**
     * Quan hệ với bảng Admin
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Quan hệ với bảng NhaCungCap
     */
    public function nhaCungCap(): BelongsTo
    {
        return $this->belongsTo(NhaCungCap::class, 'nha_cung_cap_id');
    }

    /**
     * Quan hệ với bảng PhieuNhapKho
     */
    public function phieuNhapKhos(): HasMany
    {
        return $this->hasMany(PhieuNhapKho::class, 'phieu_chi_id');
    }

    /**
     * Relationship với lịch sử thanh toán
     */
    public function lichSuThanhToan(): HasMany
    {
        return $this->hasMany(LichSuThanhToanPhieuChi::class, 'phieu_chi_id')
            ->orderBy('ngay_thanh_toan', 'desc');
    }

    /**
     * Tự động tính toán và gán người tạo khi lưu
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Tự động tính số tiền còn nợ
            $model->so_tien_con_no = $model->tong_so_tien - $model->so_tien_thanh_toan_ngay;

            // Tự động cập nhật trạng thái dựa vào số tiền còn nợ
            if ($model->so_tien_con_no <= 0) {
                $model->trang_thai = 'da_hoan_thanh';
            } else {
                $model->trang_thai = 'con_no';
            }

            // Tự động lấy người đăng nhập
            if (!$model->admin_id && !$model->nhan_vien_id) {
                $user = Auth::user();

                if ($user) {
                    if ($user instanceof Admin) {
                        $model->admin_id = $user->id;
                        $model->nguoi_tao_type = 'Admin';
                    } elseif ($user instanceof NhanVien) {
                        $model->nhan_vien_id = $user->id;
                        $model->nguoi_tao_type = 'NhanVien';
                    } else {
                        // Kiểm tra guard
                        if (Auth::guard('admin')->check()) {
                            $admin = Auth::guard('admin')->user();
                            $model->admin_id = $admin->id;
                            $model->nguoi_tao_type = 'Admin';
                        } elseif (Auth::guard('nhan_vien')->check()) {
                            $nhanVien = Auth::guard('nhan_vien')->user();
                            $model->nhan_vien_id = $nhanVien->id;
                            $model->nguoi_tao_type = 'NhanVien';
                        }
                    }
                }
            }
        });

        static::updating(function ($model) {
            // Tự động tính lại số tiền còn nợ khi cập nhật
            if ($model->isDirty(['tong_so_tien', 'so_tien_thanh_toan_ngay'])) {
                $model->so_tien_con_no = $model->tong_so_tien - $model->so_tien_thanh_toan_ngay;

                // Tự động cập nhật trạng thái
                if ($model->so_tien_con_no <= 0) {
                    $model->trang_thai = 'da_hoan_thanh';
                } else {
                    $model->trang_thai = 'con_no';
                }
            }
        });
    }

    /**
     * Get label loại phiếu chi
     */
    public function getLoaiPhieuChiLabelAttribute()
    {
        $labels = [
            'chi_nhap_hang' => 'Chi nhập hàng',
            'chi_van_hanh' => 'Chi phí vận hành',
        ];

        return $labels[$this->loai_phieu_chi] ?? $this->loai_phieu_chi;
    }

    /**
     * Get label trạng thái
     */
    public function getTrangThaiLabelAttribute()
    {
        $labels = [
            'con_no' => 'Còn nợ NCC',
            'da_hoan_thanh' => 'Đã hoàn thành',
        ];

        return $labels[$this->trang_thai] ?? $this->trang_thai;
    }

    /**
     * Get thông tin người tạo
     */
    public function getNguoiTaoInfoAttribute()
    {
        if ($this->admin_id && $this->admin) {
            return [
                'type' => 'Admin',
                'id' => $this->admin_id,
                'ho_ten' => $this->admin->ho_ten,
                'email' => $this->admin->email,
                'anh_dai_dien' => $this->admin->anh_dai_dien,
            ];
        } elseif ($this->nhan_vien_id && $this->nhanVien) {
            return [
                'type' => 'Nhân viên',
                'id' => $this->nhan_vien_id,
                'ho_ten' => $this->nhanVien->full_name,
                'email' => $this->nhanVien->email,
                'anh_dai_dien' => $this->nhanVien->anh_dai_dien,
                'chuc_danh' => $this->nhanVien->chuc_danh,
            ];
        }

        return null;
    }
}
