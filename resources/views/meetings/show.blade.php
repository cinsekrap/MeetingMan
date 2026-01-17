<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
            <div>
                <a href="{{ route('people.meetings.index', $meeting->person) }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; {{ $meeting->person->name }}</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                    <span class="mr-2">{{ $meeting->mood_emoji }}</span>
                    <span class="hidden sm:inline">{{ $meeting->meeting_date->format('l, j F Y') }}</span>
                    <span class="sm:hidden">{{ $meeting->meeting_date->format('j M Y') }}</span>
                </h2>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('meetings.email-preview', $meeting) }}" target="_blank" class="inline-flex items-center px-3 sm:px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <span class="hidden sm:inline">Preview Email</span>
                    <span class="sm:hidden">Email</span>
                </a>
                <a href="{{ route('meetings.edit', $meeting) }}" class="inline-flex items-center px-3 sm:px-4 py-2 bg-primary-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-600 focus:bg-primary-600 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Topics --}}
            @if ($meeting->topics->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Discussion Topics</h3>
                    <div class="space-y-6">
                        @foreach ($meeting->topics as $topic)
                            <div>
                                <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Topic {{ $topic->position_word }}</div>
                                <div class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $topic->content }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            @if ($meeting->actions->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Action Items</h3>
                    <div class="space-y-4">
                        @foreach ($meeting->actions as $action)
                            <div class="p-3 sm:p-4 border rounded-lg {{ $action->is_overdue ? 'border-red-300 bg-red-50' : ($action->is_due_soon ? 'border-yellow-300 bg-yellow-50' : '') }}">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">{{ $action->description }}</div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            Assigned to: {{ $action->assigned_to_name }}
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 sm:text-right sm:flex-col sm:items-end">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $action->status->value === 'complete' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $action->status->value === 'on_track' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $action->status->value === 'not_started' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $action->status->value === 'dropped' ? 'bg-gray-100 text-gray-400' : '' }}
                                        ">
                                            {{ $action->status->label() }}
                                        </span>
                                        <div class="text-sm text-gray-500">
                                            Due: {{ $action->due_date->format('j M Y') }}
                                            @if ($action->is_overdue)
                                                <span class="text-red-600 font-medium">(Overdue)</span>
                                            @elseif ($action->is_due_soon)
                                                <span class="text-yellow-600 font-medium">(Soon)</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- No content --}}
            @if ($meeting->topics->isEmpty() && $meeting->actions->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-500">No topics or actions recorded for this meeting.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
