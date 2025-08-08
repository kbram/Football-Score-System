<?php

namespace App\Console\Commands;

use App\Events\ScoreUpdated;
use App\Models\FootballMatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateMatchTimers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'football:update-timers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update match timers for all running matches and broadcast live updates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $runningMatches = FootballMatch::where('timer_running', true)
            ->where('status', FootballMatch::STATUS_IN_PROGRESS)
            ->whereNotNull('started_at')
            ->get();

        if ($runningMatches->isEmpty()) {
            $this->info('No running matches found.');
            return 0;
        }

        $this->info("Found {$runningMatches->count()} running matches.");

        foreach ($runningMatches as $match) {
            try {
                $currentTime = $match->current_match_time;
                
                // Create a more complete match data structure for broadcasting
                $matchData = [
                    'id' => $match->id,
                    'team_a' => $match->team_a,
                    'team_b' => $match->team_b,
                    'team_a_score' => $match->team_a_score,
                    'team_b_score' => $match->team_b_score,
                    'status' => $match->status,
                    'status_text' => $match->status_text,
                    'match_time' => $match->match_time,
                    'current_match_time' => $currentTime,
                    'timer_running' => $match->timer_running,
                    'started_at' => $match->started_at,
                    'match_title' => $match->match_title
                ];
                
                // Broadcast the time update
                broadcast(new ScoreUpdated($match, 'timer_update', [
                    'message' => "Timer update: {$currentTime} minutes",
                    'current_time' => $currentTime,
                    'timer_running' => true,
                    'event_type' => 'timer_update'
                ]));

                $this->info("Updated timer for match: {$match->match_title} - {$currentTime} minutes");
                
                Log::info('Timer updated for match', [
                    'match_id' => $match->id,
                    'match_title' => $match->match_title,
                    'current_time' => $currentTime,
                    'started_at' => $match->started_at
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to update timer for match: ' . $match->id, [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $this->error("Failed to update timer for match: {$match->match_title}");
            }
        }

        return 0;
    }
}
