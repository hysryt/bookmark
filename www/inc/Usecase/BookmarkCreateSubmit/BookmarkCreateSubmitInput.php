<?php

namespace Hysryt\Bookmark\UseCase\BookmarkCreateSubmit;

use Hysryt\Bookmark\Framework\Validation\ValidationResult;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Bookmark追加用インプット
 */
class BookmarkCreateSubmitInput {
	private string $url;

	/**
	 * コンストラクタ
	 *
	 * @param ServerRequestInterface $request
	 */
	public function __construct(ServerRequestInterface $request) {
        $parsedBody = $request->getParsedBody();
        $this->url = isset($parsedBody['url']) ? $parsedBody['url'] : '';
	}

	/**
	 * URLを取得する
	 *
	 * @return string
	 */
	public function getUrl(): string {
		return $this->url;
	}

	/**
	 * バリデーション
	 *
	 * @return ValidationResult
	 */
	public function validate(): ValidationResult {
        $result = new ValidationResult();
        if (!$this->url) {
            $result->addError('url', 'URLが未入力');
        }
		return $result;
	}
}