<?php

namespace Hysryt\Bookmark\Framework\View;

use Hysryt\Bookmark\Framework\Exception\NotFoundException;
use Hysryt\Bookmark\Framework\Exception\PermissionDeniedException;
use LogicException;

/**
 * テンプレートエンジン
 * 
 * テンプレート内で使用可能な関数
 * - $echo(string $text): エスケープして出力
 */
class TemplateEngine implements TemplateEngineInterface {
	private string $baseDir;

	/**
	 * コンストラクタ
	 *
	 * @param string $baseDir
	 */
	public function __construct(string $baseDir) {
		$baseDir = str_replace('/', DIRECTORY_SEPARATOR, $baseDir);
		$baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR);

		if (! is_dir($baseDir)) {
			throw new NotFoundException();
		}

		if (! is_readable($baseDir)) {
			throw new PermissionDeniedException();
		}

		$this->baseDir = $baseDir;
	}

	/**
	 * テンプレートにデータをはめ込む
	 *
	 * @param string $name
	 * @param array $data
	 * @return string
	 */
	public function render(string $name, array $data = []): string {
		$fullpath = $this->baseDir . DIRECTORY_SEPARATOR . $name;

		if (! is_file($fullpath) || ! is_readable($fullpath)) {
			throw new LogicException('テンプレートファイルが存在しない（' . $fullpath . '）');
		}

		$result = $this->applyData($fullpath, $data);
		return $result;
	}

	/**
	 * 指定したファイルを読み込みデータをはめ込む
	 *
	 * @param string $filepath
	 * @param array $data
	 * @return string
	 */
	private function applyData(string $filepath, array $data): string {
		extract($data);

		$echo = function($val) {
			echo htmlentities($val, ENT_QUOTES | ENT_HTML5);
		};

		ob_start();
		require($filepath);
		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}
}