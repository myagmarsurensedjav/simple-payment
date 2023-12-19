<?php

use Carbon\Carbon;
use MyagmarsurenSedjav\SimplePayment\Actions\CreatePayment;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithDriverData;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithExpiresAt;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithTransactionFee;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithTransactionId;
use MyagmarsurenSedjav\SimplePayment\Drivers\AbstractDriver;
use MyagmarsurenSedjav\SimplePayment\Enums\PaymentStatus;
use MyagmarsurenSedjav\SimplePayment\Exceptions\NothingToPay;
use MyagmarsurenSedjav\SimplePayment\Payment;
use MyagmarsurenSedjav\SimplePayment\PendingPayment;
use MyagmarsurenSedjav\SimplePayment\Tests\Support\TestCanBePaidPartially;
use MyagmarsurenSedjav\SimplePayment\Tests\Support\TestPayable;
use MyagmarsurenSedjav\SimplePayment\Tests\Support\TestPayment;

use function Pest\Laravel\assertDatabaseHas;

it('should throw an exception if the payment amount for the given payable is zero', function () {
    $driver = mockWithPest(AbstractDriver::class)->expect();
    $payable = TestPayable::create(['amount' => 0]);
    expect($payable->getPaymentAmount())->toBe(0.0);

    app(CreatePayment::class)($driver, $payable);
})->throws(NothingToPay::class);

it('should throw an exception if the payment amount for the given payable is negative', function () {
    $driver = mockWithPest(AbstractDriver::class)->expect();
    $payable = TestPayable::create(['amount' => -100]);
    expect($payable->getPaymentAmount())->toBe(-100.0);

    app(CreatePayment::class)($driver, $payable);
})->throws(NothingToPay::class);

it('creates a pending payment', function () {
    $pendingPaymentMock = mockWithPest(PendingPayment::class)->expect();

    $driver = mockWithPest(AbstractDriver::class)->expect(
        name: fn () => 'driver-mock',
        register: fn () => $pendingPaymentMock
    );

    $pendingPayment = app(CreatePayment::class)($driver, $payable = TestPayable::create());

    expect($pendingPayment)
        ->toBeInstanceOf(PendingPayment::class)
        ->toBe($pendingPaymentMock);

    assertDatabaseHas(Payment::class, [
        'user_id' => $payable->getUserId(),
        'amount' => $payable->getPaymentAmount(),
        'description' => $payable->getPaymentDescription(),
        'payable_type' => $payable->getMorphClass(),
        'payable_id' => $payable->getKey(),
        'driver' => 'driver-mock',
        'status' => PaymentStatus::Pending->value,
    ]);
});

test('if the driver result has a transaction ID, it should be stored in the payment', function () {
    $driver = mockWithPest(AbstractDriver::class)->expect(
        name: fn () => 'driver-mock',
        register: fn ($payment) => new class($payment) extends PendingPayment implements WithTransactionId
        {
            public function getTransactionId(): string
            {
                return 'transaction-id';
            }
        }
    );

    app(CreatePayment::class)($driver, TestPayable::create());

    assertDatabaseHas(Payment::class, [
        'transaction_id' => 'transaction-id',
    ]);
});

test('if the driver result has a transaction Fee, it should be stored in the payment', function () {
    $driver = mockWithPest(AbstractDriver::class)->expect(
        name: fn () => 'driver-mock',
        register: fn ($payment) => new class($payment) extends PendingPayment implements WithTransactionFee
        {
            public function getTransactionFee(): float
            {
                return 10.0;
            }
        }
    );

    app(CreatePayment::class)($driver, TestPayable::create());

    assertDatabaseHas(Payment::class, [
        'transaction_fee' => 10.0,
    ]);
});

test('if the driver result has a custom data, it should be stored in the payment', function () {
    $driver = mockWithPest(AbstractDriver::class)->expect(
        name: fn () => 'driver-mock',
        register: fn ($payment) => new class($payment) extends PendingPayment implements WithDriverData
        {
            public function getDriverData(): array
            {
                return ['foo' => 'bar'];
            }
        }
    );

    app(CreatePayment::class)($driver, TestPayable::create());

    assertDatabaseHas(Payment::class, [
        'driver_data' => json_encode(['foo' => 'bar']),
    ]);
});

test('if the driver result has a expire date, it should be stored in the payment', function () {
    $driver = mockWithPest(AbstractDriver::class)->expect(
        name: fn () => 'driver-mock',
        register: fn ($payment) => new class($payment) extends PendingPayment implements WithExpiresAt
        {
            public function getExpiresAt(): Carbon
            {
                return Carbon::now()->addDays(1);
            }
        }
    );

    app(CreatePayment::class)($driver, TestPayable::create());

    assertDatabaseHas(Payment::class, [
        'expires_at' => Carbon::now()->addDays(1)->millis(0),
    ]);
});

test('it should override the payment amount if the amount option is provided', function () {
    $driver = mockWithPest(AbstractDriver::class)->expect(
        name: fn () => 'driver-mock',
        register: fn ($payment) => new class($payment) extends PendingPayment
        {
        }
    );

    $payable = TestCanBePaidPartially::create();

    app(CreatePayment::class)($driver, $payable, ['amount' => 50]);

    assertDatabaseHas(Payment::class, ['amount' => 50]);
});

test('it sets additional attributes if the extended model has', function () {
    $driver = mockWithPest(AbstractDriver::class)->expect(
        name: fn () => 'driver-mock'
    );

    $payable = new class extends TestPayable
    {
    };

    TestPayment::use()::creating(function (Payment $payment) {
        expect($payment)->foo->toBe('bar');
        expect($payment)->baz->toBe(null);
        throw new \InvalidArgumentException('expected');
    });

    app(CreatePayment::class)($driver, $payable, [
        'foo' => 'bar',
        'baz' => 'bol',
    ]);
})->throws(\InvalidArgumentException::class);

it('should throw an exception when the provided amount option is greater than amount of the payable', function () {
    $driver = mockWithPest(AbstractDriver::class)->expect();

    app(CreatePayment::class)($driver, TestCanBePaidPartially::create(['amount' => 50]), ['amount' => 100]);
})->throws(InvalidArgumentException::class, 'Payment amount cannot be greater than payable amount.');

it('should throw an exception when the provided amount option is less than zero', function () {
    $driver = mockWithPest(AbstractDriver::class)->expect();

    app(CreatePayment::class)($driver, TestCanBePaidPartially::create(['amount' => 50]), ['amount' => -100]);
})->throws(InvalidArgumentException::class, 'Payment amount cannot be zero.');

it('should throw an exception when the provided amount option is zero', function () {
    $driver = mockWithPest(AbstractDriver::class)->expect();
    app(CreatePayment::class)($driver, TestCanBePaidPartially::create(['amount' => 50]), ['amount' => 0]);
})->throws(InvalidArgumentException::class, 'Payment amount cannot be zero.');

it('should throw an exception when the payable does not support partial payments', function () {
    $driver = mockWithPest(AbstractDriver::class)->expect();
    app(CreatePayment::class)($driver, TestPayable::create(['amount' => 50]), ['amount' => 25]);
})->throws(InvalidArgumentException::class, 'Payment amount cannot be specified.');
