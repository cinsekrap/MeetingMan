<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Company Settings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Company Info --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Company Information</h3>

                <form method="POST" action="{{ route('company.settings.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" :value="__('Company Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $company->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-primary-button>Save Changes</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Company People --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Company People</h3>
                    <a href="{{ route('people.create') }}" class="text-sm text-primary-600 hover:text-primary-800">
                        Add Person
                    </a>
                </div>

                @if($pendingInvites->isNotEmpty())
                    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm font-medium text-yellow-800 mb-2">Pending Invitations</p>
                        <div class="space-y-1">
                            @foreach($pendingInvites as $invite)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-yellow-700">{{ $invite->email }}</span>
                                    <span class="text-yellow-600 text-xs">Sent {{ $invite->created_at->diffForHumans() }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($people->isEmpty())
                    <p class="text-gray-500 text-sm">No people added yet.</p>
                @else
                    <div class="divide-y divide-gray-200">
                        @foreach($people as $person)
                            <div class="py-3 flex items-center justify-between">
                                <div class="flex items-center">
                                    @if($person->hierarchy_level > 0)
                                        <span class="text-gray-400 mr-2 font-mono text-sm">{{ str_repeat('— ', $person->hierarchy_level) }}</span>
                                    @endif
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-gray-900">{{ $person->name }}</span>
                                            @if($person->isLinkedToUser())
                                                @php
                                                    $linkedUser = $person->linkedUser;
                                                    $role = $linkedUser ? $company->getUserRole($linkedUser) : null;
                                                @endphp
                                                <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">
                                                    MeetingMan User{{ $role ? ' · ' . ucfirst($role->value) : '' }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($person->email)
                                            <div class="text-sm text-gray-500">{{ $person->email }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    @if(!$person->isLinkedToUser() && $person->email)
                                        <a href="{{ route('company.invites.index', ['prefill_email' => $person->email]) }}" class="text-sm text-green-600 hover:text-green-800">
                                            Invite
                                        </a>
                                    @endif
                                    <a href="{{ route('people.edit', $person) }}" class="text-sm text-primary-600 hover:text-primary-800">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
