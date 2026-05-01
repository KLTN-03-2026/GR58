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
