<?php

namespace Orumad\LaravelRedsys\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Orumad\LaravelRedsys\Helpers\CryptHelper;
use Orumad\LaravelRedsys\Services\Redsys\DsMerchantCustomerLanguage;
use Orumad\LaravelRedsys\Services\Redsys\NotificationsResponses;

class RedsysNotification extends Model
{
    protected $table = 'redsys_notifications';

    protected $casts = [
        'ds_date_hour' => 'datetime',
    ];

    private $originalMerchantParametersJson;

    public function redsysPayment()
    {
        return $this->belongsTo(RedsysPayment::class, 'redsys_payment_id');
    }

    public function isValidSignature(string $signature): bool
    {
        $key = base64_decode(config('redsys.keySecret'));
        $key = CryptHelper::to3DES($this->ds_order, $key);
        $res = CryptHelper::toHmac256($this->originalMerchantParametersJson, $key);

        return $signature === strtr(base64_encode($res), '+/', '-_');
    }

    public function getResponseText(): string
    {
        return NotificationsResponses::getResponse($this->ds_response);
    }

    public function setUp(string $merchantParameters)
    {
        $this->originalMerchantParametersJson = $merchantParameters;

        $merchantParameters = json_decode(urldecode(base64_decode(strtr($merchantParameters, '-_', '+/'))), true);

        $this->ds_date_hour = Carbon::createFromFormat('d/m/Y H:i', "{$merchantParameters['Ds_Date']} {$merchantParameters['Ds_Hour']}");
        $this->ds_amount = floatval($merchantParameters['Ds_Amount']) / 100;
        $this->ds_currency = $merchantParameters['Ds_Currency'];
        $this->ds_order = $merchantParameters['Ds_Order'];
        $this->ds_response = $merchantParameters['Ds_Response'];
        $this->ds_merchant_merchantdata = $merchantParameters['Ds_MerchantData'];
        $this->ds_secure_payment = $merchantParameters['Ds_SecurePayment'];
        $this->ds_transaction_type = $merchantParameters['Ds_TransactionType'];
        $this->ds_card_country = Arr::get($merchantParameters, 'Ds_Card_Country');
        $this->ds_authorisation_code = Arr::get($merchantParameters, 'Ds_AuthorisationCode');
        $this->ds_customer_language = Arr::get($merchantParameters, 'Ds_ConsumerLanguage') ?: DsMerchantCustomerLanguage::UNSPECIFIED;
        $this->ds_card_type = Arr::get($merchantParameters, 'Ds_Card_Type') ?: '';
        $this->ds_card_brand = Arr::get($merchantParameters, 'Ds_Card_Brand') ?: '';
        $this->ds_merchant_identifier = Arr::get($merchantParameters, 'Ds_Merchant_Identifier');
    }
}
