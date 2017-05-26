<?php
namespace Czim\CmsUploadModule\Http\Controllers\Api;

use Czim\CmsCore\Contracts\Core\CoreInterface;
use Czim\CmsUploadModule\Contracts\Repositories\FileRepositoryInterface;
use Czim\CmsUploadModule\Contracts\Support\Security\FileCheckerInterface;
use Czim\CmsUploadModule\Http\Controllers\Controller;
use Czim\CmsUploadModule\Http\Requests\UploadFileRequest;
use Illuminate\Filesystem\Filesystem;

/**
 * Class FileController
 *
 * @todo plan the API and implement this
 *
 * @codeCoverageIgnore
 */
class FileController extends Controller
{

    /**
     * @var FileRepositoryInterface
     */
    protected $fileRepository;

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var FileCheckerInterface
     */
    protected $fileChecker;

    /**
     * @param CoreInterface           $core
     * @param FileRepositoryInterface $fileRepository
     * @param Filesystem              $files
     * @param FileCheckerInterface    $fileChecker
     */
    public function __construct(
        CoreInterface $core,
        FileRepositoryInterface $fileRepository,
        Filesystem $files,
        FileCheckerInterface $fileChecker
    ) {
        parent::__construct($core);

        $this->files          = $files;
        $this->fileRepository = $fileRepository;
        $this->fileChecker    = $fileChecker;
    }

    /**
     * @param UploadFileRequest $request
     * @return mixed
     */
    public function store(UploadFileRequest $request)
    {
        // todo
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function destroy($id)
    {
        // todo
    }

}
