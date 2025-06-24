<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use Carbon\Carbon;

class AutoArchiveMovies extends Command
{
    
    protected $signature = 'movies:auto-archive-movies';
    protected $description = 'Automatycznie archiwizowanie filmów(playing_until < dziś)';
    public function handle()
    {
        $today = Carbon::today();
        $count = Movie::where('playing_until', '<', $today)
            ->where('status', '!=', 'archived')
            ->update(['status' => 'archived']);

        $this -> info("Zarchiwizowano {$count} filmów.");
    }
}
