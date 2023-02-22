<?php

namespace Selmonal\LaravelSimplePayment\Http\Controllers;

use Illuminate\Routing\Controller;
use Selmonal\LaravelSimplePayment\Actions\CheckPayment;
use Selmonal\LaravelSimplePayment\Payment;

class QpayWebhookController extends Controller
{
    public function handle(string $paymentId): array
    {
        $payment = Payment::findOrFail($paymentId);

        CheckPayment::run($payment);

        return ['status' => 'ok'];
    }
}
