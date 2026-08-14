<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique(); // ID из дочерней системы
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('RUB');
            $table->string('status')->default('pending'); // pending, processing, success, failed, canceled
            $table->string('payment_id')->nullable(); // ID из Tinkoff
            $table->string('payment_url')->nullable(); // URL для редиректа на Tinkoff
            $table->json('payment_data')->nullable(); // Данные от Tinkoff
            $table->string('return_url')->nullable(); // URL для возврата
            $table->string('webhook_url')->nullable(); // URL для вебхука дочке
            $table->json('child_data')->nullable(); // Данные от дочки
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('transaction_id');
            $table->index('payment_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
