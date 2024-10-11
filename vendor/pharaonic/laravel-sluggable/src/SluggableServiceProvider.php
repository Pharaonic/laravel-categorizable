<?php

namespace Pharaonic\Laravel\Sluggable;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class SluggableServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Blueprint::macro('sluggable', function () {
            $this->string('slug')->nullable();
        });
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
            __DIR__ . '/config.php' => config_path('Pharaonic/sluggable.php'),
        ], ['pharaonic', 'laravel-sluggable']);

        // Blade
        Blade::directive('slug', function ($data) {
            return "<?php echo slug($data); ?>";
        });
    }
}
