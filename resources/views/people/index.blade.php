<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('People') }}
            </h2>
            <a href="{{ route('people.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-primary-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-600 focus:bg-primary-600 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Add Person
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($people->isEmpty())
                        <p class="text-gray-500">No people yet. <a href="{{ route('people.create') }}" class="text-primary-600 hover:text-primary-800">Add your first team member</a>.</p>
                    @else
                        {{-- Search input --}}
                        <div class="mb-4">
                            <input
                                type="text"
                                id="people-search"
                                placeholder="Filter people..."
                                class="w-full sm:w-64 border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm"
                                autocomplete="off"
                            >
                        </div>

                        <div id="people-list" class="space-y-3 sm:space-y-4">
                            @foreach ($people as $person)
                                <div class="person-row p-3 sm:p-4 border rounded-lg hover:bg-gray-50" data-name="{{ strtolower($person->name) }}" data-email="{{ strtolower($person->email ?? '') }}">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <a href="{{ route('people.meetings.index', $person) }}" class="flex-1 min-w-0">
                                            <div class="font-medium text-gray-900 hover:text-primary-600 truncate">
                                                {{ $person->name }}
                                            </div>
                                            @if ($person->email)
                                                <p class="text-sm text-gray-500 truncate">{{ $person->email }}</p>
                                            @endif
                                            <p class="text-sm text-gray-400">{{ $person->meetings()->count() }} meeting(s)</p>
                                        </a>
                                        <div class="flex items-center space-x-3 text-sm">
                                            <a href="{{ route('people.meetings.index', $person) }}" class="text-primary-600 hover:text-primary-800">Meetings</a>
                                            <a href="{{ route('people.edit', $person) }}" class="text-gray-600 hover:text-gray-800">Edit</a>
                                            <form action="{{ route('people.archive', $person) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-gray-600 hover:text-gray-800">Archive</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <p id="no-results" class="text-gray-500 hidden">No people match your search.</p>
                    @endif
                </div>
            </div>

            <div class="mt-4 text-sm">
                <a href="{{ route('people.archived') }}" class="text-gray-500 hover:text-gray-700">View archived people</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('people-search');
            const peopleList = document.getElementById('people-list');
            const noResults = document.getElementById('no-results');

            if (!searchInput || !peopleList) return;

            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const rows = peopleList.querySelectorAll('.person-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const name = row.dataset.name || '';
                    const email = row.dataset.email || '';

                    if (name.includes(query) || email.includes(query)) {
                        row.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        row.classList.add('hidden');
                    }
                });

                if (noResults) {
                    noResults.classList.toggle('hidden', visibleCount > 0);
                }
            });
        });
    </script>
</x-app-layout>
