<?php

namespace Hysryt\Bookmark\Test;

use Hysryt\Bookmark\Framework\Router\Route;

require_once(__DIR__ . '/../www/vendor/autoload.php');

class RouteTest {
    public function testMethod() {
        $route = new Route('GET', '/', RouteTest::class, 'action');
        assert($route->isMethod('GET') === true);
        assert($route->isMethod('POST') === false);
    }

    public function testControllerActionName() {
        $route = new Route('GET', '/', RouteTest::class, 'index');
        $controllerName = $route->getControllerName();
        $actionName = $route->getActionName();
        assert($controllerName === 'Hysryt\Bookmark\Test\RouteTest');
        assert($actionName === 'index');
    }

    public function testMatchPathPattern() {
        $route = new Route('GET', '/test/', RouteTest::class, 'index');
        assert($route->matchPathPattern('/test/') === true);
        assert($route->matchPathPattern('/notmatch/') === false);
    }

    public function testMatchAttributePathPattern() {
        $route = new Route('GET', '/test/{id}/', RouteTest::class, 'index');
        assert($route->matchPathPattern('/test/') === false);
        assert($route->matchPathPattern('/test/10/') === true);

        $route = new Route('GET', '/test/{id}/edit/', RouteTest::class, 'index');
        assert($route->matchPathPattern('/test/edit/') === false);
        assert($route->matchPathPattern('/test/10/edit/') === true);
    }

    public function testGetAttribute() {
        $route = new Route('GET', '/test/{id}/', RouteTest::class, 'index');
        $attributes = $route->getAttributes('/test/10/');
        assert($attributes['id'] === '10');

        $route = new Route('GET', '/test/{id}/edit/', RouteTest::class, 'index');
        $attributes = $route->getAttributes('/test/20/edit/');
        assert($attributes['id'] === '20');
    }

    public function testPermalinkPath() {
        $route = new Route('GET', '/test/', RouteTest::class, 'index');
        assert($route->getPermalinkPath() === '/test/');

        $route = new Route('GET', '/test/{id}/', RouteTest::class, 'index');
        assert($route->getPermalinkPath(['id' => 1]) === '/test/1/');

        $route = new Route('GET', '/test/{id}/edit/{hello}/', RouteTest::class, 'index');
        assert($route->getPermalinkPath(['id' => 1, 'hello' => 'world']) === '/test/1/edit/world/');
    }
}

$test = new RouteTest();
$test->testMethod();
$test->testControllerActionName();
$test->testMatchPathPattern();
$test->testMatchAttributePathPattern();
$test->testGetAttribute();
$test->testPermalinkPath();