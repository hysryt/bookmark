<?php

namespace Hysryt\Bookmark\Model;

use Hysryt\Bookmark\Log\Log;
use Hysryt\Bookmark\Framework\Exception\NetworkException;
use Hysryt\Bookmark\Framework\Exception\NotSupportedException;
use Hysryt\Bookmark\Lib\Html\HtmlDocument;
use Hysryt\Bookmark\Lib\Html\HtmlDocumentInterface;
use Hysryt\Bookmark\Lib\HttpMessage\Request;
use Hysryt\Bookmark\Lib\HttpMessage\Uri;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use RuntimeException;

/**
 * サイト情報をスクレイピングするクラス
 */
class SiteInfoScraper {
	private Uri $url;
	private ClientInterface $client;
	private HtmlDocumentInterface $html;

	/**
	 * コンストラクタ
	 *
	 * @param Uri $url
	 * @param ClientInterface $client;
	 * @throws NetworkException - ネットワークエラー
	 * @throws NotSupportedException - サポートしないMIME-Type
	 */
	public function __construct(Uri $url, ClientInterface $client) {
		Log::info('取得 ' . $url);

		$this->url    = $url;
		$this->client = $client;
		$this->html   = $this->downloadHtml();
	}

	/**
	 * @throws NetworkException - ネットワークエラー
	 * @throws NotSupportedException - サポートしないMIME-Type
	 */
	private function downloadHtml() {
		try {
			$request = Request::create('GET', $this->url);
			$response = $this->client->sendRequest($request);
			$htmlDocument = new HtmlDocument($response->getBody()->getContents());
			return $htmlDocument;

		} catch (ClientExceptionInterface $e) {
			throw new NetworkException($e->getMessage(), 0, $e);

		} catch (RuntimeException $e) {
			throw new NotSupportedException($e->getMessage(), 0, $e);
		}
	}

	/**
	 * サムネイル画像（OGP画像）が設定されているかどうか
	 *
	 * @return bool
	 */
	public function hasThumbnailPicture(): bool {
		return $this->html->parseOgp()->getImage() !== null;
	}

	/**
	 * サムネイル画像（OGP画像）のURLを取得
	 *
	 * @return string
	 */
	public function getThumbnailPictureUrl(): string {
		return $this->html->parseOgp()->getImage();
	}

	/**
	 * URLを取得
	 *
	 * @return Uri
	 */
	public function getUrl(): Uri {
		return $this->url;
	}

	/**
	 * 以下の優先順位でタイトルを取得。
	 * 1. og:title
	 * 2. <title>タグ
	 * どちらも設定されていない場合はnullを返す
	 *
	 * @return ?string
	 */
	public function getTitle(): ?string {
		if ($this->html->parseOgp() && $this->html->parseOgp()->getTitle()) {
			return $this->html->parseOgp()->getTitle();
		}
		return $this->html->parseTitle();
	}

	/**
	 * 以下の優先順位でディスクリプションを取得
	 * 1. og:description
	 * 2. <meta name="description" content="...">
	 * どちらも設定されていない場合はnullを返す
	 *
	 * @return ?string
	 */
	public function getDescription(): ?string {
		if ($this->html->parseOgp() && $this->html->parseOgp()->getDescription()) {
			return $this->html->parseOgp()->getDescription();
		}
		return $this->html->parseDescription();
	}
}
