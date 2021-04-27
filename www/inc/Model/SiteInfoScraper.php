<?php

namespace Hysryt\Bookmark\Model;

use Hysryt\Bookmark\Log\Log;
use Hysryt\Bookmark\Framework\Exception\NetworkException;
use Hysryt\Bookmark\Framework\Exception\NotSupportedException;
use Hysryt\Bookmark\Framework\Exception\PermissionDeniedException;
use Hysryt\Bookmark\Framework\Exception\NotFoundException;
use LogicException;
use Exception;
use Hysryt\Bookmark\Lib\HttpMessage\Request;
use Hysryt\Bookmark\Lib\HttpMessage\Uri;
use Psr\Http\Client\ClientInterface;

/**
 * サイト情報をスクレイピングするクラス
 */
class SiteInfoScraper {
	private Uri $url;
	private ClientInterface $client;
	private ?string $title;
	private ?string $description;
	private string $html;
	private ?OpenGraph $ogp = null;

	/**
	 * コンストラクタ
	 *
	 * @param Uri $url
	 * @param ClientInterface $client;
	 * @throws NetworkException - ネットワークエラー
	 * @throws NotSupportedException - URLから取得したデータが未対応のファイル形式
	 */
	public function __construct(Uri $url, ClientInterface $client) {
		$this->url = $url;
		$this->client = $client;

		Log::info('取得 ' . $this->url);
		$data = $this->fetchData($this->url);

		// HTML形式以外は拒否
		$mimeType = $this->getMimeType($data);
		if ($mimeType !== 'text/html') {
			Log::warning('サポートしないMIME-Type（' . $mimeType . '）' . $this->url);
			throw new NotSupportedException('unsupported mime-type: ' . $mimeType);
		}

		$this->html = $data;
		$this->parse();
	}

	/**
	 * URLからデータを取得
	 * @param Uri $uri
	 * @return string
	 */
	private function fetchData(Uri $url): string {
		$request = new Request([],[],[],[],[],[]);
		$request = $request->withUri($url);
		$response = $this->client->sendRequest($request);

		return $response->getBody()->getContents();
	}

	/**
	 * MIME Type取得
	 *
	 * @param string $data
	 * @return void
	 */
	private function getMimeType(string $data) {
		$fileInfo = new \finfo(FILEINFO_MIME_TYPE);
		$mimeType = $fileInfo->buffer($data);
		return $mimeType;
	}

	/**
	 * HTMLから情報を取得
	 *
	 * @return void
	 */
	private function parse() {
		try {
			$parser = new HtmlParser($this->html);
			$this->ogp = $parser->parseOgp();
			$this->title = $parser->parseTitle();
			$this->description = $parser->parseMetaDescription();
		} catch (Exception $e) {
			Log::warning('パース失敗 ' . $this->url, array(), $e);
			throw $e;
		}
	}

	/**
	 * OGP情報を取得する
	 *
	 * @return OpenGraph
	 */
	public function getOgp(): OpenGraph {
		return $this->ogp;
	}

	/**
	 * サムネイル画像（OGP画像）が設定されているかどうか
	 *
	 * @return bool
	 */
	public function hasThumbnailPicture(): bool {
		return $this->ogp->hasImage();
	}

	/**
	 * サムネイル画像（OGP画像）のURLを取得
	 *
	 * @return string
	 */
	public function getThumbnailPictureUrl(): bool {
		return $this->ogp->getImage();
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
		if (! $this->ogp->hasImage()) {
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
		$imageUrl = $this->ogp->getImage();
		
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
		if ($this->ogp && $this->ogp->getTitle()) {
			return $this->ogp->getTitle();
		}
		return $this->title;
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
		if ($this->ogp && $this->ogp->getDescription()) {
			return $this->ogp->getDescription();
		}
		return $this->description;
	}
}
