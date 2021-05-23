<?php

namespace Hysryt\Bookmark\Test;

use Exception;
use Hysryt\Bookmark\Framework\Exception\NotSupportedException;
use Hysryt\Bookmark\Framework\Http\HttpClient;
use Hysryt\Bookmark\Lib\HttpMessage\Uri;
use Hysryt\Bookmark\Repository\ThumbnailRepository;
use Hysryt\Bookmark\Service\BookmarkService;

require_once(__DIR__ . '/../www/vendor/autoload.php');

class BookmarkServiceTest {
    public function testNormal() {
        $repository = new ThumbnailRepository(__DIR__ . '/thumbnail', 200, 200);
        $client = new HttpClient();
        $bookmarkService = new BookmarkService($repository, $client);
        $bookmark = $bookmarkService->createBookmark(Uri::createFromUriString('https://qiita.com/'));
        assert($bookmark->getTitle() === 'プログラマの技術情報共有サービス - Qiita');
        assert($bookmark->getDescription() === 'Qiitaは、プログラマのための技術情報共有サービスです。 プログラミングに関するTips、ノウハウ、メモを簡単に記録 &amp; 公開することができます。');
        assert((string)$bookmark->getUrl() === 'https://qiita.com/');
    }

    public function testUnreachableUrl() {
        $repository = new ThumbnailRepository(__DIR__ . '/thumbnail', 200, 200);
        $client = new HttpClient();
        $bookmarkService = new BookmarkService($repository, $client);
        // via https://qiita.com/mocklab/items/5aaa92225fe4c93d0898
        $bookmark = $bookmarkService->createBookmark(Uri::createFromUriString('https://api-responser.mock-lab.com/'));
        assert($bookmark === null);
    }

    public function testUnsupportType() {
        $repository = new ThumbnailRepository(__DIR__ . '/thumbnail', 200, 200);
        $client = new HttpClient();
        $bookmarkService = new BookmarkService($repository, $client);
        try {
            $bookmarkService->createBookmark(Uri::createFromUriString('https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png'));
            throw new Exception();
        } catch (Exception $e) {
            assert($e instanceof NotSupportedException);
        }
    }
}

$test = new BookmarkServiceTest();
$test->testNormal();
// $test->testUnreachableUrl();
$test->testUnsupportType();