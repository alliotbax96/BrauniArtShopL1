<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();

            // Информация о главе
            $table->string('title');
            $table->integer('order')->default(0);
            $table->text('description')->nullable();

            // Для электронных книг
            $table->longText('content')->nullable();

            // Для аудиокниг
            $table->string('audio_file_path')->nullable();
            $table->integer('duration')->nullable()->comment('Продолжительность в секундах');
            $table->bigInteger('file_size')->nullable()->comment('Размер файла в байтах');

            // Статусы
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('is_free_preview')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_chapters');
    }
};
