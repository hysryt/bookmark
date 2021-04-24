<?php

require_once(__DIR__ . '/../www/inc/autoload.php');

use Hysryt\Bookmark\Framework\Log\FileLogger;
use Hysryt\Bookmark\Log\Log;
use Hysryt\Bookmark\Repository\BookmarkFileRepository;
use Hysryt\Bookmark\Service\BookmarkService;

try {
	// ログ設定
	$logger = new FileLogger(__DIR__ . '/../www/log/log.log', false, new DateTimeZone('asia/tokyo'));
	Log::addLogger('filelogger', $logger);

	// URLからBookmark情報取得
	$url = 'https://github.com/';
	$service = new BookmarkService(__DIR__ . '/thumbnail', 400, 210);
	$bookmark = $service->createBookmark($url);

	// 保存
	$dataFilepath = __DIR__ . '/../www/bookmarks';
	$repo = new BookmarkFileRepository($dataFilepath);
	$repo->add($bookmark);

} catch(Exception $e) {
	Log::error($e->getMessage());
}
