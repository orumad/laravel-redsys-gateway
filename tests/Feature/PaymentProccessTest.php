<?php

namespace Orumad\LaravelRedsys\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orumad\LaravelRedsys\Models\RedsysNotification;
use Orumad\LaravelRedsys\Models\RedsysPaymentRequest;
use Orumad\LaravelRedsys\Tests\Support\FakeRedsysGateway;
use Orumad\LaravelRedsys\Tests\TestCase;

class PaymentProccessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function payment_proccess_works()
    {
        $paymentRequest = new RedsysPaymentRequest();
        $paymentRequest->order = '0001';
        $paymentRequest->amount = 1075;
        $paymentRequest->merchantUrl = 'http://www.example.com';
        $paymentRequest->productDescription = 'Example product';
        $paymentRequest->titular = 'Luke Skywalker';
        $paymentRequest->merchantName = 'LUKAS Films, Inc';

        $redsysPayment = $paymentRequest->saveToDataBase();

        $fakeRedsysGateway = new FakeRedsysGateway($paymentRequest);

        $notificationResponse = $fakeRedsysGateway->notificationResponse();

        $redsysNotification = new RedsysNotification();
        $redsysNotification->setUp($notificationResponse['Ds_MerchantParameters']);

        $this->assertTrue($paymentRequest->order === $redsysNotification->ds_order);
        $this->assertTrue($redsysNotification->signature($notificationResponse['Ds_Signature']));

        $redsysPayment->redsysNotifications()->save($redsysNotification);

        $this->assertDatabaseHas(
            'redsys_payments',
            [
                'id' => $redsysPayment->id,
                'ds_merchant_order' => $paymentRequest->order,
                'ds_merchant_amount' => $paymentRequest->amount,
            ]
        );

        $this->assertDatabaseHas(
            'redsys_notifications',
            [
                'id' => $redsysNotification->id,
                'ds_order' => $redsysNotification->ds_order,
                'ds_amount' => $redsysNotification->ds_amount,
                'redsys_payment_id' => $redsysPayment->id,
            ]
        );
    }
}
