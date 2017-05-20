<?php
namespace Czim\CmsUploadModule\Support\Security;

use Czim\CmsUploadModule\Contracts\Support\Security\FileCheckerInterface;

/**
 * Class FileChecker
 *
 * For checking whether uploaded files should be accepted or blocked.
 * Do not trust this as 'real' security! Names & mime types may be spoofed.
 */
class FileChecker implements FileCheckerInterface
{

    /**
     * Returns whether a file may be uploaded.
     *
     * @param string $fileName
     * @param string $mimeType
     * @return bool
     */
    public function check($fileName, $mimeType)
    {
        return $this->checkFileName($fileName) && $this->checkMimeType($mimeType);
    }

    /**
     * Checks whether the file name is acceptable for upload.
     *
     * @param string $fileName
     * @return bool
     */
    protected function checkFileName($fileName)
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allow = $this->getWhitelistedExtensions();
        $block = $this->getBlacklistedExtensions();

        return  (   (false === $allow || in_array($extension, $allow))
                &&  (false === $block || ! in_array($extension, $block))
                );
    }

    /**
     * Checks whether the mimetype is acceptable for upload.
     *
     * @param string $mimeType
     * @return bool
     */
    protected function checkMimeType($mimeType)
    {
        $allow = $this->getWhitelistedMimeTypes();
        $block = $this->getBlacklistedMimeTypes();

        if (false !== $allow) {
            foreach ($allow as $pattern) {
                if ($this->wildCardMatch($mimeType, $pattern)) {
                    return true;
                }
            }

            return false;
        }

        if (false !== $block) {
            foreach ($block as $pattern) {
                if ($this->wildCardMatch($mimeType, $pattern)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Returns whitelisted extensions, or false if no whitelist is set.
     *
     * @return bool|string[]
     */
    protected function getWhitelistedExtensions()
    {
        $allow = config('cms-upload-module.upload.restrict.extensions.allow', null);

        if ( ! is_array($allow) || ! count($allow)) {
            return false;
        }

        return array_map('trim', array_map('strtolower', $allow));
    }

    /**
     * Returns blacklisted extensions, or false if no whitelist is set.
     *
     * @return bool|string[]
     */
    protected function getBlacklistedExtensions()
    {
        $block = config('cms-upload-module.upload.restrict.extensions.block', null);

        if ( ! is_array($block) || ! count($block)) {
            return false;
        }

        return array_map('trim', array_map('strtolower', $block));
    }

    /**
     * Returns whitelisted mimetypes, or false if no whitelist is set.
     *
     * @return bool|string[]
     */
    protected function getWhitelistedMimeTypes()
    {
        $allow = config('cms-upload-module.upload.restrict.mimetypes.allow', null);

        if ( ! is_array($allow) || ! count($allow)) {
            return false;
        }

        return array_map('trim', array_map('strtolower', $allow));
    }

    /**
     * Returns blacklisted mimetypes, or false if no whitelist is set.
     *
     * @return bool|string[]
     */
    protected function getBlacklistedMimeTypes()
    {
        $block = config('cms-upload-module.upload.restrict.mimetypes.block', null);

        if ( ! is_array($block) || ! count($block)) {
            return false;
        }

        return array_map('trim', array_map('strtolower', $block));
    }

    /**
     * Returns whether a string matches against a wildcard pattern.
     *
     * May use * and ? wildcards.
     *
     * @param string $source
     * @param string $pattern
     * @return bool
     */
    protected function wildCardMatch($source, $pattern)
    {
        $pattern = preg_quote($pattern, '#');

        $pattern = str_replace('\?' , '.?', $pattern);
        $pattern = str_replace('\*' , '.*?', $pattern);

        return (bool) preg_match('#^' . $pattern . '$#i' , $source);
    }

}
