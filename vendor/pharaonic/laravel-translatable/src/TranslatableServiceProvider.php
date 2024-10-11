<?php

namespace Pharaonic\Laravel\Translatable;

use Illuminate\Support\ServiceProvider;
use Pharaonic\Laravel\Translatable\Commands\Transtable;

class TranslatableServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            Transtable::class
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishes
        $this->publishes([
            __DIR__ . '/config.php' => config_path('Pharaonic/translatable.php'),
        ], ['pharaonic', 'laravel-translatable']);
    }
}
