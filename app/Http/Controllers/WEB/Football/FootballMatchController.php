<?php

namespace App\Http\Controllers\WEB\Football;

use App\Http\Controllers\Controller;
use App\Http\Requests\FootballMatchRequest;
use App\Models\FootballMatch;
use App\Repositories\Football\FootballMatchRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FootballMatchController extends Controller
{
    private $repo;

    public function __construct(FootballMatchRepository $footballMatchRepository)
    {
        $this->repo = $footballMatchRepository;
        
        // Admin-only routes
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->isAdmin()) {
                abort(403, 'Access denied. Admin privileges required.');
            }
            return $next($request);
        })->only([
            'create', 'store', 'edit', 'update', 'destroy', 
            'controlPanel', 'updateScore', 'updateStatus', 'updateTime', 'simulateGoal'
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activeMatches = $this->repo->getActiveMatches();
        return view('football.matches.index', compact('activeMatches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('football.matches.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FootballMatchRequest $request
     * @return RedirectResponse
     */
    public function store(FootballMatchRequest $request)
    {
        return $this->repo->createMatch($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $match = $this->repo->getMatch($id);
        return view('football.matches.show', compact('match'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $match = $this->repo->getMatch($id);
        return view('football.matches.edit', compact('match'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FootballMatchRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(FootballMatchRequest $request, string $id)
    {
        return $this->repo->updateMatch($request->all(), $id);
    }

    /**
     * Update match status
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', [
                FootballMatch::STATUS_NOT_STARTED,
                FootballMatch::STATUS_IN_PROGRESS,
                FootballMatch::STATUS_HALF_TIME,
                FootballMatch::STATUS_FINISHED
            ])
        ]);

        return $this->repo->updateStatus($id, $request->get('status'));
    }

    /**
     * Update match score
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateScore(Request $request, $id)
    {
        $request->validate([
            'team_a_score' => 'required|integer|min:0|max:50',
            'team_b_score' => 'required|integer|min:0|max:50'
        ]);

        return $this->repo->updateScore(
            $id,
            $request->get('team_a_score'),
            $request->get('team_b_score'),
            $request->only(['scorer', 'goal_time', 'assist'])
        );
    }

    /**
     * Update match time
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMatchTime(Request $request, $id)
    {
        $request->validate([
            'match_time' => 'required|integer|min:0|max:180'
        ]);

        return $this->repo->updateMatchTime($id, $request->get('match_time'));
    }

    /**
     * Simulate a goal for a team
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function simulateGoal(Request $request, $id)
    {
        $request->validate([
            'team' => 'required|in:team_a,team_b',
            'scorer' => 'nullable|string|max:255',
            'goal_time' => 'nullable|integer|min:0|max:180'
        ]);

        return $this->repo->simulateGoal(
            $id,
            $request->get('team'),
            $request->only(['scorer', 'goal_time', 'assist'])
        );
    }

    /**
     * Match list for datatable
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxMatchData(Request $request)
    {
        $model = FootballMatch::query()
            ->orderBy('created_at', 'desc');

        return DataTables::eloquent($model)
            ->editColumn('teams', function ($match) {
                return $match->match_title;
            })
            ->editColumn('score', function ($match) {
                return '<span class="badge badge-primary text-lg">' . $match->formatted_score . '</span>';
            })
            ->editColumn('status', function ($match) {
                $statusClass = match($match->status) {
                    FootballMatch::STATUS_NOT_STARTED => 'badge-secondary',
                    FootballMatch::STATUS_IN_PROGRESS => 'badge-success',
                    FootballMatch::STATUS_HALF_TIME => 'badge-warning',
                    FootballMatch::STATUS_FINISHED => 'badge-dark',
                    default => 'badge-light'
                };
                return '<span class="badge ' . $statusClass . '">' . $match->status_text . '</span>';
            })
            ->editColumn('match_time', function ($match) {
                $displayTime = $match->current_match_time ?? $match->match_time;
                return $displayTime . ' min';
            })
            ->addColumn('current_time', function ($match) {
                $displayTime = $match->current_match_time ?? $match->match_time;
                return $displayTime . ' min';
            })
            ->editColumn('created_at', function ($match) {
                return $match->created_at->format('d M Y, H:i');
            })
            ->addColumn('action', function ($match) {
                return view('football.matches.partials._action', compact('match'));
            })
            ->rawColumns(['score', 'status', 'action'])
            ->toJson();
    }

    /**
     * Get live match data for real-time updates
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLiveMatchData($id)
    {
        try {
            $match = $this->repo->getMatch($id);
            return response()->json([
                'success' => true,
                'match' => [
                    'id' => $match->id,
                    'team_a' => $match->team_a,
                    'team_b' => $match->team_b,
                    'team_a_score' => $match->team_a_score,
                    'team_b_score' => $match->team_b_score,
                    'status' => $match->status,
                    'status_text' => $match->status_text,
                    'match_time' => $match->match_time,
                    'formatted_score' => $match->formatted_score,
                    'match_title' => $match->match_title,
                    'updated_at' => $match->updated_at->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Match not found'
            ], 404);
        }
    }

    /**
     * Delete record
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        return $this->repo->deleteMatch($id);
    }

    /**
     * Live score page for real-time viewing
     */
    public function liveScore($id = null)
    {
        if ($id) {
            $match = $this->repo->getMatch($id);
            return view('football.matches.live', compact('match'));
        }
        
        $activeMatches = $this->repo->getActiveMatches();
        return view('football.matches.live-list', compact('activeMatches'));
    }

    /**
     * Match control panel for admins
     */
    public function controlPanel($id)
    {
        $match = $this->repo->getMatch($id);
        return view('football.matches.control-panel', compact('match'));
    }
}
