<?php
namespace App\Events;

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

    public $teamA;
    public $teamB;
    public $scoreA;
    public $scoreB;
    public $status;

    public function __construct($teamA, $teamB, $scoreA, $scoreB, $status)
    {
        $this->teamA = $teamA;
        $this->teamB = $teamB;
        $this->scoreA = $scoreA;
        $this->scoreB = $scoreB;
        $this->status = $status;
    }

    public function broadcastOn()
    {
        return new Channel('football.match');
    }
}
