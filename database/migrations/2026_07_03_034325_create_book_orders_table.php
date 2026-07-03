<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('restrict'); // нельзя удалить книгу, если есть заказы
            $table->foreignId('seller_id')->nullable()->constrained('sellers')->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method', 32);
            $table->enum('status', ['pending', 'paid', 'cancelled', 'refunded'])->default('pending');
            $table->string('payment_id', 128)->nullable(); // ID платежа в банке
            $table->json('payment_meta')->nullable(); // доп. данные от платёжной системы
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'book_id'], 'idx_book_orders_user_book'); // проверка «уже куплено»
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_orders');
    }
};
