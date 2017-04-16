<?php
namespace Czim\CmsUploadModule\Repositories;

use Czim\CmsUploadModule\Contracts\Repositories\FileRepositoryInterface;
use Czim\CmsUploadModule\Models\File;
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
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return (bool) File::destroy($id);
    }

}
