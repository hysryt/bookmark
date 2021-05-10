<?php

namespace Hysryt\Bookmark\Lib\Image;

use RuntimeException;

class ImageFactory {
    /**
     * @throws RuntimeException
     */
    public static function fromString(string $str) {
        $image = imagecreatefromstring($str);
        if ($image === false) {
            throw new RuntimeException();
        }

        return new Image($image);
    }
}