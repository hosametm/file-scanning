<?php

namespace Hosametm\FileScanning\Tests;

use Hosametm\FileScanning\FileScanner;
use Orchestra\Testbench\TestCase;
use InvalidArgumentException;

class FileScannerTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Hosametm\FileScanning\FileScannerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('file-scanning.mime_types', [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
        ]);

        $app['config']->set('file-scanning.malicious_mime_types', [
            'application/x-msdownload',
            'application/x-dosexec',
        ]);
    }

    /** @test */
    public function it_validates_allowed_file_types()
    {
        $fileScanner = new FileScanner();
        
        // Create a temporary PDF file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, '%PDF-1.4');
        
        $this->assertTrue($fileScanner->validate($tempFile));
        
        unlink($tempFile);
    }

    /** @test */
    public function it_rejects_malicious_file_types()
    {
        $fileScanner = new FileScanner();
        
        // Create a temporary executable file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, 'MZ');
        
        $this->assertFalse($fileScanner->validate($tempFile));
        
        unlink($tempFile);
    }

    /** @test */
    public function it_throws_exception_for_nonexistent_file()
    {
        $fileScanner = new FileScanner();
        
        $this->expectException(InvalidArgumentException::class);
        $fileScanner->validate('/nonexistent/file.pdf');
    }

    /** @test */
    public function it_can_get_mime_type()
    {
        $fileScanner = new FileScanner();
        
        // Create a temporary PDF file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, '%PDF-1.4');
        
        $this->assertEquals('application/pdf', $fileScanner->getMimeType($tempFile));
        
        unlink($tempFile);
    }

    /** @test */
    public function it_can_check_if_file_is_malicious()
    {
        $fileScanner = new FileScanner();
        
        // Create a temporary executable file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, 'MZ');
        
        $this->assertTrue($fileScanner->isMalicious($tempFile));
        
        unlink($tempFile);
    }
} 