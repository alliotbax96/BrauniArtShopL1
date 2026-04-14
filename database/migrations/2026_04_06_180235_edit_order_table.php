<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Временное поле для хранения текущих статусов
        Schema::table('orders', function (Blueprint $table) {
            $table->string('temp_status')->nullable();
        });

        // Копируем текущие значения статусов во временное поле
        DB::table('orders')->update([
            'temp_status' => DB::raw('status')
        ]);

        // Удаляем старое поле status
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Добавляем новое поле status и связь с orderStatuses
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pending');

            $table->foreign('status')
                ->references('slug')
                ->on('orderStatuses')
                ->onDelete('restrict');
        });

        // Переносим данные из временного поля в новое поле
        DB::table('orders')
            ->whereNotNull('temp_status')
            ->update([
                'status' => DB::raw('temp_status')
            ]);

        // Удаляем временное поле
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('temp_status');
        });
    }

    public function down(): void
    {
        // Создаём временное поле для хранения статусов slug
        Schema::table('orders', function (Blueprint $table) {
            $table->string('temp_slug')->nullable();
        });

        // Копируем данные из status во временное поле
        DB::table('orders')->update([
            'temp_slug' => DB::raw('status')
        ]);

        // Удаляем связь и поле status
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['status']);
            $table->dropColumn('status');
        });

        // Восстанавливаем старое поле status с ENUM
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'cancelled', 'completed'])->default('pending');
        });

        // Переносим данные обратно (если slug соответствует старым статусам)
        DB::table('orders')
            ->whereIn('temp_slug', ['pending', 'paid', 'cancelled', 'completed'])
            ->update([
                'status' => DB::raw('temp_slug')
            ]);

        // Удаляем временное поле
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('temp_slug');
        });
    }
};
