<?php

namespace Hysryt\Bookmark\Model;

use Hysryt\Bookmark\Log\Log;
use Hysryt\Bookmark\Framework\Exception\NetworkException;
use Hysryt\Bookmark\Framework\Exception\NotSupportedException;
use Hysryt\Bookmark\Framework\Exception\PermissionDeniedException;
use Hysryt\Bookmark\Framework\Exception\NotFoundException;
use LogicException;
use Exception;
use Hysryt\Bookmark\Lib\Html\HtmlDocument;
use Hysryt\Bookmark\Lib\Html\HtmlDocumentInterface;
use Hysryt\Bookmark\Lib\Html\OpenGraphInterface;
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
	 * OGP情報を取得する
	 *
	 * @return OpenGraphInterface
	 */
	public function getOgp(): OpenGraphInterface {
		return $this->html->parseOgp();
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
	 * OGPの画像をダウンロードし、リサイズして保存
	 *
	 * @param string $distDir 保存先ディレクトリ
	 * @param int $width リサイズ後の横幅
	 * @param int $height リサイズ後の縦幅
	 * @return ?string 保存ファイル名、失敗した場合はnull
	 */
	public function downloadThumbnailPicture($distDir, $width, $height): ?string {
		// 保存先ディレクトリパスの末尾に/を付与
		$distDir = rtrim($distDir, '/') . '/';
		if (! is_dir($distDir)) {
			// 保存先ディレクトリが存在しない
			Log::error('OGP画像の保存先ディレクトリが存在しない ' . $distDir);
			return null;
		}

		// OGP画像が設定されていない
		if (! $this->hasThumbnailPicture()) {
			throw new LogicException('downloadThumbnailPictureの前にhasThumbnailPictureで画像があるかどうか確認する必要がある');
		}

		// オリジナル画像を一時的に保存
		$originalImageFilepath = null;
		try {
			$originalImageFilename = $this->downloadOriginalOgpImage($distDir);

			// オリジナル画像をリサイズして別ファイルとして画像を保存
			$originalImageFilepath = $distDir . $originalImageFilename;
			$resizedFilename = $this->resizeOgpImage($originalImageFilepath, $distDir, $width, $height);

		} catch (Exception $e) {
			// 画像が取得できなかった場合は画像なしのまま処理続行
			Log::warning($e->getMessage(), array(), $e);
			return null;

		} finally {
			// ダウンロードしたオリジナル画像を削除
			if ($originalImageFilepath && file_exists($originalImageFilepath)) {
				unlink($originalImageFilepath);
			}
		}

		return $resizedFilename;
	}

	/**
	 * OGPのオリジナル画像をダウンロード
	 *
	 * @param string $distDir
	 * @return string 保存ファイル名
	 * @throws NetworkException
	 * @throws PermissionDeniedException
	 * @throws NotSupportedException
	 */
	private function downloadOriginalOgpImage($distDir): string {
		$imageUrl = $this->html->parseOgp()->getImage();
		
		$request = new Request([],[],[],[],[],[]);
		$request = $request->withUri(Uri::createFromUriString($imageUrl));
		$response = $this->client->sendRequest($request);
		if ($response->getStatusCode() !== 200) {
			// ネットワークエラーまたは4xx,5xxエラー
			throw new NetworkException('ネットワークエラー （' . $imageUrl . '）');
		}
		$originalImage = $response->getBody()->getContents();

		$hash = sha1($originalImage);
		$originalFilename = 'orig-' . $hash;
		$saveResult = file_put_contents($distDir . $originalFilename, $originalImage);
		if ($saveResult === false) {
			// ファイル保存エラー
			throw new PermissionDeniedException('OGP画像ダウンロード時のファイル書き込みエラー （' . $distDir . $originalFilename . '）');
		}

		try {
			new Image($distDir . $originalFilename);
		} catch (NotFoundException $e) {
			// 未対応の画像形式
			throw new NotSupportedException('未対応のOGP画像形式 （' . $imageUrl . '）', 0, $e);
		}

		return $originalFilename;
	}

	/**
	 * OGPのオリジナル画像をリサイズ
	 *
	 * @param string $tmpFilepath オリジナル画像のファイルパス
	 * @param string $distDir 保存先ディレクトリ
	 * @param int $width リサイズ後の横幅
	 * @param int $height リサイズ後の縦幅
	 * @return string 保存先ファイル名
	 * @throws NotSupoprtedException
	 * @throws PermissionDeniedException
	 */
	private function resizeOgpImage($tmpFilepath, $distDir, $width, $height): ?string {
		try {
			$image = new Image($tmpFilepath);
			$filename = $image->getHash() . $image->getExtension();

			$image->resize($width, $height);
			$image->saveAs($distDir . $filename);

		} catch(NotSupportedException $e) {
			throw new LogicException('サポートしない画像形式の場合はコンストラクタで例外を投げるのでここには来ないはず', 0, $e);

		} catch(Exception $e) {
			// ファイル保存エラー
			throw new PermissionDeniedException('リサイズ後のファイル書き込みエラー ' . $distDir . $filename, 0, $e);
		}

		return $filename;
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
