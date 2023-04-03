<?php

namespace MyagmarsurenSedjav\SimplePayment\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use MyagmarsurenSedjav\SimplePayment\Contracts\PartialPayable;
use MyagmarsurenSedjav\SimplePayment\Contracts\Payable;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithExpiresAt;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithGatewayData;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithTransactionFee;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithTransactionId;
use MyagmarsurenSedjav\SimplePayment\Exceptions\NothingToPay;
use MyagmarsurenSedjav\SimplePayment\Gateways\AbstractGateway;
use MyagmarsurenSedjav\SimplePayment\Payment;
use MyagmarsurenSedjav\SimplePayment\PendingPayment;

class CreatePayment
{
    public function __invoke(AbstractGateway $gateway, Payable $payable, array $options = []): PendingPayment
    {
        if ($payable->getPaymentAmount() <= 0) {
            throw new NothingToPay(__('Payment amount cannot be zero.'));
        }

        $this->guardAgainstInvalidAmountOption($options, $payable);

        return DB::transaction(fn () => $this->process($gateway, $payable, $options));
    }

    private function process(AbstractGateway $gateway, Payable $payable, array $options = []): PendingPayment
    {
        // Урьдчилаад хүлээгдэж байгаа төлбөрийг өгөгдлийн санд үүсгээд өгнө.
        $payment = Payment::create([
            'id' => (string) Str::uuid(),
            'user_id' => $payable->getUserId(),
            'amount' => Arr::get($options, 'amount', $payable->getPaymentAmount()),
            'description' => $payable->getPaymentDescription(),
            'payable_type' => $payable->getMorphClass(),
            'payable_id' => $payable->getKey(),
            'gateway' => $gateway->name(),
        ]);

        // Төлбөрийг тухайн төлбөрийн гарцад бүртгэж өгнө.
        $pendingPayment = $gateway->register($payment, $options);

        $attributesShouldBeUpdated = [];

        // Хэрэв тухайн төлбөрийн хэлбэр нь гүйлгээг шалгахад өөрийн гүйлгээний
        // дугаарыг ашиглахыг шаарддаг бол хадгалж авах хэрэгтэй болно.
        if ($pendingPayment instanceof WithTransactionId) {
            $attributesShouldBeUpdated['gateway_transaction_id'] = $pendingPayment->getTransactionId();
        }

        // Тухайн төлбөрийн гарц гүйлгээг хийхэд шимтгэл авдаг бол хадгалж авна.
        if ($pendingPayment instanceof WithTransactionFee) {
            $attributesShouldBeUpdated['gateway_transaction_fee'] = $pendingPayment->getTransactionFee();
        }

        // Тухайн төлбөрийн гарц нэмэлт өгөгдөлтэй бол хадгалж авна.
        if ($pendingPayment instanceof WithGatewayData) {
            $attributesShouldBeUpdated['gateway_data'] = $pendingPayment->getGatewayData();
        }

        // Тухайн төлбөрийн гарц дуусах хугацаатай бол хадгалж авна.
        if ($pendingPayment instanceof WithExpiresAt) {
            $attributesShouldBeUpdated['expires_at'] = $pendingPayment->getExpiresAt();
        }

        if (count($attributesShouldBeUpdated) > 0) {
            $payment->update($attributesShouldBeUpdated);
        }

        return $pendingPayment;
    }

    private function guardAgainstInvalidAmountOption(array $options, Payable $payable): void
    {
        if (! isset($options['amount'])) {
            return;
        }

        if (! $payable instanceof PartialPayable) {
            throw new InvalidArgumentException(__('Payment amount cannot be specified.'));
        }

        if (! is_numeric($options['amount'])) {
            throw new InvalidArgumentException(__('Payment amount must be numeric.'));
        }

        if ($options['amount'] <= 0) {
            throw new InvalidArgumentException(__('Payment amount cannot be zero.'));
        }

        if ($options['amount'] > $payable->getPaymentAmount()) {
            throw new InvalidArgumentException(__('Payment amount cannot be greater than payable amount.'));
        }
    }
}
