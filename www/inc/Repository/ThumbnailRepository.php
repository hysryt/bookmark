<?php

namespace Hysryt\Bookmark\Repository;

use Hysryt\Bookmark\Lib\Image\Image;

class ThumbnailRepository {
    private string $dir;
    private int $width;
    private int $height;

    public function __construct(string $dir, int $width, int $height) {
        $this->dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->width = $width;
        $this->height = $height;
    }

    public function save(Image $image): string {
        $image = $this->resize($image);
        $filename = $image->hash() . '.jpg';
        $filepath = $this->createFilepath($filename);
        $image->saveAsJpeg($filepath);
        return $filename;
    }

    private function resize(Image $image): Image {
        return $image->resize($this->width, $this->height);
    }

    private function createFilepath($filename) {
        return $this->dir . $filename;
    }
}