<div class="flex justify-center items-center space-x-2">
    <a href="{{ route('football.matches.show', $match->id) }}"
        class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-2 rounded" title="View Match">
        👁️
    </a>

    <a href="{{ route('football.matches.live', $match->id) }}"
        class="bg-green-500 hover:bg-green-700 text-white text-xs font-bold py-1 px-2 rounded" target="_blank"
        title="Live Score">
        🔴
    </a>

    @if (auth()->user()->isAdmin())
        <a href="{{ route('football.matches.control-panel', $match->id) }}"
            class="bg-orange-500 hover:bg-orange-700 text-white text-xs font-bold py-1 px-2 rounded"
            title="Control Panel">
            ⚙️
        </a>

        <a href="{{ route('football.matches.edit', $match->id) }}"
            class="bg-yellow-500 hover:bg-yellow-700 text-white text-xs font-bold py-1 px-2 rounded" title="Edit Match">
            ✏️
        </a>

        <form method="POST" action="{{ route('football.matches.destroy', $match->id) }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-2 rounded"
                onclick="return confirm('Are you sure you want to delete this match?')" title="Delete Match">
                🗑️
            </button>
        </form>
    @endif
</div>
