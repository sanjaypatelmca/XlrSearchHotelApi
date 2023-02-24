<?php

namespace  Xlr8rms\Hotelsearchapi;

use Illuminate\Support\ServiceProvider;

class HotelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Xlr8rms\Hotelsearchapi\SearchController');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');  
    }
}
