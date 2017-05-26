<?php
namespace Czim\CmsUploadModule\Test\Repositories;

use Czim\CmsUploadModule\Models\File;
use Czim\CmsUploadModule\Repositories\FileRepository;
use Czim\CmsUploadModule\Test\TestCase;
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

        $repository = new FileRepository;

        static::assertTrue($repository->delete(2));

        $this->notSeeInDatabase('file_uploads', ['id' => 2]);
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

}
