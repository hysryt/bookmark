<?php

namespace Hysryt\Bookmark\Framework\Router;

class RouteList {
    private array $routes;
    private array $routeMap;

    public function __construct($routes = []) {
        $this->routes = $routes;
        $this->routeMap = array();
    }

    /**
     * ルートを追加
     * 
     * @param string $method
     * @param string $pathPattern
     * @param string $controllerName
     * @param string $actionName
     */
    public function add(string $name, string $method, string $pathPattern, string $controllerName, string $actionName) {
        $route = new Route($method, $pathPattern, $controllerName, $actionName);
        $this->routes[] = $route;
        $this->routeMap[$name] = $route;
    }

    /**
     * ルートを $request の情報をもとにフィルタリングし、最初の1つを返す
     * 
     * @param string $method
     * @param string $path
     * @return Route|null
     */
    public function find(string $method, string $path): ?Route {
        $routeList = $this->filterByMethod($method)
                          ->filterByPathPattern($path);

        if ($routeList->hasRoutes()) {
            return $routeList->first();
        }

        return null;
    }

    /**
     * メソッドでフィルタリング
     * 
     * @param string $method
     * @return RouteList
     */
    public function filterByMethod(string $method): RouteList {
        $filtered = array_filter($this->routes, function($route) use($method) {
            return $route->isMethod($method);
        });
        $filtered = array_values($filtered);
        return new self($filtered);
    }

    /**
     * パスでフィルタリング
     * 
     * @param string $path
     * @return RouteList
     */
    public function filterByPathPattern(string $path): RouteList {
        $filtered = array_filter($this->routes, function(Route $route) use($path) {
            return $route->matchPathPattern($path);
        });
        $filtered = array_values($filtered);
        return new self($filtered);
    }

    /**
     * ルートがあるかどうか
     * 
     * @return bool
     */
    public function hasRoutes(): bool {
        return count($this->routes) > 0;
    }

    /**
     * 名前でルートを取得
     * 
     * @param string $name
     */
    public function getRouteByName(string $name): ?Route {
        if (isset($this->routeMap[$name])) {
            return $this->routeMap[$name];
        }
        return null;
    }

    /**
     * 最初のルートを返す
     * 
     * @return Route
     */
    public function first(): Route {
        return $this->routes[0];
    }
}