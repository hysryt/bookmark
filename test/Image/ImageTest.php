<?php

namespace Hysryt\Bookmark\Test;

use Hysryt\Bookmark\Lib\HttpClient\Client;
use Hysryt\Bookmark\Lib\HttpMessage\Request;
use Hysryt\Bookmark\Lib\HttpMessage\ResponseFactory;
use Hysryt\Bookmark\Lib\Image\Image;

require_once(__DIR__ . '/../../www/inc/autoload.php');

class ImageTest {
    public function testSave() {
        $gdImage = imagecreatefromstring($this->getImageString());
        $image = new Image($gdImage);
        $image->saveAsJpeg(__DIR__ . '/../thumbnail/test.jpg');
    }

    public function testResize() {
        $gdImage = imagecreatefromstring($this->getImageString());
        $image = new Image($gdImage);
        $image = $image->resize(200, 400);
        $image->saveAsJpeg(__DIR__ . '/../thumbnail/testresize.jpg');
    }

    private function getImageString() {
        $responseFactory = new ResponseFactory();
        $client = new Client($responseFactory);
        $request = Request::create('GET', 'https://hysryt.com/httptest/cat.jpg');
        $response = $client->sendRequest($request);
        return $response->getBody()->getContents();
    }
}

$test = new ImageTest();
$test->testSave();
$test->testResize();