<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\ScoreUpdated;

class SimulateScore extends Command
{
    protected $signature = 'score:simulate';
    protected $description = 'Simulate football match score updates';

    public function handle()
    {
        $teamA = 'Team A';
        $teamB = 'Team B';
        $scoreA = 0;
        $scoreB = 0;
        $status = 'In Progress';

        $this->info("Match started: $teamA vs $teamB");

        for ($i = 1; $i <= 5; $i++) {
            sleep(5); // Simulate time between goals
            if (rand(0, 1)) {
                $scoreA++;
            } else {
                $scoreB++;
            }
            broadcast(new ScoreUpdated($teamA, $teamB, $scoreA, $scoreB, $status));
            $this->info("Score updated: $scoreA - $scoreB");
        }

        $status = 'Finished';
        broadcast(new ScoreUpdated($teamA, $teamB, $scoreA, $scoreB, $status));
        $this->info("Match finished: $scoreA - $scoreB");
    }
}
