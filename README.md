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

The configuration file (`config/file-scanning.php`) contains three main sections:

1. `temp_directory`: The directory where temporary files will be stored when downloading files from URLs (defaults to system temp directory)
2. `mime_types`: A list of allowed file extensions and their corresponding MIME types
3. `malicious_mime_types`: A list of MIME types that are considered potentially malicious

You can customize these settings according to your needs.

## Usage

### Basic Usage with File Uploads

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
        
        if ($this->fileScanner->validateUpload($file)) {
            // File is valid, proceed with upload
        } else {
            // File is invalid or potentially malicious
        }
    }
}
```

### Validating Files from URLs

```php
use Hosametm\FileScanning\FileScanner;

class YourController extends Controller
{
    protected $fileScanner;

    public function __construct(FileScanner $fileScanner)
    {
        $this->fileScanner = $fileScanner;
    }

    public function validateUrl(Request $request)
    {
        $url = $request->input('file_url');
        
        if ($this->fileScanner->validateUrl($url)) {
            // File from URL is valid, proceed with processing
        } else {
            // File is invalid or potentially malicious
        }
    }
}
```

### Available Methods

#### Validate a File Path
```php
$fileScanner->validate(string $filePath): bool
```

#### Validate an Uploaded File
```php
$fileScanner->validateUpload(\Illuminate\Http\UploadedFile $file): bool
```

#### Validate a File from URL
```php
$fileScanner->validateUrl(string $url): bool
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
- Securely handling file uploads and URL downloads
- Automatically cleaning up temporary files

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
