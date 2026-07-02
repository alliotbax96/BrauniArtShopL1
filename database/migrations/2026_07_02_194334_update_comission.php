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
        Schema::table('comission', function (Blueprint $table) {
            // Сначала удаляем существующий ключ
            $table->dropForeign('comission_product_group_id_foreign');

            // Создаём заново с каскадным удалением
            $table->foreign('product_group_id')
                ->references('id')
                ->on('productGroups')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
