<?php

namespace Orumad\LaravelRedsys\Controllers;

use Orumad\LaravelRedsys\Models\RedsysNotification;
use Orumad\LaravelRedsys\Models\RedsysPayment;

class RedsysNotificationController
{
    public function __invoke(
        RedsysNotification $redsysNotification
    ) {
        $redsysNotification->setUp(request()->input('Ds_MerchantParameters'));

        // The signature must be valid
        if ($redsysNotification->isValidSignature(request()->input('Ds_Signature'))) {
            $redsysPayment = RedsysPayment::where('DS_Merchan_Order', $redsysNotification->order)
                ->firstOrFail();

            // Add notification to the payment (DB)
            $redsysPayment->redsysNotifications()->save($redsysNotification);

            // Emit event to notify the notification to the app
        }

        // Signature is invalid: do nothing
    }
}
