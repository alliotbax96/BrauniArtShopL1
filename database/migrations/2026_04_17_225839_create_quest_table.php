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
        Schema::create('quests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image_path'); // путь к изображению в S3
            $table->enum('type', ['quest', 'performance']); // квест/перформанс
            $table->json('features')->nullable(); // особенности (массив)
            $table->tinyInteger('difficulty')->between(1, 3); // сложность
            $table->tinyInteger('fear_level')->between(1, 3); // уровень страха
            $table->integer('duration'); // продолжительность в минутах
            $table->integer('min_age'); // минимальный возраст
            $table->decimal('base_price', 10, 2); // базовая цена
            $table->integer('min_players'); // минимальное количество игроков
            $table->integer('max_players'); // максимальное количество игроков
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest');
    }
};
