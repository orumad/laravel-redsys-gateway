<?php

namespace Orumad\LaravelRedsys;

use Illuminate\Support\ServiceProvider;

class LaravelRedsysServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes(
            [
                __DIR__ . '/config/redsys.php' => config_path('redsys.php'),
            ],
            'config'
        );

        if (!class_exists('CreateRedsysPaymentsTable')
            && !class_exists('CreateRedsysNotificationsTable')) {
            $this->publishes(
                [
                    __DIR__ . '/database/migrations/create_redsys_payments_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_redsys_payments_table.php'),
                    __DIR__ . '/database/migrations/create_redsys_notifications_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time() + 1) . '_create_redsys_notifications_table.php'),
                ],
                'migrations'
            );
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/redsys.php',
            'redsys'
        );
    }
}
