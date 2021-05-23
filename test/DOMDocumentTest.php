<?php

namespace Hysryt\Bookmark\Test;

use DOMDocument;
use DOMXPath;

require_once(__DIR__ . '/../www/vendor/autoload.php');

class DOMDocumentTest {
    public function test() {
        $orig = libxml_use_internal_errors(true);
        $html = '<html><head><meta charset="sjis"></head><div>アイウエオ</div></html>';
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        var_dump($xpath->query('*/div')[0]);
        libxml_use_internal_errors($orig);
    }
}

$test = new DOMDocumentTest();
$test->test();