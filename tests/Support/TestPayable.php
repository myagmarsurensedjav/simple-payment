<?php

namespace Selmonal\SimplePayment\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Selmonal\SimplePayment\Contracts\Payable;
use Selmonal\SimplePayment\Payment;

/**
 * @property float $amount
 * @property string $description
 * @property int|string|null $user_id
 * @property string $id
 *
 * @method static TestPayable make()
 */
class TestPayable extends Model implements Payable
{
    protected $attributes = [
        'amount' => 100,
        'description' => 'Test payable',
    ];

    protected $guarded = [];

    public function getPaymentAmount(): float
    {
        return $this->amount;
    }

    public function getPaymentDescription(): string
    {
        return $this->description;
    }

    public function whenPaid(Payment $payment): void
    {
    }

    public function getUserId(): int|string|null
    {
        return $this->user_id ?: null;
    }
}
