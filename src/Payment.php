<?php

namespace Selmonal\SimplePayment;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Selmonal\SimplePayment\Contracts\Payable;
use Selmonal\SimplePayment\Enums\PaymentStatus;
use Selmonal\SimplePayment\Facades\SimplePayment;
use Selmonal\SimplePayment\Gateways\AbstractGateway;
use Selmonal\SimplePayment\Support\PaymentFactory;

/**
 * @property string $id
 * @property float $amount
 * @property string $gateway_transaction_id
 * @property Payable $payable
 * @property string $error_message
 * @property PaymentStatus $status
 * @property Carbon $created_at
 * @property string $description
 * @property array|mixed $qpay
 * @property int $verifies_count
 * @property string $gateway
 * @property Carbon $paid_at
 * @property Carbon $verified_at
 * @property Carbon $expires_at
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
        'gateway_data' => 'array',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount' => 'float',
    ];

    protected $guarded = [];

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

    public function verify(): CheckedPayment
    {
        return SimplePayment::driver($this->gateway)->verify($this);
    }

    public function check(): CheckedPayment
    {
        return SimplePayment::driver($this->gateway)->check($this);
    }

    public function gateway(): AbstractGateway
    {
        return SimplePayment::driver($this->gateway);
    }
}
