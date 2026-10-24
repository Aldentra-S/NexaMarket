<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'payment_number',
        'status',
        'amount',
        'payment_method',
        'snap_token',
        'snap_redirect_url',
        'gateway_transaction_id',
        'payload',
        'paid_at',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => PaymentStatus::PENDING,
        'payment_method' => 'mock_payment',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'amount' => 'decimal:2',
            'payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get the order related to this payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Format amount into Rupiah currency display.
     */
    public function formattedAmount(): string
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }
}
