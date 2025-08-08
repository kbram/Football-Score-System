<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Football Score - {{ $match->match_title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">🔴 LIVE FOOTBALL</h1>
            <p class="text-gray-600">Real-time score updates</p>
        </div>

        <!-- Live Match Card -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden" id="match-card">
                <!-- Match Header -->
                <div class="bg-gradient-to-r from-blue-600 to-green-600 text-white p-6">
                    <div class="flex justify-between items-center">
                        <div class="text-sm font-medium" id="match-status">{{ $match->status_text }}</div>
                        <div class="text-sm" id="match-time">
                            @php
                                $headerTime = $match->current_match_time ?? ($match->match_time ?? 0);
                            @endphp
                            {{ $headerTime }}'
                        </div>
                    </div>
                </div>

                <!-- Teams and Score -->
                <div class="p-8">
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <!-- Team A -->
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-800 mb-2" id="team-a-name">{{ $match->team_a }}
                            </div>
                            <div class="text-6xl font-bold text-blue-600" id="team-a-score">{{ $match->team_a_score }}
                            </div>
                        </div>

                        <!-- VS and Time -->
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-400 mb-2">VS</div>
                            <div class="text-lg text-gray-600" id="current-time">
                                @php
                                    $displayTime = $match->current_match_time ?? ($match->match_time ?? 0);
                                    if ($match->status === 'not_started' && $displayTime == 0) {
                                        $displayTime = 0;
                                    }
                                @endphp
                                {{ $displayTime }}'
                            </div>
                        </div>

                        <!-- Team B -->
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-800 mb-2" id="team-b-name">{{ $match->team_b }}
                            </div>
                            <div class="text-6xl font-bold text-red-600" id="team-b-score">{{ $match->team_b_score }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Match Info -->
                <div class="bg-gray-50 p-4">
                    <div class="text-center text-gray-600">
                        <div class="text-sm">Last Updated: <span
                                id="last-updated">{{ $match->updated_at->format('H:i:s') }}</span></div>
                        @if ($match->description)
                            <div class="text-sm mt-2">{{ $match->description }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Events Feed -->
            <div class="mt-8 bg-white rounded-lg shadow-lg">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Live Events</h3>
                </div>
                <div class="p-4">
                    <div id="events-feed" class="space-y-3 max-h-64 overflow-y-auto">
                        <!-- Events will be populated here via WebSocket -->
                    </div>
                </div>
            </div>

            <!-- Connection Status -->
            <div class="mt-4 text-center">
                <div id="connection-status" class="inline-flex items-center px-3 py-1 rounded-full text-sm">
                    <div class="w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse" id="status-indicator"></div>
                    <span id="status-text">Connecting...</span>
                </div>
            </div>

            <!-- Navigation -->
            <div class="mt-8 text-center space-x-4">
                <a href="{{ route('football.matches.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Back to Matches
                </a>
                <a href="{{ route('football.matches.control-panel', $match->id) }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Control Panel
                </a>
            </div>
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

        // Subscribe to match-specific channel
        const matchChannel = pusher.subscribe('football-match.{{ $match->id }}');

        // Global events channel
        const globalChannel = pusher.subscribe('football-matches');

        // Connection status handling
        pusher.connection.bind('connected', function() {
            updateConnectionStatus('connected', 'Connected', 'bg-green-500');
        });

        pusher.connection.bind('disconnected', function() {
            updateConnectionStatus('disconnected', 'Disconnected', 'bg-red-500');
        });

        pusher.connection.bind('connecting', function() {
            updateConnectionStatus('connecting', 'Connecting...', 'bg-yellow-500');
        });

        // Listen for score updates
        matchChannel.bind('score.updated', function(data) {
            console.log('Received update:', data);
            updateMatchDisplay(data.match);
            addEventToFeed(data);
        });

        // Update connection status indicator
        function updateConnectionStatus(status, text, colorClass) {
            const indicator = document.getElementById('status-indicator');
            const statusText = document.getElementById('status-text');
            const statusContainer = document.getElementById('connection-status');

            indicator.className = `w-2 h-2 rounded-full mr-2 ${colorClass}`;
            statusText.textContent = text;

            if (status === 'connected') {
                indicator.classList.remove('animate-pulse');
            } else {
                indicator.classList.add('animate-pulse');
            }
        }

        // Update match display
        function updateMatchDisplay(match) {
            document.getElementById('team-a-name').textContent = match.team_a;
            document.getElementById('team-b-name').textContent = match.team_b;
            document.getElementById('team-a-score').textContent = match.team_a_score;
            document.getElementById('team-b-score').textContent = match.team_b_score;
            document.getElementById('match-status').textContent = match.status_text;

            // Use current_match_time if available and valid, fallback to match_time, default to 0
            let displayTime = 0;
            if (match.current_match_time !== undefined && match.current_match_time !== null) {
                displayTime = match.current_match_time;
            } else if (match.match_time !== undefined && match.match_time !== null) {
                displayTime = match.match_time;
            }

            // Ensure displayTime is a valid number
            displayTime = parseInt(displayTime) || 0;

            document.getElementById('match-time').textContent = displayTime + "'";
            document.getElementById('current-time').textContent = displayTime + "'";

            document.getElementById('last-updated').textContent = new Date(match.updated_at).toLocaleTimeString();
        }

        // Add event to live feed
        function addEventToFeed(data) {
            const feed = document.getElementById('events-feed');
            const eventDiv = document.createElement('div');
            eventDiv.className = 'flex items-center justify-between p-3 bg-gray-50 rounded border-l-4 border-blue-500';

            const eventType = getEventTypeDisplay(data.event_type);
            const timestamp = new Date(data.timestamp).toLocaleTimeString();

            eventDiv.innerHTML = `
                <div>
                    <div class="font-medium text-gray-800">${eventType}</div>
                    <div class="text-sm text-gray-600">${data.event_data.message || 'Match updated'}</div>
                </div>
                <div class="text-sm text-gray-500">${timestamp}</div>
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

        // Get display text for event types
        function getEventTypeDisplay(eventType) {
            const types = {
                'score_update': '⚽ Goal Scored!',
                'status_update': '📊 Status Change',
                'time_update': '⏱️ Time Update',
                'timer_update': '⏱️ Timer Update',
                'match_created': '🆕 Match Created',
                'match_updated': '✏️ Match Updated'
            };
            return types[eventType] || '📝 Update';
        }

        // Refresh match data every 30 seconds as backup
        setInterval(function() {
            fetch(`/football/matches/{{ $match->id }}/live-data`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateMatchDisplay(data.match);
                    }
                })
                .catch(error => console.error('Error fetching match data:', error));
        }, 30000);

        // Add initial event
        addEventToFeed({
            event_type: 'match_started',
            event_data: {
                message: 'Live coverage started'
            },
            timestamp: new Date().toISOString()
        });
    </script>
</body>

</html>
