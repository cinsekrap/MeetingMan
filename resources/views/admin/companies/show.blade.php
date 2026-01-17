<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.companies.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; All Companies</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $company->name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Company Info -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <div class="text-sm font-medium text-gray-500">Created</div>
                        <div class="mt-1 text-lg text-gray-900">{{ $company->created_at->format('j M Y') }}</div>
                        <div class="text-sm text-gray-500">{{ $company->created_at->diffForHumans() }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Users</div>
                        <div class="mt-1 text-lg text-gray-900">{{ $company->users->count() }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">People</div>
                        <div class="mt-1 text-lg text-gray-900">{{ $company->people->count() }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">ID</div>
                        <div class="mt-1 text-lg text-gray-900 font-mono">{{ $company->id }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Users -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Users ({{ $company->users->count() }})</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($company->users as $user)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $user->name }}
                                        @if($user->is_super_admin)
                                            <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">Super Admin</span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                                <div class="text-sm">
                                    <span class="px-2 py-1 rounded-full {{ $user->pivot->role === 'owner' ? 'bg-purple-100 text-purple-700' : ($user->pivot->role === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                        {{ ucfirst($user->pivot->role) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-4 text-gray-500 text-sm">No users.</div>
                        @endforelse
                    </div>
                </div>

                <!-- People -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">People ({{ $company->people->count() }})</h3>
                    </div>
                    <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                        @forelse($company->people as $person)
                            <div class="px-6 py-3">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $person->name }}
                                    @if($person->isLinkedToUser())
                                        <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Linked</span>
                                    @endif
                                </div>
                                @if($person->email)
                                    <div class="text-sm text-gray-500">{{ $person->email }}</div>
                                @endif
                            </div>
                        @empty
                            <div class="px-6 py-4 text-gray-500 text-sm">No people.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
