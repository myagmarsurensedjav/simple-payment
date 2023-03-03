<?php

namespace Selmonal\SimplePayment\Support;

use Illuminate\Database\Eloquent\Factories\Factory;
use Selmonal\SimplePayment\Contracts\Payable;
use Selmonal\SimplePayment\Payment;

/**
 * @method Payment create($attributes = [], ?Payment $parent = null)
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'gateway' => 'fake',
            'amount' => rand(100, 1000) * 1000,
            'payable_type' => Payable::class,
            'payable_id' => 'fake-payable-id',
            'description' => $this->faker->sentence,
        ];
    }
}
