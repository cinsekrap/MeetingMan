<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Quick Actions --}}
            @if ($allPeople->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 max-w-3xl items-stretch">
                    {{-- Quick Add Topic --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col h-full">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Add Topic</h3>
                        <form action="{{ route('people.planned-topics.store', $allPeople->first()) }}" method="POST" id="quick-add-form" class="flex flex-col flex-1">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="quick-add-person" class="block text-sm font-medium text-gray-700 mb-1">Person</label>
                                    <select name="person_id" id="quick-add-person" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                                        <option value="">Select person...</option>
                                        @foreach ($allPeople as $p)
                                            <option value="{{ $p->id }}">{{ $p->hierarchy_prefix }}{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="quick-add-content" class="block text-sm font-medium text-gray-700 mb-1">Topic for next meeting</label>
                                    <input type="text" name="content" id="quick-add-content" placeholder="e.g. Discuss project timeline..."
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                           required>
                                </div>
                            </div>
                            <div class="pt-4 mt-auto">
                                <button type="submit" class="w-full px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 text-sm font-medium">
                                    Add Topic
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Quick Record Meeting --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col h-full">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Record Meeting</h3>
                        <form id="quick-meeting-form" method="GET" class="flex flex-col flex-1">
                            <div>
                                <label for="quick-meeting-person" class="block text-sm font-medium text-gray-700 mb-1">Person</label>
                                <select id="quick-meeting-person" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                                    <option value="">Select person...</option>
                                    @foreach ($allPeople as $p)
                                        <option value="{{ $p->id }}">{{ $p->hierarchy_prefix }}{{ $p->name }}{{ $p->hierarchy_level > 0 ? ' (skip-level)' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="pt-4 mt-auto">
                                <button type="submit" class="w-full px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 text-sm font-medium">
                                    Record Meeting
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <script>
                    document.getElementById('quick-add-person').addEventListener('change', function() {
                        const form = document.getElementById('quick-add-form');
                        const personId = this.value;
                        if (personId) {
                            form.action = `/people/${personId}/planned-topics`;
                        }
                    });

                    document.getElementById('quick-meeting-form').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const personId = document.getElementById('quick-meeting-person').value;
                        if (personId) {
                            window.location.href = `/people/${personId}/meetings/create`;
                        }
                    });
                </script>
            @endif

            {{-- Your Team --}}
            <h3 class="text-lg font-medium text-gray-900 mb-4">Your Team</h3>

            {{-- At a Glance Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 sm:p-6">
                    <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['total_people'] }}</div>
                    <div class="text-xs sm:text-sm text-gray-500">Direct Reports</div>
                </div>
                <a href="{{ route('actions.index', ['filter' => 'overdue']) }}" class="bg-white overflow-hidden shadow-sm rounded-lg p-4 sm:p-6 {{ $stats['overdue_actions'] > 0 ? 'border-l-4 border-red-500 hover:bg-red-50' : 'hover:bg-gray-50' }} transition">
                    <div class="text-2xl sm:text-3xl font-bold {{ $stats['overdue_actions'] > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $stats['overdue_actions'] }}</div>
                    <div class="text-xs sm:text-sm text-gray-500">Overdue</div>
                </a>
                <a href="{{ route('actions.index', ['filter' => 'due_soon']) }}" class="bg-white overflow-hidden shadow-sm rounded-lg p-4 sm:p-6 {{ $stats['due_soon_actions'] > 0 ? 'border-l-4 border-yellow-500 hover:bg-yellow-50' : 'hover:bg-gray-50' }} transition">
                    <div class="text-2xl sm:text-3xl font-bold {{ $stats['due_soon_actions'] > 0 ? 'text-yellow-600' : 'text-gray-900' }}">{{ $stats['due_soon_actions'] }}</div>
                    <div class="text-xs sm:text-sm text-gray-500">Due Soon</div>
                </a>
                <a href="{{ route('objectives.index', ['filter' => 'off_track']) }}" class="bg-white overflow-hidden shadow-sm rounded-lg p-4 sm:p-6 {{ $stats['off_track_objectives'] > 0 ? 'border-l-4 border-orange-500 hover:bg-orange-50' : 'hover:bg-gray-50' }} transition">
                    <div class="text-2xl sm:text-3xl font-bold {{ $stats['off_track_objectives'] > 0 ? 'text-orange-600' : 'text-gray-900' }}">{{ $stats['off_track_objectives'] }}</div>
                    <div class="text-xs sm:text-sm text-gray-500">Off-Track</div>
                </a>
            </div>

            @if ($people->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-500">No people yet. <a href="{{ route('people.create') }}" class="text-primary-600 hover:text-primary-800">Add your first team member</a>.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($people as $person)
                        @php
                            $lastMeeting = $person->meetings->first();
                            $daysSinceLastMeeting = $lastMeeting ? $lastMeeting->meeting_date->diffInDays(now()) : null;
                            $hasOverdue = $person->overdue_actions_count > 0;
                            $hasOffTrack = $person->off_track_objectives_count > 0;
                            $isMeetingOverdue = $person->isMeetingOverdue();
                            $needsAttention = $hasOverdue || $hasOffTrack || $isMeetingOverdue;
                            $isStale = $daysSinceLastMeeting === null || $daysSinceLastMeeting > 30;
                        @endphp
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg {{ $hasOverdue ? 'ring-2 ring-red-300' : ($hasOffTrack ? 'ring-2 ring-orange-300' : ($isMeetingOverdue ? 'ring-2 ring-yellow-300' : '')) }} {{ $isStale && !$needsAttention ? 'opacity-75' : '' }}">
                            <div class="p-4 sm:p-6">
                                {{-- Header --}}
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('people.meetings.index', $person) }}" class="text-lg font-semibold text-gray-900 hover:text-primary-600">
                                                {{ $person->name }}
                                            </a>
                                            @if ($needsAttention)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Needs attention
                                                </span>
                                            @endif
                                        </div>
                                        @if ($person->email)
                                            <p class="text-sm text-gray-500">{{ $person->email }}</p>
                                        @endif
                                    </div>
                                    {{-- Mood trend (last 3 meetings) --}}
                                    <div class="flex space-x-1">
                                        @foreach ($person->meetings->take(3)->reverse() as $meeting)
                                            <span class="text-lg" title="{{ $meeting->meeting_date->format('j M Y') }}">{{ $meeting->mood_emoji }}</span>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Last Meeting --}}
                                <div class="mb-4">
                                    @if ($lastMeeting)
                                        <div class="text-sm {{ $isMeetingOverdue ? 'text-red-600' : ($daysSinceLastMeeting > 14 ? 'text-orange-600' : 'text-gray-600') }}">
                                            Last meeting: {{ $lastMeeting->meeting_date->format('j M Y') }}
                                            <span class="text-gray-400">({{ $lastMeeting->meeting_date->diffForHumans() }})</span>
                                            @if ($isMeetingOverdue)
                                                <span class="text-red-600 font-medium">- Meeting overdue</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-sm {{ $person->meeting_frequency_days ? 'text-red-600' : 'text-gray-400' }}">
                                            No meetings yet
                                            @if ($person->meeting_frequency_days)
                                                <span class="font-medium">- Meeting overdue</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if ($person->meeting_frequency_days)
                                        <div class="text-xs text-gray-400">Schedule: every {{ $person->meeting_frequency_days }} days</div>
                                    @endif
                                </div>

                                {{-- Stats --}}
                                <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Actions:</span>
                                        <span class="font-medium">{{ $person->pending_actions_count }} pending</span>
                                        @if ($person->overdue_actions_count > 0)
                                            <span class="text-red-600 font-medium">({{ $person->overdue_actions_count }} overdue)</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Objectives:</span>
                                        <span class="font-medium">{{ $person->active_objectives_count }} active</span>
                                        @if ($person->off_track_objectives_count > 0)
                                            <span class="text-orange-600 font-medium">({{ $person->off_track_objectives_count }} off-track)</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Quick Actions --}}
                                <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-100">
                                    <a href="{{ route('people.meetings.create', $person) }}" class="px-3 py-1.5 text-sm font-medium text-white bg-primary-500 rounded-md hover:bg-primary-600">
                                        Record Meeting
                                    </a>
                                    <a href="{{ route('people.actions.index', $person) }}" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                        Actions
                                    </a>
                                    <a href="{{ route('people.objectives.index', $person) }}" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                        Objectives
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- See more / Add Person Link --}}
                <div class="mt-6 text-center space-x-4">
                    @if ($hasMorePeople)
                        <a href="{{ route('people.index') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">See all {{ $totalPeopleCount }} people &rarr;</a>
                    @endif
                    <a href="{{ route('people.create') }}" class="text-sm text-gray-500 hover:text-gray-700">+ Add person</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
