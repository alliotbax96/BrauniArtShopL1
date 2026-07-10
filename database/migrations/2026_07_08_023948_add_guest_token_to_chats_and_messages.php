<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Добавляем поле в таблицу чатов
        if (!Schema::hasColumn('chats', 'guest_token')) {
            Schema::table('chats', function (Blueprint $table) {
                $table->string('guest_token')->nullable()->after('client_user_id');
                $table->index('guest_token');
            });
        }

        // Добавляем поле в таблицу сообщений
        if (!Schema::hasColumn('messages', 'guest_token')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->string('guest_token')->nullable()->after('user_id');
                $table->index('guest_token');
            });
        }
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropIndex(['guest_token']);
            $table->dropColumn('guest_token');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['guest_token']);
            $table->dropColumn('guest_token');
        });
    }
};
