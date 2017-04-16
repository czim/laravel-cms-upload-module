<?php
namespace Czim\CmsUploadModule\Contracts\Repositories;

use Illuminate\Support\Collection;

interface FileRepositoryInterface
{
    /**
     * Creates a new record for a semi-permanent uploaded file.
     *
     * @param string $path      local path to uploaded file
     * @param array  $data
     * @return \Czim\CmsUploadModule\Models\File
     */
    public function create($path, array $data);

    /**
     * Returns all uploaded files.
     *
     * @return Collection|\Czim\CmsUploadModule\Models\File[]
     */
    public function getAll();

    /**
     * Returns an uploaded file by ID.
     *
     * @param int $id
     * @return \Czim\CmsUploadModule\Models\File|null
     */
    public function findById($id);

    /**
     * Returns uploaded files by reference.
     *
     * @param string $reference
     * @return Collection|\Czim\CmsUploadModule\Models\File[]
     */
    public function findByReference($reference);

    /**
     * Deletes an uploaded file.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id);

}
