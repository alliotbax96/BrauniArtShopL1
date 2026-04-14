<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_seller_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('sellers')->onDelete('cascade');
            $table->string('status')->default('pending'); // Статус для конкретного продавца
            $table->timestamps();

            // Уникальный ключ: один продавец не может быть дважды в одном заказе
            $table->unique(['order_id', 'seller_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_seller_statuses');
    }
};
