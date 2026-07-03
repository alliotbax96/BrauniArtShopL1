<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookOrder extends Model
{
    protected $table = 'book_orders';

    protected $fillable = [
        'user_id',
        'book_id',
        'seller_id',
        'amount',
        'payment_method',
        'status',
        'payment_id',
        'payment_meta',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
}
