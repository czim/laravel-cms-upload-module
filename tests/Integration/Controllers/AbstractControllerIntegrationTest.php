<?php
namespace Czim\CmsUploadModule\Test\Integration\Controllers;

use Czim\CmsCore\Core\BasicNotifier;
use Czim\CmsCore\Support\Enums\Component;
use Czim\CmsUploadModule\Test\CmsBootTestCase;
use Czim\CmsUploadModule\Test\Helpers\Core\MockApiBootChecker;
use Czim\CmsUploadModule\Test\Helpers\Core\MockWebBootChecker;
use Czim\CmsUploadModule\Test\Helpers\Http\Middleware\NullMiddleware;
use Czim\CmsUploadModule\Modules\UploadModule;
use Czim\CmsUploadModule\Providers\CmsUploadModuleServiceProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class AbstractControllerIntegrationTest extends CmsBootTestCase
{
    use DatabaseTransactions;

    /**
     * Whether to mock booting the API (instead of web).
     *
     * @var bool
     */
    protected $mockBootApi = false;


    /**
     * {@inheritdoc}
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        // Set up configuration for modules & models
        $app['config']->set('cms-modules.modules', [
            UploadModule::class,
        ]);

        // Adjust middleware to disable authorization
        $app['config']->set(
            'cms-core.middleware.load',
            array_merge(
                $app['config']->get('cms-core.middleware.load'),
                [
                    \Czim\CmsCore\Support\Enums\CmsMiddleware::AUTHENTICATED => NullMiddleware::class,
                    \Czim\CmsCore\Support\Enums\CmsMiddleware::GUEST         => NullMiddleware::class,
                    \Czim\CmsCore\Support\Enums\CmsMiddleware::PERMISSION    => NullMiddleware::class,
                ]
            )
        );

        $app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->pushMiddleware(\Illuminate\Session\Middleware\StartSession::class);

        // Required core bindings
        $app->singleton(Component::NOTIFIER, BasicNotifier::class);

        $app->register(CmsUploadModuleServiceProvider::class);
    }

    /**
     * @return string
     */
    protected function getTestBootCheckerBinding()
    {
        if ($this->mockBootApi) {
            return MockApiBootChecker::class;
        }

        return MockWebBootChecker::class;
    }

    /**
     * Returns the headers to send with a call to simulate an AJAX request.
     *
     * @return array
     */
    protected function getAjaxHeaders()
    {
        return [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_CONTENT_TYPE'     => 'application/json',
            'HTTP_ACCEPT'           => 'application/json',
        ];
    }

}
