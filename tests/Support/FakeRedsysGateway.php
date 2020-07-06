<?php

namespace Orumad\LaravelRedsys\Tests\Support;

use Orumad\LaravelRedsys\Helpers\CryptHelper;
use Orumad\LaravelRedsys\Models\RedsysPaymentRequest;

/**
 * Class FakeRedsysGateway
 * @package Xoborg\LaravelRedsys\Tests\Support
 */
class FakeRedsysGateway
{
    /**
     * @var RedsysPaymentRequest
     */
    private $paymentRequest;

    public function __construct(RedsysPaymentRequest $paymentRequest)
    {
        $this->paymentRequest = $paymentRequest;
    }

    public function notificationResponse(): array
    {
        $merchantParameters = $this->generateMerchantParameters();
        $signature = $this->generateSignature($merchantParameters);

        return [
            'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
            'Ds_MerchantParameters' => $merchantParameters,
            'Ds_Signature' => $signature
        ];
    }

    private function generateMerchantParameters(): string
    {
        $merchantParameters = base64_encode(json_encode([
            'Ds_Date' => now()->format('d/m/Y'),
            'Ds_Hour' => now()->format('H:i'),
            'Ds_Amount' => $this->paymentRequest->amount,
            'Ds_Currency' => $this->paymentRequest->currency,
            'Ds_Order' => $this->paymentRequest->order,
            'Ds_MerchantCode' => $this->paymentRequest->merchantCode,
            'Ds_Terminal' => $this->paymentRequest->terminal,
            'Ds_Response' => '0000',
            'Ds_MerchantData' => $this->paymentRequest->merchantData,
            'Ds_SecurePayment' => 0,
            'Ds_TransactionType' => $this->paymentRequest->transactionType,
            'Ds_Card_Brand' => 1
        ]));

        return $merchantParameters;
    }

    private function generateSignature(string $merchantParameters): string
    {
        $key = base64_decode(config('redsys.keySecret'));
        $key = CryptHelper::to3DES($this->paymentRequest->order, $key);
        $res = CryptHelper::toHmac256($merchantParameters, $key);
        return strtr(base64_encode($res), '+/', '-_');
    }
}
