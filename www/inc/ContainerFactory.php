<?php

namespace Hysryt\Bookmark;

use Hysryt\Bookmark\Framework\Config\Config;
use Hysryt\Bookmark\Framework\Container\Container;
use Hysryt\Bookmark\Framework\View\TemplateEngine;
use Hysryt\Bookmark\Controller\BookmarkController;
use Hysryt\Bookmark\Controller\NotFoundController;
use Hysryt\Bookmark\Framework\Router\PermalinkFactory;
use Hysryt\Bookmark\Framework\Router\Router;
use Hysryt\Bookmark\Framework\Router\RouterConfig;
use Hysryt\Bookmark\Lib\FollowLocationHttpClient\Client as FollowLocationClient;
use Hysryt\Bookmark\Lib\HttpClient\Client as HttpClient;
use Hysryt\Bookmark\Lib\HttpMessage\ResponseFactory;
use Hysryt\Bookmark\Repository\BookmarkFileRepository;
use Hysryt\Bookmark\Service\BookmarkService;
use Hysryt\Bookmark\ViewObject\BookmarkViewFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class ContainerFactory {
    public static function create(Config $config, RouterConfig $routerConfig): ContainerInterface {
        $container = new Container();
        
        // Config
        $container->setValue('config', $config);

        // Router
        $container->setValue('routerConfig', $routerConfig);
        $container->setClosure(Router::class, function($conn) {
            return new Router(
                $conn,
                $conn->get('routerConfig')
            );
        });
        $container->setClosure(PermalinkFactory::class, function($conn) {
            return new PermalinkFactory(
                $conn->get('config')->get('siteurl'),
                $conn->get('routerConfig')->getRouteList()
            );
        });

        // Factory
        $container->setClosure(ResponseFactoryInterface::class, function($conn) {
            return new ResponseFactory();
        });
        
        // Controller
        $container->setClosure(BookmarkController::class, function($con) {
            return new BookmarkController(
                $con->get(PermalinkFactory::class),
                $con->get(TemplateEngineInterface::class),
                $con->get('config')->get('index.numPerPage'),
                $con->get(BookmarkRepositoryInterface::class),
                $con->get(BookmarkViewFactory::class),
                $con->get(BookmarkService::class)
            );
        });
        $container->setClosure(NotFoundController::class, function($con) {
            return new NotFoundController();
        });

        // Service
        $container->setClosure(BookmarkService::class, function($con) {
            return new BookmarkService(
                $con->get('config')->get('thumbnail.dir'),
                $con->get('config')->get('thumbnail.width'),
                $con->get('config')->get('thumbnail.height'),
                new FollowLocationClient(new HttpClient(
                    $con->get(ResponseFactoryInterface::class)
                ), $con->get('config')->get('max_redirect'))
            );
        });
        
        // Repository
        $container->setClosure(BookmarkRepositoryInterface::class, function($con) {
            return new BookmarkFileRepository(
                $con->get('config')->get('repository.filepath')
            );
        });

        // ViewObjectFactory
        $container->setClosure(BookmarkViewFactory::class, function($con) {
            return new BookmarkViewFactory(
                $con->get('config')->get('thumbnail.url'),
                $con->get(PermalinkFactory::class)
            );
        });
        
        // TemplateEngine
        $container->setClosure(TemplateEngineInterface::class, function($con) {
            return new TemplateEngine(
                $con->get('config')->get('view.basedir'),
                $con->get('config')
            );
        });

        return $container;
    }
}