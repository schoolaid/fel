<?php

namespace Schoolaid\Fel;

use Illuminate\Support\ServiceProvider;

class FelServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/fel.php' => config_path('fel.php'),
        ]);
    }
}