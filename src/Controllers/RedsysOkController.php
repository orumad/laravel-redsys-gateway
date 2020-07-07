<?php

namespace Orumad\LaravelRedsys\Controllers;

use Orumad\LaravelRedsys\Models\RedsysNotification;
use Orumad\LaravelRedsys\Models\RedsysPayment;

class RedsysOkController
{
    protected RedsysPayment $redsysPayment;
    protected RedsysNotification $redsysNotification;
    protected int $responseCode;
    protected string $responseText;

    public function __invoke()
    {
        $redsysNotification = new RedsysNotification;
        $redsysNotification->setUp(request()->input('Ds_MerchantParameters'));

        if ($redsysNotification->isValidSignature(request()->input('Ds_Signature'))) {
            $redsysPayment =
                RedsysPayment::where('ds_merchant_order', $redsysNotification->ds_order)
                    ->firstOrFail();

            // Load these protected properties to allow the child controller access them
            $this->redsysNotification = $redsysNotification;
            $this->redsysPayment = $redsysPayment;
            $this->responseCode = $redsysNotification->ds_response;
            $this->responseText = $redsysNotification->getResponseText();
        }

        // Invalid Data
    }
}
