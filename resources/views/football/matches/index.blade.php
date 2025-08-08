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
                        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-sm font-medium text-green-600">{{ $match->status_text }}</span>
                                <span class="text-sm text-gray-500">{{ $match->match_time }}'</span>
                            </div>
                            <div class="text-center">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $match->match_title }}</h3>
                                <div class="text-3xl font-bold text-blue-600 mb-3">{{ $match->formatted_score }}</div>
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
    <script>
        $(document).ready(function() {
            $('#matches-table').DataTable({
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
                        data: 'match_time',
                        name: 'match_time'
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
        });
    </script>
@endpush
