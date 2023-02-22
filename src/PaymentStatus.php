<?php

namespace Selmonal\LaravelSimplePayment;

enum PaymentStatus: string
{
    case Draft = 'draft';
    case Complete = 'complete';
    case Failed = 'failed';
}
