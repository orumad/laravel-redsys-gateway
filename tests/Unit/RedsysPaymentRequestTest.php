<?php

namespace Orumad\LaravelRedsys\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orumad\LaravelRedsys\Exceptions\PaymentParameterException;
use Orumad\LaravelRedsys\Models\RedsysPaymentRequest;
use Orumad\LaravelRedsys\Tests\TestCase;

class RedsysPaymentRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_validate_merchant_parameters()
    {
        $this->expectException(PaymentParameterException::class);

        $this->expectExceptionMessage('The order number format is not valid.');

        $paymentRequest = new RedsysPaymentRequest();
        $paymentRequest->order = 1;
        $paymentRequest->getMerchantParameters();
    }

    /** @test */
    public function it_can_save_payment_request_to_db()
    {
        $paymentRequest = new RedsysPaymentRequest();
        $paymentRequest->order = '0001';
        $paymentRequest->amount = 1;

        $redsysPayment = $paymentRequest->saveToDatabase();

        $this->assertDatabaseHas(
            'redsys_payments',
            [
                'id' => $redsysPayment->id,
                'ds_merchant_order' => $paymentRequest->order,
                'ds_merchant_amount' => $paymentRequest->amount
            ]
        );
    }
}
