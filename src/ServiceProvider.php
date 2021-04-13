<?php


namespace SamuelJTaylor\NoiseGenerator;

use Illuminate\Support\ServiceProvider as BaseProvider;

class ServiceProvider extends BaseProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/noise_generator.php', 'noise_generator');

        $this->publishes([
            __DIR__.'/../config/noise_generator.php' => config_path('noise_generator.php'),
        ]);
    }
}
