<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Orumad\LaravelRedsys\Controllers\RedsysNotificationController;
use Orumad\LaravelRedsys\Models\RedsysNotification;
use Orumad\LaravelRedsys\Models\RedsysPaymentRequest;
use Orumad\LaravelRedsys\Tests\Support\FakeRedsysGateway;

it('receives notification', function () {
    // The request...
    $paymentRequest = new RedsysPaymentRequest();
    $paymentRequest->order = '0001';
    $paymentRequest->amount = 1075;
    $paymentRequest->merchantUrl = 'http://www.example.com';
    $paymentRequest->productDescription = 'Example product';
    $paymentRequest->titular = 'Luke Skywalker';
    $paymentRequest->merchantName = 'LUKAS Films, Inc';
    $redsysPayment = $paymentRequest->saveToDataBase();

    // Gets notification response (as Redsys will send it)
    $fakeRedsysGateway = new FakeRedsysGateway($paymentRequest);
    $notificationResponse = $fakeRedsysGateway->notificationResponse();
    // Send response to the controller
    Event::fake();
    $request = Request::create('/redsys-notification', 'POST', $notificationResponse);
    $controller = new RedsysNotificationController;
    $controller($request, new RedsysNotification);
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
            'ds_order' => $paymentRequest->order,
            'ds_amount' => $paymentRequest->amount,
            'redsys_payment_id' => $redsysPayment->id,
        ]
    );
});
