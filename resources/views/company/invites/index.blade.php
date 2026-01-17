<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Team Invitations
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

            {{-- People who can be invited --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">People Without Accounts</h3>
                    <a href="{{ route('people.create') }}" class="text-sm text-primary-600 hover:text-primary-800">
                        Add Person
                    </a>
                </div>

                @if($uninvitedPeople->isEmpty())
                    <p class="text-gray-500 text-sm">All people in your company have been invited or have accounts.</p>
                @else
                    <p class="text-sm text-gray-500 mb-4">These people don't have MeetingMan accounts yet. Send them an invite to join.</p>
                    <div class="divide-y divide-gray-200">
                        @foreach($uninvitedPeople as $person)
                            <div class="py-3 flex items-center justify-between">
                                <div class="flex items-center">
                                    @if($person->hierarchy_level > 0)
                                        <span class="text-gray-400 mr-2 font-mono text-sm">{{ str_repeat('— ', $person->hierarchy_level) }}</span>
                                    @endif
                                    <div>
                                        <span class="font-medium text-gray-900">{{ $person->name }}</span>
                                        @if($person->email)
                                            <span class="text-sm text-gray-500 ml-2">{{ $person->email }}</span>
                                        @endif
                                    </div>
                                </div>
                                @if($person->email)
                                    <form method="POST" action="{{ route('company.invites.store') }}" class="flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="email" value="{{ $person->email }}">
                                        <input type="hidden" name="role" value="member">
                                        <button type="submit" class="text-sm text-green-600 hover:text-green-800">
                                            Send Invite
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('people.edit', $person) }}" class="text-sm text-gray-500 hover:text-gray-700">
                                        Add email first
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Pending Invites --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pending Invitations</h3>

                @if($invites->where('accepted_at', null)->where('expires_at', '>', now())->isEmpty())
                    <p class="text-gray-500">No pending invitations.</p>
                @else
                    <div class="divide-y divide-gray-200">
                        @foreach($invites->where('accepted_at', null)->where('expires_at', '>', now()) as $invite)
                            <div class="py-4 flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $invite->email }}</div>
                                    <div class="text-sm text-gray-500">
                                        Invited as {{ ucfirst($invite->role) }} by {{ $invite->invitedBy->name }}
                                        &middot; Expires {{ $invite->expires_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <form method="POST" action="{{ route('company.invites.resend', $invite) }}">
                                        @csrf
                                        <button type="submit" class="text-sm text-primary-600 hover:text-primary-800">
                                            Resend
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('company.invites.destroy', $invite) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                            Cancel
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Accepted Invites --}}
            @if($invites->where('accepted_at', '!=', null)->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Accepted Invitations</h3>

                    <div class="divide-y divide-gray-200">
                        @foreach($invites->where('accepted_at', '!=', null) as $invite)
                            <div class="py-4">
                                <div class="font-medium text-gray-900">{{ $invite->email }}</div>
                                <div class="text-sm text-gray-500">
                                    Joined as {{ ucfirst($invite->role) }}
                                    &middot; Accepted {{ $invite->accepted_at->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Expired Invites --}}
            @if($invites->where('accepted_at', null)->where('expires_at', '<=', now())->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Expired Invitations</h3>

                    <div class="divide-y divide-gray-200">
                        @foreach($invites->where('accepted_at', null)->where('expires_at', '<=', now()) as $invite)
                            <div class="py-4 flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-500">{{ $invite->email }}</div>
                                    <div class="text-sm text-gray-400">
                                        Expired {{ $invite->expires_at->diffForHumans() }}
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('company.invites.destroy', $invite) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
