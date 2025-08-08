@extends('layouts.app')

@section('title', 'Football Matches')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Football Matches</h1>
            <div class="space-x-2">
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('football.matches.create') }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Create New Match
                    </a>
                @endif
                <a href="{{ route('football.live-scores') }}"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Live Scores
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Active Matches Section -->
        @if ($activeMatches->count() > 0)
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">🔴 Live Matches</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($activeMatches as $match)
                        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500"
                            data-match-id="{{ $match->id }}">
                            <div class="flex justify-between items-center mb-3">
                                <span
                                    class="text-sm font-medium text-green-600 match-status">{{ $match->status_text }}</span>
                                <span
                                    class="text-sm text-gray-500 match-time">{{ $match->current_match_time ?? $match->match_time }}'</span>
                            </div>
                            <div class="text-center">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $match->match_title }}</h3>
                                <div class="text-3xl font-bold text-blue-600 mb-3 match-score">{{ $match->formatted_score }}
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('football.matches.live', $match->id) }}"
                                        class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm">
                                        Watch Live
                                    </a>
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('football.matches.control-panel', $match->id) }}"
                                            class="flex-1 bg-gray-500 hover:bg-gray-700 text-white text-center py-2 px-3 rounded text-sm">
                                            Control
                                        </a>
                                    @else
                                        <a href="{{ route('football.matches.live', $match->id) }}"
                                            class="flex-1 bg-green-500 hover:bg-green-700 text-white text-center py-2 px-3 rounded text-sm">
                                            View Details
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- All Matches Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">All Matches</h2>
            </div>
            <div class="p-6">
                <table id="matches-table" class="w-full">
                    <thead>
                        <tr>
                            <th>Teams</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Time</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#matches-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('football.matches.ajax-data') }}',
                columns: [{
                        data: 'teams',
                        name: 'teams'
                    },
                    {
                        data: 'score',
                        name: 'score',
                        orderable: false
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'current_time',
                        name: 'current_time'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [4, 'desc']
                ],
                responsive: true
            });

            // WebSocket Configuration for real-time updates
            const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
                cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
                encrypted: true,
                wsHost: '{{ config('broadcasting.connections.pusher.options.host') }}',
                wsPort: '{{ config('broadcasting.connections.pusher.options.port') }}',
                forceTLS: false,
                enabledTransports: ['ws', 'wss']
            });

            // Subscribe to global matches channel
            const globalChannel = pusher.subscribe('football-matches');

            // Listen for match updates
            globalChannel.bind('score.updated', function(data) {
                console.log('Match update received:', data);

                // Update live match cards
                updateLiveMatchCard(data.match);

                // Refresh DataTable
                table.ajax.reload(null, false);
            });

            // Subscribe to individual match channels for active matches
            @foreach ($activeMatches as $match)
                const channel{{ $match->id }} = pusher.subscribe('football-match.{{ $match->id }}');
                channel{{ $match->id }}.bind('score.updated', function(data) {
                    updateLiveMatchCard(data.match);
                });
            @endforeach

            // Function to update live match cards
            function updateLiveMatchCard(match) {
                const matchCard = $(`[data-match-id="${match.id}"]`);
                if (matchCard.length > 0) {
                    // Update time - use current_match_time if available, fallback to match_time
                    const displayTime = match.current_match_time !== undefined ? match.current_match_time : match
                        .match_time;
                    matchCard.find('.match-time').text(displayTime + "'");

                    // Update status
                    matchCard.find('.match-status').text(match.status_text);

                    // Update score
                    const formattedScore = `${match.team_a_score} - ${match.team_b_score}`;
                    matchCard.find('.match-score').text(formattedScore);
                }
            }
        });
    </script>
@endpush
