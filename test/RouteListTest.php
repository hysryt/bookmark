<?php

namespace Hysryt\Bookmark\Test;

use Hysryt\Bookmark\Framework\Router\Route;
use Hysryt\Bookmark\Framework\Router\RouteList;

require_once(__DIR__ . '/../www/vendor/autoload.php');

class RouteListTest {
    public function testAddRoute() {
        $routeList = new RouteList();
        assert($routeList->hasRoutes() === false);

        $routeList->add('test1', 'GET', '/', 'cont', 'act');
        assert($routeList->hasRoutes() === true);

        $route = $routeList->first();
        $expect = new Route('GET', '/', 'cont', 'act');
        assert($route == $expect);
    }

    public function testFilterByMethod() {
        $routeList = new RouteList();
        $routeList->add('test1', 'GET', '/test1/', 'cont1', 'act1');
        $routeList->add('test2', 'POST', '/test2/', 'cont2', 'act2');
        $routeList->add('test3', 'GET', '/test3/', 'cont3', 'act3');

        $routeList = $routeList->filterByMethod('POST');
        assert($routeList->hasRoutes() === true);
        $route = $routeList->first();
        assert($route->getControllerName() === 'cont2');
    }

    public function testFilterByPathPattern() {
        $routeList = new RouteList();
        $routeList->add('test1', 'GET', '/test/', 'cont1', 'act1');
        $routeList->add('test2', 'GET', '/test/{id}/', 'cont2', 'act2');
        $routeList->add('test3', 'GET', '/test/{id}/edit/', 'cont3', 'act3');

        $routeList = $routeList->filterByPathPattern('/test/10/edit/');
        assert($routeList->hasRoutes() === true);
        $route = $routeList->first();
        assert($route->getControllerName() === 'cont3');
    }

    public function testFind() {
        $routeList = new RouteList();
        $routeList->add('test1', 'GET' , '/test/'         , 'cont1', 'act1');
        $routeList->add('test2', 'GET' , '/test/create/'  , 'cont2', 'act2');
        $routeList->add('test3', 'POST', '/test/create/'  , 'cont3', 'act3');
        $routeList->add('test4', 'GET' , '/test/{id}/'    , 'cont4', 'act4');
        $routeList->add('test5', 'GET' , '/test/{id}/edit', 'cont5', 'act5');
        $routeList->add('test6', 'POST', '/test/{id}/edit', 'cont6', 'act6');

        $route = $routeList->find('GET', '/test/10/');
        $expected = new Route('GET', '/test/{id}/', 'cont4', 'act4');
        assert($route == $expected);
    }

    public function testFindNotFoundRoute() {
        $routeList = new RouteList();
        $routeList->add('test1', 'GET' , '/test/'         , 'cont1', 'act1');
        $routeList->add('test2', 'GET' , '/test/create/'  , 'cont2', 'act2');
        $routeList->add('test3', 'POST', '/test/create/'  , 'cont3', 'act3');
        $routeList->add('test4', 'GET' , '/test/{id}/'    , 'cont4', 'act4');
        $routeList->add('test5', 'GET' , '/test/{id}/edit', 'cont5', 'act5');
        $routeList->add('test6', 'POST', '/test/{id}/edit', 'cont6', 'act6');

        $route = $routeList->find('GET', '/test/10/notfound/');
        assert($route === null);
    }
}

$test = new RouteListTest();
$test->testAddRoute();
$test->testFilterByMethod();
$test->testFilterByPathPattern();
$test->testFind();
$test->testFindNotFoundRoute();