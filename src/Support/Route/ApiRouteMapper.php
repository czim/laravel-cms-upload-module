<?php
namespace Czim\CmsUploadModule\Support\Route;

use Illuminate\Routing\Router;

class ApiRouteMapper
{

    /**
     * @param Router $router
     */
    public function mapRoutes(Router $router)
    {
        $router->group(
            [
                'as'        => 'fileupload.',
                'prefix'    => 'fileupload',
                'namespace' => '\\Czim\\CmsUploadModule\\Http\\Controllers\\Api',
            ],
            function (Router $router)  {

            }
        );
    }

}
