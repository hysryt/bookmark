<?php

namespace Hysryt\Bookmark\UseCase\BookmarkShow;

use Hysryt\Bookmark\Framework\Validation\ValidationResult;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Bookmark詳細表示用インプット
 */
class BookmarkShowInput {
	private int $id = 0;

	/**
	 * コンストラクタ
	 *
	 * @param ServerRequestInterface $request
	 */
	public function __construct(ServerRequestInterface $request) {
        $pathAttributes = $request->getAttribute('pathAttributes');
		if (isset($pathAttributes['id'])) {
			$this->id = intVal($pathAttributes['id']);
		}
	}

	/**
	 * IDを取得する
	 *
	 * @return integer
	 */
	public function getId(): int {
		return $this->id;
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