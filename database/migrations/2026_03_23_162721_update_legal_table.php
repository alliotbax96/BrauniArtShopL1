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
        Schema::table('legal_entity_details', function (Blueprint $table) {
            // Удаляем столбцы entity_id и entity_type
            $table->dropColumn('entity_id');
            $table->dropColumn('entity_type');

            // Добавляем столбец user_id
            $table->unsignedBigInteger('user_id')->after('id');

            // Можно сразу добавить индекс для user_id, если планируется часто использовать его в запросах
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legal_entity_details', function (Blueprint $table) {
            // Восстанавливаем удалённые столбцы
            $table->unsignedBigInteger('entity_id')->after('id');
            $table->string('entity_type', 50)->after('entity_id'); // Тип сущности: 'buyer', 'seller', 'partner'

            // Восстанавливаем индекс для пары entity_id + entity_type
            $table->index(['entity_id', 'entity_type']);

            // Удаляем добавленный столбец user_id
            $table->dropColumn('user_id');
        });
    }
};
