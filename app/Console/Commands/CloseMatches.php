<?php

namespace App\Console\Commands;

use App\Models\Match;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseMatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matches:close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command run every night to close matches';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::now();
        $matches = Match::all();

        $matches = $matches->where('is_closed', false);
        $matches = $matches->filter(function ($match) use ($today) {
            return $match->when_play < $today->toDateTimeString();
        });

        foreach ($matches as $match) {
            $match->update([
                'is_closed' => true
            ]);
        }

        Log::info('Matches closed ===> ' . json_encode($matches->count()));
        Log::info('List matches ids ===> ' . json_encode($matches->pluck('id')));

        return 'All matches closed';
    }
}
