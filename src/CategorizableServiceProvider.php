<?php

namespace Pharaonic\Laravel\Categorizable;

use Illuminate\Support\ServiceProvider;

class CategorizableServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Config Merge
        $this->mergeConfigFrom(__DIR__ . '/config/categorizable.php', 'laravel-categorizable');

        // Migration Loading
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
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
            __DIR__ . '/config/categorizable.php'                                                       => config_path('Pharaonic/categorizable.php'),

            __DIR__ . '/database/migrations/2021_02_01_000010_create_categories_table.php'              => database_path('migrations/2021_02_01_000010_create_categories_table.php'),
            __DIR__ . '/database/migrations/2021_02_01_000011_create_category_translations_table.php'   => database_path('migrations/2021_02_01_000011_create_category_translations_table.php'),
            __DIR__ . '/database/migrations/2021_02_01_000012_create_categorizables_table.php'          => database_path('migrations/2021_02_01_000012_create_categorizables_table.php'),
        ], ['pharaonic', 'laravel-categorizable']);
    }
}
