<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'namespace' => 'Orumad\LaravelRedsys\Controllers',
        'domain' => config('redsys.notificationDomain'),
    ],
    function () {
        // Redsys Notification
        Route::post(
            'redsys/notification',
            'RedsysNotificationController'
        )->name('redsys-notification');
    }
);
