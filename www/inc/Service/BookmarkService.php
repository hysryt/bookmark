<?php

namespace Hysryt\Bookmark\Service;

use Exception;
use Hysryt\Bookmark\Framework\Exception\NetworkException;
use Hysryt\Bookmark\Framework\Exception\NotSupportedException;
use Hysryt\Bookmark\Lib\HttpMessage\Uri;
use Hysryt\Bookmark\Lib\ImageDownloader\ImageDownloader;
use Hysryt\Bookmark\Log\Log;
use Hysryt\Bookmark\Model\Bookmark;
use Hysryt\Bookmark\Model\SiteInfoScraper;
use Hysryt\Bookmark\Repository\ThumbnailRepository;
use Psr\Http\Client\ClientInterface;

class BookmarkService {
    private ThumbnailRepository $thumbnailRepository;
    private ClientInterface $client;

    public function __construct(ThumbnailRepository $thumbnailRepository, ClientInterface $client, ImageDownloader $imageDownloader) {
        $this->thumbnailRepository = $thumbnailRepository;
        $this->client = $client;
        $this->imageDownloader = $imageDownloader;
    }

    /**
     * $url で指定したサイトから情報を取得し、Bookmarkインスタンスを生成する。
     * $url に接続できないなどの理由で生成できない場合は NetworkException 例外を投げる。
     * $url で指定したサイトがHTML形式でない場合は NotSupportedException 例外を投げる。
     * 
     * @param Uri $url
     * @return ?Bookmark
     * @throws NetworkException
     * @throws NotSupportedException
     */
    public function createBookmark(Uri $url): ?Bookmark {
        $scraper = $this->createScraper($url);
	
		$url = $scraper->getUrl();
		$title = $scraper->getTitle();
		$description = trim(preg_replace('/\s\s+/', ' ', $scraper->getDescription()));

        $thumbnailFilename = null;
        if ($scraper->hasThumbnailPicture()) {
            $thumbnailUrl = $scraper->getThumbnailPictureUrl();
            $thumbnailFilename = $this->tryDownloadThumbnail($thumbnailUrl);
        }

		$bookmark = new Bookmark($url, $title, $description, $thumbnailFilename);

		return $bookmark;
    }

    private function createScraper(Uri $url) {
        try {
            return new SiteInfoScraper($url, $this->client);
        } catch (NetworkException $e) {
            Log::info("URLに接続できない {$url}");
            throw $e;
        }
    }

    private function tryDownloadThumbnail(string $url): ?string {
        try {
            return $this->downloadThumbnail($url);
        } catch(Exception $e) {
            Log::info("画像取得失敗 {$url}");
            return null;
        }
    }

    private function downloadThumbnail(string $url): string {
        $image = $this->imageDownloader->download($url);
        $filename = $this->thumbnailRepository->save($image);
        return $filename;
    }
}