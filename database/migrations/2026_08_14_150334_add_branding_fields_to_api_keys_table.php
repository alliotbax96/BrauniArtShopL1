<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->string('service_name')->nullable()->after('name');
            $table->string('service_logo')->nullable()->after('service_name');
            $table->string('primary_color', 50)->default('#90e9d1')->after('service_logo');
            $table->string('secondary_color', 50)->default('#5cd4b8')->after('primary_color');
            $table->string('text_color', 50)->default('#0a1a1a')->after('secondary_color');
            $table->string('bg_color', 50)->default('#ffffff')->after('text_color');
            $table->string('company_name')->nullable()->after('bg_color');
            $table->string('inn')->nullable()->after('company_name');
            $table->string('return_url')->nullable()->after('inn');
        });
    }

    public function down(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->dropColumn([
                'service_name',
                'service_logo',
                'primary_color',
                'secondary_color',
                'text_color',
                'bg_color',
                'company_name',
                'inn',
                'return_url'
            ]);
        });
    }
};
