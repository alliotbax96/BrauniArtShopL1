<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            // Основные поля книги
            $table->enum('type', ['ebook', 'audiobook'])->default('ebook');
            $table->enum('status', ['draft', 'complete'])->default('draft');
            $table->enum('moderation_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_active')->default(true);

            // Мета-данные книги
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->year('publication_year')->nullable();
            $table->string('isbn')->nullable()->unique();
            $table->string('language')->default('ru');
            $table->integer('pages_count')->nullable();
            $table->text('annotation')->nullable();

            // Для аудиокниг
            $table->string('narrator')->nullable();
            $table->integer('total_duration')->nullable()->comment('Общая продолжительность в секундах');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
