<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            // Тип чата (support_seller, support_buyer)
            $table->string('type')->default('support')->change();

            // Статус чата
            $table->enum('status', ['waiting', 'active', 'closed'])->default('waiting')->after('type');

            // Информация о клиенте
            $table->string('client_name')->nullable()->after('status');
            $table->string('client_email')->nullable()->after('client_name');
            $table->string('client_phone')->nullable()->after('client_email');

            // Информация о местоположении
            $table->string('ip_address')->nullable()->after('client_phone');
            $table->string('country')->nullable()->after('ip_address');
            $table->string('city')->nullable()->after('country');
            $table->string('region')->nullable()->after('city');

            // Информация о странице
            $table->string('current_page')->nullable()->after('region');
            $table->string('user_agent')->nullable()->after('current_page');

            // Информация о пользователе
            $table->unsignedBigInteger('client_user_id')->nullable()->after('user_agent');
            $table->foreign('client_user_id')->references('id')->on('users')->onDelete('set null');

            // Назначенный сотрудник поддержки
            $table->unsignedBigInteger('assigned_admin_id')->nullable()->after('client_user_id');
            $table->foreign('assigned_admin_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['client_user_id']);
            $table->dropForeign(['assigned_admin_id']);

            $table->dropColumn([
                'status',
                'client_name',
                'client_email',
                'client_phone',
                'ip_address',
                'country',
                'city',
                'region',
                'current_page',
                'user_agent',
                'client_user_id',
                'assigned_admin_id'
            ]);
        });
    }
};
