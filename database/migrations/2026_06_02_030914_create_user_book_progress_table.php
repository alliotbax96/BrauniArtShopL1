<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_book_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();

            // Прогресс чтения
            $table->integer('completed_chapters')->default(0);
            $table->integer('total_chapters')->default(0);
            $table->float('progress_percentage')->default(0);

            // Время чтения
            $table->integer('total_reading_time')->default(0)->comment('Общее время чтения в секундах');
            $table->timestamp('last_read_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_book_progress');
    }
};
