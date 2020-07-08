<?php

namespace Orumad\LaravelRedsys\Models;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Orumad\LaravelRedsys\Exceptions\PaymentParameterException;
use Orumad\LaravelRedsys\Helpers\CryptHelper;

class RedsysPaymentRequest implements \JsonSerializable
{
    /**
     * FUC code.
     * @var int
     */
    public $merchantCode;
    /**
     * Terminal number.
     * @var string
     */
    public $terminal;
    /**
     * Transaction type.
     * @var string
     */
    public $transactionType;
    /**
     * Amount.
     * @var float
     */
    public $amount;
    /**
     * Currency code.
     * @var int
     */
    public $currency;
    /**
     * Order number.
     * @var string
     */
    public $order;
    /**
     * URL for notifications.
     * @var string
     */
    public $merchantUrl;
    /**
     * Product description.
     * @var string
     */
    public $productDescription;
    /**
     * Cardholder fullname.
     * @var string
     */
    public $cardholder;
    /**
     * URL for OK transactions.
     * @var string
     */
    public $urlOk;
    /**
     * URL for KO transactions.
     * @var string
     */
    public $urlKo;
    /**
     * Merchant name.
     * @var string
     */
    public $merchantName;
    /**
     * Customer language.
     * @var int
     */
    public $customerLanguage;
    /**
     * Total amount (recurring fee).
     * @var float
     */
    public $sumTotal;
    /**
     * Merchant data.
     * @var string
     */
    public $merchantData;
    /**
     * Time period.
     * @var int
     */
    public $dateFrecuency;
    /**
     * Expiry date.
     * @var null|Carbon
     */
    public $chargeExpiryDate;
    /**
     * Authorization code.
     * @var int
     */
    public $authorisationCode;
    /**
     * Successive recurring operation date.
     * @var null|Carbon
     */
    public $transactionDate;
    /**
     * Reference.
     * @var string
     */
    public $identifier;
    /**
     * Group code.
     * @var string
     */
    public $group;
    /**
     * Payment without authentication.
     * @var bool
     */
    public $directPayment;
    /**
     * Card number.
     * @var int
     */
    public $pan;
    /**
     * Card Expiration date.
     * @var int
     */
    public $expiryDate;
    /**
     * CVV2.
     * @var int
     */
    public $cvv2;

    private bool $_allowSubmit = false;

    public static function forTokenization(): self
    {
        $request = new self;

        $request->identifier = 'REQUIRED';

        return $request;
    }

    public static function withToken(string $token): self
    {
        $request = new self;

        $request->_allowSubmit = true;

        $request->identifier = $token;
        $request->directPayment = true;

        return $request;
    }

    public static function makeRefund(RedsysPayment $redsysPayment): self
    {
        $request = new self;

        $request->_allowSubmit = true;

        $request->transactionType = '3';
        $request->order = $redsysPayment->ds_merchant_order;
        $request->amount = $redsysPayment->ds_merchant_amount;
        $notification = $redsysPayment->redsysNotifications()->where('ds_response', 0)->first();
        $request->authorisationCode = $notification->ds_authorisation_code;

        return $request;
    }

    public function __construct()
    {
        $this->merchantCode = config('redsys.dsMerchantCode');
        $this->currency = intval(config('redsys.dsCurrencyCode'));
        $this->transactionType = config('redsys.dsTransactionType');
        $this->terminal = config('redsys.dsTerminalNumber');
        $this->customerLanguage = intval(config('redsys.dsCustomerLanguage'));
        $this->merchantName = config('redsys.dsMerchantName');
        $this->merchantUrl = route('redsys-notification');
        $this->okUrl = config('redsys.okRoute') ? route(config('redsys.okRoute')) : null;
        $this->koUrl = config('redsys.koRoute') ? route(config('redsys.koRoute')) : null;
    }

    public function jsonSerialize(): array
    {
        $parameters = collect(
            [
                'Ds_Merchant_MerchantCode' => $this->merchantCode,
                'Ds_Merchant_Terminal' => $this->terminal,
                'Ds_Merchant_TransactionType' => $this->transactionType,
                'Ds_Merchant_Amount' => $this->numberToString($this->amount),
                'Ds_Merchant_Currency' => $this->currency,
                'Ds_Merchant_Order' => $this->order,
                'Ds_Merchant_MerchantURL' => $this->merchantUrl,
                'Ds_Merchant_ProductDescription' => $this->productDescription,
                'Ds_Merchant_Titular' => $this->cardholder,
                'Ds_Merchant_UrlOK' => $this->urlOk,
                'Ds_Merchant_UrlKO' => $this->urlKo,
                'Ds_Merchant_MerchantName' => $this->merchantName,
                'Ds_Merchant_ConsumerLanguage' => $this->customerLanguage,
                'Ds_Merchant_SumTotal' => $this->numberToString($this->sumTotal),
                'Ds_Merchant_MerchantData' => $this->merchantData,
                'Ds_Merchant_DateFrecuency' => $this->dateFrecuency,
                'Ds_Merchant_ChargeExpiryDate' => optional($this->chargeExpiryDate)->format('Y-m-d'),
                'Ds_Merchant_AuthorisationCode' => $this->authorisationCode,
                'Ds_Merchant_TransactionDate' => optional($this->transactionDate)->format('Y-m-d'),
                'Ds_Merchant_Identifier' => $this->identifier,
                'Ds_Merchant_Group' => $this->group,
                'Ds_Merchant_DirectPayment' => $this->directPayment,
                'Ds_Merchant_Pan' => $this->pan,
                'Ds_Merchant_ExpiryDate' => $this->expiryDate,
                'Ds_Merchant_CVV2' => $this->cvv2,
            ]
        );

        return $parameters->filter(function ($value) {
            return $value && ! empty($value);
        })->toArray();
    }

    private function validateMerchantParameters()
    {
        if (preg_match('/^\d{9,}$/', $this->merchantCode) !== 1) {
            throw PaymentParameterException::invalidMerchantCode();
        }

        if (preg_match('/^\d{1,3}$/', $this->terminal) !== 1) {
            throw PaymentParameterException::invalidTerminal();
        }

        if (preg_match('/^\d{4,}([A-Za-z0-9]{1,8})?$/', $this->order) !== 1) {
            throw PaymentParameterException::invalidOrderFormat();
        }

        // if (preg_match('/^\d{1,12}$/', $this->amount) !== 1) {
        //     throw PaymentParameterException::invalidAmount();
        // }

        if (preg_match('/^\d{3,4}$/', $this->currency) !== 1) {
            throw PaymentParameterException::invalidCurrency();
        }

        if (preg_match('/^[0-9OPQRS]{1}$/', $this->transactionType) !== 1) {
            throw PaymentParameterException::invalidTransactionType();
        }
    }

    public function getMerchantParameters(): string
    {
        $this->validateMerchantParameters();

        return base64_encode(json_encode($this));
    }

    public function getMerchantSignature(): string
    {
        $key = base64_decode(config('redsys.keySecret'));
        $ent = $this->getMerchantParameters();
        $key = CryptHelper::to3DES($this->order, $key);

        return base64_encode(CryptHelper::toHmac256($ent, $key));
    }

    public function saveToDatabase(): RedsysPayment
    {
        $this->validateMerchantParameters();

        $redsysPayment = new RedsysPayment();

        $redsysPayment->ds_merchant_transaction_type = $this->transactionType;
        $redsysPayment->ds_merchant_amount = $this->amount;
        $redsysPayment->ds_merchant_currency = $this->currency;
        $redsysPayment->ds_merchant_order = $this->order;
        $redsysPayment->ds_merchant_product_description = $this->productDescription;
        $redsysPayment->ds_merchant_cardholder = $this->cardholder;
        $redsysPayment->ds_merchant_customer_language = $this->customerLanguage;
        $redsysPayment->ds_merchant_sum_total = $this->sumTotal;
        $redsysPayment->ds_merchant_merchantdata = $this->merchantData;
        $redsysPayment->ds_merchant_date_frecuency = $this->dateFrecuency;
        $redsysPayment->ds_merchant_charge_expiry_date = $this->chargeExpiryDate;
        $redsysPayment->ds_merchant_authorisation_code = $this->authorisationCode;
        $redsysPayment->ds_merchant_transaction_date = $this->transactionDate;
        $redsysPayment->ds_merchant_identifier = $this->identifier;
        $redsysPayment->ds_merchant_group = $this->group;
        $redsysPayment->ds_merchant_direct_payment = $this->directPayment;
        $redsysPayment->ds_merchant_pan = $this->pan;
        $redsysPayment->ds_merchant_expiry_date = $this->expiryDate;
        $redsysPayment->ds_merchant_ccv2 = $this->cvv2;

        $redsysPayment->save();

        return $redsysPayment;
    }

    public function redsysUrl(): string
    {
        return config('redsys.url.'.(app()->isProduction() ? 'production' : 'testing'));
    }

    public function htmlForm($buttonTitle = 'Submit'): string
    {
        return
            '<form name="redsys_form" action="'.$this->redsysUrl().'" method="POST">'.
                '<input type="hidden" name="Ds_SignatureVersion" value="'.config('redsys.dsSignatureVersion').'" />'.
                '<input type="hidden" name="Ds_MerchantParameters" value="'.$this->getMerchantParameters().'" />'.
                '<input type="hidden" name="Ds_Signature" value="'.$this->getMerchantSignature().'" />'.
                '<input type="submit" class="redsys-submit-button" value="'.$buttonTitle.'" />'.
            '</form>';
    }

    public function jsonForm(): array
    {
        return [
            'url' => $this->redsysUrl(),
            'Ds_SignatureVersion' => config('redsys.dsSignatureVersion'),
            'Ds_MerchantParameters' => $this->getMerchantParameters(),
            'Ds_Signature' => $this->getMerchantSignature(),
        ];
    }

    public function submit(): bool
    {
        if (! $this->_allowSubmit) {
            throw new Exception('Submit method only can be used with direct payments (token)!');
        }

        $response = Http::asForm()->post($this->redsysUrl(), [
            'Ds_SignatureVersion' => config('redsys.dsSignatureVersion'),
            'Ds_MerchantParameters' => $this->getMerchantParameters(),
            'Ds_Signature' => $this->getMerchantSignature(),
        ]);
        dd($response->body());
        return $response->successful();
    }

    private function numberToString($number)
    {
        return number_format(round($number, 2) * 100, 0, '', '');
    }
}
