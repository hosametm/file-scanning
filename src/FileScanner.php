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
    protected string $tempDirectory;

    public function __construct()
    {
        $this->allowedMimeTypes = Config::get('file-scanning.mime_types', []);
        $this->maliciousMimeTypes = Config::get('file-scanning.malicious_mime_types', []);
        $this->mimeTypes = new MimeTypes();
        $this->tempDirectory = Config::get('file-scanning.temp_directory', sys_get_temp_dir());
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

    /**
     * Validate an uploaded file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return bool
     * @throws InvalidArgumentException
     */
    public function validateUpload(\Illuminate\Http\UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        
        if (!$mimeType) {
            throw new InvalidArgumentException("Could not determine MIME type for uploaded file");
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
     * Validate a file from a URL
     *
     * @param string $url
     * @return bool
     * @throws InvalidArgumentException
     */
    public function validateUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid URL provided");
        }

        $tempFile = $this->downloadFile($url);
        
        try {
            return $this->validate($tempFile);
        } finally {
            // Clean up the temporary file
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    /**
     * Download a file from URL to a temporary location
     *
     * @param string $url
     * @return string Path to the temporary file
     * @throws InvalidArgumentException
     */
    protected function downloadFile(string $url): string
    {
        $tempFile = tempnam($this->tempDirectory, 'file_scan_');
        
        if ($tempFile === false) {
            throw new InvalidArgumentException("Could not create temporary file");
        }

        $ch = curl_init($url);
        $fp = fopen($tempFile, 'wb');
        
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $success = curl_exec($ch);
        
        curl_close($ch);
        fclose($fp);

        if (!$success) {
            unlink($tempFile);
            throw new InvalidArgumentException("Failed to download file from URL");
        }

        return $tempFile;
    }
} 