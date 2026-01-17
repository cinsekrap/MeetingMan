<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Welcome to MeetingMan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Let's get you set up</h3>
                    <p class="text-gray-600 mb-6">Choose how you'd like to get started with MeetingMan.</p>

                    @if($invite)
                        <!-- Show invite acceptance option -->
                        <div class="mb-8 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                            <h4 class="font-medium text-purple-900 mb-2">You've been invited!</h4>
                            <p class="text-purple-800 mb-4">
                                You have an invitation to join <strong>{{ $invite->company->name }}</strong>.
                            </p>
                            <form action="{{ route('company.setup.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="action" value="join">
                                <input type="hidden" name="invite_token" value="{{ $invite->token }}">
                                <button type="submit" class="w-full px-4 py-3 bg-purple-600 text-white rounded-md hover:bg-purple-700 font-medium">
                                    Join {{ $invite->company->name }}
                                </button>
                            </form>
                        </div>

                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">Or create your own company</span>
                            </div>
                        </div>
                    @endif

                    <!-- Create new company -->
                    <form action="{{ route('company.setup.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="create">

                        <div class="mb-4">
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Company Name
                            </label>
                            <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                   placeholder="e.g., Acme Corp" required>
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full px-4 py-3 bg-gray-800 text-white rounded-md hover:bg-gray-900 font-medium">
                            Create Company
                        </button>
                    </form>

                    @if(!$invite)
                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">Have an invite?</span>
                            </div>
                        </div>

                        <form action="{{ route('company.setup.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="join">

                            <div class="mb-4">
                                <label for="invite_token" class="block text-sm font-medium text-gray-700 mb-1">
                                    Invite Code
                                </label>
                                <input type="text" name="invite_token" id="invite_token"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                       placeholder="Paste your invite code here">
                                @error('invite_token')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="w-full px-4 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 font-medium">
                                Join with Invite Code
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
