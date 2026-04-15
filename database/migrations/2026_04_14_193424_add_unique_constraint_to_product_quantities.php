<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToProductQuantities extends Migration
{
    public function up()
    {
        Schema::table('product_quantities', function (Blueprint $table) {
            // Уникальный ключ: один товар — один склад
            $table->unique(['product_id', 'warehouse_id'], 'product_warehouse_unique');
            // Индексы для ускорения запросов
            $table->index('product_id');
            $table->index('warehouse_id');
        });
    }

    public function down()
    {
        Schema::table('product_quantities', function (Blueprint $table) {
            $table->dropIndex('product_warehouse_unique');
            $table->dropIndex('product_id');
            $table->dropIndex('warehouse_id');
        });
    }
}
