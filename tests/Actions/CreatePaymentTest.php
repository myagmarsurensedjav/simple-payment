<?php

use Carbon\Carbon;
use MyagmarsurenSedjav\SimplePayment\Actions\CreatePayment;
use MyagmarsurenSedjav\SimplePayment\Contracts\WithExpiresAt;
use MyagmarsurenSedjav\SimplePayment\Contracts\WithGatewayData;
use MyagmarsurenSedjav\SimplePayment\Contracts\WithTransactionFee;
use MyagmarsurenSedjav\SimplePayment\Contracts\WithTransactionId;
use MyagmarsurenSedjav\SimplePayment\Enums\PaymentStatus;
use MyagmarsurenSedjav\SimplePayment\Exceptions\NothingToPay;
use MyagmarsurenSedjav\SimplePayment\Gateways\AbstractGateway;
use MyagmarsurenSedjav\SimplePayment\Payment;
use MyagmarsurenSedjav\SimplePayment\PendingPayment;
use MyagmarsurenSedjav\SimplePayment\Tests\Support\TestPayable;
use function Pest\Laravel\assertDatabaseHas;

it('should throw an exception if the payment amount for the given payable is zero', function () {
    $gateway = mock(AbstractGateway::class)->expect();
    $payable = TestPayable::create(['amount' => 0]);
    expect($payable->getPaymentAmount())->toBe(0.0);

    app(CreatePayment::class)($gateway, $payable);
})->throws(NothingToPay::class);

it('should throw an exception if the payment amount for the given payable is negative', function () {
    $gateway = mock(AbstractGateway::class)->expect();
    $payable = TestPayable::create(['amount' => -100]);
    expect($payable->getPaymentAmount())->toBe(-100.0);

    app(CreatePayment::class)($gateway, $payable);
})->throws(NothingToPay::class);

it('creates a pending payment', function () {
    $pendingPaymentMock = mock(PendingPayment::class)->expect();

    $gateway = mock(AbstractGateway::class)->expect(
        name: fn () => 'gateway-mock',
        register: fn () => $pendingPaymentMock
    );

    $pendingPayment = app(CreatePayment::class)($gateway, $payable = TestPayable::create());

    expect($pendingPayment)
        ->toBeInstanceOf(PendingPayment::class)
        ->toBe($pendingPaymentMock);

    assertDatabaseHas(Payment::class, [
        'user_id' => $payable->getUserId(),
        'amount' => $payable->getPaymentAmount(),
        'description' => $payable->getPaymentDescription(),
        'payable_type' => $payable->getMorphClass(),
        'payable_id' => $payable->getKey(),
        'gateway' => 'gateway-mock',
        'status' => PaymentStatus::Pending->value,
    ]);
});

test('if the gateway result has a transaction ID, it should be stored in the payment', function () {
    $gateway = mock(AbstractGateway::class)->expect(
        name: fn () => 'gateway-mock',
        register: fn ($payment) => new class($payment) extends PendingPayment implements WithTransactionId
        {
            public function getTransactionId(): string
            {
                return 'transaction-id';
            }
        }
    );

    app(CreatePayment::class)($gateway, TestPayable::create());

    assertDatabaseHas(Payment::class, [
        'gateway_transaction_id' => 'transaction-id',
    ]);
});

test('if the gateway result has a transaction Fee, it should be stored in the payment', function () {
    $gateway = mock(AbstractGateway::class)->expect(
        name: fn () => 'gateway-mock',
        register: fn ($payment) => new class($payment) extends PendingPayment implements WithTransactionFee
        {
            public function getTransactionFee(): float
            {
                return 10.0;
            }
        }
    );

    app(CreatePayment::class)($gateway, TestPayable::create());

    assertDatabaseHas(Payment::class, [
        'gateway_transaction_fee' => 10.0,
    ]);
});

test('if the gateway result has a custom data, it should be stored in the payment', function () {
    $gateway = mock(AbstractGateway::class)->expect(
        name: fn () => 'gateway-mock',
        register: fn ($payment) => new class($payment) extends PendingPayment implements WithGatewayData
        {
            public function getGatewayData(): array
            {
                return ['foo' => 'bar'];
            }
        }
    );

    app(CreatePayment::class)($gateway, TestPayable::create());

    assertDatabaseHas(Payment::class, [
        'gateway_data' => json_encode(['foo' => 'bar']),
    ]);
});

test('if the gateway result has a expire date, it should be stored in the payment', function () {
    $gateway = mock(AbstractGateway::class)->expect(
        name: fn () => 'gateway-mock',
        register: fn ($payment) => new class($payment) extends PendingPayment implements WithExpiresAt
        {
            public function getExpiresAt(): Carbon
            {
                return Carbon::now()->addDays(1);
            }
        }
    );

    app(CreatePayment::class)($gateway, TestPayable::create());

    assertDatabaseHas(Payment::class, [
        'expires_at' => Carbon::now()->addDays(1),
    ]);
});
