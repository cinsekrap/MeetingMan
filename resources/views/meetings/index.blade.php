<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <a href="{{ route('people.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; People</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <span class="hidden sm:inline">Meetings with</span> {{ $person->name }}
                </h2>
            </div>
            <a href="{{ route('people.meetings.create', $person) }}" class="inline-flex items-center justify-center px-4 py-2 bg-primary-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-600 focus:bg-primary-600 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Record Meeting
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Navigation tabs --}}
            <div class="mb-4 border-b border-gray-200 overflow-x-auto">
                <nav class="flex space-x-4 sm:space-x-8 min-w-max">
                    <a href="{{ route('people.meetings.index', $person) }}" class="py-2 px-1 text-sm font-medium text-primary-600 border-b-2 border-primary-500">
                        Meetings
                    </a>
                    <a href="{{ route('people.actions.index', $person) }}" class="py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                        Actions
                    </a>
                    <a href="{{ route('people.objectives.index', $person) }}" class="py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                        Objectives
                    </a>
                </nav>
            </div>

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Planned Topics for Next Meeting --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Topics for Next Meeting</h3>

                    {{-- Add new planned topic --}}
                    <form action="{{ route('people.planned-topics.store', $person) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="flex gap-2">
                            <input type="text" name="content" placeholder="Add a topic to discuss next time..."
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                   required>
                            <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 text-sm font-medium">
                                Add
                            </button>
                        </div>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </form>

                    {{-- List of planned topics --}}
                    @if ($plannedTopics->isEmpty())
                        <p class="text-sm text-gray-500">No topics planned. Add topics here and they'll appear when you create the next meeting.</p>
                    @else
                        <ul class="space-y-2">
                            @foreach ($plannedTopics as $topic)
                                <li class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <span class="text-gray-700">{{ $topic->content }}</span>
                                    <form action="{{ route('planned-topics.destroy', $topic) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-500" title="Remove">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($meetings->isEmpty())
                        <p class="text-gray-500">No meetings recorded yet. <a href="{{ route('people.meetings.create', $person) }}" class="text-primary-600 hover:text-primary-800">Record your first meeting</a>.</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($meetings as $meeting)
                                <a href="{{ route('meetings.show', $meeting) }}" class="block p-3 sm:p-4 border rounded-lg hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <span class="text-xl sm:text-2xl">{{ $meeting->mood_emoji }}</span>
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    <span class="hidden sm:inline">{{ $meeting->meeting_date->format('l, j F Y') }}</span>
                                                    <span class="sm:hidden">{{ $meeting->meeting_date->format('j M Y') }}</span>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $meeting->topics->count() }} topic(s), {{ $meeting->actions->count() }} action(s)
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-gray-400">&rarr;</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
