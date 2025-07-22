<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeysWithCascade extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['screening_id']);
            $table->foreign('screening_id')
                ->references('id')->on('screenings')
                ->onDelete('cascade');
        });

        Schema::table('reservation_seat', function (Blueprint $table) {
            $table->dropForeign(['reservation_id']);
            $table->foreign('reservation_id')
                ->references('id')->on('reservations')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['screening_id']);
            $table->foreign('screening_id')
                ->references('id')->on('screenings')
                ->onDelete('restrict'); // lub omit `onDelete()` całkowicie
        });

        Schema::table('reservation_seat', function (Blueprint $table) {
            $table->dropForeign(['reservation_id']);
            $table->foreign('reservation_id')
                ->references('id')->on('reservations')
                ->onDelete('restrict'); // lub omit
        });
    }
}
