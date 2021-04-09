<?php

namespace Hysryt\Bookmark\Framework\Router;

class RouterConfig {
    private RouteList $routeList;
    private ?NotFoundRoute $notFoundRoute;

    public function __construct() {
        $this->routeList = new RouteList();
        $this->notFoundRoute = null;
    }

    /**
     * ルートを追加
     * 
     * @param string $name
     * @param string $method GETかPOST
     * @param string $pathPattern
     * @param string $controllerName
     * @param string $actionName
     */
    public function add(string $name, string $method, string $pathPattern, string $controllerName, string $actionName) {
        $this->routeList->add($name, $method, $pathPattern, $controllerName, $actionName);
    }

    /**
     * ルートが見つからない時のルートを設定
     * 
     * @param string $controller
     * @param string $action
     */
    public function setNotFoundRoute(string $controller, string $action) {
        $this->notFoundRoute = new NotFoundRoute($controller, $action);
    }

    /**
     * ルートリストを取得
     * 
     * @return RouteList
     */
    public function getRouteList(): RouteList {
        return $this->routeList;
    }

    /**
     * ルートが見つからない時のルートを返す
     * 
     * @return ?NotFoundRoute
     */
    public function getNotFoundRoute(): ?NotFoundRoute {
        return $this->notFoundRoute;
    }
}