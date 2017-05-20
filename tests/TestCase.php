<?php
namespace Czim\CmsUploadModule\Test;

use App\Console\Kernel;
use Czim\CmsCore\Providers\CmsCoreServiceProvider;
use Czim\CmsCore\Support\Enums\Component;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cms-upload-module', include(realpath(dirname(__DIR__) . '/config/cms-upload-module.php')));

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', $this->getDatabaseConfigForSqlite());

        $app['config']->set('cms-modules.modules', []);
        $app['config']->set('cms-core.testing', true);

        $app['config']->set('cms-core.providers', [
            \Czim\CmsCore\Providers\ModuleManagerServiceProvider::class,
            \Czim\CmsCore\Providers\LogServiceProvider::class,
            \Czim\CmsCore\Providers\RouteServiceProvider::class,
            \Czim\CmsCore\Providers\MiddlewareServiceProvider::class,
            \Czim\CmsCore\Providers\MigrationServiceProvider::class,
            \Czim\CmsCore\Providers\ViewServiceProvider::class,
            //\Czim\CmsAuth\Providers\CmsAuthServiceProvider::class,
            //\Czim\CmsAuth\Providers\Api\OAuthSetupServiceProvider::class,
            \Czim\CmsCore\Providers\Api\CmsCoreApiServiceProvider::class,
            \Czim\CmsCore\Providers\Api\ApiRouteServiceProvider::class,
        ]);

        // Mock component bindings in the config
        $app['config']->set(
            'cms-core.bindings', [
            Component::BOOTCHECKER => $this->getTestBootCheckerBinding(),
            Component::CACHE       => \Czim\CmsCore\Core\Cache::class,
            Component::CORE        => \Czim\CmsCore\Core\Core::class,
            Component::MODULES     => \Czim\CmsCore\Modules\ModuleManager::class,
            Component::AUTH        => \Czim\CmsAuth\Auth\Authenticator::class,
            Component::API         => \Czim\CmsCore\Api\ApiCore::class,
            Component::ACL         => \Czim\CmsCore\Auth\AclRepository::class,
            Component::MENU        => \Czim\CmsCore\Menu\MenuRepository::class,
            Component::AUTH        => \Czim\CmsAuth\Auth\Authenticator::class,
        ]);

        $app->register(CmsCoreServiceProvider::class);
    }

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /**
     * Returns the testing config for a (shared) SQLite connection.
     *
     * @return array
     */
    protected function getDatabaseConfigForSqlite()
    {
        return [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ];
    }

    /**
     * Sets up the database for testing. This includes migration and standard seeding.
     */
    protected function setUpDatabase()
    {
        $this->migrateDatabase()
            ->seedDatabase();
    }

    /**
     * @return $this
     */
    protected function migrateDatabase()
    {
        // Note that although this will set up the migrated tables with the
        // prefix set by the CMS config, this will NOT use the cms:migrate
        // artisan context, so the migrations table will not be prefixed.

        $this->artisan('migrate', [
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__ . '/../migrations'),
        ]);

        return $this;
    }

    /**
     * Seeds the database with standard testing content.
     */
    protected function seedDatabase()
    {
    }

    /**
     * @param string $table
     * @return string
     */
    protected function prefixTable($table)
    {
        return 'cms_' . $table;
    }

    /**
     * @return ConsoleKernelContract|Kernel
     */
    protected function getConsoleKernel()
    {
        return $this->app[ConsoleKernelContract::class];
    }

    /**
     * @return string
     */
    protected function getTestBootCheckerBinding()
    {
        return \Czim\CmsCore\Core\BootChecker::class;
    }

}
