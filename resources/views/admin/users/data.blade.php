<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User Data: {{ $user->name }}
            </h2>
            <a href="{{ route('admin.users.show', $user) }}" class="text-sm text-purple-600 hover:text-purple-800">&larr; Back to User</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Notice -->
            <div class="mb-6 bg-amber-50 border border-amber-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-amber-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-amber-800">
                        <strong>Read-only access.</strong> This data access has been logged and the user has been notified.
                    </div>
                </div>
            </div>

            <!-- People -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">People ({{ $people->count() }})</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($people as $person)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $person->name }}</div>
                                <div class="text-sm text-gray-500">{{ $person->email ?? 'No email' }}</div>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $person->meetings_count }} meetings
                                @if($person->archived_at)
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Archived</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-4 text-gray-500 text-sm">No people recorded.</div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Meetings -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Meetings ({{ $recentMeetings->count() }})</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($recentMeetings as $meeting)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-sm font-medium text-gray-900">
                                    Meeting with {{ $meeting->person->name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $meeting->meeting_date->format('M j, Y') }}
                                    @if($meeting->mood)
                                        <span class="ml-2">{{ $meeting->mood_emoji }}</span>
                                    @endif
                                </div>
                            </div>

                            @if($meeting->topics->count() > 0)
                                <div class="mt-2">
                                    <div class="text-xs font-medium text-gray-500 uppercase mb-1">Topics</div>
                                    <ul class="text-sm text-gray-700 space-y-1">
                                        @foreach($meeting->topics as $topic)
                                            <li class="pl-3 border-l-2 border-gray-200">{{ Str::limit($topic->content, 100) }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($meeting->actions->count() > 0)
                                <div class="mt-2">
                                    <div class="text-xs font-medium text-gray-500 uppercase mb-1">Actions</div>
                                    <ul class="text-sm text-gray-700 space-y-1">
                                        @foreach($meeting->actions as $action)
                                            <li class="pl-3 border-l-2 border-gray-200">
                                                {{ Str::limit($action->description, 100) }}
                                                <span class="text-gray-500">- {{ $action->status->value }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="px-6 py-4 text-gray-500 text-sm">No meetings recorded.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
