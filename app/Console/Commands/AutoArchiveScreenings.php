<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Screening;
use Carbon\Carbon;

class AutoArchiveScreenings extends Command
{
    
    protected $signature = 'screenings:auto-archive-screenings';
    protected $description = 'Automatycznie archiwizowanie seansów(playing_until < dziś)';
    public function handle()
    {
        $today = Carbon::today();
        $count = Screening::where('screening_date', '<', $today)
            ->where('status', '!=', 'archived')
            ->update(['status' => 'archived']);

        $this -> info("Zarchiwizowano {$count} seansów.");
    }
}
