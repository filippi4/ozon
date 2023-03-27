<?php


namespace Filippi4\Ozon;

use Illuminate\Support\ServiceProvider;

class OzonPerformanceServiceProvider extends ServiceProvider
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
        $this->app->bind('ozon_performance', function () {
            return new OzonPerformance();
        });
    }
}
