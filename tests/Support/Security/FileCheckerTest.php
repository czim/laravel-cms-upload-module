<?php
namespace Czim\CmsUploadModule\Test\Support\Security;

use Czim\CmsUploadModule\Support\Security\FileChecker;
use Czim\CmsUploadModule\Test\TestCase;

class FileCheckerTest extends TestCase
{

    /**
     * @test
     */
    function it_accepts_a_file_when_no_white_or_blacklists_are_configured()
    {
        $checker = new FileChecker;

        $this->app['config']->set('cms-upload-module.upload.restrict.extensions.allow', null);
        $this->app['config']->set('cms-upload-module.upload.restrict.extensions.block', null);
        $this->app['config']->set('cms-upload-module.upload.restrict.mimetypes.allow', null);
        $this->app['config']->set('cms-upload-module.upload.restrict.mimetypes.block', null);

        static::assertTrue($checker->check('somefile.exe', 'application/octet-stream'));
    }

    /**
     * @test
     */
    function it_accepts_files_with_a_whitelisted_extension_only()
    {
        $checker = new FileChecker;

        $this->app['config']->set('cms-upload-module.upload.restrict.extensions.allow', [
            'jpg',
            'png',
        ]);
        $this->app['config']->set('cms-upload-module.upload.restrict.extensions.block', null);
        $this->app['config']->set('cms-upload-module.upload.restrict.mimetypes.allow', null);
        $this->app['config']->set('cms-upload-module.upload.restrict.mimetypes.block', null);

        static::assertTrue($checker->check('somefile.jpg', 'image/jpeg'));
        static::assertTrue($checker->check('somefile.png', 'image/jpeg'));
        static::assertFalse($checker->check('somefile.exe', 'image/jpeg'));
        static::assertFalse($checker->check('somefile.', 'image/jpeg'));

        // Assert that empty extension can be whitelisted
        $this->app['config']->set('cms-upload-module.upload.restrict.extensions.allow', ['']);

        static::assertTrue($checker->check('somefile.', 'image/jpeg'), 'Empty extension not whitelistable');
    }

    /**
     * @test
     */
    function it_accepts_files_without_a_blacklisted_extensions_only()
    {
        $checker = new FileChecker;

        $this->app['config']->set('cms-upload-module.upload.restrict.extensions.allow', null);
        $this->app['config']->set('cms-upload-module.upload.restrict.extensions.block', ['exe', '']);
        $this->app['config']->set('cms-upload-module.upload.restrict.mimetypes.allow', null);
        $this->app['config']->set('cms-upload-module.upload.restrict.mimetypes.block', null);

        static::assertTrue($checker->check('somefile.jpg', 'image/jpeg'));
        static::assertTrue($checker->check('somefile.png', 'image/jpeg'));
        static::assertFalse($checker->check('somefile.exe', 'image/jpeg'));
        static::assertFalse($checker->check('somefile.', 'image/jpeg'));
    }

    /**
     * @test
     */
    function it_accepts_files_with_a_whitelisted_mimetype_only()
    {
        $checker = new FileChecker;

        $this->app['config']->set('cms-upload-module.upload.restrict.extensions.allow', null);
        $this->app['config']->set('cms-upload-module.upload.restrict.extensions.block', null);
        $this->app['config']->set('cms-upload-module.upload.restrict.mimetypes.allow', [
            'image/jpeg',
            'text/*',
            'image/jp?',
        ]);
        $this->app['config']->set('cms-upload-module.upload.restrict.mimetypes.block', null);

        static::assertTrue($checker->check('somefile.jpg', 'image/jpeg'));
        static::assertTrue($checker->check('somefile.txt', 'text/plain'));
        static::assertTrue($checker->check('somefile.jpg', 'image/jpg'));
        static::assertFalse($checker->check('somefile.png', 'image/png'));
        static::assertFalse($checker->check('somefile.exe', 'application/octet-stream'));
    }

    /**
     * @test
     */
    function it_accepts_files_with_a_blacklisted_mimetype_only()
    {
        $checker = new FileChecker;

        $this->app['config']->set('cms-upload-module.upload.restrict.extensions.allow', null);
        $this->app['config']->set('cms-upload-module.upload.restrict.extensions.block', null);
        $this->app['config']->set('cms-upload-module.upload.restrict.mimetypes.allow', null);
        $this->app['config']->set('cms-upload-module.upload.restrict.mimetypes.block', [
            'image/jpeg',
            'text/*',
            'image/jp?',
        ]);

        static::assertFalse($checker->check('somefile.jpg', 'image/jpeg'));
        static::assertFalse($checker->check('somefile.txt', 'text/plain'));
        static::assertFalse($checker->check('somefile.jpg', 'image/jpg'));
        static::assertTrue($checker->check('somefile.png', 'image/png'));
        static::assertTrue($checker->check('somefile.exe', 'application/octet-stream'));
    }

}
