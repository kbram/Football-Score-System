<?php

namespace App\Events;

use App\Models\FootballMatch;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScoreUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public FootballMatch $match;
    public string $eventType;
    public array $eventData;

    /**
     * Create a new event instance.
     */
    public function __construct(FootballMatch $match, string $eventType = 'score_update', array $eventData = [])
    {
        $this->match = $match;
        $this->eventType = $eventType;
        $this->eventData = $eventData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('football-match.' . $this->match->id),
            new Channel('football-matches'), // Global channel for all matches
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'match' => [
                'id' => $this->match->id,
                'team_a' => $this->match->team_a,
                'team_b' => $this->match->team_b,
                'team_a_score' => $this->match->team_a_score,
                'team_b_score' => $this->match->team_b_score,
                'status' => $this->match->status,
                'status_text' => $this->match->status_text,
                'match_time' => $this->match->match_time,
                'current_match_time' => $this->match->current_match_time,
                'timer_running' => $this->match->timer_running,
                'started_at' => $this->match->started_at,
                'formatted_score' => $this->match->formatted_score,
                'match_title' => $this->match->match_title,
                'updated_at' => $this->match->updated_at->toISOString()
            ],
            'event_type' => $this->eventType,
            'event_data' => $this->eventData,
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'score.updated';
    }
}
