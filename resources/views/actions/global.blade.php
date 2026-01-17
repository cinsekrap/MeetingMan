<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Dashboard</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $title }}
                </h2>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('actions.index') }}" class="text-sm px-3 py-1 rounded-full {{ !$filter ? 'bg-primary-100 text-primary-800' : 'text-gray-600 hover:bg-gray-100' }}">All</a>
                <a href="{{ route('actions.index', ['filter' => 'overdue']) }}" class="text-sm px-3 py-1 rounded-full {{ $filter === 'overdue' ? 'bg-red-100 text-red-800' : 'text-gray-600 hover:bg-gray-100' }}">Overdue</a>
                <a href="{{ route('actions.index', ['filter' => 'due_soon']) }}" class="text-sm px-3 py-1 rounded-full {{ $filter === 'due_soon' ? 'bg-yellow-100 text-yellow-800' : 'text-gray-600 hover:bg-gray-100' }}">Due Soon</a>
                <a href="{{ route('actions.index', ['filter' => 'pending']) }}" class="text-sm px-3 py-1 rounded-full {{ $filter === 'pending' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:bg-gray-100' }}">Pending</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if ($actions->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-500">
                        @if ($filter === 'overdue')
                            No overdue actions. Great job staying on top of things!
                        @elseif ($filter === 'due_soon')
                            No actions due soon.
                        @else
                            No actions found.
                        @endif
                    </p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($actions as $personName => $personActions)
                        @php
                            $person = $personActions->first()->meeting->person;
                            $overdueActionsForPerson = $personActions->filter(fn($a) => $a->is_overdue && !in_array($a->status->value, ['complete', 'dropped']));
                            $dueSoonActionsForPerson = $personActions->filter(fn($a) => $a->is_due_soon && !$a->is_overdue && !in_array($a->status->value, ['complete', 'dropped']));
                            $reminderCountForPerson = $overdueActionsForPerson->count() + $dueSoonActionsForPerson->count();
                        @endphp
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900">{{ $personName }}</h3>

                                @if ($reminderCountForPerson >= 2 && $person->email)
                                    <div x-data="{ open: false }">
                                        <button
                                            @click="open = true"
                                            type="button"
                                            class="text-sm px-3 py-1.5 rounded bg-orange-100 text-orange-700 hover:bg-orange-200"
                                        >
                                            Send Reminder for All ({{ $reminderCountForPerson }})
                                        </button>

                                        {{-- Modal backdrop --}}
                                        <div
                                            x-show="open"
                                            x-cloak
                                            class="fixed inset-0 bg-gray-500 bg-opacity-75 z-40"
                                            @click="open = false"
                                        ></div>

                                        {{-- Modal --}}
                                        <div
                                            x-show="open"
                                            x-cloak
                                            class="fixed inset-0 z-50 overflow-y-auto"
                                        >
                                            <div class="flex min-h-full items-center justify-center p-4">
                                                <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6" @click.stop>
                                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                                        Email Preview
                                                    </h3>

                                                    <div class="bg-gray-50 border rounded-lg p-4 mb-4 text-sm">
                                                        <div class="mb-2">
                                                            <span class="font-medium text-gray-600">To:</span>
                                                            <span class="text-gray-900">{{ $person->email }}</span>
                                                        </div>
                                                        <div class="mb-3">
                                                            <span class="font-medium text-gray-600">Subject:</span>
                                                            <span class="text-gray-900">{{ $overdueActionsForPerson->isNotEmpty() ? 'Quick check-in: Action items update' : 'Upcoming: Action items due soon' }}</span>
                                                        </div>
                                                        <hr class="my-3">
                                                        <div class="text-gray-700 space-y-3">
                                                            <p>Hi {{ $person->name }},</p>
                                                            <p>I hope you're doing well. I wanted to check in on a few action items from our meetings.</p>

                                                            @if ($overdueActionsForPerson->isNotEmpty())
                                                                <p class="font-medium text-red-700">Past due date:</p>
                                                                @foreach ($overdueActionsForPerson as $overdueAction)
                                                                    <blockquote class="border-l-4 border-red-300 pl-3 my-2">
                                                                        <strong>{{ $overdueAction->description }}</strong><br>
                                                                        Due: {{ $overdueAction->due_date->format('j F Y') }}
                                                                    </blockquote>
                                                                @endforeach
                                                            @endif

                                                            @if ($dueSoonActionsForPerson->isNotEmpty())
                                                                <p class="font-medium text-yellow-700">Coming up soon:</p>
                                                                @foreach ($dueSoonActionsForPerson as $dueSoonAction)
                                                                    <blockquote class="border-l-4 border-yellow-300 pl-3 my-2">
                                                                        <strong>{{ $dueSoonAction->description }}</strong><br>
                                                                        Due: {{ $dueSoonAction->due_date->format('j F Y') }}
                                                                    </blockquote>
                                                                @endforeach
                                                            @endif

                                                            <p>Could you give me a quick update on where these stand when we next meet? If there's anything I can do to help move things forward, just let me know.</p>
                                                            <p>Thanks,<br>{{ auth()->user()->name }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="flex justify-end gap-4">
                                                        <button
                                                            @click="open = false"
                                                            type="button"
                                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                                        >
                                                            Cancel
                                                        </button>
                                                        <form action="{{ route('actions.sendBatchReminder', $person) }}" method="POST">
                                                            @csrf
                                                            <button
                                                                type="submit"
                                                                class="px-4 py-2 text-sm font-medium text-white bg-primary-500 rounded-md hover:bg-primary-600"
                                                            >
                                                                Send Email
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    @foreach ($personActions as $action)
                                        <div class="p-3 sm:p-4 border rounded-lg {{ $action->is_overdue ? 'border-red-300 bg-red-50' : ($action->is_due_soon ? 'border-yellow-300 bg-yellow-50' : '') }} {{ in_array($action->status->value, ['complete', 'dropped']) ? 'opacity-60' : '' }}">
                                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-medium text-gray-900 {{ $action->status->value === 'complete' ? 'line-through' : '' }}">
                                                        {{ $action->description }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 mt-1 space-y-1 sm:space-y-0">
                                                        <span>Due: {{ $action->due_date->format('j M Y') }}</span>
                                                        @if ($action->is_overdue)
                                                            <span class="text-red-600 font-medium">(Overdue)</span>
                                                        @elseif ($action->is_due_soon)
                                                            <span class="text-yellow-600 font-medium">(Soon)</span>
                                                        @endif
                                                        <span class="hidden sm:inline mx-2">|</span>
                                                        <span class="block sm:inline">Assigned: {{ $action->assigned_to_name }}</span>
                                                        <span class="hidden sm:inline mx-2">|</span>
                                                        <a href="{{ route('meetings.show', $action->meeting) }}" class="block sm:inline text-primary-600 hover:text-primary-800">
                                                            Meeting {{ $action->meeting->meeting_date->format('j M') }}
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 sm:gap-4 flex-wrap sm:flex-nowrap">
                                                @if (($action->is_overdue || $action->is_due_soon) && $action->meeting->person->email && !in_array($action->status->value, ['complete', 'dropped']))
                                                    <div x-data="{ open: false }" class="relative">
                                                        <button
                                                            @click="open = true"
                                                            type="button"
                                                            class="text-sm px-2 py-1 rounded {{ $action->is_overdue ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}"
                                                            title="Send polite reminder email"
                                                        >
                                                            Send Reminder
                                                        </button>

                                                        {{-- Modal backdrop --}}
                                                        <div
                                                            x-show="open"
                                                            x-cloak
                                                            class="fixed inset-0 bg-gray-500 bg-opacity-75 z-40"
                                                            @click="open = false"
                                                        ></div>

                                                        {{-- Modal --}}
                                                        <div
                                                            x-show="open"
                                                            x-cloak
                                                            class="fixed inset-0 z-50 overflow-y-auto"
                                                        >
                                                            <div class="flex min-h-full items-center justify-center p-4">
                                                                <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6" @click.stop>
                                                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                                                        Email Preview
                                                                    </h3>

                                                                    <div class="bg-gray-50 border rounded-lg p-4 mb-4 text-sm">
                                                                        <div class="mb-2">
                                                                            <span class="font-medium text-gray-600">To:</span>
                                                                            <span class="text-gray-900">{{ $action->meeting->person->email }}</span>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <span class="font-medium text-gray-600">Subject:</span>
                                                                            <span class="text-gray-900">{{ $action->is_overdue ? 'Quick check-in: Action item update' : 'Upcoming: Action item due soon' }}</span>
                                                                        </div>
                                                                        <hr class="my-3">
                                                                        <div class="text-gray-700 space-y-3">
                                                                            <p>Hi {{ $action->meeting->person->name }},</p>
                                                                            @if ($action->is_overdue)
                                                                                <p>I hope you're doing well. I wanted to check in on an action item from our previous meeting that's now past its due date:</p>
                                                                                <blockquote class="border-l-4 border-gray-300 pl-3 my-2">
                                                                                    <strong>{{ $action->description }}</strong><br>
                                                                                    Due: {{ $action->due_date->format('j F Y') }}
                                                                                </blockquote>
                                                                                <p>Could you give me a quick update on where this stands when we next meet? If there's anything I can do to help move this forward, just let me know.</p>
                                                                            @else
                                                                                <p>I hope you're doing well. Just a friendly heads-up that we have an action item coming up soon:</p>
                                                                                <blockquote class="border-l-4 border-gray-300 pl-3 my-2">
                                                                                    <strong>{{ $action->description }}</strong><br>
                                                                                    Due: {{ $action->due_date->format('j F Y') }}
                                                                                </blockquote>
                                                                                <p>Could you update me on how this is progressing at our next meeting? If you need any support or if the timeline needs adjusting, I'm happy to discuss.</p>
                                                                            @endif
                                                                            <p>Thanks,<br>{{ auth()->user()->name }}</p>
                                                                        </div>
                                                                    </div>

                                                                    <div class="flex justify-end gap-4">
                                                                        <button
                                                                            @click="open = false"
                                                                            type="button"
                                                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                                                        >
                                                                            Cancel
                                                                        </button>
                                                                        <form action="{{ route('actions.sendReminder', $action) }}" method="POST">
                                                                            @csrf
                                                                            <input type="hidden" name="type" value="{{ $action->is_overdue ? 'overdue' : 'due_soon' }}">
                                                                            <button
                                                                                type="submit"
                                                                                class="px-4 py-2 text-sm font-medium text-white bg-primary-500 rounded-md hover:bg-primary-600"
                                                                            >
                                                                                Send Email
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <select
                                                    onchange="updateActionStatus({{ $action->id }}, this.value)"
                                                    class="text-sm border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500
                                                        {{ $action->status->value === 'complete' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $action->status->value === 'on_track' ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ $action->status->value === 'not_started' ? 'bg-gray-100 text-gray-800' : '' }}
                                                        {{ $action->status->value === 'dropped' ? 'bg-gray-100 text-gray-400' : '' }}"
                                                >
                                                    @foreach ($statuses as $status)
                                                        <option value="{{ $status->value }}" {{ $action->status->value === $status->value ? 'selected' : '' }}>
                                                            {{ $status->label() }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                </div>
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
        function updateActionStatus(actionId, status) {
            fetch(`/actions/${actionId}/status`, {
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
