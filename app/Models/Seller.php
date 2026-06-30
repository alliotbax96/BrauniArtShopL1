<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LegalEntityDetail;
use App\Models\SelfEmployedRecord;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Seller extends Model
{
    protected $table = 'sellers';

    protected $fillable = [
        'name',
        'phone',
    ];

    // --- Существующие связи (оставлены без изменений) ---

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'seller_user')
            ->withTimestamps();
    }

    /**
     * Связь с реквизитами юрлица.
     * Ограничение limit(1) уже было, оставляем.
     */
    public function legalDetails(): BelongsToMany
    {
        return $this->belongsToMany(
            LegalEntityDetail::class,
            'seller_legal_details',
            'seller_id',
            'legal_entity_detail_id'
        )->withTimestamps()->limit(1);
    }

    public function legalDetail()
    {
        return $this->legalDetails()->first();
    }

    // --- НОВЫЕ связи для самозанятых ---
    // Используем те же имена методов, что и раньше, для консистентности

    public function selfEmployedRecords(): BelongsToMany
    {
        return $this->belongsToMany(
            SelfEmployedRecord::class,
            'seller_self_employed_records',
            'seller_id',
            'self_employed_record_id'
        )->withTimestamps()->limit(1);
    }

    public function selfEmployedRecord()
    {
        return $this->selfEmployedRecords()->first();
    }

    public function SellerPvz()
    {
        return $this->hasOne(SellerPvz::class);
    }

    public function contacts()
    {
        return $this->hasMany(SellerContract::class, 'seller_id', 'id');
    }

    /**
     * ПРОВЕРКА СТАТУСА
     *
     * Логика согласно ТЗ:
     * 1. Если есть связь с юрлицом -> 'company'
     * 2. Если есть связь с самозанятым -> 'self_employed'
     * 3. Если нет НИКАКИХ связей (пустая связка) -> 'no_sales_rights'
     * 4. В остальных случаях -> false
     *
     * @return string|false
     */
    public function getSalesStatus(): string|false
    {
        // Проверяем наличие договора с юрлицом
        // Метод legalDetail() сделает запрос к seller_legal_details
        if ($this->legalDetail()) {
            return 'company';
        }

        // Проверяем наличие договора с самозанятым
        // Метод selfEmployedRecord() сделает запрос к seller_self_employed_records
        if ($this->selfEmployedRecord()) {
            return 'self_employed';
        }

        // Если мы здесь, значит в обеих таблицах-связках нет записей для этого продавца.
        // Это и есть состояние "без права продаж, но с правом публикации".
        return 'no_sales_rights';
    }

    /**
     * Вспомогательный метод для быстрой проверки в контроллерах/вьюхах
     * Возвращает true, если продавец может продавать (есть договор)
     */
    public function hasSalesRights(): bool
    {
        $status = $this->getSalesStatus();
        return in_array($status, ['company', 'self_employed']);
    }

    public function notifyRelatedUsers(
        string $subject,
        string $greeting,
        string $line,
        ?string $actionUrl = null,
        ?string $actionText = null
    ): void {
        $this->users()
            ->where('email', '!=', null)
            ->each(function (User $user) use ($subject, $greeting, $line, $actionUrl, $actionText) {
                $user->notify(new \App\Notifications\SellerNotification(
                    $this,
                    $subject,
                    $greeting,
                    $line,
                    $actionUrl,
                    $actionText,
                ));
            });
    }
}
