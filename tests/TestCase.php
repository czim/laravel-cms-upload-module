<?php
namespace Czim\CmsUploadModule\Test;

use App\Console\Kernel;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Orchestra\Database\ConsoleServiceProvider;

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
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ConsoleServiceProvider::class,
        ];
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
