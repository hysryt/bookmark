<?php

namespace Hysryt\Bookmark\Lib\Image;

use RuntimeException;

class Image implements ImageInterface {
    private $image;

    public function __construct($image) {
        $this->image = $image;
    }

    public function resize(int $width, int $height): ImageInterface {
        $ratio = $this->getWidth() / $this->getHeight();
        $newRatio = $width / $height;

        if ($newRatio > $ratio) {
            return $this->resizeFitWidth($width, $height);
        }
        return $this->resizeFitHeight($width, $height);
    }

    private function getWidth() {
        return imagesx($this->image);
    }

    private function getHeight() {
        return imagesy($this->image);
    }

    private function resizeFitWidth(int $width, int $height): ImageInterface {
        $scale = $width / $this->getWidth();
        $scaledHeight = $this->getHeight() * $scale;
        $distY = ($height - $scaledHeight) / 2;
        $image = $this->copyAndResample($width, $height, 0, $distY, $width, $scaledHeight);
        return new Image($image);
    }

    private function resizeFitHeight(int $width, int $height): ImageInterface {
        $scale = $height / $this->getHeight();
        $scaledWidth = $this->getWidth() * $scale;
        $distX = ($width - $scaledWidth) / 2;
        $image = $this->copyAndResample($width, $height, $distX, 0, $scaledWidth, $height);
        return new Image($image);
    }

    private function copyAndResample(int $width, int $height, int $distX, int $distY, int $distWidth, int $distHeight) {
        $new = imagecreatetruecolor($width, $height);
		imagecopyresampled(
			$new
			, $this->image
			, $distX
			, $distY
			, 0
			, 0
			, $distWidth
			, $distHeight
			, $this->getWidth()
			, $this->getHeight()
		);
        return $new;
    }

    public function saveAsJpeg(string $filepath) {
        $isSuccess = imagejpeg($this->image, $filepath);
        if ($isSuccess === false) {
            throw new RuntimeException('画像の保存に失敗：' . $filepath);
        }
    }

    public function hash(): string {
        ob_start();
        imagejpeg($this->image);
        return sha1(ob_get_clean());
    }
}