# Changelog

## Releases

### [1.0.3] - 2018-02-16

- Lowered varchar length from 255 to 191 in the `file_uploads` migration in order to support utf8mb4.
- Increased minimum required PHP version to reflect actual support.


### [1.0.2] - 2017-05-26

- Added configurable automatic garbage collection. 
- Added cleanup console command for manual garbage collection.
- Solved a relative path issue by using the `File` facade instead of FlySystem dependency injection.


[1.0.3]: https://github.com/czim/laravel-cms-upload-module/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/czim/laravel-cms-upload-module/compare/1.0.1...1.0.2
