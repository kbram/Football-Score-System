<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FootballMatch extends Model
{
    use HasFactory;

    protected $table = 'football_matches';

    protected $fillable = [
        'team_a',
        'team_b',
        'team_a_score',
        'team_b_score',
        'status',
        'match_time',
        'started_at',
        'timer_running',
        'description',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'team_a_score' => 'integer',
        'team_b_score' => 'integer',
        'match_time' => 'integer',
        'timer_running' => 'boolean',
        'started_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status constants
    const STATUS_NOT_STARTED = 'not_started';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_HALF_TIME = 'half_time';
    const STATUS_FINISHED = 'finished';

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            self::STATUS_NOT_STARTED => 'Not Started',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_HALF_TIME => 'Half Time',
            self::STATUS_FINISHED => 'Finished',
            default => 'Unknown'
        };
    }

    /**
     * Get formatted score
     */
    public function getFormattedScoreAttribute()
    {
        return $this->team_a_score . ' - ' . $this->team_b_score;
    }

    /**
     * Get match title
     */
    public function getMatchTitleAttribute()
    {
        return $this->team_a . ' vs ' . $this->team_b;
    }

    /**
     * Get current match time (automatically calculated if timer is running)
     */
    public function getCurrentMatchTimeAttribute()
    {
        if ($this->timer_running && $this->started_at && $this->status === self::STATUS_IN_PROGRESS) {
            $minutesElapsed = now()->diffInMinutes($this->started_at);
            return $this->match_time + $minutesElapsed;
        }
        
        return $this->match_time;
    }

    /**
     * Start the match timer
     */
    public function startTimer()
    {
        $this->update([
            'started_at' => now(),
            'timer_running' => true,
            'status' => self::STATUS_IN_PROGRESS
        ]);
    }

    /**
     * Stop the match timer
     */
    public function stopTimer()
    {
        if ($this->timer_running && $this->started_at) {
            $totalMinutes = now()->diffInMinutes($this->started_at);
            $this->update([
                'match_time' => $this->match_time + $totalMinutes,
                'timer_running' => false,
                'started_at' => null
            ]);
        }
    }

    /**
     * Pause the match timer
     */
    public function pauseTimer()
    {
        $this->stopTimer();
    }
}
