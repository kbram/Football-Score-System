<?php

namespace Database\Seeders;

use App\Models\FootballMatch;
use Illuminate\Database\Seeder;

class FootballMatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matches = [
            [
                'team_a' => 'Manchester United',
                'team_b' => 'Liverpool FC',
                'team_a_score' => 2,
                'team_b_score' => 1,
                'status' => FootballMatch::STATUS_IN_PROGRESS,
                'match_time' => 65,
                'description' => 'Premier League - Old Trafford',
                'created_by' => 1,
            ],
            [
                'team_a' => 'Arsenal',
                'team_b' => 'Chelsea',
                'team_a_score' => 0,
                'team_b_score' => 0,
                'status' => FootballMatch::STATUS_HALF_TIME,
                'match_time' => 45,
                'description' => 'Premier League - Emirates Stadium',
                'created_by' => 1,
            ],
            [
                'team_a' => 'Real Madrid',
                'team_b' => 'Barcelona',
                'team_a_score' => 3,
                'team_b_score' => 2,
                'status' => FootballMatch::STATUS_FINISHED,
                'match_time' => 90,
                'description' => 'El Clasico - Santiago Bernabeu',
                'created_by' => 1,
            ],
            [
                'team_a' => 'Bayern Munich',
                'team_b' => 'Borussia Dortmund',
                'team_a_score' => 0,
                'team_b_score' => 0,
                'status' => FootballMatch::STATUS_NOT_STARTED,
                'match_time' => 0,
                'description' => 'Bundesliga - Allianz Arena',
                'created_by' => 1,
            ],
            [
                'team_a' => 'Paris Saint-Germain',
                'team_b' => 'Olympique Marseille',
                'team_a_score' => 1,
                'team_b_score' => 1,
                'status' => FootballMatch::STATUS_IN_PROGRESS,
                'match_time' => 78,
                'description' => 'Ligue 1 - Parc des Princes',
                'created_by' => 1,
            ],
        ];

        foreach ($matches as $matchData) {
            FootballMatch::create($matchData);
        }
    }
}
