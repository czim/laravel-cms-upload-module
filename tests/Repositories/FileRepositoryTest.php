<?php
namespace Czim\CmsUploadModule\Test\Repositories;

use Carbon\Carbon;
use Czim\CmsUploadModule\Models\File;
use Czim\CmsUploadModule\Repositories\FileRepository;
use Czim\CmsUploadModule\Test\TestCase;
use File as FileFacade;
use Illuminate\Support\Collection;

class FileRepositoryTest extends TestCase
{

    /**
     * @test
     */
    function it_creates_a_new_file_record()
    {
        $repository = new FileRepository;

        $record = $repository->create('/test/path/some_file.txt', [
            'reference' => 'some reference',
            'name'      => 'some_file.txt',
            'uploader'  => 'test@user.com',
            'file_size' => 1000,
        ]);
        
        static::assertInstanceOf(File::class, $record);
        static::assertTrue($record->exists);
    }
    
    /**
     * @test
     */
    function it_returns_all_file_records()
    {
        $this->seedRecords();

        $repository = new FileRepository;

        $records = $repository->getAll();

        static::assertInstanceOf(Collection::class, $records);
        static::assertCount(2, $records);
        static::assertEquals(1, $records->first()->id);
        static::assertEquals(2, $records->last()->id);
    }

    /**
     * @test
     */
    function it_returns_a_file_record_by_id()
    {
        $this->seedRecords();

        $repository = new FileRepository;

        $record = $repository->findById(2);

        static::assertInstanceOf(File::class, $record);
        static::assertEquals(2, $record->id);
    }

    /**
     * @test
     */
    function it_returns_file_records_by_reference()
    {
        $this->seedRecords();

        $repository = new FileRepository;

        $records = $repository->findByReference('some reference');

        static::assertInstanceOf(Collection::class, $records);
        static::assertInstanceOf(File::class, $records->first());
        static::assertEquals(1, $records->first()->id);
    }

    /**
     * @test
     */
    function it_deletes_a_file_record_by_id()
    {
        $this->seedRecords();
        $this->prepareRecordFilePath(2);

        $repository = new FileRepository;

        static::assertTrue($repository->delete(2));

        $this->notSeeInDatabase('file_uploads', ['id' => 2]);
        static::assertFalse(FileFacade::exists(storage_path('testing.txt')));
    }

    /**
     * @test
     */
    function it_deletes_a_file_record_without_a_stored_file_silently()
    {
        $this->seedRecords();

        $repository = new FileRepository;

        static::assertTrue($repository->delete(2));

        $this->notSeeInDatabase('file_uploads', ['id' => 2]);
    }

    /**
     * @test
     */
    function it_silently_ignores_file_record_not_found_on_delete()
    {
        $repository = new FileRepository;

        static::assertTrue($repository->delete(999));
    }

    /**
     * @test
     */
    function it_performs_cleanup_by_removing_old_upload_records()
    {
        $this->seedRecords();

        /** @var File $oldFile */
        $oldFile = File::find(1);
        $oldFile->created_at = Carbon::now()->subMonth();
        $oldFile->updated_at = Carbon::now()->subMonth();
        $oldFile->save();

        $repository = new FileRepository;

        // Should delete just the first record
        static::assertEquals(1, $repository->cleanup());

        $this->seeInDatabase('file_uploads', ['id' => 2]);
        $this->notSeeInDatabase('file_uploads', ['id' => 1]);

        // Should silently return 0 if there's nothing to clean up
        static::assertEquals(0, $repository->cleanup());
    }


    protected function seedRecords()
    {
        File::create([
            'path'      => '/test/path/some_file.txt',
            'reference' => 'some reference',
            'name'      => 'some_file.txt',
            'uploader'  => 'test@user.com',
            'file_size' => 1000,
        ]);

        File::create([
            'path'      => '/test/path/another.txt',
            'reference' => '',
            'name'      => 'another.txt',
            'uploader'  => 'test@user.com',
            'file_size' => 600,
        ]);
    }

    /**
     * Prepares the first seeded records file path so it has an existing stored file.
     *
     * @param int $id
     */
    protected function prepareRecordFilePath($id)
    {
        $path = storage_path('testing.txt');

        FileFacade::put($path, 'test.');

        File::find($id)->update(['path' => $path]);
    }

}
