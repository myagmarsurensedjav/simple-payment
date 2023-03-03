<?php

namespace Selmonal\SimplePayment\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Selmonal\SimplePayment\Facades\SimplePayment;
use Selmonal\SimplePayment\Payment;

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
        Payment::findOrFail($paymentId)->verify();

        return ['status' => 'ok'];
    }

    public function handleReturn(string $paymentId): JsonResponse|Response|View
    {
        $checkedPayment = Payment::findOrFail($paymentId)->verify();

        $response = SimplePayment::handleBrowserReturn($checkedPayment);

        return $response ?: view('simple-payment::return', [
            'payment' => $checkedPayment->payment,
            'status' => $checkedPayment->status(),
            'message' => $checkedPayment->errorMessage(),
        ]);
    }
}
