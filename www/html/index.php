<?php
use Hysryt\Bookmark\ContainerFactory;
use Hysryt\Bookmark\Controller\BookmarkController;
use Hysryt\Bookmark\Controller\NotFoundController;
use Hysryt\Bookmark\Framework\App;
use Hysryt\Bookmark\Framework\Config\Config;
use Hysryt\Bookmark\Framework\Emitter\Emitter;
use Hysryt\Bookmark\Framework\Router\Router;
use Hysryt\Bookmark\Framework\Router\RouterConfig;

require_once(__DIR__ . '/../inc/autoload.php');

// アプリケーション設定
$config = new Config([
    'debug' => true,
    'index.numPerPage' => 10,
    'repository.filepath' => '/var/www/bookmarks',
    'view.basedir' => '/var/www/view',
    'siteurl' => 'http://localhost:8080',
    'thumbnail.url' => 'http://localhost:8080/thumbnails',
    'thumbnail.dir' => '/var/www/html/thumbnails',
    'thumbnail.width' => 400,
    'thumbnail.height' => 210,
    'max_redirect' => 5,
]);

// ルート設定
$routerConfig = new RouterConfig();
$routerConfig->add('bookmark.index', 'GET', '/bookmarks/', BookmarkController::class, 'index');
$routerConfig->add('bookmark.createForm', 'GET', '/bookmarks/create/', BookmarkController::class, 'createForm');
$routerConfig->add('bookmark.createSubmit', 'POST', '/bookmarks/create/', BookmarkController::class, 'createSubmit');
$routerConfig->add('bookmark.show', 'GET', '/bookmarks/{id}/', BookmarkController::class, 'show');
$routerConfig->setNotFoundRoute(NotFoundController::class, 'do');

$container = ContainerFactory::create($config, $routerConfig);

$router = $container->get(Router::class);
$emitter = new Emitter();
$app = new App($router, $emitter);

try {
    $app->run($_SERVER, $_COOKIE, $_GET, $_POST, $_FILES, getallheaders());
} catch(Throwable $e) {
    if ($config->get('debug')) {
        var_dump($e);
    }
}
