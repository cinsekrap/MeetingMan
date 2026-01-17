<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Person') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('people.store') }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Email (optional)')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" onchange="toggleInviteOption()" onkeyup="toggleInviteOption()" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="mb-4 hidden" id="invite-option">
                            <label class="flex items-start">
                                <input type="checkbox" name="send_invite" value="1" {{ old('send_invite') ? 'checked' : '' }} class="mt-1 rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                                <span class="ms-2">
                                    <span class="text-sm font-medium text-gray-700">Send invite to join MeetingMan</span>
                                    <span class="block text-sm text-gray-500">They'll receive an email invitation to create an account and join your company.</span>
                                </span>
                            </label>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="meeting_frequency_days" :value="__('Meeting Frequency')" />
                            <p class="text-sm text-gray-500 mb-2">How often should you meet with this person?</p>
                            <div class="flex items-center space-x-3">
                                <x-text-input
                                    id="meeting_frequency_days"
                                    name="meeting_frequency_days"
                                    type="number"
                                    min="1"
                                    max="365"
                                    class="w-24"
                                    :value="old('meeting_frequency_days', $defaultFrequency)"
                                />
                                <span class="text-gray-700">days</span>
                            </div>
                            <div class="mt-2 text-sm text-gray-500">
                                <button type="button" onclick="document.getElementById('meeting_frequency_days').value=7" class="text-primary-600 hover:text-primary-800">Weekly</button> |
                                <button type="button" onclick="document.getElementById('meeting_frequency_days').value=14" class="text-primary-600 hover:text-primary-800">Fortnightly</button> |
                                <button type="button" onclick="document.getElementById('meeting_frequency_days').value=30" class="text-primary-600 hover:text-primary-800">Monthly</button> |
                                <button type="button" onclick="document.getElementById('meeting_frequency_days').value=''" class="text-gray-500 hover:text-gray-700">No schedule</button>
                            </div>
                            <x-input-error :messages="$errors->get('meeting_frequency_days')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="reports_to_person_id" :value="__('Reports To (optional)')" />
                            <p class="text-sm text-gray-500 mb-2">Who does this person report to? This enables skip-level meetings.</p>
                            <select id="reports_to_person_id" name="reports_to_person_id" class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">
                                <option value="">No one (direct report to you)</option>
                                @foreach($allPeople as $p)
                                    <option value="{{ $p->id }}" {{ old('reports_to_person_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('reports_to_person_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('people.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Add Person') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleInviteOption() {
            const email = document.getElementById('email').value.trim();
            const inviteOption = document.getElementById('invite-option');
            if (email && email.includes('@')) {
                inviteOption.classList.remove('hidden');
            } else {
                inviteOption.classList.add('hidden');
            }
        }
        // Run on page load in case of old() value
        document.addEventListener('DOMContentLoaded', toggleInviteOption);
    </script>
</x-app-layout>
