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
                <div class="overflow-x-auto">
                    <table id="matches-table" class="w-full table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Teams</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Score</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Time</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- DataTable will populate this -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* DataTable Styling */
        .dataTables_wrapper {
            margin-top: 0;
            font-family: inherit;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_length {
            float: left;
        }

        .dataTables_wrapper .dataTables_filter {
            float: right;
        }

        .dataTables_wrapper .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        .dataTables_wrapper .dataTables_filter input {
            @apply border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm;
            min-width: 250px;
        }

        .dataTables_wrapper .dataTables_length label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        .dataTables_wrapper .dataTables_length select {
            @apply border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm;
        }

        .dataTables_wrapper .dataTables_info {
            float: left;
            padding-top: 0.75rem;
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .dataTables_wrapper .dataTables_processing {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 200px;
            margin-left: -100px;
            margin-top: -26px;
            text-align: center;
            padding: 1rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .dataTables_wrapper .dataTables_paginate {
            float: right;
            padding-top: 0.5rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            @apply inline-flex items-center justify-center text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500;
            margin-left: 0.375rem;
            margin-right: 0.375rem;
            text-decoration: none !important;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            min-width: 2.75rem;
            height: 2.5rem;
            padding: 0.5rem 1rem !important;
            position: relative;
            background-image: linear-gradient(to bottom, #ffffff, #f8fafc);
            display: inline-flex;
            text-align: center;
            vertical-align: middle;
            line-height: 1;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -2px rgba(0, 0, 0, 0.15), 0 4px 6px -1px rgba(0, 0, 0, 0.08);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.08);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:first-child {
            margin-left: 0;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:last-child {
            margin-right: 0;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            @apply bg-blue-600 text-white border-blue-600;
            background-image: linear-gradient(to bottom, #3b82f6, #2563eb);
            box-shadow: 0 2px 6px 0 rgba(59, 130, 246, 0.3), 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            @apply bg-blue-700 border-blue-700;
            background-image: linear-gradient(to bottom, #2563eb, #1d4ed8);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(59, 130, 246, 0.4), 0 4px 8px -2px rgba(0, 0, 0, 0.1);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            @apply text-gray-400 bg-gray-50 border-gray-200 cursor-not-allowed;
            background-image: none;
            opacity: 0.6;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            @apply text-gray-400 bg-gray-50 border-gray-200;
            transform: none;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        /* Special styling for previous/next buttons - make them more prominent */
        .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next {
            @apply font-bold bg-gradient-to-r from-blue-500 to-blue-600 text-white border-blue-600;
            background-image: linear-gradient(to right, #3b82f6, #2563eb);
            min-width: auto;
            margin-left: 0.5rem;
            margin-right: 0.5rem;
            padding: 0.625rem 1.5rem !important;
            height: 2.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-shadow: 0 2px 6px 0 rgba(59, 130, 246, 0.3), 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.previous:hover,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next:hover {
            @apply bg-blue-700 border-blue-700 text-white;
            background-image: linear-gradient(to right, #2563eb, #1d4ed8);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(59, 130, 246, 0.4), 0 4px 8px -2px rgba(0, 0, 0, 0.1);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.previous.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next.disabled {
            @apply bg-gray-300 border-gray-300 text-gray-500;
            background-image: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.previous.disabled:hover,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next.disabled:hover {
            @apply bg-gray-300 border-gray-300 text-gray-500;
            transform: none;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        /* Table styling */
        #matches-table {
            border-collapse: separate;
            border-spacing: 0;
        }

        #matches-table td {
            @apply px-4 py-4 text-sm text-gray-900;
            border-bottom: 1px solid #e5e7eb;
        }

        #matches-table tbody tr:hover {
            @apply bg-gray-50;
        }

        /* Clear fix for DataTable wrapper */
        .dataTables_wrapper:after {
            content: "";
            display: table;
            clear: both;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin-bottom: 1rem;
        }

        /* Pagination container styling */
        .dataTables_wrapper .dataTables_paginate {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
        }

        /* Responsive improvements */
        @media (max-width: 640px) {

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                float: none;
                text-align: center;
                margin-bottom: 1rem;
            }

            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
            }

            .dataTables_wrapper .dataTables_paginate {
                flex-wrap: wrap;
                gap: 0.5rem;
                padding: 0.75rem;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button {
                margin: 0.125rem;
                min-width: 2.25rem;
                height: 2.25rem;
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
            .dataTables_wrapper .dataTables_paginate .paginate_button.next {
                padding: 0.375rem 0.75rem;
                font-size: 0.75rem;
                margin: 0.25rem;
            }
        }
    </style>
@endpush

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
                        name: 'teams',
                        className: 'font-medium text-gray-900'
                    },
                    {
                        data: 'score',
                        name: 'score',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center'
                    },
                    {
                        data: 'current_time',
                        name: 'current_time',
                        className: 'text-center font-mono'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        className: 'text-sm text-gray-500'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [4, 'desc']
                ],
                responsive: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                language: {
                    search: "Search matches:",
                    lengthMenu: "Show _MENU_ matches per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ matches",
                    infoEmpty: "No matches found",
                    infoFiltered: "(filtered from _MAX_ total matches)",
                    zeroRecords: "No matching matches found",
                    emptyTable: "No matches available",
                    processing: "Loading matches...",
                    paginate: {
                        first: "« First",
                        last: "Last »",
                        next: "Next »",
                        previous: "« Previous"
                    }
                },
                dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6"<"mb-2 sm:mb-0"l><"mb-2 sm:mb-0"f>>rt<"flex flex-col sm:flex-row sm:items-center sm:justify-between mt-6"<"mb-2 sm:mb-0"i><"mb-2 sm:mb-0"p>>'
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
