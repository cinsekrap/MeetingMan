<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('people.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; People</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span class="hidden sm:inline">Objectives for</span> {{ $person->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Navigation tabs --}}
            <div class="mb-4 border-b border-gray-200 overflow-x-auto">
                <nav class="flex space-x-4 sm:space-x-8 min-w-max">
                    <a href="{{ route('people.meetings.index', $person) }}" class="py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                        Meetings
                    </a>
                    <a href="{{ route('people.actions.index', $person) }}" class="py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                        Actions
                    </a>
                    <a href="{{ route('people.objectives.index', $person) }}" class="py-2 px-1 text-sm font-medium text-primary-600 border-b-2 border-primary-500">
                        Objectives
                    </a>
                </nav>
            </div>

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Add new objective --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Objective</h3>
                    <form method="POST" action="{{ route('people.objectives.store', $person) }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-2">
                                <x-input-label for="definition" :value="__('Objective')" />
                                <x-text-input id="definition" class="block mt-1 w-full" type="text" name="definition" :value="old('definition')" required placeholder="What is the objective?" />
                                <x-input-error :messages="$errors->get('definition')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="start_date" :value="__('Start Date')" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', now()->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="due_date" :value="__('Due Date')" />
                                <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date', now()->addMonths(3)->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <x-primary-button>Add Objective</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Existing objectives --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($objectives->isEmpty())
                        <p class="text-gray-500">No objectives yet. Add one above.</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($objectives as $objective)
                                <div class="p-4 border rounded-lg {{ in_array($objective->status->value, ['complete', 'dropped']) ? 'opacity-60 bg-gray-50' : '' }}" id="objective-{{ $objective->id }}">
                                    <form method="POST" action="{{ route('objectives.update', $objective) }}" class="objective-form">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                                            <div class="md:col-span-2">
                                                <x-input-label :value="__('Objective')" />
                                                <x-text-input class="block mt-1 w-full" type="text" name="definition" :value="$objective->definition" required />
                                            </div>
                                            <div>
                                                <x-input-label :value="__('Start')" />
                                                <x-text-input class="block mt-1 w-full" type="date" name="start_date" :value="$objective->start_date->format('Y-m-d')" required />
                                            </div>
                                            <div>
                                                <x-input-label :value="__('Due')" />
                                                <x-text-input class="block mt-1 w-full" type="date" name="due_date" :value="$objective->due_date->format('Y-m-d')" required />
                                            </div>
                                            <div>
                                                <x-input-label :value="__('Status')" />
                                                <select name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500
                                                    {{ $objective->status->value === 'complete' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $objective->status->value === 'on_track' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $objective->status->value === 'off_track' ? 'bg-red-100 text-red-800' : '' }}
                                                    {{ $objective->status->value === 'dropped' ? 'bg-gray-100 text-gray-400' : '' }}">
                                                    @foreach ($statuses as $status)
                                                        <option value="{{ $status->value }}" {{ $objective->status->value === $status->value ? 'selected' : '' }}>
                                                            {{ $status->label() }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="flex justify-between mt-4">
                                            <button type="button" onclick="deleteObjective({{ $objective->id }})" class="text-sm text-red-600 hover:text-red-800">
                                                Delete
                                            </button>
                                            <x-primary-button>Save Changes</x-primary-button>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden delete forms --}}
    @foreach ($objectives as $objective)
        <form id="delete-form-{{ $objective->id }}" method="POST" action="{{ route('objectives.destroy', $objective) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    <script>
        function deleteObjective(objectiveId) {
            if (confirm('Are you sure you want to delete this objective?')) {
                document.getElementById(`delete-form-${objectiveId}`).submit();
            }
        }
    </script>
</x-app-layout>
