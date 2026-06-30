<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('self_employed_records', function (Blueprint $table) {
            $table->id();
            $table->string('inn', 12)->comment('ИНН самозанятого (12 цифр)');
            $table->unsignedBigInteger('user_id')->comment('ID пользователя в системе');

            // Банковские реквизиты (могут быть null)
            $table->string('bank_account_number')->nullable()->comment('Номер банковского счёта');
            $table->string('bic')->nullable()->comment('БИК банка');
            $table->string('correspondent_account')->nullable()->comment('Кореспондентский счет банка');
            $table->string('bank_name')->nullable()->comment('Наименование банка');
            $table->string('account_holder_name')->nullable()->comment('ФИО владельца счёта');

            // Временные метки
            $table->timestamps();

            // Уникальный индекс по user_id + inn
            $table->unique(['user_id', 'inn'], 'unique_user_inn');

            // Внешний ключ на таблицу users
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('self_employed_records');
    }
};
