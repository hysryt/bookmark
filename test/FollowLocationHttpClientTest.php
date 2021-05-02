<?php

namespace Hysryt\Bookmark\Test;

require_once(__DIR__ . '/../www/inc/autoload.php');

use Exception;
use Hysryt\Bookmark\Lib\FollowLocationHttpClient\Client;
use Hysryt\Bookmark\Lib\FollowLocationHttpClient\RedirectException;
use Hysryt\Bookmark\Lib\HttpMessage\Request;
use Hysryt\Bookmark\Lib\HttpMessage\ResponseFactory;

class FollowLocationHttpClientTest {
    public function testRedirect() {
        $responseFactory = new ResponseFactory();
        $client = new \Hysryt\Bookmark\Lib\HttpClient\Client($responseFactory);
        $client = new Client($client);

        $request = Request::create('GET', 'https://httpstat.us/301');
        $response = $client->sendRequest($request);

        assert($response->getStatusCode() === 200);
    }

    public function testTooManyRedirects() {
        $responseFactory = new ResponseFactory();
        $client = new \Hysryt\Bookmark\Lib\HttpClient\Client($responseFactory);
        $client = new Client($client, 0);

        $request = Request::create('GET', 'https://httpstat.us/301');

        try {
            $response = $client->sendRequest($request);
            throw new Exception();
        } catch(Exception $e) {
            assert($e instanceof RedirectException);
            assert($e->getCode() === RedirectException::TOO_MANY_REDIRECTS);
        }
    }

    public function testNotProvidedRedirectUrl() {
        $responseFactory = new ResponseFactory();
        $client = new \Hysryt\Bookmark\Lib\HttpClient\Client($responseFactory);
        $client = new Client($client);

        $request = Request::create('GET', 'https://hysryt.com/httptest/not_provided_redirect_url.php');

        try {
            $response = $client->sendRequest($request);
            throw new Exception();
        } catch(Exception $e) {
            assert($e instanceof RedirectException);
            assert($e->getCode() === RedirectException::NOT_PROVIDED_REDIRECT_URL);
        }
    }

    public function testInvalidRedirectUrl() {
        $responseFactory = new ResponseFactory();
        $client = new \Hysryt\Bookmark\Lib\HttpClient\Client($responseFactory);
        $client = new Client($client);

        $request = Request::create('GET', 'https://hysryt.com/httptest/invalid_redirect_url.php');

        try {
            $response = $client->sendRequest($request);
            throw new Exception();
        } catch(Exception $e) {
            assert($e instanceof RedirectException);
            assert($e->getCode() === RedirectException::INVALID_REDIRECT_URL);
        }
    }
}

$test = new FollowLocationHttpClientTest();
$test->testRedirect();
$test->testTooManyRedirects();
$test->testNotProvidedRedirectUrl();
$test->testInvalidRedirectUrl();