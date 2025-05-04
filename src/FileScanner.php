<?php

namespace Hosametm\FileScanning;

use Symfony\Component\Mime\MimeTypes;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

class FileScanner
{
    protected array $allowedMimeTypes;
    protected array $maliciousMimeTypes;
    protected MimeTypes $mimeTypes;

    public function __construct()
    {
        $this->allowedMimeTypes = Config::get('file-scanning.mime_types', []);
        $this->maliciousMimeTypes = Config::get('file-scanning.malicious_mime_types', []);
        $this->mimeTypes = new MimeTypes();
    }

    /**
     * Validate a file against allowed and malicious MIME types
     *
     * @param string $filePath
     * @return bool
     * @throws InvalidArgumentException
     */
    public function validate(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File does not exist: {$filePath}");
        }

        $mimeType = $this->mimeTypes->guessMimeType($filePath);
        
        if (!$mimeType) {
            throw new InvalidArgumentException("Could not determine MIME type for file: {$filePath}");
        }

        // Check if the MIME type is in the malicious list
        if (in_array($mimeType, $this->maliciousMimeTypes)) {
            return false;
        }

        // If allowed MIME types are configured, check against them
        if (!empty($this->allowedMimeTypes)) {
            return in_array($mimeType, $this->allowedMimeTypes);
        }

        return true;
    }

    /**
     * Get the MIME type of a file
     *
     * @param string $filePath
     * @return string|null
     */
    public function getMimeType(string $filePath): ?string
    {
        return $this->mimeTypes->guessMimeType($filePath);
    }

    /**
     * Check if a file's MIME type is in the malicious list
     *
     * @param string $filePath
     * @return bool
     */
    public function isMalicious(string $filePath): bool
    {
        $mimeType = $this->getMimeType($filePath);
        return $mimeType && in_array($mimeType, $this->maliciousMimeTypes);
    }

    /**
     * Get the file extension from a MIME type
     *
     * @param string $mimeType
     * @return array
     */
    public function getExtensionsFromMimeType(string $mimeType): array
    {
        return $this->mimeTypes->getExtensions($mimeType);
    }
} 