<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User: {{ $user->name }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-purple-600 hover:text-purple-800">&larr; Back to Users</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-4 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg">
                    {{ session('warning') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- User Info Card -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">User Information</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    @if($user->suspended_at)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Suspended {{ $user->suspended_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Role</dt>
                                <dd class="mt-1">
                                    @if($user->is_super_admin)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Super Admin</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">User</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Joined</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('F j, Y \a\t g:i A') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email Verified</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($user->email_verified_at)
                                        {{ $user->email_verified_at->format('F j, Y') }}
                                    @else
                                        <span class="text-amber-600">Not verified</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Usage Statistics</h4>
                            <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div class="bg-gray-50 rounded-lg p-3 text-center">
                                    <dt class="text-xs text-gray-500">People</dt>
                                    <dd class="text-xl font-semibold text-gray-900">{{ $user->people_count }}</dd>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3 text-center">
                                    <dt class="text-xs text-gray-500">Meetings</dt>
                                    <dd class="text-xl font-semibold text-gray-900">{{ $user->meetings_count }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- View User Data -->
                        <div x-data="{ showModal: false }">
                            <button @click="showModal = true" class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm">
                                View User Data
                            </button>

                            <!-- Justification Modal -->
                            <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>

                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                                        <form action="{{ route('admin.users.view-data', $user) }}" method="POST">
                                            @csrf
                                            <div>
                                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                    Access User Data
                                                </h3>
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-500">
                                                        You are about to view {{ $user->name }}'s meeting data. This action will be logged and the user will be notified.
                                                    </p>
                                                </div>
                                                <div class="mt-4">
                                                    <label for="justification" class="block text-sm font-medium text-gray-700">Justification (required)</label>
                                                    <textarea name="justification" id="justification" rows="3" required minlength="10" maxlength="500" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" placeholder="Explain why you need to access this user's data..."></textarea>
                                                </div>
                                            </div>
                                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:col-start-2 sm:text-sm">
                                                    Access Data
                                                </button>
                                                <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Suspend / Unsuspend -->
                        @if($user->id !== auth()->id())
                            @if($user->suspended_at)
                                <form action="{{ route('admin.users.unsuspend', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                        Unsuspend User
                                    </button>
                                </form>
                            @else
                                <div x-data="{ showSuspendModal: false }">
                                    <button @click="showSuspendModal = true" class="w-full px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 text-sm">
                                        Suspend User
                                    </button>

                                    <div x-show="showSuspendModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="showSuspendModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showSuspendModal = false"></div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                            <div x-show="showSuspendModal" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                                                <form action="{{ route('admin.users.suspend', $user) }}" method="POST">
                                                    @csrf
                                                    <div>
                                                        <h3 class="text-lg leading-6 font-medium text-gray-900">Suspend User</h3>
                                                        <div class="mt-4">
                                                            <label for="reason" class="block text-sm font-medium text-gray-700">Reason (optional)</label>
                                                            <textarea name="reason" id="reason" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3">
                                                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 sm:text-sm">Suspend</button>
                                                        <button type="button" @click="showSuspendModal = false" class="mt-3 sm:mt-0 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:text-sm">Cancel</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- Force Password Reset -->
                        <form action="{{ route('admin.users.force-password-reset', $user) }}" method="POST" onsubmit="return confirm('This will reset the user\'s password and generate a temporary one. Continue?')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                                Force Password Reset
                            </button>
                        </form>

                        <!-- Promote / Demote Admin -->
                        @if($user->id !== auth()->id())
                            @if($user->is_super_admin)
                                <form action="{{ route('admin.users.demote', $user) }}" method="POST" onsubmit="return confirm('Remove admin privileges from this user?')">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 border border-purple-600 text-purple-600 rounded-md hover:bg-purple-50 text-sm">
                                        Demote from Admin
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.users.promote', $user) }}" method="POST" onsubmit="return confirm('Promote this user to super admin?')">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 border border-purple-600 text-purple-600 rounded-md hover:bg-purple-50 text-sm">
                                        Promote to Admin
                                    </button>
                                </form>
                            @endif
                        @endif

                        <!-- Delete User -->
                        @if($user->id !== auth()->id() && !$user->is_super_admin)
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                                    Delete User
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
