<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('people.meetings.index', $person) }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Meetings</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Record Meeting with {{ $person->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('people.meetings.store', $person) }}" class="space-y-6">
                @csrf

                {{-- Date and Mood --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="meeting_date" :value="__('Date')" />
                            <x-text-input id="meeting_date" class="block mt-1 w-full" type="datetime-local" name="meeting_date" :value="old('meeting_date', now()->format('Y-m-d\TH:i'))" required />
                            <x-input-error :messages="$errors->get('meeting_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label :value="__('Mood')" />
                            <div class="flex items-center space-x-1 mt-2" id="mood-selector">
                                @foreach ([1 => '😭', 2 => '😞', 3 => '😐', 4 => '🙂', 5 => '😀'] as $value => $emoji)
                                    <button type="button"
                                        onclick="selectMood({{ $value }})"
                                        data-mood="{{ $value }}"
                                        class="mood-btn p-2 text-3xl rounded-lg border-2 {{ old('mood', 3) == $value ? 'border-primary-500 bg-primary-50' : 'border-transparent hover:bg-gray-100' }}">
                                        {{ $emoji }}
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" name="mood" id="mood-input" value="{{ old('mood', 3) }}">
                            <x-input-error :messages="$errors->get('mood')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Planned Topics --}}
                @if ($plannedTopics->isNotEmpty())
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Planned Topics</h3>
                        <p class="text-sm text-gray-500 mb-4">Click a topic to add it as a discussion topic:</p>
                        <div class="flex flex-wrap gap-2" id="planned-topics-container">
                            @foreach ($plannedTopics as $topic)
                                <button type="button"
                                        onclick="addPlannedTopic({{ $topic->id }}, '{{ addslashes($topic->content) }}')"
                                        id="planned-topic-btn-{{ $topic->id }}"
                                        class="px-3 py-1.5 text-sm bg-primary-100 text-primary-700 rounded-full hover:bg-primary-200 transition-colors">
                                    + {{ $topic->content }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Topics --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Discussion Topics</h3>
                    <div id="topics-container" class="space-y-4">
                        <div class="topic-row">
                            <x-input-label :value="__('Topic 1')" />
                            <textarea name="topics[0][content]" rows="3" class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" placeholder="What did you discuss?">{{ old('topics.0.content') }}</textarea>
                        </div>
                    </div>
                    <button type="button" onclick="addTopic()" class="mt-4 text-sm text-primary-600 hover:text-primary-800">+ Add another topic</button>
                </div>

                {{-- Actions --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Action Items</h3>
                    <div id="actions-container" class="space-y-4">
                        <div class="action-row p-4 border rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <x-input-label :value="__('Description')" />
                                    <x-text-input class="block mt-1 w-full" type="text" name="actions[0][description]" :value="old('actions.0.description')" placeholder="What needs to be done?" />
                                </div>
                                <div>
                                    <x-input-label :value="__('Assigned To')" />
                                    <select name="actions[0][assigned_to_person_id]" class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">
                                        <option value="">Select person...</option>
                                        @foreach ($people as $p)
                                            <option value="{{ $p->id }}">{{ $p->hierarchy_prefix ?? '' }}{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-text-input class="block mt-2 w-full" type="text" name="actions[0][assigned_to_text]" :value="old('actions.0.assigned_to_text')" placeholder="Or type a name..." />
                                </div>
                                <div>
                                    <x-input-label :value="__('Due Date')" />
                                    <x-text-input class="block mt-1 w-full" type="date" name="actions[0][due_date]" :value="old('actions.0.due_date', now()->addWeek()->format('Y-m-d'))" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="addAction()" class="mt-4 text-sm text-primary-600 hover:text-primary-800">+ Add another action</button>
                </div>

                {{-- Sharing Options --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">Options</h3>

                    @if ($person->isLinkedToUser())
                        <label class="flex items-start">
                            <input type="checkbox" name="shared_with_person" value="1" class="mt-1 rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                            <span class="ms-2">
                                <span class="text-sm font-medium text-gray-700">Share with {{ $person->name }}</span>
                                <span class="block text-sm text-gray-500">{{ $person->name }} will be able to see this meeting's topics and actions in their account.</span>
                            </span>
                        </label>
                    @else
                        <div class="flex items-start text-sm text-gray-500">
                            <svg class="w-5 h-5 me-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>
                                {{ $person->name }} isn't registered with MeetingMan yet.
                                @if($person->email && auth()->user()->isCompanyAdmin(auth()->user()->currentCompany()))
                                    <a href="{{ route('company.invites.index', ['prefill_email' => $person->email]) }}" class="text-primary-600 hover:text-primary-800">Send them an invite &raquo;</a>
                                @elseif($person->email)
                                    Ask your company admin to send them an invite.
                                @else
                                    Add their email address to send an invite.
                                @endif
                            </span>
                        </div>
                    @endif

                    @if ($person->email)
                        <label class="flex items-start">
                            <input type="checkbox" name="send_email" value="1" class="mt-1 rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                            <span class="ms-2">
                                <span class="text-sm font-medium text-gray-700">Send meeting summary email</span>
                                <span class="block text-sm text-gray-500">Email will be sent to {{ $person->email }}</span>
                            </span>
                        </label>
                    @endif
                </div>

                {{-- Submit --}}
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('people.meetings.index', $person) }}" class="text-sm text-gray-600 hover:text-gray-900 py-2">Cancel</a>
                    <x-primary-button>Save Meeting</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let topicCount = 1;
        let actionCount = 1;

        function selectMood(value) {
            document.getElementById('mood-input').value = value;
            document.querySelectorAll('.mood-btn').forEach(btn => {
                btn.classList.remove('border-primary-500', 'bg-primary-50');
                btn.classList.add('border-transparent');
            });
            document.querySelector(`[data-mood="${value}"]`).classList.remove('border-transparent');
            document.querySelector(`[data-mood="${value}"]`).classList.add('border-primary-500', 'bg-primary-50');
        }

        function addPlannedTopic(id, content) {
            const container = document.getElementById('topics-container');
            const div = document.createElement('div');
            div.className = 'topic-row';
            div.innerHTML = `
                <div class="flex justify-between items-center">
                    <label class="block font-medium text-sm text-gray-700">Topic ${topicCount + 1}</label>
                    <button type="button" onclick="removePlannedTopic(this, ${id})" class="text-sm text-red-600 hover:text-red-800">Remove</button>
                </div>
                <input type="hidden" name="planned_topic_ids[]" value="${id}">
                <textarea name="topics[${topicCount}][content]" rows="3" class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" placeholder="What did you discuss?">${content}\n\n</textarea>
            `;
            container.appendChild(div);
            topicCount++;

            // Hide the button
            document.getElementById('planned-topic-btn-' + id).style.display = 'none';

            // Focus the textarea and put cursor at the end
            const textarea = div.querySelector('textarea');
            textarea.focus();
            textarea.setSelectionRange(textarea.value.length, textarea.value.length);
        }

        function removePlannedTopic(button, id) {
            button.parentElement.parentElement.remove();
            // Show the button again
            const btn = document.getElementById('planned-topic-btn-' + id);
            if (btn) btn.style.display = '';
        }

        function addTopic() {
            const container = document.getElementById('topics-container');
            const div = document.createElement('div');
            div.className = 'topic-row';
            div.innerHTML = `
                <div class="flex justify-between items-center">
                    <label class="block font-medium text-sm text-gray-700">Topic ${topicCount + 1}</label>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-sm text-red-600 hover:text-red-800">Remove</button>
                </div>
                <textarea name="topics[${topicCount}][content]" rows="3" class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" placeholder="What did you discuss?"></textarea>
            `;
            container.appendChild(div);
            topicCount++;
        }

        function addAction() {
            const container = document.getElementById('actions-container');
            const div = document.createElement('div');
            div.className = 'action-row p-4 border rounded-lg';
            div.innerHTML = `
                <div class="flex justify-end mb-2">
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-sm text-red-600 hover:text-red-800">Remove</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block font-medium text-sm text-gray-700">Description</label>
                        <input type="text" name="actions[${actionCount}][description]" class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" placeholder="What needs to be done?" />
                    </div>
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Assigned To</label>
                        <select name="actions[${actionCount}][assigned_to_person_id]" class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">
                            <option value="">Select person...</option>
                            @foreach ($people as $p)
                                <option value="{{ $p->id }}">{{ $p->hierarchy_prefix ?? '' }}{{ $p->name }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="actions[${actionCount}][assigned_to_text]" class="block mt-2 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" placeholder="Or type a name..." />
                    </div>
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Due Date</label>
                        <input type="date" name="actions[${actionCount}][due_date]" value="{{ now()->addWeek()->format('Y-m-d') }}" class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" />
                    </div>
                </div>
            `;
            container.appendChild(div);
            actionCount++;
        }
    </script>
</x-app-layout>
