<?php

namespace Hysryt\Bookmark\Test;

use Hysryt\Bookmark\Controller\BookmarkController;
use Hysryt\Bookmark\Framework\Container\Container;
use Hysryt\Bookmark\Framework\Http\Request;
use Hysryt\Bookmark\Framework\Router\PermalinkFactory;
use Hysryt\Bookmark\Framework\Router\RouteList;
use Hysryt\Bookmark\Framework\Router\Router;
use Hysryt\Bookmark\Framework\Router\RouterConfig;
use Hysryt\Bookmark\Framework\View\TemplateEngine;
use Hysryt\Bookmark\Repository\BookmarkFileRepository;
use Hysryt\Bookmark\ViewObject\BookmarkViewFactory;

require_once(__DIR__ . '/../www/inc/autoload.php');

$dataFilepath = __DIR__ . '/../www/bookmarks';
$repo = new BookmarkFileRepository($dataFilepath);

$viewDir = __DIR__ . '/template/';
$templateEngine = new TemplateEngine($viewDir);

$numPerPage = 10;
$container = new Container();
$router = new Router($container, new RouterConfig());
$permalinkFactory = new PermalinkFactory('', new RouteList());
$bookmarkViewFactory = new BookmarkViewFactory('/', $permalinkFactory);
$controller = new BookmarkController($permalinkFactory, $templateEngine, $numPerPage, $repo, $bookmarkViewFactory);

$request = new Request(array(), array(), array(), array(), array(), array());
$response = $controller->index($request);

var_dump($response);
assert($response->getStatusCode() === 200);