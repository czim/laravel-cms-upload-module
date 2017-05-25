<?php
namespace Czim\CmsUploadModule\Contracts\Support\Security;

interface SessionGuardInterface
{

    /**
     * Returns whether the session guard is enabled.
     *
     * @return bool
     */
    public function enabled();

    /**
     * Links a given ID to this session.
     *
     * @param int $fileId
     * @return $this
     */
    public function link($fileId);

    /**
     * Unlinks a given ID from this session.
     *
     * @param int $fileId
     * @return $this
     */
    public function unlink($fileId);

    /**
     * Checks whether file ID belongs to this session.
     *
     * @param int $fileId
     * @return bool
     */
    public function check($fileId);

}
