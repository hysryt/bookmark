<?php

namespace Hysryt\Bookmark\Framework\Router;

use LogicException;

class PermalinkFactory implements PermalinkFactoryInterface {
    private string $baseUrl;
    private RouteList $routeList;

    public function __construct(string $baseUrl, RouteList $routeList) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->routeList = $routeList;
    }

    /**
     * パーマリンク作成
     */
    public function create(string $name, array $data = []) {
        $route = $this->routeList->getRouteByName($name);
        if (! $route) {
            throw new LogicException('ルートが存在しない');
        }

        $path = preg_replace_callback('/\{([^\}]+)\}/', function($matches) use ($data) {
            return $data[$matches[1]];
        }, $route->getPathPattern());

        return $this->baseUrl . $path;
    }
}