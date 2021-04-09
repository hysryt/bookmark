<?php

namespace Hysryt\Bookmark\Test;

require_once(__DIR__ . '/../www/inc/autoload.php');

use Hysryt\Bookmark\Framework\Container\Container;
use Hysryt\Bookmark\Framework\Http\Request;
use Hysryt\Bookmark\Framework\Http\Response;
use Hysryt\Bookmark\Framework\Http\Uri;
use Hysryt\Bookmark\Framework\Router\Router;
use Hysryt\Bookmark\Framework\Router\RouterConfig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestStub extends Request {
    private string $method;
    private string $path;

    public function __construct($method, $path) {
        parent::__construct([],[],[],[],[],[]);
        $this->method = $method;
        $this->path = $path;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getUri() {
        return new Uri('https', '', 'example.com', 443, $this->path, '', '');
    }
}

class RouterTest {
    public function testDispatch() {
        $container = new Container();
        $container->setClosure(RouterTest::class, function() {
            return new RouterTest();
        });

        $routerConfig = new RouterConfig();
        $routerConfig->add('test', 'GET', '/test/', RouterTest::class, 'do');
        $router = new Router($container, $routerConfig);

        $request = new RequestStub('GET', '/test/');
        $response = $router->dispatch($request);
        assert($response->getStatusCode() === 200);
        assert($response->getBody()->getContents() === 'test body');
    }

    public function testDispatchNotFound() {
        $container = new Container();
        $container->setClosure(RouterTest::class, function() {
            return new RouterTest();
        });

        $routerConfig = new RouterConfig();
        $routerConfig->setNotFoundRoute(RouterTest::class, 'notfound');
        $router = new Router($container, $routerConfig);

        $request = new RequestStub('GET', '/notfound/');
        $response = $router->dispatch($request);
        assert($response->getStatusCode() === 404);
        assert($response->getBody()->getContents() === 'notfound body');
    }

    public function do(ServerRequestInterface $request): ResponseInterface {
        assert($request->getMethod() === 'GET');
        assert($request->getUri()->getPath() === '/test/');
        return Response::ok('test body');
    }

    public function notfound(ServerRequestInterface $request): ResponseInterface {
        assert($request->getMethod() === 'GET');
        assert($request->getUri()->getPath() === '/notfound/');
        return Response::notfound('notfound body');
    }
}

$test = new RouterTest();
$test->testDispatch();
$test->testDispatchNotFound();