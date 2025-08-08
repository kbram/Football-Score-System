<?php

namespace App\Repositories\Football;

use App\Events\ScoreUpdated;
use App\Models\FootballMatch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FootballMatchRepository
{
    /**
     * Create a new football match
     */
    public function createMatch($request)
    {
        try {
            DB::beginTransaction();

            $match = FootballMatch::create([
                'team_a' => $request['team_a'],
                'team_b' => $request['team_b'],
                'team_a_score' => $request['team_a_score'] ?? 0,
                'team_b_score' => $request['team_b_score'] ?? 0,
                'status' => $request['status'] ?? FootballMatch::STATUS_NOT_STARTED,
                'match_time' => $request['match_time'] ?? 0,
                'description' => $request['description'] ?? null,
                'created_by' => Auth::id()
            ]);

            DB::commit();
            
            // Broadcast the match creation
            broadcast(new ScoreUpdated($match, 'match_created', [
                'message' => 'New match created: ' . $match->match_title
            ]));

            Log::info('New football match created.', [
                'Match' => $match->match_title, 
                'Created by: ' => Auth::user()?->name ?? 'System'
            ]);
            
            return redirect()->route('football.matches.index')->with('success', 'Match created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Football match creation failed.', ['Error: ' => $e->getMessage()]);
            return redirect()->route('football.matches.index')->with('error', 'Failed to create match');
        }
    }

    /**
     * Update match details
     */
    public function updateMatch($request, $id)
    {
        try {
            DB::beginTransaction();
            
            $match = FootballMatch::findOrFail($id);
            $oldData = $match->toArray();

            $match->update([
                'team_a' => $request['team_a'],
                'team_b' => $request['team_b'],
                'description' => $request['description'] ?? $match->description,
                'updated_by' => Auth::id()
            ]);

            DB::commit();

            // Broadcast the match update
            broadcast(new ScoreUpdated($match, 'match_updated', [
                'message' => 'Match details updated: ' . $match->match_title,
                'changes' => $this->getChanges($oldData, $match->toArray())
            ]));

            Log::info('Football match updated.', [
                'Match' => $match->match_title, 
                'Updated by: ' => Auth::user()?->name ?? 'System'
            ]);
            
            return redirect()->route('football.matches.index')->with('success', 'Match updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Football match update failed.', ['Error: ' => $e->getMessage()]);
            return redirect()->route('football.matches.index')->with('error', 'Failed to update match');
        }
    }

    /**
     * Update match score
     */
    public function updateScore($id, $teamAScore, $teamBScore, $eventData = [])
    {
        try {
            DB::beginTransaction();
            
            $match = FootballMatch::findOrFail($id);
            $oldScore = [
                'team_a_score' => $match->team_a_score,
                'team_b_score' => $match->team_b_score
            ];

            $match->update([
                'team_a_score' => $teamAScore,
                'team_b_score' => $teamBScore,
                'updated_by' => Auth::id()
            ]);

            DB::commit();

            // Broadcast the score update
            broadcast(new ScoreUpdated($match, 'score_update', array_merge($eventData, [
                'message' => 'Score updated: ' . $match->formatted_score,
                'old_score' => $oldScore['team_a_score'] . ' - ' . $oldScore['team_b_score'],
                'new_score' => $match->formatted_score
            ])));

            Log::info('Football match score updated.', [
                'Match' => $match->match_title,
                'Score' => $match->formatted_score,
                'Updated by: ' => Auth::user()?->name ?? 'System'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Score updated successfully',
                'match' => $match
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Football match score update failed.', ['Error: ' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update score'
            ], 500);
        }
    }

    /**
     * Update match status
     */
    public function updateStatus($id, $status)
    {
        try {
            DB::beginTransaction();
            
            $match = FootballMatch::findOrFail($id);
            $oldStatus = $match->status;

            // Handle timer logic based on status change
            if ($status === FootballMatch::STATUS_IN_PROGRESS && $oldStatus !== FootballMatch::STATUS_IN_PROGRESS) {
                // Starting the match - start timer
                $match->update([
                    'status' => $status,
                    'started_at' => now(),
                    'timer_running' => true,
                    'match_time' => 0, // Reset time when starting
                    'updated_by' => Auth::id()
                ]);
            } elseif ($status === FootballMatch::STATUS_HALF_TIME && $oldStatus === FootballMatch::STATUS_IN_PROGRESS) {
                // Going to half time - pause timer
                if ($match->timer_running && $match->started_at) {
                    $totalMinutes = now()->diffInMinutes($match->started_at);
                    $match->update([
                        'status' => $status,
                        'match_time' => $match->match_time + $totalMinutes,
                        'timer_running' => false,
                        'started_at' => null,
                        'updated_by' => Auth::id()
                    ]);
                } else {
                    $match->update([
                        'status' => $status,
                        'updated_by' => Auth::id()
                    ]);
                }
            } elseif ($status === FootballMatch::STATUS_IN_PROGRESS && $oldStatus === FootballMatch::STATUS_HALF_TIME) {
                // Resuming from half time - restart timer
                $match->update([
                    'status' => $status,
                    'started_at' => now(),
                    'timer_running' => true,
                    'updated_by' => Auth::id()
                ]);
            } elseif ($status === FootballMatch::STATUS_FINISHED) {
                // Finishing the match - stop timer
                if ($match->timer_running && $match->started_at) {
                    $totalMinutes = now()->diffInMinutes($match->started_at);
                    $match->update([
                        'status' => $status,
                        'match_time' => $match->match_time + $totalMinutes,
                        'timer_running' => false,
                        'started_at' => null,
                        'updated_by' => Auth::id()
                    ]);
                } else {
                    $match->update([
                        'status' => $status,
                        'updated_by' => Auth::id()
                    ]);
                }
            } else {
                // Regular status update
                $match->update([
                    'status' => $status,
                    'updated_by' => Auth::id()
                ]);
            }

            DB::commit();

            // Broadcast the status update
            broadcast(new ScoreUpdated($match, 'status_update', [
                'message' => 'Match status changed to: ' . $match->status_text,
                'old_status' => $oldStatus,
                'new_status' => $status
            ]));

            Log::info('Football match status updated.', [
                'Match' => $match->match_title,
                'Status' => $match->status_text,
                'Updated by: ' => Auth::user()?->name ?? 'System'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'match' => [
                    'id' => $match->id,
                    'team_a' => $match->team_a,
                    'team_b' => $match->team_b,
                    'team_a_score' => $match->team_a_score,
                    'team_b_score' => $match->team_b_score,
                    'status' => $match->status,
                    'status_text' => $match->status_text,
                    'match_time' => $match->match_time,
                    'current_match_time' => $match->current_match_time,
                    'timer_running' => $match->timer_running,
                    'started_at' => $match->started_at,
                    'match_title' => $match->match_title
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Football match status update failed.', ['Error: ' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    /**
     * Update match time
     */
    public function updateMatchTime($id, $matchTime)
    {
        try {
            DB::beginTransaction();
            
            $match = FootballMatch::findOrFail($id);

            $match->update([
                'match_time' => $matchTime,
                'updated_by' => Auth::id()
            ]);

            DB::commit();

            // Broadcast the time update
            broadcast(new ScoreUpdated($match, 'time_update', [
                'message' => 'Match time: ' . $matchTime . ' minutes',
                'match_time' => $matchTime
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Match time updated successfully',
                'match' => $match
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Football match time update failed.', ['Error: ' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update match time'
            ], 500);
        }
    }

    /**
     * Delete a match
     */
    public function deleteMatch($id)
    {
        try {
            DB::beginTransaction();

            $match = FootballMatch::findOrFail($id);
            $matchTitle = $match->match_title;
            
            $match->update(['updated_by' => Auth::id()]);
            $match->delete();

            DB::commit();

            Log::info('Football match deleted.', [
                'Match' => $matchTitle,
                'Deleted by: ' => Auth::user()?->name ?? 'System'
            ]);
            
            return redirect()->route('football.matches.index')->with('success', 'Match deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Football match deletion failed.', ['Error: ' => $e->getMessage()]);
            return redirect()->route('football.matches.index')->with('error', 'Failed to delete match');
        }
    }

    /**
     * Get all matches with pagination
     */
    public function getAllMatches($perPage = 15)
    {
        return FootballMatch::orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get active matches (in progress or half time)
     */
    public function getActiveMatches()
    {
        return FootballMatch::whereIn('status', [
            FootballMatch::STATUS_IN_PROGRESS,
            FootballMatch::STATUS_HALF_TIME
        ])->orderBy('updated_at', 'desc')->get();
    }

    /**
     * Get a specific match
     */
    public function getMatch($id)
    {
        return FootballMatch::findOrFail($id);
    }

    /**
     * Simulate a goal for a team
     */
    public function simulateGoal($id, $team, $goalData = [])
    {
        $match = FootballMatch::findOrFail($id);
        
        if ($team === 'team_a') {
            $newScore = $match->team_a_score + 1;
            return $this->updateScore($id, $newScore, $match->team_b_score, array_merge($goalData, [
                'goal_team' => $match->team_a,
                'scorer' => $goalData['scorer'] ?? 'Unknown',
                'goal_time' => $goalData['goal_time'] ?? $match->match_time
            ]));
        } else {
            $newScore = $match->team_b_score + 1;
            return $this->updateScore($id, $match->team_a_score, $newScore, array_merge($goalData, [
                'goal_team' => $match->team_b,
                'scorer' => $goalData['scorer'] ?? 'Unknown',
                'goal_time' => $goalData['goal_time'] ?? $match->match_time
            ]));
        }
    }

    /**
     * Get changes between old and new data
     */
    private function getChanges($oldData, $newData)
    {
        $changes = [];
        foreach ($newData as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $oldData[$key],
                    'new' => $value
                ];
            }
        }
        return $changes;
    }
}
