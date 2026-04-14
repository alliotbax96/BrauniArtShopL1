<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_legal_details', function (Blueprint $table) {
            $table->foreignId('seller_id')->constrained('sellers')->onDelete('cascade');
            $table->foreignId('legal_entity_detail_id')->constrained('legal_entity_details')->onDelete('cascade');
            $table->primary(['seller_id', 'legal_entity_detail_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_legal_details');
    }
};

