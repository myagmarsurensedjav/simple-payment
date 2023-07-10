<?php

namespace MyagmarsurenSedjav\SimplePayment\Http\Controllers;

use Illuminate\Routing\Controller;
use MyagmarsurenSedjav\SimplePayment\Facades\SimplePayment;

class CallbackController extends Controller
{
    public function __construct()
    {
        if ($middleware = config('simple-payment.notification_middleware')) {
            $this->middleware($middleware, ['only' => 'handleNotification']);
        }

        if ($middleware = config('simple-payment.return_middleware')) {
            $this->middleware($middleware, ['only' => 'handleReturn']);
        }
    }

    public function handleNotification(string $paymentId): array
    {
        SimplePayment::paymentModel()::findOrFail($paymentId)->verify();

        return ['status' => 'ok'];
    }

    public function handleReturn(string $paymentId): mixed
    {
        $checkedPayment = SimplePayment::paymentModel()::findOrFail($paymentId)->verify();

        $response = SimplePayment::handleBrowserReturn($checkedPayment);

        return $response ?: view('simple-payment::return', [
            'payment' => $checkedPayment->payment,
            'status' => $checkedPayment->status(),
            'message' => $checkedPayment->errorMessage(),
        ]);
    }
}
