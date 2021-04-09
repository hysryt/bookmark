<?php

namespace Hysryt\Bookmark\UseCase\BookmarkIndex;

use Hysryt\Bookmark\Framework\Validation\ValidationResult;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Bookmark一覧表示用インプット
 */
class BookmarkIndexInput {
	private int $page = 0;

	/**
	 * コンストラクタ
	 *
	 * @param ServerRequestInterface $request
	 */
	public function __construct(ServerRequestInterface $request) {
		$queryParams = $request->getQueryParams();
		if (isset($queryParams['page'])) {
			$this->page = intVal($queryParams['page']);
		}
	}

	/**
	 * ページ番号を取得する
	 *
	 * @return integer
	 */
	public function getPage(): int {
		return $this->page;
	}

	/**
	 * バリデーション
	 *
	 * @return ValidationResult
	 */
	public function validate(): ValidationResult {
		return new ValidationResult();
	}
}