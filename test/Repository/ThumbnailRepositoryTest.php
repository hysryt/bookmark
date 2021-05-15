<?php

namespace Hysryt\Bookmark\Test;

use Hysryt\Bookmark\Lib\HttpClient\Client;
use Hysryt\Bookmark\Lib\HttpMessage\Request;
use Hysryt\Bookmark\Lib\HttpMessage\ResponseFactory;
use Hysryt\Bookmark\Lib\Image\ImageFactory;
use Hysryt\Bookmark\Repository\ThumbnailRepository;

require_once(__DIR__ . '/../../www/inc/autoload.php');

class ThumbnailRepositoryTest {
    public function testSaveImage() {
        $image = ImageFactory::fromString($this->getImageString());

        $repository = new ThumbnailRepository(__DIR__ . '/../thumbnail/', 100, 100);
        $repository->save($image);
    }

    private function getImageString() {
        $responseFactory = new ResponseFactory();
        $client = new Client($responseFactory);
        $request = Request::create('GET', 'https://hysryt.com/httptest/cat.jpg');
        $response = $client->sendRequest($request);
        return $response->getBody()->getContents();
    }
}

$test = new ThumbnailRepositoryTest();
$test->testSaveImage();