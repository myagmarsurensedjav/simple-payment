<?php

namespace Selmonal\LaravelSimplePayment;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Selmonal\LaravelSimplePayment\Actions\CheckPayment;
use Selmonal\LaravelSimplePayment\Database\Factories\PaymentFactory;

/**
 * @property string $id
 * @property float $amount
 * @property string $gateway_transaction_id
 * @property Model $payable
 * @property string $error_message
 * @property string $status
 * @property Carbon $created_at
 * @property string $description
 *
 * @property array|mixed $qpay
 *
 * @method static PaymentFactory factory($count = null, $state = [])
 * @method static Payment findOrFail(string $paymentId)
 */
class Payment extends Model
{
    use HasFactory;
    use HasUuids;

    protected $factory = PaymentFactory::class;

    protected $attributes = [
        'status' => PaymentStatus::Draft,
    ];

    protected $casts = [
        'status' => PaymentStatus::class
    ];

    protected $guarded = [];

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
        return $this->belongsTo(User::class);
    }

    public function check()
    {
        CheckPayment::run($this);
    }
}
