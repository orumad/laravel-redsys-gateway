<?php

namespace Orumad\LaravelRedsys\Controllers;

use Orumad\LaravelRedsys\Models\RedsysNotification;
use Orumad\LaravelRedsys\Models\RedsysPayment;

class RedsysOkController
{
    public RedsysNotification $redsysNotification;
    public RedsysPayment $redsysPayment;
    public int $responseCode;
    public string $responseText;

    public function __invoke()
    {
        $redsysNotification = new RedsysNotification;
        $redsysNotification->setUp(request()->input('Ds_MerchantParameters'));

        if ($redsysNotification->isValidSignature(request()->input('Ds_Signature'))) {
            // dd($redsysNotification);

            $redsysPayment =
                RedsysPayment::where('ds_merchant_order', $redsysNotification->ds_order)
                    ->firstOrFail();

            $this->redsysNotification = $redsysNotification;
            $this->redsysPayment = $redsysPayment;
            $this->responseCode = $redsysNotification->ds_response;
            $this->responseText = $redsysNotification->getResponseText();
        }

        // Invalid Data
    }
}
