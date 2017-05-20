<?php
namespace Czim\CmsUploadModule\Contracts\Support\Security;

interface FileCheckerInterface
{

    /**
     * Returns whether a file may be uploaded.
     *
     * @param string $fileName
     * @param string $mimeType
     * @return bool
     */
    public function check($fileName, $mimeType);

}
