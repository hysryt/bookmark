<?php

namespace Hysryt\Bookmark\Model;

use Hysryt\Bookmark\Framework\Exception\NotFoundException;
use Hysryt\Bookmark\Framework\Exception\PermissionDeniedException;
use Hysryt\Bookmark\Framework\Exception\NotSupportedException;
use RuntimeException;

/**
 * ローカルファイルから画像を読み込み、編集を行う
 * 
 * require_once('Image.php');
 *
 * $image = new Image('./test.jpg');
 * if ($image->isSupportedType()) {
 *   $image->resize(200, 500);
 *   $image->saveAs('./test3' . $image->getExtension());
 * }
 */
class Image {
	/**
	 * サポートするファイル形式
	 */
	const SUPORTTED_TYPES = [
		IMAGETYPE_JPEG,
		IMAGETYPE_PNG,
	];

	private string $file;
	private int $width;
	private int $height;
	private int $type;
	private $image;

	/**
	 * コンストラクタ
	 *
	 * @param string $file
	 * @throws NotFoundException - ファイルが存在しない
	 * @throws PermissionDeniedException - ファイルにアクセスする権限がない
	 * @throws NotSupportedException - サポートしていない画像形式
	 */
	public function __construct(string $file) {
		if (! file_exists($file)) {
			throw new NotFoundException('file not found.');
		}

		if (! is_readable($file)) {
			throw new PermissionDeniedException('permission denied.');
		}

		$this->file = $file;

		// 画像の情報（横幅、縦幅、形式）取得
		$imageInfo = getimagesize($file);
		if (is_array($imageInfo)) {
			list($this->width, $this->height, $this->type) = $imageInfo;
		} else {
			list($this->width, $this->height, $this->type) = [0, 0, -1];
		}

		// サポートしている形式であれば読み込み
		if (! $this->isSupportedType()) {
			throw new NotSupportedException('サポートしていない画像形式 （' . $this->type . '）');
		}

		$this->image = $this->loadFile($file);
	}

	/**
	 * ファイルを読み込む
	 *
	 * @param $file
	 * @return
	 */
	private function loadFile(string $file) {
		$image = 0;
		switch ($this->type) {
			case IMAGETYPE_JPEG:
				$image = \imagecreatefromjpeg($file);
				break;
			case IMAGETYPE_PNG:
				$image = \imagecreatefrompng($file);
				break;
		}
		return $image;
	}

	/**
	 * サポートしている形式かどうか
	 *
	 * @return boolean
	 */
	public function isSupportedType() {
		return in_array($this->type, self::SUPORTTED_TYPES);
	}

	/**
	 * 画像のリサイズ
	 *
	 * @param integer $width
	 * @param integer $height
	 * @return void
	 */
	public function resize(int $width, int $height) {
		$resized = imagecreatetruecolor($width, $height);

		$origRatio = $this->width / $this->height;
		if ($width/$height > $origRatio) {
			$dstWidth = $width;
			$dstHeight = $width / $origRatio;
		} else {
			$dstWidth = $height * $origRatio;
			$dstHeight = $height;
		}

		// 比率が異なる場合は、比率はそのままで領域全体を覆うように中心に合わせてリサイズ
		imagecopyresampled(
			$resized
			, $this->image
			, ($width - $dstWidth) / 2
			, ($height - $dstHeight) / 2
			, 0
			, 0
			, $dstWidth
			, $dstHeight
			, $this->width
			, $this->height
		);

		$this->image = $resized;
		$this->width = $width;
		$this->height = $height;		
	}

	/**
	 * 名前を指定して保存
	 *
	 * @param string $outputFile
	 * @return void
	 * @throws RuntimeException 保存できない
	 */
	public function saveAs(string $outputFile) {
		$result = false;
		switch ($this->type) {
			case IMAGETYPE_JPEG:
		$result = imagejpeg($this->image, $outputFile);
				break;
			case IMAGETYPE_PNG:
				$result = imagepng($this->image, $outputFile);
				break;
		}
		if (!$result) {
			throw new RuntimeException('save image error');
		}
	}

	/**
	 * 拡張子を取得
	 *
	 * @return string
	 */
	public function getExtension() {
		$extension = image_type_to_extension($this->type);

		if ($extension === '.jpeg') {
			$extension = '.jpg';
		}

		return $extension;
	}

	/**
	 * 画像のタイプを取得
	 *
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}

	public function getMimetype() {
		// TODO: MIME-Typeを取得できるようにしたい
	}

	/**
	 * ハッシュ値を取得
	 *
	 * @return string
	 */
	public function getHash() {
		return sha1_file($this->file);
	}
}