<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SelfEmployedRecord extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'self_employed_records';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string, string>
     */
    protected $fillable = [
        'inn',
        'verification_status',
        'user_id',
        'bank_account_number',
        'bic',
        'correspondent_account',
        'bank_name',
        'account_holder_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Валидация данных
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'inn' => 'required|string|regex:/^\d{12}$/|size:12',
            'user_id' => 'required|integer|exists:users,id',
            'bank_account_number' => 'nullable|string|max:20',
            'bic' => 'nullable|string|size:9',
            'bank_name' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
        ];
    }

    /**
     * Связь с моделью User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sellers() :BelongsToMany {
        return $this->belongsToMany(Seller::class, 'seller_self_employed_records', 'self_employed_record_id', 'seller_id');
    }
}
