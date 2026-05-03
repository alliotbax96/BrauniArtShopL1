<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Полиморфное отношение: тип и ID сущности
            $table->unsignedBigInteger('reviewable_id');
            $table->string('reviewable_type');

            $table->unsignedTinyInteger('estimation')->comment('Оценка от 1 до 5');
            $table->text('comment')->nullable();
            $table->timestamps();

            // Уникальный индекс: пользователь может оставить только один отзыв на сущность
            $table->unique(['user_id', 'reviewable_id', 'reviewable_type']);

            // Индексы для полиморфного отношения
            $table->index(['reviewable_id', 'reviewable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
