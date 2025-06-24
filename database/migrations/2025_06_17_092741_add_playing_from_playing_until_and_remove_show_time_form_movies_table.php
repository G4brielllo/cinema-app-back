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
        Schema::table('movies', function (Blueprint $table) {
            $table->timestamp('playing_from')->nullable()->after('category');
            $table->timestamp('playing_until')->nullable()->after('playing_from');
            $table->dropColumn('show_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn(['playing_from']);
            $table->dropColumn(['playing_until']);
            $table->timestamp('show_time')->nullable();

        });
    }
};
