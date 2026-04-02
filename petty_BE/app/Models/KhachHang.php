<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $phone
 * @property string|null $password
 * @property string $full_name
 * @property string|null $address
 * @property string $rank
 * @property string|null $anh_dai_dien
 * @property string $trang_thai
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialAccount> $socialAccounts
 * @property-read int|null $social_accounts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KhachHang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KhachHang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KhachHang query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KhachHang whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KhachHang whereAnhDaiDien($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KhachHang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KhachHang whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KhachHang whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KhachHang whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KhachHang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KhachHang wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KhachHang wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KhachHang whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KhachHang whereTrangThai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KhachHang whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class KhachHang extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'khach_hangs';

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'password',
        'address',
        'rank',
        'anh_dai_dien',
        'trang_thai',
        'email_verified_at',
    ];

    // Ẩn mật khẩu khi serialize
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function thuCungs()
    {
        return $this->hasMany(ThuCung::class, 'khach_hang_id');
    }
}
