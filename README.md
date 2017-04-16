# CMS for Laravel - File uploader module

Simple file uploading module for the CMS.

This module offers functionality for simple temporary file uploads, to be used by other modules
to allow users of the CMS to upload files with AJAX requests.

To be used to with the [Laravel CMS Core](https://github.com/czim/laravel-cms-core).

## To Do

This is a work in progress.

- [x] Database migrations
- [x] File repository
- [x] Web controller and (ajax/json) responses
- [ ] Security
    - [ ] configuration
- [ ] Validation rules
- [ ] Garbage collection:
    - [ ] configuration
    - [ ] artisan command
    - [ ] auto-cleanup files on new uploads
- [ ] API controller & routes
- [ ] Use in cms-models file/image upload strategies (if module loaded)
    - [ ] make a strategy option to block use of this

## Installation

Add the module class to your `cms-modules.php` configuration file:

``` php
    'modules' => [
        // ...
        \Czim\CmsUploadModule\Modules\UploadModule::class,
    ],
```

Add the service provider to your `cms-core.php` configuration file:

``` php
    'providers' => [
        // ...
        Czim\CmsUploadModule\Providers\CmsUploadModuleServiceProvider::class,
        // ...
    ],
```

To publish the config:

``` bash
php artisan vendor:publish
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-contributors]: ../../contributors
