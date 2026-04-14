<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalEntityDetail extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'legal_entity_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'legal_name',
        'inn',
        'kpp',
        'ogrn',
        'bank_name',
        'bik',
        'correspondent_account',
        'account_number',
        'director_name',
        'is_vat_payer',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_vat_payer' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Атрибуты, которые должны быть скрыты при преобразовании модели в массив/JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Связь с пользователем.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Проверить, является ли организация плательщиком НДС.
     *
     * @return bool
     */
    public function isVatPayer(): bool
    {
        if(is_null($this->is_vat_payer)){
            return false;
        }
        return true;
    }

    /**
     * Проверить, является ли запись данными ИП (КПП и директор отсутствуют).
     *
     * @return bool
     */
    public function isIndividualEntrepreneur(): bool
    {
        return is_null($this->kpp) && is_null($this->director_name);
    }

    public function getID() {
        return $this->id;
    }


    public static function isUserLegalEntity(int $userId): bool
    {
        return static::where('user_id', $userId)->exists();
    }

    // Обратная связь с продавцами
    public function sellers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Seller::class,
            'seller_legal_details',
            'legal_entity_detail_id',
            'seller_id'
        )->withTimestamps();
    }
}
