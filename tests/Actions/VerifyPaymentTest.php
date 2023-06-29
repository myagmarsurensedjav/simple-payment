<?php

use Illuminate\Support\Facades\Event;
use MyagmarsurenSedjav\SimplePayment\Actions\HandlePayableWhenPaid;
use MyagmarsurenSedjav\SimplePayment\Actions\VerifyPayment;
use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Enums\PaymentStatus;
use MyagmarsurenSedjav\SimplePayment\Events\PaymentWasMade;
use MyagmarsurenSedjav\SimplePayment\Gateways\AbstractGateway;
use MyagmarsurenSedjav\SimplePayment\Payment;
use MyagmarsurenSedjav\SimplePayment\Tests\Support\TestPayable;

beforeEach(function () {
    $this->payable = TestPayable::create();

    $this->payment = Payment::factory()
        ->for($this->payable, 'payable')
        ->create(['verifies_count' => 1]);
});

function verify(AbstractGateway $gateway, Payment &$payment): CheckedPayment
{
    $result = app(VerifyPayment::class)($gateway, $payment);

    $payment->refresh();

    return $result;
}

it('verifies a payment', function () {
    $checkedPaymentMock = mockWithPest(CheckedPayment::class)->expect(
        status: fn () => PaymentStatus::Paid,
        errorMessage: fn () => 'Payment is complete',
        successful: fn () => true,
    );

    $gateway = mockWithPest(AbstractGateway::class)->expect(
        check: fn () => $checkedPaymentMock,
    );

    $checkedPayment = verify($gateway, $this->payment);

    expect($checkedPayment)
        ->toBeInstanceOf(CheckedPayment::class)
        ->toBe($checkedPaymentMock);

    $this->payment->refresh();
    expect($this->payment->status)->toBe(PaymentStatus::Paid)
        ->and($this->payment->error_message)->toBe('Payment is complete')
        ->and($this->payment->verified_at)->not()->toBeNull()
        ->and($this->payment->verifies_count)->toBe(2);
});

it('verifies a paid payment', function () {
    Event::fake();

    $gateway = mockWithPest(AbstractGateway::class)->expect(
        check: fn () => mockWithPest(CheckedPayment::class)->expect(
            status: fn () => PaymentStatus::Paid,
            errorMessage: fn () => 'Payment is complete',
            successful: fn () => true,
        ),
    );

    $this->mock(HandlePayableWhenPaid::class)
        ->shouldReceive('__invoke')
        ->with(Mockery::on(fn ($payment) => $payment->is($this->payment)))
        ->once();

    verify($gateway, $this->payment);

    expect($this->payment)
        ->status->toBe(PaymentStatus::Paid)
        ->paid_at->not()->toBeNull();

    Event::assertDispatched(PaymentWasMade::class);
});

it('verifies a failed payment', function () {
    $gateway = mockWithPest(AbstractGateway::class)->expect(
        check: fn () => mockWithPest(CheckedPayment::class)->expect(
            status: fn () => PaymentStatus::Failed,
            errorMessage: fn () => 'Payment is failed',
            successful: fn () => false,
        ),
    );

    verify($gateway, $this->payment);

    expect($this->payment->status)->toBe(PaymentStatus::Failed)
        ->and($this->payment->paid_at)->toBeNull();
});
