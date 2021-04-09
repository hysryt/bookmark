<?php

namespace Hysryt\Bookmark\Test;

use Hysryt\Bookmark\Framework\Http\Uri;

require_once(__DIR__ . '/../www/inc/autoload.php');

class UriTest {
    public function testHttp() {
        $uri = new Uri('http', '', 'example.com', 80, '', '', '');
        assert('http://example.com' === (string)$uri);
    }

    public function testHttps() {
        $uri = new Uri('https', '', 'example.com', 443, '', '', '');
        assert('https://example.com' === (string)$uri);
    }

    public function testFullUri() {
        $uri = new Uri('https', 'testuser', 'example.com', 1024, '/index.html', 'key=value', 'frag');
        assert('https://testuser@example.com:1024/index.html?key=value#frag' === (string)$uri);
    }
}

$test = new UriTest();
$test->testHttp();
$test->testHttps();
$test->testFullUri();