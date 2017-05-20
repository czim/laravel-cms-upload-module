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

                $router->group(
                    [
                        'as'         => 'file.',
                        'prefix'     => 'file',
                        'middleware' => [cms_mw_permission('fileupload.file.*')],
                    ],
                    function (Router $router) {

                        $router->post('/', [
                            'as'         => 'upload',
                            'middleware' => [cms_mw_permission('fileupload.file.create')],
                            'uses'       => 'FileController@store',
                        ]);

                        $router->delete('{id}', [
                            'as'         => 'delete',
                            'middleware' => [cms_mw_permission('fileupload.file.delete')],
                            'uses'       => 'FileController@destroy',
                        ]);
                    }
                );
            }
        );
    }

}
