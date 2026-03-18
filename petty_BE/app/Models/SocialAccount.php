<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $khach_hang_id
 * @property string $provider
 * @property string $provider_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\KhachHang $khachHang
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereKhachHangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereProviderUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SocialAccount extends Model
{
    protected $fillable = [
        'khach_hang_id',
        'provider',
        'provider_user_id',
    ];

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class);
    }
}
