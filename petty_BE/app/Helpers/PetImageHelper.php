<?php

namespace App\Helpers;

class PetImageHelper
{
    /**
     * Lấy ảnh mặc định cho thú cưng dựa trên loại và giới tính
     * 
     * @param string $loai Loại thú cưng: 'cho', 'meo', 'khac'
     * @param string|null $gioiTinh Giới tính: 'duc', 'cai', null
     * @return string URL của ảnh mặc định
     */
    public static function getDefaultImage($loai, $gioiTinh = null)
    {
        $loai = strtolower($loai);
        $gioiTinh = $gioiTinh ? strtolower($gioiTinh) : null;

        // Xác định tên file ảnh mặc định (hỗ trợ cả tên tiếng Việt không dấu và tiếng Anh)
        $isDog = in_array($loai, ['cho', 'chó', 'dog']);
        $isCat = in_array($loai, ['meo', 'mèo', 'cat']);

        $imageName = match(true) {
            $isDog && $gioiTinh === 'duc' => 'choduc.jpg',
            $isDog && $gioiTinh === 'cai' => 'chocai.jpg',
            $isDog => 'choduc.jpg',
            $isCat => 'meo.jpg',
            default => 'thucungkhac.jpg',
        };

        return url('images/default-pets/' . $imageName);
    }

    /**
     * Resolve pet image path/URL to full URL with default fallback.
     *
     * Uses same 3-case logic as ImageHelper::resolveUrl but with pet-specific default.
     *
     * @param string|null $value The value from DB (relative path, full URL, or null)
     * @param string $loai Loại thú cưng: 'cho', 'meo', 'khac'
     * @param string|null $gioiTinh Giới tính: 'duc', 'cai', null
     * @return string Full URL
     */
    public static function getImageUrl($value, $loai, $gioiTinh = null): string
    {
        return ImageHelper::resolveUrl($value, self::getDefaultImage($loai, $gioiTinh));
    }

    /**
     * Kiểm tra xem có phải ảnh mặc định không
     *
     * @param string|null $imageUrl
     * @return bool
     */
    public static function isDefaultImage($imageUrl)
    {
        if (!$imageUrl) {
            return true;
        }

        $defaultImages = ['choduc.jpg', 'chocai.jpg', 'meo.jpg', 'thucungkhac.jpg'];

        foreach ($defaultImages as $defaultImage) {
            if (str_contains($imageUrl, $defaultImage)) {
                return true;
            }
        }

        return false;
    }
}
