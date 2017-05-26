<?php
namespace Czim\CmsUploadModule\Console\Commands;

use Czim\CmsUploadModule\Contracts\Repositories\FileRepositoryInterface;
use Illuminate\Console\Command;

class CleanUpFileUploads extends Command
{

    protected $signature = 'cms:upload:cleanup';

    protected $description = 'Performs garbage collection for file uploads';


    /**
     * Execute the console command.
     *
     * @param FileRepositoryInterface $repository
     */
    public function handle(FileRepositoryInterface $repository)
    {
        $deleted = $repository->cleanup();

        $this->info(
            'Cleaned up file uploads'
            . ($deleted ? " (deleted {$deleted} record(s))" : '')
            . '.'
        );
    }

}
