<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('api_key_id')->nullable()->after('id');
            $table->foreign('api_key_id')->references('id')->on('api_keys')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropForeign(['api_key_id']);
            $table->dropColumn('api_key_id');
        });
    }
};
