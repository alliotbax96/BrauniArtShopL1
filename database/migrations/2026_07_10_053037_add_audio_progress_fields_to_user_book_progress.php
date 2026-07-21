<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('user_book_progress', function (Blueprint $table) {
            $table->foreignId('current_chapter_id')->nullable()->constrained('book_chapters')->nullOnDelete();
            $table->integer('current_position')->default(0); // секунды
            $table->float('playback_speed')->default(1.0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_book_progress', function (Blueprint $table) {
            //
        });
    }
};
