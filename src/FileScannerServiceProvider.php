<?php

namespace Hosametm\FileScanning;

use Illuminate\Support\ServiceProvider;

class FileScannerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/config/file-scanning.php' => config_path('file-scanning.php'),
        ], 'file-scanning-config');

        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/config/file-scanning.php', 'file-scanning'
        );
    }

    public function register(): void
    {
        $this->app->singleton(FileScanner::class, function ($app) {
            return new FileScanner();
        });
    }
}
