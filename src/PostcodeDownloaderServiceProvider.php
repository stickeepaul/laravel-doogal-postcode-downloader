<?php

namespace stickeepaul\PostcodeDownloader;

use Illuminate\Support\ServiceProvider;
// use StickeePaul\PostcodeDownloader\Console\Commands\WriteCommand;
// use StickeePaul\PostcodeDownloader\Console\Commands\ImportCommand;

class PostcodeDownloaderServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/postcode-downloader.php', 'postcode-downloader'
        );
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/postcode-downloader.php' => config_path('postcode-downloader.php'),
            ], 'config');
        }
    }

    
}