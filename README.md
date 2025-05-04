# File Scanning Package

A Laravel package for secure file scanning and validation. This package helps you validate files based on their MIME types and provides protection against potentially malicious file uploads.

## Installation

You can install the package via composer:

```bash
composer require hosametm/file-scanning
```

After installing the package, publish the configuration file:

```bash
php artisan vendor:publish --tag=file-scanning-config
```

## Configuration

The configuration file (`config/file-scanning.php`) contains two main sections:

1. `mime_types`: A list of allowed file extensions and their corresponding MIME types
2. `malicious_mime_types`: A list of MIME types that are considered potentially malicious

You can customize these lists according to your needs.

## Usage

### Basic Usage

```php
use Hosametm\FileScanning\FileScanner;

class YourController extends Controller
{
    protected $fileScanner;

    public function __construct(FileScanner $fileScanner)
    {
        $this->fileScanner = $fileScanner;
    }

    public function upload(Request $request)
    {
        $file = $request->file('file');
        
        if ($this->fileScanner->validate($file->getPathname())) {
            // File is valid, proceed with upload
        } else {
            // File is invalid or potentially malicious
        }
    }
}
```

### Available Methods

#### Validate a File
```php
$fileScanner->validate(string $filePath): bool
```

#### Get File MIME Type
```php
$fileScanner->getMimeType(string $filePath): ?string
```

#### Check if File is Malicious
```php
$fileScanner->isMalicious(string $filePath): bool
```

#### Get Extensions from MIME Type
```php
$fileScanner->getExtensionsFromMimeType(string $mimeType): array
```

## Security

This package helps protect your application by:
- Validating file MIME types
- Blocking potentially malicious file types
- Providing a configurable whitelist of allowed file types

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
