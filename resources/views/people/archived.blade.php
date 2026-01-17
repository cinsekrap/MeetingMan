<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Archived People') }}
            </h2>
            <a href="{{ route('people.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                Back to People
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($people->isEmpty())
                        <p class="text-gray-500">No archived people.</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($people as $person)
                                <div class="flex items-center justify-between p-4 border rounded-lg bg-gray-50">
                                    <div>
                                        <span class="font-medium text-gray-600">{{ $person->name }}</span>
                                        @if ($person->email)
                                            <p class="text-sm text-gray-400">{{ $person->email }}</p>
                                        @endif
                                        <p class="text-xs text-gray-400">Archived {{ $person->archived_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <form action="{{ route('people.restore', $person) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-sm text-primary-600 hover:text-primary-800">Restore</button>
                                        </form>
                                        <form action="{{ route('people.destroy', $person) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this person and all their data?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
