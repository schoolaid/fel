<?php
namespace SchoolAid\FEL;

use Illuminate\Support\ServiceProvider;

class FELServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/fel.php' => config_path('fel.php'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/fel.php', 'fel'
        );
    }
}
