<?php
namespace Czim\CmsUploadModule\Http\Controllers;

use Czim\CmsCore\Contracts\Core\CoreInterface;
use Czim\CmsUploadModule\Contracts\Repositories\FileRepositoryInterface;
use Czim\CmsUploadModule\Contracts\Support\Security\FileCheckerInterface;
use Czim\CmsUploadModule\Http\Requests\UploadFileRequest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Validator;

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
     * Action: store a file record.
     *
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
            return response()->json([
                'success' => false,
                'error'   => cms_trans('upload.error.disallowed-type'),
            ]);
        }

        // Apply custom request-specified validation
        $rules     = $request->getNormalizedValidationRules();
        $validator = $this->getCustomValidator($file, $rules);

        if ($validator) {
            try {
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'error'   => implode("\n", array_get($validator->getMessageBag()->toArray(), 'file', [])),
                    ]);
                }
            } catch (\Exception $e) {
                cms()->log('error', "Error applying validation custom rules for uploaded file", [
                    'file'  => $storePath,
                    'rules' => $rules,
                ]);
                return response()->json([
                    'success' => false,
                    'error'   => cms_trans('upload.error.validation-failed'),
                ]);
            }
        }

        $path = $file->move($storeDir, $fileName);

        if (false === $path || ! $this->files->exists($storePath)) {
            cms()->log('error', "Error moving uploaded file to {$storePath}");
            return response()->json([
                'success' => false,
                'error'   => cms_trans('upload.error.upload-failed'),
            ]);
        }

        $data = [
            'reference' => $request->input('reference'),
            'name'      => $request->input('name'),
            'uploader'  => $this->core->auth()->user()->getUsername(),
            'file_size' => $this->files->exists($path) ? $this->files->size($path) : null,
        ];

        if ( ! ($record = $this->fileRepository->create($path, $data))) {
            cms()->log('error', "Error saving record for uploaded file at {$storePath}");
            return response()->json([
                'success' => false,
                'error'   => cms_trans('upload.error.saving-record-failed'),
            ]);
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
     * Action: delete an existing file record.
     *
     * @param int $id
     * @return mixed
     */
    public function destroy($id)
    {
        if ( ! ($record = $this->fileRepository->findById($id))) {
            return response()->json([
                'success' => false,
                'error'   => cms_trans('upload.error.file-not-found'),
            ]);
        }

        if ($record->path && $this->files->exists($record->path)) {
            if ( ! $this->files->delete($record->path)) {
                cms()->log('error', "Failed to delete uploaded file at {$record->path}", ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'error'   => cms_trans('upload.error.file-not-found'),
                ]);
            }
        }

        if ( ! $this->fileRepository->delete($id)) {
            cms()->log('error', "Failed to delete uploaded file #{$id}", ['id' => $id]);
            return response()->json([
                'success' => false,
                'error'   => cms_trans('upload.error.file-not-found'),
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Gets a validator for the uploaded file with custom request-specified rules.
     *
     * @param UploadedFile      $file
     * @param array|string|null $rules
     * @return \Illuminate\Validation\Validator|null
     */
    protected function getCustomValidator(UploadedFile $file, $rules)
    {
        if (null === $rules) {
            return null;
        }

        if ( ! is_array($rules)) {
            $rules = explode('|', $rules);
        }

        return Validator::make(['file' => $file], ['file' => $rules]);
    }

}
