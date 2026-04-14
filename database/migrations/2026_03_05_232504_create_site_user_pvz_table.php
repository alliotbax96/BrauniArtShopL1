<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('site_user_pvz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('pvz');
            $table->string('pvz_name');
            $table->string('citikode')->nullable();
            $table->boolean('last')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'pvz']);
            $table->index(['user_id', 'last']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_user_pvz');
    }
};
