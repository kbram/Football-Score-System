<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Football Scores - All Matches</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">🔴 LIVE FOOTBALL SCORES</h1>
            <p class="text-gray-600">All matches with real-time updates</p>
        </div>

        <!-- Connection Status -->
        <div class="mb-6 text-center">
            <div id="connection-status" class="inline-flex items-center px-4 py-2 rounded-full text-sm">
                <div class="w-3 h-3 bg-red-500 rounded-full mr-2 animate-pulse" id="status-indicator"></div>
                <span id="status-text">Connecting to live feed...</span>
            </div>
        </div>

        <!-- Active Matches Section -->
        @if ($activeMatches->count() > 0)
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-700 mb-6 text-center">⚡ Active Matches</h2>
                <div id="active-matches" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($activeMatches as $match)
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden" id="match-{{ $match->id }}">
                            <!-- Match Header -->
                            <div class="bg-gradient-to-r from-green-500 to-blue-500 text-white p-4">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-medium status-text">{{ $match->status_text }}</span>
                                    <span class="match-time"
                                        data-timer-running="{{ $match->timer_running ? 'true' : 'false' }}"
                                        data-current-time="{{ $match->current_match_time }}">{{ $match->current_match_time }}'</span>
                                </div>
                            </div>

                            <!-- Teams and Score -->
                            <div class="p-6">
                                <div class="grid grid-cols-3 gap-4 items-center">
                                    <!-- Team A -->
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-gray-800 mb-2 team-a-name">
                                            {{ $match->team_a }}</div>
                                        <div class="text-3xl font-bold text-blue-600 team-a-score">
                                            {{ $match->team_a_score }}</div>
                                    </div>

                                    <!-- VS and Time -->
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-gray-400 mb-2">VS</div>
                                        <div class="text-sm text-gray-600 current-time"
                                            data-timer-running="{{ $match->timer_running ? 'true' : 'false' }}"
                                            data-current-time="{{ $match->current_match_time }}">
                                            {{ $match->current_match_time }}'</div>
                                    </div>

                                    <!-- Team B -->
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-gray-800 mb-2 team-b-name">
                                            {{ $match->team_b }}</div>
                                        <div class="text-3xl font-bold text-red-600 team-b-score">
                                            {{ $match->team_b_score }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Match Actions -->
                            <div class="bg-gray-50 px-6 py-3">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('football.matches.live', $match->id) }}"
                                        class="flex-1 bg-green-500 hover:bg-green-700 text-white text-center py-2 px-3 rounded text-sm font-medium"
                                        target="_blank">
                                        Watch Live
                                    </a>
                                    @auth
                                        @if (auth()->user()->isAdmin())
                                            <a href="{{ route('football.matches.control-panel', $match->id) }}"
                                                class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm font-medium">
                                                Control
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                                <div class="text-center text-xs text-gray-500 mt-2">
                                    Last updated: <span
                                        class="last-updated">{{ $match->updated_at->format('H:i:s') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">⚽</div>
                <h3 class="text-2xl font-semibold text-gray-700 mb-2">No Active Matches</h3>
                <p class="text-gray-500 mb-6">There are currently no matches in progress.</p>
                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('football.matches.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                            Create New Match
                        </a>
                    @endif
                @endauth
            </div>
        @endif

        <!-- Global Events Feed -->
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">📰 Live Events Feed</h3>
            </div>
            <div class="p-4">
                <div id="events-feed" class="space-y-3 max-h-64 overflow-y-auto">
                    <!-- Events will be populated here via WebSocket -->
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="mt-8 text-center space-x-4">
            @auth
                <a href="{{ route('football.matches.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Back to Dashboard
                </a>
            @endauth
            <a href="{{ url('/') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Home
            </a>
        </div>
    </div>

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

        // Subscribe to global matches channel
        const globalChannel = pusher.subscribe('football-matches');

        // Connection status handling
        pusher.connection.bind('connected', function() {
            updateConnectionStatus('connected', '🟢 Connected to live feed', 'bg-green-100 text-green-800');
            addEventToFeed('System', 'Connected to live feed successfully', 'connection');
        });

        pusher.connection.bind('disconnected', function() {
            updateConnectionStatus('disconnected', '🔴 Disconnected', 'bg-red-100 text-red-800');
            addEventToFeed('System', 'Connection lost', 'error');
        });

        pusher.connection.bind('connecting', function() {
            updateConnectionStatus('connecting', '🟡 Connecting...', 'bg-yellow-100 text-yellow-800');
        });

        // Listen for score updates on global channel
        globalChannel.bind('score.updated', function(data) {
            console.log('Received global update:', data);
            updateMatchDisplay(data.match);
            addEventToFeed(data.match.match_title, data.event_data.message || 'Match updated', data.event_type);
        });

        // Subscribe to individual match channels for active matches
        @foreach ($activeMatches as $match)
            const matchChannel{{ $match->id }} = pusher.subscribe('football-match.{{ $match->id }}');
            matchChannel{{ $match->id }}.bind('score.updated', function(data) {
                console.log('Match {{ $match->id }} update:', data);
                updateMatchDisplay(data.match);
                addEventToFeed(data.match.match_title, data.event_data.message || 'Match updated', data.event_type);
            });
        @endforeach

        // Client-side timer management
        const matchTimers = {};

        // Initialize timers for running matches
        document.querySelectorAll('.match-time, .current-time').forEach(timeElement => {
            const matchId = timeElement.closest('[id*="match-"]').id.split('-')[1];
            const isRunning = timeElement.dataset.timerRunning === 'true';
            const currentTime = parseInt(timeElement.dataset.currentTime) || 0;

            if (isRunning) {
                startMatchTimer(matchId, currentTime);
            }
        });

        function startMatchTimer(matchId, initialTime) {
            // Clear existing timer if any
            if (matchTimers[matchId]) {
                clearInterval(matchTimers[matchId]);
            }

            let currentTime = initialTime;

            matchTimers[matchId] = setInterval(() => {
                currentTime++;
                updateMatchTimeDisplay(matchId, currentTime);
            }, 60000); // Update every minute
        }

        function stopMatchTimer(matchId) {
            if (matchTimers[matchId]) {
                clearInterval(matchTimers[matchId]);
                delete matchTimers[matchId];
            }
        }

        function updateMatchTimeDisplay(matchId, time) {
            const matchCard = document.getElementById(`match-${matchId}`);
            if (matchCard) {
                const matchTime = matchCard.querySelector('.match-time');
                const currentTime = matchCard.querySelector('.current-time');

                if (matchTime) {
                    matchTime.textContent = time + "'";
                    matchTime.dataset.currentTime = time;
                }
                if (currentTime) {
                    currentTime.textContent = time + "'";
                    currentTime.dataset.currentTime = time;
                }
            }
        }

        // Update connection status indicator
        function updateConnectionStatus(status, text, colorClass) {
            const indicator = document.getElementById('status-indicator');
            const statusText = document.getElementById('status-text');
            const statusContainer = document.getElementById('connection-status');

            statusContainer.className = 'inline-flex items-center px-4 py-2 rounded-full text-sm ' + colorClass;

            statusText.textContent = text;

            if (status === 'connected') {
                indicator.className = 'w-3 h-3 bg-green-500 rounded-full mr-2';
            } else if (status === 'connecting') {
                indicator.className = 'w-3 h-3 bg-yellow-500 rounded-full mr-2 animate-pulse';
            } else {
                indicator.className = 'w-3 h-3 bg-red-500 rounded-full mr-2 animate-pulse';
            }
        }

        // Update match display when receiving WebSocket updates
        function updateMatchDisplay(match) {
            const matchCard = document.getElementById(`match-${match.id}`);
            if (matchCard) {
                // Update scores
                const teamAScore = matchCard.querySelector('.team-a-score');
                const teamBScore = matchCard.querySelector('.team-b-score');
                const statusText = matchCard.querySelector('.status-text');
                const matchTime = matchCard.querySelector('.match-time');
                const currentTime = matchCard.querySelector('.current-time');
                const lastUpdated = matchCard.querySelector('.last-updated');

                if (teamAScore) teamAScore.textContent = match.team_a_score;
                if (teamBScore) teamBScore.textContent = match.team_b_score;
                if (statusText) statusText.textContent = match.status_text;

                // Update time and timer state
                const displayTime = match.current_match_time || match.match_time || 0;
                if (matchTime) {
                    matchTime.textContent = displayTime + "'";
                    matchTime.dataset.currentTime = displayTime;
                    matchTime.dataset.timerRunning = match.timer_running ? 'true' : 'false';
                }
                if (currentTime) {
                    currentTime.textContent = displayTime + "'";
                    currentTime.dataset.currentTime = displayTime;
                    currentTime.dataset.timerRunning = match.timer_running ? 'true' : 'false';
                }

                // Handle timer based on match status and timer_running flag
                if (match.status === 'in_progress' && match.timer_running) {
                    startMatchTimer(match.id, displayTime);
                } else {
                    stopMatchTimer(match.id);
                }

                if (lastUpdated) lastUpdated.textContent = new Date().toLocaleTimeString();

                // Add update animation
                matchCard.style.transform = 'scale(1.02)';
                matchCard.style.transition = 'transform 0.3s ease';
                setTimeout(() => {
                    matchCard.style.transform = 'scale(1)';
                }, 300);

                // Update status color
                const header = matchCard.querySelector('.bg-gradient-to-r');
                if (header) {
                    header.className = 'bg-gradient-to-r text-white p-4 ' + getStatusGradient(match.status);
                }
            }
        }

        // Get gradient class for match status
        function getStatusGradient(status) {
            const gradients = {
                'not_started': 'from-gray-500 to-gray-600',
                'in_progress': 'from-green-500 to-blue-500',
                'half_time': 'from-yellow-500 to-orange-500',
                'finished': 'from-gray-700 to-gray-800'
            };
            return gradients[status] || 'from-gray-500 to-gray-600';
        }

        // Add event to live feed
        function addEventToFeed(matchTitle, message, eventType) {
            const feed = document.getElementById('events-feed');
            const eventDiv = document.createElement('div');

            const icon = getEventIcon(eventType);
            const timestamp = new Date().toLocaleTimeString();

            eventDiv.className = 'flex items-start space-x-3 p-3 bg-blue-50 rounded border-l-4 border-blue-500';
            eventDiv.innerHTML = `
                <div class="text-lg">${icon}</div>
                <div class="flex-1">
                    <div class="font-medium text-gray-800">${matchTitle}</div>
                    <div class="text-sm text-gray-600">${message}</div>
                </div>
                <div class="text-xs text-gray-500">${timestamp}</div>
            `;

            feed.insertBefore(eventDiv, feed.firstChild);

            // Keep only last 10 events
            while (feed.children.length > 10) {
                feed.removeChild(feed.lastChild);
            }

            // Animate new event
            eventDiv.style.opacity = '0';
            eventDiv.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                eventDiv.style.transition = 'all 0.3s ease';
                eventDiv.style.opacity = '1';
                eventDiv.style.transform = 'translateY(0)';
            }, 10);
        }

        // Get icon for event type
        function getEventIcon(eventType) {
            const icons = {
                'score_update': '⚽',
                'status_update': '📊',
                'time_update': '⏱️',
                'match_created': '🆕',
                'match_updated': '✏️',
                'connection': '🔗',
                'system': '🖥️',
                'error': '❌'
            };
            return icons[eventType] || '📝';
        }

        // Add initial event
        document.addEventListener('DOMContentLoaded', function() {
            addEventToFeed('System', 'Live scores page loaded - {{ $activeMatches->count() }} active matches',
                'system');
        });

        // Auto-refresh backup (every 60 seconds)
        setInterval(function() {
            addEventToFeed('System', 'Auto-refresh check completed', 'system');
        }, 60000);
    </script>
</body>

</html>
