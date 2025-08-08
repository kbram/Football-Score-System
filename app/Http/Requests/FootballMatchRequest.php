<?php

namespace App\Http\Requests;

use App\Models\FootballMatch;
use Illuminate\Foundation\Http\FormRequest;

class FootballMatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $matchId = $this->route('id') ?? $this->route('match');
        
        return [
            'team_a' => 'required|string|max:255',
            'team_b' => 'required|string|max:255|different:team_a',
            'team_a_score' => 'nullable|integer|min:0|max:50',
            'team_b_score' => 'nullable|integer|min:0|max:50',
            'status' => 'nullable|in:' . implode(',', [
                FootballMatch::STATUS_NOT_STARTED,
                FootballMatch::STATUS_IN_PROGRESS,
                FootballMatch::STATUS_HALF_TIME,
                FootballMatch::STATUS_FINISHED
            ]),
            'match_time' => 'nullable|integer|min:0|max:180',
            'description' => 'nullable|string|max:1000'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'team_a.required' => 'Team A name is required.',
            'team_b.required' => 'Team B name is required.',
            'team_b.different' => 'Team B must be different from Team A.',
            'team_a_score.integer' => 'Team A score must be a valid number.',
            'team_b_score.integer' => 'Team B score must be a valid number.',
            'team_a_score.min' => 'Team A score cannot be negative.',
            'team_b_score.min' => 'Team B score cannot be negative.',
            'team_a_score.max' => 'Team A score seems unrealistic (maximum 50).',
            'team_b_score.max' => 'Team B score seems unrealistic (maximum 50).',
            'match_time.min' => 'Match time cannot be negative.',
            'match_time.max' => 'Match time cannot exceed 180 minutes.',
            'status.in' => 'Invalid match status selected.',
            'description.max' => 'Description cannot exceed 1000 characters.'
        ];
    }

    /**
     * Get custom attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'team_a' => 'Team A',
            'team_b' => 'Team B',
            'team_a_score' => 'Team A Score',
            'team_b_score' => 'Team B Score',
            'match_time' => 'Match Time',
            'description' => 'Description'
        ];
    }
}
