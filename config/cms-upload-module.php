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

        // Restrictions on uploaded content.
        //
        // Use this to globally block uploading certain content.
        // IMPORTANT: Do NOT rely on this for *real* security.
        //            These measures can always be bypassed.
        //            Make sure that no uploaded content can be executed.
        //
        'restrict' => [

            // Extensions must be exact matches, no wildcards are allowed.
            'extensions' => [
                // To allow anything, set to null.
                'allow' => null,
                // This blacklist will override the allow whitelist.
                'block' => [
                    'exe',
                    'php',
                    'sh'
                ],
            ],

            // Mimetypes may be checked against wildcards (? and *; f.i.: 'image/*').
            'mimetypes' => [
                // To allow anything, set to null.
                'allow' => null,
                // This blacklist will override the allow whitelist.
                'block' => [
                    'application/x-bsh',
                    'application/x-sh',
                    'application/x-shar',
                    'text/x-script.sh',
                ],
            ],
        ],
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
