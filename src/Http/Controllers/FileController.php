<?php
namespace Czim\CmsUploadModule\Http\Controllers;

use Czim\CmsCore\Contracts\Core\CoreInterface;
use Czim\CmsUploadModule\Contracts\Repositories\FileRepositoryInterface;
use Czim\CmsUploadModule\Contracts\Support\Security\FileCheckerInterface;
use Czim\CmsUploadModule\Http\Requests\UploadFileRequest;
use Illuminate\Filesystem\Filesystem;

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
        $file = $request->file('file');

        $fileName  = uniqid('upload') . '.' . $file->extension();
        $storeDir  = rtrim(config('cms-upload-module.upload.path'), '/');
        $storePath = $storeDir . '/' . $fileName;

        $mimeType = $file->getMimeType();

        if ( ! $this->fileChecker->check($file->getClientOriginalName(), $mimeType)) {
            abort(403, "Not allowed to upload file type");
        }

        $path = $file->move($storeDir, $fileName);

        if (false === $path || ! $this->files->exists($storePath)   ) {
            abort(500, "Error moving uploaded file to {$storePath}");
        }

        $data = [
            'reference' => $request->input('reference'),
            'name'      => $request->input('name'),
            'uploader'  => $this->core->auth()->user()->getUsername(),
            'file_size' => $this->files->exists($path) ? $this->files->size($path) : null,
        ];

        if ( ! ($record = $this->fileRepository->create($path, $data))) {
            abort("Error saving record for uploaded file at {$storePath}");
        }

        return response()->json([
            'success'   => true,
            'id'        => $record->getKey(),
            'reference' => $record->reference,
            'name'      => $record->name,
            'size'      => $record->file_size,
            'mimetype'  => $mimeType,
        ]);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function destroy($id)
    {
        if ( ! ($record = $this->fileRepository->findById($id))) {
            return abort(404, 'Could not find uploaded file');
        }

        if ($record->path && $this->files->exists($record->path)) {
            if ( ! $this->files->delete($record->path)) {
                return abort(500, "Failed to delete uploaded file at {$record->path}");
            }
        }

        if ( ! $this->fileRepository->delete($id)) {
            return abort(500, "Failed to delete uploaded file #{$id}");
        }

        return response()->json(['success' => true]);
    }

}
