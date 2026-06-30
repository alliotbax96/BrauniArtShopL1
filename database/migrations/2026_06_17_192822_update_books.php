<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // 1. Создаем колонку (тип должен совпадать с типом ID в таблице sellers, обычно это unsignedBigInteger)
            $table->unsignedBigInteger('book_seller_id')->nullable();

            // 2. Сразу вешаем внешний ключ
            $table->foreign('book_seller_id')
                ->references('id')
                ->on('sellers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Сначала удаляем ключ
            $table->dropForeign(['book_seller_id']);
            // Потом удаляем колонку
            $table->dropColumn('book_seller_id');
        });
    }
};
