<?php

return [

    /*
    |--------------------------------------------------------------------------
    | File Uploading
    |--------------------------------------------------------------------------
    |
    | How and where files will be uploaded.
    |
    */

    'upload' => [

        // Where file are uploaded
        'path' => storage_path('cms_uploads'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Garbage Collection
    |--------------------------------------------------------------------------
    |
    | Garbage collection settings for cleaning up old uploads automatically.
    |
    */

    'gc' => [

        'enabled' => false,
    ],

];
