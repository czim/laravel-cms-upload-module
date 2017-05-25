<?php
namespace Czim\CmsUploadModule\Support\Security;

use Czim\CmsUploadModule\Contracts\Support\Security\SessionGuardInterface;

/**
 * Class SessionGuard
 *
 * Registry and checker to help prevent cross-session hijacking of uploads.
 */
class SessionGuard implements SessionGuardInterface
{
    const SESSION_KEY = 'cms-upload-module:file-session-guard';


    /**
     * Returns whether the session guard is enabled.
     *
     * @return bool
     */
    public function enabled()
    {
        return (bool) config('cms-upload-module.restrict.session', true);
    }

    /**
     * Links a given ID to this session.
     *
     * @param int $fileId
     * @return $this
     */
    public function link($fileId)
    {
        $links = session(static::SESSION_KEY, []);

        $links[ $fileId ] = true;

        session()->put(static::SESSION_KEY, $links);

        return $this;
    }

    /**
     * Unlinks a given ID from this session.
     *
     * @param int $fileId
     * @return $this
     */
    public function unlink($fileId)
    {
        if ( ! $this->isIdLinked($fileId)) {
            return $this;
        }

        $links = session(static::SESSION_KEY, []);

        unset($links[ $fileId ]);

        session()->put(static::SESSION_KEY, $links);

        return $this;
    }

    /**
     * Checks whether file ID belongs to this session.
     *
     * @param int $fileId
     * @return bool
     */
    public function check($fileId)
    {
        return $this->isIdLinked($fileId);
    }

    /**
     * Returns whether a given file ID is linked to the current session.
     *
     * @param int $fileId
     * @return bool
     */
    protected function isIdLinked($fileId)
    {
        return array_key_exists($fileId, session(static::SESSION_KEY, []));
    }

}
