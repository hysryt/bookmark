<?php

namespace Hysryt\Bookmark\Lib\Image;

interface ImageInterface {
    public function resize(int $width, int $height): ImageInterface;
    public function saveAsJpeg(string $filepath);
}