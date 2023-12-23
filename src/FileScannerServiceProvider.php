<?php

namespace Hosametm\FileScanning;


use Illuminate\Support\ServiceProvider;

class FileScannerServiceProvider extends ServiceProvider
{

    public function boot(): void
    {

        // config
        $this->mergeConfigFrom(
            __DIR__ . '/config/file-scanning.php', 'file-scanning'
        );
        $this->publishes([
            __DIR__ . '/config/file-scanning.php' => config_path('file-scanning.php'),
        ], 'file-scanning-config');
    }


    public function register(): void
    {

    }
}
