<?php

namespace Hysryt\Bookmark\Test;

use Exception;
use Hysryt\Bookmark\Framework\Http\Uri;
use InvalidArgumentException;

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

    public function testCreateFromUriString() {
        $uri = Uri::createFromUriString('https://test@example.com:8080/path/test?query=aaa#fragment');
        assert($uri->getScheme() === 'https');
        assert($uri->getUserInfo() === 'test');
        assert($uri->getHost() === 'example.com');
        assert($uri->getPort() === 8080);
        assert($uri->getPath() === '/path/test');
        assert($uri->getQuery() === 'query=aaa');
        assert($uri->getFragment() === 'fragment');

        $uri = Uri::createFromUriString('http://example.com');
        assert($uri->getScheme() === 'http');
        assert($uri->getHost() === 'example.com');
    }

    public function testCreateFromUriStringThrowsException() {
        try {
            $uri = Uri::createFromUriString('/file/system');
        } catch(Exception $e) {
            assert($e instanceof InvalidArgumentException);
        }
        
        try {
            $uri = Uri::createFromUriString('/file/system');
        } catch(Exception $e) {
            assert($e instanceof InvalidArgumentException);
        }
    }
}

$test = new UriTest();
$test->testHttp();
$test->testHttps();
$test->testFullUri();
$test->testCreateFromUriString();
$test->testCreateFromUriStringThrowsException();