<?php

namespace MyagmarsurenSedjav\SimplePayment;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use MyagmarsurenSedjav\SimplePayment\Contracts\Payable;
use MyagmarsurenSedjav\SimplePayment\Drivers\AbstractDriver;
use MyagmarsurenSedjav\SimplePayment\Enums\PaymentStatus;
use MyagmarsurenSedjav\SimplePayment\Facades\SimplePayment;
use MyagmarsurenSedjav\SimplePayment\Support\PaymentFactory;
use MyagmarsurenSedjav\SimplePayment\Actions\RefundPayment;

/**
 * @property string $id
 * @property float $amount
 * @property string $transaction_id
 * @property ?Payable $payable
 * @property string $error_message
 * @property PaymentStatus $status
 * @property Carbon $created_at
 * @property string $description
 * @property array|mixed $qpay
 * @property int $verifies_count
 * @property string $driver
 * @property Carbon $paid_at
 * @property Carbon $verified_at
 * @property Carbon $expires_at
 * @property Carbon $transaction_fee
 * @property array $driver_data
 * @property string $user_id
 * @property string $payable_type
 * @property string $payable_id
 *
 * @method static PaymentFactory factory($count = null, $state = [])
 * @method static Payment findOrFail(string $paymentId)
 * @method static Payment create(array $array)
 * @method static expired()
 */
class Payment extends Model
{
    use HasFactory;
    use HasUuids;

    protected $attributes = [
        'status' => PaymentStatus::Pending,
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'driver_data' => 'array',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'refunded_at' => 'datetime',
        'amount' => 'float',
    ];

    protected $guarded = [];

    protected array $additional = [];

    protected static function newFactory(): PaymentFactory
    {
        return PaymentFactory::new();
    }

    public function scopeExpired($query)
    {
        return $query->where(function (Builder $builder) {
            $builder
                ->where('status', '!=', PaymentStatus::Paid)
                ->where('expires_at', '<', now());
        });
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function formattedAmount(): Attribute
    {
        return Attribute::get(fn () => number_format($this->amount).' ₮');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('simple-payment.user_model'));
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Paid;
    }

    public function isRefunded(): bool
    {
        return $this->status === PaymentStatus::Refunded;
    }

    public function refund(?string $reason = null): self
    {
        return app(RefundPayment::class)($this, $reason);
    }

    public function verify(): CheckedPayment
    {
        return $this->driver()->verify($this);
    }

    public function check(): CheckedPayment
    {
        return $this->driver()->check($this);
    }

    public function driver(): AbstractDriver
    {
        return SimplePayment::driver($this->driver);
    }

    public function setOptionsAttribute(array $options)
    {
        foreach ($this->additional as $attribute) {
            if (isset($options[$attribute])) {
                $this->{$attribute} = $options[$attribute];
            }
        }
    }
}
