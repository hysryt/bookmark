<?php

namespace Hysryt\Bookmark\Model;

use Hysryt\Bookmark\Framework\Log\Log;

/**
 * Bookmarkを作成するクラス
 */
class BookmarkCreator {
	/**
	 * ブックマーク作成
	 *
	 * @param string $url
	 * @return Bookmark
	 */
	public function create(string $url): Bookmark {
		$scraper = new SiteInfoScraper($url);
	
		$url = $scraper->getUrl();
		$title = $scraper->getTitle();
		$description = trim(preg_replace('/\s\s+/', ' ', $scraper->getDescription()));
	
		$thumbnailFilename = null;
		if ( $scraper->hasThumbnailPicture() ) {
			$thumbnailFilename = $scraper->downloadThumbnailPicture(__DIR__ . '/../html/thumbnails', 400, 210);
			if ($thumbnailFilename === null) {
				Log::warning('画像取得失敗');
			}
		}

		$bookmark = new Bookmark($url, $title, $description, $thumbnailFilename);

		return $bookmark;
	}
}