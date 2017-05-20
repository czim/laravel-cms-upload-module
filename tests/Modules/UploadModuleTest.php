<?php
namespace Czim\CmsModels\Test\Modules;

use Czim\CmsCore\Support\Enums\AclPresenceType;
use Czim\CmsUploadModule\Modules\UploadModule;
use Czim\CmsUploadModule\Test\TestCase;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;

class UploadModuleTest extends TestCase
{

    /**
     * @test
     */
    function it_returns_its_key()
    {
        $module = new UploadModule;

        static::assertEquals('file-uploader', $module->getKey());
    }

    /**
     * @test
     */
    function it_returns_its_name()
    {
        $module = new UploadModule;

        static::assertEquals('File Uploader', $module->getName());
    }

    /**
     * @test
     */
    function it_returns_its_version()
    {
        $module = new UploadModule;

        static::assertEquals(UploadModule::VERSION, $module->getVersion());
    }

    /**
     * @test
     */
    function it_returns_its_associated_class()
    {
        $module = new UploadModule;

        static::assertNull($module->getAssociatedClass());
    }

    /**
     * @test
     * @uses \Illuminate\Routing\Router
     */
    function it_maps_its_web_routes()
    {
        $router = app(Router::class);

        $module = new UploadModule;

        $module->mapWebRoutes($router);

        /** @var RouteCollection $routes */
        $routes = $router->getRoutes();
        static::assertCount(2, $routes);

        /** @var Route $route */
        $route = $routes->getRoutes()[0];
        static::assertEquals('fileupload/file', $route->uri());
    }

    /**
     * @test
     * @uses \Illuminate\Routing\Router
     */
    function it_maps_its_api_routes()
    {
        $router = app(Router::class);

        $module = new UploadModule;

        $module->mapApiRoutes($router);

        /** @var RouteCollection $routes */
        $routes = $router->getRoutes();
        static::assertCount(2, $routes);

        /** @var Route $route */
        $route = $routes->getRoutes()[0];
        static::assertEquals('fileupload/file', $route->uri());
    }

    /**
     * @test
     */
    function it_returns_its_acl_presence()
    {
        $module = new UploadModule;

        $presence = $module->getAclPresence();

        static::assertInternalType('array', $presence);

        $presence = $presence[0];

        static::assertInternalType('array', $presence, 'Presence child should be an array');
        static::assertEquals(AclPresenceType::GROUP, array_get($presence, 'type'));
        static::assertEquals(
            [
                'fileupload.file.create',
                'fileupload.file.delete',
            ],
            array_get($presence, 'permissions')
        );
    }

    /**
     * @test
     */
    function it_returns_its_menu_presence()
    {
        $module = new UploadModule;

        static::assertNull($module->getMenuPresence());
    }

}
