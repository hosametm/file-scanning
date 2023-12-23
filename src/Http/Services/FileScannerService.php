<?php

namespace Hosametm\FileScanning\Http\Services;

class FileScannerService
{
    static function getFileMimeType($fileUrl): string
    {
        // Fetch the file content
        $fileContent = file_get_contents($fileUrl);
        if ($fileContent !== false) {
            // Create a temporary file with the fetched content
            $tempFileName = tempnam(sys_get_temp_dir(), 'file_');
            file_put_contents($tempFileName, $fileContent);
            // Use the finfo extension to determine the file's MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tempFileName);
            finfo_close($finfo);
            // Clean up the temporary file
            unlink($tempFileName);

            return $mime;
        }
        throw new \Exception('File not found');
    }

    static function isFileSafe($fileUrl): bool
    {
        $mimeTypes = config('file-scanning.mime_types');
        $maliciousMimeTypes = config('file-scanning.malicious_mime_types');
        $fileExtension = explode('.', $fileUrl);
        $fileExtension = end($fileExtension);
        $fileExtension = strtolower($fileExtension);

        $mime = self::getFileMimeType($fileUrl);
        if (in_array($mime, $maliciousMimeTypes)) {
            throw new \Exception('File is malicious');
        }

        // Check if the MIME type matches the extension
        if ($mime !== $mimeTypes[$fileExtension]) {
            throw new \Exception('File is not safe');
        }

        return true;
    }
}
