<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_self_employed_records', function (Blueprint $table) {
            $table->foreignId('seller_id')->constrained('sellers')->onDelete('cascade');
            $table->foreignId('self_employed_record_id')->constrained('self_employed_records')->onDelete('cascade');

            // Составной первичный ключ
            $table->primary(['seller_id', 'self_employed_record_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_self_employed_records');
    }
};
