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
        Schema::table('seller_contract', function (Blueprint $table) {
            $table->boolean('signed_status')->default(false);
            $table->foreignId('seller_legal_details_id')->constrained('seller_legal_details', 'legal_entity_detail_id')->onDelete('cascade');
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
