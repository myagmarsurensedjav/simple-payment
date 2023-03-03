<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@lang('Payment') - {{ $payment->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div class="container mx-auto max-w-xl my-8 px-4 md:px-0">
    <div class="text-center text-2xl text-gray-500 mb-6">
        {{ config('app.name') }}
    </div>

    <div class="text-gray-600">
        @if ($status === \Selmonal\SimplePayment\Enums\PaymentStatus::Paid)
            <p class="text-green-500 text-2xl text-center mb-4">
                @lang('Амжилттай төлөгдсөн')
            </p>
        @elseif ($status === \Selmonal\SimplePayment\Enums\PaymentStatus::Failed)
            <p class="text-red-500 text-2xl text-center mb-4">
                @lang('Төлбөр төлөлт амжилтгүй')
            </p>

        @elseif ($status === \Selmonal\SimplePayment\Enums\PaymentStatus::Pending)
            <p class="text-gray-500 text-2xl text-center mb-4">
                @lang('Төлбөр хүлээгдэж байна')
            </p>
        @endif

        <p class="text-center">
            {{ $message }}
        </p>
    </div>
</div>
</body>
</html>
