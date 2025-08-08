@extends('layouts.app')

@section('title', 'Edit Match - ' . $match->match_title)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Edit Football Match</h1>
                <p class="text-gray-600">Update match details for {{ $match->match_title }}</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <form action="{{ route('football.matches.update', $match->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Team Names -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="team_a" class="block text-sm font-medium text-gray-700 mb-2">Team A *</label>
                                <input type="text" id="team_a" name="team_a"
                                    value="{{ old('team_a', $match->team_a) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('team_a') border-red-500 @enderror"
                                    placeholder="e.g., Manchester United" required>
                                @error('team_a')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="team_b" class="block text-sm font-medium text-gray-700 mb-2">Team B *</label>
                                <input type="text" id="team_b" name="team_b"
                                    value="{{ old('team_b', $match->team_b) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('team_b') border-red-500 @enderror"
                                    placeholder="e.g., Liverpool FC" required>
                                @error('team_b')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Scores (Read-only display) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Score -
                                    {{ $match->team_a }}</label>
                                <div
                                    class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-center text-2xl font-bold text-blue-600">
                                    {{ $match->team_a_score }}
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Use Control Panel to update scores in real-time</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Score -
                                    {{ $match->team_b }}</label>
                                <div
                                    class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-center text-2xl font-bold text-red-600">
                                    {{ $match->team_b_score }}
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Use Control Panel to update scores in real-time</p>
                            </div>
                        </div>

                        <!-- Current Status and Time (Read-only display) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Status</label>
                                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                                    <span class="font-medium">{{ $match->status_text }}</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Use Control Panel to change status</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Match Time</label>
                                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-center">
                                    <span class="font-bold">{{ $match->match_time }}'</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Use Control Panel to update time</p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" name="description" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                placeholder="Match description, venue, or additional notes...">{{ old('description', $match->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6">
                            <a href="{{ route('football.matches.show', $match->id) }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Cancel
                            </a>

                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update Match
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-4">⚠️ Note about Live Match Updates</h3>
                <p class="text-yellow-700 mb-4">
                    This form only updates basic match information (team names and description).
                    For real-time updates during the match, use the dedicated Control Panel.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('football.matches.control-panel', $match->id) }}"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                        🎮 Open Control Panel
                    </a>
                    <a href="{{ route('football.matches.live', $match->id) }}"
                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded" target="_blank">
                        🔴 Watch Live
                    </a>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="mt-8 bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Match Preview</h3>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-800" id="preview-team-a">{{ $match->team_a }}</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $match->team_a_score }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-gray-600">VS</div>
                            <div class="text-sm text-gray-500">{{ $match->status_text }}</div>
                            <div class="text-sm text-gray-500">{{ $match->match_time }}'</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-800" id="preview-team-b">{{ $match->team_b }}</div>
                            <div class="text-2xl font-bold text-red-600">{{ $match->team_b_score }}</div>
                        </div>
                    </div>
                    @if ($match->description)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600" id="preview-description">{{ $match->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Live preview
            function updatePreview() {
                $('#preview-team-a').text($('#team_a').val() || 'Team A');
                $('#preview-team-b').text($('#team_b').val() || 'Team B');

                const description = $('#description').val();
                if (description) {
                    $('#preview-description').text(description).parent().show();
                } else {
                    $('#preview-description').parent().hide();
                }
            }

            // Update preview on input changes
            $('#team_a, #team_b, #description').on('input', updatePreview);

            // Initial preview update
            updatePreview();
        });
    </script>
@endpush
