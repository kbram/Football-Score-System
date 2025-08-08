@extends('layouts.app')

@section('title', 'Match Control Panel - ' . $match->match_title)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">⚽ Match Control Panel</h1>
                <h2 class="text-xl text-gray-600">{{ $match->match_title }}</h2>
                @if (auth()->user()->isAdmin())
                    <div class="mt-2">
                        <span class="inline-block bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                            🔧 Admin Controls Active
                        </span>
                    </div>
                @else
                    <div class="mt-2">
                        <span class="inline-block bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full">
                            👁️ View Only Mode
                        </span>
                    </div>
                @endif
            </div>

            <!-- Current Match Status -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ $match->team_a }}</h3>
                        <div class="text-4xl font-bold text-blue-600" id="current-score-a">{{ $match->team_a_score }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-600 mb-1">Status</div>
                        <div class="text-lg font-semibold" id="current-status">{{ $match->status_text }}</div>
                        <div class="text-sm text-gray-600 mt-2">Time: <span
                                id="current-time">{{ $match->match_time }}'</span></div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ $match->team_b }}</h3>
                        <div class="text-4xl font-bold text-red-600" id="current-score-b">{{ $match->team_b_score }}</div>
                    </div>
                </div>
            </div>

            @if (auth()->user()->isAdmin())
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Score Control -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Score Control</h3>

                        <!-- Manual Score Update -->
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-700 mb-3">Update Score</h4>
                            <form id="score-form" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $match->team_a }}
                                            Score</label>
                                        <input type="number" id="team_a_score" name="team_a_score" min="0"
                                            max="50" value="{{ $match->team_a_score }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $match->team_b }}
                                            Score</label>
                                        <input type="number" id="team_b_score" name="team_b_score" min="0"
                                            max="50" value="{{ $match->team_b_score }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                                <button type="submit"
                                    class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Update Score
                                </button>
                            </form>
                        </div>

                        <!-- Quick Goal Buttons -->
                        <div class="border-t pt-6">
                            <h4 class="text-lg font-medium text-gray-700 mb-3">Quick Goal</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <button id="goal-team-a"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded">
                                    ⚽ Goal for {{ $match->team_a }}
                                </button>
                                <button id="goal-team-b"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded">
                                    ⚽ Goal for {{ $match->team_b }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Match Control -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Match Control</h3>

                        <!-- Quick Start Match Button -->
                        @if ($match->status === 'not_started')
                            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <h4 class="text-lg font-medium text-green-800 mb-3">🏁 Ready to Start?</h4>
                                <button
                                    class="start-match-btn w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg text-lg transition-colors"
                                    data-status="in_progress">
                                    ⚽ START MATCH
                                </button>
                            </div>
                        @endif

                        <!-- Status Control -->
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-700 mb-3">Match Status</h4>
                            <div class="space-y-2">
                                <button class="status-btn w-full" data-status="not_started">
                                    <span class="flex items-center justify-center">
                                        <span class="mr-2">⏳</span> Not Started
                                    </span>
                                </button>
                                <button class="status-btn w-full" data-status="in_progress">
                                    <span class="flex items-center justify-center">
                                        <span class="mr-2">⚽</span> In Progress
                                    </span>
                                </button>
                                <button class="status-btn w-full" data-status="half_time">
                                    <span class="flex items-center justify-center">
                                        <span class="mr-2">⏸️</span> Half Time
                                    </span>
                                </button>
                                <button class="status-btn w-full" data-status="finished">
                                    <span class="flex items-center justify-center">
                                        <span class="mr-2">🏁</span> Finished
                                    </span>
                                </button>
                            </div>
                            <div class="mt-3 text-sm text-gray-600">
                                Current Status: <span id="status-indicator"
                                    class="font-semibold">{{ $match->status_text }}</span>
                            </div>
                        </div>

                        <!-- Time Control -->
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-700 mb-3">Match Time</h4>
                            <form id="time-form" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Minutes</label>
                                    <input type="number" id="match_time" name="match_time" min="0" max="180"
                                        value="{{ $match->match_time }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <button type="submit"
                                    class="w-full bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                                    Update Time
                                </button>
                            </form>
                        </div>

                        <!-- Quick Time Buttons -->
                        <div class="border-t pt-6">
                            <h4 class="text-lg font-medium text-gray-700 mb-3">Quick Time</h4>
                            <div class="grid grid-cols-3 gap-2">
                                <button class="time-btn" data-time="0">
                                    <span class="flex items-center justify-center">
                                        <span class="mr-1">🏁</span> Start (0')
                                    </span>
                                </button>
                                <button class="time-btn" data-time="45">
                                    <span class="flex items-center justify-center">
                                        <span class="mr-1">⏸️</span> Half (45')
                                    </span>
                                </button>
                                <button class="time-btn" data-time="90">
                                    <span class="flex items-center justify-center">
                                        <span class="mr-1">🏆</span> Full (90')
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- View Only Mode for Non-Admin Users -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="text-center">
                        <div class="mb-4">
                            <span class="text-6xl">🔒</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">View Only Access</h3>
                        <p class="text-gray-600 mb-4">You can view the match status and scores, but admin privileges are
                            required to make changes.</p>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600">To request admin access, please contact your system
                                administrator.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Live Feed -->
            <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Live Updates</h3>
                <div id="control-feed" class="space-y-3 max-h-64 overflow-y-auto">
                    <!-- Updates will appear here -->
                </div>
            </div>

            <!-- Navigation -->
            <div class="mt-8 text-center space-x-4">
                <a href="{{ route('football.matches.index') }}"
                    class="back-btn bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Back to Matches
                </a>
                <a href="{{ route('football.matches.live', $match->id) }}"
                    class="view-live-btn bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                    target="_blank">
                    View Live Score
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .status-btn,
        .time-btn {
            @apply bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg text-sm transition-all border-2 border-transparent;
        }

        .status-btn.active {
            @apply bg-blue-500 hover:bg-blue-600 text-white border-blue-700 shadow-lg transform scale-105;
        }

        .start-match-btn {
            @apply shadow-lg transform transition-transform hover:scale-105;
        }

        .status-btn:hover {
            @apply shadow-md transform scale-102;
        }

        .time-btn.active {
            @apply bg-orange-500 text-white border-orange-700;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        $(document).ready(function() {
            const matchId = {{ $match->id }};
            const isAdmin = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
            let clientTimer = null;
            let isTimerRunning = {{ $match->timer_running ? 'true' : 'false' }};
            let currentMatchTime = {{ $match->current_match_time }};
            let lastUpdateTime = Date.now();

            // Start client-side timer if match is running
            if (isTimerRunning && '{{ $match->status }}' === 'in_progress') {
                startClientTimer();
            }

            // WebSocket setup
            const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
                cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
                encrypted: true,
                wsHost: '{{ config('broadcasting.connections.pusher.options.host') }}',
                wsPort: '{{ config('broadcasting.connections.pusher.options.port') }}',
                forceTLS: false,
                enabledTransports: ['ws', 'wss']
            });

            const channel = pusher.subscribe('football-match.' + matchId);

            channel.bind('score.updated', function(data) {
                console.log('WebSocket event received:', data);

                // Handle different event types
                if (data.event_type === 'timer_update') {
                    // Timer update - update only the time display
                    if (data.match && data.match.current_match_time !== undefined) {
                        const newTime = data.match.current_match_time;
                        currentMatchTime = newTime;
                        $('#current-time').text(newTime + "'");
                        $('#match_time').val(newTime);

                        // Add timer update to feed
                        addToControlFeed({
                            event_data: {
                                message: `⏱️ Timer: ${newTime} minutes`
                            },
                            timestamp: data.timestamp
                        });
                    }
                } else {
                    // Other updates - full display update
                    updateDisplayFromWebSocket(data.match);
                    addToControlFeed(data);
                }
            });

            // Update current status display
            updateStatusButtons('{{ $match->status }}');

            // Admin-only event handlers
            if (isAdmin) {
                // Score form submission
                $('#score-form').on('submit', function(e) {
                    e.preventDefault();
                    updateScore();
                });

                // Time form submission
                $('#time-form').on('submit', function(e) {
                    e.preventDefault();
                    updateTime();
                });

                // Quick goal buttons
                $('#goal-team-a').on('click', function() {
                    simulateGoal('team_a');
                });

                $('#goal-team-b').on('click', function() {
                    simulateGoal('team_b');
                });

                // Status buttons (including start match button)
                $('.status-btn, .start-match-btn').on('click', function() {
                    const status = $(this).data('status');
                    const buttonText = $(this).text().trim();

                    // Confirm for important status changes
                    if (status === 'finished') {
                        if (!confirm(
                                'Are you sure you want to finish this match? This action cannot be undone.'
                                )) {
                            return;
                        }
                    } else if (status === 'in_progress' && $(this).hasClass('start-match-btn')) {
                        if (!confirm('Ready to start the match? The match time will be set to 0.')) {
                            return;
                        }
                        // Also set time to 0 when starting match
                        $('#match_time').val(0);
                        updateTime();
                    }

                    updateStatus(status, buttonText);
                });

                // Quick time buttons
                $('.time-btn').on('click', function() {
                    const time = $(this).data('time');
                    $('#match_time').val(time);
                    updateTime();
                });
            } else {
                // Non-admin users: disable all form controls and show messages when clicked
                $('input, button, form').not('.back-btn, .view-live-btn').on('click focus', function(e) {
                    e.preventDefault();
                    showNotification('Admin privileges required to make changes', 'error');
                    $(this).blur(); // Remove focus
                });
            }

            // Functions
            function updateScore() {
                if (!isAdmin) {
                    showNotification('Admin privileges required', 'error');
                    return;
                }

                const data = {
                    team_a_score: $('#team_a_score').val(),
                    team_b_score: $('#team_b_score').val(),
                    _token: $('input[name="_token"]').val()
                };

                $.ajax({
                    url: `/football/matches/${matchId}/update-score`,
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            showNotification('Score updated successfully!', 'success');
                        } else {
                            showNotification('Failed to update score', 'error');
                        }
                    },
                    error: function() {
                        showNotification('Error updating score', 'error');
                    }
                });
            }

            function updateStatus(status, buttonText = '') {
                if (!isAdmin) {
                    showNotification('Admin privileges required', 'error');
                    return;
                }

                // Show loading state
                const statusIndicator = $('#status-indicator');
                statusIndicator.text('Updating...');

                $.ajax({
                    url: `/football/matches/${matchId}/update-status`,
                    method: 'POST',
                    data: {
                        status: status,
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            updateStatusButtons(status);
                            updateStatusIndicator(status);

                            // Handle timer based on status
                            if (status === 'in_progress') {
                                currentMatchTime = response.match.current_match_time || 0;
                                resumeClientTimer();
                                $('#current-time').text(currentMatchTime + "'");
                                $('#match_time').val(currentMatchTime);
                            } else {
                                stopClientTimer();
                                if (response.match.match_time !== undefined) {
                                    currentMatchTime = response.match.match_time;
                                    $('#current-time').text(currentMatchTime + "'");
                                    $('#match_time').val(currentMatchTime);
                                }
                            }

                            // Show appropriate message
                            let message = buttonText ? `${buttonText} - Status updated!` :
                                'Status updated successfully!';
                            if (status === 'in_progress' && buttonText.includes('START')) {
                                message = '🏁 Match Started! Timer is now running automatically!';
                            } else if (status === 'half_time') {
                                message = '⏸️ Half Time - Timer paused';
                            } else if (status === 'finished') {
                                message = '🏁 Match Finished - Final time recorded';
                            }

                            showNotification(message, 'success');

                            // Hide start button if match is started
                            if (status === 'in_progress') {
                                $('.start-match-btn').closest('.mb-6').slideUp();
                            }
                        } else {
                            showNotification('Failed to update status', 'error');
                            statusIndicator.text('Error updating status');
                        }
                    },
                    error: function() {
                        showNotification('Error updating status', 'error');
                        statusIndicator.text('Error updating status');
                    }
                });
            }

            function updateTime() {
                if (!isAdmin) {
                    showNotification('Admin privileges required', 'error');
                    return;
                }

                const time = $('#match_time').val();

                $.ajax({
                    url: `/football/matches/${matchId}/update-time`,
                    method: 'POST',
                    data: {
                        match_time: time,
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotification('Time updated successfully!', 'success');
                        } else {
                            showNotification('Failed to update time', 'error');
                        }
                    },
                    error: function() {
                        showNotification('Error updating time', 'error');
                    }
                });
            }

            function simulateGoal(team) {
                if (!isAdmin) {
                    showNotification('Admin privileges required', 'error');
                    return;
                }

                $.ajax({
                    url: `/football/matches/${matchId}/simulate-goal`,
                    method: 'POST',
                    data: {
                        team: team,
                        scorer: 'Player',
                        goal_time: $('#match_time').val(),
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotification('Goal added!', 'success');
                        } else {
                            showNotification('Failed to add goal', 'error');
                        }
                    },
                    error: function() {
                        showNotification('Error adding goal', 'error');
                    }
                });
            }

            function updateDisplayFromWebSocket(match) {
                $('#current-score-a').text(match.team_a_score);
                $('#current-score-b').text(match.team_b_score);
                $('#current-status').text(match.status_text);
                $('#team_a_score').val(match.team_a_score);
                $('#team_b_score').val(match.team_b_score);
                updateStatusButtons(match.status);
                updateStatusIndicator(match.status);

                // Update timer state - use current_match_time if available, fallback to match_time
                const displayTime = match.current_match_time !== undefined ? match.current_match_time : match
                    .match_time;
                currentMatchTime = displayTime;
                $('#current-time').text(displayTime + "'");
                $('#match_time').val(displayTime);

                // Handle timer based on status
                if (match.status === 'in_progress' && match.timer_running) {
                    if (!isTimerRunning) {
                        resumeClientTimer();
                    }
                } else {
                    stopClientTimer();
                }

                // Hide start button if match is not in "not_started" status
                if (match.status !== 'not_started') {
                    $('.start-match-btn').closest('.mb-6').hide();
                } else {
                    $('.start-match-btn').closest('.mb-6').show();
                }
            }

            function updateStatusButtons(currentStatus) {
                $('.status-btn').removeClass('active');
                $(`.status-btn[data-status="${currentStatus}"]`).addClass('active');
            }

            function updateStatusIndicator(status) {
                const statusTexts = {
                    'not_started': '⏳ Not Started',
                    'in_progress': '⚽ In Progress',
                    'half_time': '⏸️ Half Time',
                    'finished': '🏁 Finished'
                };

                $('#status-indicator').text(statusTexts[status] || status);
            }

            function startClientTimer() {
                if (clientTimer) clearInterval(clientTimer);

                let secondsInCurrentMinute = 0;

                clientTimer = setInterval(function() {
                    if (isTimerRunning) {
                        secondsInCurrentMinute++;

                        // Update display every minute (60 seconds)
                        if (secondsInCurrentMinute >= 60) {
                            currentMatchTime++;
                            $('#current-time').text(currentMatchTime + "'");
                            $('#match_time').val(currentMatchTime);
                            secondsInCurrentMinute = 0; // Reset seconds counter
                        }
                    }
                }, 1000); // Update every second for smooth operation
            }

            function stopClientTimer() {
                if (clientTimer) {
                    clearInterval(clientTimer);
                    clientTimer = null;
                }
                isTimerRunning = false;
            }

            function resumeClientTimer() {
                isTimerRunning = true;
                startClientTimer();
            }

            function addToControlFeed(data) {
                const feed = $('#control-feed');
                const timestamp = new Date(data.timestamp).toLocaleTimeString();
                const message = data.event_data.message || 'Match updated';

                const feedItem = `
            <div class="flex items-center justify-between p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                <div class="text-sm text-gray-800">${message}</div>
                <div class="text-xs text-gray-500">${timestamp}</div>
            </div>
        `;

                feed.prepend(feedItem);

                // Keep only last 10 items
                feed.children().slice(10).remove();
            }

            function showNotification(message, type) {
                const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
                const notification = $(`
            <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded shadow-lg z-50">
                ${message}
            </div>
        `);

                $('body').append(notification);

                setTimeout(() => {
                    notification.fadeOut(() => notification.remove());
                }, 3000);
            }
        });
    </script>
@endpush
