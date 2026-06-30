<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            // Статус: 'company', 'self_employed', 'no_sales_rights'
            $table->string('legal_status', 20)
                ->default('no_sales_rights')
                ->after('phone');

            // ID связанных реквизитов (может быть ID юрлица или самозанятого)
            // Nullable, так как при статусе 'no_sales_rights' он не нужен
            $table->unsignedBigInteger('legal_reference_id')->nullable()->after('legal_status');

            // Индекс для быстрого поиска по статусу
            $table->index('legal_status');
        });

        // ВАЖНО: Если у тебя уже есть данные в таблице sellers, нужно заполнить эту колонку.
        // Раскомментируй этот блок, если запускаешь миграцию на существующей базе с данными:

        \App\Models\Seller::withoutEvents(function () {
            \App\Models\Seller::chunk(100, function ($sellers) {
                foreach ($sellers as $seller) {
                    if ($seller->legalDetail()) {
                        $seller->legal_status = 'company';
                        $seller->legal_reference_id = $seller->legalDetail()->id;
                    } elseif ($seller->selfEmployedRecord()) { // Нужно сначала добавить связь в модель (см. Шаг 2)
                        $seller->legal_status = 'self_employed';
                        $seller->legal_reference_id = $seller->selfEmployedRecord()->id;
                    } else {
                        $seller->legal_status = 'no_sales_rights';
                    }
                    $seller->save();
                }
            });
        });

    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn(['legal_status', 'legal_reference_id']);
        });
    }
};
