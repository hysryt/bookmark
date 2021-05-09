<?php

namespace Hysryt\Bookmark\Test;

use Hysryt\Bookmark\Lib\Html\HtmlDocument;

require_once(__DIR__ . '/../../www/inc/autoload.php');

class HtmlDocumentTest {
    public function testAllNull() {
        $html = '';
        $doc = new HtmlDocument($html);
        assert($doc->parseTitle() === null);
        assert($doc->parseDescription() === null);
        assert($doc->isIndexable() === true);
    }

    public function testNormal() {
        $html = '<html><head><meta charset="utf-8"><title>テストタイトル</title><meta name="description" content="テストディスクリプション"></head></html>';
        $doc = new HtmlDocument($html);
        assert($doc->parseTitle() === 'テストタイトル');
        assert($doc->parseDescription() === 'テストディスクリプション');
        assert($doc->isIndexable() === true);
    }

    public function testNoindex() {
        $html = '<html><head><meta name="robots" content="noindex"></head></html>';
        $doc = new HtmlDocument($html);
        assert($doc->isIndexable() === false);
    }

    public function testOgp() {
        $html = '<html><head><meta charset="utf-8"><meta property="og:title" content="OGPタイトル" /><meta property="og:description" content="OGPディスクリプション" /><meta property="og:image" content="https://example.com/ogp.jpg" /><meta property="og:url" content="https://example.com" /></head></html>';
        $doc = new HtmlDocument($html);
        $ogp = $doc->parseOgp();
        assert($ogp->getTitle() === 'OGPタイトル');
        assert($ogp->getDescription() === 'OGPディスクリプション');
        assert($ogp->getImage() === 'https://example.com/ogp.jpg');
        assert($ogp->getUrl() === 'https://example.com');
    }
}

$test = new HtmlDocumentTest();
$test->testAllNull();
$test->testNormal();
$test->testNoindex();
$test->testOgp();