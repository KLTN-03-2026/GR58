<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThongBao extends Model
{
    protected $table = 'thong_baos';

    protected $fillable = [
        'khach_hang_id',
        'loai',
        'tieu_de',
        'noi_dung',
        'da_doc',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'da_doc' => 'boolean',
        'khach_hang_id' => 'integer',
        'reference_id' => 'integer',
    ];

    public function khachHang(): BelongsTo
    {
        return $this->belongsTo(KhachHang::class, 'khach_hang_id');
    }
}
