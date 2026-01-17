<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Dashboard</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $title }}
                </h2>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('objectives.index') }}" class="text-sm px-3 py-1 rounded-full {{ !$filter ? 'bg-primary-100 text-primary-800' : 'text-gray-600 hover:bg-gray-100' }}">All</a>
                <a href="{{ route('objectives.index', ['filter' => 'off_track']) }}" class="text-sm px-3 py-1 rounded-full {{ $filter === 'off_track' ? 'bg-orange-100 text-orange-800' : 'text-gray-600 hover:bg-gray-100' }}">Off-Track</a>
                <a href="{{ route('objectives.index', ['filter' => 'active']) }}" class="text-sm px-3 py-1 rounded-full {{ $filter === 'active' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:bg-gray-100' }}">Active</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($objectives->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-500">
                        @if ($filter === 'off_track')
                            No off-track objectives. Everything is on track!
                        @else
                            No objectives found.
                        @endif
                    </p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($objectives as $personName => $personObjectives)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-4 bg-gray-50 border-b">
                                <h3 class="font-semibold text-gray-900">{{ $personName }}</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    @foreach ($personObjectives as $objective)
                                        <div class="flex items-start justify-between p-4 border rounded-lg {{ $objective->status->value === 'off_track' ? 'border-orange-300 bg-orange-50' : '' }} {{ in_array($objective->status->value, ['complete', 'dropped']) ? 'opacity-60' : '' }}">
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-900 {{ $objective->status->value === 'complete' ? 'line-through' : '' }}">
                                                    {{ $objective->definition }}
                                                </div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    <span>Started: {{ $objective->start_date->format('j M Y') }}</span>
                                                    <span class="mx-2">|</span>
                                                    <span>Due: {{ $objective->due_date->format('j M Y') }}</span>
                                                    <span class="mx-2">|</span>
                                                    <a href="{{ route('people.objectives.index', $objective->person) }}" class="text-primary-600 hover:text-primary-800">
                                                        Manage
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <select
                                                    onchange="updateObjectiveStatus({{ $objective->id }}, this.value)"
                                                    class="text-sm border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500
                                                        {{ $objective->status->value === 'complete' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $objective->status->value === 'on_track' ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ $objective->status->value === 'off_track' ? 'bg-orange-100 text-orange-800' : '' }}
                                                        {{ $objective->status->value === 'dropped' ? 'bg-gray-100 text-gray-400' : '' }}"
                                                >
                                                    @foreach ($statuses as $status)
                                                        <option value="{{ $status->value }}" {{ $objective->status->value === $status->value ? 'selected' : '' }}>
                                                            {{ $status->label() }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        function updateObjectiveStatus(objectiveId, status) {
            fetch(`/objectives/${objectiveId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update status');
            });
        }
    </script>
</x-app-layout>
