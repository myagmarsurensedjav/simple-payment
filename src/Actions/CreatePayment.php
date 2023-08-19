<?php

namespace MyagmarsurenSedjav\SimplePayment\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use MyagmarsurenSedjav\SimplePayment\Contracts\CanBePaidPartially;
use MyagmarsurenSedjav\SimplePayment\Contracts\Payable;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithExpiresAt;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithDriverData;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithTransactionFee;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithTransactionId;
use MyagmarsurenSedjav\SimplePayment\Exceptions\NothingToPay;
use MyagmarsurenSedjav\SimplePayment\Facades\SimplePayment;
use MyagmarsurenSedjav\SimplePayment\Drivers\AbstractDriver;
use MyagmarsurenSedjav\SimplePayment\PendingPayment;

class CreatePayment
{
    public function __invoke(AbstractDriver $driver, Payable $payable, array $options = []): PendingPayment
    {
        if ($payable->getPaymentAmount() <= 0) {
            throw new NothingToPay(__('Payment amount cannot be zero.'));
        }

        $this->guardAgainstInvalidAmountOption($options, $payable);

        return DB::transaction(fn () => $this->process($driver, $payable, $options));
    }

    private function process(AbstractDriver $driver, Payable $payable, array $options = []): PendingPayment
    {
        // Урьдчилаад хүлээгдэж байгаа төлбөрийг өгөгдлийн санд үүсгээд өгнө.
        $payment = SimplePayment::paymentModel()::create([
            'user_id' => $payable->getUserId(),
            'amount' => Arr::get($options, 'amount', $payable->getPaymentAmount()),
            'description' => $payable->getPaymentDescription(),
            'payable_type' => $payable->getMorphClass(),
            'payable_id' => $payable->getKey(),
            'driver' => $driver->name(),
            'options' => $options,
        ]);

        // Төлбөрийг тухайн төлбөрийн гарцад бүртгэж өгнө.
        $pendingPayment = $driver->register($payment, $options);

        $attributesShouldBeUpdated = [];

        // Хэрэв тухайн төлбөрийн хэлбэр нь гүйлгээг шалгахад өөрийн гүйлгээний
        // дугаарыг ашиглахыг шаарддаг бол хадгалж авах хэрэгтэй болно.
        if ($pendingPayment instanceof WithTransactionId) {
            $attributesShouldBeUpdated['transaction_id'] = $pendingPayment->getTransactionId();
        }

        // Тухайн төлбөрийн гарц гүйлгээг хийхэд шимтгэл авдаг бол хадгалж авна.
        if ($pendingPayment instanceof WithTransactionFee) {
            $attributesShouldBeUpdated['transaction_fee'] = $pendingPayment->getTransactionFee();
        }

        // Тухайн төлбөрийн гарц нэмэлт өгөгдөлтэй бол хадгалж авна.
        if ($pendingPayment instanceof WithDriverData) {
            $attributesShouldBeUpdated['driver_data'] = $pendingPayment->getDriverData();
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

        if (! $payable instanceof CanBePaidPartially) {
            throw new InvalidArgumentException(__('Payment amount cannot be specified.'));
        }

        if (! $payable->canBePaidPartially()) {
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
