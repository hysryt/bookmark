<?php

namespace Hysryt\Bookmark\Test;

use Exception;
use Hysryt\Bookmark\Lib\HttpMessage\UriFactory;
use InvalidArgumentException;

require_once(__DIR__ . '/../www/inc/autoload.php');

class UriFactoryTest {
    public function testNormal() {
        $factory = new UriFactory();
        $uri = $factory->createUri('https://google.com');
        assert((string)$uri === 'https://google.com');
        assert($uri->getScheme() === 'https');
        assert($uri->getHost() === 'google.com');
    }

    public function testInvalidUri() {
        try {
            $factory = new UriFactory();
            $uri = $factory->createUri('invaliduri');
            throw new Exception();
        } catch(Exception $e) {
            assert($e instanceof InvalidArgumentException);
        }
    }
}

$test = new UriFactoryTest();
$test->testNormal();
$test->testInvalidUri();