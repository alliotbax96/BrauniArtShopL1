<?php
// database/migrations/2024_01_01_000000_create_views_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('views', function (Blueprint $table) {
            $table->id();
            $table->morphs('viewable'); // Полиморфная связь (product, category, etc.)
            $table->unsignedBigInteger('user_id')->nullable(); // Для авторизованных пользователей
            $table->string('session_id', 100)->nullable(); // Для гостей
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referer_url', 2048)->nullable();
            $table->json('metadata')->nullable(); // Дополнительные данные
            $table->timestamp('viewed_at');

            $table->index(['user_id', 'viewed_at']);
            $table->index(['session_id', 'viewed_at']);
            $table->index(['viewable_type', 'viewable_id', 'viewed_at']);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('views');
    }
};
