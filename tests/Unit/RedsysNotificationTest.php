<?php

namespace Orumad\LaravelRedsys\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orumad\LaravelRedsys\Helpers\CryptHelper;
use Orumad\LaravelRedsys\Models\RedsysNotification;
use Orumad\LaravelRedsys\Models\RedsysPaymentRequest;
use Orumad\LaravelRedsys\Tests\TestCase;

class RedsysNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    private $order;
    /**
     * @var string
     */
    private $merchantParameters;
    /**
     * @var \Orumad\LaravelRedsys\Models\RedsysPayment
     */
    private $redsysPayment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->order = '0001';

        $this->merchantParameters = base64_encode(json_encode([
            'Ds_Date' => now()->format('d/m/Y'),
            'Ds_Hour' => now()->format('H:i'),
            'Ds_Amount' => 1000,
            'Ds_Currency' => config('redsys.dsCurrencyCode'),
            'Ds_Order' => $this->order,
            'Ds_MerchantCode' => config('redsys.dsMerchantCode'),
            'Ds_Terminal' => config('redsys.dsTerminalNumber'),
            'Ds_Response' => '0000',
            'Ds_MerchantData' => '',
            'Ds_SecurePayment' => 0,
            'Ds_TransactionType' => config('redsys.dsTransactionType'),
            'Ds_Card_Brand' => 1
        ]));

        $paymentRequest = new RedsysPaymentRequest();
        $paymentRequest->order = $this->order;
        $paymentRequest->amount = 1;

        $this->redsysPayment = $paymentRequest->saveToDatabase();
    }

    /** @test */
    public function it_can_validate_the_signature()
    {
        $key = base64_decode(config('redsys.keySecret'));
        $key = CryptHelper::to3DES($this->order, $key);
        $res = CryptHelper::toHmac256($this->merchantParameters, $key);
        $signature = strtr(base64_encode($res), '+/', '-_');

        $redsysNotification = new RedsysNotification();
        $redsysNotification->setUp($this->merchantParameters);

        $this->assertTrue($redsysNotification->signature($signature));
    }

    /** @test */
    public function it_can_attach_a_notification_to_a_payment()
    {
        $redsysNotification = new RedsysNotification();
        $redsysNotification->setUp($this->merchantParameters);

        $this->redsysPayment->redsysNotifications()->save($redsysNotification);

        $this->assertDatabaseHas(
            'redsys_notifications',
            [
                'id' => $redsysNotification->id,
                'ds_order' => $redsysNotification->ds_order,
                'ds_amount' => $redsysNotification->ds_amount,
                'redsys_payment_id' => $this->redsysPayment->id
            ]
        );
    }
}
