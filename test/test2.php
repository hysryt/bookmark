<?php

require_once(__DIR__ . '/../www/inc/autoload.php');

use Hysryt\Bookmark\Framework\Log\FileLogger;
use Hysryt\Bookmark\Framework\Log\Log;
use Hysryt\Bookmark\Repository\BookmarkFileRepository;

try {
	// ログ設定
	$logger = new FileLogger(__DIR__ . '/../www/log/log.log', false, new DateTimeZone('asia/tokyo'));
	Log::addLogger('filelogger', $logger);

	// 登録されているBookmarkを取得
	$dataFilepath = __DIR__ . '/../www/bookmarks';
	$repo = new BookmarkFileRepository($dataFilepath);
	$bookmarkList = $repo->findAllOrderById();

	// タイトル表示
	foreach ($bookmarkList->toArray() as $bookmark) {
		echo $bookmark->getId() . ': ' . $bookmark->getTitle() . PHP_EOL;
	}

} catch(Exception $e) {
	Log::error($e->getMessage());
}
