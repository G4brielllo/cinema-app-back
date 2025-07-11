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
        Schema::table('reservations', function (Blueprint $table) {
            $table -> integer('total_amount')->default(0)->after('status');
        });
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('total_amount');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('total_amount');
        });
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('total_amount');
            $table -> integer('total_amount')->default(0)->after('status');

        });
    }
};
