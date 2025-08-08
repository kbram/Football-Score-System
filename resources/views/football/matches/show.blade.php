@extends('layouts.app')

@section('title', 'Match Details - ' . $match->match_title)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Match Details</h1>
                <h2 class="text-xl text-gray-600">{{ $match->match_title }}</h2>
            </div>

            <!-- Match Information Card -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                <!-- Match Header -->
                <div class="bg-gradient-to-r from-blue-600 to-green-600 text-white p-6">
                    <div class="flex justify-between items-center">
                        <div class="text-lg font-medium">{{ $match->status_text }}</div>
                        <div class="text-lg">{{ $match->match_time }}'</div>
                    </div>
                </div>

                <!-- Teams and Score -->
                <div class="p-8">
                    <div class="grid grid-cols-3 gap-4 items-center mb-6">
                        <!-- Team A -->
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-800 mb-3">{{ $match->team_a }}</div>
                            <div class="text-6xl font-bold text-blue-600">{{ $match->team_a_score }}</div>
                        </div>

                        <!-- VS and Details -->
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-400 mb-2">VS</div>
                            <div class="text-lg text-gray-600">{{ $match->match_time }}'</div>
                            <div class="text-sm text-gray-500 mt-2">{{ $match->status_text }}</div>
                        </div>

                        <!-- Team B -->
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-800 mb-3">{{ $match->team_b }}</div>
                            <div class="text-6xl font-bold text-red-600">{{ $match->team_b_score }}</div>
                        </div>
                    </div>

                    <!-- Match Description -->
                    @if ($match->description)
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">Match Information</h4>
                            <p class="text-gray-600">{{ $match->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Match Metadata -->
                <div class="bg-gray-50 px-8 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <strong>Created:</strong> {{ $match->created_at->format('d M Y, H:i') }}
                        </div>
                        <div>
                            <strong>Last Updated:</strong> {{ $match->updated_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap justify-center gap-4 mb-8">
                <a href="{{ route('football.matches.live', $match->id) }}"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg" target="_blank">
                    🔴 Watch Live
                </a>

                <a href="{{ route('football.matches.control-panel', $match->id) }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                    ⚙️ Control Panel
                </a>

                <a href="{{ route('football.matches.edit', $match->id) }}"
                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-3 px-6 rounded-lg">
                    ✏️ Edit Match
                </a>

                <a href="{{ route('football.matches.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg">
                    ← Back to Matches
                </a>
            </div>

            <!-- Real-time Status Display -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Live Updates</h3>
                <div id="live-updates" class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded border-l-4 border-blue-500">
                        <div>
                            <div class="font-medium text-gray-800">Match Loaded</div>
                            <div class="text-sm text-gray-600">Current score: {{ $match->formatted_score }}</div>
                        </div>
                        <div class="text-sm text-gray-500">{{ now()->format('H:i:s') }}</div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <div id="connection-status"
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></div>
                        <span>Connecting to live feed...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // WebSocket Configuration
        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            encrypted: true,
            wsHost: '{{ config('broadcasting.connections.pusher.options.host') }}',
            wsPort: '{{ config('broadcasting.connections.pusher.options.port') }}',
            forceTLS: false,
            enabledTransports: ['ws', 'wss']
        });

        // Subscribe to match-specific channel
        const matchChannel = pusher.subscribe('football-match.{{ $match->id }}');

        // Connection status handling
        pusher.connection.bind('connected', function() {
            updateConnectionStatus('🟢 Connected to live feed', 'bg-green-100 text-green-800');
        });

        pusher.connection.bind('disconnected', function() {
            updateConnectionStatus('🔴 Disconnected', 'bg-red-100 text-red-800');
        });

        // Listen for score updates
        matchChannel.bind('score.updated', function(data) {
            console.log('Received update:', data);
            updateMatchDisplay(data.match);
            addLiveUpdate(data);
        });

        function updateConnectionStatus(text, classes) {
            const statusElement = document.getElementById('connection-status');
            statusElement.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm ' + classes;
            statusElement.innerHTML = `
            <div class="w-2 h-2 bg-current rounded-full mr-2"></div>
            <span>${text}</span>
        `;
        }

        function updateMatchDisplay(match) {
            // Update scores if elements exist (they might not on this page)
            const teamAScore = document.getElementById('team-a-score');
            const teamBScore = document.getElementById('team-b-score');
            if (teamAScore) teamAScore.textContent = match.team_a_score;
            if (teamBScore) teamBScore.textContent = match.team_b_score;
        }

        function addLiveUpdate(data) {
            const updatesContainer = document.getElementById('live-updates');
            const updateDiv = document.createElement('div');

            const eventType = getEventTypeDisplay(data.event_type);
            const timestamp = new Date(data.timestamp).toLocaleTimeString();

            updateDiv.className = 'flex items-center justify-between p-3 bg-green-50 rounded border-l-4 border-green-500';
            updateDiv.innerHTML = `
            <div>
                <div class="font-medium text-gray-800">${eventType}</div>
                <div class="text-sm text-gray-600">${data.event_data.message || 'Match updated'}</div>
            </div>
            <div class="text-sm text-gray-500">${timestamp}</div>
        `;

            updatesContainer.insertBefore(updateDiv, updatesContainer.firstChild);

            // Keep only last 5 updates
            while (updatesContainer.children.length > 5) {
                updatesContainer.removeChild(updatesContainer.lastChild);
            }
        }

        function getEventTypeDisplay(eventType) {
            const types = {
                'score_update': '⚽ Score Update',
                'status_update': '📊 Status Change',
                'time_update': '⏱️ Time Update',
                'match_created': '🆕 Match Created',
                'match_updated': '✏️ Match Updated'
            };
            return types[eventType] || '📝 Update';
        }
    </script>
@endpush
