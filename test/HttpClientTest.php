<?php

namespace Hysryt\Bookmark\Test;

use Hysryt\Bookmark\Framework\Http\HttpClient;
use Hysryt\Bookmark\Lib\HttpMessage\Request;
use Hysryt\Bookmark\Lib\HttpMessage\Uri;

require_once(__DIR__ . '/../www/vendor/autoload.php');

class HttpClientTest {
    public function testNormal() {
        $client = new HttpClient();
        $request = new Request([],[],[],[],[],[]);
        $request = $request->withUri(Uri::createFromUriString('https://github.com/'));
        $response = $client->sendRequest($request);

        assert($response->getStatusCode() === 200);
    }
}

$test = new HttpClientTest();
$test->testNormal();