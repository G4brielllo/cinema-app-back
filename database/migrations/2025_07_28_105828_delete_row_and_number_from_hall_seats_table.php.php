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
         Schema::table('hall_seats', function (Blueprint $table) {
            $table->dropColumn(['row', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('hall_seats', function (Blueprint $table) {
            $table->integer('row');
            $table->integer('number')->after('row');
        });
    }
};
