<?php

namespace Hysryt\Bookmark\Test;

require_once(__DIR__ . '/../www/vendor/autoload.php');

use Exception;
use Hysryt\Bookmark\Lib\HttpClient\Client;
use Hysryt\Bookmark\Lib\HttpMessage\Request;
use Hysryt\Bookmark\Lib\HttpMessage\ResponseFactory;
use Psr\Http\Client\NetworkExceptionInterface;

class ClientTest {
    public function testOk() {
        $responseFactory = new ResponseFactory();
        $client = new Client($responseFactory);

        $request = Request::create('GET', 'https://httpstat.us/200');
        $response = $client->sendRequest($request);

        assert($response->getStatusCode() === 200);
        assert($response->getBody()->getContents() === '200 OK');
    }

    public function testRedirect() {
        $responseFactory = new ResponseFactory();
        $client = new Client($responseFactory);

        $request = Request::create('GET', 'https://httpstat.us/301');
        $response = $client->sendRequest($request);

        assert($response->getStatusCode() === 301);
        assert($response->getHeader('location')[0] === 'https://httpstat.us');
    }

    public function testNotfound() {
        $responseFactory = new ResponseFactory();
        $client = new Client($responseFactory);

        $request = Request::create('GET', 'https://httpstat.us/404');
        $response = $client->sendRequest($request);

        assert($response->getStatusCode() === 404);
        assert($response->getBody()->getContents() === '404 Not Found');
    }

    public function testTimeout() {
        $responseFactory = new ResponseFactory();
        $client = new Client($responseFactory, [
            'timeout' => 1,
        ]);

        $request = Request::create('GET', 'https://hysryt.com/httptest/timeout.php');

        try {
            $client->sendRequest($request);
            throw new Exception();
        } catch (Exception $e) {
            assert($e instanceof NetworkExceptionInterface);
        }
    }
}

$test = new ClientTest();
$test->testOk();
$test->testRedirect();
$test->testNotfound();
$test->testTimeout();