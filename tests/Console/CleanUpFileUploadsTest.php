<?php
namespace Czim\CmsUploadModule\Test\Console;

use Czim\CmsUploadModule\Console\Commands\CleanUpFileUploads;
use Czim\CmsUploadModule\Contracts\Repositories\FileRepositoryInterface;
use Czim\CmsUploadModule\Test\TestCase;
use Mockery;

class CleanUpFileUploadsTest extends TestCase
{

    /**
     * @test
     */
    function it_cleans_up_old_file_upload_records()
    {
        $this->getConsoleKernel()->registerCommand(new CleanUpFileUploads);

        $mock = $this->getMockRepository();
        $mock->shouldReceive('cleanup')->once();

        $this->app->instance(FileRepositoryInterface::class, $mock);

        $this->artisan('cms:upload:cleanup');
    }

    /**
     * @return FileRepositoryInterface|Mockery\MockInterface|Mockery\Mock
     */
    protected function getMockRepository()
    {
        return Mockery::mock(FileRepositoryInterface::class);
    }

}
