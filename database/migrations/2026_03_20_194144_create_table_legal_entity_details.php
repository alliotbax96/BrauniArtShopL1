<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('legal_entity_details', function (Blueprint $table) {
            $table->id();

            // Связь с сущностью (покупатель, компания, партнёр)
            $table->unsignedBigInteger('entity_id');
            $table->string('entity_type', 50); // Тип сущности: 'buyer', 'seller', 'partner'

            // Реквизиты юридического лица
            $table->string('legal_name', 255); // Наименование юрлица
            $table->string('inn', 12)->unique(); // ИНН (10 для юрлиц, 12 для ИП)
            $table->string('kpp', 9)->nullable(); // КПП (может быть NULL для ИП)
            $table->string('ogrn', 15)->unique(); // ОГРН

            // Данные банка и счёта
            $table->string('bank_name', 255); // Название банка
            $table->string('bik', 9); // БИК банка
            $table->string('correspondent_account', 20); // Корреспондентский счёт
            $table->string('account_number', 20); // Номер расчётного счёта

            // Дополнительные данные
            $table->string('director_name', 255)->nullable(); // ФИО генерального директора (может быть NULL для ИП)
            $table->boolean('is_vat_payer')->default(false); // Является ли плательщиком НДС

            // Стандарты Laravel
            $table->timestamps();

            // Индексы для оптимизации запросов
            $table->index(['entity_id', 'entity_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_entity_details');
    }
};
