<?php

namespace Hysryt\Bookmark\Test;

use Exception;
use Hysryt\Bookmark\Framework\Http\Request;
use InvalidArgumentException;

require_once(__DIR__ . '/../www/inc/autoload.php');

class RequestTest {
    public function testNormal() {
        $request =  Request::create('GET', 'https://google.com');
        assert($request->getMethod() === 'GET');
        assert((string)$request->getUri() === 'https://google.com');
    }

    public function testInvalidUrl() {
        try {
            Request::create('GET', 'invalidUri');
            throw new Exception();
        } catch(Exception $e) {
            assert($e instanceof InvalidArgumentException);
        }
    }
}

$test = new RequestTest();
$test->testNormal();
$test->testInvalidUrl();