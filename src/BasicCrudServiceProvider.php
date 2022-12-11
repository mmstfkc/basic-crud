<?php

namespace Mmstfkc\BasicCrud;

use Illuminate\Support\ServiceProvider;

class BasicCrudServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/basicCrud.php', 'basicCrud');
        $this->publishes([__DIR__ . '/config/basicCrud.php' => config_path('basicCrud.php')]);
    }

    public function register()
    {

    }
}
