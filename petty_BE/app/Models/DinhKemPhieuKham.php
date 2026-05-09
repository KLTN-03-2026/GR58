<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DinhKemPhieuKham extends Model
{
    protected $table = 'dinh_kem_phieu_khams';

    protected $fillable = [
        'phieu_kham_id',
        'ten_file',
        'duong_dan',
        'loai_mime',
        'kich_thuoc',
        'nguoi_upload_id',
    ];

    /**
     * Liên kết với phiếu khám.
     */
    public function phieuKham()
    {
        return $this->belongsTo(PhieuKham::class, 'phieu_kham_id');
    }

    /**
     * Liên kết với nhân viên upload.
     */
    public function nguoiUpload()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_upload_id');
    }

    /**
     * Accessor: trả về URL đầy đủ của file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->duong_dan);
    }
}
