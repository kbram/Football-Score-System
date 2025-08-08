@extends('layouts.app')

@section('title', 'Create New Match')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Create New Football Match</h1>
                <p class="text-gray-600">Set up a new match for live score tracking</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <form action="{{ route('football.matches.store') }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <!-- Team Names -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="team_a" class="block text-sm font-medium text-gray-700 mb-2">Team A *</label>
                                <input type="text" id="team_a" name="team_a" value="{{ old('team_a') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('team_a') border-red-500 @enderror"
                                    placeholder="e.g., Manchester United" required>
                                @error('team_a')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="team_b" class="block text-sm font-medium text-gray-700 mb-2">Team B *</label>
                                <input type="text" id="team_b" name="team_b" value="{{ old('team_b') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('team_b') border-red-500 @enderror"
                                    placeholder="e.g., Liverpool FC" required>
                                @error('team_b')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Initial Scores (Optional) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="team_a_score" class="block text-sm font-medium text-gray-700 mb-2">Initial Score
                                    - Team A</label>
                                <input type="number" id="team_a_score" name="team_a_score"
                                    value="{{ old('team_a_score', 0) }}" min="0" max="50"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('team_a_score') border-red-500 @enderror">
                                @error('team_a_score')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="team_b_score" class="block text-sm font-medium text-gray-700 mb-2">Initial Score
                                    - Team B</label>
                                <input type="number" id="team_b_score" name="team_b_score"
                                    value="{{ old('team_b_score', 0) }}" min="0" max="50"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('team_b_score') border-red-500 @enderror">
                                @error('team_b_score')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Match Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Initial
                                    Status</label>
                                <select id="status" name="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                                    <option value="not_started" {{ old('status') == 'not_started' ? 'selected' : '' }}>Not
                                        Started</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In
                                        Progress</option>
                                    <option value="half_time" {{ old('status') == 'half_time' ? 'selected' : '' }}>Half
                                        Time</option>
                                    <option value="finished" {{ old('status') == 'finished' ? 'selected' : '' }}>Finished
                                    </option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="match_time" class="block text-sm font-medium text-gray-700 mb-2">Match Time
                                    (minutes)</label>
                                <input type="number" id="match_time" name="match_time" value="{{ old('match_time', 0) }}"
                                    min="0" max="180"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('match_time') border-red-500 @enderror">
                                @error('match_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description
                                (Optional)</label>
                            <textarea id="description" name="description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                placeholder="Match description, venue, or additional notes...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6">
                            <a href="{{ route('football.matches.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Cancel
                            </a>

                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Create Match
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Preview Card -->
            <div class="mt-8 bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Match Preview</h3>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-800" id="preview-team-a">Team A</div>
                            <div class="text-2xl font-bold text-blue-600" id="preview-score-a">0</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-gray-600">VS</div>
                            <div class="text-sm text-gray-500" id="preview-status">Not Started</div>
                            <div class="text-sm text-gray-500" id="preview-time">0'</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-800" id="preview-team-b">Team B</div>
                            <div class="text-2xl font-bold text-red-600" id="preview-score-b">0</div>
                        </div>
                    </div>
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
                $('#preview-score-a').text($('#team_a_score').val() || '0');
                $('#preview-score-b').text($('#team_b_score').val() || '0');
                $('#preview-time').text(($('#match_time').val() || '0') + "'");

                const statusText = $('#status option:selected').text();
                $('#preview-status').text(statusText);
            }

            // Update preview on input changes
            $('#team_a, #team_b, #team_a_score, #team_b_score, #match_time, #status').on('input change',
                updatePreview);

            // Initial preview update
            updatePreview();
        });
    </script>
@endpush
