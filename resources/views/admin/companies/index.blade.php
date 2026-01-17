<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Companies
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-purple-600 hover:text-purple-800">&larr; Back to Admin Dashboard</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search -->
            <div class="mb-6">
                <form method="GET" class="flex gap-4">
                    <div class="flex-1">
                        <x-text-input
                            name="search"
                            type="text"
                            class="w-full"
                            placeholder="Search companies..."
                            :value="request('search')"
                        />
                    </div>
                    <x-primary-button type="submit">Search</x-primary-button>
                    @if(request('search'))
                        <a href="{{ route('admin.companies.index') }}" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
                    @endif
                </form>
            </div>

            <!-- Companies Table -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('admin.companies.index', ['sort' => 'name', 'dir' => request('sort') === 'name' && request('dir') === 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" class="hover:text-gray-700">
                                    Company
                                    @if(request('sort') === 'name')
                                        <span class="ml-1">{{ request('dir') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('admin.companies.index', ['sort' => 'users_count', 'dir' => request('sort') === 'users_count' && request('dir') === 'desc' ? 'asc' : 'desc', 'search' => request('search')]) }}" class="hover:text-gray-700">
                                    Users
                                    @if(request('sort') === 'users_count')
                                        <span class="ml-1">{{ request('dir') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('admin.companies.index', ['sort' => 'people_count', 'dir' => request('sort') === 'people_count' && request('dir') === 'desc' ? 'asc' : 'desc', 'search' => request('search')]) }}" class="hover:text-gray-700">
                                    People
                                    @if(request('sort') === 'people_count')
                                        <span class="ml-1">{{ request('dir') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('admin.companies.index', ['sort' => 'created_at', 'dir' => request('sort', 'created_at') === 'created_at' && request('dir', 'desc') === 'desc' ? 'asc' : 'desc', 'search' => request('search')]) }}" class="hover:text-gray-700">
                                    Created
                                    @if(request('sort', 'created_at') === 'created_at')
                                        <span class="ml-1">{{ request('dir', 'desc') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($companies as $company)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $company->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $company->users_count }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $company->people_count }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $company->created_at->format('j M Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $company->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.companies.show', $company) }}" class="text-purple-600 hover:text-purple-900">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No companies found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($companies->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $companies->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
