<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('chapter_id')->constrained('book_chapters')->cascadeOnDelete();

            // Позиция в книге
            $table->integer('position')->default(0)->comment('Позиция в тексте (номер символа)');
            $table->integer('scroll_position')->default(0)->comment('Позиция скролла в пикселях');

            // Для аудиокниг
            $table->integer('audio_position')->nullable()->comment('Позиция в аудио в секундах');

            // Дополнительная информация
            $table->float('playback_speed')->default(1.0);
            $table->text('note')->nullable();

            $table->timestamps();

            // Один пользователь может иметь только одну закладку на книгу
            $table->unique(['user_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_bookmarks');
    }
};
