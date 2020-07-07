<?php

namespace Orumad\LaravelRedsys\Helpers;

use Orumad\LaravelRedsys\Helpers\CryptHelper;
use Orumad\LaravelRedsys\Models\RedsysPaymentRequest;

class FakeRedsysGateway
{
    private RedsysPaymentRequest $paymentRequest;
    private string $responseCode;

    public function __construct(
        RedsysPaymentRequest $paymentRequest,
        string $responseCode = '0000'
    ) {
        $this->paymentRequest = $paymentRequest;
        $this->responseCode = $responseCode;
    }

    public function notificationResponse(): array
    {
        $merchantParameters = $this->generateMerchantParameters();
        $signature = $this->generateSignature($merchantParameters);

        return [
            'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
            'Ds_MerchantParameters' => $merchantParameters,
            'Ds_Signature' => $signature,
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
            'Ds_Response' => $this->responseCode,
            // Fake Authorization code
            'Ds_AuthorisationCode' => intval($this->responseCode) < 100 ? '' : mt_rand(100000, 999999),
            'Ds_MerchantData' => $this->paymentRequest->merchantData,
            'Ds_SecurePayment' => 0,
            'Ds_TransactionType' => $this->paymentRequest->transactionType,
            'Ds_Card_Brand' => 1,
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
