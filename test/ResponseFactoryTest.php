<?php

namespace Hysryt\Bookmark\Test;

use Hysryt\Bookmark\Lib\HttpMessage\ResponseFactory;

require_once(__DIR__ . '/../www/vendor/autoload.php');

class ResponseFactoryTest {
    public function testNormal() {
        $factory = new ResponseFactory();
        $response = $factory->createResponse();
        assert($response->getStatusCode() === 200);
        assert($response->getReasonPhrase() === '');
    }

    public function test404() {
        $factory = new ResponseFactory();
        $response = $factory->createResponse(404, 'Not found');
        assert($response->getStatusCode() === 404);
        assert($response->getReasonPhrase() === 'Not found');
    }
}

$test = new ResponseFactoryTest();
$test->testNormal();
$test->test404();