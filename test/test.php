<?php

require_once(__DIR__ . '/../www/inc/autoload.php');

use Hysryt\Bookmark\Framework\Log\FileLogger;
use Hysryt\Bookmark\Log\Log;
use Hysryt\Bookmark\Model\BookmarkCreator;
use Hysryt\Bookmark\Repository\BookmarkFileRepository;

try {
	// ログ設定
	$logger = new FileLogger(__DIR__ . '/../www/log/log.log', false, new DateTimeZone('asia/tokyo'));
	Log::addLogger('filelogger', $logger);

	// URLからBookmark情報取得
	$url = 'https://github.com/';
	$bookmarkCreator = new BookmarkCreator();
	$bookmark = $bookmarkCreator->create($url);

	// 保存
	$dataFilepath = __DIR__ . '/../www/bookmarks';
	$repo = new BookmarkFileRepository($dataFilepath);
	$repo->add($bookmark);

} catch(Exception $e) {
	Log::error($e->getMessage());
}
