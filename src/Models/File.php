<?php
namespace Czim\CmsUploadModule\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class File
 *
 * @property int    $id
 * @property string $name
 * @property string $path
 * @property string $reference
 * @property string $uploader
 * @property int    $file_size
 */
class File extends Model
{

    /**
     * @var string
     */
    protected $table = 'file_uploads';

    protected $fillable = [
        'name',
        'path',
        'reference',
        'uploader',
        'file_size',
    ];


    /**
     * Override to add configured database prefix
     *
     * {@inheritdoc}
     */
    public function getTable()
    {
        return $this->getCmsTablePrefix() . parent::getTable();
    }

    /**
     * Override to force the database connection
     *
     * {@inheritdoc}
     */
    public function getConnectionName()
    {
        return $this->getCmsDatabaseConnection() ?: $this->connection;
    }

    /**
     * @return string
     */
    protected function getCmsTablePrefix()
    {
        return config('cms-core.database.prefix', '');
    }

    /**
     * @return string|null
     */
    protected function getCmsDatabaseConnection()
    {
        return config('cms-core.database.driver');
    }

}
