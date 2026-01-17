<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Audit Logs
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-purple-600 hover:text-purple-800">&larr; Back to Admin</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <!-- Filters -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <form method="GET" class="flex flex-wrap gap-4">
                        <div>
                            <select name="action" class="rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                                <option value="">All Actions</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $action)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm">Filter</button>
                            @if(request()->hasAny(['action', 'admin_id', 'target_user_id']))
                                <a href="{{ route('admin.audit-logs.index') }}" class="ml-2 text-sm text-gray-600 hover:text-gray-800">Clear</a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Logs Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Justification</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->created_at->format('M j, Y g:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $log->admin->name ?? 'Deleted' }}</div>
                                        <div class="text-sm text-gray-500">{{ $log->admin->email ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if(str_contains($log->action, 'view')) bg-blue-100 text-blue-800
                                            @elseif(str_contains($log->action, 'suspend')) bg-amber-100 text-amber-800
                                            @elseif(str_contains($log->action, 'delete')) bg-red-100 text-red-800
                                            @elseif(str_contains($log->action, 'promote') || str_contains($log->action, 'demote')) bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800
                                            @endif
                                        ">
                                            {{ $log->action_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($log->targetUser)
                                            <div class="text-sm font-medium text-gray-900">{{ $log->targetUser->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $log->targetUser->email }}</div>
                                        @elseif($log->target_user_id)
                                            <span class="text-sm text-gray-500 italic">Deleted user</span>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->justification)
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $log->justification }}">
                                                {{ Str::limit($log->justification, 50) }}
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->ip_address ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No audit logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($logs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
