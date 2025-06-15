<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DeleteExpiredReservations extends Command
{
    protected $signature = 'reservations:delete-expired';
    protected $description = 'Deleting expired reservations';
    public function handle()
    {
        Log::info('[CRON] reservations:delete-expired started at ' . now());
        $expired = Reservation::whereIn('status', ['canceled', 'pending'])
            ->where('reservation_time', '<', Carbon::now()->subMinutes(0.5))
            ->with(['seats'])
            ->get();
            $deletedCount = 0;
            foreach ($expired as $reservation) {
                $reservation->seats()->detach();

                foreach($reservation->seats as $seat) {
                    $seat->update (['is_booked' => false]);
                    $seat->delete();
                }
                $reservation->delete();
                $deletedCount++;
            }
        Log::info("Usunięto {$deletedCount} przeterminowanych rezerwacji.");
        $this->info("Usunięto {$deletedCount} przeterminowanych rezerwacji.");

        return 0;
    }
}
