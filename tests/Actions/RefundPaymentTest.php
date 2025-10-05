<?php

namespace MyagmarsurenSedjav\SimplePayment\Tests\Actions;

use MyagmarsurenSedjav\SimplePayment\Actions\RefundPayment;
use MyagmarsurenSedjav\SimplePayment\Enums\PaymentStatus;
use MyagmarsurenSedjav\SimplePayment\Events\PaymentWasRefunded;
use MyagmarsurenSedjav\SimplePayment\Payment;
use MyagmarsurenSedjav\SimplePayment\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class RefundPaymentTest extends TestCase
{
    /** @test */
    public function it_can_refund_a_paid_payment()
    {
        Event::fake();

        $payment = Payment::factory()->create([
            'status' => PaymentStatus::Paid,
            'paid_at' => now(),
        ]);

        $refundAction = new RefundPayment();
        $refundedPayment = $refundAction($payment, 'Customer request');

        $this->assertEquals(PaymentStatus::Refunded, $refundedPayment->status);
        $this->assertNotNull($refundedPayment->refunded_at);
        $this->assertEquals('Customer request', $refundedPayment->refund_reason);

        Event::assertDispatched(PaymentWasRefunded::class);
    }

    /** @test */
    public function it_throws_exception_when_trying_to_refund_non_paid_payment()
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatus::Pending,
        ]);

        $refundAction = new RefundPayment();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only paid payments can be refunded');

        $refundAction($payment);
    }

    /** @test */
    public function it_can_refund_without_reason()
    {
        Event::fake();

        $payment = Payment::factory()->create([
            'status' => PaymentStatus::Paid,
            'paid_at' => now(),
        ]);

        $refundedPayment = $payment->refund();

        $this->assertEquals(PaymentStatus::Refunded, $refundedPayment->status);
        $this->assertNotNull($refundedPayment->refunded_at);
        $this->assertNull($refundedPayment->refund_reason);
    }

    /** @test */
    public function it_can_check_if_payment_is_refunded()
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatus::Refunded,
            'refunded_at' => now(),
        ]);

        $this->assertTrue($payment->isRefunded());
        $this->assertFalse($payment->isPaid());
    }
}
