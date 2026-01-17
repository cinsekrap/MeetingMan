<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Person') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('people.update', $person) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $person->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Email (optional)')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $person->email)" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
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
                                    :value="old('meeting_frequency_days', $person->meeting_frequency_days)"
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
                                    <option value="{{ $p->id }}" {{ old('reports_to_person_id', $person->reports_to_person_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('reports_to_person_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('people.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Save Changes') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-red-600">Danger Zone</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Deleting this person will also delete all their meetings, topics, actions, and objectives. This cannot be undone.
                    </p>
                    <form method="POST" action="{{ route('people.destroy', $person) }}" class="mt-4" onsubmit="return confirm('Are you sure you want to delete this person and all their data? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>
                            {{ __('Delete Person') }}
                        </x-danger-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
