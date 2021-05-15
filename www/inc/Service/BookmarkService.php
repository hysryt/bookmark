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
use Hysryt\Bookmark\Repository\ThumbnailRepository;
use Psr\Http\Client\ClientInterface;

class BookmarkService {
    private ThumbnailRepository $thumbnailRepository;
    private ClientInterface $client;

    public function __construct(ThumbnailRepository $thumbnailRepository, ClientInterface $client) {
        $this->thumbnailRepository = $thumbnailRepository;
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
        $filename = $this->thumbnailRepository->save($image);
        return $filename;
    }
}