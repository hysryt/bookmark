<?php

namespace Hysryt\Bookmark\Service;

use Exception;
use Hysryt\Bookmark\Framework\Exception\NetworkException;
use Hysryt\Bookmark\Framework\Exception\NotSupportedException;
use Hysryt\Bookmark\Lib\HttpMessage\Request;
use Hysryt\Bookmark\Lib\HttpMessage\Uri;
use Hysryt\Bookmark\Lib\Image\ImageFactory;
use Hysryt\Bookmark\Log\Log;
use Hysryt\Bookmark\Model\Bookmark;
use Hysryt\Bookmark\Model\SiteInfoScraper;
use Psr\Http\Client\ClientInterface;

class BookmarkService {
    /** サムネイル画像を保存するディレクトリ */
    private string $thumbnailDir;

    /** 保存するサムネイル画像の横幅 */
    private int $thumbnailWidth;

    /** 保存するサムネイル画像の高さ */
    private int $thumbnailHeight;

    /**
     * コンストラクタ
     * 
     * @param string $thumbnailDir サムネイル画像を保存するディレクトリ
     */
    public function __construct(string $thumbnailDir, int $thumbnailWidth, int $thumbnailHeight, ClientInterface $client) {
        $this->thumbnailDir = $thumbnailDir;
        $this->thumbnailWidth = $thumbnailWidth;
        $this->thumbnailHeight = $thumbnailHeight;
        $this->client = $client;
    }

    /**
     * $url で指定したサイトから情報を取得し、Bookmarkインスタンスを生成する。
     * $url に接続できないなどの理由で生成できない場合は null を返す。
     * $url で指定したサイトがHTML形式でない場合は NotSupportedException 例外を投げる。
     * 
     * @param Uri $url
     * @return ?Bookmark
     * @throws NotSupportedException
     */
    public function createBookmark(Uri $url): ?Bookmark {
        try {
            $scraper = new SiteInfoScraper($url, $this->client);
        } catch (NetworkException $e) {
            Log::info("URLに接続できない {$url}");
            return null;
        } catch(NotSupportedException $e) {
            // 指定したサイトの形式に対応していない。
            // 適切なエラーメッセージを表示できるように throw する。
            throw $e;
        }
	
		$url = $scraper->getUrl();
		$title = $scraper->getTitle();
		$description = trim(preg_replace('/\s\s+/', ' ', $scraper->getDescription()));
	
        // サムネイル画像がある場合はダウンロード
        try {
            $thumbnailFilename = $this->downloadThumbnailIfExists($scraper);
        } catch(Exception $e) {
            Log::info("画像取得失敗 {$scraper->getThumbnailPictureUrl()}");
        }

		$bookmark = new Bookmark($url, $title, $description, $thumbnailFilename);

		return $bookmark;
    }

    private function downloadThumbnailIfExists(SiteInfoScraper $scraper): ?string {
        if ($scraper->hasThumbnailPicture()) {
            $url = $scraper->getThumbnailPictureUrl();
            return $this->downloadThumbnail($url);
        }
        return null;
    }

    private function downloadThumbnail(string $url): string {
        $request = Request::create('GET', $url);
        $response = $this->client->sendRequest($request);
        $image = ImageFactory::fromString($response->getBody()->getContents());
        $image = $image->resize($this->thumbnailWidth, $this->thumbnailHeight);
        $hash = $image->hash();
        $filename = $hash . '.jpg';
        $filepath = $this->thumbnailDir . DIRECTORY_SEPARATOR . $filename;
        $image->saveAsJpeg($filepath);
        return $filename;
    }
}