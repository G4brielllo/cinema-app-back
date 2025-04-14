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
            $table->dropColumn('audio_type');
        });
            
        Schema::table('movies', function (Blueprint $table) {
            $table->enum('audio_type', ['Dubbing', 'Napisy'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            //
        });
    }
};
