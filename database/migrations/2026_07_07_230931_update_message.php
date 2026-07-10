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
        Schema::table('messages', function (Blueprint $table) {
            // Добавляем поле is_edited если его нет
            if (!Schema::hasColumn('messages', 'is_edited')) {
                $table->boolean('is_edited')->default(false)->after('is_read');
            }

            // Добавляем поле file_size если его нет
            if (!Schema::hasColumn('messages', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('file_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Удаляем поля только если они существуют
            if (Schema::hasColumn('messages', 'is_edited')) {
                $table->dropColumn('is_edited');
            }

            if (Schema::hasColumn('messages', 'file_size')) {
                $table->dropColumn('file_size');
            }
        });
    }
};
