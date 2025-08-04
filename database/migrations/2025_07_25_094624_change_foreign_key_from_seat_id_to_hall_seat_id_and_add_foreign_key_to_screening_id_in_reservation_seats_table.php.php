<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservation_seat', function (Blueprint $table) {
            if (Schema::hasColumn('reservation_seat', 'seat_id')) {
                try {
                    $table->dropForeign(['seat_id']);
                } catch (\Throwable $e) {
                }

                $table->dropColumn('seat_id');
            }

            if (!Schema::hasColumn('reservation_seat', 'hall_seat_id')) {
                $table->unsignedBigInteger('hall_seat_id')->nullable()->after('reservation_id');
            }
            if (!Schema::hasColumn('reservation_seat', 'screening_id')) {
                $table->unsignedBigInteger('screening_id')->nullable()->after('hall_seat_id');
            }
        });

        DB::table('reservation_seat')
            ->whereNotNull('hall_seat_id')
            ->whereNotIn('hall_seat_id', function ($query) {
                $query->select('id')->from('hall_seats');
            })
            ->delete();

        Schema::table('reservation_seat', function (Blueprint $table) {
            $table->foreign('hall_seat_id')
                ->references('id')->on('hall_seats')
                ->onDelete('cascade');

            $table->foreign('screening_id')
                ->references('id')->on('screenings')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('reservation_seat', function (Blueprint $table) {
            $table->dropForeign(['hall_seat_id']);
            $table->dropForeign(['screening_id']);

            if (Schema::hasColumn('reservation_seat', 'hall_seat_id')) {
                $table->dropColumn('hall_seat_id');
            }
            if (Schema::hasColumn('reservation_seat', 'screening_id')) {
                $table->dropColumn('screening_id');
            }
        });
    }
};
