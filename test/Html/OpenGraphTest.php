<?php

namespace Hysryt\Bookmark\Test;

use Hysryt\Bookmark\Lib\Html\OpenGraph;

require_once(__DIR__ . '/../../www/vendor/autoload.php');

class OpenGraphTest {
    public function testNormal() {
        $ogp = new OpenGraph([
            'og:title' => 'タイトル',
            'og:description' => '詳細',
            'og:image' => '画像URL',
            'og:url' => 'URL',
        ]);

        assert($ogp->getTitle() === 'タイトル');
        assert($ogp->getDescription() === '詳細');
        assert($ogp->getImage() === '画像URL');
        assert($ogp->getUrl() === 'URL');
    }
}

$test = new OpenGraphTest();
$test->testNormal();