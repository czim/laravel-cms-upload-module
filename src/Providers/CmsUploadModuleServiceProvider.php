<?php
namespace Czim\CmsUploadModule\Providers;

use Czim\CmsCore\Contracts\Core\CoreInterface;
use Czim\CmsCore\Support\Enums\Component;
use Czim\CmsUploadModule\Contracts\Repositories\FileRepositoryInterface;
use Czim\CmsUploadModule\Repositories\FileRepository;
use Illuminate\Support\ServiceProvider;

class CmsUploadModuleServiceProvider extends ServiceProvider
{

    /**
     * @var CoreInterface
     */
    protected $core;


    public function boot()
    {
        $this->bootConfig();
    }

    public function register()
    {
        $this->core = app(Component::CORE);

        $this->registerConfig()
             ->registerInterfaceBindings()
             ->publishMigrations();
    }


    /**
     * @return $this
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            realpath(dirname(__DIR__) . '/../config/cms-upload-module.php'),
            'cms-upload-module'
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function registerInterfaceBindings()
    {
        $this->app->singleton(FileRepositoryInterface::class, FileRepository::class);

        return $this;
    }

    /**
     * @return $this
     */
    protected function publishMigrations()
    {
        $this->publishes([
            realpath(dirname(__DIR__) . '/../migrations') => $this->getMigrationPath(),
        ], 'migrations');

        return $this;
    }

    /**
     * @return string
     */
    protected function getMigrationPath()
    {
        return database_path($this->core->config('database.migrations.path'));
    }

    /**
     * @return $this
     */
    protected function bootConfig()
    {
        $this->publishes([
            realpath(dirname(__DIR__) . '/../config/cms-upload-module.php') => config_path('cms-upload-module.php'),
        ]);

        return $this;
    }

}
