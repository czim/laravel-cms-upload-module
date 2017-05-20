[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status](https://travis-ci.org/czim/laravel-cms-upload-module.svg?branch=master)](https://travis-ci.org/czim/laravel-cms-upload-module)
[![Coverage Status](https://coveralls.io/repos/github/czim/laravel-cms-upload-module/badge.svg?branch=master)](https://coveralls.io/github/czim/laravel-cms-upload-module?branch=master)

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
- [x] Security
    - [x] configuration
- [ ] Tests
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

[ico-version]: https://img.shields.io/packagist/v/czim/laravel-cms-upload-module.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/czim/laravel-cms-upload-module.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/czim/laravel-cms-upload-module
[link-downloads]: https://packagist.org/packages/czim/laravel-cms-upload-module
[link-author]: https://github.com/czim
[link-contributors]: ../../contributors
