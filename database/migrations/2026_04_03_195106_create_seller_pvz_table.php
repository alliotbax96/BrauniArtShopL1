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
        Schema::create('seller_pvzs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->onDelete('cascade');
            $table->string('pvz');
            $table->string('pvz_name');
            $table->string('citikode')->nullable();
            $table->boolean('last')->default(false);
            $table->timestamps();

            // Исправляем ошибку: в таблице нет поля user_id
            // Предполагаем, что нужно уникальное ограничение на seller_id и pvz
            $table->unique(['seller_id', 'pvz']);

            // Индекс для быстрого поиска последнего ПВЗ по продавцу
            $table->index(['seller_id', 'last']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_pvzs');
    }
};
