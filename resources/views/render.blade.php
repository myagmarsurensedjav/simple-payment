@php use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithBase64QrImage;use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithRedirectUrl;use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithUrls; @endphp
    <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@lang('Payment') - {{ $pendingPayment->payment->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div class="container mx-auto max-w-xl my-8 px-4 md:px-0">
    <div class="text-center text-2xl text-gray-500 mb-6">
        {{ config('app.name') }}
    </div>

    <div class="mb-4 text-center">
        {{ $pendingPayment->payment->id }}
    </div>


    <div class="text-gray-600 text-center">
        @if($pendingPayment instanceof WithBase64QrImage)
            <p class="mb-4 text-sm">Та банкны апп-р дараахь QR кодыг уншуулан төлөлтийг гүйцэтгэх боломжтой.</p>

            <div class="mb-4 flex justify-center">
                <img class="border w-1/2" src="data:image/png;base64, {{ $pendingPayment->getBase64QrImage() }}"/>
            </div>

            <div class="mb-4">
                <a class="border px-3 py-2 rounded" download="payment-{{ $pendingPayment->payment->id }}.png"
                   href="data:image/png;base64, {{ $pendingPayment->getBase64QrImage() }}">
                    Файл татах
                </a>
            </div>
        @endif

        @if($pendingPayment instanceof WithRedirectUrl)
            <p class="mb-4 text-sm">Та доорхи хаяг руу хандах боломжтой.</p>

            <div class="mb-4">
                <a class="border px-3 py-2 rounded" href="{{ $pendingPayment->getRedirectUrl() }}">
                    Хандах
                </a>
            </div>
        @endif

        @if($pendingPayment instanceof WithUrls)
            <div class="md:hidden">
                <p class="mb-4">Төлбөр төлөх банкны апп-аа сонгон уу.</p>

                <div class="grid grid-cols-3 gap-4">
                    @foreach($pendingPayment->getUrls() as $url)
                        <a class="border px-3 py-3 rounded flex items-center justify-center" href="{{ $url['url'] }}">
                            <img class="w-12 h-12" src="{{ $url['image'] }}" alt="{{ $url['label'] }}">
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
</body>
</html>
