<?php
// database/migrations/2026_07_02_220000_create_budget_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Таблица бюджета проекта (всегда одна запись)
        Schema::create('project_budget', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_budget', 15, 2)->default(0)->comment('Общий бюджет');
            $table->decimal('current_balance', 15, 2)->default(0)->comment('Текущий баланс');
            $table->decimal('manager_balance', 15, 2)->default(0)->comment('Баланс руководителя');
            $table->timestamps();
        });

        // Таблица транзакций
        Schema::create('budget_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['income', 'expense'])->comment('Тип: доход/расход');
            $table->enum('category', ['development', 'testing', 'layout', 'design', 'manager'])->nullable()->comment('Категория расхода');
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->text('notes')->nullable();
            $table->decimal('balance_before', 15, 2)->comment('Баланс до операции');
            $table->decimal('balance_after', 15, 2)->comment('Баланс после операции');
            $table->decimal('manager_balance_before', 15, 2)->nullable();
            $table->decimal('manager_balance_after', 15, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['type', 'category']);
            $table->index('created_at');
        });

        // Создаем начальную запись бюджета
        DB::table('project_budget')->insert([
            'total_budget' => 0,
            'current_balance' => 0,
            'manager_balance' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_transactions');
        Schema::dropIfExists('project_budget');
    }
};
