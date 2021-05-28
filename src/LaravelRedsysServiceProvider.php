<?php

namespace Orumad\LaravelRedsys;

use Illuminate\Support\ServiceProvider;

class LaravelRedsysServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Routes
        require __DIR__.'/routes/routes.php';

        // Config file
        $this->publishes(
            [
                __DIR__.'/config/redsys.php' => config_path('redsys.php'),
            ],
            'config'
        );

        // Migrations
        $migrations = [];
        if (! class_exists('CreateRedsysPaymentsTable')) {
            $migrations[__DIR__.'/database/migrations/create_redsys_payments_table.php.stub'] = database_path('migrations/'.date('Y_m_d_His', time()).'_create_redsys_payments_table.php');
        }
        if (! class_exists('CreateRedsysNotificationsTable')) {
            $migrations[__DIR__.'/database/migrations/create_redsys_notifications_table.php.stub'] = database_path('migrations/'.date('Y_m_d_His', time() + 1).'_create_redsys_notifications_table.php');
        }
        if (! class_exists('AddCofFieldToRedsysNotificationsTable')) {
            $migrations[__DIR__.'/database/migrations/add_cof_field_to_redsys_notifications_table.php.stub'] = database_path('migrations/'.date('Y_m_d_His', time() + 1).'_add_cof_field_to_redsys_notifications_table.php');
        }
        if (! class_exists('AddCofFieldToRedsysPaymentsTable')) {
            $migrations[__DIR__.'/database/migrations/add_cof_field_to_redsys_payments_table.php.stub'] = database_path('migrations/'.date('Y_m_d_His', time() + 1).'_add_cof_field_to_redsys_payments_table.php');
        }
        ray('migrations', $migrations);
        $this->publishes($migrations, 'migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/redsys.php',
            'redsys'
        );
    }
}
