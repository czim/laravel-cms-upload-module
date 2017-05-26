<?php
namespace Czim\CmsUploadModule\Test\Integration\Controllers\Web;

use Czim\CmsUploadModule\Contracts\Support\Security\SessionGuardInterface;
use Czim\CmsUploadModule\Models\File;
use Czim\CmsUploadModule\Test\Integration\Controllers\AbstractControllerIntegrationTest;
use Illuminate\Http\UploadedFile;

class FileControllerTest extends AbstractControllerIntegrationTest
{

    // ------------------------------------------------------------------------------
    //      Store
    // ------------------------------------------------------------------------------

    /**
     * @test
     */
    function it_stores_a_file_without_validation()
    {
        $tmpPath = $this->prepareTempUploadedFilePath();

        $file = new UploadedFile($tmpPath, pathinfo($tmpPath, PATHINFO_BASENAME), filesize($tmpPath), 'text/plain', null, true);


        $this->call(
            'POST',
            route('cms::fileupload.file.upload'),
            [
                'name'      => 'upload_me.txt',
                'reference' => 'REF:testing',
            ],
            [],
            ['file' => $file],
            $this->getAjaxHeaders()
        )
            ->assertStatus(200)
            ->assertJson([
                'success'   => true,
                'id'        => 1,
                'reference' => 'REF:testing',
                'name'      => 'upload_me.txt',
                'size'      => 6,
                'mimetype'  => 'text/plain',
            ]);

        $this->assertDatabaseHas($this->prefixTable('file_uploads'), ['id' => 1]);

        if (file_exists($tmpPath)) {
            unlink($tmpPath);
        }
    }

    /**
     * @test
     */
    function it_stores_a_file_with_custom_validation()
    {
        $tmpPath = $this->prepareTempUploadedFilePath();

        $file = new UploadedFile($tmpPath, pathinfo($tmpPath, PATHINFO_BASENAME), filesize($tmpPath), 'text/plain', null, true);


        $this->call(
            'POST',
            route('cms::fileupload.file.upload'),
            [
                'name'       => 'upload_me.txt',
                'reference'  => 'REF:testing',
                'validation' => json_encode(['file', 'mimetypes:text/plain']),
            ],
            [],
            ['file' => $file],
            $this->getAjaxHeaders()
        )
            ->assertStatus(200)
            ->assertJson([
                'success'   => true,
                'id'        => 1,
                'reference' => 'REF:testing',
                'name'      => 'upload_me.txt',
                'size'      => 6,
                'mimetype'  => 'text/plain',
            ]);

        if (file_exists($tmpPath)) {
            unlink($tmpPath);
        }
    }

    /**
     * @test
     */
    function it_stores_a_file_with_custom_validation_as_a_string()
    {
        $tmpPath = $this->prepareTempUploadedFilePath();

        $file = new UploadedFile($tmpPath, pathinfo($tmpPath, PATHINFO_BASENAME), filesize($tmpPath), 'text/plain', null, true);


        $this->call(
            'POST',
            route('cms::fileupload.file.upload'),
            [
                'name'       => 'upload_me.txt',
                'reference'  => 'REF:testing',
                'validation' => json_encode('file|mimetypes:text/plain'),
            ],
            [],
            ['file' => $file],
            $this->getAjaxHeaders()
        )
            ->assertStatus(200)
            ->assertJson([
                'success'   => true,
                'id'        => 1,
                'reference' => 'REF:testing',
                'name'      => 'upload_me.txt',
                'size'      => 6,
                'mimetype'  => 'text/plain',
            ]);

        if (file_exists($tmpPath)) {
            unlink($tmpPath);
        }
    }
    
    /**
     * @test
     */
    function it_returns_422_if_no_file_is_given()
    {
        $this->call(
            'POST',
            route('cms::fileupload.file.upload'),
            [
                'name'      => 'upload_me.txt',
                'reference' => 'REF:testing',
            ], [],  [], $this->getAjaxHeaders()
        )
            ->assertStatus(422);
    }

    /**
     * @test
     */
    function it_returns_error_response_if_custom_validation_fails()
    {
        $tmpPath = $this->prepareTempUploadedFilePath();

        $file = new UploadedFile($tmpPath, pathinfo($tmpPath, PATHINFO_BASENAME), filesize($tmpPath), 'text/plain', null, true);

        $this->call(
            'POST',
            route('cms::fileupload.file.upload'),
            [
                'name'       => 'upload_me.txt',
                'reference'  => 'REF:testing',
                'validation' => json_encode(['file', 'mimetypes:image/jpeg']),
            ],
            [],
            ['file' => $file],
            $this->getAjaxHeaders()
        )
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'error'   => 'The file must be a file of type: image/jpeg.',
            ]);
    }

    /**
     * @test
     */
    function it_returns_an_error_response_if_custom_validation_could_not_be_performed()
    {
        $tmpPath = $this->prepareTempUploadedFilePath();

        $file = new UploadedFile($tmpPath, pathinfo($tmpPath, PATHINFO_BASENAME), filesize($tmpPath), 'text/plain', null, true);

        $this->call(
            'POST',
            route('cms::fileupload.file.upload'),
            [
                'name'       => 'upload_me.txt',
                'reference'  => 'REF:testing',
                'validation' => json_encode(['thisruledoesnotexist']),
            ],
            [],
            ['file' => $file],
            $this->getAjaxHeaders()
        )
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'error'   => 'upload.error.validation-failed', // translation key
            ]);
    }

    /**
     * @test
     */
    function it_returns_an_error_response_when_attempting_to_upload_a_config_blocked_file_type()
    {
        $this->app['config']->set('cms-upload-module.upload.restrict.extensions.block', ['txt']);

        $tmpPath = $this->prepareTempUploadedFilePath();

        $file = new UploadedFile($tmpPath, pathinfo($tmpPath, PATHINFO_BASENAME), filesize($tmpPath), 'text/plain', null, true);

        $this->call(
            'POST',
            route('cms::fileupload.file.upload'),
            [
                'name'      => 'upload_me.txt',
                'reference' => 'REF:testing',
            ],
            [],
            ['file' => $file],
            $this->getAjaxHeaders()
        )
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'error'   => 'upload.error.disallowed-type', // translation key
            ]);
    }

    // ------------------------------------------------------------------------------
    //      Delete
    // ------------------------------------------------------------------------------

    /**
     * @test
     */
    function it_deletes_an_existing_uploaded_file()
    {
        // Seed a record
        $path   = $this->prepareFakeUploadedFilePath();
        $record = File::create([
            'reference' => 'REF:testing',
            'path'      => $path,
        ]);

        // Link it to session
        /** @var SessionGuardInterface $guard */
        $guard = $this->app->make(SessionGuardInterface::class);
        $guard->link($record->id);

        $this->call('DELETE', route('cms::fileupload.file.delete', [$record->id]))
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing($this->prefixTable('file_uploads'), ['id' => $record->id]);

        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * @test
     */
    function it_deletes_an_existing_uploaded_file_record_with_a_missing_file_silently()
    {
        // Seed a record
        $record = File::create([
            'reference' => 'REF:testing',
            'path'      => '/some/fake/path.txt',
        ]);

        // Link it to session
        /** @var SessionGuardInterface $guard */
        $guard = $this->app->make(SessionGuardInterface::class);
        $guard->link($record->id);

        $this->call('DELETE', route('cms::fileupload.file.delete', [$record->id]))
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing($this->prefixTable('file_uploads'), ['id' => $record->id]);
    }

    /**
     * @test
     */
    function it_returns_error_response_for_delete_if_upload_record_does_not_exist()
    {
        // Link fake ID to session
        /** @var SessionGuardInterface $guard */
        $guard = $this->app->make(SessionGuardInterface::class);
        $guard->link(999);

        $this->call('DELETE', route('cms::fileupload.file.delete', [999]))
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'error'   => 'upload.error.file-not-found', // translation key
            ]);
    }

    /**
     * @test
     */
    function it_returns_error_response_for_delete_if_upload_is_not_linked_to_session()
    {
        // Seed a record
        $record = File::create([
            'reference' => 'REF:testing',
            'path'      => '/some/fake/path.txt',
        ]);

        $this->call('DELETE', route('cms::fileupload.file.delete', [$record->id]))
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'error'   => 'upload.error.delete-failed', // translation key
            ]);
    }


    /**
     * Prepares temporary file for mock upload.
     *
     * @param string $extension
     * @return string
     */
    protected function prepareTempUploadedFilePath($extension = 'txt')
    {
        $stubPath = $this->getTestFilePath();
        $tmpPath  = sys_get_temp_dir() . '/' . str_random(10) . '.' . $extension;

        copy($stubPath, $tmpPath);

        return $tmpPath;
    }

    /**
     * Prepares fake file upload and returns path.
     *
     * @return string
     */
    protected function prepareFakeUploadedFilePath()
    {
        $stubPath = $this->getTestFilePath();
        $tmpPath  = storage_path(pathinfo($stubPath, PATHINFO_BASENAME));

        copy($stubPath, $tmpPath);

        return $tmpPath;
    }

    /**
     * @return string
     */
    protected function getTestFilePath()
    {
        return realpath(__DIR__ . '/../../../Helpers/stubs/upload_me.txt');
    }

}
