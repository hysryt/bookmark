<?php

namespace Hysryt\Bookmark\Test;

use Hysryt\Bookmark\Lib\HttpClient\Client;
use Hysryt\Bookmark\Lib\HttpMessage\Request;
use Hysryt\Bookmark\Lib\HttpMessage\ResponseFactory;

require_once(__DIR__ . '/../../www/inc/autoload.php');

class GdTest {
    public function testGetimagesizefromstring() {
        $str = $this->getImageString();
    }

    public function testImagecreatefromstring() {
        $str = $this->getImageString();
        $image = imagecreatefromstring($str);
    }

    private function getImageString() {
        $responseFactory = new ResponseFactory();
        $client = new Client($responseFactory);
        $request = Request::create('GET', 'https://hysryt.com/httptest/cat.jpg');
        $response = $client->sendRequest($request);
        return $response->getBody()->getContents();
    }
}

$test = new GdTest();
$test->testGetimagesizefromstring();
$test->testImagecreatefromstring();