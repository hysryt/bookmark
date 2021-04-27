<?php

namespace Hysryt\Bookmark\Framework;

use Hysryt\Bookmark\Framework\Emitter\EmitterInterface;
use Hysryt\Bookmark\Framework\Router\Router;
use Hysryt\Bookmark\Lib\HttpMessage\Request;

class App {
    private EmitterInterface $emitter;
    private Router $router;

    public function __construct(Router $router, EmitterInterface $emitter) {
        $this->router = $router;
        $this->emitter = $emitter;
    }

    public function run($server, $cookie, $get, $post, $file, $headers) {
        $request = new Request($server, $cookie, $get, $post, $file, getallheaders());
        $response = $this->router->dispatch($request);
        $this->emitter->emit($response);
    }
}