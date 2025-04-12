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
            $table->longText('image')->nullable();
            $table->string('direction')->nullable();
            $table->string('script')->nullable();
            $table->year('production_year')->nullable();
            $table->text('cast')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn(['image', 'direction', 'script', 'production_year', 'cast']);
        });
    }
};
