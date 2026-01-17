<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin Dashboard
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.users.index') }}" class="text-sm text-purple-600 hover:text-purple-800">Users</a>
                <a href="{{ route('admin.companies.index') }}" class="text-sm text-purple-600 hover:text-purple-800">Companies</a>
                <a href="{{ route('admin.audit-logs.index') }}" class="text-sm text-purple-600 hover:text-purple-800">Audit Logs</a>
                <a href="{{ route('admin.branding.index') }}" class="text-sm text-purple-600 hover:text-purple-800">Branding</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Total Users</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($stats['total_users']) }}</div>
                    <div class="mt-1 text-sm text-gray-500">
                        {{ $stats['super_admins'] }} admin{{ $stats['super_admins'] !== 1 ? 's' : '' }}
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Active Users</div>
                    <div class="mt-1 text-3xl font-semibold text-green-600">{{ number_format($stats['active_users']) }}</div>
                    <div class="mt-1 text-sm text-gray-500">
                        {{ $stats['suspended_users'] }} suspended
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">New This Week</div>
                    <div class="mt-1 text-3xl font-semibold text-purple-600">{{ number_format($stats['new_users_this_week']) }}</div>
                    <div class="mt-1 text-sm text-gray-500">
                        {{ $stats['new_users_this_month'] }} this month
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Total Meetings</div>
                    <div class="mt-1 text-3xl font-semibold text-blue-600">{{ number_format($stats['total_meetings']) }}</div>
                    <div class="mt-1 text-sm text-gray-500">
                        {{ $stats['meetings_this_week'] }} this week
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Companies</div>
                    <div class="mt-1 text-3xl font-semibold text-orange-600">{{ number_format($stats['total_companies']) }}</div>
                    <div class="mt-1 text-sm text-gray-500">
                        {{ $stats['new_companies_this_week'] }} this week
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Users -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recent Users</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($recentUsers as $user)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $user->name }}
                                        @if($user->is_super_admin)
                                            <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">Admin</span>
                                        @endif
                                        @if($user->suspended_at)
                                            <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Suspended</span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $user->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-4 text-gray-500 text-sm">No users yet.</div>
                        @endforelse
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-purple-600 hover:text-purple-800">View all users &rarr;</a>
                    </div>
                </div>

                <!-- Recent Companies -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recent Companies</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($recentCompanies as $company)
                            <div class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $company->name }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $company->users_count }} {{ Str::plural('user', $company->users_count) }}
                                    &middot; {{ $company->people_count }} {{ Str::plural('person', $company->people_count) }}
                                    &middot; {{ $company->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-4 text-gray-500 text-sm">No companies yet.</div>
                        @endforelse
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200">
                        <a href="{{ route('admin.companies.index') }}" class="text-sm text-purple-600 hover:text-purple-800">View all companies &rarr;</a>
                    </div>
                </div>

                <!-- Recent Admin Activity -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recent Admin Activity</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($recentActivity as $log)
                            <div class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <span class="font-medium">{{ $log->admin->name }}</span>
                                    {{ strtolower($log->action_label) }}
                                    @if($log->targetUser)
                                        <span class="font-medium">{{ $log->targetUser->name }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $log->created_at->diffForHumans() }}
                                    @if($log->justification)
                                        &middot; "{{ Str::limit($log->justification, 50) }}"
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-4 text-gray-500 text-sm">No admin activity yet.</div>
                        @endforelse
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200">
                        <a href="{{ route('admin.audit-logs.index') }}" class="text-sm text-purple-600 hover:text-purple-800">View all logs &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
