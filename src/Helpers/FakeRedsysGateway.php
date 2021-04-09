<?php

namespace Orumad\LaravelRedsys\Helpers;

use Illuminate\Support\Str;
use Orumad\LaravelRedsys\Models\RedsysPaymentRequest;
use Faker;

class FakeRedsysGateway
{
    private RedsysPaymentRequest $paymentRequest;
    private string $responseCode;

    private $faker;

    public function __construct(
        RedsysPaymentRequest $paymentRequest,
        string $responseCode = '0000'
    ) {
        $this->paymentRequest = $paymentRequest;
        $this->responseCode = $responseCode;
        $this->faker = Faker\Factory::create();
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
            // Secure payment only when carlholder interaction
            'Ds_SecurePayment' => $this->paymentRequest->excepSCA === 'MIT' ? '0' : '1',
            'Ds_Amount' => round($this->paymentRequest->amount, 2) * 100,
            'Ds_Currency' => $this->paymentRequest->currency,
            'Ds_Order' => $this->paymentRequest->order,
            'Ds_MerchantCode' => $this->paymentRequest->merchantCode,
            'Ds_Terminal' => $this->paymentRequest->terminal,
            'Ds_Response' => $this->responseCode,
            'Ds_TransactionType' => $this->paymentRequest->transactionType,
            'Ds_MerchantData' => $this->paymentRequest->merchantData,
            'Ds_AuthorisationCode' => $this->fakeAuthCode(),
            // Fake Card number
            'Ds_Card_Number' => $this->fakeCardNumber(),
            'Ds_ExpiryDate' => intval($this->responseCode) < 100 ? '' : '3412',
            'Ds_Merchant_Identifier' => $this->fakeMerchantIdentifier(),
            'Ds_ConsumerLanguage' => '1',
            'Ds_Card_Country' => '724',
            'Ds_Card_Brand' => '1',
            'Ds_Merchant_Cof_Txnid' => $this->fakeCofTxnid(),
            'Ds_ProcessedPayMethod' => '1'
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

    /**
     * Fake Authorization code
     */
    private function fakeAuthCode()
    {
        return intval($this->responseCode) < 100
            ? ''
            : $this->faker->numberBetween(100000, 300000);
    }

    /**
     * Fake Merchant Identifier (token)
     */
    private function fakeMerchantIdentifier()
    {
        return $this->paymentRequest->identifier === 'REQUIRED' || $this->paymentRequest->excepSCA === 'MIT'
            ? $this->faker->regexify('[a-f0-9]{40}')
            : '';
    }

    /**
     * Fake Card Number
     */
    private function fakeCardNumber()
    {
        return intval($this->responseCode) < 100
            ? $this->faker->numerify('4548##******####')
            : '';
    }

    /**
     * Fake COF TID (token)
     */
    private function fakeCofTxnid()
    {
        return $this->paymentRequest->identifier === 'REQUIRED'
            ? $this->faker->numerify(date('y').'###########')
            : '';
    }
}
