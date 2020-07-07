<?php

namespace Orumad\LaravelRedsys\Controllers;

use Illuminate\Http\Request;
use Orumad\LaravelRedsys\Events\RedsysNotificationArrived;
use Orumad\LaravelRedsys\Models\RedsysNotification;
use Orumad\LaravelRedsys\Models\RedsysPayment;

class RedsysNotificationController
{
    public function __invoke(
        Request $request,
        RedsysNotification $redsysNotification
    ) {
        if ($parameters = $request->input('Ds_MerchantParameters')) {
            $redsysNotification->setUp($parameters);

            // The signature must be valid
            if ($redsysNotification->isValidSignature($request->input('Ds_Signature'))) {
                $redsysPayment =
                    RedsysPayment::where('ds_merchant_order', $redsysNotification->ds_order)
                        ->firstOrFail();

                // Add notification to the payment (DB)
                $redsysPayment->redsysNotifications()->save($redsysNotification);

                // Emit event to notify the notification to the app
                event(new RedsysNotificationArrived($redsysNotification));
            }

            // Signature is invalid: do nothing
        }
    }
}
