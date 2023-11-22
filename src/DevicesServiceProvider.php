<?php

namespace Pharaonic\Laravel\Devices;

use Illuminate\Support\ServiceProvider;

class DevicesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {


    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/database/migrations/2021_02_02_000001_create_user_devices_table.php' => database_path('migrations/2021_02_02_000001_create_user_devices_table.php')
        ], ['pharaonic', 'laravel-user-devices', 'laravel-users']);
    }


}