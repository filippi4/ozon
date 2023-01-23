<?php


namespace KFilippovk\Ozon;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class OzonServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('ozon', function () {
            return new Ozon();
        });
    }
}
