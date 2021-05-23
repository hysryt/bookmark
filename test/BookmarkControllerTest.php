<?php

namespace Hysryt\Bookmark\Test;

use Hysryt\Bookmark\Controller\BookmarkController;
use Hysryt\Bookmark\Framework\Container\Container;
use Hysryt\Bookmark\Framework\Http\HttpClient;
use Hysryt\Bookmark\Framework\Router\PermalinkFactory;
use Hysryt\Bookmark\Framework\Router\PermalinkFactoryInterface;
use Hysryt\Bookmark\Framework\Router\RouteList;
use Hysryt\Bookmark\Framework\Router\Router;
use Hysryt\Bookmark\Framework\Router\RouterConfig;
use Hysryt\Bookmark\Framework\View\TemplateEngine;
use Hysryt\Bookmark\Lib\HttpMessage\Request;
use Hysryt\Bookmark\Repository\BookmarkFileRepository;
use Hysryt\Bookmark\Service\BookmarkService;
use Hysryt\Bookmark\ViewObject\BookmarkViewFactory;

require_once(__DIR__ . '/../www/vendor/autoload.php');

$dataFilepath = __DIR__ . '/../www/bookmarks';
$repo = new BookmarkFileRepository($dataFilepath);

$viewDir = __DIR__ . '/template/';
$templateEngine = new TemplateEngine($viewDir);

$numPerPage = 10;
$container = new Container();
$router = new Router($container, new RouterConfig());

class PermalinkFactoryStub implements PermalinkFactoryInterface {
    public function create(string $name, array $data = []) {
        return 'https://example.com';
    }
}
$permalinkFactory = new PermalinkFactoryStub();
$bookmarkViewFactory = new BookmarkViewFactory('/', $permalinkFactory);
$httpClient = new HttpClient();
$bookmarkService = new BookmarkService(__DIR__ . '/thumbnail', 200, 200, $httpClient);
$controller = new BookmarkController($permalinkFactory, $templateEngine, $numPerPage, $repo, $bookmarkViewFactory, $bookmarkService);

$request = new Request(array(), array(), array(), array(), array(), array());
$response = $controller->index($request);

assert($response->getStatusCode() === 200);