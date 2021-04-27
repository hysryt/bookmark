<?php

namespace Hysryt\Bookmark\Framework\Router;

use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Router {
    private ContainerInterface $container;
    private RouteList $routeList;
    private ?NotFoundRoute $notFoundRoute;

    public function __construct(ContainerInterface $container, RouterConfig $routerConfig) {
        $this->container = $container;
        $this->routeList = $routerConfig->getRouteList();
        $this->notFoundRoute = $routerConfig->getNotFoundRoute();
    }

    /**
     * リクエストを処理する
     * パスに含まれる情報を $request の pathAttributes 属性に格納したのちアクションを実行する
     * ルートが存在しない場合はsetNotFoundRouteで設定したルートで処理する
     * setNotFoundRouteを指定していない場合はLogicExceptionを投げる
     * 
     * @throws LogicException
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface {
        $route = $this->findRoute($request);

        // ルートが見つからない場合は404
        if ($route === null) {
            if ($this->notFoundRoute) {
                $route = new Route('GET', '/', $this->notFoundRoute->getControllerName(), $this->notFoundRoute->getActionName());
            } else {
                throw new LogicException('ルートが存在しない。ルートが存在しない可能性がある場合はRouteConfig::setNotFoundRouteで存在しない時のルートを設定する。');
            }
        }

        return $this->doAction($request, $route);
    }

    /**
     * リクエストに一致するルートを選択
     */
    private function findRoute(ServerRequestInterface $request): ?Route {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        return $this->routeList->find($method, $path);
    }

    /**
     * パスに含まれる情報を $request の pathAttributes 属性に格納したのちアクションを実行する
     * @throws LogicException アクションが存在しない
     */
    private function doAction(ServerRequestInterface $request, Route $route): ResponseInterface {
        // パスから情報を取得
        $pathAttributes = $route->getAttributes($request->getUri()->getPath());
        $request = $request->withAttribute('pathAttributes', $pathAttributes);

        $controllerName = $route->getControllerName();
        $actionName = $route->getActionName();

        // コントローラのインスタンスをDIコンテナから取得
        $controller = $this->container->get($controllerName);
        if (! method_exists($controller, $actionName)) {
            throw new LogicException("アクションが存在しない（{$controllerName}::{$actionName}）");
        }

        // アクション実行
        $response = $controller->{$actionName}($request);
        return $response;
    }
}