<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payment_id',
        'payment_url',
        'payment_data',
        'return_url',
        'webhook_url',
        'child_data',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_data' => 'array',
        'child_data' => 'array',
        'paid_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELED = 'canceled';

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function markAsSuccess(): void
    {
        $this->update([
            'status' => self::STATUS_SUCCESS,
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => self::STATUS_FAILED]);
    }

    public function markAsCanceled(): void
    {
        $this->update(['status' => self::STATUS_CANCELED]);
    }
}
