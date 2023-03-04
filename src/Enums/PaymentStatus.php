<?php

namespace MyagmarsurenSedjav\SimplePayment\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';

    public function label(): string
    {
        return __('simple-payment::payment.status.'.$this->value);
    }
}
