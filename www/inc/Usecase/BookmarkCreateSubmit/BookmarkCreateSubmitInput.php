<?php

namespace Hysryt\Bookmark\UseCase\BookmarkCreateSubmit;

use Hysryt\Bookmark\Framework\Http\Uri;
use Hysryt\Bookmark\Framework\Validation\ValidationResult;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

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
	public function getUrl(): Uri {
		try {
			$url = Uri::createFromUriString($this->url);
		} catch(InvalidArgumentException $e) {
			throw new RuntimeException('無効なURL', 0, $e);
		}
		return $url;
	}

	/**
	 * バリデーション
	 *
	 * @return ValidationResult
	 */
	public function validate(): ValidationResult {
        $result = new ValidationResult();
        if (!$this->url) {
            $result->addError('url', 'URLが未入力です');
			return $result;
        }

		try {
			$url = Uri::createFromUriString($this->url);
			if (!in_array($url->getScheme(), ['http', 'https'], true)) {
				$result->addError('url', 'URLはhttpまたはhttpsで開始してください');
			}
		} catch(InvalidArgumentException $e) {
			$result->addError('url', 'URLが正しくありません');
		}

		return $result;
	}
}