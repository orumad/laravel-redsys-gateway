<?php

namespace Orumad\LaravelRedsys\Models;

use Illuminate\Database\Eloquent\Model;

class RedsysPayment extends Model
{
    protected $table = 'redsys_payments';

    public function redsysNotifications()
    {
        return $this->hasMany(RedsysNotification::class);
    }
}
