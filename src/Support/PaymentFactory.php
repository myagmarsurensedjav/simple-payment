<?php

namespace MyagmarsurenSedjav\SimplePayment\Support;

use Illuminate\Database\Eloquent\Factories\Factory;
use MyagmarsurenSedjav\SimplePayment\Contracts\Payable;
use MyagmarsurenSedjav\SimplePayment\Payment;

/**
 * @method Payment create($attributes = [], ?Payment $parent = null)
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'driver' => 'fake',
            'amount' => rand(100, 1000) * 1000,
            'payable_type' => Payable::class,
            'payable_id' => 'fake-payable-id',
            'description' => $this->faker->sentence,
        ];
    }
}
