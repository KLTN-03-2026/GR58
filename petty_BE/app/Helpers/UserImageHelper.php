<?php

namespace App\Helpers;

class UserImageHelper
{
    /**
     * Lấy ảnh đại diện mặc định cho người dùng
     * 
     * @return string URL của ảnh mặc định
     */
    public static function getDefaultAvatar()
    {
        return url('images/avatar.png');
    }

    /**
     * Lấy URL ảnh đại diện của người dùng
     * Nếu không có ảnh, trả về ảnh mặc định
     *
     * @param string|null $imageUrl URL hoặc path của ảnh
     * @return string URL đầy đủ của ảnh
     */
    public static function getAvatarUrl($imageUrl)
    {
        return ImageHelper::resolveUrl($imageUrl, self::getDefaultAvatar());
    }

    /**
     * Kiểm tra xem có phải ảnh mặc định không
     * 
     * @param string|null $imageUrl
     * @return bool
     */
    public static function isDefaultAvatar($imageUrl)
    {
        if (!$imageUrl || trim($imageUrl) === '') {
            return true;
        }

        return str_contains($imageUrl, 'avatar.png');
    }
}
