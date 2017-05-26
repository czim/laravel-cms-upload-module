[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status](https://travis-ci.org/czim/laravel-cms-upload-module.svg?branch=master)](https://travis-ci.org/czim/laravel-cms-upload-module)
[![Coverage Status](https://coveralls.io/repos/github/czim/laravel-cms-upload-module/badge.svg?branch=master)](https://coveralls.io/github/czim/laravel-cms-upload-module?branch=master)

# CMS for Laravel - File uploader module

Simple file uploading module for the CMS.

This module offers functionality for simple temporary file uploads, to be used by other modules
to allow users of the CMS to upload files with AJAX requests.

To be used to with the [Laravel CMS Core](https://github.com/czim/laravel-cms-core).

This package is compatible and tested with Laravel 5.3 and 5.4.

## To Do

This is a work in progress.

- [x] Database migrations
- [x] File repository
- [x] Web controller and (ajax/json) responses
- [x] Security
    - [x] configuration
- [x] Validation rules
- [ ] Tests
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


## The purpose of this module

This module is intended to help with situations where files should be uploaded using AJAX, before a form is submitted. 

The happy-path flow for a user using a CMS form:

1. Form displays.
2. User selects files for upload and enters some data.
3. User submits form.
4. Data is stored.

However, when a validation error or some other problem occurs after form submission:

1. Form displays.
2. User selects files for upload and enters some data.
3. User submits form.
4. Form validation errors display.
5. User fixes data.
6. *User re-selects all files for upload.*
7. User submits form.
8. Data is stored.


The upload inputs are annoyingly cleared, forcing the user to re-select their files.

### The solution

By uploading files asynchronously, the form can 'remember' a reference to these files.

1. Form displays.
2. User selects and uploads files and enters some data.
3. User submits form.
4. Form validation errors display.
5. User fixes data.
6. Data is stored.

Additionally, the files are only uploaded once, and the form submit request will be much smaller, since the files are already stored on the server.

This is the flow that this module facilitates.


## Usage

This module does not have a CMS 'presence' directly; it does not do anything on its own. 
Its functionality may be employed by other CMS modules.


### Storing a file

Files may be stored using the module's store route.
 
Send a `POST` request to `<base>/cms/fileupload/file`, with the following data:

- `file` (file content, required)  
    The file to be stored.

- `name` (string, required)  
    The (original) filename for the file to be stored. 

- `reference` (string)  
    An optional custom reference. This is not guaranteerd to be unique.

- `validation` (string, json)  
    Optional Laravel validation rules, encoded as JSON.
    If these are given, the file content will be validated using these rules;
    if validation fails, the upload is not stored and a validation error message is returned.
    
Don't forget to include a CSRF token (in any standard way that Laravel accepts).

An example success response returned by the server:

```json
{
    "success": true,
    "id": 79,
    "reference": "REF:804747507",
    "name": "test-file.jpg",
    "size": 129059,
    "mimetype": "image/jpeg"
}
```

The `id` value should be stored and used for further access to the file, as it is the unique identifier for this upload.

An example error response:

```json
{
    "success": false,
    "error": "The file must be a file of type: txt."
}
```

### Deleting a file

Files that have been uploaded (using the user's own session) may be deleted using the module's delete route.

Send a `DELETE` request to `<base>/cms/fileupload/file/{id}`.

Don't forget to include a CSRF token (in any standard way that Laravel accepts).


### Retrieving a file

File uploads each have a record that can be looked up using the `FileRepository`:

```php
$repository = app(\Czim\CmsUploadModule\Contracts\Repositories\FileRepositoryInterface::class);

// Look up an upload record by ID ...
$record = $repository->findById($fileRecordId);

// ... or by a manually set reference (returns a Collection)
$records = $repository->findByReference($referenceString);
```


### Security

As with any module, only authenticated CMS users can access its routes. 

Additionally a non-admin user must have the following permissions:

| Permission key         | Description       |
| ---------------------- | ----------------- |
| fileupload.file.create | Upload new files. |
| fileupload.file.delete | Delete (your own) uploaded files.  |

Or simply set `fileupload.file.*` for all of the above.

#### Session Guard

Asynchronous uploads require special attention to security.

In addition to requiring validation and checks during file upload as well as during form submission,
uploads are at risk of being hijacked by other CMS users.

This module offers a `SessionGuard` class to help prevent 
It is enabled by default: each upload will be linked to the user's session.

However, note that custom calls, such as accessing records through the `FileRepository` are not protected. 
The SessionGuard must be manually invoked to check:

```php
$guard = app(\Czim\CmsUploadModule\Contracts\Support\Security\SessionGuardInterface::class);

if ( ! $guard->check($fileRecordId)) {
    throw new \Exception('Not your file!');
}
```

The session guard may be disabled by setting the `cms-upload-module.upload.restrict.session` config key to `false`.

Security beyond these basics, such as linking uploads to (CMS) users specifically, are not available out of the box. This should be implemented in your application or CMS module, if required.

### Checking whether the module is available

When writing a CMS module that uses this upload module, it is recommended to create fall-back behavior,
in case the upload module is not available.

To check whether this module is available, you can ask the Core's module manager:

```php
/** @var Czim\CmsCore\Contracts\Modules\ModuleManagerInterface $moduleManager */
$moduleManager = app(\Czim\CmsCore\Support\Enums\Component::MODULES);

if ($moduleManager->has('file-uploader')) {
    // The file upload module is available...
}
```


### Example

For an example of this module in use, check out the [Models Module](https://github.com/czim/laravel-cms-models).

It features form display strategies for [Stapler file](https://github.com/czim/laravel-cms-models/blob/master/src/Strategies/Form/Display/AttachmentStaplerFileStrategy.php) 
and [image](https://github.com/czim/laravel-cms-models/blob/master/src/Strategies/Form/Display/AttachmentStaplerImageStrategy.php) 
uploads, and a [store strategy](https://github.com/czim/laravel-cms-models/blob/master/src/Strategies/Form/Store/StaplerStrategy.php) 
that uses the upload module if it is loaded.


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
