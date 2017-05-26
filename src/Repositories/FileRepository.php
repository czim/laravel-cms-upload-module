<?php
namespace Czim\CmsUploadModule\Repositories;

use Carbon\Carbon;
use Czim\CmsUploadModule\Contracts\Repositories\FileRepositoryInterface;
use Czim\CmsUploadModule\Models\File;
use File as FileFacade;
use Illuminate\Support\Collection;

class FileRepository implements FileRepositoryInterface
{

    /**
     * Creates a new record for a semi-permanent uploaded file.
     *
     * @param string $path      local path to uploaded file
     * @param array  $data
     * @return \Czim\CmsUploadModule\Models\File
     */
    public function create($path, array $data)
    {
        $data = array_merge($data, ['path' => $path]);

        return File::create($data);
    }

    /**
     * Returns all uploaded files.
     *
     * @return Collection|File[]
     */
    public function getAll()
    {
        return File::all();
    }

    /**
     * Returns an uploaded file by ID.
     *
     * @param int $id
     * @return File|null
     */
    public function findById($id)
    {
        return File::find($id);
    }

    /**
     * Returns uploaded files by reference.
     *
     * @param string $reference
     * @return Collection|File[]
     */
    public function findByReference($reference)
    {
        return File::where('reference', $reference)->get();
    }

    /**
     * Deletes an uploaded file.
     *
     * @param int  $id
     * @param bool $unlink      if true, also deletes the referenced file
     * @return bool
     */
    public function delete($id, $unlink = true)
    {
        if ( ! ($file = $this->findById($id))) {
            return true;
        }

        if (FileFacade::exists($file->path)) {
            FileFacade::delete($file->path);
        }

        return $file->delete();
    }

    /**
     * Cleans up old upload records and files.
     *
     * @return int      Records deleted
     */
    public function cleanup()
    {
        // Find all files older than the given gc age
        $fileIds = File::query()
            ->where('created_at', '<', Carbon::now()->subMinutes($this->getGarbageAgeInMinutes()))
            ->pluck('id');

        if ( ! count($fileIds)) {
            return 0;
        }

        $success = 0;

        foreach ($fileIds as $fileId) {
            $success += (int) $this->delete($fileId);
        }

        return $success;
    }

    /**
     * Returns age in minutes after which records may be garbage collected.
     *
     * @return int
     */
    protected function getGarbageAgeInMinutes()
    {
        return (int) config('cms-upload-module.gc.age-minutes', 180);
    }

}
